<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Helper {
    public static function getAWSEndpoint() {
        return "https://".$_ENV["CLOUDFLARE_ACCOUNT_ID"].".r2.cloudflarestorage.com/";
    }

    public static function getBucketName() {
        return $_ENV["CLOUDFLARE_R2_BUCKET_NAME"];
    }

    public static function getClient() {
        return new S3Client([
            'version' => 'latest',
            'region' => 'auto', // R2 utiliza "auto" como región, pero puedes especificar la región si es necesario.
            'endpoint' => self::getAWSEndpoint(), // Reemplaza con tu endpoint
            'credentials' => [
                'key'    => $_ENV['CLOUDFLARE_R2_TOKEN'],  // Tu Access Key ID
                'secret' => $_ENV['CLOUDFLARE_R2_TOKEN_SECRET'],  // Tu Secret Access Key
            ],
        ]);
    }

        // Limpia archivos de caché que tengan más de 1 día
    public static function cleanCache() {
        $cacheDir = __DIR__ . '/../cache/bucket/';
        if (!is_dir($cacheDir)) return;
        $files = glob($cacheDir . '*');
        $now = time();
        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file)) > 86400) {
                unlink($file);
            }
        }
    }


    public static function upload(EBUCKET_LOCATION $location, $name, $body, $contentType = null, $sourceFile = null) {
        self::cleanCache();
        $key = $location->value . $name;
        $params = [
            'Bucket' => self::getBucketName(),
            'Key'    => $key,
        ];

        if ($sourceFile) {
            $params['SourceFile'] = $sourceFile;
        } else {
            $params['Body'] = $body;
        }

        if ($contentType) {
            $params['ContentType'] = $contentType;
        }

        // Eliminar caché si existe
        $shouldCache = $location !== EBUCKET_LOCATION::GAME_BUILD;
        $cacheDir = __DIR__ . '/../cache/bucket/';
        $cacheFile = $cacheDir . md5($key);
        if ($shouldCache && file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        $result = self::getClient()->putObject($params);

        return isset($result);
    }

    public static function retrieve(EBUCKET_LOCATION $location, $name) {
        //self::cleanCache();
            $key = $location->value . $name;
            $shouldCache = $location !== EBUCKET_LOCATION::GAME_BUILD;
            $cacheDir = __DIR__ . '/../cache/bucket/';
            $cacheFile = $cacheDir . md5($key);

            //if ($shouldCache && file_exists($cacheFile)) {
            //    $body = file_get_contents($cacheFile);
            //    $type = mime_content_type($cacheFile);
            //    return ["body" => $body, "type" => $type];
            //}

            try {
                $result = self::getClient()->getObject([
                    'Bucket' => self::getBucketName(),
                    'Key'    => $key,
                ]);

                if ($shouldCache && isset($result['Body'])) {
                    //CHECK FOLDER
                    file_put_contents($cacheFile, (string)$result['Body']);
                }

                return [
                    "body" => $result['Body'],
                    "type" => $result['ContentType']
                ];
            } catch (AwsException $e) {
                return null;
            }
    }
}

?>