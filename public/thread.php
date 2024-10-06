<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Itokotaro\ImageWebapp\MySQLWrapper;

$dbWrapper = new MySQLWrapper('127.0.0.1', '3307', 'imageboard', 'root', 'password');
$pdo = $dbWrapper->getConnection();
$postDAO = new Itokotaro\ImageWebapp\Database\DataAccess\PostDAOImpl($pdo);

$threadId = $_GET['id'] ?? null;
if (!$threadId) {
    die("スレッドIDが指定されていません。");
}

$thread = $postDAO->getById($threadId);
if (!$thread) {
    die("スレッドが見つかりません。");
}

$replies = $postDAO->getReplies($thread, 0, 100);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($thread->getSubject()) ?> - スレッド詳細</title>
</head>

<body>
    <h1><?= htmlspecialchars($thread->getSubject()) ?></h1>
    <p><?= htmlspecialchars($thread->getContent()) ?></p>

    <?php if (!empty($thread->getImagePath()) && file_exists(__DIR__ . '/../public/uploads/' . basename($thread->getImagePath()))): ?>
        <img src="uploads/<?= htmlspecialchars(basename($thread->getImagePath())) ?>" alt="スレッド画像" style="max-width: 300px;">
    <?php else: ?>
        <p>画像がありません。</p>
    <?php endif; ?>

    <h2>返信</h2>
    <div id="replies">
        <?php foreach ($replies as $reply): ?>
            <div class="reply">
                <p><?= htmlspecialchars($reply->getContent()) ?></p>
                <?php if (!empty($reply->getImagePath()) && file_exists(__DIR__ . '/../public/uploads/' . basename($reply->getImagePath()))): ?>
                    <img src="uploads/<?= htmlspecialchars(basename($reply->getImagePath())) ?>" alt="返信画像" style="max-width: 150px;">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form action="submit-reply.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="reply_to_id" value="<?= $thread->getId() ?>">
        <textarea name="content" placeholder="返信内容"></textarea>
        <input type="file" name="image">
        <button type="submit">返信する</button>
    </form>

    <a href="index.php">
        <button>退出</button>
    </a>
</body>

</html>