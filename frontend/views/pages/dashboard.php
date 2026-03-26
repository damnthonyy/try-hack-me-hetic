<?php
// Définir le CSS spécifique pour cette page
$pageCss = 'dashboard';

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
    if ($file != '.' && $file != '..') {
        $files[] = [
            'filename' => $file,
            'upload_date' => date('Y-m-d H:i:s', filemtime($uploadDir . $file))
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = basename($file['name']);
    $tmpName = $file['tmp_name'];
    $targetPath = $uploadDir . $filename;
    
    if (move_uploaded_file($tmpName, $targetPath)) {
        $uploadMessage = "Fichier uploade avec succes: " . $filename;
        
        if (pathinfo($filename, PATHINFO_EXTENSION) == 'php') {
            $uploadMessage .= " - Flag: CTF{Shell_Upload_Success}";
        }
        
        $files = [];
        $existingFiles = scandir($uploadDir);
        foreach ($existingFiles as $file) {
            if ($file != '.' && $file != '..') {
                $files[] = [
                    'filename' => $file,
                    'upload_date' => date('Y-m-d H:i:s', filemtime($uploadDir . $file))
                ];
            }
        }
    } else {
        $uploadError = "Erreur lors de l'upload";
    }
}

if (isset($_GET['delete'])) {
    $filename = $_GET['delete'];
    $filepath = $uploadDir . $filename;
    if (file_exists($filepath)) {
        unlink($filepath);
        header("Location: /dashboard");
        exit;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /");
    exit;
}

$title = 'Dashboard';
ob_start();
?>

<div class="dashboard-container">
    <div class="header">
        <h1>Dashboard</h1>
        <p>Bienvenue, <?php echo htmlspecialchars($username); ?></p>
        <a href="?logout=1" class="btn-logout">Deconnexion</a>
    </div>

    <?php if ($uploadMessage): ?>
        <div class="alert alert-success">
            <?php echo $uploadMessage; ?>
        </div>
    <?php endif; ?>

    <?php if ($uploadError): ?>
        <div class="alert alert-error">
            <?php echo $uploadError; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Upload de fichiers</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <button type="submit">Uploader</button>
        </form>
    </div>

    <div class="card">
        <h2>Mes fichiers</h2>
        <?php if (empty($files)): ?>
            <p>Aucun fichier trouve.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom du fichier</th>
                        <th>Date d'upload</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file['filename']); ?></td>
                        <td><?php echo $file['upload_date']; ?></td>
                        <td>
                            <a href="/uploads/<?php echo $file['filename']; ?>">Telecharger</a>
                            <a href="?delete=<?php echo urlencode($file['filename']); ?>" onclick="return confirm('Supprimer ce fichier?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>