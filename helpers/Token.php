<?php

class Token
{
    private static $magicWord = "orion";

    public static function getDate()
    {
        $dt = new DateTime();
        return $dt->format("Y-m-d");
    }

    public static function createToken()
    {
        return Tript::encryptString(self::$magicWord . "_" . self::getDate());
    }

    public static function validateToken($token, &$tokenParts = [])
    {
        $parsedToken = Tript::decryptString($token);

        $tokenParts = explode("_", $parsedToken);

        return self::validationRules($tokenParts);
    }

    private static function validationRules($tokenParts)
    {
        return $tokenParts[0] == self::$magicWord &&
            $tokenParts[1] == self::getDate();
    }
}

class AuthFormToken
{
    private static $magicWord = "orionauth";

    public static function createToken()
    {
        return Tript::encryptString(self::$magicWord . "_" . self::getDate());
    }

    public static function getDate()
    {
        $endDate = new DateTime();

        $endDate->add(new DateInterval("PT30M"));

        return $endDate->format("Y-m-d H:i:s");
    }

    public static function validateToken($token, &$tokenParts = [])
    {
        $parsedToken = Tript::decryptString($token);

        $tokenParts = explode("_", $parsedToken);

        return self::validationRules($tokenParts);
    }

    private static function validationRules($tokenParts)
    {
        $dateExpiration = new DateTime($tokenParts[1]);

        $currentDate = new DateTime();

        return $tokenParts[0] == self::$magicWord &&
            $currentDate < $dateExpiration;
    }
}

class UserActionToken
{
    private static $magicWord = "orionuserdoes";

    public static function createToken()
    {
        return Tript::encryptString(self::$magicWord . "_" . self::getDate());
    }

    public static function getDate()
    {
        if (!isset($_SESSION["user"])) {
            return "";
        }

        $user = User::getById($_SESSION["user"]["id"]);

        if (is_null($user)) {
            return "";
        }

        //An user is provided, proceed.

        $endDate = new DateTime();

        $endDate->add(new DateInterval("PT30M"));

        return $endDate->format("Y-m-d H:i:s") . "_" . strval($user->id);
    }

    public static function validateToken($token, &$tokenParts = [])
    {
        $parsedToken = Tript::decryptString($token);

        $tokenParts = explode("_", $parsedToken);

        return self::validationRules($tokenParts);
    }

    private static function validationRules($tokenParts)
    {
        if (!isset($_SESSION["user"])) {
            return false;
        }

        $user = User::getById($_SESSION["user"]["id"]);

        if (is_null($user)) {
            return false;
        }

        //An user is provided, proceed.

        $dateExpiration = new DateTime($tokenParts[1]);

        $currentDate = new DateTime();

        return $tokenParts[0] == self::$magicWord &&
            $currentDate < $dateExpiration &&
            $tokenParts[2] == $user->id;
    }
}

class UserLibraryToken
{
    private static $magicWord = "orionuserlibrary";

    public static function createToken(int $userId, int $gameId): string
    {
        return Tript::encryptString(
            self::$magicWord . "_" . $userId . "_" . $gameId,
        );
    }

    public static function validateToken(string $token): bool
    {
        $parsedToken = Tript::decryptString($token);
        $tokenParts = explode("_", $parsedToken);

        return self::validationRules($tokenParts);
    }

    private static function validationRules(array $tokenParts): bool
    {
        if ($tokenParts[0] !== self::$magicWord) {
            return false;
        }

        $userId = intval($tokenParts[1]);
        $gameId = intval($tokenParts[2]);

        $user = User::getById($userId);
        if (!$user || !$user->hasAdquiredGame($gameId)) {
            return false;
        }

        return true;
    }
}

class DevActionToken
{
    private static $magicWord = "oriondevaction";

    public static function createToken(int $userId, int $gameId): string
    {
        return Tript::encryptString(
            self::$magicWord . "_" . $userId . "_" . $gameId,
        );
    }

