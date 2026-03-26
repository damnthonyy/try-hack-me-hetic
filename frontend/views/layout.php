<?php

declare(strict_types=1);

/** @var string $content */
/** @var string $title */
/** @var string|null $pageCss */

$docTitle = isset($title) ? (string) $title : 'App';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($docTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <?php if (isset($pageCss)): ?>
    <link rel="stylesheet" href="/assets/css/<?= htmlspecialchars($pageCss, ENT_QUOTES, 'UTF-8') ?>.css">
    <?php endif; ?>
</head>
<body>
    <main class="wrap">
        <?= $content ?>
    </main>
    <script src="/assets/js/app.js" defer></script>
</body>
</html>