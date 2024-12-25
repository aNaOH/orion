<?php

require_once './models/User.php';
require_once './models/Achievement.php';

class AchievementController {
    public static function triggerAchievement(int $userId, int $achievementId): void {
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

    public static function changeStat(int $userId, int $statId, int $value): void {
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
