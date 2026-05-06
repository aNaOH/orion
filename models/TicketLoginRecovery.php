<?php

class TicketLoginRecovery
{
    private static string $table = "ticket_login_recovery";

    public ?int $id;
    public int $ticket_id;
    public ?int $target_user_id;
    public string $contact_email;
    public string $message;

    public function __construct(
        int $ticket_id,
        string $contact_email,
        string $message,
        ?int $target_user_id = null,
        ?int $id = null
    ) {
        $this->ticket_id = $ticket_id;
        $this->contact_email = $contact_email;
        $this->message = $message;
        $this->target_user_id = $target_user_id;
        $this->id = $id;
    }

    public static function getByTicketId(int $ticketId): ?TicketLoginRecovery
    {
        $data = Connection::doSelect(ORION_DB, self::$table, ["ticket_id" => $ticketId]);
        if (count($data) === 1) {
            return new TicketLoginRecovery(
                $data[0]["ticket_id"],
                $data[0]["contact_email"],
                $data[0]["message"],
                $data[0]["target_user_id"],
                $data[0]["id"]
            );
        }
        return null;
    }

    public function getTargetUser(): ?User
    {
        if (!$this->target_user_id) return null;
        return User::getById($this->target_user_id);
    }

    public function save(): bool
    {
        $data = [
            "ticket_id" => $this->ticket_id,
            "contact_email" => $this->contact_email,
            "message" => $this->message,
            "target_user_id" => $this->target_user_id
        ];

        if (!isset($this->id)) {
            $this->id = Connection::doInsert(ORION_DB, self::$table, $data);
            return $this->id > 0;
        } else {
            return Connection::doUpdate(ORION_DB, self::$table, $data, ["id" => $this->id]);
        }
    }
}
