<?php
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Helper
{
    private const CHUNK_SIZE = 8 * 1024 * 1024; // 8 MB

    public static function getAWSEndpoint()
    {
        return "https://" .
            $_ENV["CLOUDFLARE_ACCOUNT_ID"] .
            ".r2.cloudflarestorage.com/";
    }

    public static function getBucketName()
    {
        return $_ENV["CLOUDFLARE_R2_BUCKET_NAME"];
    }

    public static function getClient()
    {
        return new S3Client([
            "version" => "latest",
            "region" => "auto",
            "endpoint" => self::getAWSEndpoint(),
            "credentials" => [
                "key" => $_ENV["CLOUDFLARE_R2_TOKEN"],
                "secret" => $_ENV["CLOUDFLARE_R2_TOKEN_SECRET"],
            ],
        ]);
    }

    public static function isCacheEnabled()
    {
        return isset($_ENV["ENABLE_CACHE"]) && $_ENV["ENABLE_CACHE"] == "true";
    }

    // ── Cache ──────────────────────────────────────────────────────────────

    public static function cleanCache()
    {
        if (!self::isCacheEnabled()) {
            return;
        }

        $cacheDir = __DIR__ . "/../cache/bucket/";
        if (!is_dir($cacheDir)) {
            return;
        }

        $now = time();
        foreach (glob($cacheDir . "*") as $file) {
            if (is_file($file) && $now - filemtime($file) > 86400) {
                unlink($file);
            }
        }
    }

    // ── Upload ─────────────────────────────────────────────────────────────

    /**
     * Sube un archivo a S3/R2.
     * Si el archivo supera CHUNK_SIZE, usa multipart upload automáticamente.
     */
    public static function upload(
        EBUCKET_LOCATION $location,
        $name,
        $body = null,
        $contentType = null,
        $sourceFile = null,
    ) {
        self::cleanCache();

        $key = $location->value . $name;
        $size = $sourceFile ? filesize($sourceFile) : strlen($body ?? "");
        $useMultipart = $size > self::CHUNK_SIZE;

        // Invalidar caché si procede
        if (
            self::isCacheEnabled() &&
            $location !== EBUCKET_LOCATION::GAME_BUILD
        ) {
            $cacheFile = __DIR__ . "/../cache/bucket/" . md5($key);
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }
        }

        return $useMultipart
            ? self::multipartUpload($key, $body, $contentType, $sourceFile)
            : self::simpleUpload($key, $body, $contentType, $sourceFile);
    }

    // ── Download ───────────────────────────────────────────────────────────

    /**
     * Recupera un objeto de S3/R2.
     * Archivos pequeños: devuelve ['body' => string, 'type' => string, 'size' => int]
     * Archivos grandes (>= CHUNK_SIZE): descarga en chunks y devuelve
     *   ['body' => GuzzleStream, 'type' => string, 'size' => int]
     * Retorna null si el objeto no existe.
     */
    public static function retrieve(EBUCKET_LOCATION $location, $name)
    {
        self::cleanCache();

        $key = $location->value . $name;
        $shouldCache = $location !== EBUCKET_LOCATION::GAME_BUILD;
        $cacheDir = __DIR__ . "/../cache/bucket/";
        $cacheFile = $cacheDir . md5($key);

        // Servir desde caché si está disponible
        if (self::isCacheEnabled() && $shouldCache && file_exists($cacheFile)) {
            return [
                "body" => fopen($cacheFile, "rb"),
                "type" => mime_content_type($cacheFile),
                "size" => filesize($cacheFile),
            ];
        }

        // Obtener metadatos primero (HeadObject) para decidir estrategia
        try {
            $head = self::getClient()->headObject([
                "Bucket" => self::getBucketName(),
                "Key" => $key,
            ]);
        } catch (AwsException $e) {
            return null;
        }

        $contentType = $head["ContentType"] ?? "application/octet-stream";
        $totalSize = $head["ContentLength"] ?? 0;

        // Archivos pequeños: descarga directa
        if ($totalSize < self::CHUNK_SIZE) {
            return self::simpleRetrieve(
                $key,
                $contentType,
                $totalSize,
                $shouldCache,
                $cacheDir,
                $cacheFile,
            );
        }

        // Archivos grandes: descarga multipart mediante Range requests
        return self::multipartRetrieve($key, $contentType, $totalSize);
    }

    /**
     * Stream directo al cliente HTTP en chunks de 8 MB.
     * Llama a este método desde el endpoint de stream cuando $file['streaming'] === true.
     */
    public static function streamToClient(array $file): void
    {
        if (!isset($file["streaming"]) || !$file["streaming"]) {
            // Archivo pequeño, body es un recurso fopen → lo emitimos igual
            $body = $file["body"];
            if (is_resource($body)) {
                while (!feof($body)) {
                    echo fread($body, self::CHUNK_SIZE);
                    flush();
                }
                fclose($body);
            } else {
                // GuzzleStream
                while (!$body->eof()) {
                    echo $body->read(self::CHUNK_SIZE);
                    flush();
                }
            }
            return;
        }

        // Streaming multipart: iterar el generador
        foreach ($file["body"] as $chunk) {
            echo $chunk;
            flush();
        }
    }

    // ── Private: upload helpers ────────────────────────────────────────────

    private static function simpleUpload(
        $key,
        $body,
        $contentType,
        $sourceFile,
    ): bool {
        $params = [
            "Bucket" => self::getBucketName(),
            "Key" => $key,
        ];
        if ($sourceFile) {
            $params["SourceFile"] = $sourceFile;
        } else {
            $params["Body"] = $body;
        }
        if ($contentType) {
            $params["ContentType"] = $contentType;
        }

        $result = self::getClient()->putObject($params);
        return isset($result);
    }

    /**
     * Multipart upload en chunks de 8 MB.
     * Soporta tanto $sourceFile (ruta en disco) como $body (string en memoria).
     */
    private static function multipartUpload(
        $key,
        $body,
        $contentType,
        $sourceFile,
    ): bool {
        $client = self::getClient();
        $bucket = self::getBucketName();

        // Iniciar upload
        $initParams = ["Bucket" => $bucket, "Key" => $key];
        if ($contentType) {
            $initParams["ContentType"] = $contentType;
        }

        $multipart = $client->createMultipartUpload($initParams);
        $uploadId = $multipart["UploadId"];
        $parts = [];
        $partNumber = 1;

        try {
            if ($sourceFile) {
                // Leer el archivo en chunks desde disco
                $handle = fopen($sourceFile, "rb");
                if (!$handle) {
                    throw new \RuntimeException(
                        "No se pudo abrir: $sourceFile",
                    );
                }

                while (!feof($handle)) {
                    $chunk = fread($handle, self::CHUNK_SIZE);
                    if ($chunk === false || strlen($chunk) === 0) {
                        break;
                    }

                    $part = $client->uploadPart([
                        "Bucket" => $bucket,
                        "Key" => $key,
                        "UploadId" => $uploadId,
                        "PartNumber" => $partNumber,
                        "Body" => $chunk,
                    ]);

                    $parts[] = [
                        "PartNumber" => $partNumber,
                        "ETag" => $part["ETag"],
                    ];
                    $partNumber++;
                }
                fclose($handle);
            } else {
                // Dividir el string $body en chunks
                $offset = 0;
                $total = strlen($body);

                while ($offset < $total) {
                    $chunk = substr($body, $offset, self::CHUNK_SIZE);
                    $offset += self::CHUNK_SIZE;

                    $part = $client->uploadPart([
                        "Bucket" => $bucket,
                        "Key" => $key,
                        "UploadId" => $uploadId,
                        "PartNumber" => $partNumber,
                        "Body" => $chunk,
                    ]);

                    $parts[] = [
                        "PartNumber" => $partNumber,
                        "ETag" => $part["ETag"],
                    ];
                    $partNumber++;
                }
            }

            // Completar
            $client->completeMultipartUpload([
                "Bucket" => $bucket,
                "Key" => $key,
                "UploadId" => $uploadId,
                "MultipartUpload" => ["Parts" => $parts],
            ]);

            return true;
        } catch (\Exception $e) {
            // Abortar para no dejar uploads huérfanos en R2
            try {
                $client->abortMultipartUpload([
                    "Bucket" => $bucket,
                    "Key" => $key,
                    "UploadId" => $uploadId,
                ]);
            } catch (\Exception $_) {
            }

            error_log("S3Helper multipartUpload error: " . $e->getMessage());
            return false;
        }
    }

    // ── Private: retrieve helpers ──────────────────────────────────────────

    private static function simpleRetrieve(
        $key,
        $contentType,
        $size,
        $shouldCache,
        $cacheDir,
        $cacheFile,
    ) {
        try {
            $result = self::getClient()->getObject([
                "Bucket" => self::getBucketName(),
                "Key" => $key,
            ]);

            if (
                self::isCacheEnabled() &&
                $shouldCache &&
                isset($result["Body"])
            ) {
                if (!file_exists($cacheDir)) {
                    mkdir($cacheDir, 0777, true);
                }
                file_put_contents($cacheFile, (string) $result["Body"]);
                return [
                    "body" => fopen($cacheFile, "rb"),
                    "type" => $contentType,
                    "size" => $size,
                    "streaming" => false,
                ];
            }

            return [
                "body" => $result["Body"],
                "type" => $contentType,
                "size" => $size,
                "streaming" => false,
            ];
        } catch (AwsException $e) {
            return null;
        }
    }

    /**
     * Devuelve un generador que va pidiendo rangos al bucket en chunks de 8 MB.
     * Nunca carga el archivo completo en memoria.
     */
    private static function multipartRetrieve($key, $contentType, $totalSize)
    {
        $generator = (function () use ($key, $totalSize) {
            $client = self::getClient();
            $bucket = self::getBucketName();
            $offset = 0;

            while ($offset < $totalSize) {
                $end = min($offset + self::CHUNK_SIZE - 1, $totalSize - 1);

                try {
                    $result = $client->getObject([
                        "Bucket" => $bucket,
                        "Key" => $key,
                        "Range" => "bytes={$offset}-{$end}",
                    ]);
                    yield (string) $result["Body"];
                } catch (AwsException $e) {
                    error_log(
                        "S3Helper multipartRetrieve error at offset {$offset}: " .
                            $e->getMessage(),
                    );
                    return;
                }

                $offset = $end + 1;
            }
        })();

        return [
            "body" => $generator,
            "type" => $contentType,
            "size" => $totalSize,
            "streaming" => true,
        ];
    }
}
