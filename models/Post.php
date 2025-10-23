<?php

require_once "./models/User.php";
require_once "./models/Game.php";
require_once "./models/Comment.php";

require_once "./models/PostTypes/GalleryEntry.php";
require_once "./models/PostTypes/Guide.php";
require_once "./models/PostTypes/GameNews.php";

class Post
{
    public static string $table = "posts";

    public ?int $id;
    public string $title;
    public string $body;
    public DateTime $created_at;
    public ?DateTime $last_updated_at;
    public bool $is_public;
    public EPOST_TYPE $type;
    public ?int $game_id;
    public int $author_id;

    public function __construct(
        string $title,
        string $body,
        bool $is_public,
        EPOST_TYPE|int $type,
        ?int $game_id,
        int $author_id,
        ?int $id = null,
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->is_public = $is_public;
        $this->type = is_numeric($type) ? EPOST_TYPE::from($type) : $type;
        $this->game_id = $game_id;
        $this->author_id = $author_id;
        $this->id = $id;
    }

    public function save(): bool
    {
        $datetime = new DateTime();
        $data = [
            "title" => $this->title,
            "body" => $this->body,
            "created_at" =>
                $this->created_at ?? $datetime->format("Y-m-d H:i:s"),
            "last_updated_at" =>
                $this->last_updated_at ?? $datetime->format("Y-m-d H:i:s"),
            "is_public" => $this->is_public,
            "type" => $this->type->value,
            "game_id" => $this->game_id,
            "author_id" => $this->author_id,
        ];

        if (!isset($this->id) || !self::getById($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool) $result;
        } else {
            return (bool) Connection::doUpdate(ORION_DB, self::$table, $data, [
                "id" => $this->id,
            ]);
        }
    }

    public static function getById(int $id): ?Post
    {
        $post = Connection::doSelect(ORION_DB, self::$table, ["id" => $id]);
        if (count($post) === 1) {
            return new Post(
                $post[0]["title"],
                $post[0]["body"],
                (bool) $post[0]["is_public"],
                $post[0]["type"],
                $post[0]["game_id"],
                $post[0]["author_id"],
                $post[0]["id"],
            );
        }
        return null;
    }

    public static function getAllByType(EPOST_TYPE $type)
    {
        $postSelect = Connection::doSelect(ORION_DB, self::$table, [
            "type" => $type->value,
        ]);

        $posts = [];

        foreach ($postSelect as $post) {
            $postObj = new Post(
                $post["title"],
                $post["body"],
                (bool) $post["is_public"],
                $post["type"],
                $post["game_id"],
                $post["author_id"],
                $post["id"],
            );

            $postObj->created_at = new DateTime($post["created_at"]);
            $postObj->last_updated_at = new DateTime($post["last_updated_at"]);

            $posts[] = $postObj;
        }

        return $posts;
    }

    public static function getAllByTypeAndGame(EPOST_TYPE $type, int $game_id)
    {
        $postSelect = Connection::doSelect(ORION_DB, self::$table, [
            "type" => $type->value,
            "game_id" => $game_id,
        ]);

        $posts = [];

        foreach ($postSelect as $post) {
            $postObj = new Post(
                $post["title"],
                $post["body"],
                (bool) $post["is_public"],
                $post["type"],
                $post["game_id"],
                $post["author_id"],
                $post["id"],
            );

            $postObj->created_at = new DateTime($post["created_at"]);
            $postObj->last_updated_at = new DateTime($post["last_updated_at"]);

            $posts[] = $postObj;
        }

        return $posts;
    }

    public function getAuthor(): User
    {
        return User::getById($this->author_id);
    }

    public function getPostInfo(): null|GalleryEntry|Guide|GameNews
    {
        if (is_null($this->id)) {
            return null;
        }
        switch ($this->type) {
            case EPOST_TYPE::GALLERY:
                return GalleryEntry::getByPostId($this->id);
                break;

            case EPOST_TYPE::GUIDE:
                return Guide::getByPostId($this->id);
                break;

            case EPOST_TYPE::GAME_NEWS:
                return GameNews::getByPostId($this->id);
                break;

            default:
                return null;
                break;
        }
    }

    public function addComment(int $authorId, string $body): bool
    {
        if (is_null($this->id)) {
            return null;
        }
        $comment = new Comment($authorId, $this->id, $body);

        return $comment->save();
    }

    public function getComments(): array
    {
        if (is_null($this->id)) {
            return null;
        }
        $comments = Comment::getFromPost($this);

        return $comments;
    }

    public function delete(): ?bool
    {
        if (!isset($this->id)) {
            return null;
        }
        return (bool) Connection::doDelete(ORION_DB, self::$table, [
            "id" => $this->id,
        ]);
    }
}
