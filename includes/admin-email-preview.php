<?php
/**
 * VelvetReel Email Automation Live Preview, Workflow Diagnostics & Testing Sandbox
 *
 * Provides an in-browser live rendering sandbox, full workflow architectural blueprints,
 * live system statistics, cron health monitoring, and 1-click test email dispatchers
 * for all VelvetReel automated email workflows.
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
 * Get Comprehensive Automation Blueprint & Metadata
 *
 * @return array
 */
function velvet_get_email_automations_registry() {
    return array(
        'welcome' => array(
            'category'       => 'Onboarding Lifecycle',
            'category_icon'  => '🌟',
            'title'          => 'New Member Welcome Email',
            'template_file'  => 'email-member-welcome.php',
            'subject_sample' => 'Welcome to VelvetReel – Next Steps to Launch Your Profile',
            'trigger_type'   => 'Immediate Action Hook',
            'hook_name'      => 'user_register',
            'hook_source'    => 'includes/member-onboarding-automation.php',
            'recipient'      => 'Newly Registered Member (user_email)',
            'timing'         => 'Instant (Fires immediately upon account signup)',
            'conditions'     => array(
                'Fires once per user account (checked via _velvet_welcome_email_sent meta).',
                'Suppressed if user has opted out (_velvet_notifications_optout).',
                'Stores _velvet_registered_time to initiate the 14-day onboarding reminder sequence.',
            ),
            'action_link'    => '/talent/ (Directs member to talent directory)',
        ),
        'portfolio_unverified' => array(
            'category'       => 'Onboarding Lifecycle',
            'category_icon'  => '🌟',
            'title'          => 'Portfolio Created (Verification Pending)',
            'template_file'  => 'email-portfolio-unverified.php',
            'subject_sample' => 'Your VelvetReel Portfolio is Created – Complete Verification to Publish',
            'trigger_type'   => 'Immediate Action Hook / Staged Onboarding',
            'hook_name'      => 'wp_insert_post / handle_talent_submission',
            'hook_source'    => 'includes/member-onboarding-automation.php',
            'recipient'      => 'Member who submitted talent portfolio without verification (user_email)',
            'timing'         => 'Instant upon portfolio submission (and follow-up evaluation)',
            'conditions'     => array(
                'Fires when user creates/submits their talent portfolio but has not yet paid the $5 verification fee.',
                'Fires once per user profile (guarded via _velvet_portfolio_unverified_email_sent meta).',
                'Suppressed if profile is already verified (_talent_has_paid_approval) or user opted out.',
            ),
            'action_link'    => '/talent/ (Directs member to portfolio verification page)',
        ),
        'otp_signup' => array(
            'category'       => 'Authentication & Security',
            'category_icon'  => '🔐',
            'title'          => 'OTP Code Verification (Sign-up)',
            'template_file'  => 'email-otp-verification.php',
            'subject_sample' => 'Your VelvetReel Verification Code: 849201',
            'trigger_type'   => 'User Action / Form Submission',
            'hook_name'      => 'Sign-up Form AJAX / verify-otp.php',
            'hook_source'    => 'sign-up.php / page-verify-email.php',
            'recipient'      => 'Prospective Member (submitted email address)',
            'timing'         => 'Instant (Generated on page submit)',
            'conditions'     => array(
                'Generates a cryptographically secure 6-digit numeric OTP code.',
                'Stores hashed OTP in transients with a 10-minute expiration window.',
                'Enforces brute-force rate-limiting on failed attempts.',
            ),
            'action_link'    => '/verify-otp/ (Verification screen)',
        ),
        'otp_reset' => array(
            'category'       => 'Authentication & Security',
            'category_icon'  => '🔐',
            'title'          => 'Password Reset OTP Code',
            'template_file'  => 'email-otp-verification.php',
            'subject_sample' => 'Your VelvetReel Password Reset Code: 394820',
            'trigger_type'   => 'User Action / Form Submission',
            'hook_name'      => 'Forgot Password Request Handler',
            'hook_source'    => 'forgot-password.php',
            'recipient'      => 'Existing Account Owner (user_email)',
            'timing'         => 'Instant (Generated on reset request)',
            'conditions'     => array(
                'Verifies user account existence prior to dispatching.',
                'Expires in 10 minutes from creation.',
                'Invalidates previously generated tokens on new requests.',
            ),
            'action_link'    => '/reset-password/ (New password entry screen)',
        ),
        'reminder_day_2' => array(
            'category'       => 'Onboarding Lifecycle',
            'category_icon'  => '🌟',
            'title'          => 'Day 2 Reminder (Portfolio Setup #1)',
            'template_file'  => 'email-reminder-portfolio-day-2.php',
            'subject_sample' => 'Complete your portfolio on VelvetReel – Opportunities are waiting',
            'trigger_type'   => 'Scheduled Background WP-Cron',
            'hook_name'      => 'velvet_onboarding_sequence_cron_hook (twicedaily)',
            'hook_source'    => 'includes/member-onboarding-automation.php',
            'recipient'      => 'Registered Member without a completed talent profile',
            'timing'         => 'Elapsed time ≥ 2 days (48 hours) post-registration',
            'conditions'     => array(
                'User has been registered for at least 48 hours.',
                'Talent profile is not yet created or missing key attributes.',
                'Fires once per user (tracked via _velvet_reminder_sent_day_2).',
                'Exits sequence early if user completes profile or subscribes.',
            ),
            'action_link'    => '/talent/ (Talent portfolio creation form)',
        ),
        'reminder_day_5' => array(
            'category'       => 'Onboarding Lifecycle',
            'category_icon'  => '🌟',
            'title'          => 'Day 5 Reminder (Portfolio Setup #2)',
            'template_file'  => 'email-reminder-portfolio-day-5.php',
            'subject_sample' => 'Don\'t miss out on casting calls on VelvetReel',
            'trigger_type'   => 'Scheduled Background WP-Cron',
            'hook_name'      => 'velvet_onboarding_sequence_cron_hook (twicedaily)',
            'hook_source'    => 'includes/member-onboarding-automation.php',
            'recipient'      => 'Registered Member still missing a published talent profile',
            'timing'         => 'Elapsed time ≥ 5 days (120 hours) post-registration',
            'conditions'     => array(
                'User has been registered for at least 5 days.',
                'No published talent profile found for user ID.',
                'Fires once per user (tracked via _velvet_reminder_sent_day_5).',
                'Includes 1-click unsubscribe suppression link.',
            ),
            'action_link'    => '/talent/ (Direct resume/reel upload form)',
        ),
        'reminder_day_10' => array(
            'category'       => 'Onboarding Lifecycle',
            'category_icon'  => '🌟',
            'title'          => 'Day 10 Reminder (Subscription Upgrade #1)',
            'template_file'  => 'email-reminder-subscription-day-10.php',
            'subject_sample' => 'Unlock Full Casting Access with VelvetReel Membership',
            'trigger_type'   => 'Scheduled Background WP-Cron',
            'hook_name'      => 'velvet_onboarding_sequence_cron_hook (twicedaily)',
            'hook_source'    => 'includes/member-onboarding-automation.php',
            'recipient'      => 'Free / Unsubscribed Member',
            'timing'         => 'Elapsed time ≥ 10 days post-registration',
            'conditions'     => array(
                'User does not have an active paid subscription level (Level 5 or Portfolio Plan).',
                'Fires once per user (tracked via _velvet_reminder_sent_day_10).',
                'Exits sequence if user upgraded prior to day 10.',
            ),
            'action_link'    => '/membership-join/ (Membership plan upgrade table)',
        ),
        'reminder_day_14' => array(
            'category'       => 'Onboarding Lifecycle',
            'category_icon'  => '🌟',
            'title'          => 'Day 14 Final Notice (Subscription Upgrade #2)',
            'template_file'  => 'email-reminder-subscription-day-14.php',
            'subject_sample' => 'Special Invitation: Complete your VelvetReel upgrade',
            'trigger_type'   => 'Scheduled Background WP-Cron',
            'hook_name'      => 'velvet_onboarding_sequence_cron_hook (twicedaily)',
            'hook_source'    => 'includes/member-onboarding-automation.php',
            'recipient'      => 'Free / Unsubscribed Member (Final Onboarding Call)',
            'timing'         => 'Elapsed time ≥ 14 days post-registration',
            'conditions'     => array(
                'Final reminder in the 14-day automated onboarding sequence.',
                'Sets _velvet_onboarding_sequence_completed on user meta.',
                'No further automated onboarding reminders are sent to this user.',
            ),
            'action_link'    => '/membership-join/ (Final discount / subscription link)',
        ),
        'targeted_classified' => array(
            'category'       => 'Classifieds & Audience Matching Engine',
            'category_icon'  => '📢',
            'title'          => 'Targeted Classified Opportunity Match Alert',
            'template_file'  => 'email-targeted-classified.php',
            'subject_sample' => '⚡ New Opportunity Match: Lead Female Actor for Feature Film',
            'trigger_type'   => 'Post Status Transition Hook',
            'hook_name'      => 'transition_post_status (pending -> publish)',
            'hook_source'    => 'includes/classified-notifications.php',
            'recipient'      => 'All Published Talent profiles matching criteria (Domain, Role, Gender, Location, Age)',
            'timing'         => 'Triggered the moment an advertisement is approved & published by Admin',
            'conditions'     => array(
                'Ad must transition from draft/pending to "publish".',
                'Smart Matching Filter: Compares ad requirements with each active talent\'s domain, role, gender, age, and state/country.',
                'Duplicate Guard: Checks _advertisement_notifications_sent so talents are only emailed once per ad.',
                'Suppression Check: Excludes the ad author and any talents who clicked Unsubscribe.',
                'Can be manually re-broadcasted via the "Force Resend" meta box in WP-Admin.',
            ),
            'action_link'    => '/advertisement/{ad-slug}/ (View listing & express interest)',
        ),
        'advertiser_interest' => array(
            'category'       => 'Classified Inquiries & Applications',
            'category_icon'  => '🤝',
            'title'          => 'Classified Application (To Advertiser)',
            'template_file'  => 'email-advertiser-interest.php',
            'subject_sample' => '🎬 New Interest Expressed in Your Classified: Cinematographer Needed',
            'trigger_type'   => 'AJAX Form Action',
            'hook_name'      => 'wp_ajax_express_advertisement_interest',
            'hook_source'    => 'includes/ajax-handlers.php & classified-notifications.php',
            'recipient'      => 'Advertisement Author / Contact Email',
            'timing'         => 'Instant (Sent within ~500ms of applicant clicking "Express Interest")',
            'conditions'     => array(
                'Applicant must be a logged-in member with an active paid membership level.',
                'Prevents ad author from applying to their own ad.',
                'Prevents duplicate submissions by storing applicant ID in _advertisement_interests meta array.',
                'Includes applicant profile link, role, location, and experience details.',
            ),
            'action_link'    => '/p/{talent-slug}/ (Direct link to review talent portfolio)',
        ),
        'member_interest_confirmation' => array(
            'category'       => 'Classified Inquiries & Applications',
            'category_icon'  => '🤝',
            'title'          => 'Application Confirmation (To Applicant)',
            'template_file'  => 'email-member-interest-confirmation.php',
            'subject_sample' => '✅ Application Sent: Cinematographer with Arri/RED Package Needed',
            'trigger_type'   => 'AJAX Form Action',
            'hook_name'      => 'wp_ajax_express_advertisement_interest',
            'hook_source'    => 'includes/ajax-handlers.php & classified-notifications.php',
            'recipient'      => 'Applicant / Member (current_user->user_email)',
            'timing'         => 'Instant confirmation following successful interest dispatch',
            'conditions'     => array(
                'Confirms application was delivered to hiring advertiser.',
                'Includes ad summary and timestamp for applicant records.',
            ),
            'action_link'    => '/advertisement/{ad-slug}/ (View original classified listing)',
        ),
        'admin_interest_alert' => array(
            'category'       => 'Classified Inquiries & Applications',
            'category_icon'  => '🤝',
            'title'          => 'Classified Application Audit (To Admin)',
            'template_file'  => 'email-admin-interest-alert.php',
            'subject_sample' => '📋 [Audit] New Application: Cinematographer Needed – Rahul Sharma',
            'trigger_type'   => 'AJAX Form Action',
            'hook_name'      => 'wp_ajax_express_advertisement_interest',
            'hook_source'    => 'includes/ajax-handlers.php & classified-notifications.php',
            'recipient'      => 'Site Administrator (get_option("admin_email"))',
            'timing'         => 'Instant audit record on every application',
            'conditions'     => array(
                'Logs full transaction details: Ad ID, Advertiser email, Applicant details, IP, and timestamp.',
                'Provides quick admin dashboard management link.',
            ),
            'action_link'    => '/wp-admin/post.php?post={id}&action=edit (Admin listing management)',
        ),
        'contact_user' => array(
            'category'       => 'Contact & Inquiries',
            'category_icon'  => '📩',
            'title'          => 'Contact Form Confirmation (To Submitter)',
            'template_file'  => 'email-contact-acknowledgement.php',
            'subject_sample' => 'We received your message [Ref: VR-K82910] – VelvetReel',
            'trigger_type'   => 'AJAX Form Action',
            'hook_name'      => 'wp_ajax_velvet_submit_contact_form',
            'hook_source'    => 'includes/contact-form-handler.php',
            'recipient'      => 'Form Submitter (Visitor or Member)',
            'timing'         => 'Instant confirmation on contact form submission',
            'conditions'     => array(
                'Validates email format and nonce protection.',
                'Generates unique tracking reference ID (e.g. VR-XXXXXX).',
                'Includes submitted inquiry transcript.',
            ),
            'action_link'    => '/ (Homepage / Support portal)',
        ),
        'contact_admin' => array(
            'category'       => 'Contact & Inquiries',
            'category_icon'  => '📩',
            'title'          => 'Contact Form Inquiry (To Admin Alert)',
            'template_file'  => 'email-contact-admin-alert.php',
            'subject_sample' => '📬 [Contact Inquiry] Membership & Billing – Jane Doe [VR-INQ9281]',
            'trigger_type'   => 'AJAX Form Action',
            'hook_name'      => 'wp_ajax_velvet_submit_contact_form',
            'hook_source'    => 'includes/contact-form-handler.php',
            'recipient'      => 'Site Administrator / Support Desk',
            'timing'         => 'Instant alert on new contact form inquiry',
            'conditions'     => array(
                'Contains sender full name, email, phone, IP address, and full message body.',
                'Includes direct Reply-To header to facilitate 1-click email response to user.',
            ),
            'action_link'    => 'None (Informational admin alert notification)',
        ),
    );
}

