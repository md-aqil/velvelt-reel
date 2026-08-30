<?php
/**
 * Advertiser Interest Notification Email Template
 *
 * Variables passed in $args:
 * - $advertiser_name
 * - $ad_title
 * - $ad_url
 * - $applicant_name
 * - $applicant_username
 * - $applicant_email
 * - $applicant_phone
 * - $applicant_role
 * - $applicant_location
 * - $applicant_age_group
 * - $applicant_years_active
 * - $applicant_profile_url
 * - $submission_time
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$email_title     = 'New Interest: ' . $ad_title;
$email_preheader = $applicant_name . ' has expressed interest in your classified listing on VelvetReel.';
$badge_text      = 'NEW CANDIDATE';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 16px; color: #ffffff !important; font-weight: 600;">
    Hello <?php echo esc_html(!empty($advertiser_name) ? $advertiser_name : 'Advertiser'); ?>,
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    Great news! A member has just expressed interest in your classified: <strong style="color: #ffffff !important;"><?php echo esc_html($ad_title); ?></strong>.
</p>

<!-- Candidate Profile Card -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td bgcolor="#17171c" style="padding: 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="padding-bottom: 16px; border-bottom: 1px solid #24242e;">
                        <span style="font-size: 11px; font-weight: 700; color: #DF1D3D !important; text-transform: uppercase; letter-spacing: 1px;">
                            Applicant Overview
                        </span>
                        <h2 style="margin: 6px 0 0 0; font-size: 22px; font-weight: 700; color: #ffffff !important;">
                            <?php echo esc_html($applicant_name); ?>
                        </h2>
                        <?php if (!empty($applicant_role)) : ?>
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #a1a1aa !important;">
                            <?php echo esc_html($applicant_role); ?>
                        </p>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Details Grid -->
                <tr>
                    <td style="padding-top: 16px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <?php if (!empty($applicant_email)) : ?>
                            <tr>
                                <td style="padding: 6px 0; font-size: 14px; color: #a1a1aa !important; width: 35%;">
                                    <strong style="color: #ffffff !important;">Email:</strong>
                                </td>
                                <td style="padding: 6px 0; font-size: 14px; color: #e4e4e7 !important;">
                                    <a href="mailto:<?php echo esc_attr($applicant_email); ?>" style="color: #DF1D3D !important;">
                                        <?php echo esc_html($applicant_email); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endif; ?>

                            <?php if (!empty($applicant_phone) && $applicant_phone !== 'Not provided') : ?>
                            <tr>
                                <td style="padding: 6px 0; font-size: 14px; color: #a1a1aa !important;">
                                    <strong style="color: #ffffff !important;">Phone:</strong>
                                </td>
                                <td style="padding: 6px 0; font-size: 14px; color: #e4e4e7 !important;">
                                    <a href="tel:<?php echo esc_attr($applicant_phone); ?>" style="color: #ffffff !important; text-decoration: none;">
                                        <?php echo esc_html($applicant_phone); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php endif; ?>

                            <?php if (!empty($applicant_location)) : ?>
                            <tr>
                                <td style="padding: 6px 0; font-size: 14px; color: #a1a1aa !important;">
                                    <strong style="color: #ffffff !important;">Location:</strong>
                                </td>
                                <td style="padding: 6px 0; font-size: 14px; color: #e4e4e7 !important;">
                                    <?php echo esc_html($applicant_location); ?>
                                </td>
                            </tr>
                            <?php endif; ?>

                            <?php if (!empty($applicant_age_group)) : ?>
                            <tr>
                                <td style="padding: 6px 0; font-size: 14px; color: #a1a1aa !important;">
                                    <strong style="color: #ffffff !important;">Age Group:</strong>
                                </td>
                                <td style="padding: 6px 0; font-size: 14px; color: #e4e4e7 !important;">
                                    <?php echo esc_html($applicant_age_group); ?>
                                </td>
                            </tr>
                            <?php endif; ?>

                            <?php if (!empty($applicant_years_active)) : ?>
                            <tr>
                                <td style="padding: 6px 0; font-size: 14px; color: #a1a1aa !important;">
                                    <strong style="color: #ffffff !important;">Experience:</strong>
                                </td>
                                <td style="padding: 6px 0; font-size: 14px; color: #e4e4e7 !important;">
                                    <?php echo esc_html($applicant_years_active); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            
                            <tr>
                                <td style="padding: 6px 0; font-size: 14px; color: #a1a1aa !important;">
                                    <strong style="color: #ffffff !important;">Submitted:</strong>
                                </td>
                                <td style="padding: 6px 0; font-size: 14px; color: #a1a1aa !important;">
                                    <?php echo esc_html(!empty($submission_time) ? $submission_time : date('M j, Y g:i A')); ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Action CTA Buttons -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
    <tr>
        <td align="center">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <?php if (!empty($applicant_profile_url)) : ?>
                    <td align="center" style="padding: 4px;">
                        <a href="<?php echo esc_url($applicant_profile_url); ?>" target="_blank" style="display: inline-block; padding: 13px 26px; font-size: 14px; font-weight: 700; color: #ffffff !important; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); text-decoration: none; border-radius: 8px;">
                            Review Full Talent Profile &rarr;
                        </a>
                    </td>
                    <?php endif; ?>
                    <td align="center" style="padding: 4px;">
                        <a href="<?php echo esc_url($ad_url); ?>" target="_blank" style="display: inline-block; padding: 13px 24px; font-size: 14px; font-weight: 700; color: #ffffff !important; background-color: #24242e; text-decoration: none; border-radius: 8px; border: 1px solid #333340;">
                            View Your Ad
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p style="margin: 0; font-size: 13px; color: #a1a1aa !important; text-align: center; line-height: 1.5;">
    You can get in touch with <?php echo esc_html($applicant_name); ?> directly using the contact information provided above.
</p>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
