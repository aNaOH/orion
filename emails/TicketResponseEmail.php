<?php

require_once __DIR__ . "/Email.php";

class TicketResponseEmail extends Email
{
    private $ticket;
    private $user;

    public function __construct(string $to, User $user, Ticket $ticket)
    {
        $this->user = $user;
        $this->ticket = $ticket;
        parent::__construct($to);
    }

    protected function getSubject(): string
    {
        $statusStr = $this->ticket->status == 1 ? "revisado" : "rechazado";
        return "Actualización sobre tu reporte en Orion - Ticket #" . $this->ticket->id . " [" . ucfirst($statusStr) . "]";
    }

    protected function getTemplatePath(): string
    {
        return "emails/ticket_response.twig";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => $this->user->username,
            "ticket_id" => $this->ticket->id,
            "status" => $this->ticket->status, // 1: Accepted, 2: Rejected
            "admin_comment" => $this->ticket->admin_comment
        ];
    }
}
