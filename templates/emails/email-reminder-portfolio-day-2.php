<?php
/**
 * Day 2 Portfolio Reminder Email Template
 *
 * Variables passed in $args:
 * - $member_name
 * - $profile_edit_url
 * - $unsubscribe_url
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$site_name       = get_bloginfo('name');
$email_title     = 'Complete your portfolio on VelvetReel – Opportunities are waiting';
$email_preheader = 'Your profile is almost ready! Add your photos and skills to get discovered by recruiters.';
$badge_text      = 'PORTFOLIO REMINDER';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 18px; color: #ffffff !important; font-weight: 700;">
    Hi <?php echo esc_html(!empty($member_name) ? $member_name : 'there'); ?>,
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    We noticed you registered on <strong style="color: #ffffff !important;"><?php echo esc_html($site_name); ?></strong>, but haven't finished completing your talent portfolio yet.
</p>

<!-- Benefit Box -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td bgcolor="#17171c" style="padding: 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <h3 style="margin: 0 0 12px 0; font-size: 16px; color: #ffffff !important; font-weight: 700;">
                Why a complete portfolio matters:
            </h3>
            <ul style="margin: 0; padding-left: 20px; font-size: 14px; color: #d4d4d8 !important; line-height: 1.8;">
                <li><strong style="color: #ffffff !important;">7x More Profile Views:</strong> Casting agents filter for profiles with complete photos, bio, and role details.</li>
                <li><strong style="color: #ffffff !important;">Targeted Classified Alerts:</strong> We match new casting calls directly to your specified discipline and location.</li>
                <li><strong style="color: #ffffff !important;">Instant Applications:</strong> Express interest in active listings with one click once your details are saved.</li>
            </ul>
        </td>
    </tr>
</table>

<!-- Action CTA Button -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
    <tr>
        <td align="center">
            <a href="<?php echo esc_url(!empty($profile_edit_url) ? $profile_edit_url : home_url('/submit-talent/')); ?>" target="_blank" style="display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 700; color: #ffffff !important; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); text-decoration: none; border-radius: 8px;">
                Complete My Profile in 3 Minutes &rarr;
            </a>
        </td>
    </tr>
</table>

<p style="margin: 0; font-size: 13px; color: #a1a1aa !important; text-align: center;">
    Takes only a few minutes. Upload your best headshot and your primary discipline.
</p>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
