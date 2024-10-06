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
$dbWrapper = new MySQLWrapper('127.0.0.1', '3307', 'imageboard', 'root', 'password');
$pdo = $dbWrapper->getConnection();
$postDAO = new PostDAOImpl($pdo);

// フォームデータの取得
$replyToId = $_POST['reply_to_id'] ?? null;
$content = $_POST['content'] ?? '';
$imagePath = null;

// 入力バリデーション: 投稿内容が空の場合はエラーを表示
if (empty($content)) {
    die("エラー: 投稿内容を入力してください。");
}

// 画像がアップロードされているか確認し、なければ空文字列をセット
if (!empty($_FILES['image']['tmp_name'])) {
    // アップロード先のディレクトリを public/uploads に修正
    $uploadDir = __DIR__ . '/../public/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // 画像の保存パスを設定
    $imagePath = $uploadDir . hash('sha256', uniqid() . time()) . '.jpg';

    // 画像のアップロード処理
    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        // サムネイルを生成
        $thumbnailDir = __DIR__ . '/../public/thumbnails/';
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0777, true);
        }
        $thumbnailPath = $thumbnailDir . basename($imagePath);
        exec("magick convert $imagePath -resize 150x150! $thumbnailPath", $output, $return_var);
        if ($return_var !== 0) {
            die("サムネイルの作成に失敗しました: " . implode("\n", $output));
        }
    } else {
        die("画像のアップロードに失敗しました。");
    }
} else {
    $imagePath = '';  // 画像がない場合に空文字列をセット
}

// Postオブジェクトを作成し、保存
$post = new Post(null, $replyToId, null, $content, basename($imagePath), date('Y-m-d H:i:s'));
$postDAO->create($post);

// スレッドにリダイレクト
header("Location: thread.php?id=$replyToId");
exit();
