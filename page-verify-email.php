<?php
/**
 * Template Name: Verify Email
 *
 * Handles 6-digit OTP verification, user account creation, auto-login,
 * welcome email triggering, and redirection after sign-up.
 *
 * @package HelloElementorChild
 */

// Initialize session if not already active
if (!session_id()) {
    session_start();
}

// Redirect logged in users
if (is_user_logged_in()) {
    wp_redirect(home_url('/membership-login/membership-profile/'));
    exit;
}

$errors  = array();
$success = '';
$email   = isset($_SESSION['email_to_verify']) ? $_SESSION['email_to_verify'] : '';
$pending = isset($_SESSION['pending_signup']) ? @unserialize($_SESSION['pending_signup']) : null;

// Handle Resend OTP Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'resend_otp') {
    if (!$email || !is_array($pending)) {
        $errors[] = 'Your registration session has expired. Please sign up again.';
    } else {
        $last_sent = isset($_SESSION['last_otp_sent_time']) ? (int) $_SESSION['last_otp_sent_time'] : 0;
        if (time() - $last_sent < 30) {
            $errors[] = 'Please wait ' . (30 - (time() - $last_sent)) . ' seconds before requesting another code.';
        } else {
            $otp = random_int(100000, 999999);
            $_SESSION['email_otp']           = (string) $otp;
            $_SESSION['otp_expiry']          = time() + 600; // 10 minutes
            $_SESSION['last_otp_sent_time']  = time();
            $_SESSION['otp_failed_attempts'] = 0;

            $recipient_name = !empty($pending['first_name']) ? $pending['first_name'] : '';
            if (function_exists('velvet_send_otp_email')) {
                velvet_send_otp_email($email, $otp, 'signup', $recipient_name, 10);
            } else {
                $subject = 'Your VelvetReel Verification Code: ' . $otp;
                $message = "Your verification code is: $otp\nThis code will expire in 10 minutes.";
                $headers = array('Content-Type: text/plain; charset=UTF-8');
                wp_mail($email, $subject, $message, $headers);
            }
            $success = 'A fresh 6-digit verification code has been delivered to ' . esc_html($email) . '.';
        }
    }
}

// Handle Cancel / Start Over Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_signup') {
    unset($_SESSION['pending_signup'], $_SESSION['email_to_verify'], $_SESSION['email_otp'], $_SESSION['otp_expiry'], $_SESSION['last_otp_sent_time'], $_SESSION['otp_failed_attempts']);
    wp_redirect(home_url('/sign-up/'));
    exit;
}

