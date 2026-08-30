<?php
/**
 * VelvetReel Branded Email Header Template
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

$site_name = get_bloginfo('name');
$site_url  = home_url('/');
$title     = isset($email_title) ? $email_title : $site_name;
$preheader = isset($email_preheader) ? $email_preheader : '';
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title><?php echo esc_html($title); ?></title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style type="text/css">
        :root {
            color-scheme: light dark;
            supported-color-schemes: light dark;
        }
        /* Reset Styles */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background: #0b0b0d !important;
            background-color: #0b0b0d !important;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #d4d4d8 !important;
        }
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
            border-collapse: collapse !important;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
            display: block;
        }
        a {
            text-decoration: none;
            color: #DF1D3D !important;
        }

        /* Prevent unwanted mobile light-mode inversions */
        .email-bg {
            background-color: #0b0b0d !important;
            background: #0b0b0d !important;
        }
        .email-container {
            background-color: #121215 !important;
            background: #121215 !important;
        }
        .email-card {
            background-color: #17171c !important;
            background: #17171c !important;
        }

        /* Mobile styles */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: auto !important;
                border-radius: 0 !important;
            }
            .stack-column {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }
            .mobile-padding {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .button-cta {
                width: 100% !important;
                text-align: center !important;
            }
        }

        /* Force Dark Theme in both Dark and Light mobile renderers */
        @media (prefers-color-scheme: dark) {
            body, .email-bg {
                background-color: #0b0b0d !important;
                background: #0b0b0d !important;
            }
            .email-container {
                background-color: #121215 !important;
                background: #121215 !important;
            }
            h1, h2, h3, strong {
                color: #ffffff !important;
            }
            p, td, li {
                color: #d4d4d8 !important;
            }
        }

        @media (prefers-color-scheme: light) {
            body, .email-bg {
                background-color: #0b0b0d !important;
                background: #0b0b0d !important;
            }
            .email-container {
                background-color: #121215 !important;
                background: #121215 !important;
            }
            h1, h2, h3, strong {
                color: #ffffff !important;
            }
            p, td, li {
                color: #d4d4d8 !important;
            }
        }
    </style>
</head>
<body class="email-bg" bgcolor="#0b0b0d" style="margin: 0; padding: 0; background-color: #0b0b0d; background: #0b0b0d; background-image: linear-gradient(#0b0b0d, #0b0b0d); font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #d4d4d8;">
    <?php if (!empty($preheader)) : ?>
    <!-- Hidden Preheader text -->
    <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
        <?php echo esc_html($preheader); ?>
    </div>
    <?php endif; ?>

    <!-- Main Wrapper Table -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="email-bg" bgcolor="#0b0b0d" style="background-color: #0b0b0d; background: #0b0b0d; background-image: linear-gradient(#0b0b0d, #0b0b0d); table-layout: fixed;">
        <tr>
            <td align="center" bgcolor="#0b0b0d" style="padding: 30px 10px 40px 10px; background-color: #0b0b0d; background: #0b0b0d; background-image: linear-gradient(#0b0b0d, #0b0b0d);">
                
                <!-- Email Container Box -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" bgcolor="#121215" style="max-width: 620px; background-color: #121215; background: #121215; background-image: linear-gradient(#121215, #121215); border-radius: 16px; border: 1px solid #222227; overflow: hidden; box-shadow: 0 12px 40px rgba(0,0,0,0.6);">
                    
                    <!-- Top Brand Header Banner -->
                    <tr>
                        <td align="center" bgcolor="#15151a" style="padding: 28px 30px 22px 30px; background-color: #15151a; background: linear-gradient(180deg, #18181f 0%, #121215 100%); background-image: linear-gradient(180deg, #18181f 0%, #121215 100%); border-bottom: 1px solid #26262e;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="left" valign="middle">
                                        <a href="<?php echo esc_url($site_url); ?>" style="display: inline-block; font-size: 24px; font-weight: 800; color: #ffffff !important; letter-spacing: -0.5px; text-decoration: none; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                                            <span style="color: #DF1D3D !important;">VELVET</span>REEL
                                        </a>
                                    </td>
                                    <td align="right" valign="middle">
                                        <span style="display: inline-block; padding: 5px 12px; background-color: #23141a; background: rgba(223, 29, 61, 0.12); border: 1px solid rgba(223, 29, 61, 0.35); border-radius: 20px; font-size: 11px; font-weight: 700; color: #DF1D3D !important; text-transform: uppercase; letter-spacing: 1px;">
                                            <?php echo !empty($badge_text) ? esc_html($badge_text) : 'Classifieds'; ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Email Main Content Body Starts Here -->
                    <tr>
                        <td bgcolor="#121215" style="padding: 36px 36px 20px 36px; background-color: #121215; background: #121215; background-image: linear-gradient(#121215, #121215);" class="mobile-padding">
