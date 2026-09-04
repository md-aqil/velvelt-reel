<?php
/**
 * Portfolio Created (Verification Pending) Email Template
 *
 * Variables passed in $args:
 * - $member_name
 * - $talent_title
 * - $verification_url
 * - $fee_amount
 * - $unsubscribe_url
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$site_name        = get_bloginfo('name');
$email_title      = 'Your Portfolio is Created – Complete Verification to Publish';
$email_preheader  = 'Your talent portfolio is created! Finish the one-time $5 verification to publish and get discovered.';
$badge_text       = 'PORTFOLIO CREATED • VERIFICATION PENDING';
$verification_url = !empty($verification_url) ? $verification_url : home_url('/talent/');
$fee_amount       = !empty($fee_amount) ? $fee_amount : '$5';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 18px; color: #ffffff !important; font-weight: 700;">
    Hi <?php echo esc_html(!empty($member_name) ? $member_name : 'there'); ?>,
</p>

<p style="margin: 0 0 20px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    Congratulations! Your talent portfolio has been successfully created on <strong style="color: #ffffff !important;"><?php echo esc_html($site_name); ?></strong>.
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    You're just one quick step away from having your profile verified and published to top casting directors, advertisers, and recruiters.
</p>

<!-- Verification Status & Perks Card -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td bgcolor="#17171c" style="padding: 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            
            <!-- One Step Notice -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 18px; background-color: #221317; background: #221317; border-left: 3px solid #DF1D3D; border-radius: 6px; padding: 12px 16px;">
                <tr>
                    <td>
                        <strong style="color: #ffffff !important; font-size: 14px; display: block; margin-bottom: 4px;">
                            ⚡ One-Time Verification Fee: <?php echo esc_html($fee_amount); ?>
                        </strong>
                        <span style="font-size: 13px; color: #d4d4d8 !important; line-height: 1.5;">
                            Complete the one-time verification fee to lock in your portfolio submission and fast-track priority review.
                        </span>
                    </td>
                </tr>
            </table>

            <h3 style="margin: 0 0 16px 0; font-size: 15px; color: #ffffff !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                Why Verify Your Portfolio?
            </h3>

            <!-- Perk 1 -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 14px;">
                <tr>
                    <td valign="top" style="width: 24px; padding-right: 12px;">
                        <span style="color: #DF1D3D !important; font-size: 16px; font-weight: bold;">✓</span>
                    </td>
                    <td valign="top">
                        <strong style="color: #ffffff !important; font-size: 14px; display: block;">Official Verified Talent Badge</strong>
                        <p style="margin: 2px 0 0 0; font-size: 13px; color: #a1a1aa !important; line-height: 1.5;">
                            Boost your trust and credibility with hiring teams looking for verified creatives.
                        </p>
                    </td>
                </tr>
            </table>

            <!-- Perk 2 -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 14px;">
                <tr>
                    <td valign="top" style="width: 24px; padding-right: 12px;">
                        <span style="color: #DF1D3D !important; font-size: 16px; font-weight: bold;">✓</span>
                    </td>
                    <td valign="top">
                        <strong style="color: #ffffff !important; font-size: 14px; display: block;">Priority Admin Review & Publishing</strong>
                        <p style="margin: 2px 0 0 0; font-size: 13px; color: #a1a1aa !important; line-height: 1.5;">
                            Your portfolio gets queued for fast-track verification by our curating team.
                        </p>
                    </td>
                </tr>
            </table>

            <!-- Perk 3 -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td valign="top" style="width: 24px; padding-right: 12px;">
                        <span style="color: #DF1D3D !important; font-size: 16px; font-weight: bold;">✓</span>
                    </td>
                    <td valign="top">
                        <strong style="color: #ffffff !important; font-size: 14px; display: block;">Live Discovery in Search Grid</strong>
                        <p style="margin: 2px 0 0 0; font-size: 13px; color: #a1a1aa !important; line-height: 1.5;">
                            Showcase your headshots, bio, and work links across the global VelvetReel talent directory.
                        </p>
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
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" bgcolor="#DF1D3D" style="border-radius: 8px; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%);">
                        <a href="<?php echo esc_url($verification_url); ?>" target="_blank" style="display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 700; color: #ffffff !important; text-decoration: none; border-radius: 8px; letter-spacing: 0.5px;">
                            Complete Verification Now &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p style="margin: 0; font-size: 13px; color: #a1a1aa !important; text-align: center;">
    Already completed your payment? Please allow a short time for our team to review and activate your badge. Visit your <a href="<?php echo esc_url(home_url('/talent/')); ?>" style="color: #ffffff !important; text-decoration: underline;">Portfolio Dashboard</a> to check current status.
</p>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
