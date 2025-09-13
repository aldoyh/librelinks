<?php
require_once 'config.php';

/**
 * Authentication functions
 */

function registerUser($email, $name, $handle) {
    $users = Database::getUsers();
    
    // Check if user already exists
    foreach ($users as $user) {
        if ($user['email'] === $email || $user['handle'] === $handle) {
            return ['success' => false, 'message' => 'المستخدم موجود بالفعل'];
        }
    }
    
    // Create new user
    $newUser = [
        'id' => generateId(),
        'name' => $name,
        'handle' => $handle,
        'bio' => '',
        'image' => '',
        'email' => $email,
        'totalViews' => 0,
        'createdAt' => date('Y-m-d H:i:s'),
        'updatedAt' => date('Y-m-d H:i:s'),
        'linksLocation' => 'top',
        'themePalette' => [
            'name' => 'فاتح',
            'palette' => ['#FFFFFF', '#F2F2F2', '#1F2937', '#6170F8']
        ],
        'buttonStyle' => 'rounded-md'
    ];
    
    $users[] = $newUser;
    Database::saveUsers($users);
    
    return ['success' => true, 'user' => $newUser];
}

function loginUser($email) {
    $users = Database::getUsers();
    
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            return ['success' => true, 'user' => $user];
        }
    }
    
    return ['success' => false, 'message' => 'البريد الإلكتروني غير موجود'];
}

function logout() {
    session_destroy();
    return ['success' => true];
}

function getUserByHandle($handle) {
    $users = Database::getUsers();
    
    foreach ($users as $user) {
        if ($user['handle'] === $handle) {
            return $user;
        }
    }
    
    return null;
}

function updateUser($userId, $data) {
    $users = Database::getUsers();
    
    for ($i = 0; $i < count($users); $i++) {
        if ($users[$i]['id'] === $userId) {
            foreach ($data as $key => $value) {
                if (isset($users[$i][$key])) {
                    $users[$i][$key] = $value;
                }
            }
            $users[$i]['updatedAt'] = date('Y-m-d H:i:s');
            Database::saveUsers($users);
            return $users[$i];
        }
    }
    
    return null;
}

function incrementUserViews($userId) {
    $users = Database::getUsers();
    
    for ($i = 0; $i < count($users); $i++) {
        if ($users[$i]['id'] === $userId) {
            $users[$i]['totalViews']++;
            $users[$i]['updatedAt'] = date('Y-m-d H:i:s');
            Database::saveUsers($users);
            return $users[$i];
        }
    }
    
    return null;
}
?>