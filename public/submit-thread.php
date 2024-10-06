<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Composerのオートローダーを読み込む
require_once __DIR__ . '/../vendor/autoload.php';

use Itokotaro\ImageWebapp\MySQLWrapper;
use Itokotaro\ImageWebapp\Models\Post;
use Itokotaro\ImageWebapp\Database\DataAccess\PostDAOImpl;

// データベース接続設定
try {
    $dbWrapper = new MySQLWrapper('127.0.0.1', '3307', 'imageboard', 'root', 'password');
    $pdo = $dbWrapper->getConnection();
    $postDAO = new PostDAOImpl($pdo);
} catch (Exception $e) {
    die("データベース接続エラー: " . $e->getMessage());
}

// POSTリクエストが送信された場合に処理を行う
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームデータの取得
    $subject = $_POST['subject'] ?? null;
    $content = $_POST['content'] ?? '';
    $imagePath = null;

    // バリデーション: 件名と投稿内容のどちらも空の場合はエラーを表示
    if (empty($subject) && empty($content)) {
        die("エラー: 件名または投稿内容を入力してください。");
    }

    // 画像がアップロードされているか確認し、なければ空文字列をセット
    if (!empty($_FILES['image']['tmp_name'])) {
        $uploadDir = __DIR__ . '/../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $thumbnailDir = __DIR__ . '/../public/thumbnails/';
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0777, true);
        }

        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $uniqueFilename = hash('sha256', uniqid() . time()) . '.' . $fileExtension;
        $imagePath = $uploadDir . $uniqueFilename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $thumbnailPath = $thumbnailDir . $uniqueFilename;
            exec("magick convert $imagePath -resize 150x150! $thumbnailPath", $output, $return_var);
            if ($return_var !== 0) {
                die("サムネイルの作成に失敗しました: " . implode("\n", $output));
            }
        } else {
            die("エラー: 画像のアップロードに失敗しました。");
        }
    } else {
        $imagePath = '';  // 画像がない場合に空文字列をセット
    }

    try {
        $post = new Post(null, null, $subject, $content, basename($imagePath), date('Y-m-d H:i:s'));
        $postDAO->create($post);

        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        die("エラー: スレッド作成中に問題が発生しました: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新しいスレッドを作成</title>
</head>

<body>
    <h1>新しいスレッドを作成</h1>

    <form action="submit-thread.php" method="post" enctype="multipart/form-data">
        <label for="subject">件名 (任意):</label>
        <input type="text" name="subject" id="subject">
        <br><br>

        <label for="content">投稿内容:</label>
        <textarea name="content" id="content" placeholder="投稿内容を入力してください"></textarea>
        <br><br>

        <label for="image">画像 (任意):</label>
        <input type="file" name="image" id="image">
        <br><br>

        <button type="submit">スレッド作成</button>
    </form>

    <a href="index.php">戻る</a>
</body>

</html>