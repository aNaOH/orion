<?php

class S3Helper {
    public static function getAWSEndpoint() {
        return "https://".$_ENV["CLOUDFLARE_ACCOUNT_ID"].".r2.cloudflarestorage.com/".$_ENV["CLOUDFLARE_R2_BUCKET_NAME"];
    }
}

?>