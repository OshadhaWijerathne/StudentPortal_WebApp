<?php
date_default_timezone_set('Asia/Colombo'); 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
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
    <title>Student Portal - Contact</title>
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
                    <a href="index.html" class="nav-link">Home</a>
                </li>
                <li class="nav-item">
                    <a href="profile.php" class="nav-link">Profile</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link active">Contact</a>
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
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h1 style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--text-primary);">Contact Us</h1>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; max-width: 1000px; margin: 0 auto;">
                <!-- Contact Form -->
                <div class="form-container" style="margin: 0;">
                    <h2>Send us a Message</h2>
                    
                    <form id="contactForm" action="php/contact_submit.php" method="POST">
                        <input type="hidden" name="user_email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>">
                        <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($user_data['fullName'] ?? ''); ?>">
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select id="subject" name="subject" required>
                                <option value="">Select a subject</option>
                                <option value="general">General Inquiry</option>
                                <option value="technical">Technical Support</option>
                                <option value="account">Account Issues</option>
                                <option value="feedback">Feedback</option>
                                <option value="bug">Report a Bug</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="form-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="6" required 
                                      placeholder="Tell us what's on your mind..."></textarea>
                            <div class="form-error"></div>
                        </div>

                        <div class="form-group">
                            <label>Rate your experience with our platform</label>
                            <div class="rating-container">
                                <span class="star" data-rating="1">⭐</span>
                                <span class="star" data-rating="2">⭐</span>
                                <span class="star" data-rating="3">⭐</span>
                                <span class="star" data-rating="4">⭐</span>
                                <span class="star" data-rating="5">⭐</span>
                                <span style="margin-left: 1rem; color: var(--text-secondary); font-size: 0.9rem;">
                                    Click to rate
                                </span>
                            </div>
                            <input type="hidden" id="rating" name="rating" value="0">
                        </div>

                        <div class="form-group">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" id="copy_me" name="copy_me" style="width: auto;">
                                <label for="copy_me" style="margin-bottom: 0; font-weight: normal; color: var(--text-secondary);">
                                    Send me a copy of this message
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div>
                    <div class="form-container" style="margin: 0; height: fit-content;">
                        <h2>Get in Touch</h2>
                        
                        <div style="margin-bottom: 2rem;">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                                <div style="width: 3rem; height: 3rem; background-color: var(--primary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                                    📧
                                </div>
                                <div>
                                    <h4 style="margin-bottom: 0.25rem; color: var(--text-primary);">Email</h4>
                                    <p style="color: var(--text-secondary); margin: 0;">support@studentportal.com</p>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                                <div style="width: 3rem; height: 3rem; background-color: var(--secondary-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                                    📞
                                </div>
                                <div>
                                    <h4 style="margin-bottom: 0.25rem; color: var(--text-primary);">Phone</h4>
                                    <p style="color: var(--text-secondary); margin: 0;">+1 (555) 123-4567</p>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                                <div style="width: 3rem; height: 3rem; background-color: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                                    📍
                                </div>
                                <div>
                                    <h4 style="margin-bottom: 0.25rem; color: var(--text-primary);">Address</h4>
                                    <p style="color: var(--text-secondary); margin: 0;">123 Education Street<br>Learning City, LC 12345</p>
                                </div>
                            </div>
                        </div>

                        <div style="padding: 1.5rem; background-color: rgba(79, 70, 229, 0.1); border-radius: var(--border-radius); border: 1px solid rgba(79, 70, 229, 0.2);">
                            <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Business Hours</h4>
                            <div style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">
                                <p style="margin: 0.25rem 0;"><strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM</p>
                                <p style="margin: 0.25rem 0;"><strong>Saturday:</strong> 10:00 AM - 4:00 PM</p>
                                <p style="margin: 0.25rem 0;"><strong>Sunday:</strong> Closed</p>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="form-container" style="margin-top: 2rem;">
                        <h3>Frequently Asked Questions</h3>
                        
                        <div style="margin-top: 1.5rem;">
                            <details style="margin-bottom: 1rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 1rem;">
                                <summary style="cursor: pointer; font-weight: 600; color: var(--text-primary);">
                                    How do I reset my password?
                                </summary>
                                <p style="color: var(--text-secondary); margin-top: 0.5rem; margin-bottom: 0;">
                                    You can reset your password by clicking on "Forgot Password" on the login page and following the instructions.
                                </p>
                            </details>

                            <details style="margin-bottom: 1rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 1rem;">
                                <summary style="cursor: pointer; font-weight: 600; color: var(--text-primary);">
                                    How do I update my profile information?
                                </summary>
                                <p style="color: var(--text-secondary); margin-top: 0.5rem; margin-bottom: 0;">
                                    Go to your profile page and click on "Edit Profile" to update your personal information.
                                </p>
                            </details>

                            <details style="margin-bottom: 1rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 1rem;">
                                <summary style="cursor: pointer; font-weight: 600; color: var(--text-primary);">
                                    What browsers are supported?
                                </summary>
                                <p style="color: var(--text-secondary); margin-top: 0.5rem; margin-bottom: 0;">
                                    Our platform supports all modern browsers including Chrome, Firefox, Safari, and Edge.
                                </p>
                            </details>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Message Sent!</h3>
                <button class="close">&times;</button>
            </div>
            <div class="modal-body">
                Thank you for your message! We'll get back to you as soon as possible.
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Student Portal. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>