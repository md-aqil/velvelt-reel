<?php
/**
 * Member Interest Confirmation Email Template
 *
 * Variables passed in $args:
 * - $member_name
 * - $ad_title
 * - $ad_url
 * - $ad_category
 * - $ad_location
 * - $submission_time
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$email_title     = 'Application Confirmed: ' . $ad_title;
$email_preheader = 'Your interest for ' . $ad_title . ' has been delivered to the advertiser.';
$badge_text      = 'APPLICATION SUBMITTED';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 18px; color: #ffffff !important; font-weight: 700;">
    Hello <?php echo esc_html(!empty($member_name) ? $member_name : 'there'); ?>,
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    Your interest for the classified listing <strong style="color: #ffffff !important;"><?php echo esc_html($ad_title); ?></strong> has been successfully registered and forwarded to the advertiser.
</p>

<!-- Confirmation Card -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td bgcolor="#17171c" style="padding: 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td>
                        <span style="display: inline-block; padding: 4px 10px; background-color: #14281a; background: rgba(34, 197, 94, 0.12); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 20px; font-size: 11px; font-weight: 700; color: #22c55e !important; text-transform: uppercase; letter-spacing: 1px;">
                            ✓ Dispatched to Advertiser
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 12px; padding-bottom: 12px;">
                        <h2 style="margin: 0; font-size: 18px; font-weight: 700; color: #ffffff !important;">
                            <?php echo esc_html($ad_title); ?>
                        </h2>
                    </td>
                </tr>
                <?php if (!empty($ad_location)) : ?>
                <tr>
                    <td style="padding-bottom: 6px; font-size: 13px; color: #a1a1aa !important;">
                        <strong style="color: #ffffff !important;">Location:</strong> <?php echo esc_html($ad_location); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="font-size: 13px; color: #a1a1aa !important;">
                        <strong style="color: #ffffff !important;">Submitted on:</strong> <?php echo esc_html(!empty($submission_time) ? $submission_time : date('M j, Y g:i A')); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- What Happens Next -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#121215" style="background-color: #121215; background: #121215; background-image: linear-gradient(#121215, #121215); border-radius: 8px; border-left: 3px solid #DF1D3D; margin-bottom: 28px;">
    <tr>
        <td bgcolor="#121215" style="padding: 16px 20px; background-color: #121215; background-image: linear-gradient(#121215, #121215);">
            <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 700; color: #ffffff !important;">What happens next?</h3>
            <p style="margin: 0; font-size: 13px; color: #d4d4d8 !important; line-height: 1.5;">
                The advertiser will review your profile and reach out directly to your contact details if your profile matches their requirements.
            </p>
        </td>
    </tr>
</table>

<!-- Action CTA Button -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
    <tr>
        <td align="center">
            <a href="<?php echo esc_url($ad_url); ?>" target="_blank" style="display: inline-block; padding: 13px 28px; font-size: 14px; font-weight: 700; color: #ffffff !important; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); text-decoration: none; border-radius: 8px;">
                Revisit Classified Listing &rarr;
            </a>
        </td>
    </tr>
</table>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
