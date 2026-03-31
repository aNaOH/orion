<?php

require_once "models/Game.php";
require_once "models/User.php";
require_once "helpers/OrderHelper.php";
require_once "controllers/StripeController.php";
require_once "emails/OrderSuccessEmail.php";
require_once "helpers/forms.php";

class CartController
{
    public static function add()
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::COMMON);
        $gameId = $_POST["gameId"] ?? null;
        if (!$gameId) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "error" => "Game ID is required"]);
            exit();
        }

        $game = Game::getById($gameId);
        if (!$game) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "error" => "Game not found"]);
            exit();
        }

        if (!OrderHelper::getOrder()) {
            OrderHelper::beginOrder();
        }

        if (OrderHelper::hasItem($gameId)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "error" => "Item already in cart"]);
            exit();
        }

        if (OrderHelper::addItem($game)) {
            header("HTTP/1.1 200 OK");
            echo json_encode(["status" => 200, "message" => "Item added to cart"]);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(["status" => 500, "error" => "Failed to add item to cart"]);
        }
        exit();
    }

    public static function remove($id)
    {
        FormHelper::ValidateToken($_GET["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::COMMON);
        if (!OrderHelper::removeItem($id)) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "error" => "Item not found in cart"]);
            exit();
        }

        header("HTTP/1.1 200 OK");
        echo json_encode(["status" => 200, "message" => "Item removed from cart"]);
        exit();
    }

    public static function createOrder()
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::COMMON);
        if (!isset($_SESSION["user"])) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["status" => 401, "error" => "User not logged in"]);
            exit();
        }

        $user = User::getById($_SESSION["user"]["id"]);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "error" => "User not found"]);
            exit();
        }

        StripeController::createOrder($user);
    }

    public static function saveOrder()
    {
        if (!isset($_SESSION["user"])) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["status" => 401, "error" => "User not logged in"]);
            exit();
        }

        $user = User::getById($_SESSION["user"]["id"]);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "error" => "User not found"]);
            exit();
        }

        $json = json_decode(file_get_contents("php://input"), true);
        FormHelper::ValidateToken($json["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::COMMON);
        $order = $json["order"] ?? null;
        $stripe_id = $json["stripe_id"] ?? null;
        
        if (!isset($order["items"]) || !is_array($order["items"]) || count($order["items"]) === 0) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "error" => "Invalid order"]);
            exit();
        }

        $purchasedGames = [];
        foreach ($order["items"] as $item) {
            $game = Game::getById($item["game_id"]);
            if (!$game) {
                header("HTTP/1.1 404 Not Found");
                echo json_encode(["status" => 404, "error" => "Game not found: " . $item["game_id"]]);
                exit();
            }

            if ($user->adquireGame($game, $stripe_id)) {
                $purchasedGames[] = $game;
            }
        }

        if (count($purchasedGames) > 0) {
            $email = new OrderSuccessEmail($user->email, $user, $purchasedGames);
            $email->send();
        }

        OrderHelper::clearOrder();

        header("HTTP/1.1 200 OK");
        echo json_encode(["status" => 200, "message" => "Order processed successfully"]);
        exit();
    }
}
