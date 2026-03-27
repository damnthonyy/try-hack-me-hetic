<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Session;
use App\Infrastructure\Database;

final class LoginController
{
    public function login(): void
    {
        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

        if ($email === '' || $password === '') {
            $this->redirect('/auth/login?error=required');
        }

        try {
            $pdo = Database::createPdoFromEnv();

            $stmt = $pdo->prepare(
                'SELECT id, email, username, password FROM users WHERE email = :email LIMIT 1'
            );
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $this->redirect('/auth/login?error=invalid');
            }

            Session::set('user_id', $user['id']);
            Session::set('user_email', $user['email']);
            Session::set('user_username', (string)$user['username']);

            $this->redirect('/dashboard');
        } catch (\Throwable) {
            $this->redirect('/auth/login?error=server');
        }
    }

    public function logout(): void
    {
        Session::destroy();

        $this->redirect('/');
    }

    public function register(): void
    {
        $username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
        $passwordConfirmation = isset($_POST['password_confirmation'])
            ? (string)$_POST['password_confirmation']
            : '';

        if ($username === '' || $email === '' || $password === '' || $passwordConfirmation === '') {
            $this->redirect('/auth/register?error=required');
        }

        if ($password !== $passwordConfirmation) {
            $this->redirect('/auth/register?error=mismatch');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect('/auth/register?error=email');
        }

        if (strlen($password) < 6) {
            $this->redirect('/auth/register?error=password');
        }

        $usernameLen = strlen($username);
        if ($usernameLen < 3 || $usernameLen > 64 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $this->redirect('/auth/register?error=invalid_username');
        }

        try {
            $pdo = Database::createPdoFromEnv();

            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);

            if ($stmt->fetch()) {
                $this->redirect('/auth/register?error=taken');
            }

            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
            $stmt->execute(['username' => $username]);

            if ($stmt->fetch()) {
                $this->redirect('/auth/register?error=username_taken');
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'INSERT INTO users (email, username, password) VALUES (:email, :username, :password)'
            );
            $stmt->execute([
                'email' => $email,
                'username' => $username,
                'password' => $hashedPassword,
            ]);

            $userId = (int)$pdo->lastInsertId();

            Session::set('user_id', $userId);
            Session::set('user_email', $email);
            Session::set('user_username', $username);

            $this->redirect('/dashboard');
        } catch (\Throwable) {
            $this->redirect('/auth/register?error=server');
        }
    }

    private function redirect(string $url): never
    {
        header('Location: ' . $url, true, 302);
        exit;
    }
}
