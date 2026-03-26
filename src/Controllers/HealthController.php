<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\JsonResponse;

final class HealthController
{
    public function get(): void
    {
        JsonResponse::ok(['status' => 'ok']);
    }
}

