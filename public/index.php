<?php

// エラーメッセージを表示する設定
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Composerのオートローダーを読み込む
require_once __DIR__ . '/../vendor/autoload.php';

use Itokotaro\ImageWebapp\MySQLWrapper;

// データベース接続の設定
$dbWrapper = new MySQLWrapper('127.0.0.1', '3307', 'imageboard', 'root', 'password');
$pdo = $dbWrapper->getConnection();

// DAOの初期化
$postDAO = new Itokotaro\ImageWebapp\Database\DataAccess\PostDAOImpl($pdo);

// スレッド一覧を取得
$threads = $postDAO->getAllThreads(0, 20);

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
                <h3><a href="thread.php?id=<?= $thread->getId() ?>"><?= htmlspecialchars($thread->getSubject() ?? '', ENT_QUOTES, 'UTF-8') ?></a></h3>
                <p><?= htmlspecialchars($thread->getContent() ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>