<?php
/**
 * VelvetReel Email Automation Live Preview & Testing Sandbox
 *
 * Provides an in-browser live rendering sandbox and 1-click test email dispatcher
 * for all VelvetReel automated email templates.
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Admin Menu for Email Testing Sandbox
 */
function velvet_register_email_preview_menu() {
    add_management_page(
        'Email Automation Sandbox',
        'Email Sandbox',
        'manage_options',
        'velvet-email-preview',
        'velvet_render_email_preview_page'
    );
}
add_action('admin_menu', 'velvet_register_email_preview_menu');

/**
 * Get Mock Sample Data for Email Templates
 *
 * @param string $template_key
 * @return array
 */
function velvet_get_mock_email_data($template_key) {
    $site_name = get_bloginfo('name');
    $sample_user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $display_name = !empty($current_user->display_name) ? $current_user->display_name : 'Jane Doe';

    switch ($template_key) {
        case 'welcome':
            return array(
                'template_file' => 'email-member-welcome.php',
                'title'         => 'New Member Welcome Email',
                'args'          => array(
                    'member_name'      => $display_name,
                    'member_username'  => $current_user->user_login,
                    'member_email'     => $current_user->user_email,
                    'profile_edit_url' => home_url('/submit-talent/'),
                    'classifieds_url'  => get_post_type_archive_link('advertisement'),
                    'unsubscribe_url'  => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
                ),
            );

        case 'otp_signup':
            return array(
                'template_file' => 'email-otp-verification.php',
                'title'         => 'OTP Verification (Sign-up)',
                'args'          => array(
                    'otp_code'          => '849201',
                    'recipient_name'    => $display_name,
                    'verification_type' => 'signup',
                    'expiry_minutes'    => 10,
                ),
            );

        case 'otp_reset':
            return array(
                'template_file' => 'email-otp-verification.php',
                'title'         => 'Password Reset OTP',
                'args'          => array(
                    'otp_code'          => '394820',
                    'recipient_name'    => $display_name,
                    'verification_type' => 'reset_password',
                    'expiry_minutes'    => 10,
                ),
            );

        case 'reminder_day_2':
            return array(
                'template_file' => 'email-reminder-portfolio-day-2.php',
                'title'         => 'Day 2 Reminder (Portfolio Setup #1)',
                'args'          => array(
                    'member_name'      => $display_name,
                    'profile_edit_url' => home_url('/submit-talent/'),
                    'unsubscribe_url'  => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
                ),
            );

        case 'reminder_day_5':
            return array(
                'template_file' => 'email-reminder-portfolio-day-5.php',
                'title'         => 'Day 5 Reminder (Portfolio Setup #2)',
                'args'          => array(
                    'member_name'      => $display_name,
                    'profile_edit_url' => home_url('/submit-talent/'),
                    'unsubscribe_url'  => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
                ),
            );

        case 'reminder_day_10':
            return array(
                'template_file' => 'email-reminder-subscription-day-10.php',
                'title'         => 'Day 10 Reminder (Subscription Upgrade #1)',
                'args'          => array(
                    'member_name'     => $display_name,
                    'upgrade_url'     => home_url('/membership-join/'),
                    'unsubscribe_url' => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
                ),
            );

        case 'reminder_day_14':
            return array(
                'template_file' => 'email-reminder-subscription-day-14.php',
                'title'         => 'Day 14 Final Notice (Subscription Upgrade #2)',
                'args'          => array(
                    'member_name'     => $display_name,
                    'upgrade_url'     => home_url('/membership-join/'),
                    'unsubscribe_url' => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
                ),
            );

        case 'targeted_classified':
            return array(
                'template_file' => 'email-targeted-classified.php',
                'title'         => 'Targeted Classified Opportunity Match Alert',
                'args'          => array(
                    'member_name'      => $display_name,
                    'ad_title'         => 'Lead Female Actor for Upcoming Feature Film',
                    'ad_category'      => 'Casting Calls',
                    'ad_excerpt'       => 'Seeking a versatile actress aged 22-30 for a principal role in a psychological drama shooting in Mumbai this fall.',
                    'ad_location'      => 'Mumbai, Maharashtra',
                    'ad_budget'        => 'Competitive Day Rate + Travel',
                    'ad_url'           => home_url('/advertisement/'),
                    'matching_reasons' => array('Acting / Lead Role', 'Location: Mumbai', 'Verified Portfolio'),
                    'unsubscribe_url'  => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
                ),
            );

        case 'advertiser_interest':
            return array(
                'template_file' => 'email-advertiser-interest.php',
                'title'         => 'Classified Application (To Advertiser)',
                'args'          => array(
                    'advertiser_name'        => 'Red Carpet Productions',
                    'ad_title'               => 'Cinematographer with Arri/RED Package Needed',
                    'ad_url'                 => home_url('/advertisement/'),
                    'applicant_name'         => 'Rahul Sharma',
                    'applicant_role'         => 'Director of Photography / Cinematographer',
                    'applicant_email'        => 'rahul.dop@example.com',
                    'applicant_phone'        => '+91 98765 43210',
                    'applicant_message'      => 'Hi! I have 7 years of narrative feature experience with my own RED V-Raptor package. Looking forward to connecting!',
                    'applicant_profile_url'  => home_url('/p/demo-public-portfolio-link/'),
                    'submission_time'        => date('M j, Y g:i A'),
                    'unsubscribe_url'        => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
                ),
            );

        case 'member_interest_confirmation':
            return array(
                'template_file' => 'email-member-interest-confirmation.php',
                'title'         => 'Application Confirmation (To Applicant)',
                'args'          => array(
                    'member_name'     => $display_name,
                    'ad_title'        => 'Cinematographer with Arri/RED Package Needed',
                    'ad_category'     => 'Crew Calls',
                    'ad_url'          => home_url('/advertisement/'),
                    'advertiser_name' => 'Red Carpet Productions',
                    'submission_time' => date('M j, Y g:i A'),
                    'unsubscribe_url' => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
                ),
            );

        case 'admin_interest_alert':
            return array(
                'template_file' => 'email-admin-interest-alert.php',
                'title'         => 'Classified Application Audit (To Admin)',
                'args'          => array(
                    'ad_id'                 => '1042',
                    'ad_title'              => 'Cinematographer with Arri/RED Package Needed',
                    'ad_url'                => home_url('/advertisement/'),
                    'advertiser_name'       => 'Red Carpet Productions',
                    'advertiser_email'      => 'contact@redcarpetfilms.com',
                    'applicant_name'        => 'Rahul Sharma',
                    'applicant_username'    => 'rahuldop',
                    'applicant_email'       => 'rahul.dop@example.com',
                    'applicant_phone'       => '+91 98765 43210',
                    'applicant_profile_url' => home_url('/p/demo-public-portfolio-link/'),
                    'submission_time'       => date('M j, Y g:i A'),
                ),
            );

        case 'contact_user':
            return array(
                'template_file' => 'email-contact-acknowledgement.php',
                'title'         => 'Contact Form Confirmation (To User)',
                'args'          => array(
                    'sender_name'     => $display_name,
                    'sender_email'    => $current_user->user_email,
                    'sender_subject'  => 'Membership & Billing Inquiry',
                    'sender_message'  => 'Hello VelvetReel support team, I would like to inquire about annual subscription benefits for production houses.',
                    'reference_id'    => 'VR-' . strtoupper(wp_generate_password(6, false)),
                    'submission_time' => date('M j, Y g:i A'),
                ),
            );

        case 'contact_admin':
            return array(
                'template_file' => 'email-contact-admin-alert.php',
                'title'         => 'Contact Form Inquiry Alert (To Admin)',
                'args'          => array(
                    'sender_name'     => $display_name,
                    'sender_email'    => 'inquiry@productionhouse.com',
                    'sender_phone'    => '+91 98200 12345',
                    'sender_subject'  => 'Membership & Billing Inquiry',
                    'sender_message'  => 'Hello VelvetReel support team, I would like to inquire about annual subscription benefits for production houses.',
                    'reference_id'    => 'VR-INQ9281',
                    'sender_ip'       => '127.0.0.1',
                    'submission_time' => date('M j, Y g:i A'),
                ),
            );

        default:
            return false;
    }
}

