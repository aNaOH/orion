<?php

require_once "./models/User.php";
require_once "./models/Ticket.php";
require_once "./models/TicketReportUser.php";
require_once "./helpers/s3.php";
require_once "./emails/TicketCreatedEmail.php";
require_once "./emails/TicketAdminNotificationEmail.php";
require_once "./helpers/forms.php";

class SupportController
{
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

    public static function showReportUser($reportedUserId)
    {
        $user = self::getLoggedUser();
        if (!$user) {
            header("location: /login?to=" . urlencode($_SERVER['REQUEST_URI']));
            exit();
        }

        $targetUser = User::getById($reportedUserId);
        if (!$targetUser) {
            ViewController::render('errors/404');
            exit();
        }

        ViewController::render('support/report_user', [
            'target_user' => $targetUser,
            'reasons' => TicketReportUser::getReasons(),
            'selected_reason' => $_GET['reason'] ?? '',
            'description' => $_GET['description'] ?? ''
        ]);
    }

    public static function apiCreateReportUser()
    {
        $user = self::getLoggedUserOrExit();
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::USERACTION);

        $reportedId = $_POST["reported_id"] ?? null;
        $reason = $_POST["reason"] ?? null;
        $description = $_POST["description"] ?? "";

        FormHelper::ValidateRequiredField($reportedId, "reported_id");
        FormHelper::ValidateRequiredField($reason, "reason");
        FormHelper::ValidateRequiredField($description, "description");
        FormHelper::ValidateAllowedValue($reason, TicketReportUser::getAllowedReasonKeys(), "reason", "Selecciona un motivo de reporte válido.");
        FormHelper::ValidateMinChars(trim($description), 10, "description");
        FormHelper::ValidateMaxChars(trim($description), 2000, "description");
        FormHelper::ValidateNotSameValue($reportedId, $user->id, "reported_id", "No puedes reportarte a ti mismo.");

        $targetUser = User::getById($reportedId);
        if (!$targetUser) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Usuario reportado no encontrado"]);
            exit();
        }

        FormHelper::ValidateBusinessRule(
            !TicketReportUser::hasPendingDuplicate($user->id, $targetUser->id, $reason),
            "Ya tienes un reporte pendiente contra este usuario por este motivo.",
            "reason",
            409
        );

        // 1. Create base Ticket
        $ticket = new Ticket($user->id, "report_user");
        $ticket->save();

        // 2. Capture Snapshot & Image
        $profilePicKey = $targetUser->profile_pic;
        $snapshotPicKey = $profilePicKey ? "snapshot_" . $ticket->id . "_" . $profilePicKey : "default.png";

        if ($profilePicKey) {
            S3Helper::copy(EBUCKET_LOCATION::PROFILE_PIC, $profilePicKey, EBUCKET_LOCATION::SNAPSHOT, $snapshotPicKey);
        }

        $snapshot = [
            "username" => $targetUser->username,
            "motd" => $targetUser->motd,
            "profile_pic" => $snapshotPicKey
        ];

        // 3. Create Specific Report
        $report = new TicketReportUser($ticket->id, $targetUser->id, $reason, trim($description), $snapshot);
        $report->save();

        // 4. Send Emails
        // To User
        $userEmail = new TicketCreatedEmail($user->email, $user, $ticket);
        $userEmail->send();

        // To Admins
        $admins = User::all();
        foreach ($admins as $admin) {
            if ($admin->role === EUSER_TYPE::ADMIN) {
                try {
                    $adminEmail = new TicketAdminNotificationEmail($admin->email, $ticket, $user);
                    $adminEmail->send();
                } catch (Exception $e) {
                    // Log error but continue
                    error_log("Failed to send admin notification email to " . $admin->email);
                }
            }
        }

        echo json_encode(["status" => 200, "message" => "Reporte enviado correctamente", "ticket_id" => $ticket->id]);
        exit();
    }
}
