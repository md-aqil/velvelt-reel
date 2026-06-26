<?php
/**
 * Template Name: Forgot Password
 */
session_start();
require_once ABSPATH . 'wp-load.php';

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_email(trim($_POST['email'] ?? ''));

    // Validate email
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    } else {
        // Get user by email from WordPress
        $user = get_user_by('email', $email);

        if ($user) {
            $user_id = $user->ID;

            // Check if OTP has been requested recently
            $last_request_time = get_user_meta($user_id, 'last_otp_request_time', true);
            if ($last_request_time && strtotime($last_request_time) > time() - 600) {
                $errors[] = "You can only request an OTP once every 10 minutes.";
            }

            if (empty($errors)) {
                // Generate 6-digit OTP
                $otp = random_int(100000, 999999);
                $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

                // Save OTP in user_meta
                update_user_meta($user_id, 'reset_token', $otp);
                update_user_meta($user_id, 'reset_token_expiry', $expiry);
                update_user_meta($user_id, 'last_otp_request_time', current_time('mysql'));

                // Send OTP via wp_mail()
                $subject = "Your Password Reset OTP";
                $message = "Hello,\n\nYour password reset OTP is: $otp\n\nThis OTP will expire in 10 minutes.\n\nIf you didn’t request this, you can safely ignore this email.";
                $headers = array('Content-Type: text/plain; charset=UTF-8');

                // Attempt to send the email
                $mail_sent = wp_mail($email, $subject, $message, $headers);

                // Check if the email was sent successfully
                if ($mail_sent) {
                    $_SESSION['reset_email'] = $email;
                    wp_redirect(home_url('/verify-otp')); // Redirect to OTP verification page
                    exit;
                } else {
                    $errors[] = "Could not send OTP. Please check your email configuration or try again later.";
                }
            }
        } else {
            $errors[] = "No account found with that email address.";
        }
    }

    // Store errors in session to display them after redirect
    if (!empty($errors)) {
        $_SESSION['forgot_errors'] = $errors;
        wp_redirect(home_url('/forgot-password')); // Redirect back to the forgot password page
        exit;
    }
}

// To display the errors on the forgot-password page
if (isset($_SESSION['forgot_errors'])) {
    foreach ($_SESSION['forgot_errors'] as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
    unset($_SESSION['forgot_errors']);
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - The VelvetReel</title>
    <style>
    .chaitu-reset {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .chaitu-body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    }

    .chaitu-label {
        display: block;
        color: #ffffff;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .chaitu-input {
        width: 95%;
        padding: 15px 5px;
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
        border-color: #CD2838;
        background: rgba(70, 70, 70, 0.9);
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }

    .chaitu-button {
        width: 100%;
        padding: 15px;
        background: #CD2838;
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
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        transform: translateY(-2px);

    }

    .chaitu-button:active {
        transform: translateY(0);
    }

    .chaitu-error {
        background: rgba(231, 76, 60, 0.1);
        border: 1px solid #CD2838;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        color: #CD2838;
    }

    .chaitu-error ul {
        margin: 0;
        padding-left: 20px;
    }

    .chaitu-error li {
        margin-bottom: 5px;
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
        color: #dc3545;
        text-decoration: underline;
    }

    .chaitu-icon {
        font-size: 3rem;
        color: #CD2838;
        margin-bottom: 20px;
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
        <div class="chaitu-icon">🔐</div>
        <h1 class="chaitu-title">The VelvetReel</h1>
        <p class="chaitu-subtitle">Reset your password securely</p>

        <h2 class="chaitu-heading">Forgot Your Password?</h2>

        <?php if ($errors): ?>
        <div class="chaitu-error">
            <ul>
                <?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= esc_url(home_url('/forgot-password')) ?>" novalidate>

            <div class="chaitu-form-group">
                <label for="email" class="chaitu-label">Email Address</label>
                <input type="email" id="email" name="email" class="chaitu-input" placeholder="Enter your email address"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
            </div>

            <button type="submit" class="chaitu-button">Send OTP</button>
        </form>

        <a href="<?php echo home_url('/membership-login/'); ?>" class="chaitu-back-link">← Back to Sign In</a>

    </div>
</body>

</html>