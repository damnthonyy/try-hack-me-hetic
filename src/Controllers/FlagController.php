<?php

namespace App\Controllers;

class FlagController
{
    public function getFlag(): void
    {
        $token = $_GET['token'] ?? '';

        if ($token === "admin123") {
            echo json_encode(["flag" => "FLAG{SQL_INJECTION_SUCCESS}"]);
        } else {
            echo json_encode(["error" => "Access denied"]);
        }
    }
}