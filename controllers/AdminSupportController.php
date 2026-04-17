<?php

require_once "./models/User.php";
require_once "./models/Ticket.php";
require_once "./models/TicketReportUser.php";
require_once "./emails/TicketResponseEmail.php";

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
            'reported_user' => $report ? $report->getReportedUser() : null
        ]);
    }

    public static function apiUpdateStatus()
    {
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::COMMON);

        $id = $_POST["id"] ?? null;
        $status = $_POST["status"] ?? null;
        $adminComment = $_POST["admin_comment"] ?? null;

        FormHelper::ValidateRequiredField($id, "id");
        FormHelper::ValidateRequiredField($status, "status");

        if ($status == 2) { // Rejected
            FormHelper::ValidateRequiredField($adminComment, "admin_comment");
        }

        $ticket = Ticket::getById($id);
        if (!$ticket) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(["status" => 404, "message" => "Ticket no encontrado"]);
            exit();
        }

        $ticket->status = (int) $status;
        $ticket->admin_comment = $adminComment;
        $ticket->save();

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
