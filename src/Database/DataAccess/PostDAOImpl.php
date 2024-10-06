<?php

namespace Itokotaro\ImageWebapp\Database\DataAccess;

use Itokotaro\ImageWebapp\Database\DataAccess\Interfaces\PostDAO;
use Itokotaro\ImageWebapp\Models\Post;
use PDO;

class PostDAOImpl implements PostDAO
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // 新しい投稿を作成するメソッド
    public function create(Post $postData): bool
    {
        $sql = "INSERT INTO posts (reply_to_id, subject, content, image_path, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $postData->getReplyToId(),
            $postData->getSubject(),
            $postData->getContent(),
            $postData->getImagePath(),
        ]);
    }

    // 投稿IDで投稿を取得するメソッド
    public function getById(int $id): ?Post
    {
        $sql = "SELECT * FROM posts WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Post($row['id'], $row['reply_to_id'], $row['subject'], $row['content'], $row['image_path'], $row['created_at']);
        }
        return null;
    }

    // すべてのメインスレッドを取得するメソッド
    public function getAllThreads(int $offset, int $limit): array
    {
        $sql = "SELECT * FROM posts WHERE reply_to_id IS NULL LIMIT $offset, $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        // 連想配列としてデータを取得
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $posts = [];

        // 手動でPostオブジェクトを作成
        foreach ($rows as $row) {
            $posts[] = new Post(
                $row['id'],
                $row['reply_to_id'],
                $row['subject'],
                $row['content'],
                $row['image_path'],
                $row['created_at']
            );
        }

        return $posts;
    }

    // 特定の投稿に対するすべての返信を取得するメソッド
    public function getReplies(Post $postData, int $offset, int $limit): array
    {
        $sql = "SELECT * FROM posts WHERE reply_to_id = ? LIMIT $offset, $limit";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$postData->getId()]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $posts = [];

        foreach ($rows as $row) {
            $posts[] = new Post(
                $row['id'],
                $row['reply_to_id'],
                $row['subject'],
                $row['content'],
                $row['image_path'],
                $row['created_at']
            );
        }

        return $posts;
    }

    // 投稿を更新するメソッド
    public function update(Post $postData): bool
    {
        $sql = "UPDATE posts SET reply_to_id = ?, subject = ?, content = ?, image_path = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $postData->getReplyToId(),
            $postData->getSubject(),
            $postData->getContent(),
            $postData->getImagePath(),
            $postData->getId(),
        ]);
    }

    // 投稿を削除するメソッド
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM posts WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // 新規作成または既存投稿の更新を行うメソッド
    public function createOrUpdate(Post $postData): bool
    {
        if ($postData->getId() === null) {
            return $this->create($postData);
        }
        return $this->update($postData);
    }
}
