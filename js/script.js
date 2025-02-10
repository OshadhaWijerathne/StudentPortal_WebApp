// Global Variables
let currentTheme = 'light'; // Removed localStorage for Claude.ai compatibility
let currentRating = 0;

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    initializeTheme();
    initializeNavigation();
    initializeFormValidation();
    initializeModals();
    initializeRating();
    initializePasswordToggle();
});

// Theme Management
function initializeTheme() {
    const themeToggle = document.getElementById('themeToggle');
    
    // Apply saved theme
    document.documentElement.setAttribute('data-theme', currentTheme);
    updateThemeIcon();
    
    // Theme toggle event listener
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
}

function toggleTheme() {
    currentTheme = currentTheme === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);
    updateThemeIcon();
}

function updateThemeIcon() {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.textContent = currentTheme === 'light' ? '🌙' : '☀️';
    }
}

// Navigation Management
function initializeNavigation() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        
        // Close mobile menu when clicking on links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }
}

// Form Validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (form.id === 'registerForm') {
                validateRegistrationForm(form);
            } else if (form.id === 'loginForm') {
                validateLoginForm(form);
            } else if (form.id === 'contactForm') {
                validateContactForm(form);
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(input);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(input);
            });
        });
    });
}

function validateRegistrationForm(form) {
    const formData = new FormData(form);
    let isValid = true;
    
    // Validate full name
    const fullName = formData.get('fullName');
    if (!validateRequired(fullName)) {
        showFieldError(form.querySelector('[name="fullName"]'), 'Full name is required');
        isValid = false;
    } else if (!validateName(fullName)) {
        showFieldError(form.querySelector('[name="fullName"]'), 'Please enter a valid name');
        isValid = false;
    }
    
    // Validate email
    const email = formData.get('email');
    if (!validateRequired(email)) {
        showFieldError(form.querySelector('[name="email"]'), 'Email is required');
        isValid = false;
    } else if (!validateEmail(email)) {
        showFieldError(form.querySelector('[name="email"]'), 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate password
    const password = formData.get('password');
    if (!validateRequired(password)) {
        showFieldError(form.querySelector('[name="password"]'), 'Password is required');
        isValid = false;
    } else if (!validatePassword(password)) {
        showFieldError(form.querySelector('[name="password"]'), 'Password must be at least 8 characters long');
        isValid = false;
    }
    
    // Validate confirm password
    const confirmPassword = formData.get('confirmPassword');
    if (!validateRequired(confirmPassword)) {
        showFieldError(form.querySelector('[name="confirmPassword"]'), 'Please confirm your password');
        isValid = false;
    } else if (password !== confirmPassword) {
        showFieldError(form.querySelector('[name="confirmPassword"]'), 'Passwords do not match');
        isValid = false;
    }
    
    // Validate date of birth
    const dob = formData.get('dob');
    if (!validateRequired(dob)) {
        showFieldError(form.querySelector('[name="dob"]'), 'Date of birth is required');
        isValid = false;
    }
    
    // Validate gender
    const gender = formData.get('gender');
    if (!validateRequired(gender)) {
        showFieldError(form.querySelector('[name="gender"]'), 'Please select your gender');
        isValid = false;
    }
    
    if (isValid) {
        // Simulate registration process
        showLoadingButton(form.querySelector('button[type="submit"]'));
        
        setTimeout(() => {
            hideLoadingButton(form.querySelector('button[type="submit"]'));
            showSuccessModal('Account created successfully! You can now log in with your credentials.');
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        }, 1000);
    }
}

function validateLoginForm(form) {
    const formData = new FormData(form);
    let isValid = true;
    
    // Validate email
    const email = formData.get('email');
    if (!validateRequired(email)) {
        showFieldError(form.querySelector('[name="email"]'), 'Email is required');
        isValid = false;
    } else if (!validateEmail(email)) {
        showFieldError(form.querySelector('[name="email"]'), 'Please enter a valid email address');
        isValid = false;
    }
    
    // Validate password
    const password = formData.get('password');
    if (!validateRequired(password)) {
        showFieldError(form.querySelector('[name="password"]'), 'Password is required');
        isValid = false;
    }
    
    if (isValid) {
        // Simulate login process
        showLoadingButton(form.querySelector('button[type="submit"]'));
        
        setTimeout(() => {
            hideLoadingButton(form.querySelector('button[type="submit"]'));
            showSuccessModal('Login successful! Redirecting to your profile...');
            setTimeout(() => {
                window.location.href = 'profile.php';
            }, 1500);
        }, 1000);
    }
}

function validateContactForm(form) {
    const formData = new FormData(form);
    let isValid = true;
    
    // Validate subject
    const subject = formData.get('subject');
    if (!validateRequired(subject)) {
        showFieldError(form.querySelector('[name="subject"]'), 'Subject is required');
        isValid = false;
    }
    
    // Validate message
    const message = formData.get('message');
    if (!validateRequired(message)) {
        showFieldError(form.querySelector('[name="message"]'), 'Message is required');
        isValid = false;
    } else if (message.length < 10) {
        showFieldError(form.querySelector('[name="message"]'), 'Message must be at least 10 characters long');
        isValid = false;
    }
    
    // Validate rating
    if (currentRating === 0) {
        showAlert('Please provide a rating', 'error');
        isValid = false;
    }
    
    if (isValid) {
        showLoadingButton(form.querySelector('button[type="submit"]'));
        
        setTimeout(() => {
            hideLoadingButton(form.querySelector('button[type="submit"]'));
            showSuccessModal('Thank you for your message! We will get back to you soon.');
            form.reset();
            resetRating();
        }, 1000);
    }
}

// Field Validation Functions
function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    
    clearFieldError(field);
    
    switch(fieldName) {
        case 'fullName':
            if (!validateRequired(value)) {
                showFieldError(field, 'Full name is required');
            } else if (!validateName(value)) {
                showFieldError(field, 'Please enter a valid name');
            } else {
                showFieldSuccess(field);
            }
            break;
            
        case 'email':
            if (!validateRequired(value)) {
                showFieldError(field, 'Email is required');
            } else if (!validateEmail(value)) {
                showFieldError(field, 'Please enter a valid email address');
            } else {
                showFieldSuccess(field);
            }
            break;
            
        case 'password':
            if (!validateRequired(value)) {
                showFieldError(field, 'Password is required');
            } else if (!validatePassword(value)) {
                showFieldError(field, 'Password must be at least 8 characters long');
            } else {
                showFieldSuccess(field);
            }
            break;
            
        case 'confirmPassword':
            const password = document.querySelector('[name="password"]').value;
            if (!validateRequired(value)) {
                showFieldError(field, 'Please confirm your password');
            } else if (value !== password) {
                showFieldError(field, 'Passwords do not match');
            } else {
                showFieldSuccess(field);
            }
            break;
            
        case 'subject':
        case 'message':
            if (!validateRequired(value)) {
                showFieldError(field, `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} is required`);
            } else {
                showFieldSuccess(field);
            }
            break;
    }
}

// Validation Helper Functions
function validateRequired(value) {
    return value && value.trim().length > 0;
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePassword(password) {
    return password && password.length >= 8;
}

function validateName(name) {
    const nameRegex = /^[a-zA-Z\s]{2,}$/;
    return nameRegex.test(name);
}

// Field Error/Success Display
function showFieldError(field, message) {
    const formGroup = field.closest('.form-group');
    const errorElement = formGroup.querySelector('.form-error');
    
    formGroup.classList.add('error');
    formGroup.classList.remove('success');
    
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
}

function showFieldSuccess(field) {
    const formGroup = field.closest('.form-group');
    formGroup.classList.add('success');
    formGroup.classList.remove('error');
    clearFieldError(field);
}

function clearFieldError(field) {
    const formGroup = field.closest('.form-group');
    const errorElement = formGroup.querySelector('.form-error');
    
    formGroup.classList.remove('error');
    
    if (errorElement) {
        errorElement.classList.remove('show');
    }
}

// Password Toggle Functionality
function initializePasswordToggle() {
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const passwordField = this.previousElementSibling;
            const isPassword = passwordField.type === 'password';
            
            passwordField.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? '🙈' : '👁️';
        });
    });
}

