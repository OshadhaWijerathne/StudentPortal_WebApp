<?php
date_default_timezone_set('Asia/Colombo'); 
session_start();

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to find user by email
function findUser($email, $filename) {
    if (!file_exists($filename)) {
        return false;
    }
    
    $users = file($filename, FILE_IGNORE_NEW_LINES);
    foreach ($users as $user) {
        $userData = json_decode($user, true);
        if ($userData && $userData['email'] === $email) {
            return $userData;
        }
    }
    return false;
}

// Function to update last login
function updateLastLogin($email, $filename) {
    if (!file_exists($filename)) {
        return false;
    }
    
    $users = file($filename, FILE_IGNORE_NEW_LINES);
    $updatedUsers = [];
    
    foreach ($users as $user) {
        $userData = json_decode($user, true);
        if ($userData && $userData['email'] === $email) {
            $userData['last_login'] = date('Y-m-d H:i:s');
        }
        $updatedUsers[] = json_encode($userData);
    }
    
    return file_put_contents($filename, implode("\n", $updatedUsers) . "\n") !== false;
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Get and sanitize form data
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validation
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    }
    
    // If no validation errors, check credentials
    if (empty($errors)) {
        $usersFile = '../data/users.txt';
        $user = findUser($email, $usersFile);
        
        // Check for demo credentials
        if ($email === 'demo@student.com' && $password === 'demo123456') {
            $user = [
                'fullName' => 'Demo User',
                'email' => 'demo@student.com',
                'dob' => '1995-01-01',
                'gender' => 'prefer-not-to-say',
                'phone' => '+1 (555) 123-4567',
                'address' => '123 Demo Street, Demo City, DC 12345',
                'registration_date' => '2024-01-01 00:00:00',
                'last_login' => date('Y-m-d H:i:s')
            ];
        }
        
        if ($user && (password_verify($password, $user['password'] ?? '') || $email === 'demo@student.com')) {
            // Login successful
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_data'] = $user;
            
            // Update last login (except for demo user)
            if ($email !== 'demo@student.com') {
                updateLastLogin($email, $usersFile);
            }
            
            // Set remember me cookie if requested
            if ($remember) {
                $cookieValue = base64_encode(json_encode([
                    'email' => $email,
                    'token' => hash('sha256', $email . time())
                ]));
                setcookie('remember_user', $cookieValue, time() + (30 * 24 * 60 * 60), '/'); // 30 days
            }
            
            // Return JSON response for AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful!',
                    'redirect' => 'profile.php'
                ]);
                exit();
            }
            
            // Regular form submission - redirect to profile
            header('Location: ../profile.php');
            exit();
        } else {
            $errors[] = 'Invalid email or password';
        }
    }
    
    // If there are errors, return them
    if (!empty($errors)) {
        // AJAX response
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit();
        }
        
        // Regular form submission - redirect back with errors
        $_SESSION['login_errors'] = $errors;
        $_SESSION['login_email'] = $email;
        header('Location: ../login.html?error=1');
        exit();
    }
} else {
    // Not a POST request - redirect to login page
    header('Location: ../login.html');
    exit();
}
?>