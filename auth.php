<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_admin_logged_in(): bool
{
    return isset($_SESSION["admin_id"], $_SESSION["admin_username"]);
}

function require_admin_login(): void
{
    if (!is_admin_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

