<?php
date_default_timezone_set('Asia/Colombo');
session_start();
header('Content-Type: application/json');

// 1. --- SECURITY CHECKS ---
// ------------------------------------
if (!isset($_SESSION['user_logged_in']) || !isset($_SESSION['user_email'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['errors' => ['Please log in to change your password.']]);
    exit();
}
// Cannot change the demo user's password
if ($_SESSION['user_email'] === 'demo@student.com') {
    http_response_code(403); // Forbidden
    echo json_encode(['errors' => ['Cannot change the password for the demo account.']]);
    exit();
}

// 2. --- GET INPUT & VALIDATE ---
// ---------------------------------
$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmNewPassword = $_POST['confirmNewPassword'] ?? '';
$errors = [];

if (empty($currentPassword)) { $errors[] = 'Current Password is required.'; }
if (empty($newPassword)) { $errors[] = 'New Password is required.'; }
if (strlen($newPassword) < 8) { $errors[] = 'New password must be at least 8 characters long.'; }
if ($newPassword !== $confirmNewPassword) { $errors[] = 'New passwords do not match.'; }

if (!empty($errors)) {
    http_response_code(400); // Bad Request
    echo json_encode(['errors' => $errors]);
    exit();
}

// 3. --- READ FILE & VERIFY PASSWORD ---
// -----------------------------------------
$usersFile = '../data/users.txt';
$user_email = $_SESSION['user_email'];

if (!file_exists($usersFile)) {
    http_response_code(500);
    echo json_encode(['errors' => ['User data file not found.']]);
    exit();
}

$users = file($usersFile, FILE_IGNORE_NEW_LINES);
$userFound = false;
$userToUpdate = null;
$userIndex = -1;

foreach ($users as $index => $line) {
    $userData = json_decode($line, true);
    if ($userData && $userData['email'] === $user_email) {
        $userFound = true;
        $userIndex = $index;
        $userToUpdate = $userData;
        break;
    }
}

if (!$userFound) {
    http_response_code(404);
    echo json_encode(['errors' => ['Your user account could not be found.']]);
    exit();
}

// Verify the current password
if (!password_verify($currentPassword, $userToUpdate['password'])) {
    http_response_code(400);
    echo json_encode(['errors' => ['Incorrect current password.']]);
    exit();
}

// 4. --- HASH NEW PASSWORD & WRITE TO FILE ---
// ----------------------------------------------
$new_password_hash = password_hash($newPassword, PASSWORD_DEFAULT);
$userToUpdate['password'] = $new_password_hash;

// Replace the old user data with the new data at the correct index
$users[$userIndex] = json_encode($userToUpdate);

// Write the entire updated array back to the file
if (file_put_contents($usersFile, implode("\n", $users) . "\n") !== false) {
    // Success!
    http_response_code(200);
    echo json_encode(['message' => 'Password updated successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['errors' => ['Failed to write updated user data.']]);
}
?>