<?php
/**
 * Day 14 Subscription Reminder Email Template
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
$email_title     = 'Special Invitation: Complete your VelvetReel upgrade';
$email_preheader = 'This is your final invitation to activate full casting privileges on VelvetReel.';
$badge_text      = 'FINAL REMINDER';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 18px; color: #ffffff !important; font-weight: 700;">
    Hello <?php echo esc_html(!empty($member_name) ? $member_name : 'there'); ?>,
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    This is our final reminder regarding your <strong style="color: #ffffff !important;">VelvetReel Membership</strong>. Don't miss out on prime opportunities being filled this month.
</p>

<!-- Highlight Box -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td bgcolor="#17171c" style="padding: 24px; text-align: center; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <span style="font-size: 12px; font-weight: 700; color: #DF1D3D !important; text-transform: uppercase; letter-spacing: 1.5px;">
                Special Community Access
            </span>
            <h2 style="margin: 8px 0 12px 0; font-size: 20px; font-weight: 700; color: #ffffff !important;">
                Ready to be considered for top roles?
            </h2>
            <p style="margin: 0 0 20px 0; font-size: 14px; color: #d4d4d8 !important; line-height: 1.6;">
                Activate your membership today to submit applications directly and showcase your talent to top producers.
            </p>

            <table border="0" cellpadding="0" cellspacing="0" style="margin: auto;">
                <tr>
                    <td align="center" bgcolor="#DF1D3D" style="border-radius: 8px; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%);">
                        <a href="<?php echo esc_url(!empty($upgrade_url) ? $upgrade_url : home_url('/membership-join/')); ?>" target="_blank" style="display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 700; color: #ffffff !important; text-decoration: none; border-radius: 8px;">
                            Activate My Membership &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p style="margin: 0; font-size: 13px; color: #a1a1aa !important; text-align: center;">
    If you have any questions about plans or casting submissions, our team is always here to assist.
</p>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
