<?php

require_once __DIR__ . "/Email.php";

class TicketCreatedEmail extends Email
{
    private $user;
    private $ticket;

    public function __construct(string $to, User $user, Ticket $ticket)
    {
        $this->user = $user;
        $this->ticket = $ticket;
        parent::__construct($to);
    }

    protected function getSubject(): string
    {
        return "Hemos recibido tu reporte - Ticket #" . $this->ticket->id;
    }

    protected function getTemplatePath(): string
    {
        return "emails/ticket_created.twig";
    }

    protected function getVariables(): array
    {
        return [
            "user_name" => $this->user->username,
            "ticket_id" => $this->ticket->id,
            "ticket_type" => $this->ticket->type,
        ];
    }
}