// Handle OTP Verification Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] === 'verify_otp')) {
    // Collect OTP from either individual inputs or single input
    $otp_input = '';
    if (isset($_POST['otp']) && !empty($_POST['otp'])) {
        $otp_input = trim(sanitize_text_field($_POST['otp']));
    } elseif (isset($_POST['otp_digits']) && is_array($_POST['otp_digits'])) {
        $otp_input = trim(implode('', array_map('sanitize_text_field', $_POST['otp_digits'])));
    }

    if (empty($otp_input)) {
        $errors[] = 'Please enter the 6-digit verification code.';
    } elseif (!$email || !is_array($pending)) {
        $errors[] = 'Your registration session has expired. Please fill out the sign up form again.';
    } else {
        $stored_otp = isset($_SESSION['email_otp']) ? (string) $_SESSION['email_otp'] : '';
        $expiry     = isset($_SESSION['otp_expiry']) ? (int) $_SESSION['otp_expiry'] : 0;
        $failed     = isset($_SESSION['otp_failed_attempts']) ? (int) $_SESSION['otp_failed_attempts'] : 0;

        if ($failed >= 5) {
            $errors[] = 'Too many incorrect attempts. Please click "Resend Code" to receive a new verification code.';
        } elseif (empty($stored_otp) || $expiry < time()) {
            $errors[] = 'The verification code has expired. Please click "Resend Code" to get a fresh code.';
        } elseif ($otp_input !== $stored_otp) {
            $_SESSION['otp_failed_attempts'] = $failed + 1;
            $remaining = 5 - ($failed + 1);
            $errors[] = 'Invalid verification code. Please check your email and try again.' . ($remaining > 0 ? " ($remaining attempts remaining)" : '');
        } else {
            // OTP is valid! Proceed with User Creation
            $username     = $pending['username'];
            $email        = $pending['email'];
            $password     = $pending['password'];
            $first_name   = $pending['first_name'];
            $last_name    = $pending['last_name'];
            $phone        = $pending['phone'];
            $dob          = $pending['dob'];
            $account_type = $pending['account_type'];

            // Double-check if user already exists
            if (username_exists($username) || email_exists($email)) {
                $errors[] = 'An account with this username or email already exists. Please sign in instead.';
            } else {
                $user_data = array(
                    'user_login'   => $username,
                    'user_email'   => $email,
                    'user_pass'    => $password,
                    'first_name'   => $first_name,
                    'last_name'    => $last_name,
                    'display_name' => trim($first_name . ' ' . $last_name),
                    'role'         => 'subscriber',
                );

                $user_id = wp_insert_user($user_data);

                if (is_wp_error($user_id)) {
                    $errors[] = 'Account creation failed: ' . $user_id->get_error_message();
                } else {
                    // Update user meta
                    update_user_meta($user_id, 'phone', $phone);
                    update_user_meta($user_id, 'dob', $dob);
                    update_user_meta($user_id, 'account_type', $account_type);
                    update_user_meta($user_id, '_velvet_registered_time', time());

                    // Default membership plan tracking
                    if ($account_type === 'talent') {
                        $default_plan = defined('FREE_PLAN_LEVEL') ? FREE_PLAN_LEVEL : 4;
                        update_user_meta($user_id, 'membership_plans', array($default_plan));
                    }

                    // Trigger welcome email
                    if (function_exists('velvet_send_welcome_email')) {
                        velvet_send_welcome_email($user_id);
                    }

                    // Clear session data
                    unset($_SESSION['pending_signup'], $_SESSION['email_to_verify'], $_SESSION['email_otp'], $_SESSION['otp_expiry'], $_SESSION['last_otp_sent_time'], $_SESSION['otp_failed_attempts']);

                    // Authenticate and log the user in
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id, true, is_ssl());

                    // Redirect based on account type
                    if ($account_type === 'hiring') {
                        wp_redirect(home_url('/submit-advertisement/?registered=1'));
                    } else {
                        wp_redirect(home_url('/talent/?registered=1'));
                    }
                    exit;
                }
            }
        }
    }
}

get_header();
?>

<style>
/* VelvetReel Premium Dark Luxe Auth Styles */
.velvet-verify-section {
    position: relative;
    min-height: 85vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    background: #08080a;
    background-image: 
        radial-gradient(circle at 50% 0%, rgba(254, 17, 75, 0.12) 0%, transparent 60%),
        radial-gradient(circle at 10% 90%, rgba(20, 20, 26, 0.8) 0%, transparent 50%);
}

.velvet-verify-card {
    max-width: 520px;
    width: 100%;
    background: rgba(18, 18, 22, 0.85);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 24px;
    padding: 45px 35px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.6), 0 0 1px 1px rgba(255, 255, 255, 0.05);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.velvet-verify-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #FE114B, #d80b3d, #ff5c7c);
}

