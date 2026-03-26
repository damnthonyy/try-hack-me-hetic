<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\JsonResponse;
use App\Http\Session;
use App\Infrastructure\Database;

final class LoginController
{
    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['email']) || !isset($input['password'])) {
            JsonResponse::error('Email and password are required', 400);
            return;
        }

        $email = $input['email'];
        $password = $input['password'];

        try {
            $pdo = Database::createPdoFromEnv();
            
            $stmt = $pdo->prepare('SELECT id, email, password FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                JsonResponse::error('Invalid credentials', 401);
                return;
            }

            Session::set('user_id', $user['id']);
            Session::set('user_email', $user['email']);

            JsonResponse::ok([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email']
                ]
            ]);
        } catch (\Throwable $throwable) {
            JsonResponse::error('Database error: ' . $throwable->getMessage(), 500);
        }
    }

    public function logout(): void
    {
        Session::destroy();
        
        JsonResponse::ok(['message' => 'Logout successful']);
    }

    public function register(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['email']) || !isset($input['password'])) {
            JsonResponse::error('Email and password are required', 400);
            return;
        }

        $email = $input['email'];
        $password = $input['password'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            JsonResponse::error('Invalid email format', 400);
            return;
        }

        if (strlen($password) < 6) {
            JsonResponse::error('Password must be at least 6 characters', 400);
            return;
        }

        try {
            $pdo = Database::createPdoFromEnv();
            
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            
            if ($stmt->fetch()) {
                JsonResponse::error('Email already exists', 409);
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');
            $stmt->execute([
                'email' => $email,
                'password' => $hashedPassword
            ]);

            $userId = (int)$pdo->lastInsertId();

            Session::set('user_id', $userId);
            Session::set('user_email', $email);

            JsonResponse::ok([
                'message' => 'Registration successful',
                'user' => [
                    'id' => $userId,
                    'email' => $email
                ]
            ], 201);
        } catch (\Throwable $throwable) {
            JsonResponse::error('Database error: ' . $throwable->getMessage(), 500);
        }
    }
}
