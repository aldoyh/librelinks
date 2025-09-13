<?php
require_once 'config.php';
require_once 'auth.php';

/**
 * API Endpoints for AJAX calls
 */

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// CSRF protection for POST requests
if ($method === 'POST' && !isset($_POST['_token'])) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        jsonResponse(['success' => false, 'message' => 'بيانات غير صحيحة'], 400);
    }
    $_POST = $input;
}

switch ($action) {
    case 'register':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        $email = sanitizeInput($_POST['email'] ?? '');
        $name = sanitizeInput($_POST['name'] ?? '');
        $handle = sanitizeInput($_POST['handle'] ?? '');
        
        if (empty($email) || empty($name) || empty($handle)) {
            jsonResponse(['success' => false, 'message' => 'جميع الحقول مطلوبة']);
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'البريد الإلكتروني غير صحيح']);
        }
        
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $handle)) {
            jsonResponse(['success' => false, 'message' => 'اسم المستخدم يجب أن يحتوي على أحرف وأرقام فقط']);
        }
        
        $result = registerUser($email, $name, $handle);
        if ($result['success']) {
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_email'] = $result['user']['email'];
            $_SESSION['user_name'] = $result['user']['name'];
        }
        
        jsonResponse($result);
        break;
        
    case 'login':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        $email = sanitizeInput($_POST['email'] ?? '');
        
        if (empty($email)) {
            jsonResponse(['success' => false, 'message' => 'البريد الإلكتروني مطلوب']);
        }
        
        $result = loginUser($email);
        jsonResponse($result);
        break;
        
    case 'logout':
        logout();
        jsonResponse(['success' => true]);
        break;
        
    case 'get_current_user':
        $user = getCurrentUser();
        if ($user) {
            jsonResponse(['success' => true, 'user' => $user]);
        } else {
            jsonResponse(['success' => false, 'message' => 'غير مسجل الدخول']);
        }
        break;
        
    case 'update_profile':
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'يجب تسجيل الدخول'], 401);
        }
        
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        $userId = $_SESSION['user_id'];
        $updateData = [];
        
        if (isset($_POST['name'])) {
            $updateData['name'] = sanitizeInput($_POST['name']);
        }
        if (isset($_POST['bio'])) {
            $updateData['bio'] = sanitizeInput($_POST['bio']);
        }
        if (isset($_POST['handle'])) {
            $handle = sanitizeInput($_POST['handle']);
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $handle)) {
                jsonResponse(['success' => false, 'message' => 'اسم المستخدم غير صحيح']);
            }
            $updateData['handle'] = $handle;
        }
        
        $updatedUser = updateUser($userId, $updateData);
        if ($updatedUser) {
            jsonResponse(['success' => true, 'user' => $updatedUser]);
        } else {
            jsonResponse(['success' => false, 'message' => 'فشل في التحديث']);
        }
        break;
        
    case 'get_links':
        $userId = $_GET['userId'] ?? $_SESSION['user_id'] ?? '';
        if (empty($userId)) {
            jsonResponse(['success' => false, 'message' => 'معرف المستخدم مطلوب']);
        }
        
        $links = Database::getLinks($userId);
        usort($links, function($a, $b) {
            return $a['order'] - $b['order'];
        });
        
        jsonResponse(['success' => true, 'links' => $links]);
        break;
        
    case 'add_link':
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'يجب تسجيل الدخول'], 401);
        }
        
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        $title = sanitizeInput($_POST['title'] ?? '');
        $url = sanitizeInput($_POST['url'] ?? '');
        
        if (empty($title) || empty($url)) {
            jsonResponse(['success' => false, 'message' => 'العنوان والرابط مطلوبان']);
        }
        
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            jsonResponse(['success' => false, 'message' => 'الرابط غير صحيح']);
        }
        
        $userId = $_SESSION['user_id'];
        $allLinks = Database::getLinks($userId);
        $order = count($allLinks) + 1;
        
        $newLink = [
            'id' => generateId(),
            'title' => $title,
            'url' => $url,
            'archived' => false,
            'order' => $order,
            'isSocial' => false,
            'clicks' => 0,
            'createdAt' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s'),
            'userId' => $userId
        ];
        
        // Get all links and add new one
        $file = 'data/links.json';
        $allLinksData = [];
        if (file_exists($file)) {
            $allLinksData = json_decode(file_get_contents($file), true) ?: [];
        }
        $allLinksData[] = $newLink;
        Database::saveLinks($allLinksData);
        
        jsonResponse(['success' => true, 'link' => $newLink]);
        break;
        
    case 'update_link':
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'يجب تسجيل الدخول'], 401);
        }
        
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        $linkId = sanitizeInput($_POST['linkId'] ?? '');
        $title = sanitizeInput($_POST['title'] ?? '');
        $url = sanitizeInput($_POST['url'] ?? '');
        
        if (empty($linkId)) {
            jsonResponse(['success' => false, 'message' => 'معرف الرابط مطلوب']);
        }
        
        $file = 'data/links.json';
        $allLinks = [];
        if (file_exists($file)) {
            $allLinks = json_decode(file_get_contents($file), true) ?: [];
        }
        
        $updated = false;
        for ($i = 0; $i < count($allLinks); $i++) {
            if ($allLinks[$i]['id'] === $linkId && $allLinks[$i]['userId'] === $_SESSION['user_id']) {
                if (!empty($title)) $allLinks[$i]['title'] = $title;
                if (!empty($url)) $allLinks[$i]['url'] = $url;
                $allLinks[$i]['updatedAt'] = date('Y-m-d H:i:s');
                $updated = true;
                break;
            }
        }
        
        if ($updated) {
            Database::saveLinks($allLinks);
            jsonResponse(['success' => true]);
        } else {
            jsonResponse(['success' => false, 'message' => 'الرابط غير موجود']);
        }
        break;
        
    case 'delete_link':
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'يجب تسجيل الدخول'], 401);
        }
        
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        $linkId = sanitizeInput($_POST['linkId'] ?? '');
        
        if (empty($linkId)) {
            jsonResponse(['success' => false, 'message' => 'معرف الرابط مطلوب']);
        }
        
        $file = 'data/links.json';
        $allLinks = [];
        if (file_exists($file)) {
            $allLinks = json_decode(file_get_contents($file), true) ?: [];
        }
        
        $allLinks = array_filter($allLinks, function($link) use ($linkId) {
            return !($link['id'] === $linkId && $link['userId'] === $_SESSION['user_id']);
        });
        
        Database::saveLinks(array_values($allLinks));
        jsonResponse(['success' => true]);
        break;
        
    case 'track_click':
        if ($method !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }
        
        $linkId = sanitizeInput($_POST['linkId'] ?? '');
        
        if (empty($linkId)) {
            jsonResponse(['success' => false, 'message' => 'معرف الرابط مطلوب']);
        }
        
        $file = 'data/links.json';
        $allLinks = [];
        if (file_exists($file)) {
            $allLinks = json_decode(file_get_contents($file), true) ?: [];
        }
        
        for ($i = 0; $i < count($allLinks); $i++) {
            if ($allLinks[$i]['id'] === $linkId) {
                $allLinks[$i]['clicks']++;
                $allLinks[$i]['updatedAt'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        Database::saveLinks($allLinks);
        jsonResponse(['success' => true]);
        break;
        
    default:
        jsonResponse(['success' => false, 'message' => 'إجراء غير معروف'], 404);
}
?>