.velvet-icon-badge {
    width: 64px;
    height: 64px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: rgba(254, 17, 75, 0.1);
    border: 1px solid rgba(254, 17, 75, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FE114B;
    box-shadow: 0 0 25px rgba(254, 17, 75, 0.2);
}

.velvet-verify-title {
    font-size: 26px;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 10px;
    letter-spacing: -0.5px;
}

.velvet-verify-subtitle {
    font-size: 14px;
    color: #a1a1aa;
    line-height: 1.6;
    margin: 0 0 30px;
}

.velvet-email-pill {
    display: inline-block;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 4px 14px;
    color: #ffffff;
    font-weight: 600;
    font-size: 13px;
    margin-top: 6px;
}

/* Alert notifications */
.velvet-alert {
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 25px;
    font-size: 14px;
    text-align: left;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.velvet-alert-error {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #f87171;
}

.velvet-alert-success {
    background: rgba(34, 197, 94, 0.12);
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: #4ade80;
}

/* 6-Digit OTP Group */
.velvet-otp-inputs {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 28px;
}

.velvet-otp-digit {
    width: 52px;
    height: 60px;
    background: #0d0d10;
    border: 1.5px solid #27272e;
    border-radius: 12px;
    color: #ffffff;
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    font-family: inherit;
    transition: all 0.2s ease;
}

.velvet-otp-digit:focus {
    outline: none;
    border-color: #FE114B;
    background: #131318;
    box-shadow: 0 0 0 3px rgba(254, 17, 75, 0.15);
    transform: translateY(-2px);
}

/* Submit Action Button */
.velvet-btn-submit {
    width: 100%;
    padding: 15px 24px;
    background: linear-gradient(135deg, #FE114B 0%, #d80b3d 100%);
    border: none;
    border-radius: 12px;
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 10px 25px rgba(254, 17, 75, 0.3);
}

.velvet-btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(254, 17, 75, 0.45);
    background: linear-gradient(135deg, #ff2358 0%, #e01244 100%);
}

.velvet-btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Resend Timer & Links */
.velvet-resend-row {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    display: flex;
    flex-direction: column;
    gap: 12px;
    font-size: 13px;
    color: #71717a;
}

.velvet-resend-btn {
    background: none;
    border: none;
    color: #FE114B;
    font-weight: 600;
    cursor: pointer;
    padding: 0;
    font-size: 13px;
    text-decoration: none;
    transition: color 0.2s;
}

.velvet-resend-btn:hover {
    color: #ff5c7c;
    text-decoration: underline;
}

.velvet-resend-btn:disabled {
    color: #71717a;
    cursor: not-allowed;
    text-decoration: none;
}

.velvet-cancel-btn {
    background: none;
    border: none;
    color: #a1a1aa;
    font-size: 12px;
    cursor: pointer;
    text-decoration: underline;
}

.velvet-cancel-btn:hover {
    color: #ffffff;
}

@media (max-width: 480px) {
    .velvet-verify-card {
        padding: 30px 20px;
    }
    .velvet-otp-digit {
        width: 42px;
        height: 52px;
        font-size: 20px;
        gap: 6px;
    }
}
</style>

<div class="velvet-verify-section">
    <div class="velvet-verify-card">
        <div class="velvet-icon-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
        </div>

        <h1 class="velvet-verify-title">Verify Your Email</h1>

        <?php if (!empty($email)) : ?>
            <p class="velvet-verify-subtitle">
                We've sent a 6-digit verification code to:<br>
                <span class="velvet-email-pill"><?php echo esc_html($email); ?></span>
            </p>
        <?php else : ?>
            <p class="velvet-verify-subtitle">
                No active registration was detected. Please complete the sign up form first.
            </p>
        <?php endif; ?>

        <?php if (!empty($errors)) : ?>
            <div class="velvet-alert velvet-alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <div>
                    <?php foreach ($errors as $err) : ?>
                        <div><?php echo esc_html($err); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)) : ?>
            <div class="velvet-alert velvet-alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <div><?php echo esc_html($success); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($email) && is_array($pending)) : ?>
            <form method="POST" action="<?php echo esc_url(home_url('/verify-email')); ?>" id="velvetOtpForm">
                <input type="hidden" name="action" value="verify_otp">
                <input type="hidden" name="otp" id="fullOtpInput" value="">

                <div class="velvet-otp-inputs">
                    <?php for ($i = 0; $i < 6; $i++) : ?>
                        <input 
                            type="text" 
                            name="otp_digits[]" 
                            class="velvet-otp-digit" 
                            maxlength="1" 
                            inputmode="numeric" 
                            pattern="[0-9]*" 
                            autocomplete="off" 
                            data-index="<?php echo $i; ?>" 
                            required
                        >
                    <?php endfor; ?>
                </div>

                <button type="submit" class="velvet-btn-submit" id="verifySubmitBtn">
                    Verify & Complete Registration &rarr;
                </button>
            </form>

            <div class="velvet-resend-row">
                <div>
                    Didn't receive the code? 
                    <form method="POST" action="<?php echo esc_url(home_url('/verify-email')); ?>" style="display:inline;" id="resendForm">
                        <input type="hidden" name="action" value="resend_otp">
                        <button type="submit" class="velvet-resend-btn" id="resendBtn">Resend Code</button>
                    </form>
                    <span id="countdownTimer" style="display:none; color:#a1a1aa; margin-left:4px;"></span>
                </div>

                <form method="POST" action="<?php echo esc_url(home_url('/verify-email')); ?>">
                    <input type="hidden" name="action" value="cancel_signup">
                    <button type="submit" class="velvet-cancel-btn">Wrong email address? Start over</button>
                </form>
            </div>
        <?php else : ?>
            <div style="margin-top: 20px;">
                <a href="<?php echo esc_url(home_url('/sign-up/')); ?>" class="velvet-btn-submit" style="display:inline-block; text-decoration:none;">
                    Go to Sign Up &rarr;
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var digits = document.querySelectorAll('.velvet-otp-digit');
    var fullOtpInput = document.getElementById('fullOtpInput');
    var form = document.getElementById('velvetOtpForm');
    var resendBtn = document.getElementById('resendBtn');
    var countdownTimer = document.getElementById('countdownTimer');

    if (digits.length > 0) {
        digits[0].focus();

        digits.forEach(function(input, idx) {
            // Auto-advance on input
            input.addEventListener('input', function(e) {
                var val = input.value.replace(/[^0-9]/g, '');
                input.value = val ? val[val.length - 1] : '';

                if (input.value && idx < digits.length - 1) {
                    digits[idx + 1].focus();
                }
                updateFullOtp();
            });

            // Backspace handling
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !input.value && idx > 0) {
                    digits[idx - 1].focus();
                } else if (e.key === 'ArrowLeft' && idx > 0) {
                    digits[idx - 1].focus();
                } else if (e.key === 'ArrowRight' && idx < digits.length - 1) {
                    digits[idx + 1].focus();
                }
            });

            // Paste handling for the whole 6 digits
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                var pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                var numbers = pasteData.replace(/[^0-9]/g, '').slice(0, 6);
                
                if (numbers.length > 0) {
                    for (var i = 0; i < digits.length; i++) {
                        if (i < numbers.length) {
                            digits[i].value = numbers[i];
                        }
                    }
                    if (numbers.length < digits.length) {
                        digits[numbers.length].focus();
                    } else {
                        digits[digits.length - 1].focus();
                    }
                    updateFullOtp();
                }
            });
        });

        function updateFullOtp() {
            var code = '';
            digits.forEach(function(d) { code += d.value; });
            if (fullOtpInput) fullOtpInput.value = code;
        }

        if (form) {
            form.addEventListener('submit', function() {
                updateFullOtp();
            });
        }
    }

    // Cooldown countdown timer for resend
    var cooldownSeconds = 30;
    var lastSent = <?php echo isset($_SESSION['last_otp_sent_time']) ? (int) $_SESSION['last_otp_sent_time'] : 0; ?>;
    var now = Math.floor(Date.now() / 1000);
    var elapsed = now - lastSent;
    var remaining = cooldownSeconds - elapsed;

    if (remaining > 0 && resendBtn && countdownTimer) {
        resendBtn.disabled = true;
        countdownTimer.style.display = 'inline';

        var interval = setInterval(function() {
            remaining--;
            if (remaining <= 0) {
                clearInterval(interval);
                resendBtn.disabled = false;
                countdownTimer.style.display = 'none';
            } else {
                countdownTimer.textContent = '(Wait ' + remaining + 's)';
            }
        }, 1000);
    }
});
</script>

<?php get_footer(); ?>
