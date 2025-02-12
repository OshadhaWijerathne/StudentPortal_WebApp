<?php
date_default_timezone_set('Asia/Colombo'); 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../login.html');
    exit();
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to save contact data
function saveContact($contactData, $filename) {
    // Create data directory if it doesn't exist
    $dir = dirname($filename);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Append contact data to file
    $jsonData = json_encode($contactData) . "\n";
    return file_put_contents($filename, $jsonData, FILE_APPEND | LOCK_EX) !== false;
}

// Function to send notification email (simulation)
function sendNotificationEmail($contactData) {
    // In a real application, you would send an actual email here
    // For this demo, we'll just log the attempt
    $logFile = '../data/email_log.txt';
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'to' => 'admin@studentportal.com',
        'subject' => 'New Contact Form Submission',
        'from' => $contactData['user_email'],
        'message' => 'New contact form submission received',
        'status' => 'simulated'
    ];
    
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    return true;
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Get and sanitize form data
    $userEmail = sanitizeInput($_POST['user_email'] ?? '');
    $userName = sanitizeInput($_POST['user_name'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0);
    $copyMe = isset($_POST['copy_me']);
    
    // Validation
    if (empty($subject)) {
        $errors[] = 'Subject is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    } elseif (strlen($message) < 10) {
        $errors[] = 'Message must be at least 10 characters long';
    }
    
    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Please provide a rating between 1 and 5 stars';
    }
    
    // If no errors, save the contact data
    if (empty($errors)) {
        $contactData = [
            'user_email' => $userEmail,
            'user_name' => $userName,
            'subject' => $subject,
            'message' => $message,
            'rating' => $rating,
            'copy_me' => $copyMe,
            'submission_date' => date('Y-m-d H:i:s'),
            'status' => 'new',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        $contactFile = '../data/contact.txt';
        
        if (saveContact($contactData, $contactFile)) {
            // Send notification email (simulated)
            sendNotificationEmail($contactData);
            
            // If user requested a copy, simulate sending them a copy
            if ($copyMe) {
                $copyLogEntry = [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'to' => $userEmail,
                    'subject' => 'Copy of your message: ' . $subject,
                    'message' => 'This is a copy of the message you sent to Student Portal',
                    'status' => 'simulated'
                ];
                
                $logFile = '../data/email_log.txt';
                file_put_contents($logFile, json_encode($copyLogEntry) . "\n", FILE_APPEND | LOCK_EX);
            }
            
            // Return JSON response for AJAX
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Thank you for your message! We will get back to you soon.'
                ]);
                exit();
            }
            
            // Regular form submission - redirect back with success message
            $_SESSION['contact_success'] = 'Thank you for your message! We will get back to you soon.';
            header('Location: ../contact.php?sent=1');
            exit();
        } else {
            $errors[] = 'Failed to save your message. Please try again.';
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
        $_SESSION['contact_errors'] = $errors;
        $_SESSION['contact_form_data'] = $_POST;
        header('Location: ../contact.php?error=1');
        exit();
    }
} else {
    // Not a POST request - redirect to contact page
    header('Location: ../contact.php');
    exit();
}
?>