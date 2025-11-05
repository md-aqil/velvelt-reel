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
    $address = isset($_POST['address']) ? (function_exists('sanitize_text_field') ? sanitize_text_field($_POST['address']) : htmlspecialchars($_POST['address'])) : '';
    $region = isset($_POST['region']) ? (function_exists('sanitize_text_field') ? sanitize_text_field($_POST['region']) : htmlspecialchars($_POST['region'])) : '';
    $street_address = isset($_POST['street_address']) ? (function_exists('sanitize_text_field') ? sanitize_text_field($_POST['street_address']) : htmlspecialchars($_POST['street_address'])) : '';
    $zip_code = isset($_POST['zip_code']) ? (function_exists('sanitize_text_field') ? sanitize_text_field($_POST['zip_code']) : htmlspecialchars($_POST['zip_code'])) : '';
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
    if (!$address) $errors[] = "Address is required.";
    if (!$region) $errors[] = "Region is required.";
    if (!$street_address) $errors[] = "Street address is required.";
    if (!$zip_code) $errors[] = "ZIP code is required.";

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
                'address' => $address,
                'region' => $region,
                'street_address' => $street_address,
                'zip_code' => $zip_code,
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
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.chaitu-header {
    text-align: center;
    margin-bottom: 40px;
}

.chaitu-links {
    text-align: center;
    margin-top: 25px;
    padding-top: 25px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
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
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    padding: 15px 18px;
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 12px;
    cursor: pointer;
}

select.chaitu-input:focus {
    outline: none;
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    transform: translateY(-1px);
    background: #2A2A2A;
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
    background: rgba(220, 53, 69, 0.15);
    border: 1px solid rgba(220, 53, 69, 0.3);
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    backdrop-filter: blur(10px);
}

.chaitu-error-messages ul {
    list-style: none;
}

.chaitu-error-messages li {
    color: #ff6b7a;
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
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    padding: 15px 18px;
    font-size: 1rem;
    color: #ffffff;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.chaitu-input::placeholder {
    color: #999999;
}

.chaitu-input:focus {
    outline: none;
    border-color: #dc3545;
    background: rgba(255, 255, 255, 0.12);
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    transform: translateY(-1px);
}

.chaitu-input:hover {
    border-color: rgba(255, 255, 255, 0.25);
    background: rgba(255, 255, 255, 0.1);
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

/* Custom date picker styling */
.chaitu-input[type="date"] {
    color-scheme: dark;
}

.chaitu-input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
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
                    <label class="chaitu-label">Address <span class="star">*</span></label>
                    <input type="text" name="address" class="chaitu-input"
                        value="<?php echo function_exists('esc_attr') ? esc_attr($_POST['address'] ?? '') : htmlspecialchars($_POST['address'] ?? ''); ?>" placeholder="Enter your full address"
                        required />
                </div>

                <div class="chaitu-form-group">
                    <label class="chaitu-label">Region <span class="star">*</span></label>
                    <select name="region" id="region-select" class="chaitu-input chaitu-select" required>
                        <option value="">Select your country</option>
                    </select>
                </div>
            </div>

            <div class="chaitu-form-row">
                <div class="chaitu-form-group">
                    <label class="chaitu-label">Street Address <span class="star">*</span></label>
                    <input type="text" name="street_address" class="chaitu-input"
                        value="<?php echo function_exists('esc_attr') ? esc_attr($_POST['street_address'] ?? '') : htmlspecialchars($_POST['street_address'] ?? ''); ?>"
                        placeholder="Enter your street address" required />
                </div>

                <div class="chaitu-form-group">
                    <label class="chaitu-label">ZIP Code <span class="star">*</span></label>
                    <input type="text" name="zip_code" class="chaitu-input"
                        value="<?php echo function_exists('esc_attr') ? esc_attr($_POST['zip_code'] ?? '') : htmlspecialchars($_POST['zip_code'] ?? ''); ?>" placeholder="Enter ZIP or postal code"
                        required />
                </div>
            </div>

            <div class="chaitu-form-row">
                <div class="chaitu-form-group">
                    <label class="chaitu-label">Password <span class="star">*</span></label>
                    <input type="password" name="password" class="chaitu-input" placeholder="Create a strong password"
                        required />
                </div>

                <div class="chaitu-form-group">
                    <label class="chaitu-label">Confirm Password <span class="star">*</span></label>
                    <input type="password" name="password_confirm" class="chaitu-input"
                        placeholder="Confirm your password" required />
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('region-select');
    const countries = [
        "India",
        "United States",
        "United Kingdom",
        // European countries
        "Albania", "Andorra", "Armenia", "Austria", "Azerbaijan", "Belarus", "Belgium", "Bosnia and Herzegovina",
        "Bulgaria", "Croatia", "Cyprus", "Czech Republic", "Denmark", "Estonia", "Finland", "France", "Georgia",
        "Germany", "Greece", "Hungary", "Iceland", "Ireland", "Italy", "Kazakhstan", "Kosovo", "Latvia",
        "Liechtenstein",
        "Lithuania", "Luxembourg", "Malta", "Moldova", "Monaco", "Montenegro", "Netherlands", "North Macedonia",
        "Norway", "Poland", "Portugal", "Romania", "Russia", "San Marino", "Serbia", "Slovakia", "Slovenia",
        "Spain", "Sweden", "Switzerland", "Turkey", "Ukraine", "Vatican City"
    ];

    // Populate the select field
    countries.forEach(country => {
        const option = document.createElement('option');
        option.value = country;
        option.textContent = country;
        if ("<?php echo function_exists('esc_js') ? esc_js($_POST['region'] ?? '') : htmlspecialchars($_POST['region'] ?? ''); ?>" === country) {
            option.selected = true;
        }
        countrySelect.appendChild(option);
    });
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