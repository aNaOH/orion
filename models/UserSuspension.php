<?php

class UserSuspension
{
    public static string $table = "user_suspensions";

    public ?int $id;
    public int $user_id;
    public ?int $ticket_id;
    public string $reason;
    public ?string $admin_comment;
    public string $starts_at;
    public ?string $ends_at;
    public bool $is_active;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        int $user_id,
        ?int $ticket_id,
        string $reason,
        ?string $admin_comment,
        string $starts_at,
        ?string $ends_at = null,
        bool $is_active = true,
        ?int $id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->user_id = $user_id;
        $this->ticket_id = $ticket_id;
        $this->reason = $reason;
        $this->admin_comment = $admin_comment;
        $this->starts_at = $starts_at;
        $this->ends_at = $ends_at;
        $this->is_active = $is_active;
        $this->id = $id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    private static function fromRow(array $row): UserSuspension
    {
        return new UserSuspension(
            (int) $row["user_id"],
            isset($row["ticket_id"]) ? (int) $row["ticket_id"] : null,
            $row["reason"],
            $row["admin_comment"],
            $row["starts_at"],
            $row["ends_at"],
            (bool) $row["is_active"],
            (int) $row["id"],
            $row["created_at"] ?? null,
            $row["updated_at"] ?? null
        );
    }

    public static function getById(int $id): ?UserSuspension
    {
        $data = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);
        return count($data) === 1 ? self::fromRow($data[0]) : null;
    }

    public static function getActiveByUserId(int $userId): ?UserSuspension
    {
        self::expireObsoleteSuspensions($userId);

        $sql = "SELECT * FROM user_suspensions
                WHERE user_id = :user_id
                  AND is_active = 1
                  AND starts_at <= NOW()
                  AND (ends_at IS NULL OR ends_at > NOW())
                ORDER BY created_at DESC
                LIMIT 1";
        $row = Connection::customQuery(ORION_DB, $sql, ["user_id" => $userId])->fetch(PDO::FETCH_ASSOC);

        return $row ? self::fromRow($row) : null;
    }

    public static function expireObsoleteSuspensions(?int $userId = null): void
    {
        $sql = "UPDATE user_suspensions
                SET is_active = 0
                WHERE is_active = 1
                  AND ends_at IS NOT NULL
                  AND ends_at <= NOW()";

        $params = [];
        if ($userId !== null) {
            $sql .= " AND user_id = :user_id";
            $params["user_id"] = $userId;
        }

        Connection::customQuery(ORION_DB, $sql, $params);
    }

    public function isIndefinite(): bool
    {
        return $this->ends_at === null;
    }

    public function save(): bool
    {
        $data = [
            "user_id" => $this->user_id,
            "ticket_id" => $this->ticket_id,
            "reason" => $this->reason,
            "admin_comment" => $this->admin_comment,
            "starts_at" => $this->starts_at,
            "ends_at" => $this->ends_at,
            "is_active" => (int) $this->is_active,
        ];

        if (!isset($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = (int) ORION_DB->lastInsertId();
            return (bool) $result;
        }

        return (bool) Connection::doUpdate(ORION_DB, self::$table, $data, ["id" => $this->id]);
    }
}
