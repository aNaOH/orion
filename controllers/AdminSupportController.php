<?php

require_once "./models/User.php";
require_once "./models/Ticket.php";
require_once "./models/TicketReportUser.php";
require_once "./models/TicketAppeal.php";
require_once "./models/TicketGeneral.php";
require_once "./models/TicketLoginRecovery.php";
require_once "./models/UserSuspension.php";
require_once "./emails/TicketResponseEmail.php";
require_once "./emails/UserSuspendedEmail.php";
require_once "./emails/UserUnsuspendedEmail.php";
require_once "./helpers/forms.php";

class AdminSupportController
{
    public static function index()
    {
        $tickets = Ticket::getAll();
        ViewController::render('admin/support/index', [
            'tickets' => $tickets
        ]);
    }

    public static function view($id)
    {
        $ticket = Ticket::getById($id);
        if (!$ticket) {
            ViewController::render('errors/404');
            exit();
        }

        $report = null;
        if ($ticket->type === "report_user") {
            $report = TicketReportUser::getByTicketId($ticket->id);
        }

        $appeal = null;
        $original_suspension = null;
        if ($ticket->type === "appeal") {
            $appeal = TicketAppeal::getByTicketId($ticket->id);
            if ($appeal) {
                $original_suspension = $appeal->getSuspension();
            }
        }

        $general = null;
        if ($ticket->type === "general") {
            $general = TicketGeneral::getByTicketId($ticket->id);
        }

        $recovery = null;
        $target_user = null;
        if ($ticket->type === "login_recovery") {
            $recovery = TicketLoginRecovery::getByTicketId($ticket->id);
            if ($recovery) {
                $target_user = $recovery->getTargetUser();
            }
        }

        ViewController::render('admin/support/detail', [
            'ticket' => $ticket,
            'report' => $report,
            'appeal' => $appeal,
            'general' => $general,
            'recovery' => $recovery,
            'target_user' => $target_user,
            'original_suspension' => $original_suspension,
            'reporter' => $ticket->getReporter(),
            'reported_user' => $report ? $report->getReportedUser() : null,
            'active_suspension' => $report ? $report->getReportedUser()?->getActiveSuspension() : null,
            'server_timezone' => date('T'),
        ]);
    }

