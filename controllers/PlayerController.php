<?php

require_once './models/User.php';
require_once './models/Achievement.php';
require_once './helpers/Token.php';

class PlayerController {
    public static function getToken(string $email, string $password, int $gameId): void {
        $response = [];
        $user = User::getByEmail($email);

        if (!$user || !password_verify($password, $user->getPassword())) {
            header('HTTP/1.1 401 Unauthorized');
            $response['status'] = 401;
            $response['message'] = 'Invalid email or password';
            echo json_encode($response);
            exit();
        }

        if (!$user->hasAdquiredGame($gameId)) {
            header('HTTP/1.1 403 Forbidden');
            $response['status'] = 403;
            $response['message'] = 'User does not own the game';
            echo json_encode($response);
            exit();
        }

        $token = UserLibraryToken::createToken($user->id, $gameId);
        header('HTTP/1.1 200 OK');
        $response['status'] = 200;
        $response['token'] = $token;
        echo json_encode($response);
        exit();
    }

    public static function validateToken(string $token, int $userId, int $gameId): void {
        $response = [];
        if (UserLibraryToken::validateToken($token, $userId, $gameId)) {
            header('HTTP/1.1 200 OK');
            $response['status'] = 200;
            $response['message'] = 'Token is valid';
        } else {
            header('HTTP/1.1 401 Unauthorized');
            $response['status'] = 401;
            $response['message'] = 'Invalid token';
        }
        echo json_encode($response);
        exit();
    }

    public static function getUserInfo(string $token): void {
        $response = [];
        $parsedToken = Tript::decryptString($token);
        $tokenParts = explode('_', $parsedToken);

        if (count($tokenParts) !== 3 || $tokenParts[0] !== 'orionuserlibrary') {
            header('HTTP/1.1 401 Unauthorized');
            $response['status'] = 401;
            $response['message'] = 'Invalid token';
            echo json_encode($response);
            exit();
        }

        $userId = intval($tokenParts[1]);
        $user = User::getById($userId);

        if (!$user) {
            header('HTTP/1.1 404 Not Found');
            $response['status'] = 404;
            $response['message'] = 'User not found';
            echo json_encode($response);
            exit();
        }

        header('HTTP/1.1 200 OK');
        $response['status'] = 200;
        $response['user'] = [
            'id' => $user->id,
            'username' => $user->username,
            'handle' => $user->getHandle(),
            'profile_pic_url' => $user->getProfilePicURL()
        ];
        echo json_encode($response);
        exit();
    }

    public static function triggerAchievement(int $userId, int $achievementId, string $token): void {
        if (!UserLibraryToken::validateToken($token, $userId, $achievementId)) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['status' => 401, 'message' => 'Invalid token']);
            exit();
        }

        $response = [];
        $user = User::getById($userId);
        $achievement = Achievement::getById($achievementId);

        if (!$user || !$achievement) {
            header('HTTP/1.1 404 Not Found');
            $response['status'] = 404;
            $response['message'] = 'User or Achievement not found';
            echo json_encode($response);
            exit();
        }

        if ($achievement->type !== EACHIEVEMENT_TYPE::TRIGGERED) {
            header('HTTP/1.1 400 Bad Request');
            $response['status'] = 400;
            $response['message'] = 'Achievement is not of type TRIGGERED';
            echo json_encode($response);
            exit();
        }

        if ($user->unlockAchievement($achievement)) {
            header('HTTP/1.1 200 OK');
            $response['status'] = 200;
            $response['message'] = 'Achievement unlocked successfully';
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            $response['status'] = 500;
            $response['message'] = 'Failed to unlock achievement';
        }

        echo json_encode($response);
        exit();
    }

    public static function changeStat(int $userId, int $statId, int $value, string $token): void {
        if (!UserLibraryToken::validateToken($token, $userId, $statId)) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['status' => 401, 'message' => 'Invalid token']);
            exit();
        }

        $response = [];
        $user = User::getById($userId);
        $stat = Stat::getById($statId);

        if (!$user || !$stat) {
            header('HTTP/1.1 404 Not Found');
            $response['status'] = 404;
            $response['message'] = 'User or Stat not found';
            echo json_encode($response);
            exit();
        }

        if ($user->updateStat($stat, $value)) {
            $achievements = Achievement::getAllByStat($statId);
            $unlockedAchievements = [];

            foreach ($achievements as $achievement) {
                if ($achievement->type === EACHIEVEMENT_TYPE::STAT && $value >= $achievement->stat_value) {
                    if ($user->unlockAchievement($achievement)) {
                        $unlockedAchievements[] = $achievement->id;
                    }
                }
            }

            header('HTTP/1.1 200 OK');
            $response['status'] = 200;
            $response['message'] = 'Stat updated successfully';
            $response['unlocked_achievements'] = $unlockedAchievements;
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            $response['status'] = 500;
            $response['message'] = 'Failed to update stat';
        }

        echo json_encode($response);
        exit();
    }
}
