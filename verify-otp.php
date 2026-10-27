<?php
/**
 * Template Name: Verify Otp
 * 
 * Luxury VelvetReel OTP Verification Step
 * 
 * @package HelloElementorChild
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$errors = [];
$success = "";
$resend_message = "";
$can_resend = true;
$remaining_time = 0;

// Security check: Must have reset email in session
if (empty($_SESSION['reset_email'])) {
    wp_redirect(home_url('/forgot-password/'));
    exit;
}

$email = sanitize_email($_SESSION['reset_email']);
$user  = get_user_by('email', $email);

if (!$user) {
    $errors[] = "No account found matching this email session. Please request a new code.";
} else {
    $user_id = $user->ID;
}

// Check if resend is allowed (60 seconds cooldown)
if (isset($user_id)) {
    $last_otp_time = get_user_meta($user_id, 'last_otp_time', true);
    if ($last_otp_time) {
        $time_diff = time() - strtotime($last_otp_time);
        if ($time_diff < 60) {
            $can_resend = false;
            $remaining_time = 60 - $time_diff;
        }
    }
}

// Handle resend OTP request
if (isset($_POST['resend_otp']) && $can_resend && isset($user_id)) {
    // Generate new OTP
    $otp = (string)random_int(100000, 999999);
    
    // Store OTP and expiry (10 minutes)
    update_user_meta($user_id, 'reset_token', $otp);
    update_user_meta($user_id, 'reset_token_expiry', date('Y-m-d H:i:s', strtotime('+10 minutes')));
    update_user_meta($user_id, 'last_otp_time', current_time('mysql'));
    
    // Send branded email with OTP
    $mail_sent = false;
    if (function_exists('velvet_send_otp_email')) {
        $recipient_name = !empty($user->first_name) ? $user->first_name : $user->display_name;
        $mail_sent = velvet_send_otp_email($email, $otp, 'reset_password', $recipient_name, 10);
    } else {
        $subject = 'Your VelvetReel Password Reset Code: ' . $otp;
        $message = "Your OTP for password reset is: $otp\n\nThis OTP will expire in 10 minutes.";
        $mail_sent = wp_mail($email, $subject, $message);
    }
    
    if ($mail_sent) {
        $resend_message = "A fresh verification code has been dispatched to your email.";
    } else {
        $errors[] = "Could not resend verification code. Please check your email configuration.";
    }
    $can_resend = false;
    $remaining_time = 60;
}

// Handle OTP submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['resend_otp']) && isset($user_id)) {
    $input_otp = trim($_POST['otp'] ?? '');

    if (!$input_otp) {
        $errors[] = "Please enter the 6-digit verification code.";
    } else {
        $stored_otp = (string)get_user_meta($user_id, 'reset_token', true);
        $expiry     = get_user_meta($user_id, 'reset_token_expiry', true);

        if (!$stored_otp || !$expiry) {
            $errors[] = "No active OTP request found. Please request a new code.";
        } elseif ($input_otp === $stored_otp && strtotime($expiry) > time()) {
            // OTP is valid — clear temporary OTP tokens and authorize reset step
            delete_user_meta($user_id, 'reset_token');
            delete_user_meta($user_id, 'reset_token_expiry');
            delete_user_meta($user_id, 'last_otp_time');

            $_SESSION['otp_verified'] = true;
            $_SESSION['reset_user_id'] = $user_id;

            // Redirect to reset password page
            wp_redirect(home_url('/reset-password/'));
            exit;
        } else {
            $errors[] = "Invalid or expired verification code. Please check the code or request a new one.";
        }
    }
}

get_header();
?>

<!-- Google Fonts Outfit & Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
.velvet-auth-section {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
}
.velvet-auth-section .auth-title {
    font-family: 'Outfit', 'Inter', -apple-system, sans-serif !important;
    letter-spacing: -0.5px;
}
.velvet-auth-section .auth-subtitle {
    font-family: 'Inter', -apple-system, sans-serif !important;
}
.velvet-auth-section .auth-field-label,
.velvet-auth-section .auth-badge,
.velvet-auth-section .btn-auth-submit,
.velvet-auth-section .auth-footer-text,
.velvet-auth-section .auth-signup-link {
    font-family: 'Inter', -apple-system, sans-serif !important;
}
.auth-email-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(223, 29, 61, 0.12);
    border: 1px solid rgba(223, 29, 61, 0.3);
    color: #fca5a5;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 24px;
}
.auth-email-badge svg {
    width: 14px;
    height: 14px;
    color: #df1d3d;
}
.auth-otp-wrapper {
    position: relative;
    display: flex;
    justify-content: center;
    margin-top: 6px;
}
.auth-otp-input {
    width: 100% !important;
    max-width: 280px;
    text-align: center !important;
    letter-spacing: 12px !important;
    font-size: 26px !important;
    font-weight: 700 !important;
    font-family: 'Outfit', monospace !important;
    padding: 14px 16px !important;
    background: rgba(28, 28, 42, 0.9) !important;
    border: 1.5px solid rgba(223, 29, 61, 0.35) !important;
    border-radius: 14px !important;
    color: #ffffff !important;
    transition: all 0.25s ease !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35) !important;
}
.auth-otp-input:focus {
    border-color: #df1d3d !important;
    box-shadow: 0 0 0 3px rgba(223, 29, 61, 0.25) !important;
    background: rgba(34, 34, 52, 0.95) !important;
    outline: none !important;
}
.auth-otp-input::placeholder {
    letter-spacing: 8px;
    color: #666682;
}
.auth-resend-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 18px;
    font-size: 13px;
}
.auth-resend-status {
    color: #9090aa;
    display: flex;
    align-items: center;
    gap: 6px;
}
.auth-resend-status svg {
    width: 14px;
    height: 14px;
}
.btn-resend-link {
    background: transparent;
    border: none;
    color: #df1d3d;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    padding: 0;
    text-decoration: underline;
    transition: color 0.2s ease;
}
.btn-resend-link:hover:not(:disabled) {
    color: #ff3b5c;
}
.btn-resend-link:disabled {
    color: #666680;
    cursor: not-allowed;
    text-decoration: none;
}
.auth-success-banner {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 18px;
    background: rgba(34, 197, 94, 0.15);
    border: 1px solid rgba(34, 197, 94, 0.4);
    border-radius: 12px;
    margin-bottom: 24px;
    text-align: left;
}
.auth-success-banner .alert-icon {
    color: #4ade80;
    flex-shrink: 0;
    margin-top: 2px;
}
.auth-success-banner .alert-content p {
    color: #86efac;
    font-size: 13px;
    line-height: 1.4;
    margin: 0;
}
</style>

<main id="content" class="site-main velvet-auth-section" role="main">
    <!-- Ambient Background Glow Elements -->
    <div class="auth-bg-glow glow-1"></div>
    <div class="auth-bg-glow glow-2"></div>
    <div class="auth-bg-glow glow-3"></div>

    <div class="velvet-auth-container">
        <div class="velvet-auth-card">
            <!-- Step Badge -->
            <div class="auth-badge-wrapper">
                <span class="auth-badge">
                    <i class="fas fa-key" aria-hidden="true"></i>
                    <?php esc_html_e('STEP 2 OF 3 • VERIFICATION', 'hello-elementor-child'); ?>
                </span>
            </div>

            <h1 class="auth-title"><?php esc_html_e('Verify Code', 'hello-elementor-child'); ?></h1>
            <p class="auth-subtitle"><?php esc_html_e('Enter the 6-digit verification code sent to your email address:', 'hello-elementor-child'); ?></p>

            <div class="auth-email-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <span><?php echo esc_html($email); ?></span>
            </div>

            <?php if (!empty($errors)) : ?>
                <div class="auth-alert-banner" role="alert" aria-live="polite">
                    <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div class="alert-content">
                        <?php foreach ($errors as $error) : ?>
                            <p><?php echo esc_html($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($resend_message)) : ?>
                <div class="auth-success-banner" role="status">
                    <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <div class="alert-content">
                        <p><?php echo esc_html($resend_message); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- OTP Verification Form -->
            <form id="velvetOtpForm" class="velvet-auth-form" method="post" action="" novalidate>
                <div class="auth-field-group">
                    <label for="otp" class="auth-field-label" style="text-align: center;"><?php esc_html_e('6-Digit Verification Code', 'hello-elementor-child'); ?></label>
                    <div class="auth-otp-wrapper">
                        <input type="text" 
                               name="otp" 
                               id="otp" 
                               class="auth-otp-input" 
                               maxlength="6" 
                               inputmode="numeric" 
                               pattern="[0-9]*" 
                               placeholder="······" 
                               required 
                               autocomplete="one-time-code" 
                               autofocus />
                    </div>
                </div>

                <div class="auth-submit-group">
                    <button type="submit" class="btn-auth-submit" id="btnOtpSubmit">
                        <span><?php esc_html_e('Verify & Continue', 'hello-elementor-child'); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Resend OTP Form -->
            <form id="velvetResendForm" method="post" action="" style="display:inline;">
                <input type="hidden" name="resend_otp" value="1" />
                <div class="auth-resend-row">
                    <span class="auth-resend-status">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span id="countdown-text"><?php echo $can_resend ? esc_html__('Did not receive the code?', 'hello-elementor-child') : sprintf(esc_html__('Resend code in %ds', 'hello-elementor-child'), $remaining_time); ?></span>
                    </span>
                    <button type="submit" class="btn-resend-link" id="btnResend" <?php echo !$can_resend ? 'disabled' : ''; ?>>
                        <?php esc_html_e('Resend Code', 'hello-elementor-child'); ?>
                    </button>
                </div>
            </form>

            <!-- Back Link -->
            <div class="auth-card-footer">
                <p class="auth-footer-text">
                    <a href="<?php echo esc_url(home_url('/forgot-password/')); ?>" class="auth-signup-link">
                        &larr; <?php esc_html_e('Request code for a different account', 'hello-elementor-child'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var otpInput = document.getElementById('otp');
    var otpForm = document.getElementById('velvetOtpForm');
    var btnSubmit = document.getElementById('btnOtpSubmit');
    var btnResend = document.getElementById('btnResend');
    var countdownText = document.getElementById('countdown-text');
    var remaining = <?php echo intval($remaining_time); ?>;

    // Auto-focus and filter digits only
    if (otpInput) {
        otpInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 6 && otpForm) {
                // Auto-submit when 6 digits are typed
                btnSubmit.style.opacity = '0.75';
                btnSubmit.querySelector('span').textContent = '<?php esc_attr_e('Verifying...', 'hello-elementor-child'); ?>';
                otpForm.submit();
            }
        });
    }

    if (otpForm && btnSubmit) {
        otpForm.addEventListener('submit', function() {
            btnSubmit.style.opacity = '0.75';
            btnSubmit.style.pointerEvents = 'none';
            btnSubmit.querySelector('span').textContent = '<?php esc_attr_e('Verifying...', 'hello-elementor-child'); ?>';
        });
    }

    // Countdown timer for resend
    if (remaining > 0) {
        var timer = setInterval(function() {
            remaining--;
            if (remaining <= 0) {
                clearInterval(timer);
                if (btnResend) btnResend.disabled = false;
                if (countdownText) countdownText.textContent = '<?php esc_attr_e('Did not receive the code?', 'hello-elementor-child'); ?>';
            } else {
                if (countdownText) countdownText.textContent = 'Resend code in ' + remaining + 's';
            }
        }, 1000);
    }
});
</script>

<?php
get_footer();