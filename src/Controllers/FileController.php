<?php

namespace App\Controllers;

class FileController
{
    public function upload(): void
    {
        if (!isset($_FILES['file'])) {
            echo json_encode(["error" => "No file"]);
            return;
        }

        $file = $_FILES['file'];

        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = $file['name'];

        // ⚠️ aucune vérification → vulnérable
        move_uploaded_file($file['tmp_name'], $uploadDir . $fileName);

        echo json_encode(["message" => "File uploaded"]);
    }

    public function download(): void
    {
        $file = $_GET['file'] ?? '';

        $path = __DIR__ . '/../../uploads/' . $file;

        // ⚠️ path traversal possible
        if (file_exists($path)) {
            readfile($path);
        } else {
            echo json_encode(["error" => "File not found"]);
        }
    }
}