<?php
/**
 * LibreLinks - PHP Configuration
 * Database connection and global settings
 */

// Start session
session_start();

// Database configuration
define('DB_CONNECTION_STRING', getenv('DATABASE_URL') ?: 'mongodb://localhost:27017/librelinks');

// App configuration
define('APP_NAME', 'ليبر لينكس'); // LibreLinks in Arabic
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');
define('ITEMS_PER_PAGE', 20);

// Security
define('CSRF_TOKEN_LENGTH', 32);

// MongoDB connection
class Database {
    private static $client = null;
    private static $db = null;
    
    public static function connect() {
        if (self::$client === null) {
            try {
                // Simple file-based storage for now (can be replaced with MongoDB later)
                self::$client = true;
                if (!file_exists('data')) {
                    mkdir('data', 0755, true);
                }
            } catch (Exception $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new Exception("فشل في الاتصال بقاعدة البيانات");
            }
        }
        return self::$client;
    }
    
    public static function getUsers() {
        $file = 'data/users.json';
        if (!file_exists($file)) {
            return [];
        }
        return json_decode(file_get_contents($file), true) ?: [];
    }
    
    public static function saveUsers($users) {
        file_put_contents('data/users.json', json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    
    public static function getLinks($userId) {
        $file = 'data/links.json';
        if (!file_exists($file)) {
            return [];
        }
        $allLinks = json_decode(file_get_contents($file), true) ?: [];
        return array_filter($allLinks, function($link) use ($userId) {
            return $link['userId'] === $userId;
        });
    }
    
    public static function saveLinks($links) {
        file_put_contents('data/links.json', json_encode($links, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}

// Utility functions
function generateId() {
    return uniqid('ll_', true);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $users = Database::getUsers();
    foreach ($users as $user) {
        if ($user['id'] === $_SESSION['user_id']) {
            return $user;
        }
    }
    return null;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Initialize database connection
Database::connect();
?>