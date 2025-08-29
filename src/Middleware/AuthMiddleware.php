<?php

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Check if a user is logged in
     *
     * @return bool
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    /**
     * Check if the logged-in user is an admin
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        if (!self::isLoggedIn()) {
            return false;
        }

        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Require user to be logged in, redirect to login page if not
     *
     * @return void
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Require user to be an admin, redirect to login or home page if not
     *
     * @return void
     */
    public static function requireAdmin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        if (!self::isAdmin()) {
            header('Location: /');
            exit;
        }
    }

    /**
     * Get the current logged-in user
     *
     * @return array|null
     */
    public static function getUser(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['user_username'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'role' => $_SESSION['user_role'] ?? null,
        ];
    }

    /**
     * Login a user and set session variables
     *
     * @param int $userId
     * @param string $username
     * @param string $email
     * @param string $role
     * @return void
     */
    public static function login(int $userId, string $username, string $email, string $role): void
    {
        // Prevent session fixation
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_username'] = $username;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
    }

    /**
     * Logout the current user
     *
     * @return void
     */
    public static function logout(): void
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);

        // Destroy the session
        session_destroy();
    }
}
