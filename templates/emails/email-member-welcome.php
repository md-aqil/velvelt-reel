<?php
/**
 * New Member Welcome Email Template
 *
 * Variables passed in $args:
 * - $member_name
 * - $member_username
 * - $member_email
 * - $profile_edit_url
 * - $classifieds_url
 * - $unsubscribe_url
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$site_name       = get_bloginfo('name');
$email_title     = 'Welcome to ' . $site_name . ' – Next Steps to Launch Your Profile';
$email_preheader = 'Welcome to VelvetReel! Here are 3 quick steps to complete your profile and start getting noticed.';
$badge_text      = 'WELCOME TO VELVETREEL';

include get_stylesheet_directory() . '/templates/emails/email-header.php';
?>

<!-- Greeting -->
<p style="margin: 0 0 16px 0; font-size: 18px; color: #ffffff !important; font-weight: 700;">
    Welcome to VelvetReel, <?php echo esc_html(!empty($member_name) ? $member_name : 'there'); ?>! 👋
</p>

<p style="margin: 0 0 24px 0; font-size: 15px; color: #d4d4d8 !important; line-height: 1.6;">
    You've joined a premier community of verified talents, models, actors, creators, and casting directors. We're excited to have you with us!
</p>

<!-- 3 Steps to Launch Box -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#17171c" class="email-card" style="background-color: #17171c; background: #17171c; background-image: linear-gradient(#17171c, #17171c); border-radius: 12px; border: 1px solid #282832; margin-bottom: 28px; overflow: hidden;">
    <tr>
        <td bgcolor="#17171c" style="padding: 24px; background-color: #17171c; background-image: linear-gradient(#17171c, #17171c);">
            <h3 style="margin: 0 0 18px 0; font-size: 16px; color: #ffffff !important; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                🚀 3 Quick Steps to Get Started:
            </h3>

            <!-- Step 1 -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 16px;">
                <tr>
                    <td valign="top" style="width: 32px; padding-right: 12px;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #2a151b; background-color: #2a151b; border: 1px solid #DF1D3D; color: #DF1D3D !important; font-weight: 700; font-size: 13px; line-height: 28px; text-align: center;">
                            1
                        </div>
                    </td>
                    <td valign="top">
                        <strong style="color: #ffffff !important; font-size: 14px; display: block;">Complete Your Talent Portfolio</strong>
                        <p style="margin: 4px 0 0 0; font-size: 13px; color: #a1a1aa !important; line-height: 1.5;">
                            Upload your headshots, bio, measurements, skills, and work links to make your profile stand out to recruiters.
                        </p>
                    </td>
                </tr>
            </table>

            <!-- Step 2 -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 16px;">
                <tr>
                    <td valign="top" style="width: 32px; padding-right: 12px;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #2a151b; background-color: #2a151b; border: 1px solid #DF1D3D; color: #DF1D3D !important; font-weight: 700; font-size: 13px; line-height: 28px; text-align: center;">
                            2
                        </div>
                    </td>
                    <td valign="top">
                        <strong style="color: #ffffff !important; font-size: 14px; display: block;">Explore Live Casting Calls & Classifieds</strong>
                        <p style="margin: 4px 0 0 0; font-size: 13px; color: #a1a1aa !important; line-height: 1.5;">
                            Browse and express interest in active casting calls, collaborations, and production opportunities.
                        </p>
                    </td>
                </tr>
            </table>

            <!-- Step 3 -->
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td valign="top" style="width: 32px; padding-right: 12px;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #2a151b; background-color: #2a151b; border: 1px solid #DF1D3D; color: #DF1D3D !important; font-weight: 700; font-size: 13px; line-height: 28px; text-align: center;">
                            3
                        </div>
                    </td>
                    <td valign="top">
                        <strong style="color: #ffffff !important; font-size: 14px; display: block;">Get Discovered by Industry Hiring Teams</strong>
                        <p style="margin: 4px 0 0 0; font-size: 13px; color: #a1a1aa !important; line-height: 1.5;">
                            Advertisers and brands can search your discipline and contact you directly for auditions.
                        </p>
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
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" bgcolor="#DF1D3D" style="border-radius: 8px; background: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%); background-image: linear-gradient(135deg, #DF1D3D 0%, #B2122D 100%);">
                        <a href="<?php echo esc_url(!empty($profile_edit_url) ? $profile_edit_url : home_url('/submit-talent/')); ?>" target="_blank" style="display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 700; color: #ffffff !important; text-decoration: none; border-radius: 8px; letter-spacing: 0.5px;">
                            Complete Your Portfolio Profile Now &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<p style="margin: 0; font-size: 13px; color: #a1a1aa !important; text-align: center;">
    Need help getting set up? Simply reply to this email or visit our <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" style="color: #ffffff !important; text-decoration: underline;">Support Center</a>.
</p>

<?php
include get_stylesheet_directory() . '/templates/emails/email-footer.php';
?>
