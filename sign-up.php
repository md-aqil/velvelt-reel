<?php
/**
 * Template Name: Sign Up
 *
 * Upgraded VelvetReel Dark-Luxe Sign Up Experience
 *
 * @package HelloElementorChild
 */

// Initialize session if not already started
if (!session_id()) {
    session_start();
}

// Redirect logged in users
if (is_user_logged_in()) {
    wp_redirect(home_url('/membership-login/membership-profile/'));
    exit;
}

$errors = array();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name       = trim(isset($_POST['first_name']) ? sanitize_text_field(wp_unslash($_POST['first_name'])) : '');
    $last_name        = trim(isset($_POST['last_name']) ? sanitize_text_field(wp_unslash($_POST['last_name'])) : '');
    $dob              = isset($_POST['dob']) ? sanitize_text_field(wp_unslash($_POST['dob'])) : '';
    $username         = trim(isset($_POST['username']) ? sanitize_user(wp_unslash($_POST['username'])) : '');
    $email            = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $phone            = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $account_type     = isset($_POST['account_type']) ? sanitize_text_field(wp_unslash($_POST['account_type'])) : 'talent';
    $password         = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    $terms            = isset($_POST['terms']);

    // Validations
    if (!$first_name) {
        $errors[] = 'First name is required.';
    }
    if (!$last_name) {
        $errors[] = 'Last name is required.';
    }

    if ($dob) {
        try {
            $dobDate = new DateTime($dob);
            $today   = new DateTime();
            if ($today->diff($dobDate)->y < 18) {
                $errors[] = 'You must be at least 18 years old to join VelvetReel.';
            }
        } catch (Exception $e) {
            $errors[] = 'Please enter a valid date of birth.';
        }
    } else {
        $errors[] = 'Date of birth is required.';
    }

    if (!$username) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters long.';
    }

    if (!$email || !is_email($email)) {
        $errors[] = 'A valid email address is required.';
    }

    if (!$phone) {
        $errors[] = 'Phone number is required.';
    }

    if (!$account_type || !in_array($account_type, array('talent', 'hiring'), true)) {
        $errors[] = 'Please select an account type (Talent or Hiring).';
    }

    if (!$password) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$terms) {
        $errors[] = 'You must accept the Terms and Conditions and Privacy Policy.';
    }

    // Check existing WordPress users
    if (empty($errors)) {
        if (username_exists($username)) {
            $errors[] = 'This username is already taken. Please choose another one.';
        } elseif (email_exists($email)) {
            $errors[] = 'An account with this email address already exists. Please sign in instead.';
        } else {
            // Generate 6-digit OTP
            $otp = random_int(100000, 999999);

            // Store pending data in session
            $_SESSION['pending_signup'] = serialize(array(
                'first_name'   => $first_name,
                'last_name'    => $last_name,
                'dob'          => $dob,
                'username'     => $username,
                'email'        => $email,
                'phone'        => $phone,
                'account_type' => $account_type,
                'password'     => $password,
            ));

            $_SESSION['email_to_verify']       = $email;
            $_SESSION['email_otp']             = (string) $otp;
            $_SESSION['otp_expiry']            = time() + 600; // 10 minutes expiry
            $_SESSION['last_otp_sent_time']    = time();
            $_SESSION['otp_failed_attempts']   = 0;

            // Send branded OTP verification email
            $mail_sent = false;
            if (function_exists('velvet_send_otp_email')) {
                $mail_sent = velvet_send_otp_email($email, $otp, 'signup', $first_name, 10);
            } else {
                $subject = 'Your VelvetReel Verification Code: ' . $otp;
                $message = "Your verification code is: $otp\nThis code will expire in 10 minutes.";
                $headers = array('Content-Type: text/plain; charset=UTF-8');
                $mail_sent = wp_mail($email, $subject, $message, $headers);
            }

            if ($mail_sent) {
                wp_redirect(home_url('/verify-email/'));
                exit;
            } else {
                // If mail sending failed, try direct redirect or show notice
                wp_redirect(home_url('/verify-email/'));
                exit;
            }
        }
    }
}

get_header();
?>

<style>
/* VelvetReel Dark Luxury Aesthetic */
.velvet-auth-section {
    position: relative;
    min-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    background: #08080a;
    background-image: 
        radial-gradient(circle at 50% -10%, rgba(254, 17, 75, 0.15) 0%, transparent 65%),
        radial-gradient(circle at 90% 80%, rgba(254, 17, 75, 0.06) 0%, transparent 45%),
        radial-gradient(circle at 10% 90%, rgba(20, 20, 26, 0.8) 0%, transparent 50%);
}

.velvet-auth-container {
    max-width: 660px;
    width: 100%;
    background: rgba(18, 18, 22, 0.85);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 24px;
    padding: 45px 40px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.6), 0 0 1px 1px rgba(255, 255, 255, 0.05);
    position: relative;
    overflow: hidden;
}

.velvet-auth-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #FE114B, #d80b3d, #ff5c7c);
}

.velvet-auth-header {
    text-align: center;
    margin-bottom: 35px;
}

