<?php

require_once "controllers/UserController.php"; //Import user Controller
require_once "controllers/HomeController.php"; //Import home Controller
require_once "controllers/StripeController.php"; //Import stripe Controller
require_once "controllers/FriendController.php"; //Import friend Controller
require_once "emails/OrderSuccessEmail.php";

//API
$router->mount("/api", function () use ($router) {
    $router->get("/", function () {
        $response = [];
        $date = new DateTime();

        header("HTTP/1.1 200 OK");

        $response["name"] = "Orion API";
        $response["author"] = "Abel";
        $response["lastModifiedDate"] = "2025-10-19";
        $response["currentSystemDate"] = $date->format("Y-m-d");
        $response["message"] = "hello!";
        $response["surprise"] =
            "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAICAIAAACkr0LiAAAACXBIWXMAAA9hAAAPYQGoP6dpAAAAdUlEQVQImV2OQQ3DQADDvKoEQuEoDOUgtBQOwkqhFELBFPZoNU3LK4/YymPb3gDIX2S5SpLOV+cOqBBh/e4yBuRmNMnqZRPyjLW9POICBGzbszCPfR7zwleU5DimOkZCasWQ5eeXwHMEohVW7wMC1bOFJAP9AD4XPtggqnyXAAAAAElFTkSuQmCC";

        echo json_encode($response);
        exit();
    });

    $router->post("/", function () {
        $response = [];
        $date = new DateTime();

        header("HTTP/1.1 200 OK");

        $response["name"] = "Orion API";
        $response["author"] = "Abel";
        $response["lastModifiedDate"] = "2024-11-29";
        $response["currentSystemDate"] = $date->format("Y-m-d");
        $response["message"] = "hello!";
        $response["surprise"] =
            "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAICAIAAACkr0LiAAAACXBIWXMAAA9hAAAPYQGoP6dpAAAAdUlEQVQImV2OQQ3DQADDvKoEQuEoDOUgtBQOwkqhFELBFPZoNU3LK4/YymPb3gDIX2S5SpLOV+cOqBBh/e4yBuRmNMnqZRPyjLW9POICBGzbszCPfR7zwleU5DimOkZCasWQ5eeXwHMEohVW7wMC1bOFJAP9AD4XPtggqnyXAAAAAElFTkSuQmCC";

        echo json_encode($response);
        exit();
    });

    $router->get("/triptencrypt", function () {
        $header = $_GET["header"];
        $name = $_GET["name"];

        $triptText = Tript::encryptString($header . $name);

        $response = [];
        $response["result"] = $triptText;

        echo json_encode($response);
        exit();
    });

    $router->get("/home", function () {
        HomeController::do();
    });

    $router->post("/cart", function () {
        $gameId = $_POST["gameId"];
        $game = Game::getById($gameId);
        if (!$game) {
            $response["status"] = 404;
            $response["error"] = "Game not found";
            echo json_encode($response);
            exit();
        }

        if (!OrderHelper::getOrder()) {
            OrderHelper::beginOrder();
        }

        if (OrderHelper::hasItem($gameId)) {
            $response["status"] = 400;
            $response["error"] = "Item already in cart";
            echo json_encode($response);
            exit();
        }

        if (OrderHelper::addItem($game)) {
            $response["status"] = 200;
            $response["message"] = "Item added to cart";
        } else {
            $response["status"] = 500;
            $response["error"] = "Failed to add item to cart";
        }
        echo json_encode($response);
        exit();
    });

    $router->delete("/cart/{id}", function ($id) {
        if (!OrderHelper::removeItem($id)) {
            $response["status"] = 404;
            $response["error"] = "Item not found in cart";
            echo json_encode($response);
            exit();
        }

        $response["status"] = 200;
        $response["message"] = "Item removed from cart";
        echo json_encode($response);
        exit();
    });

    $router->post("/order", function () {
        if (!isset($_SESSION["user"])) {
            $response["status"] = 401;
            $response["error"] = "User not logged in";
            echo json_encode($response);
            exit();
        }

        if (!isset($_SESSION["user"]["id"])) {
            $response["status"] = 404;
            $response["error"] = "User not found";
            echo json_encode($response);
            exit();
        }

        $user = User::getById($_SESSION["user"]["id"]);
        if (!$user) {
            $response["status"] = 404;
            $response["error"] = "User not found";
            echo json_encode($response);
            exit();
        }

        StripeController::createOrder($user);
    });

    $router->post("/order/save", function () {
        if (!isset($_SESSION["user"])) {
            $response["status"] = 401;
            $response["error"] = "User not logged in";
            echo json_encode($response);
            exit();
        }

        if (!isset($_SESSION["user"]["id"])) {
            $response["status"] = 404;
            $response["error"] = "User not found";
            echo json_encode($response);
            exit();
        }

        $user = User::getById($_SESSION["user"]["id"]);
        if (!$user) {
            $response["status"] = 404;
            $response["error"] = "User not found";
            echo json_encode($response);
            exit();
        }

        $json = json_decode(file_get_contents("php://input"), true);
        $order = $json["order"];
        $stripe_id = $json["stripe_id"] ?? null;
        if (
            !isset($order["items"]) ||
            !is_array($order["items"]) ||
            count($order["items"]) === 0
        ) {
            $response["status"] = 400;
            $response["error"] = "Invalid order";
            echo json_encode($response);
            exit();
        }

        foreach ($order["items"] as $item) {
            if (!isset($item["game_id"]) || $item["game_id"] <= 0) {
                $response["status"] = 400;
                $response["error"] = "Invalid order item";
                echo json_encode($response);
                exit();
            }

            $game = Game::getById($item["game_id"]);
            if (!$game) {
                $response["status"] = 404;
                $response["error"] = "Game not found";
                echo json_encode($response);
                exit();
            }

            $user->adquireGame($game, $stripe_id);
        }

        $email = new OrderSuccessEmail(
            $user->email,
            $user,
            OrderHelper::getInstances(),
        );
        $email->send();

        OrderHelper::clearOrder();

        $response["status"] = 200;
        $response["message"] = "Order processed successfully";
        echo json_encode($response);
    });

    $router->post("/auth/login", function () {
        $email = $_POST["email"];
        $password = $_POST["password"];

        UserController::login($email, $password);
    });

    $router->post("/auth/register", function () {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirmPassword"];
        $terms = $_POST["terms"];
        $birthdate = $_POST["birthdate"];

        UserController::register(
            $email,
            $password,
            $confirmPassword,
            $birthdate,
            $terms,
        );
    });

    $router->post("/auth/edit", function () {
        $user = null;

        if (isset($_SESSION["user"])) {
            $user = User::getById($_SESSION["user"]["id"]);
        }

        if (is_null($user)) {
            header("HTTP/1.1 401 Unauthorized");

            $jsonArray = [];
            $jsonArray["status"] = "401";
            $jsonArray["status_text"] = "User not logged";

            echo json_encode($jsonArray);
            exit();
        }

        $email = $_POST["email"] ?? null;
        $password = $_POST["password"] ?? null;
        $confirmPassword = $_POST["confirmPassword"] ?? null;
        $currentPassword = $_POST["currentPassword"] ?? null;
        $username = $_POST["username"];
        $motd = $_POST["motd"] ?? null;
        $token = $_POST["tript_token"];

        $profilePic = $_FILES["profilePic"] ?? null;

        UserController::edit(
            $user,
            $username,
            $motd,
            $profilePic,
            $email,
            $currentPassword,
            $password,
            $confirmPassword,
            $token,
        );
    });

    include "routes/api/community.php";

    include "routes/api/admin.php";

    include "routes/api/dev.php";

    include "routes/api/library.php";

    include "routes/api/game.php";

    $router->mount("/friends", function () use ($router) {
        $router->post("/request/(\d+)", function ($id) {
            FriendController::sendRequest($id);
        });
        $router->post("/accept/(\d+)", function ($id) {
            FriendController::acceptRequest($id);
        });
        $router->post("/decline/(\d+)", function ($id) {
            FriendController::declineRequest($id);
        });
        $router->post("/remove/(\d+)", function ($id) {
            FriendController::removeFriend($id);
        });
        $router->post("/block/(\d+)", function ($id) {
            FriendController::blockUser($id);
        });
        $router->post("/unblock/(\d+)", function ($id) {
            FriendController::unblockUser($id);
        });
    });
});

$router->set404("/api(/.*)?", function () {
    header("HTTP/1.1 404 Not Found");
    header("Content-Type: application/json");

    $jsonArray = [];
    $jsonArray["status"] = "404";
    $jsonArray["status_text"] = "route not defined";

    echo json_encode($jsonArray);
});
