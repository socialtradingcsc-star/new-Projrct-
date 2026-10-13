<?php
// core/Security.php

class Security {
    // CSRF टोकन बनाना (फॉर्म्स को सुरक्षित रखने के लिए)
    public static function generateCSRF() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // CSRF टोकन चेक करना
    public static function verifyCSRF($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            die('Security Check Failed: Invalid CSRF Token.');
        }
        return true;
    }

    // आउटपुट को सुरक्षित करना (XSS हमलों से बचाव)
    public static function escape($html) {
        return htmlspecialchars($html ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    // इनपुट को साफ़ करना (डेटाबेस में जाने से पहले)
    public static function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data ?? '')), ENT_QUOTES, 'UTF-8');
    }
}
?>