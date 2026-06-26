# Registration Email Troubleshooting Guide

## Issue Summary
New registration confirmation emails are not being sent to users who register through the Simple Membership Plugin (SWPM).

## Current Setup Analysis

### 1. Registration Flow
Your site has **two separate registration systems**:

#### A. Custom Sign-Up Form (`/sign-up/`)
- Located in: `sign-up.php`
- Sends OTP verification email using `wp_mail()` (lines 93-98)
- Uses custom email sending logic
- **This system appears to be working** based on the code

#### B. Simple Membership Plugin (SWPM) Registration
- Uses SWPM plugin's built-in email system
- Handled by WordPress hooks in `includes/swpm-registration-handler.php`
- Shows completion message about email link (line 103)
- **This is likely where the issue exists**

### 2. Email Sending Methods Found

```php
// Custom sign-up uses wp_mail() directly
wp_mail($email, $subject, $message, $headers);

// SWPM uses its internal email system (not visible in your theme code)
```

## Potential Causes

### 1. **Local Development Environment** ⚠️ MOST LIKELY
You're running on Local by Flywheel (`/Users/mdaqil/Local Sites/velvet3/`).

**Problem:** PHP's `mail()` function and even `wp_mail()` typically **do not work** on localhost without proper SMTP configuration.

**Solution Options:**
- Install an SMTP plugin (WP Mail SMTP, Easy WP SMTP, Post SMTP)
- Configure Local by Flywheel's email settings
- Use a service like Mailtrap.io for testing
- Deploy to production server where email is configured

### 2. **SWPM Email Configuration**
The Simple Membership Plugin has its own email settings.

**Check:**
- Go to **WordPress Admin → Simple Membership → Settings → Email Settings**
- Verify "From Email" and "From Name" are set
- Check if email notifications are enabled
- Ensure the registration completion email template is configured

### 3. **Email Going to Spam**
Confirmation emails might be delivered but landing in spam/junk folders.

**Check:**
- Ask test users to check their spam folder
- Use a proper SMTP service with authentication
- Set up SPF/DKIM records for your domain

### 4. **Plugin Conflict or Hook Issue**
The SWPM hooks might not be firing correctly.

**Evidence in your code:**
```php
// From swpm-registration-handler.php line 201
add_action('swpm_front_end_registration_complete', 'handle_swpm_payment_on_registration_fixed', 10, 1);
```

This hook is properly registered, but the email sending is handled by SWPM core, not your theme.

## Diagnostic Steps

### Step 1: Use the Email Diagnostic Tool
I've created a diagnostic tool at:
`/wp-content/themes/hello-elementor-child/test-email-diagnostic.php`

**How to use:**
1. Log into WordPress as admin
2. Navigate to: `http://your-local-site/wp-content/themes/hello-elementor-child/test-email-diagnostic.php`
3. Send a test email
4. Review the results

### Step 2: Check SWPM Settings
1. Go to **WordPress Admin → Simple Membership → Settings**
2. Click on **Email Settings** tab
3. Verify all email settings are configured
4. Check email templates for registration completion

### Step 3: Enable Debug Logging
Add these lines to `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
```

Then check `/wp-content/debug.log` after a test registration.

### Step 4: Test SWPM Email Directly
Add this temporary debug code to see if SWPM email hooks are firing:

```php
// Add to functions.php temporarily
add_action('swpm_front_end_registration_complete', 'debug_swpm_email', 10, 1);
function debug_swpm_email($user_data) {
    error_log('SWPM Registration Complete - User Data: ' . print_r($user_data, true));
    
    // Try to send a manual test email
    if (is_array($user_data) && isset($user_data['email'])) {
        $test_email = $user_data['email'];
        $test_message = "Test email from SWPM registration. User ID: {$user_data['member_id']}";
        wp_mail($test_email, 'SWPM Registration Test', $test_message);
        error_log("Manual test email sent to: {$test_email}");
    }
}
```

### Step 5: Check Server/Local Email Logs
If using Local by Flywheel:
1. Open Local application
2. Check the site's mailhog or email logs
3. See if emails are being queued/sent

## Recommended Solutions

### For Local Development:
1. **Use Mailtrap** (Free for testing):
   - Sign up at mailtrap.io
   - Get SMTP credentials
   - Install WP Mail SMTP plugin
   - Configure with Mailtrap SMTP

2. **Use Local's Built-in Tools**:
   - Local by Flywheel has email testing features
   - Check the application's email tab

### For Production:
1. **Install SMTP Plugin**:
   - WP Mail SMTP (most popular)
   - Configure with:
     - Gmail/Google Workspace
     - SendGrid
     - Mailgun
     - Your hosting provider's SMTP

2. **Configure DNS Records**:
   - SPF record
   - DKIM record
   - These improve email deliverability

## Quick Test Code

If you want to immediately test if `wp_mail()` works, create this test file:

```php
<?php
/**
 * Quick Email Test
 * Access: your-site.com/wp-content/themes/hello-elementor-child/quick-email-test.php
 */
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    wp_die('Admin access required');
}

$test_email = get_option('admin_email');
$subject = 'Quick Email Test - ' . date('Y-m-d H:i:s');
$message = 'If you received this, wp_mail() is working!';
$headers = ['Content-Type: text/plain; charset=UTF-8'];

$result = wp_mail($test_email, $subject, $message, $headers);

echo '<h1>Email Test Result</h1>';
echo $result ? 
    '<p style="color:green;">✓ SUCCESS! Email sent to: ' . esc_html($test_email) . '</p>' :
    '<p style="color:red;">✗ FAILED! Check your email configuration.</p>';
?>
```

## Files Involved

Based on your codebase, these files handle registration and email:

1. **Custom Registration:**
   - `sign-up.php` - Custom sign-up form with OTP email
   - `verify.email.php` - Email verification page

2. **SWPM Registration:**
   - `includes/swpm-registration-handler.php` - SWPM payment and alert handling
   - `includes/registration-handler.php` - Alternative SWPM handler
   - `functions.php` - SWPM form modifications (lines 87-1905)

3. **Email Testing:**
   - `test-email-diagnostic.php` - Comprehensive email diagnostic tool (NEW)

## Next Steps

1. ✅ Run the email diagnostic tool
2. ✅ Check if you're on localhost (likely the issue)
3. ✅ Install and configure an SMTP plugin
4. ✅ Test SWPM registration with a real email address
5. ✅ Check spam folders
6. ✅ Review SWPM email settings in WordPress admin

## Need More Help?

If the issue persists after trying these steps:
1. Check SWPM plugin documentation for email configuration
2. Contact your hosting provider about email delivery
3. Consider using a transactional email service (SendGrid, Mailgun, Amazon SES)
