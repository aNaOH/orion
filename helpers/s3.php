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

    public static function upload($location, $name, $body) {

        //TODO: make this work
        $key = $location . $name;

        $result = self::getClient()->putObject([
            'Bucket' => self::getBucketName(),
            'Key'    => $key,
            'Body'   => $body,
        ]);
    }

    public static function retrieve($location, $name) {

        //TODO: make this work
        $key = $location . $name;

        try {
            // Obtener el objeto
            $result = self::getClient()->getObject([
                'Bucket' => self::getBucketName(),
                'Key'    => $key,
            ]);

            return [
                "body" => $result['Body'],
                "type" => $result['ContentType']
            ];
        
        } catch (AwsException $e) {
            // Manejo de errores
            return null;
        }
    }
}

?>