    public static function apiUpdateStatus()
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::COMMON);

        $id = $_POST["id"] ?? null;
        $status = $_POST["status"] ?? null;
        $adminComment = $_POST["admin_comment"] ?? null;
        $moderationAction = $_POST["moderation_action"] ?? "none";
        $suspensionReason = trim($_POST["suspension_reason"] ?? "");
        $suspensionUntil = $_POST["suspension_until"] ?? null;
        $newEmail = $_POST["new_email"] ?? null;
        $resetPassword = isset($_POST["reset_password"]) && $_POST["reset_password"] === "on";

        FormHelper::ValidateRequiredField($id, "id");
        FormHelper::ValidateRequiredField($status, "status");
        FormHelper::ValidateAllowedValue($status, ["1", "2", 1, 2], "status", "El estado indicado no es válido.");
        FormHelper::ValidateAllowedValue(
            $moderationAction,
            ["none", "suspend_temp", "suspend_indef"],
            "moderation_action",
            "La acción de moderación indicada no es válida."
        );

        if ($status == 2) { // Rejected
            FormHelper::ValidateRequiredField($adminComment, "admin_comment");
            FormHelper::ValidateMinChars(trim($adminComment), 5, "admin_comment");
        }

        $ticket = Ticket::getById($id);
        if (!$ticket) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Ticket no encontrado"]);
            exit();
        }

        $report = $ticket->type === "report_user" ? TicketReportUser::getByTicketId($ticket->id) : null;
        $appeal = $ticket->type === "appeal" ? TicketAppeal::getByTicketId($ticket->id) : null;

        if ((int) $status === 1 && $moderationAction !== "none") {
            FormHelper::ValidateBusinessRule(
                $report !== null,
                "Solo los reportes de usuario pueden aplicar suspensiones.",
                "moderation_action"
            );
            FormHelper::ValidateRequiredField($adminComment, "admin_comment");
            FormHelper::ValidateMinChars(trim($adminComment), 5, "admin_comment");
            FormHelper::ValidateRequiredField($suspensionReason, "suspension_reason");
            FormHelper::ValidateMinChars($suspensionReason, 3, "suspension_reason");
            FormHelper::ValidateMaxChars($suspensionReason, 255, "suspension_reason");

            $reportedUser = $report->getReportedUser();
            FormHelper::ValidateBusinessRule(
                $reportedUser !== null,
                "No se ha encontrado al usuario reportado.",
                "moderation_action"
            );
            FormHelper::ValidateBusinessRule(
                $reportedUser->getActiveSuspension() === null,
                "El usuario reportado ya tiene una suspensión activa.",
                "moderation_action",
                409
            );

            if ($moderationAction === "suspend_temp") {
                FormHelper::ValidateRequiredField($suspensionUntil, "suspension_until");
                $suspensionUntilDate = FormHelper::ValidateDateTimeField(
                    $suspensionUntil,
                    "suspension_until",
                    "Indica una fecha de fin válida para la suspensión."
                );
                FormHelper::ValidateFutureDateTime(
                    $suspensionUntilDate,
                    "suspension_until",
                    "La suspensión temporal debe finalizar en una fecha futura."
                );
            }
        }

        // If it's an accepted login recovery, perform the requested actions
        if ($ticket->type === 'login_recovery' && (int)$status === 1) {
            $recovery = TicketLoginRecovery::getByTicketId($ticket->id);
            if ($recovery) {
                $targetUser = $recovery->getTargetUser();
                if ($targetUser) {
                    if ($newEmail && trim($newEmail) !== "" && $newEmail !== $targetUser->email) {
                        FormHelper::ValidateEmailField($newEmail, "new_email");
                        // Check if email already exists
                        if (User::getByEmail($newEmail) === null) {
                            $targetUser->email = trim($newEmail);
                        }
                    }
                    if ($resetPassword) {
                        $newPass = bin2hex(random_bytes(4)); // 8 chars
                        $targetUser->password = password_hash($newPass, PASSWORD_BCRYPT);
                        $adminComment .= "\n\n[SISTEMA]: Se ha generado una nueva contraseña: " . $newPass;
                    }
                    $targetUser->save();
                }
            }
        }

        $ticket->status = (int) $status;
        $ticket->admin_comment = trim((string) $adminComment) !== "" ? trim((string) $adminComment) : null;
        $ticket->save();

        if ((int) $status === 1 && $moderationAction !== "none" && $report !== null) {
            $reportedUser = $report->getReportedUser();
            $suspension = new UserSuspension(
                $reportedUser->id,
                $ticket->id,
                $suspensionReason,
                $ticket->admin_comment,
                (new DateTime())->format("Y-m-d H:i:s"),
                $moderationAction === "suspend_temp" ? $suspensionUntilDate->format("Y-m-d H:i:s") : null,
                true
            );
            $suspension->save();

            try {
                $email = new UserSuspendedEmail($reportedUser->email, $reportedUser, $suspension);
                $email->send();
            } catch (Exception $e) {
                error_log("Failed to send suspension email to " . $reportedUser->email);
            }
        }

        // Send Email to User
        $reporter = $ticket->getReporter();
        if ($reporter) {
            try {
                $email = new TicketResponseEmail($reporter->email, $reporter, $ticket);
                $email->send();
            } catch (Exception $e) {
                error_log("Failed to send ticket response email to " . $reporter->email);
            }
        }

        echo json_encode(["status" => 200, "message" => "Ticket actualizado correctamente"]);
        exit();
    }

    public static function showSuspendUser($userId)
    {
        $targetUser = User::getById($userId);
        if (!$targetUser) {
            ViewController::render('errors/404');
            exit();
        }

        ViewController::render('admin/support/suspend_user', [
            'target_user' => $targetUser,
            'active_suspension' => $targetUser->getActiveSuspension(),
            'server_timezone' => date('T'),
        ]);
    }

    public static function apiSuspendUser()
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::COMMON);

        $userId = $_POST["user_id"] ?? null;
        $moderationAction = $_POST["moderation_action"] ?? "none";
        $suspensionReason = trim($_POST["suspension_reason"] ?? "");
        $suspensionUntil = $_POST["suspension_until"] ?? null;
        $adminComment = $_POST["admin_comment"] ?? null;

        FormHelper::ValidateRequiredField($userId, "user_id");
        FormHelper::ValidateAllowedValue(
            $moderationAction,
            ["suspend_temp", "suspend_indef"],
            "moderation_action",
            "La acción de moderación indicada no es válida."
        );

        FormHelper::ValidateRequiredField($suspensionReason, "suspension_reason");
        FormHelper::ValidateMinChars($suspensionReason, 3, "suspension_reason");
        FormHelper::ValidateRequiredField($adminComment, "admin_comment");
        FormHelper::ValidateMinChars(trim($adminComment), 5, "admin_comment");

        $targetUser = User::getById($userId);
        if (!$targetUser) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Usuario no encontrado"]);
            exit();
        }

        FormHelper::ValidateBusinessRule(
            $targetUser->getActiveSuspension() === null,
            "El usuario ya tiene una suspensión activa.",
            "moderation_action",
            409
        );

        $suspensionUntilDate = null;
        if ($moderationAction === "suspend_temp") {
            FormHelper::ValidateRequiredField($suspensionUntil, "suspension_until");
            $suspensionUntilDate = FormHelper::ValidateDateTimeField(
                $suspensionUntil,
                "suspension_until",
                "Indica una fecha de fin válida para la suspensión."
            );
            FormHelper::ValidateFutureDateTime(
                $suspensionUntilDate,
                "suspension_until",
                "La suspensión temporal debe finalizar en una fecha futura."
            );
        }

        $suspension = new UserSuspension(
            $targetUser->id,
            null,
            $suspensionReason,
            $adminComment,
            (new DateTime())->format("Y-m-d H:i:s"),
            $suspensionUntilDate ? $suspensionUntilDate->format("Y-m-d H:i:s") : null,
            true
        );
        $suspension->save();

        try {
            $email = new UserSuspendedEmail($targetUser->email, $targetUser, $suspension);
            $email->send();
        } catch (Exception $e) {
            error_log("Failed to send suspension email to " . $targetUser->email);
        }

        echo json_encode(["status" => 200, "message" => "Usuario suspendido correctamente"]);
        exit();
    }

    public static function usersIndex()
    {
        $users = User::getAll();
        ViewController::render('admin/users/index', [
            'users' => $users,
            'server_timezone' => date('T'),
        ]);
    }

    public static function apiUnsuspendUser()
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::COMMON);

        $userId = $_POST["user_id"] ?? null;
        $reason = trim($_POST["reason"] ?? "");

        FormHelper::ValidateRequiredField($userId, "user_id");
        FormHelper::ValidateRequiredField($reason, "reason");
        FormHelper::ValidateMinChars($reason, 5, "reason");

        $user = User::getById($userId);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Usuario no encontrado"]);
            exit();
        }

        $suspension = $user->getActiveSuspension();
        if (!$suspension) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "El usuario no tiene una suspensión activa"]);
            exit();
        }

        // Revoke suspension
        $suspension->is_active = false;
        // Optionally store the revocation reason in admin_comment if we want to track it
        $suspension->admin_comment .= "\n\n[REVOCACIÓN]: " . $reason;
        $suspension->save();

        // Send Email
        try {
            $email = new UserUnsuspendedEmail($user->email, $user, $reason);
            $email->send();
        } catch (Exception $e) {
            error_log("Failed to send unsuspension email to " . $user->email);
        }

        echo json_encode(["status" => 200, "message" => "Suspensión revocada correctamente"]);
        exit();
    }
}
