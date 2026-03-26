<?php

declare(strict_types=1);

/** @var string $content */
/** @var string $title */

$docTitle = isset($title) ? (string) $title : 'App';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($docTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <main class="wrap">
        <?= $content ?>
    </main>
    <script src="/assets/js/app.js" defer></script>
</body>
</html>
