<?php

require_once "./helpers/enums.php";

class Ticket
{
    public static string $table = "tickets";

    public ?int $id;
    public int $user_id;
    public string $type;
    public int $status; // 0: Pending, 1: Accepted, 2: Rejected
    public ?string $admin_comment;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct(
        int $user_id,
        string $type,
        int $status = 0,
        ?string $admin_comment = null,
        ?int $id = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->user_id = $user_id;
        $this->type = $type;
        $this->status = $status;
        $this->admin_comment = $admin_comment;
        $this->id = $id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public static function getById(int $id): ?Ticket
    {
        $data = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);
        if (count($data) === 1) {
            return new Ticket(
                $data[0]["user_id"],
                $data[0]["type"],
                $data[0]["status"],
                $data[0]["admin_comment"],
                $data[0]["id"],
                $data[0]["created_at"],
                $data[0]["updated_at"]
            );
        }
        return null;
    }

    public static function getAll(): array
    {
        $sql = "SELECT * FROM tickets ORDER BY created_at DESC";
        $stmt = Connection::customQuery(ORION_DB, $sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tickets = [];
        foreach ($data as $row) {
            $tickets[] = new Ticket(
                $row["user_id"],
                $row["type"],
                $row["status"],
                $row["admin_comment"],
                $row["id"],
                $row["created_at"],
                $row["updated_at"]
            );
        }
        return $tickets;
    }

    public function save(): bool
    {
        $data = [
            "user_id" => $this->user_id,
            "type" => $this->type,
            "status" => $this->status,
            "admin_comment" => $this->admin_comment
        ];

        if (!isset($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool) $result;
        } else {
            return (bool) Connection::doUpdate(ORION_DB, self::$table, $data, ["id" => $this->id]);
        }
    }

    public function getReporter(): ?User
    {
        return User::getById($this->user_id);
    }
}
