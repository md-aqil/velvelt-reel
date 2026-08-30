<?php
/**
 * Company Admin Contact Alert Email Template
 *
 * Variables passed in $args:
 * - $sender_name
 * - $sender_email
 * - $sender_phone
 * - $sender_subject
 * - $sender_message
 * - $submission_time
 * - $sender_ip
 * - $reference_id
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$site_name       = get_bloginfo('name');
$email_title     = '[Contact Us] ' . (!empty($sender_subject) ? $sender_subject : 'New Inquiry from ' . $sender_name);
$email_preheader = 'New contact inquiry received from ' . $sender_name . ' (' . $sender_email . ')';
$badge_text      = 'NEW INQUIRY';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Title -->
<p style="margin: 0 0 16px 0; font-size: 16px; color: #ffffff !important; font-weight: 600;">
    New Contact Us Form Submission
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    A visitor has submitted an inquiry via the VelvetReel Contact Us form. Details are below:
</p>

<!-- Message & Contact Details Table -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td bgcolor="#17171c" style="padding: 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <?php if (!empty($reference_id)) : ?>
                <tr>
                    <td style="font-size: 12px; color: #a1a1aa !important; padding-bottom: 12px;" colspan="2">
                        Reference Ticket: <strong style="color: #ffffff !important;">#<?php echo esc_html($reference_id); ?></strong>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="font-size: 14px; color: #a1a1aa !important; padding-bottom: 8px; width: 30%;"><strong style="color: #ffffff !important;">Full Name:</strong></td>
                    <td style="font-size: 14px; color: #ffffff !important; padding-bottom: 8px;">
                        <?php echo esc_html($sender_name); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 14px; color: #a1a1aa !important; padding-bottom: 8px;"><strong style="color: #ffffff !important;">Email:</strong></td>
                    <td style="font-size: 14px; color: #e4e4e7 !important; padding-bottom: 8px;">
                        <a href="mailto:<?php echo esc_attr($sender_email); ?>" style="color: #DF1D3D !important; font-weight: 600;">
                            <?php echo esc_html($sender_email); ?>
                        </a>
                    </td>
                </tr>
                <?php if (!empty($sender_phone)) : ?>
                <tr>
                    <td style="font-size: 14px; color: #a1a1aa !important; padding-bottom: 8px;"><strong style="color: #ffffff !important;">Phone:</strong></td>
                    <td style="font-size: 14px; color: #e4e4e7 !important; padding-bottom: 8px;">
                        <a href="tel:<?php echo esc_attr($sender_phone); ?>" style="color: #ffffff !important; text-decoration: none;">
                            <?php echo esc_html($sender_phone); ?>
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($sender_subject)) : ?>
                <tr>
                    <td style="font-size: 14px; color: #a1a1aa !important; padding-bottom: 8px;"><strong style="color: #ffffff !important;">Subject:</strong></td>
                    <td style="font-size: 14px; color: #ffffff !important; padding-bottom: 8px;">
                        <?php echo esc_html($sender_subject); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="font-size: 14px; color: #a1a1aa !important; padding-bottom: 8px;"><strong style="color: #ffffff !important;">Date:</strong></td>
                    <td style="font-size: 14px; color: #a1a1aa !important; padding-bottom: 8px;">
                        <?php echo esc_html(!empty($submission_time) ? $submission_time : date('M j, Y g:i A')); ?>
                    </td>
                </tr>
                <?php if (!empty($sender_ip)) : ?>
                <tr>
                    <td style="font-size: 13px; color: #a1a1aa !important; padding-bottom: 8px;"><strong style="color: #ffffff !important;">IP Address:</strong></td>
                    <td style="font-size: 13px; color: #a1a1aa !important; padding-bottom: 8px;">
                        <?php echo esc_html($sender_ip); ?>
                    </td>
                </tr>
                <?php endif; ?>

                <!-- Message Body -->
                <tr>
                    <td colspan="2" style="padding-top: 14px; border-top: 1px solid #24242e;">
                        <strong style="font-size: 13px; color: #a1a1aa !important; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">Inquiry Message:</strong>
                        <div style="padding: 16px; background-color: #121215; background: #121215; background-image: linear-gradient(#121215, #121215); border-radius: 8px; border: 1px solid #22222a; font-size: 14px; color: #e4e4e7 !important; line-height: 1.7; white-space: pre-wrap; font-family: monospace, sans-serif;">
<?php echo esc_html($sender_message); ?>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Direct Reply CTA Button -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
    <tr>
        <td align="center">
            <a href="mailto:<?php echo esc_attr($sender_email); ?>?subject=Re: <?php echo rawurlencode(!empty($sender_subject) ? $sender_subject : 'Your inquiry on VelvetReel'); ?>" style="display: inline-block; padding: 13px 28px; font-size: 14px; font-weight: 700; color: #ffffff !important; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); text-decoration: none; border-radius: 8px;">
                Reply Directly to <?php echo esc_html($sender_name); ?> &rarr;
            </a>
        </td>
    </tr>
</table>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
