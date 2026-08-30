<?php
/**
 * Visitor Contact Us Acknowledgement Email Template
 *
 * Variables passed in $args:
 * - $sender_name
 * - $sender_email
 * - $sender_phone
 * - $sender_subject
 * - $sender_message
 * - $submission_time
 * - $reference_id
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$site_name       = get_bloginfo('name');
$email_title     = 'Thank you for contacting ' . $site_name;
$email_preheader = 'We have received your message and our team will get back to you shortly.';
$badge_text      = 'MESSAGE RECEIVED';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 16px; color: #ffffff !important; font-weight: 600;">
    Hello <?php echo esc_html(!empty($sender_name) ? $sender_name : 'there'); ?>,
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    Thank you for reaching out to <strong style="color: #ffffff !important;"><?php echo esc_html($site_name); ?></strong>! We have received your inquiry and our support team is currently reviewing your message.
</p>

<!-- Reference Card -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td bgcolor="#17171c" style="padding: 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="padding-bottom: 14px; border-bottom: 1px solid #24242e;">
                        <span style="display: inline-block; padding: 4px 10px; background-color: #14281a; background: rgba(34, 197, 94, 0.12); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 20px; font-size: 11px; font-weight: 700; color: #22c55e !important; text-transform: uppercase; letter-spacing: 1px;">
                            ✓ Inquiry Logged
                        </span>
                        <?php if (!empty($reference_id)) : ?>
                        <span style="float: right; font-size: 12px; color: #a1a1aa !important;">
                            Ref: <strong style="color: #ffffff !important;">#<?php echo esc_html($reference_id); ?></strong>
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>

                <?php if (!empty($sender_subject)) : ?>
                <tr>
                    <td style="padding-top: 14px; font-size: 14px; color: #a1a1aa !important;">
                        <strong style="color: #ffffff !important;">Subject:</strong> <?php echo esc_html($sender_subject); ?>
                    </td>
                </tr>
                <?php endif; ?>

                <tr>
                    <td style="padding-top: 12px; padding-bottom: 6px;">
                        <strong style="font-size: 13px; color: #a1a1aa !important; text-transform: uppercase; letter-spacing: 0.5px;">Your Message Summary:</strong>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#121215" style="padding: 12px 16px; background-color: #121215; background: #121215; background-image: linear-gradient(#121215, #121215); border-radius: 8px; border: 1px solid #22222a; font-size: 14px; color: #d4d4d8 !important; line-height: 1.6; font-style: italic;">
                        <?php echo nl2br(esc_html($sender_message)); ?>
                    </td>
                </tr>

                <tr>
                    <td style="padding-top: 14px; font-size: 12px; color: #a1a1aa !important;">
                        Received on: <?php echo esc_html(!empty($submission_time) ? $submission_time : date('M j, Y g:i A')); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- What to Expect -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#121215" style="background-color: #121215; background: #121215; background-image: linear-gradient(#121215, #121215); border-radius: 8px; border-left: 3px solid #DF1D3D; margin-bottom: 28px;">
    <tr>
        <td bgcolor="#121215" style="padding: 18px 20px; background-color: #121215; background-image: linear-gradient(#121215, #121215);">
            <h3 style="margin: 0 0 8px 0; font-size: 14px; font-weight: 700; color: #ffffff !important;">What to expect next:</h3>
            <p style="margin: 0; font-size: 13px; color: #d4d4d8 !important; line-height: 1.6;">
                A member of our team typically responds within <strong>24 to 48 business hours</strong>. If your request is urgent, you can also reach us through our official support lines listed below.
            </p>
        </td>
    </tr>
</table>

<!-- Action CTA Button -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
    <tr>
        <td align="center">
            <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank" style="display: inline-block; padding: 13px 30px; font-size: 14px; font-weight: 700; color: #ffffff !important; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); text-decoration: none; border-radius: 8px;">
                Visit VelvetReel Homepage &rarr;
            </a>
        </td>
    </tr>
</table>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
