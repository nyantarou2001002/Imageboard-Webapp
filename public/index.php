<?php

// エラーメッセージを表示する設定
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Composerのオートローダーを読み込む
require_once __DIR__ . '/../vendor/autoload.php';

use Itokotaro\ImageWebapp\MySQLWrapper;
use Itokotaro\ImageWebapp\Database\DataAccess\PostDAOImpl;

// データベース接続の設定
$dbWrapper = new MySQLWrapper(
    '127.0.0.1',
    '3307',
    'imageboard',
    'root',
    'password'
);
$pdo = $dbWrapper->getConnection();

// DAOの初期化
$postDAO = new PostDAOImpl($pdo);

// スレッド一覧を取得し、作成日時でソート
$threads = $postDAO->getAllThreads();
usort($threads, function ($a, $b) {
    return strtotime($b->getCreatedAt()) - strtotime($a->getCreatedAt());
});

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イメージボード</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome の読み込み -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha384-lZN37fQIK6y1rZ0GTC2V5iA8xWAtR1xgiRG/9rHBlnT6jDh7U/9S93J6X+YCFdfA"
        crossorigin="anonymous">
</head>

<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4 text-center">イメージボード</h1>
        <hr class="mb-4">

        <!-- スレッド作成リンク -->
        <div class="mb-6 text-center">
            <a href="submit-thread.php"
                class="inline-block bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                <i class="fas fa-plus"></i> 新しいスレッドを作成
            </a>
        </div>

        <!-- スレッド一覧表示 -->
        <h2 class="text-xl font-semibold mb-2">スレッド一覧</h2>
        <hr class="mb-4">

        <div id="threads" class="space-y-6">
            <?php foreach ($threads as $thread): ?>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h3 class="text-lg font-semibold">
                        匿名<br>
                        件名：<a href="thread.php?id=<?= $thread->getId() ?>"
                            class="text-blue-500 hover:underline">
                            <?= htmlspecialchars($thread->getSubject() ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </h3>
                    <p class="mt-2 text-gray-700">コメント : <?= htmlspecialchars($thread->getContent() ?? '', ENT_QUOTES, 'UTF-8') ?></p>

                    <!-- スレッドの画像表示 -->
                    <?php
                    $imagePath = $thread->getImagePath();
                    if (!empty($imagePath)) {
                        $imageBaseName = basename($imagePath);
                        $thumbnailPath = 'thumbnails/' . htmlspecialchars($imageBaseName, ENT_QUOTES, 'UTF-8');
                        $fullImagePath = 'uploads/' . htmlspecialchars($imageBaseName, ENT_QUOTES, 'UTF-8');
                        $fullImageServerPath = __DIR__ . '/uploads/' . $imageBaseName;

                        if (file_exists($fullImageServerPath)) {
                            echo '<a href="' . $fullImagePath . '" target="_blank">';
                            echo '<img src="' . $thumbnailPath . '" alt="スレッド画像のサムネイル" class="mt-4 w-32 h-auto rounded">';
                            echo '</a>';
                        } else {
                            echo '<p class="text-gray-500 mt-2">画像がありません。</p>';
                        }
                    } else {
                        echo '<p class="text-gray-500 mt-2">画像がありません。</p>';
                    }
                    ?>

                    <div class="replies mt-4">
                        <h4 class="text-md font-semibold">最新の返信:</h4>
                        <div class="space-y-2 mt-2">
                            <?php
                            $latestReplies = $postDAO->getLatestReplies($thread->getId(), 5);
                            foreach ($latestReplies as $reply):
                            ?>
                                <div class="bg-gray-50 rounded-lg p-3 shadow-sm">
                                    <p class="text-gray-800">匿名 : <?= htmlspecialchars($reply->getContent() ?? '', ENT_QUOTES, 'UTF-8') ?></p>

                                    <!-- 返信の画像表示 -->
                                    <?php
                                    $replyImagePath = $reply->getImagePath();
                                    if (!empty($replyImagePath)) {
                                        $replyImageBaseName = basename($replyImagePath);
                                        $replyThumbnailPath = 'thumbnails/' . htmlspecialchars($replyImageBaseName, ENT_QUOTES, 'UTF-8');
                                        $replyFullImagePath = 'uploads/' . htmlspecialchars($replyImageBaseName, ENT_QUOTES, 'UTF-8');
                                        $replyFullImageServerPath = __DIR__ . '/uploads/' . $replyImageBaseName;

                                        if (file_exists($replyFullImageServerPath)) {
                                            echo '<a href="' . $replyFullImagePath . '" target="_blank">';
                                            echo '<img src="' . $replyThumbnailPath . '" alt="返信画像のサムネイル" class="mt-2 w-24 h-auto rounded">';
                                            echo '</a>';
                                        }
                                    }
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>