.velvet-auth-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 14px;
    background: rgba(254, 17, 75, 0.12);
    border: 1px solid rgba(254, 17, 75, 0.3);
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    color: #FE114B;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 14px;
}

.velvet-auth-title {
    font-size: 28px;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 8px;
    letter-spacing: -0.5px;
}

.velvet-auth-subtitle {
    font-size: 14px;
    color: #a1a1aa;
    line-height: 1.5;
    margin: 0;
}

/* Account Type Radio Selector Cards */
.velvet-account-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 24px;
}

.velvet-account-option {
    position: relative;
}

.velvet-account-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.velvet-account-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 16px 14px;
    background: #0e0e12;
    border: 1.5px solid #22222a;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
}

.velvet-account-card .acc-icon {
    font-size: 24px;
    margin-bottom: 6px;
}

.velvet-account-card .acc-title {
    font-size: 14px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 2px;
}

.velvet-account-card .acc-desc {
    font-size: 11px;
    color: #71717a;
}

.velvet-account-option input[type="radio"]:checked + .velvet-account-card {
    border-color: #FE114B;
    background: rgba(254, 17, 75, 0.08);
    box-shadow: 0 0 20px rgba(254, 17, 75, 0.18);
}

.velvet-account-option input[type="radio"]:checked + .velvet-account-card .acc-title {
    color: #FE114B;
}

/* Form Styles */
.velvet-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.velvet-form-full {
    grid-column: 1 / -1;
}

.velvet-field-group {
    display: flex;
    flex-direction: column;
}

.velvet-field-label {
    font-size: 13px;
    font-weight: 600;
    color: #d4d4d8;
    margin-bottom: 6px;
    letter-spacing: 0.2px;
}

.velvet-field-label .req {
    color: #FE114B;
    margin-left: 2px;
}

.velvet-input-wrap {
    position: relative;
}

.velvet-text-input {
    width: 100%;
    padding: 13px 16px;
    background: #0d0d10;
    border: 1px solid #24242e;
    border-radius: 12px;
    color: #ffffff;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

.velvet-text-input:focus {
    outline: none;
    border-color: #FE114B;
    background: #121216;
    box-shadow: 0 0 0 3px rgba(254, 17, 75, 0.15);
}

.velvet-text-input::placeholder {
    color: #52525b;
}

.velvet-text-input[type="date"] {
    color-scheme: dark;
}

/* Password eye button */
.velvet-pwd-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #71717a;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s;
}

.velvet-pwd-toggle:hover {
    color: #ffffff;
}

/* Checkbox */
.velvet-checkbox-wrap {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin: 18px 0 24px;
}

.velvet-checkbox {
    width: 18px;
    height: 18px;
    accent-color: #FE114B;
    cursor: pointer;
    margin-top: 2px;
}

.velvet-checkbox-label {
    font-size: 13px;
    color: #a1a1aa;
    line-height: 1.5;
    cursor: pointer;
}

.velvet-checkbox-label a {
    color: #FE114B;
    text-decoration: none;
    font-weight: 600;
}

.velvet-checkbox-label a:hover {
    text-decoration: underline;
}

