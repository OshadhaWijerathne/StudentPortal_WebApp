// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeTheme();
    initializeNavigation();
    initializeForms();
    initializeModals();
    initializePasswordToggle();
    initializeRating(); 
});

// --- THEME MANAGEMENT ---
function initializeTheme() {
    const themeToggle = document.getElementById('themeToggle');
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);
    if (themeToggle) {
        themeToggle.textContent = currentTheme === 'light' ? '🌙' : '☀️';
        themeToggle.addEventListener('click', toggleTheme);
    }
}

function toggleTheme() {
    let currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    document.getElementById('themeToggle').textContent = newTheme === 'light' ? '🌙' : '☀️';
}

// --- NAVIGATION ---
function initializeNavigation() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }
}
function initializeModals() {
}
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'block';
}
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'none';
}

// --- ADDED: VALIDATION HELPER FUNCTIONS (From Old Script) ---
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

function showFieldError(field, message) {
    const formGroup = field.closest('.form-group');
    if (!formGroup) return;
    const errorElement = formGroup.querySelector('.error-message'); 
    formGroup.classList.add('error');
    formGroup.classList.remove('success');
    if (errorElement) errorElement.textContent = message;
}

function showFieldSuccess(field) {
    const formGroup = field.closest('.form-group');
     if (!formGroup) return;
    formGroup.classList.add('success');
    formGroup.classList.remove('error');
}

function clearFieldError(field) {
    const formGroup = field.closest('.form-group');
     if (!formGroup) return;
    const errorElement = formGroup.querySelector('.error-message');
    formGroup.classList.remove('error');
    if (errorElement) errorElement.textContent = '';
}

// --- ADDED: CLIENT-SIDE VALIDATION LOGIC ---
function validateFormClientSide(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input, textarea, select');
    
    // Clear all previous errors before validating again
    inputs.forEach(input => clearFieldError(input));

    for (const input of inputs) {
        const value = input.value.trim();
        let fieldIsValid = true;

        if (input.required && !validateRequired(value)) {
            showFieldError(input, `${input.name.charAt(0).toUpperCase() + input.name.slice(1)} is required.`);
            fieldIsValid = false;
        } else if (input.type === 'email' && !validateEmail(value)) {
            showFieldError(input, 'Please enter a valid email address.');
            fieldIsValid = false;
        } else if (input.name === 'password' && !validatePassword(value)) {
            showFieldError(input, 'Password must be at least 8 characters long.');
            fieldIsValid = false;
        } else if (input.name === 'confirmPassword') {
            const passwordField = form.querySelector('[name="password"]');
            if (value !== passwordField.value) {
                showFieldError(input, 'Passwords do not match.');
                fieldIsValid = false;
            }
        }
        
        if (!fieldIsValid) {
            isValid = false;
        } else if (value) { // Show success only if there's a value and it's valid
            showFieldSuccess(input);
        }
    }
    return isValid;
}


// --- FORM HANDLING & VALIDATION (MODIFIED) ---
function initializeForms() {
    const formsToHandle = ['registerForm', 'loginForm', 'contactForm', 'changePasswordForm'];
    formsToHandle.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', handleFormSubmit);

            // --- ADDED: REAL-TIME VALIDATION ---
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('blur', () => validateFormClientSide(form)); // Validate on blur
                input.addEventListener('input', () => clearFieldError(input)); // Clear error on typing
            });
        }
    });
}

// --- MODIFIED handleFormSubmit ---
async function handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;

    // --- ADDED: Client-side validation check ---
    const isFormValid = validateFormClientSide(form);
    // if (!isFormValid) {
    //     showAlert('Please fix the errors before submitting.');
    //     return; // Stop the function if validation fails
    // }
    // --- END OF ADDED CHECK ---

    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    
    submitButton.disabled = true;
    submitButton.innerHTML = 'Processing...';

    const formData = new FormData(form);
    const action = form.getAttribute('action');

    try {
        const response = await fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        if (response.ok) {
            showAlert(result.message);
            if (result.redirect) {
                window.location.href = result.redirect;
            } else {
                form.reset();
                // Clear all success states from fields
                form.querySelectorAll('.form-group').forEach(fg => fg.classList.remove('success'));
                if(form.id === 'contactForm') resetRating(); // Reset stars if it's the contact form
            }
        } else {
            const errorMessage = result.errors ? result.errors.join('\n') : 'An unknown error occurred.';
            showAlert(errorMessage);
        }
    } catch (error) {
        console.error('Submission Error:', error);
        showAlert('A network error occurred. Please try again.');
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
    }
}

// --- UTILITIES --- 
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

// --- ADDED: RATING SYSTEM (From Old Script) ---
let currentRating = 0;

function initializeRating() {
    const stars = document.querySelectorAll('.star');
    if (stars.length === 0) return;
    
    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            currentRating = index + 1;
            document.getElementById('rating').value = currentRating; 
            updateStarDisplay();
        });
        star.addEventListener('mouseenter', () => highlightStars(index + 1));
    });

    document.querySelector('.rating-container').addEventListener('mouseleave', () => updateStarDisplay());
}

function highlightStars(rating) {
    document.querySelectorAll('.star').forEach((star, index) => {
        star.classList.toggle('active', index < rating);
    });
}

function updateStarDisplay() {
    highlightStars(currentRating);
}

function resetRating() {
    currentRating = 0;
    if(document.getElementById('rating')) document.getElementById('rating').value = 0;
    updateStarDisplay();
}