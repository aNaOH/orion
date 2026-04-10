<?php

require_once "./models/User.php";
require_once "./models/Game.php";
require_once "./emails/RegisterEmail.php";
require_once "./controllers/ViewController.php";
require_once "./helpers/forms.php";
require_once "./helpers/Token.php";

class UserController
{
    // --- Internal Helpers ---
    private static function getLoggedUser()
    {
        if (!isset($_SESSION["user"]) || !isset($_SESSION["user"]["id"])) {
            return null;
        }
        return User::getById($_SESSION["user"]["id"]);
    }

    private static function getLoggedUserOrExit()
    {
        $user = self::getLoggedUser();
        if (!$user) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(["status" => 401, "message" => "User not logged in"]);
            exit();
        }
        return $user;
    }

    // --- View Methods ---

    public static function showRegister()
    {
        if (isset($_SESSION["user"])) {
            header("location: /");
            exit();
        }
        ViewController::renderFromController('auth/register', ['title' => 'Unirse a Orion']);
    }

    public static function showLogin()
    {
        if (isset($_SESSION["user"])) {
            header("location: /");
            exit();
        }
        ViewController::renderFromController('auth/login', ['title' => 'Entrar a Orion']);
    }

    public static function logout()
    {
        if (isset($_SESSION["user"])) {
            session_destroy();
        }

        $location = "/";
        if (isset($_GET["to"])) {
            $location = $_GET["to"] . "?from=logout";
        }

        header("location: " . $location);
        exit();
    }

    public static function showProfile()
    {
        $user = self::getLoggedUser();
        if (!$user) {
            header("location: /login");
            exit();
        }

        ViewController::renderFromController('auth/profile', [
            'target_user' => $user,
            'is_self' => true
        ]);
    }

    public static function showLibrary()
    {
        $user = self::getLoggedUser();
        if (!$user) {
            header("location: /login");
            exit();
        }

        ViewController::renderFromController('auth/library', [
            'title' => 'Tu biblioteca en Orion'
        ]);
    }

    public static function downloadGame($gameid, $version)
    {
        global $router;
        $user = self::getLoggedUser();
        if (!$user) {
            header("location: /");
            exit();
        }

        $game = Game::getById($gameid);
        if (is_null($game) || !$user->hasAdquiredGame($game)) {
            $router->trigger404();
            exit();
        }

        $build = ($version == "latest") ? $game->getLatestBuild() : $game->getBuildVersion($version);
        if (is_null($build)) {
            $router->trigger404();
            exit();
        }

        $file = $build->getFile();
        if (is_null($file)) {
            $router->trigger404();
            exit();
        }

        header("Content-Type: " . $file["type"]);
        header('Content-Disposition: attachment; filename="' . str_replace(" ", "_", $game->title) . "-ver-" . $version . '.zip"');
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");

        echo $file["body"];
        exit();
    }

    public static function showProfileEdit()
    {
        $user = self::getLoggedUser();
        if (!$user) {
            header("location: /login");
            exit();
        }

        ViewController::renderFromController('auth/profileEdit', [
            'title' => 'Editar perfil de Orion'
        ]);
    }

    public static function showPublicProfile($userId)
    {
        global $router;
        if (isset($_SESSION["user"]) && $_SESSION["user"]["id"] == $userId) {
            header("location: /profile");
            exit();
        }

        $targetUser = User::getById($userId);
        if (!isset($targetUser)) {
            $router->trigger404();
            exit();
        }

        $data = [
            'target_user' => $targetUser,
            'is_self' => false,
            'has_blocked' => false,
            'is_blocked_by' => false,
            'is_friends' => false,
            'has_friend_request' => false,
            'friend_request_pending' => false
        ];

        $currentUser = self::getLoggedUser();
        if ($currentUser) {
            $data['has_blocked'] = $currentUser->hasBlocked($targetUser);
            $data['is_blocked_by'] = $currentUser->isBlockedBy($targetUser);
            $data['is_friends'] = $currentUser->isFriendWith($targetUser);
            $data['has_friend_request'] = $currentUser->hasPendingFriendRequestFrom($targetUser);
            $data['friend_request_pending'] = $currentUser->isFriendRequestPending($targetUser);
        }

        ViewController::renderFromController('auth/profile', $data);
    }

    public static function showFriendsList()
    {
        $user = self::getLoggedUser();
        if (!$user) {
            header("location: /login");
            exit();
        }

        ViewController::renderFromController('auth/friends', [
            'friends' => $user->getFriends(),
            'pending_requests' => $user->getPendingFriendRequests()
        ]);
    }

    // --- API Methods (Auth) ---

    public static function apiLogin()
    {
        $token = $_POST["tript_token"] ?? "";
        FormHelper::ValidateToken($token, "tript_token", ETOKEN_TYPE::AUTHFORM);

        $email = $_POST["email"] ?? null;
        $password = $_POST["password"] ?? null;

        FormHelper::ValidateRequiredField($email, "email");
        FormHelper::ValidateRequiredField($password, "password");
        FormHelper::ValidateMinChars($password, 8, "password");

        $user = User::getByEmail($email);
        if (!isset($user) || !password_verify($password, $user->getPassword())) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode([
                "status" => 400,
                "message" => "Credenciales incorrectas",
                "field" => isset($user) ? "password" : "email"
            ]);
            exit();
        }

        $_SESSION["user"] = $user->toSessionArray();
        echo json_encode(["status" => 200, "message" => "Login successful"]);
        exit();
    }

    public static function apiRegister()
    {
        $token = $_POST["tript_token"] ?? "";
        FormHelper::ValidateToken($token, "tript_token", ETOKEN_TYPE::AUTHFORM);

        $email = $_POST["email"] ?? null;
        $password = $_POST["password"] ?? null;
        $confirmPassword = $_POST["confirmPassword"] ?? null;
        $birthdate = $_POST["birthdate"] ?? null;
        $terms = $_POST["terms"] ?? null;

        FormHelper::ValidateRequiredField($email, "emailAddress");
        FormHelper::ValidateRequiredField($password, "password");
        FormHelper::ValidateRequiredField($confirmPassword, "confirmPassword");
        FormHelper::ValidateRequiredField($birthdate, "birthdate");
        FormHelper::ValidateEmailField($email, "emailAddress");
        FormHelper::ValidateMinAge($birthdate, 14, "birthdate");
        FormHelper::ValidateMinChars($password, 8, "password");
        FormHelper::ValidatePasswordRequirements($password, "password");

        if (!isset($terms) || $terms != "on") {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Debes aceptar los términos", "field" => "terms"]);
            exit();
        }

        if (User::getByEmail($email)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Email ya registrado", "field" => "emailAddress"]);
            exit();
        }

        if ($password != $confirmPassword) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Las contraseñas no coinciden", "field" => "confirmPassword"]);
            exit();
        }

        $username = explode("@", $email)[0];
        $user = new User($email, $username, $password, $birthdate, EUSER_TYPE::USER);
        $user->save();

        $registerEmail = new RegisterEmail("abehsosa2004@gmail.com", $user);
        $registerEmail->send();

        echo json_encode(["status" => 200, "message" => "Usuario registrado"]);
        exit();
    }

    public static function apiEditProfile()
    {
        $token = $_POST["tript_token"] ?? "";
        FormHelper::ValidateToken($token, "tript_token", ETOKEN_TYPE::USERACTION);

        $user = self::getLoggedUserOrExit();

        $username = $_POST["username"] ?? null;
        $motd = $_POST["motd"] ?? null;
        $email = $_POST["email"] ?? null;
        $currentPassword = $_POST["currentPassword"] ?? null;
        $password = $_POST["password"] ?? null;
        $confirmPassword = $_POST["confirmPassword"] ?? null;
        $profilePic = $_FILES["profilePic"] ?? null;

        FormHelper::ValidateRequiredField($username, "username");
        FormHelper::ValidateRequiredField($email, "email");

        if (!empty($password)) {
            FormHelper::ValidateRequiredField($currentPassword, "currentPassword");
            FormHelper::ValidateRequiredField($confirmPassword, "confirmPassword");

            if (!password_verify($currentPassword, $user->getPassword())) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["status" => 400, "message" => "Contraseña actual incorrecta", "field" => "currentPassword"]);
                exit();
            }

            if ($password != $confirmPassword) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["status" => 400, "message" => "Las contraseñas no coinciden", "field" => "confirmPassword"]);
                exit();
            }

            $user->setPassword($password);
            $user->savePassword();
        }

        if (isset($profilePic)) {
            $uuid = Tript::encryptString("userprofilepic" . $user->id);
            S3Helper::upload(EBUCKET_LOCATION::PROFILE_PIC, $uuid, null, $profilePic["type"], $profilePic["tmp_name"]);
            $user->profile_pic = $uuid;
        }

        if ($email != $user->email) {
            FormHelper::ValidateEmailField($email, "email");
            $user->email = $email;
        }

        $user->username = $username;
        $user->motd = $motd;
        $user->save();

        $_SESSION["user"] = $user->toSessionArray();
        echo json_encode(["status" => 200, "message" => "Perfil actualizado"]);
        exit();
    }

    // --- API Methods (Friends) ---

    public static function apiSendFriendRequest($targetUserId)
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::USERACTION);
        $user = self::getLoggedUserOrExit();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Usuario no encontrado"]);
            exit();
        }

        if ($user->sendFriendRequest($targetUser)) {
            echo json_encode(["status" => 200, "message" => "Solicitud enviada"]);
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Error al enviar solicitud"]);
        }
        exit();
    }

    public static function apiAcceptFriendRequest($targetUserId)
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::USERACTION);
        $user = self::getLoggedUserOrExit();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser || !$user->acceptFriendRequest($targetUser)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Error al aceptar solicitud"]);
            exit();
        }

        echo json_encode(["status" => 200, "message" => "Solicitud aceptada"]);
        exit();
    }

    public static function apiDeclineFriendRequest($targetUserId)
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::USERACTION);
        $user = self::getLoggedUserOrExit();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser || !$user->declineFriendRequest($targetUser)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Error al rechazar solicitud"]);
            exit();
        }

        echo json_encode(["status" => 200, "message" => "Solicitud rechazada"]);
        exit();
    }

    public static function apiRemoveFriend($targetUserId)
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::USERACTION);
        $user = self::getLoggedUserOrExit();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser || !$user->removeFriend($targetUser)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Error al eliminar amigo"]);
            exit();
        }

        echo json_encode(["status" => 200, "message" => "Amigo eliminado"]);
        exit();
    }

    public static function apiBlockUser($targetUserId)
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::USERACTION);
        $user = self::getLoggedUserOrExit();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser || !$user->blockUser($targetUser)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Error al bloquear usuario"]);
            exit();
        }

        echo json_encode(["status" => 200, "message" => "Usuario bloqueado"]);
        exit();
    }

    public static function apiUnblockUser($targetUserId)
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::USERACTION);
        $user = self::getLoggedUserOrExit();
        $targetUser = User::getById($targetUserId);

        if (!$targetUser || !$user->unblockUser($targetUser)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "Error al desbloquear usuario"]);
            exit();
        }

        echo json_encode(["status" => 200, "message" => "Usuario desbloqueado"]);
        exit();
    }
}
