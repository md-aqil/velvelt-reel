<?php
/**
 * Targeted Classified Posting Email Template
 *
 * Variables passed in $args:
 * - $recipient_name
 * - $ad_title
 * - $ad_url
 * - $ad_category
 * - $ad_location
 * - $ad_type
 * - $ad_budget
 * - $ad_excerpt
 * - $ad_image_url
 * - $matching_reasons
 * - $unsubscribe_url
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$email_title     = 'New Opportunity: ' . $ad_title;
$email_preheader = 'A new classified matching your talent profile has just been published on VelvetReel.';
$badge_text      = !empty($ad_type) ? strtoupper($ad_type) : 'NEW OPPORTUNITY';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 16px; color: #ffffff !important; font-weight: 600;">
    Hello <?php echo esc_html(!empty($recipient_name) ? $recipient_name : 'there'); ?>,
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    A new classified listing that matches your profile and preferences was just published on VelvetReel.
</p>

<!-- Opportunity Hero Box -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <?php if (!empty($ad_image_url)) : ?>
    <tr>
        <td bgcolor="#17171c" style="padding: 0;">
            <img src="<?php echo esc_url($ad_image_url); ?>" alt="<?php echo esc_attr($ad_title); ?>" width="100%" style="width: 100%; max-height: 220px; object-fit: cover; display: block; border-bottom: 1px solid #282832;" />
        </td>
    </tr>
    <?php endif; ?>
    <tr>
        <td bgcolor="#17171c" style="padding: 24px 24px 20px 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <?php if (!empty($ad_category)) : ?>
                <tr>
                    <td>
                        <span style="font-size: 11px; font-weight: 700; color: #DF1D3D !important; text-transform: uppercase; letter-spacing: 1px;">
                            <?php echo esc_html($ad_category); ?>
                        </span>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="padding-top: 8px; padding-bottom: 14px;">
                        <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #ffffff !important; line-height: 1.4;">
                            <a href="<?php echo esc_url($ad_url); ?>" style="color: #ffffff !important; text-decoration: none;">
                                <?php echo esc_html($ad_title); ?>
                            </a>
                        </h2>
                    </td>
                </tr>
                <?php if (!empty($ad_excerpt)) : ?>
                <tr>
                    <td style="padding-bottom: 18px; font-size: 14px; color: #d4d4d8 !important; line-height: 1.6;">
                        <?php echo wp_kses_post($ad_excerpt); ?>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- Specs Grid -->
                <tr>
                    <td style="padding-top: 12px; border-top: 1px solid #24242e;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <?php if (!empty($ad_location)) : ?>
                                <td align="left" style="font-size: 13px; color: #a1a1aa !important; padding: 4px 0;">
                                    <strong style="color: #ffffff !important;">📍 Location:</strong> <?php echo esc_html($ad_location); ?>
                                </td>
                                <?php endif; ?>
                                <?php if (!empty($ad_budget)) : ?>
                                <td align="right" style="font-size: 13px; color: #a1a1aa !important; padding: 4px 0;">
                                    <strong style="color: #ffffff !important;">💰 Compensation:</strong> <?php echo esc_html($ad_budget); ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php if (!empty($matching_reasons) && is_array($matching_reasons)) : ?>
<!-- Match Criteria Tags -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
    <tr>
        <td style="font-size: 12px; color: #a1a1aa !important; padding-bottom: 8px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">
            Why you matched:
        </td>
    </tr>
    <tr>
        <td>
            <?php foreach ($matching_reasons as $reason) : ?>
                <span style="display: inline-block; padding: 4px 10px; margin: 0 6px 6px 0; background-color: #202028; border: 1px solid #2e2e3a; border-radius: 6px; font-size: 12px; color: #ffffff !important;">
                    ✓ <?php echo esc_html($reason); ?>
                </span>
            <?php endforeach; ?>
        </td>
    </tr>
</table>
<?php endif; ?>

<!-- Action CTA Button -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 28px;">
    <tr>
        <td align="center">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" bgcolor="#DF1D3D" style="border-radius: 8px; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%);">
                        <a href="<?php echo esc_url($ad_url); ?>" target="_blank" style="display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 700; color: #ffffff !important; text-decoration: none; border-radius: 8px; letter-spacing: 0.5px;">
                            View Details & Apply Now &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p style="margin: 0; font-size: 13px; color: #a1a1aa !important; text-align: center;">
    Be among the first to express interest to secure consideration from the advertiser.
</p>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
