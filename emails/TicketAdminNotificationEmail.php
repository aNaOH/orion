<?php

require_once __DIR__ . "/Email.php";

class TicketAdminNotificationEmail extends Email
{
    private $ticket;
    private $reporter;

    public function __construct(string $to, Ticket $ticket, User $reporter)
    {
        $this->ticket = $ticket;
        $this->reporter = $reporter;
        parent::__construct($to);
    }

    protected function getSubject(): string
    {
        return "[ADMIN] Nuevo ticket de soporte #" . $this->ticket->id;
    }

    protected function getTemplatePath(): string
    {
        return "emails/ticket_admin_notification.twig";
    }

    protected function getVariables(): array
    {
        return [
            "ticket_id" => $this->ticket->id,
            "ticket_type" => $this->ticket->type,
            "reporter_name" => $this->reporter->username,
            "admin_url" => $_ENV["APP_URL"] . "/admin/tickets/" . $this->ticket->id
        ];
    }
}
