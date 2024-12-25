<?php

require_once './models/Post.php';
require_once './models/GameNewsCategory.php';

class GameNews {
    public static string $table = 'game_news';

    public int $post_id;
    public int $category_id;

    public function __construct(int $post_id, int $category_id) {
        $this->post_id = $post_id;
        $this->category_id = $category_id;
    }

    public function save(): bool {
        $data = [
            'post_id' => $this->post_id,
            'category_id' => $this->category_id
        ];

        if (!self::getByPostId($this->post_id)) {
            return (bool)Connection::doInsert(ORION_DB, self::$table, $data);
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['post_id' => $this->post_id]);
        }
    }

    public static function getByPostId(int $post_id): ?GameNews {
        $gameNews = Connection::doSelect(ORION_DB, self::$table, ['post_id' => $post_id]);
        if (count($gameNews) === 1) {
            return new GameNews($gameNews[0]['post_id'], $gameNews[0]['category_id']);
        }
        return null;
    }

    public function getCategory() {
        return GameNewsCategory::getById($this->category_id);
    }

    public function getParentPost(): Post {
        return Post::getById($this->post_id);
    }

    public function delete(): ?bool {
        return (bool)Connection::doDelete(ORION_DB, self::$table, ['post_id' => $this->post_id]);
    }
}
