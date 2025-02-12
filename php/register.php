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

// Function to validate password
function validatePassword($password) {
    return strlen($password) >= 8;
}

// Function to hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to check if user already exists
function userExists($email, $filename) {
    if (!file_exists($filename)) {
        return false;
    }
    
    $users = file($filename, FILE_IGNORE_NEW_LINES);
    foreach ($users as $user) {
        $userData = json_decode($user, true);
        if ($userData && $userData['email'] === $email) {
            return true;
        }
    }
    return false;
}

// Function to save user data
function saveUser($userData, $filename) {
    // Create data directory if it doesn't exist
    $dir = dirname($filename);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Append user data to file
    $jsonData = json_encode($userData) . "\n";
    return file_put_contents($filename, $jsonData, FILE_APPEND | LOCK_EX) !== false;
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $data = [];
    
    // Get and sanitize form data
    $fullName = sanitizeInput($_POST['fullName'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $dob = sanitizeInput($_POST['dob'] ?? '');
    $gender = sanitizeInput($_POST['gender'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    
    // Validation
    if (empty($fullName)) {
        $errors[] = 'Full name is required';
    } elseif (!preg_match("/^[a-zA-Z\s]{2,}$/", $fullName)) {
        $errors[] = 'Please enter a valid name';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (!validatePassword($password)) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    
    if (empty($confirmPassword)) {
        $errors[] = 'Please confirm your password';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($dob)) {
        $errors[] = 'Date of birth is required';
    }
    
    if (empty($gender)) {
        $errors[] = 'Please select your gender';
    }
    
    // Check if user already exists
    $usersFile = '../data/users.txt';
    if (userExists($email, $usersFile)) {
        $errors[] = 'An account with this email already exists';
    }
    
    // If no errors, save the user
    if (empty($errors)) {
        $userData = [
            'fullName' => $fullName,
            'email' => $email,
            'password' => hashPassword($password),
            'dob' => $dob,
            'gender' => $gender,
            'phone' => $phone,
            'address' => $address,
            'registration_date' => date('Y-m-d H:i:s'),
            'last_login' => null
        ];
        
        if (saveUser($userData, $usersFile)) {
            // Registration successful
            $_SESSION['registration_success'] = true;
            $_SESSION['registered_email'] = $email;
            
            // Return JSON response for AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Registration successful! You can now log in.',
                    'redirect' => 'login.html'
                ]);
                exit();
            }
            
            // Regular form submission - redirect to login page
            header('Location: ../login.html?registered=1');
            exit();
        } else {
            $errors[] = 'Failed to save user data. Please try again.';
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
        $_SESSION['registration_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: ../register.html?error=1');
        exit();
    }
} else {
    // Not a POST request - redirect to registration page
    header('Location: ../register.html');
    exit();
}
?>