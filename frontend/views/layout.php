<?php

declare(strict_types=1);

/** @var string $content */
/** @var string $title */

$docTitle = isset($title) ? (string) $title : 'App';

/** @var list<string> $layoutStyles */
$layoutStyles = $layoutStyles ?? [];
$stylesheets = array_merge(
    ['/assets/css/app.css'],
    $layoutStyles
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($docTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <?php foreach ($stylesheets as $href): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
</head>
<body>
    <main class="wrap">
        <?= $content ?>
    </main>
    <script src="/assets/js/app.js" defer></script>
</body>
</html>