/**
 * Get Mock Sample Data for Email Templates
 *
 * @param string $template_key
 * @return array|false
 */
function velvet_get_mock_email_data($template_key) {
    $sample_user_id = get_current_user_id();
    $current_user   = wp_get_current_user();
    $display_name   = !empty($current_user->display_name) ? $current_user->display_name : 'Jane Doe';
    $registry       = velvet_get_email_automations_registry();

    if (!isset($registry[$template_key])) {
        return false;
    }

    $meta = $registry[$template_key];

    switch ($template_key) {
        case 'welcome':
            $args = array(
                'member_name'      => $display_name,
                'member_username'  => $current_user->user_login,
                'member_email'     => $current_user->user_email,
                'profile_edit_url' => home_url('/talent/'),
                'classifieds_url'  => get_post_type_archive_link('advertisement'),
                'unsubscribe_url'  => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
            );
            break;

        case 'portfolio_unverified':
            $args = array(
                'member_name'      => $display_name,
                'talent_title'     => $display_name . ' – Talent Portfolio',
                'verification_url' => home_url('/talent/'),
                'fee_amount'       => '$5',
                'unsubscribe_url'  => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
            );
            break;

        case 'otp_signup':
            $args = array(
                'otp_code'          => '849201',
                'recipient_name'    => $display_name,
                'verification_type' => 'signup',
                'expiry_minutes'    => 10,
            );
            break;

        case 'otp_reset':
            $args = array(
                'otp_code'          => '394820',
                'recipient_name'    => $display_name,
                'verification_type' => 'reset_password',
                'expiry_minutes'    => 10,
            );
            break;

        case 'reminder_day_2':
            $args = array(
                'member_name'      => $display_name,
                'profile_edit_url' => home_url('/talent/'),
                'unsubscribe_url'  => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
            );
            break;

        case 'reminder_day_5':
            $args = array(
                'member_name'      => $display_name,
                'profile_edit_url' => home_url('/talent/'),
                'unsubscribe_url'  => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
            );
            break;

        case 'reminder_day_10':
            $args = array(
                'member_name'     => $display_name,
                'upgrade_url'     => home_url('/membership-join/'),
                'unsubscribe_url' => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
            );
            break;

        case 'reminder_day_14':
            $args = array(
                'member_name'     => $display_name,
                'upgrade_url'     => home_url('/membership-join/'),
                'unsubscribe_url' => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
            );
            break;

        case 'targeted_classified':
            $args = array(
                'recipient_name'   => $display_name,
                'member_name'      => $display_name,
                'ad_title'         => 'Lead Female Actor for Upcoming Feature Film',
                'ad_category'      => 'Casting Calls',
                'ad_type'          => 'Casting Call',
                'ad_excerpt'       => 'Seeking a versatile actress aged 22-30 for a principal role in a psychological drama shooting in Jersey City, NJ this fall.',
                'ad_location'      => '101 Hudson St., Jersey City, NJ 07304',
                'ad_budget'        => 'Competitive Day Rate + Travel',
                'ad_url'           => home_url('/advertisement/'),
                'ad_image_url'     => '',
                'matching_reasons' => array('Domain: Acting', 'Role: Lead Actor', 'Location: 101 Hudson St., Jersey City, NJ 07304'),
                'unsubscribe_url'  => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
            );
            break;

        case 'advertiser_interest':
            $args = array(
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
            );
            break;

        case 'member_interest_confirmation':
            $args = array(
                'member_name'     => $display_name,
                'ad_title'        => 'Cinematographer with Arri/RED Package Needed',
                'ad_category'     => 'Crew Calls',
                'ad_url'          => home_url('/advertisement/'),
                'advertiser_name' => 'Red Carpet Productions',
                'submission_time' => date('M j, Y g:i A'),
                'unsubscribe_url' => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($sample_user_id) : home_url('/'),
            );
            break;

        case 'admin_interest_alert':
            $args = array(
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
            );
            break;

        case 'contact_user':
            $args = array(
                'sender_name'     => $display_name,
                'sender_email'    => $current_user->user_email,
                'sender_subject'  => 'Membership & Billing Inquiry',
                'sender_message'  => 'Hello VelvetReel support team, I would like to inquire about annual subscription benefits for production houses.',
                'reference_id'    => 'VR-' . strtoupper(wp_generate_password(6, false)),
                'submission_time' => date('M j, Y g:i A'),
            );
            break;

        case 'contact_admin':
            $args = array(
                'sender_name'     => $display_name,
                'sender_email'    => 'inquiry@productionhouse.com',
                'sender_phone'    => '+91 98200 12345',
                'sender_subject'  => 'Membership & Billing Inquiry',
                'sender_message'  => 'Hello VelvetReel support team, I would like to inquire about annual subscription benefits for production houses.',
                'reference_id'    => 'VR-INQ9281',
                'sender_ip'       => '127.0.0.1',
                'submission_time' => date('M j, Y g:i A'),
            );
            break;

        default:
            return false;
    }

    return array(
        'meta'          => $meta,
        'template_file' => $meta['template_file'],
        'title'         => $meta['title'],
        'args'          => $args,
    );
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

    $registry = velvet_get_email_automations_registry();
    $current_template = isset($_GET['preview_template']) ? sanitize_text_field($_GET['preview_template']) : 'welcome';
    if (!array_key_exists($current_template, $registry)) {
        $current_template = 'welcome';
    }

    $selected_item = $registry[$current_template];
    $template_file_path = get_stylesheet_directory() . '/templates/emails/' . $selected_item['template_file'];
    $template_exists = file_exists($template_file_path);

    $notice = '';

    // Handle Manual Cron Execution Test
    if (isset($_POST['velvet_trigger_onboarding_cron']) && check_admin_referer('velvet_cron_action', 'velvet_cron_nonce')) {
        if (function_exists('velvet_process_onboarding_reminders')) {
            $stats = velvet_process_onboarding_reminders();
            $notice = '<div class="notice notice-success is-dismissible"><p><strong>⚡ Onboarding Cron Executed!</strong> Evaluated: ' . intval($stats['evaluated']) . ' members | Day 2 Sent: ' . intval($stats['day_2_sent']) . ' | Day 5 Sent: ' . intval($stats['day_5_sent']) . ' | Day 10 Sent: ' . intval($stats['day_10_sent']) . ' | Day 14 Sent: ' . intval($stats['day_14_sent']) . ' | Completed: ' . intval($stats['completed']) . ' | Skipped/Opt-out: ' . intval($stats['skipped']) . '.</p></div>';
        }
    }

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
                    $notice = '<div class="notice notice-success is-dismissible"><p><strong>✅ Success!</strong> Test email for <em>' . esc_html($data['title']) . '</em> successfully dispatched to <strong>' . esc_html($target_email) . '</strong>.</p></div>';
                } else {
                    $notice = '<div class="notice notice-error is-dismissible"><p><strong>❌ Error:</strong> wp_mail failed to send. Please check your mail server configuration or Local WP Mailpit.</p></div>';
                }
            }
        } else {
            $notice = '<div class="notice notice-warning is-dismissible"><p>Please enter a valid email address.</p></div>';
        }
    }

    // System Statistics for Diagnostics Bar
    $total_users = count_users();
    $total_members_count = isset($total_users['total_users']) ? $total_users['total_users'] : 0;
    $published_ads_count = wp_count_posts('advertisement')->publish;
    $pending_ads_count   = wp_count_posts('advertisement')->pending;
    $published_talents   = wp_count_posts('talent')->publish;
    $next_cron_time      = wp_next_scheduled('velvet_onboarding_sequence_cron_hook');
    $next_cron_display   = $next_cron_time ? human_time_diff($next_cron_time) . ' from now' : 'Not scheduled';

    $preview_url = add_query_arg('velvet_email_raw_preview', $current_template, admin_url('index.php'));
    $current_user = wp_get_current_user();
    $current_admin_email = !empty($current_user->user_email) ? $current_user->user_email : 'thevelvetreelproductions@gmail.com';

    // Group items by category
    $categories = array();
    foreach ($registry as $key => $item) {
        $cat = $item['category'];
        if (!isset($categories[$cat])) {
            $categories[$cat] = array(
                'icon'  => $item['category_icon'],
                'items' => array(),
            );
        }
        $categories[$cat]['items'][$key] = $item;
    }
    ?>

    <style>
        .velvet-sandbox-wrap {
            max-width: 1400px;
            margin: 20px 20px 40px 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            color: #1e1e24;
        }
        .velvet-header {
            background: linear-gradient(135deg, #111116 0%, #1c1c24 100%);
            color: #ffffff;
            padding: 24px 30px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .velvet-header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 0 0 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }
        .velvet-header p {
            color: #a1a1aa;
            margin: 0;
            font-size: 14px;
        }
        .velvet-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .velvet-stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .velvet-stat-card .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #71717a;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .velvet-stat-card .value {
            font-size: 20px;
            font-weight: 700;
            color: #09090b;
        }
        .velvet-stat-card .sub {
            font-size: 11px;
            color: #a1a1aa;
            margin-top: 2px;
        }
        .velvet-layout {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 24px;
            align-items: start;
        }
        .velvet-sidebar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .velvet-category-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 700;
            color: #a1a1aa;
            margin: 18px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #f4f4f5;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .velvet-category-title:first-child {
            margin-top: 0;
        }
        .velvet-nav-item {
            display: block;
            padding: 9px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            margin-bottom: 3px;
            transition: all 0.15s ease-in-out;
            border-left: 3px solid transparent;
        }
        .velvet-nav-item.active {
            background-color: #FE114B;
            color: #ffffff !important;
            font-weight: 600;
            border-left-color: #ffffff;
            box-shadow: 0 4px 12px rgba(254, 17, 75, 0.3);
        }
        .velvet-nav-item:not(.active) {
            color: #3f3f46;
        }
        .velvet-nav-item:not(.active):hover {
            background-color: #f4f4f5;
            color: #18181b;
            border-left-color: #FE114B;
        }
        .velvet-main-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .velvet-meta-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .velvet-meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .velvet-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
            margin: 20px 0;
            background: #f8fafc;
            padding: 18px;
            border-radius: 10px;
            border: 1px solid #edf2f7;
        }
        .velvet-meta-field strong {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 4px;
        }
        .velvet-meta-field span, .velvet-meta-field code {
            font-size: 13px;
            color: #0f172a;
        }
        .velvet-meta-field code {
            background: #e2e8f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
        }
        .velvet-conditions-box {
            background: #fafafa;
            border-left: 4px solid #FE114B;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin-top: 14px;
        }
        .velvet-conditions-box h4 {
            margin: 0 0 8px 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #27272a;
        }
        .velvet-conditions-box ul {
            margin: 0;
            padding-left: 18px;
            font-size: 13px;
            color: #52525b;
            line-height: 1.6;
        }
        .velvet-action-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .velvet-preview-container {
            background: #09090b;
            border: 1px solid #27272a;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        .velvet-preview-frame {
            width: 100%;
            max-width: 660px;
            height: 750px;
            border: none;
            border-radius: 12px;
            background: #09090b;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            transition: all 0.3s ease;
        }
        .velvet-viewport-controls {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 16px;
        }
        .velvet-btn-view {
            background: #18181b;
            color: #a1a1aa;
            border: 1px solid #27272a;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.15s ease;
        }
        .velvet-btn-view.active, .velvet-btn-view:hover {
            background: #FE114B;
            color: #ffffff;
            border-color: #FE114B;
        }
    </style>

    <div class="velvet-sandbox-wrap">
        <!-- Top Hero Banner -->
        <div class="velvet-header">
            <div>
                <h1><span>✉️</span> VelvetReel Email Automation Sandbox & Live Blueprint</h1>
                <p>Complete visual overview, runtime rules, trigger architecture, and real-time interactive rendering for all VelvetReel email flows.</p>
            </div>
            <div>
                <!-- Cron Trigger Action -->
                <form method="post" style="display: inline-block;">
                    <?php wp_nonce_field('velvet_cron_action', 'velvet_cron_nonce'); ?>
                    <button type="submit" name="velvet_trigger_onboarding_cron" class="button" style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.25); border-radius: 6px; padding: 6px 14px; font-weight: 600; cursor: pointer;">
                        ⚡ Run Onboarding Cron Now
                    </button>
                </form>
            </div>
        </div>

        <?php echo $notice; ?>

        <!-- System Stats / Diagnostics Bar -->
        <div class="velvet-stats-grid">
            <div class="velvet-stat-card">
                <div class="label">Total Members</div>
                <div class="value"><?php echo esc_html($total_members_count); ?></div>
                <div class="sub">Registered accounts</div>
            </div>
            <div class="velvet-stat-card">
                <div class="label">Published Talents</div>
                <div class="value" style="color: #10b981;"><?php echo esc_html($published_talents); ?></div>
                <div class="sub">Eligible matching profiles</div>
            </div>
            <div class="velvet-stat-card">
                <div class="label">Active Classifieds</div>
                <div class="value" style="color: #6366f1;"><?php echo esc_html($published_ads_count); ?></div>
                <div class="sub"><?php echo esc_html($pending_ads_count); ?> pending moderation</div>
            </div>
            <div class="velvet-stat-card">
                <div class="label">Next Onboarding Run</div>
                <div class="value" style="font-size: 16px; color: #f59e0b;"><?php echo esc_html($next_cron_display); ?></div>
                <div class="sub">Twice-daily scheduled cron</div>
            </div>
            <div class="velvet-stat-card">
                <div class="label">Mail System</div>
                <div class="value" style="font-size: 15px; color: #3b82f6;">Mailpit / wp_mail</div>
                <div class="sub">Ready for test dispatch</div>
            </div>
        </div>

        <!-- 2-Column Main Workspace -->
        <div class="velvet-layout">
            <!-- Left Sidebar: Categorized Workflow Navigation -->
            <div class="velvet-sidebar">
                <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #09090b; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0;">
                    Automated Workflows (<?php echo count($registry); ?>)
                </h3>

                <?php foreach ($categories as $cat_name => $cat_data): ?>
                    <div class="velvet-category-title">
                        <span><?php echo esc_html($cat_data['icon']); ?></span> <?php echo esc_html($cat_name); ?>
                    </div>
                    <?php foreach ($cat_data['items'] as $slug => $item): 
                        $is_active = ($slug === $current_template);
                    ?>
                        <a href="<?php echo esc_url(add_query_arg('preview_template', $slug, menu_page_url('velvet-email-preview', false))); ?>"
                           class="velvet-nav-item <?php echo $is_active ? 'active' : ''; ?>">
                            <?php echo esc_html($item['title']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>

            <!-- Right Area: Detailed Automation Architecture & Visual Sandbox -->
            <div class="velvet-main-content">
                <!-- Workflow Metadata & Conditions Card -->
                <div class="velvet-meta-panel">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; flex-wrap: wrap;">
                        <div>
                            <span class="velvet-meta-badge"><?php echo esc_html($selected_item['category']); ?></span>
                            <h2 style="margin: 8px 0 4px 0; font-size: 20px; font-weight: 700; color: #0f172a;">
                                <?php echo esc_html($selected_item['title']); ?>
                            </h2>
                            <p style="margin: 0; color: #64748b; font-size: 13px;">
                                <strong>Default Subject:</strong> <em>"<?php echo esc_html($selected_item['subject_sample']); ?>"</em>
                            </p>
                        </div>
                        <div>
                            <?php if ($template_exists): ?>
                                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    ✅ Template Active
                                </span>
                            <?php else: ?>
                                <span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    ⚠️ File Missing
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Technical Specs Grid -->
                    <div class="velvet-meta-grid">
                        <div class="velvet-meta-field">
                            <strong>Trigger Event / Hook</strong>
                            <code><?php echo esc_html($selected_item['hook_name']); ?></code>
                        </div>
                        <div class="velvet-meta-field">
                            <strong>Trigger Mechanism</strong>
                            <span><?php echo esc_html($selected_item['trigger_type']); ?></span>
                        </div>
                        <div class="velvet-meta-field">
                            <strong>Target Recipient</strong>
                            <span style="font-weight: 600; color: #1e293b;"><?php echo esc_html($selected_item['recipient']); ?></span>
                        </div>
                        <div class="velvet-meta-field">
                            <strong>Timing / Schedule</strong>
                            <span><?php echo esc_html($selected_item['timing']); ?></span>
                        </div>
                        <div class="velvet-meta-field">
                            <strong>Template File</strong>
                            <code>templates/emails/<?php echo esc_html($selected_item['template_file']); ?></code>
                        </div>
                        <div class="velvet-meta-field">
                            <strong>Primary CTA Target</strong>
                            <code><?php echo esc_html($selected_item['action_link']); ?></code>
                        </div>
                    </div>

                    <!-- Automation Guardrails & Business Logic -->
                    <div class="velvet-conditions-box">
                        <h4>⚙️ Execution Conditions & Automation Logic</h4>
                        <ul>
                            <?php foreach ($selected_item['conditions'] as $condition): ?>
                                <li><?php echo esc_html($condition); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Test Dispatch & Full View Action Bar -->
                <div class="velvet-action-bar">
                    <div>
                        <strong style="font-size: 14px; color: #0f172a;">🚀 1-Click Inbox Test Dispatcher</strong>
                        <div style="font-size: 12px; color: #64748b;">Dispatches sample email via velvet_send_email() to verify live client inbox layout.</div>
                    </div>
                    <form method="post" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <?php wp_nonce_field('velvet_email_test_action', 'velvet_email_test_nonce'); ?>
                        <input type="email" name="target_email" value="<?php echo esc_attr($current_admin_email); ?>" required style="padding: 7px 12px; border-radius: 6px; border: 1px solid #cbd5e1; width: 260px; font-size: 13px;" placeholder="recipient@example.com">
                        <button type="submit" name="velvet_send_test_email" class="button button-primary" style="background: #FE114B; border-color: #FE114B; border-radius: 6px; padding: 3px 14px; font-weight: 600; box-shadow: 0 2px 6px rgba(254,17,75,0.3);">
                            Send Test Email
                        </button>
                        <a href="<?php echo esc_url($preview_url); ?>" target="_blank" class="button" style="border-radius: 6px; padding: 3px 12px;" title="Open live render in standalone browser tab">
                            ↗ Open Tab
                        </a>
                    </form>
                </div>

                <!-- Interactive Visual Preview Device Frame -->
                <div class="velvet-preview-container">
                    <div class="velvet-viewport-controls">
                        <button type="button" class="velvet-btn-view active" onclick="setPreviewWidth('660px', this)">🖥️ Desktop / Tablet (660px)</button>
                        <button type="button" class="velvet-btn-view" onclick="setPreviewWidth('390px', this)">📱 Mobile View (390px)</button>
                    </div>

                    <iframe id="velvet_email_iframe" src="<?php echo esc_url($preview_url); ?>" class="velvet-preview-frame" title="Email Live Preview Frame"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setPreviewWidth(width, btn) {
            var iframe = document.getElementById('velvet_email_iframe');
            if (iframe) {
                iframe.style.maxWidth = width;
            }
            var buttons = document.querySelectorAll('.velvet-btn-view');
            buttons.forEach(function(b) { b.classList.remove('active'); });
            if (btn) {
                btn.classList.add('active');
            }
        }
    </script>
    <?php
}