/**
 * Handle Direct Browser Preview Endpoint
 */
function velvet_handle_email_preview_render() {
    if (isset($_GET['velvet_email_raw_preview']) && current_user_can('manage_options')) {
        $template_key = sanitize_text_field($_GET['velvet_email_raw_preview']);
        $data = velvet_get_mock_email_data($template_key);
        if ($data && function_exists('velvet_render_email_template')) {
            $html = velvet_render_email_template($data['template_file'], $data['args']);
            header('Content-Type: text/html; charset=UTF-8');
            echo $html;
            exit;
        }
    }
}
add_action('admin_init', 'velvet_handle_email_preview_render');

/**
 * Render Admin Email Testing Page
 */
function velvet_render_email_preview_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    $templates_list = array(
        'welcome'                      => 'New Member Welcome Email',
        'otp_signup'                   => 'OTP Code Verification (Sign-up)',
        'otp_reset'                    => 'Password Reset OTP Code',
        'reminder_day_2'               => 'Day 2 Reminder (Portfolio Setup #1)',
        'reminder_day_5'               => 'Day 5 Reminder (Portfolio Setup #2)',
        'reminder_day_10'              => 'Day 10 Reminder (Subscription Upgrade #1)',
        'reminder_day_14'              => 'Day 14 Final Notice (Subscription Upgrade #2)',
        'targeted_classified'          => 'Targeted Classified Match Notification',
        'advertiser_interest'          => 'Classified Application (To Advertiser)',
        'member_interest_confirmation' => 'Application Confirmation (To Applicant)',
        'admin_interest_alert'         => 'Classified Application Audit (To Admin)',
        'contact_user'                 => 'Contact Form Confirmation (To Submitter)',
        'contact_admin'                => 'Contact Form Inquiry (To Admin Alert)',
    );

    $current_template = isset($_GET['preview_template']) ? sanitize_text_field($_GET['preview_template']) : 'welcome';
    if (!array_key_exists($current_template, $templates_list)) {
        $current_template = 'welcome';
    }

    $notice = '';
    // Handle Test Dispatch Action
    if (isset($_POST['velvet_send_test_email']) && check_admin_referer('velvet_email_test_action', 'velvet_email_test_nonce')) {
        $target_email = sanitize_email($_POST['target_email']);
        if (is_email($target_email)) {
            $data = velvet_get_mock_email_data($current_template);
            if ($data && function_exists('velvet_render_email_template') && function_exists('velvet_send_email')) {
                $html = velvet_render_email_template($data['template_file'], $data['args']);
                $subject = '[TEST PREVIEW] ' . $data['title'] . ' – VelvetReel';
                $sent = velvet_send_email($target_email, $subject, $html);
                if ($sent) {
                    $notice = '<div class="notice notice-success is-dismissible"><p><strong>✅ Success!</strong> Test email for <em>' . esc_html($data['title']) . '</em> sent to <strong>' . esc_html($target_email) . '</strong>.</p></div>';
                } else {
                    $notice = '<div class="notice notice-error is-dismissible"><p><strong>❌ Error:</strong> wp_mail failed to send. Please check your mail server configuration or Mailpit.</p></div>';
                }
            }
        } else {
            $notice = '<div class="notice notice-warning is-dismissible"><p>Please enter a valid email address.</p></div>';
        }
    }

    $preview_url = add_query_arg('velvet_email_raw_preview', $current_template, admin_url('index.php'));
    $current_admin_email = 'thevelvetreelproductions@gmail.com';
    ?>
    <div class="wrap" style="max-width: 1200px;">
        <h1 style="display: flex; align-items: center; gap: 10px;">
            <span style="color: #DF1D3D;">✉️</span> VelvetReel Email Automation Sandbox
        </h1>
        <p style="color: #666; font-size: 14px;">
            Preview all automated HTML email templates in real-time or dispatch a 1-click test to <strong>thevelvetreelproductions@gmail.com</strong> or your test inbox.
        </p>

        <?php echo $notice; ?>

        <div style="display: grid; grid-template-columns: 320px 1fr; gap: 24px; margin-top: 20px;">
            <!-- Left Sidebar: Templates Navigation -->
            <div style="background: #ffffff; border: 1px solid #ccd0d4; border-radius: 8px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee; font-size: 15px;">
                    Select Email Template
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 4px;">
                    <?php foreach ($templates_list as $key => $label): 
                        $active = ($key === $current_template);
                    ?>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('preview_template', $key, menu_page_url('velvet-email-preview', false))); ?>" 
                           style="display: block; padding: 9px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: <?php echo $active ? '700' : '500'; ?>; color: <?php echo $active ? '#ffffff' : '#333333'; ?>; background-color: <?php echo $active ? '#DF1D3D' : 'transparent'; ?>; transition: all 0.2s ease;">
                            <?php echo esc_html($label); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Right Area: Actions & Live Preview Frame -->
            <div>
                <!-- Action Controls Bar -->
                <div style="background: #ffffff; border: 1px solid #ccd0d4; border-radius: 8px; padding: 16px 20px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                    <div>
                        <strong style="font-size: 16px; color: #1d2327;">
                            <?php echo esc_html($templates_list[$current_template]); ?>
                        </strong>
                    </div>

                    <!-- Send Test Dispatch Form -->
                    <form method="post" style="display: flex; align-items: center; gap: 8px;">
                        <?php wp_nonce_field('velvet_email_test_action', 'velvet_email_test_nonce'); ?>
                        <input type="email" name="target_email" value="<?php echo esc_attr($current_admin_email); ?>" required style="padding: 6px 12px; border-radius: 4px; border: 1px solid #8c8f94; width: 260px; font-size: 13px;">
                        <button type="submit" name="velvet_send_test_email" class="button button-primary" style="background: #DF1D3D; border-color: #DF1D3D;">
                            🚀 Send Test Email
                        </button>
                        <a href="<?php echo esc_url($preview_url); ?>" target="_blank" class="button button-secondary" title="Open preview in full screen tab">
                            ↗ Open Tab
                        </a>
                    </form>
                </div>

                <!-- Responsive Live Preview Frame -->
                <div style="background: #0b0b0d; border: 1px solid #222; border-radius: 8px; padding: 20px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                    <iframe src="<?php echo esc_url($preview_url); ?>" style="width: 100%; max-width: 660px; height: 750px; border: none; border-radius: 12px; background: #0b0b0d;" title="Email Live Preview"></iframe>
                </div>
            </div>
        </div>
    </div>
    <?php
}
