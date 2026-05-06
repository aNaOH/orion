<?php

require_once "./models/Ticket.php";

class TicketGeneral
{
    public static string $table = "ticket_general";
    
    public const CATEGORIES = [
        "technical" => "Problema Técnico",
        "billing" => "Pagos y Facturación",
        "account" => "Seguridad de Cuenta",
        "login_issue" => "Problemas con el inicio de sesión",
        "general" => "Consulta General",
        "feedback" => "Sugerencias y Feedback"
    ];

    public ?int $id;
    public int $ticket_id;
    public string $category;
    public string $subject;
    public string $message;
    public ?string $guest_email;

    public function __construct(
        int $ticket_id,
        string $category,
        string $subject,
        string $message,
        ?string $guest_email = null,
        ?int $id = null
    ) {
        $this->ticket_id = $ticket_id;
        $this->category = $category;
        $this->subject = $subject;
        $this->message = $message;
        $this->guest_email = $guest_email;
        $this->id = $id;
    }

    public static function getByTicketId(int $ticketId): ?TicketGeneral
    {
        $data = Connection::doSelect(ORION_DB, self::$table, ["ticket_id" => $ticketId]);
        if (count($data) === 1) {
            return new TicketGeneral(
                $data[0]["ticket_id"],
                $data[0]["category"],
                $data[0]["subject"],
                $data[0]["message"],
                $data[0]["guest_email"],
                $data[0]["id"]
            );
        }
        return null;
    }

    public static function getCategories(): array
    {
        return self::CATEGORIES;
    }

    public static function getAllowedCategoryKeys(): array
    {
        return array_keys(self::CATEGORIES);
    }

    public function getCategoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function save(): bool
    {
        $data = [
            "ticket_id" => $this->ticket_id,
            "category" => $this->category,
            "subject" => $this->subject,
            "message" => $this->message,
            "guest_email" => $this->guest_email
        ];

        if (!isset($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool) $result;
        } else {
            return (bool) Connection::doUpdate(ORION_DB, self::$table, $data, ["id" => $this->id]);
        }
    }
}
