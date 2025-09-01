<?php

require_once 'controllers/UserController.php'; //Import user Controller
require_once 'controllers/HomeController.php'; //Import home Controller
require_once 'controllers/StripeController.php'; //Import stripe Controller

//API
$router->mount('/api', function() use ($router) {

    $router->get('/', function(){
        $response = [];

        header('HTTP/1.1 200 OK');

        $response['name'] = "Orion API";
        $response['author'] = "Abel";
        $response['lastModifiedDate'] = "2024-11-29";
        $response['currentSystemDate'] = (new DateTime())->format('Y-m-d');
        $response['message'] = "hello!";
        $response['surprise'] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAICAIAAACkr0LiAAAACXBIWXMAAA9hAAAPYQGoP6dpAAAAdUlEQVQImV2OQQ3DQADDvKoEQuEoDOUgtBQOwkqhFELBFPZoNU3LK4/YymPb3gDIX2S5SpLOV+cOqBBh/e4yBuRmNMnqZRPyjLW9POICBGzbszCPfR7zwleU5DimOkZCasWQ5eeXwHMEohVW7wMC1bOFJAP9AD4XPtggqnyXAAAAAElFTkSuQmCC";

        echo json_encode($response);
        exit();
    });

    $router->post('/', function(){
        $response = [];

        header('HTTP/1.1 200 OK');

        $response['name'] = "Orion API";
        $response['author'] = "Abel";
        $response['lastModifiedDate'] = "2024-11-29";
        $response['currentSystemDate'] = (new DateTime())->format('Y-m-d');
        $response['message'] = "hello!";
        $response['surprise'] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAICAIAAACkr0LiAAAACXBIWXMAAA9hAAAPYQGoP6dpAAAAdUlEQVQImV2OQQ3DQADDvKoEQuEoDOUgtBQOwkqhFELBFPZoNU3LK4/YymPb3gDIX2S5SpLOV+cOqBBh/e4yBuRmNMnqZRPyjLW9POICBGzbszCPfR7zwleU5DimOkZCasWQ5eeXwHMEohVW7wMC1bOFJAP9AD4XPtggqnyXAAAAAElFTkSuQmCC";

        echo json_encode($response);
        exit();
    });

    $router->get('/home', function(){
        HomeController::do();
    });

    $router->post('/stripe', function(){
        StripeController::webhook();
    });

    $router->post('/auth/login', function(){
        $email = $_POST['email'];
        $password = $_POST['password'];

        UserController::login($email, $password);
    });

    $router->post('/auth/register', function(){
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $terms = $_POST['terms'];
        $birthdate = $_POST['birthdate'];

        UserController::register($email, $password, $confirmPassword, $birthdate, $terms);
    });

    $router->post('/auth/edit', function(){

        $user = null;

        if(isset($_SESSION['user'])){
            $user = User::getById($_SESSION['user']['id']);
        }

        if(is_null($user)){
            header('HTTP/1.1 401 Unauthorized');
        
            $jsonArray = array();
            $jsonArray['status'] = "401";
            $jsonArray['status_text'] = "User not logged";
        
            echo json_encode($jsonArray);
            exit();
        }

        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $confirmPassword = $_POST['confirmPassword'] ?? null;
        $currentPassword = $_POST['currentPassword'] ?? null;
        $username = $_POST['username'];
        $motd = $_POST['motd'] ?? null;
        $token = $_POST['tript_token'];

        $profilePic = $_FILES['profilePic'] ?? null;

        UserController::edit($user, $username, $motd, $profilePic, $email, $currentPassword, $password, $confirmPassword, $token);
    });

    include('routes/api/community.php');

    include('routes/api/admin.php');

    include('routes/api/dev.php');

    include('routes/api/library.php');

    include('routes/api/game.php');
});


$router->set404('/api(/.*)?', function() {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json');

    $jsonArray = array();
    $jsonArray['status'] = "404";
    $jsonArray['status_text'] = "route not defined";

    echo json_encode($jsonArray);
});