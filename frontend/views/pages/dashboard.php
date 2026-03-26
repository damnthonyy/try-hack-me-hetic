<?php

declare(strict_types=1);

$pageCss = 'pages/dashboard';

session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
}

$username = $_SESSION['username'];
$uploadMessage = '';
$uploadError = '';

$uploadDir = __DIR__ . '/../../../public/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$files = [];
$existingFiles = scandir($uploadDir);
foreach ($existingFiles as $file) {
    if ($file !== '.' && $file !== '..') {
        $files[] = [
            'filename' => $file,
            'upload_date' => date('Y-m-d H:i:s', filemtime($uploadDir . $file)),
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = basename((string) $file['name']);
    $tmpName = $file['tmp_name'];
    $targetPath = $uploadDir . $filename;

    if (is_string($tmpName) && move_uploaded_file($tmpName, $targetPath)) {
        $uploadMessage = 'Fichier uploadé avec succès : ' . $filename;

        if (pathinfo($filename, PATHINFO_EXTENSION) === 'php') {
            $uploadMessage .= ' — Flag: CTF{Shell_Upload_Success}';
        }

        $files = [];
        $existingFiles = scandir($uploadDir);
        foreach ($existingFiles as $f) {
            if ($f !== '.' && $f !== '..') {
                $files[] = [
                    'filename' => $f,
                    'upload_date' => date('Y-m-d H:i:s', filemtime($uploadDir . $f)),
                ];
            }
        }
    } else {
        $uploadError = "Erreur lors de l'upload";
    }
}

if (isset($_GET['delete'])) {
    $filename = (string) $_GET['delete'];
    $filepath = $uploadDir . basename($filename);
    if (file_exists($filepath)) {
        unlink($filepath);
        header('Location: /dashboard');
        exit;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /');
    exit;
}

$title = 'Dashboard';
ob_start();
?>

<article class="page page--dashboard">
    <header class="dashboard__masthead">
        <h1 class="title">Dashboard</h1>
        <p class="dashboard__lead">Bienvenue, <?= htmlspecialchars((string) $username, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="dashboard__masthead-actions">
            <a href="?logout=1" class="dashboard__logout">Déconnexion</a>
        </div>
    </header>

    <?php if ($uploadMessage !== ''): ?>
        <div class="dashboard__alert dashboard__alert--success">
            <?= htmlspecialchars($uploadMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($uploadError !== ''): ?>
        <div class="dashboard__alert dashboard__alert--error">
            <?= htmlspecialchars($uploadError, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <section class="dashboard__card" aria-labelledby="upload-heading">
        <h2 id="upload-heading" class="dashboard__card-title">Upload de fichiers</h2>
        <form class="dashboard__upload-form" method="post" action="" enctype="multipart/form-data">
            <div>
                <label class="form-label" for="upload-file">Fichier</label>
                <input class="dashboard__file-input" type="file" name="file" id="upload-file" required>
            </div>
            <button type="submit" class="btn btn-primary">Uploader</button>
        </form>
    </section>

    <section class="dashboard__card" aria-labelledby="files-heading">
        <h2 id="files-heading" class="dashboard__card-title">Mes fichiers</h2>
        <?php if ($files === []): ?>
            <p class="dashboard__empty">Aucun fichier trouvé.</p>
        <?php else: ?>
            <div class="dashboard__table-wrap">
                <table class="dashboard__table">
                    <thead>
                        <tr>
                            <th scope="col">Nom du fichier</th>
                            <th scope="col">Date d’upload</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $file): ?>
                            <tr>
                                <td><?= htmlspecialchars($file['filename'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($file['upload_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <div class="dashboard__actions">
                                        <a class="dashboard__btn dashboard__btn--primary" href="/uploads/<?= rawurlencode($file['filename']) ?>">Télécharger</a>
                                        <a class="dashboard__btn dashboard__btn--danger" href="?delete=<?= rawurlencode($file['filename']) ?>" onclick="return confirm('Supprimer ce fichier ?')">Supprimer</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</article>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
