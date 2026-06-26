<?php
/**
 * Email Diagnostic Tool for VelvetReel
 * Tests WordPress email functionality and SWPM registration email configuration
 * 
 * Usage: Access this file directly in browser (must be logged in as admin)
 */

// Load WordPress
require_once(dirname(dirname(dirname(__FILE__))) . '/wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You do not have permission to access this page.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Diagnostic Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #dc3545; border-bottom: 3px solid #dc3545; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; }
        .status { padding: 10px 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; border-left: 5px solid #28a745; color: #155724; }
        .error { background: #f8d7da; border-left: 5px solid #dc3545; color: #721c24; }
        .warning { background: #fff3cd; border-left: 5px solid #ffc107; color: #856404; }
        .info { background: #d1ecf1; border-left: 5px solid #17a2b8; color: #0c5460; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .test-section { margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 5px; }
        input[type="email"], input[type="text"] { padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #dc3545; color: white; padding: 12px 25px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #c82333; }
        pre { background: #f4f4f4; padding: 15px; overflow-x: auto; border-radius: 5px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Diagnostic Tool</h1>
        <p>This tool checks your WordPress email configuration and tests email delivery.</p>

        <?php
        // Test 1: Check WordPress Configuration
        echo '<div class="test-section"><h2>1. WordPress Configuration</h2>';
        
        $checks = [];
        
        // Check WP_DEBUG
        $checks[] = [
            'name' => 'WP_DEBUG',
            'value' => defined('WP_DEBUG') ? (WP_DEBUG ? 'Enabled' : 'Disabled') : 'Not defined',
            'status' => defined('WP_DEBUG') && WP_DEBUG ? 'warning' : 'info'
        ];
        
        // Check DISALLOW_FILE_EDIT
        $checks[] = [
            'name' => 'File Editing',
            'value' => defined('DISALLOW_FILE_EDIT') ? (DISALLOW_FILE_EDIT ? 'Disabled' : 'Enabled') : 'Enabled (default)',
            'status' => 'info'
        ];
        
        foreach ($checks as $check) {
            echo "<div class='status {$check['status']}'><strong>{$check['name']}:</strong> {$check['value']}</div>";
        }
        
        echo '</div>';

        // Test 2: Check PHP Mail Configuration
        echo '<div class="test-section"><h2>2. PHP Mail Configuration</h2>';
        
        $php_checks = [];
        
        // Check mail function exists
        $php_checks[] = [
            'name' => 'PHP mail() function',
            'value' => function_exists('mail') ? 'Available' : 'Not available',
            'status' => function_exists('mail') ? 'success' : 'error'
        ];
        
        // Check wp_mail function
        $php_checks[] = [
            'name' => 'WordPress wp_mail()',
            'value' => function_exists('wp_mail') ? 'Available' : 'Not available',
            'status' => function_exists('wp_mail') ? 'success' : 'error'
        ];
        
        foreach ($php_checks as $check) {
            $status_class = $check['status'];
            echo "<div class='status {$status_class}'><strong>{$check['name']}:</strong> {$check['value']}</div>";
        }
        
        echo '</div>';

        // Test 3: SWPM Plugin Check
        echo '<div class="test-section"><h2>3. Simple Membership Plugin Check</h2>';
        
        $swpm_checks = [];
        
        // Check if SWPM is active
        $swpm_active = class_exists('SwpmMemberUtils');
        $swpm_checks[] = [
            'name' => 'SWPM Plugin Active',
            'value' => $swpm_active ? 'Yes' : 'No',
            'status' => $swpm_active ? 'success' : 'error'
        ];
        
        if ($swpm_active) {
            // Check SWPM email settings
            if (class_exists('SwpmSettings')) {
                $swpm_settings = SwpmSettings::get_instance();
                $swpm_checks[] = [
                    'name' => 'SWPM Settings Class',
                    'value' => 'Available',
                    'status' => 'success'
                ];
            }
            
            // Check for SWPM email hooks
            global $wp_filter;
            $email_hooks = ['swpm_email_sent', 'wp_mail'];
            foreach ($email_hooks as $hook) {
                $has_hook = isset($wp_filter[$hook]) && !empty($wp_filter[$hook]);
                $swpm_checks[] = [
                    'name' => "Hook: {$hook}",
                    'value' => $has_hook ? 'Registered' : 'Not registered',
                    'status' => $has_hook ? 'success' : 'warning'
                ];
            }
        }
        
        foreach ($swpm_checks as $check) {
            $status_class = $check['status'];
            echo "<div class='status {$status_class}'><strong>{$check['name']}:</strong> {$check['value']}</div>";
        }
        
        echo '</div>';

        // Test 4: Email Sending Test
        echo '<div class="test-section"><h2>4. Test Email Sending</h2>';
        
        if (isset($_POST['send_test_email'])) {
            $test_email = sanitize_email($_POST['test_email']);
            $test_subject = sanitize_text_field($_POST['test_subject']);
            
            if (is_email($test_email)) {
                $from_email = get_option('admin_email');
                $from_name = get_option('blogname');
                
                $headers = array(
                    'Content-Type: text/html; charset=UTF-8',
                    "From: {$from_name} <{$from_email}>"
                );
                
                $message = "
                    <html>
                    <head>
                        <title>Email Test from VelvetReel</title>
                    </head>
                    <body>
                        <h2>Email Test Successful!</h2>
                        <p>This is a test email sent from your WordPress site at <strong>" . site_url() . "</strong></p>
                        <p><strong>Test Details:</strong></p>
                        <ul>
                            <li>Sent to: {$test_email}</li>
                            <li>Subject: {$test_subject}</li>
                            <li>Time: " . current_time('mysql') . "</li>
                        </ul>
                        <p>If you received this email, your WordPress email system is working correctly.</p>
                        <hr>
                        <p><small>This email was sent by the Email Diagnostic Tool plugin.</small></p>
                    </body>
                    </html>
                ";
                
                // Try to send email
                $result = wp_mail($test_email, $test_subject, $message, $headers);
                
                if ($result) {
                    echo "<div class='status success'><strong>✓ Email Sent Successfully!</strong><br>The test email was sent to <code>{$test_email}</code>. Please check your inbox (and spam folder).</div>";
                    
                    // Log the successful send
                    error_log("Email Diagnostic: Test email sent successfully to {$test_email}");
                } else {
                    echo "<div class='status error'><strong>✗ Email Failed to Send!</strong><br>WordPress returned false when trying to send email to <code>{$test_email}</code>.</div>";
                    error_log("Email Diagnostic: Failed to send test email to {$test_email}");
                }
            } else {
                echo "<div class='status error'><strong>Invalid Email Address!</strong><br>Please enter a valid email address.</div>";
            }
        }
        
        ?>
        <form method="post" action="">
            <p><strong>Send a test email to verify your email system is working:</strong></p>
            <p>
                <label>To Email: <input type="email" name="test_email" value="<?php echo esc_attr(get_option('admin_email')); ?>" required></label>
            </p>
            <p>
                <label>Subject: <input type="text" name="test_subject" value="VelvetReel Email Test" required></label>
            </p>
            <p>
                <button type="submit" name="send_test_email">Send Test Email</button>
            </p>
        </form>
        <?php
        
        echo '</div>';

        // Test 5: Common Issues
        echo '<div class="test-section"><h2>5. Common Email Issues & Solutions</h2>';
        
        $issues = [];
        
        // Check if on localhost
        $is_local = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || 
                    strpos(site_url(), 'local') !== false;
        
        if ($is_local) {
            $issues[] = [
                'issue' => 'Local Development Environment',
                'description' => 'You are running on a local server. PHP mail() typically does not work on localhost without proper SMTP configuration.',
                'solution' => 'Use an SMTP plugin (like WP Mail SMTP) or configure SMTP settings. Alternatively, use a service like Mailtrap for testing.'
            ];
        }
        
        // Check for SMTP plugins
        $smtp_plugins = [
            'wp-mail-smtp/wp_mail_smtp.php',
            'easy-wp-smtp/easy-wp-smtp.php',
            'post-smtp/post-smtp.php'
        ];
        
        $has_smtp = false;
        foreach ($smtp_plugins as $plugin) {
            if (is_plugin_active($plugin)) {
                $has_smtp = true;
                $issues[] = [
                    'issue' => 'SMTP Plugin Detected',
                    'description' => 'You have an SMTP plugin installed which should handle email delivery.',
                    'solution' => 'Check the SMTP plugin configuration to ensure it\'s properly set up.'
                ];
                break;
            }
        }
        
        if (!$has_smtp && !$is_local) {
            $issues[] = [
                'issue' => 'No SMTP Plugin Installed',
                'description' => 'On production servers, using PHP mail() directly often results in emails going to spam or not being delivered.',
                'solution' => 'Install an SMTP plugin like WP Mail SMTP and configure it with a reliable email service (Gmail, SendGrid, Mailgun, etc.)'
            ];
        }
        
        if (empty($issues)) {
            echo "<div class='status success'>No common issues detected. Your email configuration appears to be correct.</div>";
        } else {
            foreach ($issues as $issue) {
                echo "<div class='status warning'>";
                echo "<strong>⚠ {$issue['issue']}</strong><br>";
                echo "<em>Issue:</em> {$issue['description']}<br>";
                echo "<em>Solution:</em> {$issue['solution']}";
                echo "</div>";
            }
        }
        
        echo '</div>';

        // Test 6: Debug Information
        echo '<div class="test-section"><h2>6. Debug Information</h2>';
        ?>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Site URL</td>
                <td><code><?php echo site_url(); ?></code></td>
            </tr>
            <tr>
                <td>Home URL</td>
                <td><code><?php echo home_url(); ?></code></td>
            </tr>
            <tr>
                <td>Admin Email</td>
                <td><code><?php echo get_option('admin_email'); ?></code></td>
            </tr>
            <tr>
                <td>Server Software</td>
                <td><code><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></code></td>
            </tr>
            <tr>
                <td>PHP Version</td>
                <td><code><?php echo phpversion(); ?></code></td>
            </tr>
            <tr>
                <td>WordPress Version</td>
                <td><code><?php echo $GLOBALS['wp_version']; ?></code></td>
            </tr>
        </table>
        <?php
        
        echo '</div>';
        ?>
    </div>
</body>
</html>
