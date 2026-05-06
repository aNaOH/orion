<?php

require_once "./models/User.php";
require_once "./models/Ticket.php";
require_once "./models/TicketReportUser.php";
require_once "./models/TicketAppeal.php";
require_once "./models/TicketGeneral.php";
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

    public static function index()
    {
        $user = self::getLoggedUser();
        ViewController::render('support/index', [
            'user' => $user
        ]);
    }

    public static function showFAQ()
    {
        $user = self::getLoggedUser();
        ViewController::render('support/faq', [
            'user' => $user
        ]);
    }

    public static function showSafety()
    {
        $user = self::getLoggedUser();
        ViewController::render('support/safety', [
            'user' => $user
        ]);
    }

    public static function showUserTickets()
    {
        $user = self::getLoggedUser();
        if (!$user) {
            header("location: /login?to=" . urlencode($_SERVER['REQUEST_URI']));
            exit();
        }

        $tickets = Ticket::getByUserId($user->id);

        ViewController::render('support/tickets', [
            'user' => $user,
            'tickets' => $tickets
        ]);
    }

    public static function showCreateTicket()
    {
        $user = self::getLoggedUser();
        ViewController::render('support/create', [
            'user' => $user,
            'categories' => TicketGeneral::getCategories()
        ]);
    }

    public static function apiCreateTicket()
    {
        $user = self::getLoggedUser(); // Don't exit if not logged in
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::USERACTION);

        $category = $_POST["category"] ?? null;
        $subject = $_POST["subject"] ?? null;
        $message = $_POST["message"] ?? null;
        $guestEmail = $_POST["guest_email"] ?? null;

        if (!$user) {
            FormHelper::ValidateRequiredField($guestEmail, "guest_email");
            FormHelper::ValidateEmailField($guestEmail, "guest_email");
            // If guest, only allow login_issue
            if ($category !== 'login_issue') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(["status" => 403, "message" => "Como invitado, solo puedes crear tickets por problemas de inicio de sesión."]);
                exit();
            }
        }

        FormHelper::ValidateRequiredField($category, "category");
        FormHelper::ValidateRequiredField($subject, "subject");
        FormHelper::ValidateRequiredField($message, "message");
        FormHelper::ValidateAllowedValue($category, TicketGeneral::getAllowedCategoryKeys(), "category", "Selecciona una categoría válida.");
        FormHelper::ValidateMinChars(trim($subject), 5, "subject");
        FormHelper::ValidateMaxChars(trim($subject), 100, "subject");
        FormHelper::ValidateMinChars(trim($message), 20, "message");
        FormHelper::ValidateMaxChars(trim($message), 5000, "message");

        // 1. Create base Ticket
        $type = ($category === 'login_issue') ? "login_recovery" : "general";
        $ticket = new Ticket($user ? $user->id : null, $type);
        $ticket->save();

        // 2. Create specific Ticket details
        if ($type === "login_recovery") {
            require_once "models/TicketLoginRecovery.php";
            // For recovery, we can try to find the target user if they mentioned it in the subject or message
            // But better to add a field for it. For now, we'll just save it as recovery.
            $recoveryTicket = new TicketLoginRecovery($ticket->id, $guestEmail ?? ($user ? $user->email : ""), trim($message));
            
            // Basic heuristic: if the subject is a username or email that exists
            $targetUser = User::getByUsername(trim($subject)) ?? User::getByEmail(trim($subject));
            if ($targetUser) {
                $recoveryTicket->target_user_id = $targetUser->id;
            }
            
            $recoveryTicket->save();
        } else {
            $generalTicket = new TicketGeneral($ticket->id, $category, trim($subject), trim($message), $guestEmail);
            $generalTicket->save();
        }

        // 3. Notify Admins
        $admins = User::all();
        foreach ($admins as $admin) {
            if ($admin->role === EUSER_TYPE::ADMIN) {
                try {
                    $adminEmail = new TicketAdminNotificationEmail($admin->email, $ticket, $user);
                    $adminEmail->send();
                } catch (Exception $e) {}
            }
        }

        echo json_encode(["status" => 200, "message" => "Tu ticket ha sido creado correctamente.", "ticket_id" => $ticket->id]);
        exit();
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

        if ($user->id == $targetUser->id) {
            header("location: /support");
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

    public static function showAppeal()
    {
        $user = self::getLoggedUser();
        if (!$user) {
            header("location: /login?to=" . urlencode($_SERVER['REQUEST_URI']));
            exit();
        }

        $suspension = $user->getActiveSuspension();
        if (!$suspension) {
            header("location: /");
            exit();
        }

        ViewController::render('support/appeal', [
            'suspension' => $suspension
        ]);
    }

    public static function apiCreateAppeal()
    {
        $user = self::getLoggedUserOrExit();
        FormHelper::ValidateToken($_POST["tript_token"] ?? "", "tript_token", ETOKEN_TYPE::USERACTION);

        $message = $_POST["message"] ?? null;
        FormHelper::ValidateRequiredField($message, "message");
        FormHelper::ValidateMinChars(trim($message), 20, "message");
        FormHelper::ValidateMaxChars(trim($message), 3000, "message");

        $suspension = $user->getActiveSuspension();
        if (!$suspension) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["status" => 400, "message" => "No tienes una suspensión activa que apelar."]);
            exit();
        }

        // Check if there's already a pending appeal
        $sql = "SELECT id FROM tickets WHERE user_id = :user_id AND type = 'appeal' AND status = 0";
        $row = Connection::customQuery(ORION_DB, $sql, ["user_id" => $user->id])->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            header("HTTP/1.1 409 Conflict");
            echo json_encode(["status" => 409, "message" => "Ya tienes una apelación pendiente."]);
            exit();
        }

        $ticket = new Ticket($user->id, "appeal");
        $ticket->save();

        $appeal = new TicketAppeal($ticket->id, $suspension->id, trim($message));
        $appeal->save();

        // Notify admins
        $admins = User::all();
        foreach ($admins as $admin) {
            if ($admin->role === EUSER_TYPE::ADMIN) {
                try {
                    $adminEmail = new TicketAdminNotificationEmail($admin->email, $ticket, $user);
                    $adminEmail->send();
                } catch (Exception $e) {}
            }
        }

        echo json_encode(["status" => 200, "message" => "Tu apelación ha sido enviada correctamente."]);
        exit();
    }
}