/* Submit Button */
.velvet-submit-btn {
    width: 100%;
    padding: 16px 28px;
    background: linear-gradient(135deg, #FE114B 0%, #d80b3d 100%);
    border: none;
    border-radius: 12px;
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 10px 25px rgba(254, 17, 75, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.velvet-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(254, 17, 75, 0.5);
    background: linear-gradient(135deg, #ff2358 0%, #e01244 100%);
}

.velvet-submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

/* Error Alerts */
.velvet-auth-errors {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.3);
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 24px;
    color: #f87171;
    font-size: 13px;
    line-height: 1.6;
}

.velvet-auth-errors ul {
    margin: 0;
    padding-left: 18px;
}

/* Sign In Switch */
.velvet-auth-switch {
    text-align: center;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    font-size: 13px;
    color: #71717a;
}

.velvet-auth-switch a {
    color: #ffffff;
    font-weight: 700;
    text-decoration: none;
    margin-left: 4px;
}

.velvet-auth-switch a:hover {
    color: #FE114B;
    text-decoration: underline;
}

@media (max-width: 600px) {
    .velvet-auth-container {
        padding: 30px 20px;
    }
    .velvet-form-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }
    .velvet-account-selector {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="velvet-auth-section">
    <div class="velvet-auth-container">
        <div class="velvet-auth-header">
            <div class="velvet-auth-badge">
                <span>✦ JOIN THE NETWORK ✦</span>
            </div>
            <h1 class="velvet-auth-title">Create Your Account</h1>
            <p class="velvet-auth-subtitle">Join the premier platform for creative talents and casting teams</p>
        </div>

        <?php if (!empty($errors)) : ?>
            <div class="velvet-auth-errors">
                <ul>
                    <?php foreach ($errors as $err) : ?>
                        <li><?php echo esc_html($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo esc_url(home_url('/sign-up/')); ?>" class="velvet-signup-form" id="velvetSignupForm">
            <!-- Account Type Choice -->
            <label class="velvet-field-label" style="margin-bottom: 10px; display: block;">I am joining as a: <span class="req">*</span></label>
            <div class="velvet-account-selector">
                <label class="velvet-account-option">
                    <input type="radio" name="account_type" value="talent" <?php echo (!isset($_POST['account_type']) || $_POST['account_type'] === 'talent') ? 'checked' : ''; ?>>
                    <div class="velvet-account-card">
                        <span class="acc-icon">🌟</span>
                        <span class="acc-title">Talent / Creator</span>
                        <span class="acc-desc">Actor, Model, Dancer, Stylist, Crew</span>
                    </div>
                </label>

                <label class="velvet-account-option">
                    <input type="radio" name="account_type" value="hiring" <?php echo (isset($_POST['account_type']) && $_POST['account_type'] === 'hiring') ? 'checked' : ''; ?>>
                    <div class="velvet-account-card">
                        <span class="acc-icon">🎬</span>
                        <span class="acc-title">Hiring / Advertiser</span>
                        <span class="acc-desc">Director, Agency, Brand, Producer</span>
                    </div>
                </label>
            </div>

            <div class="velvet-form-grid">
                <!-- First Name -->
                <div class="velvet-field-group">
                    <label class="velvet-field-label" for="first_name">First Name <span class="req">*</span></label>
                    <input type="text" id="first_name" name="first_name" class="velvet-text-input" placeholder="e.g. John" value="<?php echo esc_attr($_POST['first_name'] ?? ''); ?>" required>
                </div>

                <!-- Last Name -->
                <div class="velvet-field-group">
                    <label class="velvet-field-label" for="last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="last_name" name="last_name" class="velvet-text-input" placeholder="e.g. Doe" value="<?php echo esc_attr($_POST['last_name'] ?? ''); ?>" required>
                </div>

                <!-- Username -->
                <div class="velvet-field-group">
                    <label class="velvet-field-label" for="username">Username <span class="req">*</span></label>
                    <input type="text" id="username" name="username" class="velvet-text-input" placeholder="unique_handle" value="<?php echo esc_attr($_POST['username'] ?? ''); ?>" required>
                </div>

                <!-- Date of Birth -->
                <div class="velvet-field-group">
                    <label class="velvet-field-label" for="dob">Date of Birth <span class="req">*</span></label>
                    <input type="date" id="dob" name="dob" class="velvet-text-input" value="<?php echo esc_attr($_POST['dob'] ?? ''); ?>" required>
                </div>

                <!-- Email -->
                <div class="velvet-field-group">
                    <label class="velvet-field-label" for="email">Email Address <span class="req">*</span></label>
                    <input type="email" id="email" name="email" class="velvet-text-input" placeholder="you@domain.com" value="<?php echo esc_attr($_POST['email'] ?? ''); ?>" required>
                </div>

                <!-- Phone -->
                <div class="velvet-field-group">
                    <label class="velvet-field-label" for="phone">Phone Number <span class="req">*</span></label>
                    <input type="tel" id="phone" name="phone" class="velvet-text-input" placeholder="+1 (555) 000-0000" value="<?php echo esc_attr($_POST['phone'] ?? ''); ?>" required>
                </div>

                <!-- Password -->
                <div class="velvet-field-group">
                    <label class="velvet-field-label" for="password">Password <span class="req">*</span></label>
                    <div class="velvet-input-wrap">
                        <input type="password" id="password" name="password" class="velvet-text-input" placeholder="Min. 6 characters" required>
                        <button type="button" class="velvet-pwd-toggle" onclick="togglePwd(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="velvet-field-group">
                    <label class="velvet-field-label" for="password_confirm">Confirm Password <span class="req">*</span></label>
                    <div class="velvet-input-wrap">
                        <input type="password" id="password_confirm" name="password_confirm" class="velvet-text-input" placeholder="Re-enter password" required>
                        <button type="button" class="velvet-pwd-toggle" onclick="togglePwd(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="velvet-checkbox-wrap">
                <input type="checkbox" name="terms" id="terms" class="velvet-checkbox" <?php echo isset($_POST['terms']) ? 'checked' : ''; ?> required>
                <label for="terms" class="velvet-checkbox-label">
                    I accept the <a href="<?php echo esc_url(home_url('/terms-and-conditions/')); ?>" target="_blank">Terms and Conditions</a> and acknowledge the <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" target="_blank">Privacy Policy</a>.
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="velvet-submit-btn" id="signupSubmitBtn">
                <span>Continue & Verify Email</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
        </form>

        <div class="velvet-auth-switch">
            Already have an account? <a href="<?php echo esc_url(home_url('/sign-in/')); ?>">Sign In Here &rarr;</a>
        </div>
    </div>
</div>

<script>
function togglePwd(btn) {
    var input = btn.previousElementSibling;
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('velvetSignupForm');
    var btn = document.getElementById('signupSubmitBtn');
    if (form && btn) {
        form.addEventListener('submit', function() {
            btn.disabled = true;
            btn.innerHTML = '<span>Processing...</span>';
        });
    }
});
</script>

<?php get_footer(); ?>