    public static function validateToken(string $token): bool
    {
        $parsedToken = Tript::decryptString($token);
        $tokenParts = explode("_", $parsedToken);

        return self::validationRules($tokenParts);
    }

    private static function validationRules(array $tokenParts): bool
    {
        if ($tokenParts[0] !== self::$magicWord) {
            return false;
        }

        $userId = intval($tokenParts[1]);
        $gameId = intval($tokenParts[2]);

        $game = Game::getById($gameId);
        if (!$game || $game->getDeveloper()->getOwner()->id !== $userId) {
            return false;
        }

        return true;
    }
}

class DownloadToken
{
    private static $magicWord = "oriondownload";

    public static function createToken(
        int $userId,
        int $gameId,
        string $version,
    ): string {
        $dt = new DateTime();
        $expiration = $dt->add(new DateInterval("PT5M"))->format("Y-m-d H:i:s");
        return Tript::encryptString(
            self::$magicWord .
                "_" .
                $userId .
                "_" .
                $gameId .
                "_" .
                $version .
                "_" .
                $expiration,
        );
    }

    public static function validateToken(string $token): bool
    {
        $parsedToken = Tript::decryptString($token);
        $tokenParts = explode("_", $parsedToken);
        return self::validationRules($tokenParts);
    }

    private static function validationRules(array $tokenParts): bool
    {
        if ($tokenParts[0] !== self::$magicWord) {
            return false;
        }

        $userId = intval($tokenParts[1]);
        $gameId = intval($tokenParts[2]);
        $version = $tokenParts[3];

        // Verificar expiración (5 minutos)
        $expiration = new DateTime($tokenParts[4]);
        $currentDate = new DateTime();
        if ($currentDate > $expiration) {
            return false;
        }

        // Verificar que el usuario de sesión coincide
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["id"] != $userId) {
            return false;
        }

        // Verificar que el usuario posee el juego
        $user = User::getById($userId);
        if (!$user || !$user->hasAdquiredGame($gameId)) {
            return false;
        }

        // Verificar que la build existe
        $game = Game::getById($gameId);
        if (!$game) {
            return false;
        }

        $build =
            $version === "latest"
                ? $game->getLatestBuild()
                : $game->getBuildVersion($version);

        if (is_null($build)) {
            return false;
        }

        return true;
    }

    // Helper para extraer los datos del token sin re-validar
    public static function getTokenData(string $token): ?array
    {
        $parsedToken = Tript::decryptString($token);
        $tokenParts = explode("_", $parsedToken);

        if (count($tokenParts) < 5 || $tokenParts[0] !== self::$magicWord) {
            return null;
        }

        return [
            "user_id" => intval($tokenParts[1]),
            "game_id" => intval($tokenParts[2]),
            "version" => $tokenParts[3],
        ];
    }
}

class ClientToken
{
    private static $magicWord = "orionclient";

    public static function createToken(
        int $userId,
        string $dateExpiration,
    ): string {
        return Tript::encryptString(
            self::$magicWord . "_" . $userId . "_" . $dateExpiration,
        );
    }

    public static function validateToken(string $token, int $userId): bool
    {
        $parsedToken = Tript::decryptString($token);
        $tokenParts = explode("_", $parsedToken);

        return self::validationRules($tokenParts, $userId);
    }

    private static function validationRules(
        array $tokenParts,
        int $userId,
    ): bool {
        if ($tokenParts[0] !== self::$magicWord) {
            return false;
        }

        $tokenUserId = intval($tokenParts[1]);
        if ($tokenUserId != $userId) {
            return false;
        }

        if ($tokenParts[2] != "none") {
            $dateExpiration = new DateTime($tokenParts[2]);
            $currentDate = new DateTime();

            if ($currentDate > $dateExpiration) {
                return false;
            }
        }

        return true;
    }
}
