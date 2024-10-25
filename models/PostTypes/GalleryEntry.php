<?php

require './models/Post.php';

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

    public function delete(): ?bool {
        return (bool)Connection::doDelete(ORION_DB, self::$table, ['post_id' => $this->post_id]);
    }
}
