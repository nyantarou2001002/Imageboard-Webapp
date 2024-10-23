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

usort($replies, function ($a, $b) {
    return strtotime($b->getCreatedAt()) - strtotime($a->getCreatedAt());
});

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($thread->getSubject()) ?> - スレッド詳細</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6">
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h1 class="text-2xl font-bold mb-2">件名: <?= htmlspecialchars($thread->getSubject()) ?></h1>
            <p class="text-gray-700 mb-4">コメント: <?= htmlspecialchars($thread->getContent()) ?></p>

            <!-- スレッドの画像表示 -->
            <?php if (!empty($thread->getImagePath()) && file_exists(__DIR__ . '/../public/uploads/' . basename($thread->getImagePath()))): ?>
                <img src="uploads/<?= htmlspecialchars(basename($thread->getImagePath())) ?>" alt="スレッド画像" class="w-full max-w-md mx-auto rounded mb-4">
            <?php else: ?>
                <p class="text-gray-500">画像がありません。</p>
            <?php endif; ?>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">返信</h2>
            <div id="replies" class="space-y-4">
                <?php foreach ($replies as $reply): ?>
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <p class="text-gray-800 mb-2">匿名: <?= htmlspecialchars($reply->getContent()) ?></p>
                        <!-- 返信の画像表示 -->
                        <?php if (!empty($reply->getImagePath()) && file_exists(__DIR__ . '/../public/uploads/' . basename($reply->getImagePath()))): ?>
                            <img src="uploads/<?= htmlspecialchars(basename($reply->getImagePath())) ?>" alt="返信画像" class="w-full max-w-xs mx-auto rounded">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md mt-6">
            <form action="submit-reply.php" method="post" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="reply_to_id" value="<?= $thread->getId() ?>">
                <div>
                    <textarea name="content" placeholder="返信内容" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4"></textarea>
                </div>
                <div>
                    <input type="file" name="image" class="w-full text-gray-700 border border-gray-300 rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">返信する</button>
                </div>
            </form>
        </div>

        <div class="mt-6 text-center">
            <a href="index.php" class="inline-block bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">退出</a>
        </div>
    </div>
</body>

</html>