<?php

require_once './models/User.php';
require_once './models/Game.php';

require_once './models/PostTypes/GalleryEntry.php';
require_once './models/PostTypes/Guide.php';

class Post {
    public static string $table = 'posts';

    public ?int $id;
    public string $title;
    public string $body;
    public string $created_at;
    public ?string $last_updated_at;
    public bool $is_public;
    public EPOST_TYPE $type;
    public ?int $game_id;
    public int $author_id;

    public function __construct(
        string $title,
        string $body,
        string $created_at,
        ?string $last_updated_at,
        bool $is_public,
        EPOST_TYPE|int $type,
        ?int $game_id,
        int $author_id,
        ?int $id = null
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->created_at = $created_at;
        $this->last_updated_at = $last_updated_at;
        $this->is_public = $is_public;
        $this->type = is_numeric($type) ? EPOST_TYPE::from($type) : $type;
        $this->game_id = $game_id;
        $this->author_id = $author_id;
        $this->id = $id;
    }

    public function save(): bool {
        $data = [
            'title' => $this->title,
            'body' => $this->body,
            'created_at' => $this->created_at,
            'last_updated_at' => $this->last_updated_at,
            'is_public' => $this->is_public,
            'type' => $this->type->value,
            'game_id' => $this->game_id,
            'author_id' => $this->author_id
        ];

        if (!isset($this->id) || !self::getById($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool)$result;
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['id' => $this->id]);
        }
    }

    public static function getById(int $id): ?Post {
        $post = Connection::doSelect(ORION_DB, self::$table, ['id' => $id]);
        if (count($post) === 1) {
            return new Post(
                $post[0]['title'],
                $post[0]['body'],
                $post[0]['created_at'],
                $post[0]['last_updated_at'],
                (bool)$post[0]['is_public'],
                $post[0]['type'],
                $post[0]['game_id'],
                $post[0]['author_id'],
                $post[0]['id']
            );
        }
        return null;
    }

    public function getAuthor(): User {
        return User::getById($this->author_id);
    }

    public function getPostInfo() : null|GalleryEntry|Guide {
        if(is_null($this->id)) return null;
        switch ($this->type) {
            case EPOST_TYPE::GALLERY:
                return GalleryEntry::getByPostId($this->id);
                break;

            case EPOST_TYPE::GUIDE:
                return Guide::getByPostId($this->id);
                break;
            
            default:
                return null;
                break;
        }
    }

    public function delete(): ?bool {
        if (!isset($this->id)) return null;
        return (bool)Connection::doDelete(ORION_DB, self::$table, ['id' => $this->id]);
    }
}
