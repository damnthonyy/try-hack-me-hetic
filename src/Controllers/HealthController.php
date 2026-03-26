<?php

namespace App\Controllers;

class HealthController
{
    public function get(): void
    {
        echo json_encode(["status" => "ok"]);
    }
}

