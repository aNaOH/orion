<?php

require_once "./models/Ticket.php";

class TicketReportUser
{
    public static string $table = "ticket_report_user";

    public ?int $id;
    public int $ticket_id;
    public int $reported_user_id;
    public string $reason;
    public string $description;
    public array $snapshot; // Decoded JSON

    public function __construct(
        int $ticket_id,
        int $reported_user_id,
        string $reason,
        string $description,
        array $snapshot,
        ?int $id = null
    ) {
        $this->ticket_id = $ticket_id;
        $this->reported_user_id = $reported_user_id;
        $this->reason = $reason;
        $this->description = $description;
        $this->snapshot = $snapshot;
        $this->id = $id;
    }

    public static function getByTicketId(int $ticket_id): ?TicketReportUser
    {
        $data = Connection::doSelect(ORION_DB, self::$table, ["ticket_id" => $ticket_id]);
        if (count($data) === 1) {
            return new TicketReportUser(
                $data[0]["ticket_id"],
                $data[0]["reported_user_id"],
                $data[0]["reason"],
                $data[0]["description"],
                json_decode($data[0]["snapshot"], true),
                $data[0]["id"]
            );
        }
        return null;
    }

    public function save(): bool
    {
        $data = [
            "ticket_id" => $this->ticket_id,
            "reported_user_id" => $this->reported_user_id,
            "reason" => $this->reason,
            "description" => $this->description,
            "snapshot" => json_encode($this->snapshot)
        ];

        if (!isset($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool) $result;
        } else {
            return (bool) Connection::doUpdate(ORION_DB, self::$table, $data, ["id" => $this->id]);
        }
    }

    public function getReportedUser(): ?User
    {
        return User::getById($this->reported_user_id);
    }
}
