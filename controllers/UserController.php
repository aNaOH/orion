<?php

require_once "./models/User.php";
require_once "./emails/RegisterEmail.php";

class UserController
{
    public static function register(
        $email,
        $password,
        $confirmPassword,
        $birthdate,
        $terms,
    ) {
        $response = [];

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
            $response["status"] = 400;
            $response["message"] =
                "Debes aceptar los términos y condiciones y la política de privacidad";
            $response["field"] = "terms";

            echo json_encode($response);
            exit();
        }

        $user = User::getByEmail($email);

        if (isset($user)) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["message"] = "An user with that email exists";
            $response["field"] = "emailAddress";

            echo json_encode($response);
            exit();
        }

        if ($password != $confirmPassword) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["message"] = "Las contraseñas deben coincidir.";
            $response["field"] = "confirmPassword";

            echo json_encode($response);
            exit();
        }

        $username = explode("@", $email)[0];

        $user = new User(
            $email,
            $username,
            $password,
            $birthdate,
            EUSER_TYPE::USER,
        );
        $user->save();

        $email = new RegisterEmail("abehsosa2004@gmail.com", $user);
        $email->send();

        header("HTTP/1.1 200 OK");
        $response["status"] = 200;
        $response["message"] =
            "Usuario creado ( ID: " . strval($user->id) . " )";

        echo json_encode($response);
        exit();
    }

    public static function edit(
        User $user,
        $username,
        $motd,
        $profilePic,
        $email,
        $currentPassword,
        $password,
        $confirmPassword,
        $token,
    ) {
        $response = [];

        FormHelper::ValidateToken(
            $token,
            "tript_token",
            ETOKEN_TYPE::USERACTION,
        );

        FormHelper::ValidateRequiredField($username, "username");
        FormHelper::ValidateRequiredField($email, "email");

        if (
            !is_null($password) &&
            strlen($password) &&
            !is_null($currentPassword) &&
            strlen($currentPassword) &&
            !is_null($confirmPassword) &&
            strlen($confirmPassword)
        ) {
            var_dump($password);
            var_dump($currentPassword);
            var_dump($confirmPassword);

            FormHelper::ValidateMinChars(
                $currentPassword,
                8,
                "currentPassword",
            );
            FormHelper::ValidatePasswordRequirements(
                $currentPassword,
                "currentPassword",
            );

            if (!password_verify($password, $user->getPassword())) {
                header("HTTP/1.1 400 Bad Request");
                $response["status"] = 400;
                $response["message"] = "Contraseña incorrecta";
                $response["field"] = "password";

                echo json_encode($response);
                exit();
            }

            if ($password != $confirmPassword) {
                header("HTTP/1.1 400 Bad Request");
                $response["status"] = 400;
                $response["message"] = "Las contraseñas deben coincidir.";
                $response["field"] = "confirmPassword";

                echo json_encode($response);
                exit();
            }

            $user->setPassword($password);
            $user->savePassword();
        }

        if (isset($profilePic)) {
            $uuid = Tript::encryptString("userprofilepic" . strval($user->id));
            S3Helper::upload(
                EBUCKET_LOCATION::PROFILE_PIC,
                $uuid,
                null,
                $profilePic["type"],
                $profilePic["tmp_name"],
            );
            $user->profile_pic = $uuid;
        }

        if ($email != $user->email) {
            FormHelper::ValidateEmailField($email, "emailAddress");
            $user->email = $email;
        }

        $user->username = $username;
        $user->motd = $motd;

        $user->save();

        $_SESSION["user"] = $user->toSessionArray();

        header("HTTP/1.1 200 OK");
        $response["status"] = 200;
        $response["message"] =
            "Usuario editado ( ID: " . strval($user->id) . " )";

        echo json_encode($response);
        exit();
    }

    public static function login($email, $password)
    {
        $response = [];

        FormHelper::ValidateRequiredField($email, "email");
        FormHelper::ValidateRequiredField($password, "password");

        FormHelper::ValidateMinChars($password, 8, "password");

        $user = User::getByEmail($email);

        if (!isset($user)) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["message"] =
                "No existe un usuario con ese correo electrónico.";
            $response["value"] = $email;
            $response["field"] = "email";

            echo json_encode($response);
            exit();
        }

        if (!password_verify($password, $user->getPassword())) {
            header("HTTP/1.1 400 Bad Request");
            $response["status"] = 400;
            $response["message"] = "Contraseña incorrecta";
            $response["field"] = "password";

            echo json_encode($response);
            exit();
        }

        $_SESSION["user"] = $user->toSessionArray();

        header("HTTP/1.1 200 OK");
        $response["status"] = 200;
        $response["message"] =
            "User with ID " . strval($user->id) . " logged in";

        echo json_encode($response);
        exit();
    }
}
