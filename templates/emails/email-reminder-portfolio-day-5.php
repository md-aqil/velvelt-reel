<?php
/**
 * Day 5 Portfolio Reminder Email Template
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
$email_title     = 'Don\'t miss out on casting calls on VelvetReel';
$email_preheader = 'Recruiters are searching for talents in your area. Finish setting up your portfolio.';
$badge_text      = 'ACTION NEEDED';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 18px; color: #ffffff !important; font-weight: 700;">
    Hello <?php echo esc_html(!empty($member_name) ? $member_name : 'there'); ?>,
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    New casting calls, crew requirements, and creative collaborations are published daily on <strong style="color: #ffffff !important;"><?php echo esc_html($site_name); ?></strong>.
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    However, without a published talent portfolio, your profile cannot be matched to incoming classifieds or discovered by hiring teams in our talent search grid.
</p>

<!-- Checklist Box -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td bgcolor="#17171c" style="padding: 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <h3 style="margin: 0 0 14px 0; font-size: 15px; color: #DF1D3D !important; font-weight: 700; text-transform: uppercase;">
                Your Setup Checklist:
            </h3>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 14px; color: #d4d4d8 !important;">
                <tr>
                    <td style="padding: 6px 0; color: #d4d4d8 !important;">⚪ <strong style="color: #ffffff !important;">Headshot & Gallery:</strong> Upload 1–3 clear photos</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #d4d4d8 !important;">⚪ <strong style="color: #ffffff !important;">Primary Role:</strong> Select your acting, modeling, or crew specialty</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #d4d4d8 !important;">⚪ <strong style="color: #ffffff !important;">Location & Bio:</strong> Tell recruiters where you're based and what you do</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Action CTA Button -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
    <tr>
        <td align="center">
            <a href="<?php echo esc_url(!empty($profile_edit_url) ? $profile_edit_url : home_url('/talent/')); ?>" target="_blank" style="display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 700; color: #ffffff !important; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); text-decoration: none; border-radius: 8px;">
                Finish & Publish Profile &rarr;
            </a>
        </td>
    </tr>
</table>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
