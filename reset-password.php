<?php
/**
 * Template Name: Reset Password
 */
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$errors = [];
$success = "";

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

    $password = $_POST['password'] ?? '';
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

        $login_url = home_url('/membership-login/');
        $success = "Password reset successfully! You can now <a href='" . esc_url($login_url) . "' class='chaitu-link' style='color:#CD2838; font-weight:bold; text-decoration:underline;'>Sign In</a> with your new password.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - The VelvetReel</title>
    <style>
    .chaitu-reset {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .chaitu-body {
                background-image: linear-gradient(rgb(107, 15, 15), rgb(45, 45, 45)) !important;

        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .chaitu-container {
      
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        width: 100%;
        max-width: 420px;
        text-align: center;
    }

    .chaitu-title {
        font-size: 2.5rem;
        font-weight: 300;
        color: #ffffff;
        margin-bottom: 10px;
        letter-spacing: 2px;
    }

    .chaitu-subtitle {
        color: #b0b0b0;
        margin-bottom: 20px;
        font-size: 1rem;
    }

    .chaitu-heading {
        font-size: 1.5rem;
        color: #ffffff;
        margin-bottom: 30px;
        font-weight: 400;
    }

    .chaitu-form-group {
        margin-bottom: 20px;
        text-align: left;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .chaitu-label {
        color: white;
        width: 100%;
        max-width: 360px;
    }

    .chaitu-input {
        width: 100%;
        max-width: 360px;
        padding: 15px 20px;
        background: rgba(60, 60, 60, 0.8);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #ffffff;
        font-size: 1rem;
        transition: all 0.3s ease;
        outline: none;
    }


    .chaitu-input::placeholder {
        color: #888;
    }

    .chaitu-input:focus {
        border-color: #e74c3c;
        background: rgba(70, 70, 70, 0.9);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .chaitu-button {
        width: 100%;
        padding: 15px;
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 10px;
    }

    .chaitu-button:hover {
        background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(231, 76, 60, 0.3);
    }

    .chaitu-button:active {
        transform: translateY(0);
    }

    .chaitu-error {
        background: rgba(231, 76, 60, 0.1);
        border: 1px solid #e74c3c;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        color: #ff6b6b;
    }

    .chaitu-error ul {
        margin: 0;
        padding-left: 20px;
    }

    .chaitu-error li {
        margin-bottom: 5px;
    }

    .chaitu-success {
        background: rgba(46, 204, 113, 0.1);
        border: 1px solid #2ecc71;
        border-radius: 8px;
        padding: 20px;
        color: #2ecc71;
        font-size: 1.1rem;
        line-height: 1.5;
    }

    .chaitu-link {
        color: #e74c3c;
        text-decoration: none;
        font-weight: 600;
    }

    .chaitu-link:hover {
        color: #c0392b;
        text-decoration: underline;
    }

    .chaitu-back-link {
        display: inline-block;
        color: #b0b0b0;
        text-decoration: none;
        margin-top: 20px;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .chaitu-back-link:hover {
        color: #e74c3c;
    }

    .chaitu-icon {
        font-size: 3rem;
        color: #e74c3c;
        margin-bottom: 20px;
    }

    .chaitu-success-icon {
        font-size: 4rem;
        color: #2ecc71;
        margin-bottom: 20px;
    }

    .chaitu-password-requirements {
        background: rgba(52, 152, 219, 0.1);
        border: 1px solid #3498db;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        color: #74b9ff;
        text-align: left;
    }

    .chaitu-password-requirements ul {
        margin: 0;
        padding-left: 20px;
    }

    .chaitu-password-requirements li {
        margin-bottom: 3px;
    }

    @media (max-width: 480px) {
        .chaitu-container {
            padding: 30px 20px;
            margin: 10px;
        }

        .chaitu-title {
            font-size: 2rem;
        }

        .chaitu-heading {
            font-size: 1.3rem;
        }
    }
    </style>
</head>

<body class="chaitu-body">
    <div class="chaitu-container">
        <?php if ($success): ?>
        <div class="chaitu-success-icon">✅</div>
        <h1 class="chaitu-title">The VelvetReel</h1>
        <p class="chaitu-subtitle">Password reset complete</p>
        <div class="chaitu-success"><?= $success ?></div>
        <?php else: ?>
        <div class="chaitu-icon">🔑</div>
        <h1 class="chaitu-title">The VelvetReel</h1>
        <p class="chaitu-subtitle">Create your new password</p>

        <h2 class="chaitu-heading">Reset Your Password</h2>

        <div class="chaitu-password-requirements">
            <strong>Password Requirements:</strong>
            <ul>
                <li>At least 6 characters long</li>
                <li>Include uppercase and lowercase letters</li>
                <li>Include at least one number</li>
                <li>Include at least one special character</li>
            </ul>
        </div>

        <?php if ($errors): ?>
        <div class="chaitu-error">
            <ul>
                <?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <?php wp_nonce_field('velvet_reset_password_action', 'velvet_reset_nonce'); ?>
            <div class="chaitu-form-group">
                <label for="password" class="chaitu-label">New Password</label>
                <input type="password" id="password" name="password" class="chaitu-input"
                    placeholder="Enter your new password" required autocomplete="new-password" />
            </div>

            <div class="chaitu-form-group">
                <label for="password_confirm" class="chaitu-label">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" class="chaitu-input"
                    placeholder="Confirm your new password" required autocomplete="new-password" />
            </div>

            <button type="submit" class="chaitu-button">Reset Password</button>
        </form>
        <a href="<?php echo esc_url(home_url('/forgot-password/')); ?>" class="chaitu-back-link">← Back to Forgot Password</a>

        <?php endif; ?>
    </div>
</body>

</html>