<?php
/**
 * Template Name: Sign Up
 */

// Initialize session if not already started
if (!session_id()) {
    session_start();
}

// Initialize errors array
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data (using WordPress functions when available)
    $first_name = trim(isset($_POST['first_name']) ? (function_exists('sanitize_text_field') ? sanitize_text_field($_POST['first_name']) : htmlspecialchars($_POST['first_name'])) : '');
    $last_name = trim(isset($_POST['last_name']) ? (function_exists('sanitize_text_field') ? sanitize_text_field($_POST['last_name']) : htmlspecialchars($_POST['last_name'])) : '');
    $dob = isset($_POST['dob']) ? (function_exists('sanitize_text_field') ? sanitize_text_field($_POST['dob']) : htmlspecialchars($_POST['dob'])) : '';
    $username = trim(isset($_POST['username']) ? (function_exists('sanitize_user') ? sanitize_user($_POST['username']) : htmlspecialchars($_POST['username'])) : '');
    $email = isset($_POST['email']) ? (function_exists('sanitize_email') ? sanitize_email($_POST['email']) : htmlspecialchars($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? (function_exists('sanitize_text_field') ? sanitize_text_field($_POST['phone']) : htmlspecialchars($_POST['phone'])) : '';
    $account_type = isset($_POST['account_type']) ? (function_exists('sanitize_text_field') ? sanitize_text_field($_POST['account_type']) : htmlspecialchars($_POST['account_type'])) : '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $terms = isset($_POST['terms']);

    // Basic validations
    if (!$first_name) $errors[] = "First name is required.";
    if (!$last_name) $errors[] = "Last name is required.";
    
    if ($dob) {
        $dobDate = new DateTime($dob);
        $today = new DateTime();
        if ($today->diff($dobDate)->y < 18) $errors[] = "You must be at least 18 years old.";
    } else {
        $errors[] = "Date of birth is required.";
    }
    
    if (!$username) $errors[] = "Username is required.";
    
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!$phone) $errors[] = "Phone number is required.";
    if (!$account_type || !in_array($account_type, ['talent', 'hiring'])) $errors[] = "Please select an account type.";

    // Password validation
    if (!$password) {
        $errors[] = "Password is required.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
        $errors[] = "Password must be at least 6 characters long and include uppercase, lowercase, a number, and a special character.";
    }

    if ($password !== $password_confirm) $errors[] = "Passwords do not match.";
    if (!$terms) $errors[] = "You must accept the terms.";

    // Check existing WordPress users (only if no other errors)
    if (empty($errors)) {
        // Check if functions exist (to avoid errors during linting)
        $user_exists = false;
        if (function_exists('username_exists') && function_exists('email_exists')) {
            $user_exists = username_exists($username) || email_exists($email);
        }
        
        if ($user_exists) {
            $errors[] = "Username or email already exists.";
        } else {
            // Generate OTP
            $otp = random_int(100000, 999999);
            
            // Store data in session
            $_SESSION['pending_signup'] = serialize([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'dob' => $dob,
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'account_type' => $account_type,
                'password' => $password,
            ]);
            $_SESSION['email_to_verify'] = $email;
            $_SESSION['email_otp'] = (string)$otp;
            $_SESSION['otp_expiry'] = time() + 300; // 5 minutes expiry
            
            // Send verification email
            $subject = 'Email Verification Code';
            $message = "Your verification code is: $otp\nThis code will expire in 5 minutes.";
            
            $headers = array(
                'Content-Type: text/plain; charset=UTF-8',
                'From: The VelvetReel <rakibislamrifat9@gmail.com>'
            );
            
            // Send the email (check if function exists)
            $mail_sent = false;
            if (function_exists('wp_mail')) {
                $mail_sent = wp_mail($email, $subject, $message, $headers);
            } else {
                // Fallback for linter
                $mail_sent = mail($email, $subject, $message, implode("\r\n", $headers));
            }
            
            if ($mail_sent) {
                // Redirect to verification page (check if functions exist)
                if (function_exists('wp_redirect') && function_exists('home_url')) {
                    wp_redirect(home_url('/verify-email'));
                    exit;
                } else {
                    // Fallback for linter
                    header('Location: /verify-email');
                    exit;
                }
            } else {
                $errors[] = "Failed to send verification email.";
            }
        }
    }
}

