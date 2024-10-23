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
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center">新しいスレッドを作成</h1>

            <form action="submit-thread.php" method="post" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">件名:</label>
                    <input type="text" name="subject" id="subject" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">投稿内容:</label>
                    <textarea name="content" id="content" rows="4" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="投稿内容を入力してください" required></textarea>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">画像:</label>
                    <input type="file" name="image" id="image" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">スレッド作成</button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <a href="index.php" class="inline-block text-gray-500 hover:text-gray-700">戻る</a>
            </div>
        </div>
    </div>
</body>

</html>