// Rating System
function initializeRating() {
    const stars = document.querySelectorAll('.star');
    
    stars.forEach((star, index) => {
        star.addEventListener('click', function() {
            currentRating = index + 1;
            updateStarDisplay();
        });
        
        star.addEventListener('mouseenter', function() {
            highlightStars(index + 1);
        });
    });
    
    const ratingContainer = document.querySelector('.rating-container');
    if (ratingContainer) {
        ratingContainer.addEventListener('mouseleave', function() {
            updateStarDisplay();
        });
    }
}

function highlightStars(rating) {
    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.add('active');
        } else {
            star.classList.remove('active');
        }
    });
}

function updateStarDisplay() {
    highlightStars(currentRating);
}

function resetRating() {
    currentRating = 0;
    updateStarDisplay();
}

// Modal Management
function initializeModals() {
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close');
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            closeModal(modal);
        });
    });
    
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal(modal);
            }
        });
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal[style*="block"]');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });
}

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modal) {
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function showSuccessModal(message) {
    const modal = document.getElementById('successModal');
    if (modal) {
        const messageElement = modal.querySelector('.modal-body');
        if (messageElement) {
            messageElement.textContent = message;
        }
        showModal('successModal');
    } else {
        // Fallback to alert if modal doesn't exist
        alert(message);
    }
}

// Alert System
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer');
    
    if (!alertContainer) {
        // Create alert container if it doesn't exist
        const container = document.createElement('div');
        container.id = 'alertContainer';
        container.style.position = 'fixed';
        container.style.top = '20px';
        container.style.right = '20px';
        container.style.zIndex = '3000';
        container.style.maxWidth = '400px';
        document.body.appendChild(container);
    }
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} show`;
    alert.textContent = message;
    
    const container = document.getElementById('alertContainer');
    container.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Loading Button States
function showLoadingButton(button) {
    if (button) {
        button.disabled = true;
        const originalText = button.textContent;
        button.setAttribute('data-original-text', originalText);
        button.innerHTML = '<span class="loading"></span> Processing...';
    }
}

function hideLoadingButton(button) {
    if (button) {
        button.disabled = false;
        const originalText = button.getAttribute('data-original-text');
        button.textContent = originalText || 'Submit';
        button.removeAttribute('data-original-text');
    }
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}