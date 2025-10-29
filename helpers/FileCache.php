<?php

class FileCache
{
    private static string $cacheDir = __DIR__ . "/../cache";
    private static int $maxEntries = 200;
    private static int $maxFileSize = 5_000_000; // 5MB total
    private static int $defaultTTL = 3600; // 1 hora

    public static function get(string $key): mixed
    {
        self::ensureCacheDir();

        $path = self::path($key);
        if (!file_exists($path)) {
            return null;
        }

        $content = file_get_contents($path);
        $data = json_decode(gzdecode($content), true);
        if (!$data || $data["expires"] < time()) {
            @unlink($path);
            return null;
        }

        return $data["value"];
    }

    public static function set(string $key, mixed $value, int $ttl = null): void
    {
        self::ensureCacheDir();
        self::cleanup();

        $path = self::path($key);
        $data = [
            "expires" => time() + ($ttl ?? self::$defaultTTL),
            "value" => $value,
        ];

        file_put_contents($path, gzencode(json_encode($data), 9));
    }

    private static function cleanup(): void
    {
        $files = glob(self::$cacheDir . "/*.cache") ?: [];

        foreach ($files as $file) {
            $decoded = json_decode(@gzdecode(file_get_contents($file)), true);
            if (!$decoded || $decoded["expires"] < time()) {
                @unlink($file);
            }
        }

        $files = glob(self::$cacheDir . "/*.cache") ?: [];
        $totalSize = array_sum(array_map("filesize", $files));

        if (
            count($files) > self::$maxEntries ||
            $totalSize > self::$maxFileSize
        ) {
            usort($files, fn($a, $b) => filemtime($a) <=> filemtime($b));
            foreach (array_slice($files, 0, 10) as $file) {
                @unlink($file);
            }
        }
    }

    private static function path(string $key): string
    {
        return self::$cacheDir . "/" . md5($key) . ".cache";
    }

    private static function ensureCacheDir(): void
    {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0777, true);
        }
    }
}
