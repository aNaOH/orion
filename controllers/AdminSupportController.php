<?php

require_once "./models/User.php";
require_once "./models/Ticket.php";
require_once "./models/TicketReportUser.php";
require_once "./models/UserSuspension.php";
require_once "./emails/TicketResponseEmail.php";
require_once "./emails/UserSuspendedEmail.php";
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

        ViewController::render('admin/support/detail', [
            'ticket' => $ticket,
            'report' => $report,
            'reporter' => $ticket->getReporter(),
            'reported_user' => $report ? $report->getReportedUser() : null,
            'active_suspension' => $report ? $report->getReportedUser()?->getActiveSuspension() : null,
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
}
