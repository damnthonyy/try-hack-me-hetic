<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\HtmlResponse;
use App\Http\Session;

final class DashboardController
{
    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = dirname(__DIR__, 2) . '/public/uploads/';
    }

    public function show(): void
    {
        if (!Session::has('user_id')) {
            header('Location: /auth/login', true, 302);
            exit;
        }

        if (isset($_GET['delete'])) {
            $this->deleteFile((string)$_GET['delete']);
            header('Location: /dashboard', true, 302);
            exit;
        }

        $username = (string)(Session::get('user_email') ?? 'Utilisateur');
        $files = $this->listUploadedFiles();
        $uploadMessage = '';
        $uploadError = '';

        HtmlResponse::render('pages/dashboard', [
            'title' => 'Tableau de bord',
            'username' => $username,
            'files' => $files,
            'uploadMessage' => $uploadMessage,
            'uploadError' => $uploadError,
        ]);
    }

    public function handlePost(): void
    {
        if (!Session::has('user_id')) {
            header('Location: /auth/login', true, 302);
            exit;
        }

        $uploadMessage = '';
        $uploadError = '';

        if (isset($_FILES['file']) && is_array($_FILES['file'])) {
            $file = $_FILES['file'];
            $filename = basename((string)($file['name'] ?? ''));
            $tmpName = $file['tmp_name'] ?? '';

            if (!is_dir($this->uploadDir)) {
                mkdir($this->uploadDir, 0777, true);
            }

            if (is_string($tmpName) && $tmpName !== '' && is_uploaded_file($tmpName)) {
                $targetPath = $this->uploadDir . $filename;
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $uploadMessage = 'Fichier uploadé avec succès : ' . $filename;

                    if (pathinfo($filename, PATHINFO_EXTENSION) === 'php') {
                        $uploadMessage .= ' — Flag: CTF{Shell_Upload_Success}';
                    }
                } else {
                    $uploadError = "Erreur lors de l'upload";
                }
            } else {
                $uploadError = "Erreur lors de l'upload";
            }
        }

        $username = (string)(Session::get('user_email') ?? 'Utilisateur');
        $files = $this->listUploadedFiles();

        HtmlResponse::render('pages/dashboard', [
            'title' => 'Tableau de bord',
            'username' => $username,
            'files' => $files,
            'uploadMessage' => $uploadMessage,
            'uploadError' => $uploadError,
        ]);
    }

    /**
     * @return list<array{filename: string, upload_date: string}>
     */
    private function listUploadedFiles(): array
    {
        if (!is_dir($this->uploadDir)) {
            return [];
        }

        $files = [];
        $existingFiles = scandir($this->uploadDir);
        if ($existingFiles === false) {
            return [];
        }

        foreach ($existingFiles as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $this->uploadDir . $file;
            $files[] = [
                'filename' => $file,
                'upload_date' => date('Y-m-d H:i:s', filemtime($path)),
            ];
        }

        return $files;
    }

    private function deleteFile(string $filename): void
    {
        $basename = basename($filename);
        $filepath = $this->uploadDir . $basename;
        if (is_file($filepath)) {
            unlink($filepath);
        }
    }
}
