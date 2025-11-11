<?php

// Ensure database connection is available globally
require_once 'db.php';

// Redirect to login if not logged in or not the expected role
function require_login($role = null) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit();
    }

    if ($role && $_SESSION['user_role'] !== $role) {
        header('Location: ../index.php');
        exit();
    }
}

// Redirect to dashboard based on role
function redirect_to_dashboard() {
    if (!isset($_SESSION['user_role'])) {
        header('Location: ../auth/login.php');
        exit();
    }

    $role = $_SESSION['user_role'];
    switch ($role) {
        case 'admin':
            header('Location: ../dashboards/admin.php');
            break;
        case 'organizer':
            header('Location: ../dashboards/organizer.php');
            break;
        case 'participant':
            header('Location: ../dashboards/participant.php');
            break;
        default:
            header('Location: ../index.php');
    }
    exit();
}

// Set a flash message to display once
function set_flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

// Get and clear a flash message by key
function get_flash($key = null) {
    if (!isset($_SESSION['flash'])) return '';

    $messages = '';

    if ($key !== null && isset($_SESSION['flash'][$key])) {
        $messages = '<div class="flash-message">' . htmlspecialchars($_SESSION['flash'][$key]) . '</div>';
        unset($_SESSION['flash'][$key]);
    } elseif ($key === null) {
        foreach ($_SESSION['flash'] as $msg) {
            $messages .= '<div class="flash-message">' . htmlspecialchars($msg) . '</div>';
        }
        unset($_SESSION['flash']);
    }

    return $messages;
}

// Basic input sanitization
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}
