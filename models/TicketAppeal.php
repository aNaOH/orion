<?php

require_once "./models/Ticket.php";
require_once "./models/UserSuspension.php";

class TicketAppeal
{
    public static string $table = "ticket_appeals";

    public ?int $id;
    public int $ticket_id;
    public int $suspension_id;
    public string $message;

    public function __construct(
        int $ticket_id,
        int $suspension_id,
        string $message,
        ?int $id = null
    ) {
        $this->ticket_id = $ticket_id;
        $this->suspension_id = $suspension_id;
        $this->message = $message;
        $this->id = $id;
    }

    public static function getByTicketId(int $ticket_id): ?TicketAppeal
    {
        $data = Connection::doSelect(ORION_DB, self::$table, ["ticket_id" => $ticket_id]);
        if (count($data) === 1) {
            return new TicketAppeal(
                $data[0]["ticket_id"],
                $data[0]["suspension_id"],
                $data[0]["message"],
                $data[0]["id"]
            );
        }
        return null;
    }

    public function save(): bool
    {
        $data = [
            "ticket_id" => $this->ticket_id,
            "suspension_id" => $this->suspension_id,
            "message" => $this->message,
        ];

        if (!isset($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool) $result;
        } else {
            return (bool) Connection::doUpdate(ORION_DB, self::$table, $data, ["id" => $this->id]);
        }
    }

    public function getSuspension(): ?UserSuspension
    {
        return UserSuspension::getById($this->suspension_id);
    }
}
