<?php

namespace App\Controllers;

use App\Infrastructure\Database;

class UserController
{
    public function getUser(): void
    {
        $db = Database::getConnection();

        $id = $_GET['id'] ?? '1';

        // ⚠️ volontairement vulnérable
        $query = "SELECT * FROM users WHERE id = $id";

        $result = $db->query($query);

        if (!$result) {
            $error = $db->errorInfo();
            echo json_encode(["sql_error" => $error]);
            return;
        }

        $user = $result->fetch(\PDO::FETCH_ASSOC);

        echo json_encode($user);
    }
}