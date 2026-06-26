<?php
/**
 * AJAX request handlers
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// All AJAX handlers are already included in other files:
// - handle_dashboard_image_upload() in user-management.php
// - handle_profile_image_ajax() in user-management.php
// - wp_ajax_save_talent_progress() in talent-forms.php

// This file exists as a placeholder for any future AJAX handlers
// that don't fit into the other categories.

/**
 * AJAX handler to generate/update shareable token for talent profile
 */
function ajax_generate_shareable_token() {
    // Verify nonce
    check_ajax_referer('shareable_token_nonce', 'nonce');
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Unauthorized']);
        wp_die();
    }
    
    // Get talent ID
    $talent_id = isset($_POST['talent_id']) ? intval($_POST['talent_id']) : 0;
    
    if (!$talent_id) {
        wp_send_json_error(['message' => 'Invalid talent ID']);
        wp_die();
    }
    
    // Verify user has permission to edit this talent
    $talent_post = get_post($talent_id);
    if (!$talent_post || $talent_post->post_type !== 'talent') {
        wp_send_json_error(['message' => 'Invalid talent post']);
        wp_die();
    }
    
    if (get_post_field('post_author', $talent_id) != get_current_user_id() && !current_user_can('edit_others_posts')) {
        wp_send_json_error(['message' => 'Permission denied']);
        wp_die();
    }
    
    // Generate unique token
    $token = bin2hex(random_bytes(16)); // 32-character hex string
    
    // Save token and enable public sharing - save as '1' for proper comparison
    update_post_meta($talent_id, '_talent_shareable_token', $token);
    update_post_meta($talent_id, '_talent_public_sharing_enabled', '1');
    
    // Generate the public URL
    $public_url = home_url('/p/' . $token);
    
    wp_send_json_success([
        'token' => $token,
        'public_url' => $public_url,
        'sharing_enabled' => true
    ]);
    
    wp_die();
}
add_action('wp_ajax_generate_shareable_token', 'ajax_generate_shareable_token');

/**
 * AJAX handler to toggle public sharing on/off
 */
function ajax_toggle_public_sharing() {
    // Verify nonce
    check_ajax_referer('shareable_token_nonce', 'nonce');
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Unauthorized']);
        wp_die();
    }
    
    // Get talent ID and new status
    $talent_id = isset($_POST['talent_id']) ? intval($_POST['talent_id']) : 0;
    $enabled = isset($_POST['enabled']) ? boolval($_POST['enabled']) : false;
    
    if (!$talent_id) {
        wp_send_json_error(['message' => 'Invalid talent ID']);
        wp_die();
    }
    
    // Verify user has permission
    $talent_post = get_post($talent_id);
    if (!$talent_post || $talent_post->post_type !== 'talent') {
        wp_send_json_error(['message' => 'Invalid talent post']);
        wp_die();
    }
    
    if (get_post_field('post_author', $talent_id) != get_current_user_id() && !current_user_can('edit_others_posts')) {
        wp_send_json_error(['message' => 'Permission denied']);
        wp_die();
    }
    
    // Update sharing status - save as '1' or empty string for proper comparison
    $enabled_value = $enabled ? '1' : '';
    update_post_meta($talent_id, '_talent_public_sharing_enabled', $enabled_value);
    
    // Get current token
    $token = get_post_meta($talent_id, '_talent_shareable_token', true);
    $public_url = $token ? home_url('/p/' . $token) : '';
    
    wp_send_json_success([
        'sharing_enabled' => $enabled,
        'public_url' => $public_url,
        'token' => $token
    ]);
    
    wp_die();
}
add_action('wp_ajax_toggle_public_sharing', 'ajax_toggle_public_sharing');

/**
 * AJAX handler to express interest in an advertisement
 */
