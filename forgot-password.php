<?php
/**
 * Template Name: Forgot Password
 * 
 * Luxury VelvetReel Account Recovery
 * 
 * @package HelloElementorChild
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nonce verification
    if (isset($_POST['velvet_forgot_nonce']) && !wp_verify_nonce($_POST['velvet_forgot_nonce'], 'velvet_forgot_password_action')) {
        $errors[] = "Security check failed. Please refresh the page and try again.";
    }

    $login_input = sanitize_text_field(trim($_POST['email'] ?? ''));

    if (empty($login_input)) {
        $errors[] = "Please enter your email address or username.";
    } else {
        // Find user by email or by username
        $user = is_email($login_input) ? get_user_by('email', $login_input) : get_user_by('login', $login_input);

        // Also check Simple Membership record if user not found in core WP users directly
        if (!$user && class_exists('SwpmMemberUtils')) {
            $swpm_user = SwpmMemberUtils::get_user_by_email($login_input);
            if (!$swpm_user) {
                $swpm_user = SwpmMemberUtils::get_user_by_user_name($login_input);
            }
            if ($swpm_user && !empty($swpm_user->user_name)) {
                $user = get_user_by('login', $swpm_user->user_name);
            }
        }

        if ($user) {
            $user_id = $user->ID;
            $user_email = $user->user_email;

            // Check if OTP cooldown is active (5 minutes)
            $last_request_time = get_user_meta($user_id, 'last_otp_request_time', true);
            $cooldown_seconds = 300; // 5 minutes
            if ($last_request_time && (strtotime($last_request_time) > time() - $cooldown_seconds)) {
                $wait_mins = ceil((strtotime($last_request_time) + $cooldown_seconds - time()) / 60);
                $errors[] = "An OTP was recently generated. Please wait " . $wait_mins . " minute(s) before requesting a new code.";
            }

            if (empty($errors)) {
                // Generate 6-digit OTP
                $otp = (string)random_int(100000, 999999);
                $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

                // Save OTP in user_meta
                update_user_meta($user_id, 'reset_token', $otp);
                update_user_meta($user_id, 'reset_token_expiry', $expiry);
                update_user_meta($user_id, 'last_otp_request_time', current_time('mysql'));

                // Track error message if wp_mail fails
                $mail_error_msg = '';
                $mail_failed_handler = function($wp_error) use (&$mail_error_msg) {
                    if (is_wp_error($wp_error)) {
                        $mail_error_msg = $wp_error->get_error_message();
                    }
                };
                add_action('wp_mail_failed', $mail_failed_handler);

                // Use branded VelvetReel OTP email template
                $mail_sent = false;
                if (function_exists('velvet_send_otp_email')) {
                    $recipient_name = !empty($user->first_name) ? $user->first_name : $user->display_name;
                    $mail_sent = velvet_send_otp_email($user_email, $otp, 'reset_password', $recipient_name, 10);
                } else {
                    $subject = "Your VelvetReel Password Reset Code: " . $otp;
                    $message = "Hello " . esc_html($user->display_name) . ",\n\nYour password reset code is: " . $otp . "\n\nThis OTP will expire in 10 minutes.\n\nIf you didn't request this, you can safely ignore this email.";
                    $headers = array('Content-Type: text/plain; charset=UTF-8');
                    $mail_sent = wp_mail($user_email, $subject, $message, $headers);
                }

                remove_action('wp_mail_failed', $mail_failed_handler);

                if ($mail_sent) {
                    $_SESSION['reset_email'] = $user_email;
                    $_SESSION['reset_user_id'] = $user_id;
                    wp_redirect(home_url('/verify-otp/'));
                    exit;
                } else {
                    $err_detail = !empty($mail_error_msg) ? ' (' . esc_html($mail_error_msg) . ')' : '';
                    $errors[] = "Could not send OTP email" . $err_detail . ". Please check mail configuration or try again shortly.";
                }
            }
        } else {
            $errors[] = "No account found matching that email address or username.";
        }
    }
}

// Pull any flash errors from session safely
if (isset($_SESSION['forgot_errors']) && is_array($_SESSION['forgot_errors'])) {
    $errors = array_merge($errors, $_SESSION['forgot_errors']);
    unset($_SESSION['forgot_errors']);
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
.velvet-auth-card {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease, box-shadow 0.3s ease;
}
.velvet-auth-card:hover {
    transform: translateY(-2px);
}
</style>

<main id="content" class="site-main velvet-auth-section" role="main">
    <!-- Ambient Background Glow Elements -->
    <div class="auth-bg-glow glow-1"></div>
    <div class="auth-bg-glow glow-2"></div>
    <div class="auth-bg-glow glow-3"></div>

    <div class="velvet-auth-container">
        <div class="velvet-auth-card">
            <!-- Security Badge -->
            <div class="auth-badge-wrapper">
                <span class="auth-badge">
                    <i class="fas fa-shield-alt" aria-hidden="true"></i>
                    <?php esc_html_e('SECURE RECOVERY', 'hello-elementor-child'); ?>
                </span>
            </div>

            <h1 class="auth-title"><?php esc_html_e('Forgot Password?', 'hello-elementor-child'); ?></h1>
            <p class="auth-subtitle"><?php esc_html_e('Enter your registered email address or username and we will send a 6-digit verification code to reset your password.', 'hello-elementor-child'); ?></p>

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

            <form id="velvetForgotForm" class="velvet-auth-form" method="post" action="" novalidate>
                <?php wp_nonce_field('velvet_forgot_password_action', 'velvet_forgot_nonce'); ?>

                <!-- Email / Username Field -->
                <div class="auth-field-group">
                    <label for="forgot_email" class="auth-field-label"><?php esc_html_e('Email Address or Username', 'hello-elementor-child'); ?></label>
                    <div class="auth-input-wrapper">
                        <svg class="auth-input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <input type="text" 
                               name="email" 
                               id="forgot_email" 
                               class="auth-input-field" 
                               placeholder="<?php esc_attr_e('e.g. alex@example.com or username', 'hello-elementor-child'); ?>" 
                               value="<?php echo esc_attr($_POST['email'] ?? ''); ?>" 
                               required 
                               autocomplete="username" 
                               autofocus />
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="auth-submit-group">
                    <button type="submit" class="btn-auth-submit" id="btnForgotSubmit">
                        <span><?php esc_html_e('Send Verification Code', 'hello-elementor-child'); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Back to Sign In Link -->
            <div class="auth-card-footer">
                <p class="auth-footer-text">
                    <?php esc_html_e('Remembered your password?', 'hello-elementor-child'); ?>
                    <a href="<?php echo esc_url(home_url('/membership-login/')); ?>" class="auth-signup-link">
                        <?php esc_html_e('Back to Sign In', 'hello-elementor-child'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('velvetForgotForm');
    var btn = document.getElementById('btnForgotSubmit');
    if (form && btn) {
        form.addEventListener('submit', function() {
            var input = document.getElementById('forgot_email');
            if (input && input.value.trim() !== '') {
                btn.style.opacity = '0.75';
                btn.style.pointerEvents = 'none';
                btn.querySelector('span').textContent = '<?php esc_attr_e('Sending Code...', 'hello-elementor-child'); ?>';
            }
        });
    }
});
</script>

<?php
get_footer();