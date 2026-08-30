<?php
/**
 * Day 10 Subscription Reminder Email Template
 *
 * Variables passed in $args:
 * - $member_name
 * - $upgrade_url
 * - $unsubscribe_url
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$site_name       = get_bloginfo('name');
$email_title     = 'Unlock Full Casting Access with VelvetReel Membership';
$email_preheader = 'Upgrade your plan to apply to unlimited casting calls, get verified, and connect with directors.';
$badge_text      = 'PREMIUM ACCESS';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 18px; color: #ffffff !important; font-weight: 700;">
    Hello <?php echo esc_html(!empty($member_name) ? $member_name : 'there'); ?>,
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    Ready to take your creative career to the next level? Upgrade to a <strong style="color: #ffffff !important;">VelvetReel Membership</strong> to unlock direct casting applications and high-visibility discovery.
</p>

<!-- Feature Comparison Box -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td bgcolor="#17171c" style="padding: 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; color: #ffffff !important; font-weight: 700;">
                What You Get With VelvetReel Premium:
            </h3>

            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 14px; color: #d4d4d8 !important;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #24242e; color: #d4d4d8 !important;">
                        <span style="color: #DF1D3D !important; font-weight: bold; margin-right: 8px;">✓</span> <strong style="color: #ffffff !important;">Instant 1-Click Applications</strong> on all Classifieds & Casting Calls
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #24242e; color: #d4d4d8 !important;">
                        <span style="color: #DF1D3D !important; font-weight: bold; margin-right: 8px;">✓</span> <strong style="color: #ffffff !important;">Verified Talent Badge</strong> for credibility with top brands
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #24242e; color: #d4d4d8 !important;">
                        <span style="color: #DF1D3D !important; font-weight: bold; margin-right: 8px;">✓</span> <strong style="color: #ffffff !important;">Priority Grid Placement</strong> in directory searches
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #d4d4d8 !important;">
                        <span style="color: #DF1D3D !important; font-weight: bold; margin-right: 8px;">✓</span> <strong style="color: #ffffff !important;">Direct Contact Access</strong> to hiring teams & advertisers
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Action CTA Button -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
    <tr>
        <td align="center">
            <a href="<?php echo esc_url(!empty($upgrade_url) ? $upgrade_url : home_url('/membership-login/')); ?>" target="_blank" style="display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 700; color: #ffffff !important; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); text-decoration: none; border-radius: 8px;">
                Explore Membership Plans &rarr;
            </a>
        </td>
    </tr>
</table>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
