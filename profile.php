<?php
session_start();

// Check if user is logged in, if not then redirect to login page
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Corrected the redirect path to be relative to the root
    header('Location: login.html');
    exit();
}

// Get user data from session
$user_data = $_SESSION['user_data'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>Student Portal</h2>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.html" class="nav-link">Home</a> </li>
                <li class="nav-item">
                    <a href="profile.php" class="nav-link active">Profile</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link">Contact</a>
                </li>
                <li class="nav-item">
                    <a href="php/logout.php" class="nav-link" style="color: var(--error-color);">Logout</a>
                </li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
            <button class="theme-toggle" id="themeToggle">🌙</button>
        </div>
    </nav>

    <main class="main-content">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user_data['fullName'] ?? 'U', 0, 1)); ?>
                </div>
                <h1><?php echo htmlspecialchars($user_data['fullName'] ?? 'User'); ?></h1>
                <p style="color: var(--text-secondary); font-size: 1.1rem;">
                    <?php echo htmlspecialchars($user_data['email'] ?? 'user@example.com'); ?>
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1.5rem;">
                    <button class="btn btn-primary" onclick="editProfile()">Edit Profile</button>
                    <button class="btn btn-secondary" onclick="changePassword()">Change Password</button>
                </div>
            </div>

            <div class="profile-info">
                <h2 style="margin-bottom: 2rem; color: var(--text-primary);">Personal Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['fullName'] ?? 'Not provided'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['email'] ?? 'Not provided'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">
                            <?php 
                            if (!empty($user_data['dob'])) {
                                $date = new DateTime($user_data['dob']);
                                echo $date->format('F j, Y');
                            } else {
                                echo 'Not provided';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value"><?php echo ucfirst(str_replace('-', ' ', htmlspecialchars($user_data['gender'] ?? 'Not provided'))); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($user_data['phone'] ?? 'Not provided'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Registration Date</div>
                        <div class="info-value">
                            <?php 
                            if (!empty($user_data['registration_date'])) {
                                $date = new DateTime($user_data['registration_date']);
                                echo $date->format('F j, Y');
                            } else {
                                echo 'Not available';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php if (!empty($user_data['address'])): ?>
                <div style="margin-top: 2rem;">
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?php echo nl2br(htmlspecialchars($user_data['address'])); ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="profile-info" style="margin-top: 2rem;">
                <h2 style="margin-bottom: 2rem; color: var(--text-primary);">Account Statistics</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Account Status</div>
                        <div class="info-value" style="color: var(--success-color); font-weight: 600;">Active</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Last Login</div>
                        <div class="info-value">
                            <?php
                            // ** FIXED BUG **: Display the actual last login time from the session data
                            if (!empty($user_data['last_login'])) {
                                $date = new DateTime($user_data['last_login']);
                                echo $date->format('F j, Y - g:i A');
                            } else {
                                echo 'This is your first login!';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Profile Completion</div>
                        <div class="info-value">
                            <?php
                            $fields = ['fullName', 'email', 'dob', 'gender', 'phone', 'address'];
                            $completed = 0;
                            foreach ($fields as $field) {
                                if (!empty($user_data[$field])) {
                                    $completed++;
                                }
                            }
                            $percentage = round(($completed / count($fields)) * 100);
                            echo $percentage . '%';
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-info" style="margin-top: 2rem;">
                 <h2 style="margin-bottom: 2rem; color: var(--text-primary);">Quick Actions</h2>
                 <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                     <a href="contact.php" class="btn btn-primary" style="text-decoration: none; display: block; text-align: center;">📞 Contact Us</a>
                     <button class="btn btn-secondary" onclick="downloadProfile()">📄 Download Profile</button>
                     <button class="btn btn-secondary" onclick="printProfile()">🖨️ Print Profile</button>
                 </div>
            </div>
        </div>
    </main>

    <div id="editModal" class="modal"><div class="modal-content"><div class="modal-header"><h3>Edit Profile</h3><button class="close" onclick="closeModal('editModal')">&times;</button></div><div class="modal-body"><p>Profile editing functionality requires a dedicated PHP script to handle updates. This is a placeholder.</p></div></div></div>
    <!-- <div id="passwordModal" class="modal"><div class="modal-content"><div class="modal-header"><h3>Change Password</h3><button class="close" onclick="closeModal('passwordModal')">&times;</button></div><div class="modal-body"><form id="changePasswordForm"><div class="form-group"><label for="currentPassword">Current Password</label><input type="password" id="currentPassword" name="currentPassword" required></div><div class="form-group"><label for="newPassword">New Password</label><input type="password" id="newPassword" name="newPassword" required></div><div class="form-group"><label for="confirmNewPassword">Confirm New Password</label><input type="password" id="confirmNewPassword" name="confirmNewPassword" required></div><div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;"><button type="button" class="btn btn-secondary" onclick="closeModal('passwordModal')">Cancel</button><button type="submit" class="btn btn-primary">Update Password</button></div></form></div></div></div> -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Change Password</h3>
                <button class="close" onclick="closeModal('passwordModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm" action="php/change_password.php" method="POST">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" name="currentPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" name="newPassword" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmNewPassword">Confirm New Password</label>
                        <input type="password" id="confirmNewPassword" name="confirmNewPassword" required>
                    </div>
                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('passwordModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> Student Portal. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script>
        function editProfile() {
            showModal('editModal');
        }

        function changePassword() {
            showModal('passwordModal');
        }

        function downloadProfile() {
            const profileData = {
                // ** FIXED SECURITY ISSUE **: Used json_encode for safe JS object creation
                name: <?php echo json_encode($user_data['fullName'] ?? ''); ?>,
                email: <?php echo json_encode($user_data['email'] ?? ''); ?>,
                dob: <?php echo json_encode($user_data['dob'] ?? ''); ?>,
                gender: <?php echo json_encode($user_data['gender'] ?? ''); ?>,
                phone: <?php echo json_encode($user_data['phone'] ?? ''); ?>,
                address: <?php echo json_encode($user_data['address'] ?? ''); ?>
            };

            const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(profileData, null, 2));
            const downloadAnchorNode = document.createElement('a');
            downloadAnchorNode.setAttribute("href", dataStr);
            downloadAnchorNode.setAttribute("download", "profile.json");
            document.body.appendChild(downloadAnchorNode);
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
        }

        function printProfile() {
            window.print();
        }
    </script>
</body>
</html>