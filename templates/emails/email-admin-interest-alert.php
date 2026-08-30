<?php
/**
 * Admin Interest Alert Email Template
 *
 * Variables passed in $args:
 * - $ad_title
 * - $ad_id
 * - $ad_url
 * - $advertiser_name
 * - $advertiser_email
 * - $applicant_name
 * - $applicant_username
 * - $applicant_email
 * - $applicant_phone
 * - $applicant_profile_url
 * - $submission_time
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$email_title     = '[Admin Alert] New Interest: ' . $ad_title;
$email_preheader = 'A new interest submission was recorded for classified #' . $ad_id;
$badge_text      = 'ADMIN ALERT';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Title -->
<p style="margin: 0 0 16px 0; font-size: 16px; color: #ffffff !important; font-weight: 600;">
    Admin Notification &bull; New Classified Application
</p>

<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 24px;">
    <tr>
        <td bgcolor="#17171c" style="padding: 20px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="font-size: 13px; color: #a1a1aa !important; padding-bottom: 6px; width: 35%;"><strong style="color: #ffffff !important;">Classified:</strong></td>
                    <td style="font-size: 13px; color: #ffffff !important; padding-bottom: 6px;">
                        <a href="<?php echo esc_url($ad_url); ?>" style="color: #DF1D3D !important;"><?php echo esc_html($ad_title); ?></a> (#<?php echo esc_html($ad_id); ?>)
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 13px; color: #a1a1aa !important; padding-bottom: 6px;"><strong style="color: #ffffff !important;">Advertiser:</strong></td>
                    <td style="font-size: 13px; color: #e4e4e7 !important; padding-bottom: 6px;">
                        <?php echo esc_html($advertiser_name); ?> (<?php echo esc_html($advertiser_email); ?>)
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 13px; color: #a1a1aa !important; padding-bottom: 6px;"><strong style="color: #ffffff !important;">Applicant:</strong></td>
                    <td style="font-size: 13px; color: #e4e4e7 !important; padding-bottom: 6px;">
                        <?php echo esc_html($applicant_name); ?> (@<?php echo esc_html($applicant_username); ?>)
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 13px; color: #a1a1aa !important; padding-bottom: 6px;"><strong style="color: #ffffff !important;">Applicant Email:</strong></td>
                    <td style="font-size: 13px; color: #e4e4e7 !important; padding-bottom: 6px;">
                        <?php echo esc_html($applicant_email); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 13px; color: #a1a1aa !important; padding-bottom: 6px;"><strong style="color: #ffffff !important;">Applicant Phone:</strong></td>
                    <td style="font-size: 13px; color: #e4e4e7 !important; padding-bottom: 6px;">
                        <?php echo esc_html($applicant_phone); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 13px; color: #a1a1aa !important;"><strong style="color: #ffffff !important;">Timestamp:</strong></td>
                    <td style="font-size: 13px; color: #a1a1aa !important;">
                        <?php echo esc_html(!empty($submission_time) ? $submission_time : date('M j, Y g:i A')); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php if (!empty($applicant_profile_url)) : ?>
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
    <tr>
        <td align="center">
            <a href="<?php echo esc_url($applicant_profile_url); ?>" target="_blank" style="display: inline-block; padding: 11px 22px; font-size: 13px; font-weight: 700; color: #ffffff !important; background-color: #22222a; border: 1px solid #383848; text-decoration: none; border-radius: 6px;">
                View Talent Profile &rarr;
            </a>
        </td>
    </tr>
</table>
<?php endif; ?>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
