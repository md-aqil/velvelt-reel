<?php
/**
 * OTP Email Verification Template
 *
 * Variables passed in $args:
 * - $otp_code
 * - $recipient_name
 * - $verification_type ('signup', 'reset_password', 'general')
 * - $expiry_minutes
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$site_name       = get_bloginfo('name');
$expiry_mins     = !empty($expiry_minutes) ? $expiry_minutes : 10;
$type            = !empty($verification_type) ? $verification_type : 'signup';

if ($type === 'reset_password') {
    $email_title     = 'Your Password Reset Verification Code';
    $email_preheader = 'Use verification code ' . $otp_code . ' to reset your VelvetReel password.';
    $badge_text      = 'PASSWORD RESET';
} else {
    $email_title     = 'Verify Your Email Address – ' . $site_name;
    $email_preheader = 'Your 6-digit VelvetReel verification code is ' . $otp_code . '. Complete your registration now.';
    $badge_text      = 'VERIFICATION CODE';
}

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 17px; color: #ffffff !important; font-weight: 700;">
    Hello <?php echo esc_html(!empty($recipient_name) ? $recipient_name : 'there'); ?>,
</p>

<?php if ($type === 'reset_password') : ?>
<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    We received a request to reset the password for your <strong style="color: #ffffff !important;"><?php echo esc_html($site_name); ?></strong> account. Enter the verification code below to proceed:
</p>
<?php else : ?>
<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    Thank you for registering on <strong style="color: #ffffff !important;"><?php echo esc_html($site_name); ?></strong>! To complete your registration and activate your account, please enter the 6-digit verification code below:
</p>
<?php endif; ?>

<!-- OTP Highlight Box -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td align="center" bgcolor="#17171c" style="padding: 32px 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <span style="font-size: 12px; color: #a1a1aa !important; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; display: block; margin-bottom: 12px;">
                Your Verification Code
            </span>
            <div style="display: inline-block; background: #0c0c0e; background-color: #0c0c0e; border: 2px dashed #DF1D3D; border-radius: 10px; padding: 14px 28px; font-family: 'Courier New', Courier, monospace; font-size: 32px; font-weight: 800; color: #DF1D3D !important; letter-spacing: 8px; text-align: center;">
                <?php echo esc_html($otp_code); ?>
            </div>
            <p style="margin: 16px 0 0 0; font-size: 13px; color: #a1a1aa !important;">
                ⏳ This code will expire in <strong style="color: #ffffff !important;"><?php echo esc_html($expiry_mins); ?> minutes</strong>.
            </p>
        </td>
    </tr>
</table>

<!-- Security Tip -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#121215" style="background-color: #121215; background: #121215; background-image: linear-gradient(#121215, #121215); border-radius: 8px; border-left: 3px solid #71717a; margin-bottom: 24px;">
    <tr>
        <td bgcolor="#121215" style="padding: 16px 20px; background-color: #121215; background-image: linear-gradient(#121215, #121215);">
            <p style="margin: 0; font-size: 13px; color: #a1a1aa !important; line-height: 1.5;">
                <strong style="color: #ffffff !important;">Security Notice:</strong> Never share this code with anyone. VelvetReel administrators will never ask for your verification code.
            </p>
        </td>
    </tr>
</table>

<p style="margin: 0; font-size: 13px; color: #71717a !important; text-align: center;">
    If you did not initiate this request, you can safely ignore this email.
</p>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
