<?php

namespace Demoshop\Local\Presentation\helper;

/**
 * Class for managing sessions and setting cookies
 */
class SessionManager
{
    /**
     * SessionManager singleton instance
     *
     * @var SessionManager|null
     */
    private static ?SessionManager $instance = null;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Singleton access to the SessionManager instance
     */
    public static function getInstance(): SessionManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Sets currently active admin(user) id in session
     *
     * @param $adminId
     *
     * @return void
     */
    public function setAdminId($adminId): void
    {
        $_SESSION['admin_id'] = $adminId;
    }

    /**
     * Returns the ID of the active admin(user)
     *
     * @return int|null
     */
    public function getAdminId(): ?int
    {
        return $_SESSION['admin_id'] ?? null;
    }

    /**
     * Check if there is an active admin(user) in cookie and session
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['admin_id']) || isset($_COOKIE['admin_id']);
    }

    /**
     * Set a "keep me logged in" cookie that expires in 30 days
     *
     * @param $adminId
     *
     * @return void
     */
    public function setRememberMeCookie($adminId): void
    {
        setcookie('admin_id', $adminId, time() + (30 * 24 * 60 * 60), '/');
    }

    /**
     * Log out and destroy session and annul cookie
     *
     * @return void
     */
    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();

        setcookie('admin_id', '', time() - 3600, '/');
    }
}