// Load WordPress header
if (function_exists('get_header')) {
    get_header();
} else {
    // Minimal header fallback for linter
    echo '<!DOCTYPE html><html><head><title>Sign Up</title></head><body>';
}
?>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.chaitu-body {
    background: linear-gradient(0deg, #6b0f0f, #2d2d2d);
    background-size: 400% 400%;
    animation: rotateGradient 10s linear infinite;
    min-height: 100vh;
    color: white;
    padding: 20px;
    margin-top: -102px;
    padding-top: 130px;
}

@keyframes rotateGradient {
    0% {
        background: linear-gradient(0deg, #6b0f0f, #2d2d2d);
    }
    25% {
        background: linear-gradient(90deg, #6b0f0f, #2d2d2d);
    }
    50% {
        background: linear-gradient(180deg, #6b0f0f, #2d2d2d);
    }
    75% {
        background: linear-gradient(270deg, #6b0f0f, #2d2d2d);
    }
    100% {
        background: linear-gradient(360deg, #6b0f0f, #2d2d2d);
    }
}

.chaitu-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 40px;
    background: #0a0a0a;
    border-radius: 20px;
    border: 1px solid #1a1a1a;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
}

.chaitu-header {
    text-align: center;
    margin-bottom: 40px;
}

.chaitu-links {
    text-align: center;
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid #222;
}

.chaitu-signin-link {
    color: #fff;
    margin-top: 15px;
    font-weight: 500;
    text-decoration: none;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    position: relative;
    display: inline-block;
}

.chaitu-signin-text {
    color: #cccccc;
}

.chaitu-signin-link:hover {
    color: #dc3545;
}

.chaitu-signin-link:after {
    content: '';
    position: absolute;
    width: 0;
    height: 1px;
    bottom: -2px;
    left: 50%;
    background-color: #dc3545;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.chaitu-signin-link:hover:after {
    width: 100%;
}

.chaitu-input select,
select.chaitu-input {
    color: #ffffff;
    border: 1px solid #222;
    border-radius: 12px;
    padding: 15px 18px;
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: #0a0a0a;
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 12px;
    cursor: pointer;
}

select.chaitu-input:focus {
    outline: none;
    border-color: #b2122d;
    box-shadow: 0 0 0 3px rgba(178, 18, 45, 0.1);
    transform: translateY(-1px);
    background: #111;
}

.chaitu-input.chaitu-select {
    width: 100%;
    box-sizing: border-box;
}

.chaitu-brand img {
    width: 250px;
}

.chaitu-subtitle {
    font-size: 1.1rem;
    color: #cccccc;
    font-weight: 300;
    margin-bottom: 10px;
    margin-top: 15px;
}

.chaitu-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #ffffff;
    margin-top: 20px;
}

.chaitu-error-messages {
    background: rgba(178, 18, 45, 0.15);
    border: 1px solid rgba(178, 18, 45, 0.3);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
}

.chaitu-error-messages ul {
    list-style: none;
}

.chaitu-error-messages li {
    color: #fff;
    margin-bottom: 8px;
    font-size: 0.95rem;
    padding-left: 20px;
    position: relative;
}

.chaitu-error-messages li:before {
    content: "⚠";
    position: absolute;
    left: 0;
    color: #dc3545;
}

.chaitu-form {
    display: grid;
    gap: 25px;
}

.chaitu-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.chaitu-form-group {
    display: flex;
    flex-direction: column;
}

.chaitu-label {
    font-size: 0.95rem;
    font-weight: 500;
    color: #e0e0e0;
    margin-bottom: 8px;
    letter-spacing: 0.3px;
}
    
.star{
    color:red;
}

.chaitu-input {
    background: #0a0a0a;
    border: 1px solid #222;
    border-radius: 12px;
    padding: 15px 18px;
    font-size: 1rem;
    color: #ffffff;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.chaitu-input::placeholder {
    color: #555;
}

.chaitu-input:focus {
    outline: none;
    border-color: #b2122d;
    background: #111;
    box-shadow: 0 0 0 3px rgba(178, 18, 45, 0.1);
    transform: translateY(-1px);
}

.chaitu-input:hover {
    border-color: #333;
    background: #0f0f0f;
}

/* Password field with eye icon */
.chaitu-password-container {
    position: relative;
    width: 100%;
}

.chaitu-password-container .chaitu-input {
    padding-right: 50px;
}

.chaitu-toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chaitu-toggle-password svg {
    width: 20px;
    height: 20px;
    fill: #555;
    transition: fill 0.3s ease;
}

.chaitu-toggle-password:hover svg {
    fill: #fff;
}

.chaitu-checkbox-group {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-top: 10px;
}

.chaitu-checkbox {
    width: 20px;
    height: 20px;
    accent-color: #dc3545;
    cursor: pointer;
    margin-top: 2px;
}

.chaitu-checkbox-label {
    color: #cccccc;
    font-size: 0.95rem;
    line-height: 1.5;
    cursor: pointer;
    flex: 1;
}

.chaitu-checkbox-label a {
    color: #dc3545;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.chaitu-checkbox-label a:hover {
    color: #ff4757;
    text-decoration: underline;
}

.chaitu-submit-btn {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 18px 40px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
    font-family: 'Inter', sans-serif;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    position: relative;
    overflow: hidden;
}

.chaitu-submit-btn:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.chaitu-submit-btn:hover:before {
    left: 100%;
}

.chaitu-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
}

.mynew .chaitu-submit-btn:active {
    transform: translateY(0);
}

/* Responsive Design */
@media (max-width: 768px) {
    .chaitu-container {
        margin: 10px;
        padding: 30px 25px;
    }

    .chaitu-form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .chaitu-input {
        padding: 12px 15px;
    }

    .chaitu-submit-btn {
        padding: 15px 30px;
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .chaitu-container {
        margin: 5px;
        padding: 25px 20px;
    }

    .chaitu-title {
        font-size: 1.5rem;
    }
}

/* Animation for form elements */
.chaitu-form-group {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
    transform: translateY(20px);
}

.chaitu-form-group:nth-child(1) {
    animation-delay: 0.1s;
}

.chaitu-form-group:nth-child(2) {
    animation-delay: 0.2s;
}

.chaitu-form-group:nth-child(3) {
    animation-delay: 0.3s;
}

.chaitu-form-group:nth-child(4) {
    animation-delay: 0.4s;
}

.chaitu-form-group:nth-child(5) {
    animation-delay: 0.5s;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading state for button */
.chaitu-submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

/* Preloader Overlay */
.chaitu-preloader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.chaitu-preloader-overlay.active {
    display: flex;
    opacity: 1;
}

.chaitu-preloader-container {
    text-align: center;
}

.chaitu-preloader-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid rgba(255, 255, 255, 0.1);
    border-top: 4px solid #dc3545;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.chaitu-preloader-text {
    color: #ffffff;
    font-size: 1.1rem;
    font-weight: 500;
    letter-spacing: 0.5px;
}

.chaitu-preloader-subtext {
    color: #cccccc;
    font-size: 0.9rem;
    margin-top: 8px;
}

/* Button loading state */
.chaitu-submit-btn.loading {
    pointer-events: auto;
    position: relative;
    color: transparent;
}

.chaitu-submit-btn.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

/* Custom date picker styling */
.chaitu-input[type="date"] {
    color-scheme: dark;
}

.chaitu-input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}

/* Error indication for required fields */
.chaitu-input:invalid[required] {
    border-color: #e74c3c !important;
}

.chaitu-input:focus:invalid[required] {
    border-color: #e74c3c !important;
    box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.2) !important;
}
</style>

<div class="chaitu-body">
    <div class="chaitu-container">
        <div class="chaitu-header">
           
            <h2 class="chaitu-title">Create Your Account</h2>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="chaitu-error-messages">
            <ul>
                <?php foreach ($errors as $err): ?>
                <li><?php echo function_exists('esc_html') ? esc_html($err) : htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo function_exists('home_url') ? esc_url(home_url('/sign-up')) : '/sign-up'; ?>" novalidate class="chaitu-form">
            <div class="chaitu-form-row">
                <div class="chaitu-form-group">
                    <label class="chaitu-label">First Name <span class="star">*</span></label>
                    <input type="text" name="first_name" class="chaitu-input"
                        value="<?php echo function_exists('esc_attr') ? esc_attr($_POST['first_name'] ?? '') : htmlspecialchars($_POST['first_name'] ?? ''); ?>" placeholder="Enter your first name"
                        required />
                </div>

                <div class="chaitu-form-group">
                    <label class="chaitu-label">Last Name <span class="star">*</span></label>
                    <input type="text" name="last_name" class="chaitu-input"
                        value="<?php echo function_exists('esc_attr') ? esc_attr($_POST['last_name'] ?? '') : htmlspecialchars($_POST['last_name'] ?? ''); ?>" placeholder="Enter your last name"
                        required />
                </div>
            </div>

            <div class="chaitu-form-group">
                <label class="chaitu-label">Date of Birth <span class="star">*</span></label>
                <input type="date" name="dob" class="chaitu-input" value="<?php echo function_exists('esc_attr') ? esc_attr($_POST['dob'] ?? '') : htmlspecialchars($_POST['dob'] ?? ''); ?>"
                    required />
            </div>

            <div class="chaitu-form-group">
                <label class="chaitu-label">Username <span class="star">*</span></label>
                <input type="text" name="username" class="chaitu-input"
                    value="<?php echo function_exists('esc_attr') ? esc_attr($_POST['username'] ?? '') : htmlspecialchars($_POST['username'] ?? ''); ?>" placeholder="Choose a unique username"
                    required />
            </div>

            <div class="chaitu-form-group">
                <label class="chaitu-label">Account Type <span class="star">*</span></label>
                <select name="account_type" class="chaitu-input" required>
                    <option value="">Select account type</option>
                    <option value="talent" <?php echo (($_POST['account_type'] ?? '') === 'talent') ? 'selected' : ''; ?>>Talent</option>
                    <option value="hiring" <?php echo (($_POST['account_type'] ?? '') === 'hiring') ? 'selected' : ''; ?>>Hiring</option>
                </select>
            </div>

            <div class="chaitu-form-row">
                <div class="chaitu-form-group">
                    <label class="chaitu-label">Email Address <span class="star">*</span></label>
                    <input type="email" name="email" class="chaitu-input"
                        value="<?php echo function_exists('esc_attr') ? esc_attr($_POST['email'] ?? '') : htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="your.email@example.com"
                        required />
                </div>

                <div class="chaitu-form-group">
                    <label class="chaitu-label">Phone Number <span class="star">*</span></label>
                    <input type="tel" name="phone" class="chaitu-input"
                        value="<?php echo function_exists('esc_attr') ? esc_attr($_POST['phone'] ?? '') : htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="+1 (555) 123-4567"
                        required />
                </div>
            </div>

            <div class="chaitu-form-row">
                <div class="chaitu-form-group">
                    <label class="chaitu-label">Password <span class="star">*</span></label>
                    <div class="chaitu-password-container">
                        <input type="password" name="password" class="chaitu-input" placeholder="Create a strong password"
                            required />
                        <button type="button" class="chaitu-toggle-password" onclick="togglePasswordVisibility(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="chaitu-form-group">
                    <label class="chaitu-label">Confirm Password <span class="star">*</span></label>
                    <div class="chaitu-password-container">
                        <input type="password" name="password_confirm" class="chaitu-input"
                            placeholder="Confirm your password" required />
                        <button type="button" class="chaitu-toggle-password" onclick="togglePasswordVisibility(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="chaitu-checkbox-group">
                <input type="checkbox" name="terms" id="terms" class="chaitu-checkbox"
                    <?php echo isset($_POST['terms']) ? 'checked' : ''; ?> />
                <label for="terms" class="chaitu-checkbox-label">
                    I accept the <a href="/terms-and-conditions" target="_blank">Terms and Conditions</a>
                    and acknowledge that I have read the <a href="/privacy-policy/" target="_blank">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" class="chaitu-submit-btn">Create Account</button>
        </form>

        <div class="chaitu-links">
            <div class="chaitu-signin-section">
                <p class="chaitu-signin-text">Already registered? <b><a href="<?php echo function_exists('home_url') ? home_url('/sign-in/') : '/sign-in/'; ?>" class="chaitu-signin-link">Sign In Here</a></b></p>
                
            </div>
        </div>
    </div>
</div>

<!-- Preloader Overlay -->
<div class="chaitu-preloader-overlay" id="preloaderOverlay">
    <div class="chaitu-preloader-container">
        <div class="chaitu-preloader-spinner"></div>
        <div class="chaitu-preloader-text">Creating Your Account</div>
        <div class="chaitu-preloader-subtext">Please wait while we process your registration...</div>
    </div>
</div>

<script>
// Function to toggle password visibility
function togglePasswordVisibility(button) {
    const passwordInput = button.previousElementSibling;
    const eyeIcon = button.querySelector('svg');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        // Change icon to eye with slash
        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><path d="M0 0h24v24H0z" fill="none"/><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" opacity=".3"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/><path d="M12 9c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
    } else {
        passwordInput.type = 'password';
        // Change icon back to eye
        eyeIcon.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
    }
}

// Preloader functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.chaitu-form');
    const preloaderOverlay = document.getElementById('preloaderOverlay');
    const submitBtn = document.querySelector('.chaitu-submit-btn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Check all required fields in the form (including dynamically shown ones)
            const requiredFields = form.querySelectorAll('[required]:not([disabled])');
            let allFilled = true;
            let firstMissingField = null;
            let phoneValid = true;
            
            for (let field of requiredFields) {
                // Skip hidden fields that aren't visible to the user
                if (field.offsetParent === null) {
                    continue;
                }
                
                if (!field.value || field.value.trim() === '') {
                    allFilled = false;
                    if (!firstMissingField) {
                        firstMissingField = field;
                    }
                    // Add error indication to the field
                    field.style.borderColor = '#e74c3c';
                } else {
                    // If this is a phone field, validate its format
                    if (field.name === 'phone' && field.value) {
                        // Simple phone validation - check if it has at least 7 digits
                        const phoneRegex = /[\d\-\+\(\)\s]{7,}/;
                        if (!phoneRegex.test(field.value)) {
                            phoneValid = false;
                            field.style.borderColor = '#e74c3c';
                            if (!firstMissingField) {
                                firstMissingField = field;
                            }
                        } else {
                            // Remove error indication if field is valid
                            field.style.borderColor = '';
                        }
                    } else {
                        // Remove error indication if field is filled
                        field.style.borderColor = '';
                    }
                }
            }
            
            // Check if terms checkbox is required and checked
            const termsCheckbox = form.querySelector('#terms');
            if (termsCheckbox && termsCheckbox.required && !termsCheckbox.checked) {
                allFilled = false;
                termsCheckbox.style.borderColor = '#e74c3c';
            } else if (termsCheckbox) {
                termsCheckbox.style.borderColor = '';
            }
            
            if (!allFilled || !phoneValid) {
                e.preventDefault(); // Stop form submission
                
                // Focus on the first missing or invalid field
                if (firstMissingField) {
                    firstMissingField.focus();
                    
                    // Scroll to the field if needed
                    firstMissingField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Show appropriate error message
                if (!phoneValid) {
                    if (typeof showNotification === 'function') {
                        showNotification('Please enter a valid phone number.', 'error');
                    } else {
                        alert('Please enter a valid phone number.');
                    }
                } else {
                    if (typeof showNotification === 'function') {
                        showNotification('Please fill in all required fields.', 'error');
                    } else {
                        alert('Please fill in all required fields.');
                    }
                }
                
                // Don't show preloader or disable button since form didn't submit
                return false;
            }
            
            // Check if form is already submitting to prevent double submission
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return false;
            }
            
            // Show the preloader
            if (preloaderOverlay) {
                preloaderOverlay.classList.add('active');
            }
            
            // Add loading state to button (visual feedback)
            submitBtn.classList.add('loading');
            submitBtn.textContent = '';
            
            // Mark form as submitting
            form.dataset.submitting = 'true';
            
            // The form will continue to submit normally
            // Preloader will stay visible until page redirects
            // Button state will be reset on page load if there are server errors
        });
        
        // Add event listener for account type changes to handle conditional fields
        const accountTypeSelect = form.querySelector('#account_type');
        if (accountTypeSelect) {
            accountTypeSelect.addEventListener('change', function() {
                // Small delay to allow any conditional fields to render
                setTimeout(() => {
                    // Re-validate the form when account type changes
                    const requiredFields = form.querySelectorAll('[required]:not([disabled])');
                    let allFilled = true;
                    
                    for (let field of requiredFields) {
                        // Skip hidden fields that aren't visible to the user
                        if (field.offsetParent === null) {
                            continue;
                        }
                        
                        if (!field.value || field.value.trim() === '') {
                            allFilled = false;
                            field.style.borderColor = '#e74c3c';
                        } else {
                            // Remove error indication if field is filled
                            field.style.borderColor = '';
                        }
                    }
                    
                    // Re-enable submit button if there were validation issues
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('loading');
                        
                        // Restore original text
                        submitBtn.textContent = 'Create Account';
                        
                        // Hide preloader if it exists
                        if (preloaderOverlay) {
                            preloaderOverlay.classList.remove('active');
                        }
                    }
                }, 300); // 300ms delay to allow conditional fields to appear
            });
        }
        
        // Check if the page loaded with server-side validation errors
        // This happens when the form is submitted but has errors returned from the server
        const errorMessages = document.querySelector('.chaitu-error-messages');
        if (errorMessages && errorMessages.children.length > 0) {
            // Re-enable the button if there are server-side errors
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.textContent = 'Create Account';
            }
            if (preloaderOverlay) {
                preloaderOverlay.classList.remove('active');
            }
            // Reset submitting flag
            if (form.dataset.submitting) {
                form.dataset.submitting = 'false';
            }
        }

        // Also reset button on page load as a safety net
        window.addEventListener('load', function() {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.textContent = 'Create Account';
            }
            if (preloaderOverlay) {
                preloaderOverlay.classList.remove('active');
            }
            if (form.dataset.submitting) {
                form.dataset.submitting = 'false';
            }
        });
    }
    
    // Removed duplicate event listener that was causing the registration button issue
});
</script>

<?php 
// Load WordPress footer
if (function_exists('get_footer')) {
    get_footer();
} else {
    // Minimal footer fallback for linter
    echo '</body></html>';
}
?>