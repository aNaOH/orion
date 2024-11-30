<?php

require_once './models/Post.php';

class GalleryEntry {
    public static string $table = 'gallery_entries';

    public int $post_id;
    public string $media;

    public function __construct(int $post_id, string $media) {
        $this->post_id = $post_id;
        $this->media = $media;
    }

    public function save(): bool {
        $data = [
            'post_id' => $this->post_id,
            'media' => $this->media
        ];

        if (!self::getByPostId($this->post_id)) {
            return (bool)Connection::doInsert(ORION_DB, self::$table, $data);
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['post_id' => $this->post_id]);
        }
    }

    public static function getByPostId(int $post_id): ?GalleryEntry {
        $entry = Connection::doSelect(ORION_DB, self::$table, ['post_id' => $post_id]);
        if (count($entry) === 1) {
            return new GalleryEntry($entry[0]['post_id'], $entry[0]['media']);
        }
        return null;
    }

    public function getParentPost(): Post {
        return Post::getById($this->post_id);
    }

    public function getValue(): int {
        $sql = "SELECT SUM(modifier) FROM votes WHERE post_id = ?";
        $valueDB = Connection::customQuery(ORION_DB, $sql, [$this->post_id])->fetchAll();
        return intval($valueDB[0][0] ?? '0');
    }

    public function hasUserVoted($voter): bool {
        $valueDB = Connection::doSelect(ORION_DB, "votes", ['post_id' => $this->post_id, 'user_id' => $voter]);
        return count($valueDB) == 1;
    }

    public function getUserValue($voter): int {
        $value = 0;
        if($this->hasUserVoted($voter)){
            $valueDB = Connection::doSelect(ORION_DB, "votes", ['post_id' => $this->post_id, 'user_id' => $voter]);
            $value = intval($valueDB[0]["modifier"] ?? '0');
        }
        return $value;
    }

    public function addVote($voter, $value) {
        $this->getValue();
        if(!$this->hasUserVoted($voter)){
            Connection::doInsert(ORION_DB, "votes", ['post_id' => $this->post_id, 'user_id' => $voter, 'modifier' => $value]);
        } else {
            Connection::doUpdate(ORION_DB, "votes", ['modifier' => $value], ['post_id' => $this->post_id, 'user_id' => $voter]);
        }
    }

    public function delete(): ?bool {
        return (bool)Connection::doDelete(ORION_DB, self::$table, ['post_id' => $this->post_id]);
    }
}
