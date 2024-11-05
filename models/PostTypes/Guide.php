<?php

require_once './models/Post.php';

class Guide {
    public static string $table = 'guides';

    public int $post_id;
    public int $type_id;

    public function __construct(int $post_id, int $type_id) {
        $this->post_id = $post_id;
        $this->type_id = $type_id;
    }

    public function save(): bool {
        $data = [
            'post_id' => $this->post_id,
            'type_id' => $this->type_id
        ];

        if (!self::getByPostId($this->post_id)) {
            return (bool)Connection::doInsert(ORION_DB, self::$table, $data);
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['post_id' => $this->post_id]);
        }
    }

    public static function getByPostId(int $post_id): ?Guide {
        $guide = Connection::doSelect(ORION_DB, self::$table, ['post_id' => $post_id]);
        if (count($guide) === 1) {
            return new Guide($guide[0]['post_id'], $guide[0]['type_id']);
        }
        return null;
    }

    public function getParentPost(): Post {
        return Post::getById($this->post_id);
    }

    public function delete(): ?bool {
        return (bool)Connection::doDelete(ORION_DB, self::$table, ['post_id' => $this->post_id]);
    }
}