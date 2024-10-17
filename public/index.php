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
$dbWrapper = new MySQLWrapper('127.0.0.1', '3307', 'imageboard', 'root', 'password');
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
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>イメージボード</h1>

    <!-- スレッド作成リンク -->
    <a href="submit-thread.php">新しいスレッドを作成</a>

    <!-- スレッド一覧表示 -->
    <h2>スレッド一覧</h2>
    <div id="threads">
        <?php foreach ($threads as $thread): ?>
            <div class="thread">
                <h3><a href="thread.php?id=<?= $thread->getId() ?>">
                        <?= htmlspecialchars($thread->getSubject() ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </a></h3>
                <p><?= htmlspecialchars($thread->getContent() ?? '', ENT_QUOTES, 'UTF-8') ?></p>

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
                        echo '<img src="' . $thumbnailPath . '" alt="スレッド画像のサムネイル" style="max-width: 150px;">';
                        echo '</a>';
                    } else {
                        echo '<p>画像がありません。</p>';
                    }
                } else {
                    echo '<p>画像がありません。</p>';
                }
                ?>

                <div class="replies">
                    <h4>最新の返信:</h4>
                    <?php
                    $latestReplies = $postDAO->getLatestReplies($thread->getId(), 5);
                    foreach ($latestReplies as $reply):
                    ?>
                        <div class="reply">
                            <p><?= htmlspecialchars($reply->getContent() ?? '', ENT_QUOTES, 'UTF-8') ?></p>

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
                                    echo '<img src="' . $replyThumbnailPath . '" alt="返信画像のサムネイル" style="max-width: 150px;">';
                                    echo '</a>';
                                }
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>