<?php
/**
 * Quick Email Test for VelvetReel
 * Tests if wp_mail() is working on your local/production environment
 * 
 * Usage: Access this file in browser while logged in as admin
 * URL: http://your-site/wp-content/themes/hello-elementor-child/quick-email-test.php
 */

// Load WordPress core
require_once(dirname(dirname(dirname(__FILE__))) . '/wp-load.php');

// Security check - only admins can access
if (!current_user_can('manage_options')) {
    wp_die('You must be an administrator to access this page.');
}

// Process form submission
$email_sent = false;
$email_result = false;
$test_email = '';

if (isset($_POST['test_email'])) {
    $test_email = sanitize_email($_POST['test_email']);
    
    if (is_email($test_email)) {
        $subject = sprintf(
            'VelvetReel Email Test - %s',
            date('Y-m-d H:i:s')
        );
        
        $message = "
This is a test email from your VelvetReel website.

Website: " . site_url() . "
Test Time: " . current_time('mysql') . "
Recipient: {$test_email}

If you received this email, it means WordPress's wp_mail() function is working correctly.

---
Sent by VelvetReel Email Testing Tool
        ";
        
        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . get_option('blogname') . ' <' . get_option('admin_email') . '>'
        );
        
        // Attempt to send email
        $email_result = wp_mail($test_email, $subject, $message, $headers);
        $email_sent = true;
        
        // Log the result
        if ($email_result) {
            error_log("VelvetReel Email Test: SUCCESS - Email sent to {$test_email}");
        } else {
            error_log("VelvetReel Email Test: FAILED - Could not send to {$test_email}");
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - VelvetReel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .status {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid;
        }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border-left-color: #17a2b8;
            color: #0c5460;
        }
        form {
            margin-top: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        input[type="email"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            width: 100%;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        button:active {
            transform: translateY(0);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        ul {
            margin: 15px 0 15px 25px;
        }
        li {
            margin-bottom: 8px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Test Tool</h1>
        <p class="subtitle">Test if WordPress can send emails on your site</p>

        <?php if ($email_sent): ?>
            <?php if ($email_result): ?>
                <div class="status success">
                    <strong>✓ Email Sent Successfully!</strong><br><br>
                    A test email has been sent to <code><?php echo esc_html($test_email); ?></code><br><br>
                    <strong>Please check:</strong>
                    <ul>
                        <li>Your inbox (may take a few minutes)</li>
                        <li>Your spam/junk folder</li>
                        <li>If using Local by Flywheel, check the MailHog tab</li>
                    </ul>
                </div>
            <?php else: ?>
                <div class="status error">
                    <strong>✗ Email Failed to Send!</strong><br><br>
                    WordPress was unable to send an email to <code><?php echo esc_html($test_email); ?></code>.<br><br>
                    <strong>This indicates a problem with your email configuration.</strong><br><br>
                    <strong>Common causes:</strong>
                    <ul>
                        <li>You're on localhost without SMTP configured</li>
                        <li>Your hosting provider blocks PHP mail()</li>
                        <li>No SMTP plugin is installed</li>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="status info">
            <strong>ℹ️ About This Test</strong><br><br>
            This tool uses WordPress's <code>wp_mail()</code> function to send a test email.<br><br>
            <strong>Current Configuration:</strong>
            <ul>
                <li><strong>Site:</strong> <?php echo site_url(); ?></li>
                <li><strong>Admin Email:</strong> <?php echo get_option('admin_email'); ?></li>
                <li><strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
                <li><strong>WordPress:</strong> <?php echo $GLOBALS['wp_version']; ?></li>
            </ul>
        </div>

        <form method="post" action="">
            <div class="form-group">
                <label for="test_email">Enter Email Address for Testing:</label>
                <input 
                    type="email" 
                    id="test_email" 
                    name="test_email" 
                    value="<?php echo esc_attr(get_option('admin_email')); ?>"
                    required
                    placeholder="your.email@example.com"
                >
            </div>
            
            <button type="submit">
                📨 Send Test Email
            </button>
        </form>

        <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
            <h3 style="margin-bottom: 10px; color: #333;">Troubleshooting Tips:</h3>
            <ul style="color: #555; line-height: 1.8;">
                <li><strong>On localhost?</strong> Install an SMTP plugin like WP Mail SMTP</li>
                <li><strong>On production?</strong> Use a service like SendGrid, Mailgun, or your host's SMTP</li>
                <li><strong>Emails going to spam?</strong> Configure SPF and DKIM records for your domain</li>
                <li><strong>Need help?</strong> Check the <code>EMAIL_TROUBLESHOOTING.md</code> file in your theme directory</li>
            </ul>
        </div>

        <a href="<?php echo admin_url(); ?>" class="back-link">← Back to WordPress Admin</a>
    </div>
</body>
</html>