function ajax_express_advertisement_interest() {
    // Verify nonce
    check_ajax_referer('express_interest_nonce', 'nonce');
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to express interest.']);
        wp_die();
    }
    
    // Get advertisement ID
    $advertisement_id = isset($_POST['advertisement_id']) ? intval($_POST['advertisement_id']) : 0;
    
    if (!$advertisement_id) {
        wp_send_json_error(['message' => 'Invalid advertisement ID']);
        wp_die();
    }
    
    // Verify the advertisement exists
    $advertisement = get_post($advertisement_id);
    if (!$advertisement || $advertisement->post_type !== 'advertisement') {
        wp_send_json_error(['message' => 'Invalid advertisement']);
        wp_die();
    }
    
    // Get current user
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    
    // Include access control functions and define constants
    if (!function_exists('has_membership_plan')) {
        require_once get_stylesheet_directory() . '/includes/access-control.php';
    }
    
    // Define constants if not already defined
    if (!defined('ADVERTISEMENT_PLAN_LEVEL')) {
        define('ADVERTISEMENT_PLAN_LEVEL', 5);
    }
    if (!defined('PORTFOLIO_PLAN_LEVEL')) {
        define('PORTFOLIO_PLAN_LEVEL', 2);
    }
    if (!defined('PORTFOLIO_PLAN_LEVEL_UPGRADE')) {
        define('PORTFOLIO_PLAN_LEVEL_UPGRADE', 3);
    }
    
    // Check if user has any paid membership (advertisement or portfolio plan), explicitly excluding FREE_PLAN_LEVEL (4)
    $is_paid_member = false;
    $user_plans = get_user_meta($user_id, 'membership_plans', true);
    if (!is_array($user_plans)) $user_plans = array();
    
    $current_level = class_exists('SwpmMemberUtils') && SwpmMemberUtils::is_member_logged_in() ? SwpmMemberUtils::get_logged_in_members_level() : 0;
    
    $paid_levels = [ADVERTISEMENT_PLAN_LEVEL, PORTFOLIO_PLAN_LEVEL, PORTFOLIO_PLAN_LEVEL_UPGRADE];
    
    if (in_array($current_level, $paid_levels)) {
        $is_paid_member = true;
    } else {
        foreach ($paid_levels as $level) {
            if (in_array($level, $user_plans)) {
                $is_paid_member = true;
                break;
            }
        }
    }
    
    // If user is not a paid member, they cannot express interest
    if (!$is_paid_member) {
        wp_send_json_error(['message' => 'You need a paid membership to express interest. Please upgrade your plan.']);
        wp_die();
    }
    
    // Check if user has already expressed interest
    $existing_interests = get_post_meta($advertisement_id, '_advertisement_interests', true);
    if (!is_array($existing_interests)) {
        $existing_interests = [];
    }
    
    // Don't allow the owner to express interest in their own advertisement
    $advertisement_author = $advertisement->post_author;
    if ($advertisement_author == $user_id) {
        wp_send_json_error(['message' => 'You cannot express interest in your own advertisement.']);
        wp_die();
    }
    
    // Check if user already expressed interest
    if (in_array($user_id, $existing_interests)) {
        wp_send_json_error(['message' => 'You have already expressed interest in this advertisement.']);
        wp_die();
    }
    
    // Add user to interests list
    $existing_interests[] = $user_id;
    update_post_meta($advertisement_id, '_advertisement_interests', $existing_interests);
    
    // Also save the interest date - ensure proper array structure
    $interest_dates = get_post_meta($advertisement_id, '_advertisement_interest_dates', true);
    if (!is_array($interest_dates)) {
        $interest_dates = array();
    }
    // Ensure we're using user_id as the array key with datetime as value
    $interest_dates[$user_id] = current_time('mysql');
    update_post_meta($advertisement_id, '_advertisement_interest_dates', $interest_dates);
    
    // Get advertisement title and author
    $advertisement_title = get_the_title($advertisement_id);
    $advertisement_url = get_permalink($advertisement_id);
    
    // Get user details
    $user_username = $current_user->user_login;
    $user_firstname = $current_user->first_name;
    $user_lastname = $current_user->last_name;
    $user_fullname = $current_user->display_name;
    $user_email = $current_user->user_email;
    
    // Get phone number from user meta
    $user_phone = get_user_meta($user_id, 'phone_number', true);
    if (empty($user_phone)) {
        $user_phone = get_user_meta($user_id, 'phone', true);
    }
    if (empty($user_phone)) {
        $user_phone = get_user_meta($user_id, 'user_phone', true);
    }
    if (empty($user_phone)) {
        $user_phone = 'Not provided';
    }
    
    // Get advertisement author details
    $advertisement_owner = get_userdata($advertisement_author);
    $owner_email = $advertisement_owner->user_email;
    $owner_name = $advertisement_owner->display_name;
    
    // Send email notification to advertisement owner
    $subject = 'New Interest Expressed - ' . get_bloginfo('name');
    $message = "Hello {$owner_name},\n\n";
    $message .= "A user has expressed interest in your advertisement: {$advertisement_title}\n\n";
    $message .= "Interested User Details:\n";
    $message .= "- Username: {$user_username}\n";
    $message .= "- First Name: {$user_firstname}\n";
    $message .= "- Last Name: {$user_lastname}\n";
    $message .= "- Full Name: {$user_fullname}\n";
    $message .= "- Email: {$user_email}\n";
    $message .= "- Phone: {$user_phone}\n\n";
    $message .= "View your advertisement: {$advertisement_url}\n\n";
    $message .= "This is an automated notification from " . get_bloginfo('name');
    
    $headers = ['Content-Type: text/plain; charset=UTF-8', 'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'];
    
    // Send email to advertisement owner
    wp_mail($owner_email, $subject, $message, $headers);
    
    // Also notify admin
    $admin_email = get_option('admin_email');
    $admin_subject = '[Admin Notification] New Interest Expressed - ' . get_bloginfo('name');
    $admin_message = "Admin Notification - New Interest Expressed\n\n";
    $admin_message .= "Advertisement: {$advertisement_title}\n";
    $admin_message .= "Advertisement ID: {$advertisement_id}\n";
    $admin_message .= "Advertisement URL: {$advertisement_url}\n\n";
    $admin_message .= "Interested User Details:\n";
    $admin_message .= "- Username: {$user_username}\n";
    $admin_message .= "- First Name: {$user_firstname}\n";
    $admin_message .= "- Last Name: {$user_lastname}\n";
    $admin_message .= "- Full Name: {$user_fullname}\n";
    $admin_message .= "- Email: {$user_email}\n";
    $admin_message .= "- Phone: {$user_phone}\n\n";
    $admin_message .= "Advertisement Owner: {$owner_name} ({$owner_email})\n";
    
    wp_mail($admin_email, $admin_subject, $admin_message, $headers);
    
    wp_send_json_success([
        'message' => 'Thank you for your interest! The advertiser has been notified.',
        'interests_count' => count($existing_interests)
    ]);
    
    wp_die();
}
add_action('wp_ajax_express_advertisement_interest', 'ajax_express_advertisement_interest');
add_action('wp_ajax_nopriv_express_advertisement_interest', 'ajax_express_advertisement_interest');