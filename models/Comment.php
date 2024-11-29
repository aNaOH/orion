<?php 

require_once "models/User.php";
require_once "models/Post.php";

class Comment {
    public static string $table = 'comments';

    public ?int $id;
    public int $author_id;
    public int $post_id;
    public string $body;
    public ?string $date;

    public static function getById(int $id){
        $comment = Connection::doSelect(ORION_DB, self::$table, [
            "id" => $id
        ]);

        if (count($comment) === 1) {
            return new Comment(
                $comment[0]['author_id'],
                $comment[0]['post_id'],
                $comment[0]['body'],
                $comment[0]['id'],
                $comment[0]['date'],
            );
        }
        return null;
    }

    public static function getFromPost(Post|int $post){
        $postId = $post instanceof Post ? $post->id : $post;
        $comments = Connection::doSelect(ORION_DB, self::$table, [
            "post_id" => $postId
        ]);

        $result = [];

        foreach ($comments as $comment) {
            $result[] = new Comment(
                $comment['author_id'],
                $comment['post_id'],
                $comment['body'],
                $comment['id'],
                $comment['date'],
            );
        }

        return $result;
    }

    public static function getAllByUser(User|int $user){
        $userId = $user instanceof User ? $user->id : $user;
        $comments = Connection::doSelect(ORION_DB, self::$table, [
            "author_id" => $userId
        ]);

        $result = [];

        foreach ($comments as $comment) {
            $result[] = new Comment(
                $comment['author_id'],
                $comment['post_id'],
                $comment['body'],
                $comment['id'],
                $comment['date'],
            );
        }

        return $result;
    }

    public function __construct(int $author_id, int $post_id, string $body, int $id = null, string $date = null) {
        $this->id = $id;
        $this->author_id = $author_id;
        $this->post_id = $post_id;
        $this->body = $body;
        $this->date = $date;
    }

    public function getAuthor() {
        return User::getById($this->author_id);
    }

    public function getParentPost() {
        return Post::getById($this->post_id);
    }

    public function save(): bool {
        $data = [
            'author_id' => $this->author_id,
            'post_id' => $this->post_id,
            'body' => $this->body,
        ];

        if (!isset($this->id) || !self::getById($this->id)) {
            $result = Connection::doInsert(ORION_DB, self::$table, $data);
            $this->id = ORION_DB->lastInsertId();
            return (bool)$result;
        } else {
            return (bool)Connection::doUpdate(ORION_DB, self::$table, $data, ['id' => $this->id]);
        }
    }
}