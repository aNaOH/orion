<?php

require_once "./models/Ticket.php";

class TicketReportUser
{
    public static string $table = "ticket_report_user";
    private const REASONS = [
        "name" => "Nombre ofensivo",
        "motd" => "MOTD ofensivo",
        "avatar" => "Foto de perfil ofensiva",
        "spam" => "Spam",
        "impersonation" => "Suplantación de identidad",
    ];

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

    public static function getReasons(): array
    {
        return self::REASONS;
    }

    public static function getAllowedReasonKeys(): array
    {
        return array_keys(self::REASONS);
    }

    public function getReasonLabel(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }

    public static function hasPendingDuplicate(int $reporterId, int $reportedUserId, string $reason): bool
    {
        $sql = "SELECT t.id
                FROM tickets t
                INNER JOIN ticket_report_user tru ON tru.ticket_id = t.id
                WHERE t.user_id = :reporter_id
                  AND t.type = 'report_user'
                  AND t.status = 0
                  AND tru.reported_user_id = :reported_user_id
                  AND tru.reason = :reason
                LIMIT 1";

        $row = Connection::customQuery(ORION_DB, $sql, [
            "reporter_id" => $reporterId,
            "reported_user_id" => $reportedUserId,
            "reason" => $reason,
        ])->fetch(PDO::FETCH_ASSOC);

        return $row !== false;
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
