<?php

declare(strict_types=1);

/** @var string $content */
/** @var string $title */
/** @var string|null $pageCss */

$docTitle = isset($title) ? (string) $title : 'App';

/** @var list<string> $layoutStyles */
$layoutStyles = $layoutStyles ?? [];

$normalizeStylesheet = static function (string $path): string {
    $path = trim($path);
    if ($path === '') {
        return $path;
    }
    if (str_starts_with($path, '/')) {
        return $path;
    }

    return '/assets/css/' . ltrim($path, '/');
};

$stylesheets = ['/assets/css/app.css'];

foreach ($layoutStyles as $sheet) {
    $href = $normalizeStylesheet($sheet);
    if ($href !== '') {
        $stylesheets[] = $href;
    }
}

if (isset($pageCss) && (string) $pageCss !== '') {
    $pc = (string) $pageCss;
    $path = str_ends_with($pc, '.css') ? $pc : $pc . '.css';
    $stylesheets[] = $normalizeStylesheet($path);
}

$stylesheets = array_values(array_unique($stylesheets));
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
