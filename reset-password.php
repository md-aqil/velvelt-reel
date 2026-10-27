<?php
/**
 * Template Name: Reset Password
 * 
 * Luxury VelvetReel Password Creation Step
 * 
 * @package HelloElementorChild
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$errors = [];
$reset_success = false;

// Security check: Must have completed OTP verification step
if (empty($_SESSION['reset_email']) || empty($_SESSION['otp_verified'])) {
    wp_redirect(home_url('/forgot-password/'));
    exit;
}

$user_id = !empty($_SESSION['reset_user_id']) ? intval($_SESSION['reset_user_id']) : 0;
$user    = $user_id ? get_user_by('id', $user_id) : get_user_by('email', $_SESSION['reset_email']);

if (!$user) {
    $errors[] = "User account not found. Please start the password reset process again.";
} else {
    $user_id = $user->ID;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($user_id) && $user) {
    if (isset($_POST['velvet_reset_nonce']) && !wp_verify_nonce($_POST['velvet_reset_nonce'], 'velvet_reset_password_action')) {
        $errors[] = "Security check failed. Please refresh the page and try again.";
    }

    $password         = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Password validations
    if (!$password) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
        $errors[] = "Password must include uppercase, lowercase, a number, and a special character.";
    }

    if ($password !== $password_confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // 1. Reset password in WordPress core
        wp_set_password($password, $user_id);

        // 2. Trigger WordPress password_reset action hook (listened to by plugins)
        do_action('password_reset', $user, $password);

        // 3. Direct Simple Membership sync to guarantee member login works
        if (class_exists('SwpmMemberUtils') && class_exists('SwpmUtils')) {
            $swpm_user = SwpmMemberUtils::get_user_by_user_name($user->user_login);
            if (!empty($swpm_user) && !empty($swpm_user->member_id)) {
                global $wpdb;
                $swpm_hash = SwpmUtils::encrypt_password($password);
                $wpdb->update(
                    $wpdb->prefix . "swpm_members_tbl",
                    array('password' => $swpm_hash),
                    array('member_id' => $swpm_user->member_id)
                );
            }
        }

        // Clear tokens and verification session
        delete_user_meta($user_id, 'reset_token');
        delete_user_meta($user_id, 'reset_token_expiry');
        delete_user_meta($user_id, 'last_otp_request_time');
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_user_id']);
        unset($_SESSION['otp_verified']);

        $reset_success = true;
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
.pwd-req-list {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-top: 10px;
    margin-bottom: 6px;
    padding: 0;
    list-style: none;
}
.pwd-req-item {
    font-size: 11.5px;
    color: #8888a4;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s ease;
}
.pwd-req-item svg {
    width: 13px;
    height: 13px;
    flex-shrink: 0;
}
.pwd-req-item.valid {
    color: #4ade80;
}
.auth-success-state {
    padding: 10px 0 20px 0;
    text-align: center;
}
.success-icon-badge {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    margin: 0 auto 20px auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(74, 222, 128, 0.2), rgba(34, 197, 94, 0.1));
    border: 1.5px solid rgba(74, 222, 128, 0.5);
    color: #4ade80;
    box-shadow: 0 10px 30px rgba(74, 222, 128, 0.25);
}
.success-icon-badge svg {
    width: 36px;
    height: 36px;
}
@media (max-width: 480px) {
    .pwd-req-list {
        grid-template-columns: 1fr;
    }
}
</style>

<main id="content" class="site-main velvet-auth-section" role="main">
    <!-- Ambient Background Glow Elements -->
    <div class="auth-bg-glow glow-1"></div>
    <div class="auth-bg-glow glow-2"></div>
    <div class="auth-bg-glow glow-3"></div>

    <div class="velvet-auth-container">
        <div class="velvet-auth-card">

            <?php if ($reset_success) : ?>
                <!-- Success State -->
                <div class="auth-success-state">
                    <div class="success-icon-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>

                    <h1 class="auth-title"><?php esc_html_e('Password Updated!', 'hello-elementor-child'); ?></h1>
                    <p class="auth-subtitle"><?php esc_html_e('Your password has been successfully reset. You can now sign in with your new credentials.', 'hello-elementor-child'); ?></p>

                    <div class="auth-submit-group" style="margin-top: 26px;">
                        <a href="<?php echo esc_url(home_url('/membership-login/')); ?>" class="btn-auth-submit" style="text-decoration: none;">
                            <span><?php esc_html_e('Proceed to Member Sign In', 'hello-elementor-child'); ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>

            <?php else : ?>
                <!-- Password Reset Form -->
                <div class="auth-badge-wrapper">
                    <span class="auth-badge">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <?php esc_html_e('STEP 3 OF 3 • NEW PASSWORD', 'hello-elementor-child'); ?>
                    </span>
                </div>

                <h1 class="auth-title"><?php esc_html_e('Create New Password', 'hello-elementor-child'); ?></h1>
                <p class="auth-subtitle"><?php esc_html_e('Choose a strong, unique password to secure your VelvetReel account.', 'hello-elementor-child'); ?></p>

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

                <form id="velvetResetForm" class="velvet-auth-form" method="post" action="" novalidate>
                    <?php wp_nonce_field('velvet_reset_password_action', 'velvet_reset_nonce'); ?>

                    <!-- New Password Field -->
                    <div class="auth-field-group">
                        <label for="password" class="auth-field-label"><?php esc_html_e('New Password', 'hello-elementor-child'); ?></label>
                        <div class="auth-input-wrapper">
                            <svg class="auth-input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="auth-input-field" 
                                   placeholder="<?php esc_attr_e('At least 6 characters', 'hello-elementor-child'); ?>" 
                                   required 
                                   autocomplete="new-password" 
                                   autofocus />
                            <button type="button" class="auth-toggle-pwd" data-target="password" aria-label="<?php esc_attr_e('Toggle password visibility', 'hello-elementor-child'); ?>">
                                <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <svg class="eye-closed" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Password Requirement Checklist -->
                    <ul class="pwd-req-list" aria-live="polite">
                        <li class="pwd-req-item" id="req-len">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle></svg>
                            <span>At least 6 characters</span>
                        </li>
                        <li class="pwd-req-item" id="req-case">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle></svg>
                            <span>Upper & lower case</span>
                        </li>
                        <li class="pwd-req-item" id="req-num">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle></svg>
                            <span>At least 1 number</span>
                        </li>
                        <li class="pwd-req-item" id="req-sym">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle></svg>
                            <span>Special character</span>
                        </li>
                    </ul>

                    <!-- Confirm Password Field -->
                    <div class="auth-field-group">
                        <label for="password_confirm" class="auth-field-label"><?php esc_html_e('Confirm New Password', 'hello-elementor-child'); ?></label>
                        <div class="auth-input-wrapper">
                            <svg class="auth-input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input type="password" 
                                   name="password_confirm" 
                                   id="password_confirm" 
                                   class="auth-input-field" 
                                   placeholder="<?php esc_attr_e('Re-enter new password', 'hello-elementor-child'); ?>" 
                                   required 
                                   autocomplete="new-password" />
                            <button type="button" class="auth-toggle-pwd" data-target="password_confirm" aria-label="<?php esc_attr_e('Toggle password visibility', 'hello-elementor-child'); ?>">
                                <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <svg class="eye-closed" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="auth-submit-group">
                        <button type="submit" class="btn-auth-submit" id="btnResetSubmit">
                            <span><?php esc_html_e('Update Password & Sign In', 'hello-elementor-child'); ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="auth-card-footer">
                    <p class="auth-footer-text">
                        <a href="<?php echo esc_url(home_url('/membership-login/')); ?>" class="auth-signup-link">
                            &larr; <?php esc_html_e('Cancel and back to Sign In', 'hello-elementor-child'); ?>
                        </a>
                    </p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggles
    var toggleButtons = document.querySelectorAll('.auth-toggle-pwd');
    toggleButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var targetId = btn.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;
            var eyeOpen = btn.querySelector('.eye-open');
            var eyeClosed = btn.querySelector('.eye-closed');
            if (input.type === 'password') {
                input.type = 'text';
                if (eyeOpen) eyeOpen.style.display = 'none';
                if (eyeClosed) eyeClosed.style.display = 'block';
            } else {
                input.type = 'password';
                if (eyeOpen) eyeOpen.style.display = 'block';
                if (eyeClosed) eyeClosed.style.display = 'none';
            }
        });
    });

    // Real-time password requirement tracker
    var pwdInput = document.getElementById('password');
    var reqLen = document.getElementById('req-len');
    var reqCase = document.getElementById('req-case');
    var reqNum = document.getElementById('req-num');
    var reqSym = document.getElementById('req-sym');

    function updateReq(el, isValid) {
        if (!el) return;
        if (isValid) {
            el.classList.add('valid');
            el.classList.remove('invalid');
            el.querySelector('svg').innerHTML = '<polyline points="20 6 9 17 4 12"></polyline>';
        } else {
            el.classList.remove('valid');
            el.classList.add('invalid');
            el.querySelector('svg').innerHTML = '<circle cx="12" cy="12" r="10"></circle>';
        }
    }

    if (pwdInput) {
        pwdInput.addEventListener('input', function() {
            var val = this.value;
            updateReq(reqLen, val.length >= 6);
            updateReq(reqCase, /[a-z]/.test(val) && /[A-Z]/.test(val));
            updateReq(reqNum, /[0-9]/.test(val));
            updateReq(reqSym, /[\W_]/.test(val));
        });
    }

    var form = document.getElementById('velvetResetForm');
    var btn = document.getElementById('btnResetSubmit');
    if (form && btn) {
        form.addEventListener('submit', function() {
            btn.style.opacity = '0.75';
            btn.style.pointerEvents = 'none';
            btn.querySelector('span').textContent = '<?php esc_attr_e('Updating...', 'hello-elementor-child'); ?>';
        });
    }
});
</script>

<?php
get_footer();