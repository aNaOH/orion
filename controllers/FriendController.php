<?php

require_once "./models/User.php";
require_once "./helpers/forms.php";

class FriendController
{
    private static function getLoggedUser()
    {
        if (!isset($_SESSION["user"]) || !isset($_SESSION["user"]["id"])) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["status" => 401, "message" => "User not logged in"]);
            exit();
        }
        return User::getById($_SESSION["user"]["id"]);
    }

    public static function sendRequest($targetUserId)
    {
        $user = self::getLoggedUser();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Target user not found"]);
            exit();
        }

        if ($user->sendFriendRequest($targetUser)) {
            echo json_encode(["status" => 200, "message" => "Friend request sent"]);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Failed to send friend request"]);
        }
        exit();
    }

    public static function acceptRequest($targetUserId)
    {
        $user = self::getLoggedUser();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Target user not found"]);
            exit();
        }

        if ($user->acceptFriendRequest($targetUser)) {
            echo json_encode(["status" => 200, "message" => "Friend request accepted"]);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Failed to accept friend request"]);
        }
        exit();
    }

    public static function declineRequest($targetUserId)
    {
        $user = self::getLoggedUser();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Target user not found"]);
            exit();
        }

        if ($user->declineFriendRequest($targetUser)) {
            echo json_encode(["status" => 200, "message" => "Friend request declined"]);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Failed to decline friend request"]);
        }
        exit();
    }

    public static function removeFriend($targetUserId)
    {
        $user = self::getLoggedUser();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Target user not found"]);
            exit();
        }

        if ($user->removeFriend($targetUser)) {
            echo json_encode(["status" => 200, "message" => "Friend removed"]);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Failed to remove friend"]);
        }
        exit();
    }

    public static function blockUser($targetUserId)
    {
        $user = self::getLoggedUser();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Target user not found"]);
            exit();
        }

        if ($user->blockUser($targetUser)) {
            echo json_encode(["status" => 200, "message" => "User blocked"]);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Failed to block user"]);
        }
        exit();
    }

    public static function unblockUser($targetUserId)
    {
        $user = self::getLoggedUser();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Target user not found"]);
            exit();
        }

        if ($user->unblockUser($targetUser)) {
            echo json_encode(["status" => 200, "message" => "User unblocked"]);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Failed to unblock user"]);
        }
        exit();
    }

    public static function friendsList()
    {
        $user = self::getLoggedUser();
        $friends = $user->getFriends();
        $pending = $user->getPendingFriendRequests();
        
        $GLOBALS["friends"] = $friends;
        $GLOBALS["pending_requests"] = $pending;
        
        include "views/auth/friends.php";
        exit();
    }
}
