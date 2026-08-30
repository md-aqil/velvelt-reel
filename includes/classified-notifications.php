<?php
/**
 * Classified Notification & Audience Matching Engine
 *
 * Handles targeted notifications when classifieds are published,
 * interest-submission notifications to advertisers & members,
 * branded HTML email rendering, and opt-out/unsubscribe management.
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render an HTML email template with given arguments
 *
 * @param string $template_name Filename in templates/emails/
 * @param array  $args          Data variables to pass to the template
 * @return string HTML output
 */
function velvet_render_email_template($template_name, $args = array()) {
    $template_file = get_stylesheet_directory() . '/templates/emails/' . ltrim($template_name, '/');
    if (!file_exists($template_file)) {
        return '';
    }

    // Extract variables for use in template
    if (is_array($args)) {
        extract($args);
    }

    ob_start();
    include $template_file;
    return ob_get_clean();
}

/**
 * Helper to dispatch an HTML email with VelvetReel brand headers
 *
 * @param string|array $to
 * @param string       $subject
 * @param string       $html_content
 * @param array        $custom_headers
 * @return bool
 */
function velvet_send_email($to, $subject, $html_content, $custom_headers = array()) {
    if (empty($to) || empty($html_content)) {
        return false;
    }

    $site_name   = get_bloginfo('name');
    $default_from = 'thevelvetreelproductions@gmail.com';
    $from_email  = apply_filters('velvet_email_from_address', $default_from);
    $from_name   = apply_filters('velvet_email_from_name', !empty($site_name) ? $site_name : 'VelvetReel');
    $reply_to    = apply_filters('velvet_email_reply_to', $default_from);

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $from_name . ' <' . $from_email . '>',
        'Reply-To: ' . $from_name . ' <' . $reply_to . '>',
    );

    if (!empty($custom_headers) && is_array($custom_headers)) {
        $headers = array_merge($headers, $custom_headers);
    }

    return wp_mail($to, $subject, $html_content, $headers);
}

// Global WordPress Mail From branding hooks
add_filter('wp_mail_from', function($email) {
    if (empty($email) || strpos($email, 'wordpress@') !== false || strpos($email, 'localhost') !== false) {
        return 'thevelvetreelproductions@gmail.com';
    }
    return $email;
});

add_filter('wp_mail_from_name', function($name) {
    if (empty($name) || $name === 'WordPress') {
        return 'VelvetReel';
    }
    return $name;
});

/**
 * Generate a secure HMAC unsubscribe token for a user
 *
 * @param int $user_id
 * @return string
 */
function velvet_generate_unsubscribe_token($user_id) {
    $user = get_userdata($user_id);
    if (!$user) {
        return '';
    }
    return hash_hmac('sha256', $user->ID . '|' . $user->user_email . '|velvet_unsub', wp_salt('auth'));
}

/**
 * Generate the direct unsubscribe URL for a user
 *
 * @param int $user_id
 * @return string
 */
function velvet_get_unsubscribe_url($user_id) {
    $token = velvet_generate_unsubscribe_token($user_id);
    if (empty($token)) {
        return home_url('/');
    }
    return add_query_arg(
        array(
            'velvet_action' => 'unsubscribe',
            'uid'           => $user_id,
            'token'         => $token,
        ),
        home_url('/')
    );
}

/**
 * Handle unsubscribe requests from email links
 */
function velvet_handle_unsubscribe_request() {
    if (isset($_GET['velvet_action']) && $_GET['velvet_action'] === 'unsubscribe') {
        $user_id = isset($_GET['uid']) ? intval($_GET['uid']) : 0;
        $token   = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';

        if ($user_id && !empty($token)) {
            $expected_token = velvet_generate_unsubscribe_token($user_id);
            if (hash_equals($expected_token, $token)) {
                update_user_meta($user_id, '_velvet_notifications_optout', 1);

                // Output branded confirmation page
                wp_die(
                    '<div style="max-width:500px; margin:80px auto; font-family:sans-serif; text-align:center; padding:40px; background:#121215; color:#fff; border-radius:16px; border:1px solid #222;">
                        <h2 style="color:#FE114B; margin-top:0;">Preferences Updated</h2>
                        <p style="color:#a1a1aa; line-height:1.6;">You have successfully unsubscribed from VelvetReel opportunity notification emails.</p>
                        <p style="margin-top:30px;"><a href="' . esc_url(home_url('/')) . '" style="color:#fff; background:#FE114B; padding:10px 24px; border-radius:8px; text-decoration:none; font-weight:bold;">Return to VelvetReel</a></p>
                    </div>',
                    'Unsubscribe Successful',
                    array('response' => 200)
                );
            }
        }
    }
}
add_action('init', 'velvet_handle_unsubscribe_request');

/**
 * Match eligible and opted-in talent profiles for a specific advertisement
 *
 * @param int $ad_id
 * @return array Array of matching recipients with details
 */
function velvet_get_matching_talents_for_ad($ad_id) {
    $ad = get_post($ad_id);
    if (!$ad || $ad->post_type !== 'advertisement') {
        return array();
    }

    // Retrieve target criteria from ad meta
    $target_domains    = get_post_meta($ad_id, '_target_domains', true);
    $target_roles      = get_post_meta($ad_id, '_target_roles', true);
    $target_gender     = strtolower(trim((string) get_post_meta($ad_id, '_target_gender', true)));
    $target_age_group  = strtolower(trim((string) get_post_meta($ad_id, '_target_age_group', true)));
    $target_location   = strtolower(trim((string) get_post_meta($ad_id, '_target_location', true)));
    $target_experience = strtolower(trim((string) get_post_meta($ad_id, '_target_experience', true)));

    // Normalize target domains
    if (empty($target_domains)) {
        $target_domains = array();
    } elseif (!is_array($target_domains)) {
        $target_domains = array($target_domains);
    }
    $target_domains = array_filter(array_map('trim', $target_domains), function($d) {
        return !empty($d) && strtolower($d) !== 'all';
    });

    // Normalize target roles
    if (empty($target_roles)) {
        $target_roles = array();
    } elseif (!is_array($target_roles)) {
        $target_roles = array($target_roles);
    }
    $target_roles = array_filter(array_map('trim', $target_roles), function($r) {
        return !empty($r) && strtolower($r) !== 'all';
    });

    // Query all published talent profiles
    $talent_query = new WP_Query(array(
        'post_type'      => 'talent',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));

    $matched_talents = array();
    $processed_users = array();

    // Don't send notification to the ad author
    $ad_author_id = (int) $ad->post_author;

    foreach ($talent_query->posts as $talent_id) {
        $talent_post = get_post($talent_id);
        $user_id = (int) $talent_post->post_author;

        if ($user_id === $ad_author_id || in_array($user_id, $processed_users)) {
            continue;
        }

        // Check if talent has opted out of notifications
        $opted_out = (int) get_user_meta($user_id, '_velvet_notifications_optout', true);
        if ($opted_out === 1) {
            continue;
        }

        $user_obj = get_userdata($user_id);
        if (!$user_obj || empty($user_obj->user_email)) {
            continue;
        }

        // Retrieve talent profile attributes
        $talent_role       = (string) get_post_meta($talent_id, '_talent_role', true);
        $talent_domain     = (string) get_post_meta($talent_id, '_talent_domain', true);
        $talent_gender     = strtolower(trim((string) get_post_meta($talent_id, '_talent_gender', true)));
        $talent_age_group  = strtolower(trim((string) get_post_meta($talent_id, '_talent_age_group', true)));
        $talent_state      = strtolower(trim((string) get_post_meta($talent_id, '_talent_state', true)));
        $talent_country    = strtolower(trim((string) get_post_meta($talent_id, '_talent_country', true)));
        $talent_preferred  = strtolower(trim((string) get_post_meta($talent_id, '_talent_preferred_locations', true)));
        $talent_experience = strtolower(trim((string) get_post_meta($talent_id, '_talent_years_active', true)));

        $is_match = true;
        $match_reasons = array();

        // 1. Domain Check
        if (!empty($target_domains)) {
            $domain_matched = false;
            foreach ($target_domains as $req_domain) {
                $req_slug = sanitize_title($req_domain);
                $talent_domain_slug = sanitize_title($talent_domain);
                if (stripos($talent_domain, $req_domain) !== false || stripos($talent_domain_slug, $req_slug) !== false) {
                    $domain_matched = true;
                    $match_reasons[] = 'Domain: ' . (!empty($talent_domain) ? $talent_domain : $req_domain);
                    break;
                }
            }
            if (!$domain_matched && !empty($target_roles)) {
                // Let role check handle it if roles are specified
            } elseif (!$domain_matched) {
                $is_match = false;
            }
        }

        // 2. Role Check
        if ($is_match && !empty($target_roles)) {
            $role_matched = false;
            foreach ($target_roles as $req_role) {
                $req_slug = sanitize_title($req_role);
                $talent_role_slug = sanitize_title($talent_role);
                $talent_domain_slug = sanitize_title($talent_domain);

                if (stripos($talent_role, $req_role) !== false ||
                    stripos($talent_role_slug, $req_slug) !== false ||
                    stripos($talent_domain, $req_role) !== false ||
                    stripos($talent_domain_slug, $req_slug) !== false) {
                    $role_matched = true;
                    $match_reasons[] = 'Role: ' . (!empty($talent_role) ? $talent_role : $talent_domain);
                    break;
                }
            }
            if (!$role_matched) {
                $is_match = false;
            }
        }

        // 3. Gender Check
        if ($is_match && !empty($target_gender) && $target_gender !== 'all' && $target_gender !== 'any') {
            if (empty($talent_gender) || (stripos($talent_gender, $target_gender) === false && stripos($target_gender, $talent_gender) === false)) {
                $is_match = false;
            } else {
                $match_reasons[] = 'Gender: ' . ucfirst($talent_gender);
            }
        }

        // 4. Age Group Check
        if ($is_match && !empty($target_age_group) && $target_age_group !== 'all' && $target_age_group !== 'any') {
            if (empty($talent_age_group) || stripos($talent_age_group, $target_age_group) === false) {
                $is_match = false;
            } else {
                $match_reasons[] = 'Age Group: ' . $talent_age_group;
            }
        }

        // 5. Location Check
        if ($is_match && !empty($target_location) && $target_location !== 'all' && $target_location !== 'remote' && $target_location !== 'any') {
            $loc_match = false;
            if (!empty($talent_state) && stripos($target_location, $talent_state) !== false) {
                $loc_match = true;
            }
            if (!empty($talent_country) && stripos($target_location, $talent_country) !== false) {
                $loc_match = true;
            }
            if (!empty($talent_preferred) && stripos($talent_preferred, $target_location) !== false) {
                $loc_match = true;
            }
            if ($loc_match) {
                $match_reasons[] = 'Location: ' . ucfirst($target_location);
            } else {
                $is_match = false;
            }
        }

        if ($is_match) {
            $processed_users[] = $user_id;
            $matched_talents[] = array(
                'user_id'          => $user_id,
                'talent_id'        => $talent_id,
                'email'            => $user_obj->user_email,
                'name'             => !empty($user_obj->display_name) ? $user_obj->display_name : $user_obj->first_name,
                'matching_reasons' => !empty($match_reasons) ? array_unique($match_reasons) : array('Profile & Preferences Match'),
                'unsubscribe_url'  => velvet_get_unsubscribe_url($user_id),
            );
        }
    }

    return $matched_talents;
}

/**
 * Dispatch targeted classified notification emails to matched audience
 *
 * @param int  $ad_id
 * @param bool $force_resend If true, bypasses duplicate check
 * @return array Results summary
 */
function velvet_send_targeted_classified_notifications($ad_id, $force_resend = false) {
    $ad = get_post($ad_id);
    if (!$ad || $ad->post_type !== 'advertisement' || $ad->post_status !== 'publish') {
        return array('success' => false, 'message' => 'Invalid or unpublished advertisement');
    }

    // Duplicate Prevention Check
    $already_sent = get_post_meta($ad_id, '_advertisement_notifications_sent', true);
    if ($already_sent && !$force_resend) {
        return array(
            'success' => false,
            'message' => 'Notifications already sent on ' . date('M j, Y H:i:s', $already_sent) . '. Skipped duplicate send.',
            'skipped' => true,
        );
    }

    $recipients = velvet_get_matching_talents_for_ad($ad_id);
    if (empty($recipients)) {
        update_post_meta($ad_id, '_advertisement_notifications_sent', time());
        update_post_meta($ad_id, '_advertisement_notifications_count', 0);
        return array('success' => true, 'count' => 0, 'message' => 'No matching active talent profiles found.');
    }

    // Prepare advertisement details
    $ad_title       = get_the_title($ad_id);
    $ad_url         = get_permalink($ad_id);
    $ad_type        = get_post_meta($ad_id, '_advertisement_type', true);
    $ad_location    = get_post_meta($ad_id, '_advertisement_location', true);
    if (empty($ad_location)) {
        $ad_location = get_post_meta($ad_id, 'ad_location', true);
    }
    $ad_budget      = get_post_meta($ad_id, '_advertisement_budget', true);
    $ad_image_url   = get_the_post_thumbnail_url($ad_id, 'large');

    // Get categories
    $categories = wp_get_post_terms($ad_id, 'advertisement_category', array('fields' => 'names'));
    $ad_category = !empty($categories) && !is_wp_error($categories) ? implode(', ', $categories) : ucfirst($ad_type);

    // Prepare short excerpt
    $raw_content = wp_strip_all_tags($ad->post_content);
    $ad_excerpt  = wp_trim_words($raw_content, 35, '...');

    $sent_count = 0;
    $errors     = array();

    foreach ($recipients as $recipient) {
        $template_args = array(
            'recipient_name'   => $recipient['name'],
            'ad_title'         => $ad_title,
            'ad_url'           => $ad_url,
            'ad_category'      => $ad_category,
            'ad_location'      => $ad_location,
            'ad_type'          => $ad_type,
            'ad_budget'        => $ad_budget,
            'ad_excerpt'       => $ad_excerpt,
            'ad_image_url'     => $ad_image_url,
            'matching_reasons' => $recipient['matching_reasons'],
            'unsubscribe_url'  => $recipient['unsubscribe_url'],
        );

        $html_content = velvet_render_email_template('email-targeted-classified.php', $template_args);
        $subject      = '⚡ New Opportunity Match: ' . $ad_title;

        $mail_sent = velvet_send_email($recipient['email'], $subject, $html_content);
        if ($mail_sent) {
            $sent_count++;
        } else {
            $errors[] = 'Failed to send to: ' . $recipient['email'];
        }
    }

    // Save tracking metadata
    update_post_meta($ad_id, '_advertisement_notifications_sent', time());
    update_post_meta($ad_id, '_advertisement_notifications_count', $sent_count);
    update_post_meta($ad_id, '_advertisement_notifications_last_log', array(
        'timestamp' => current_time('mysql'),
        'count'     => $sent_count,
        'errors'    => $errors,
    ));

    return array(
        'success' => true,
        'count'   => $sent_count,
        'errors'  => $errors,
    );
}

/**
 * Hook into advertisement status transitions to auto-trigger notifications
 */
function velvet_on_advertisement_status_transition($new_status, $old_status, $post) {
    if (!$post || $post->post_type !== 'advertisement') {
        return;
    }

    // Trigger only when transitioning to 'publish' from a non-publish state (e.g. pending, draft, new)
    if ($new_status === 'publish' && $old_status !== 'publish') {
        velvet_send_targeted_classified_notifications($post->ID, false);
    }
}
add_action('transition_post_status', 'velvet_on_advertisement_status_transition', 10, 3);

/**
 * Send immediate interest notifications to Advertiser, Member confirmation, and Admin
 *
 * @param int $ad_id
 * @param int $user_id
 * @return bool
 */
function velvet_send_interest_notifications($ad_id, $user_id) {
    $ad = get_post($ad_id);
    if (!$ad || $ad->post_type !== 'advertisement') {
        return false;
    }

    $applicant = get_userdata($user_id);
    if (!$applicant) {
        return false;
    }

    // 1. Fetch Advertiser Details
    $advertiser_email = get_post_meta($ad_id, '_advertisement_contact_email', true);
    $ad_author_id     = (int) $ad->post_author;
    $ad_author        = get_userdata($ad_author_id);

    if (empty($advertiser_email) && $ad_author) {
        $advertiser_email = $ad_author->user_email;
    }
    $advertiser_name = $ad_author ? $ad_author->display_name : 'Advertiser';

    // 2. Fetch Applicant Details & Talent Profile
    $applicant_name     = !empty($applicant->display_name) ? $applicant->display_name : $applicant->user_login;
    $applicant_username = $applicant->user_login;
    $applicant_email    = $applicant->user_email;

    // Get phone from user meta or talent profile
    $applicant_phone = get_user_meta($user_id, 'phone_number', true);
    if (empty($applicant_phone)) {
        $applicant_phone = get_user_meta($user_id, 'phone', true);
    }
    if (empty($applicant_phone)) {
        $applicant_phone = get_user_meta($user_id, 'user_phone', true);
    }

    // Find applicant's talent post
    $talent_posts = get_posts(array(
        'post_type'      => 'talent',
        'post_status'    => 'any',
        'author'         => $user_id,
        'posts_per_page' => 1,
    ));

    $applicant_role         = '';
    $applicant_location     = '';
    $applicant_age_group    = '';
    $applicant_years_active = '';
    $applicant_profile_url  = home_url('/talent-profile/?user_id=' . $user_id);

    if (!empty($talent_posts)) {
        $talent_post_id = $talent_posts[0]->ID;
        $applicant_role = get_post_meta($talent_post_id, '_talent_role', true);
        if (empty($applicant_role)) {
            $applicant_role = get_post_meta($talent_post_id, '_talent_domain', true);
        }

        $state   = get_post_meta($talent_post_id, '_talent_state', true);
        $country = get_post_meta($talent_post_id, '_talent_country', true);
        $applicant_location = trim($state . ($state && $country ? ', ' : '') . $country);

        $applicant_age_group    = get_post_meta($talent_post_id, '_talent_age_group', true);
        $applicant_years_active = get_post_meta($talent_post_id, '_talent_years_active', true);
        
        // Generate or retrieve the public shareable portfolio URL (/p/{token}/) so advertiser can view without login
        $share_token     = get_post_meta($talent_post_id, '_talent_shareable_token', true);
        $sharing_enabled = get_post_meta($talent_post_id, '_talent_public_sharing_enabled', true);

        if (empty($share_token) || $sharing_enabled !== '1') {
            $share_token = bin2hex(random_bytes(16));
            update_post_meta($talent_post_id, '_talent_shareable_token', $share_token);
            update_post_meta($talent_post_id, '_talent_public_sharing_enabled', '1');
        }

        $applicant_profile_url = home_url('/p/' . $share_token);

        if (empty($applicant_phone)) {
            $applicant_phone = get_post_meta($talent_post_id, '_talent_phone', true);
        }
    }

    if (empty($applicant_phone)) {
        $applicant_phone = 'Not provided';
    }

    $ad_title        = get_the_title($ad_id);
    $ad_url          = get_permalink($ad_id);
    $submission_time = date('M j, Y g:i A');

    // 3. Send Email to Advertiser
    if (!empty($advertiser_email)) {
        $advertiser_args = array(
            'advertiser_name'        => $advertiser_name,
            'ad_title'               => $ad_title,
            'ad_url'                 => $ad_url,
            'applicant_name'         => $applicant_name,
            'applicant_username'     => $applicant_username,
            'applicant_email'        => $applicant_email,
            'applicant_phone'        => $applicant_phone,
            'applicant_role'         => $applicant_role,
            'applicant_location'     => $applicant_location,
            'applicant_age_group'    => $applicant_age_group,
            'applicant_years_active' => $applicant_years_active,
            'applicant_profile_url'  => $applicant_profile_url,
            'submission_time'        => $submission_time,
        );

        $advertiser_html = velvet_render_email_template('email-advertiser-interest.php', $advertiser_args);
        $advertiser_subj = '🔔 New Candidate Application: ' . $ad_title . ' (' . $applicant_name . ')';
        velvet_send_email($advertiser_email, $advertiser_subj, $advertiser_html);
    }

    // 4. Send Confirmation Email to Applicant Member
    if (!empty($applicant_email)) {
        $categories = wp_get_post_terms($ad_id, 'advertisement_category', array('fields' => 'names'));
        $ad_category = !empty($categories) && !is_wp_error($categories) ? implode(', ', $categories) : '';

        $member_args = array(
            'member_name'     => $applicant_name,
            'ad_title'        => $ad_title,
            'ad_url'          => $ad_url,
            'ad_category'     => $ad_category,
            'ad_location'     => get_post_meta($ad_id, '_advertisement_location', true),
            'submission_time' => $submission_time,
        );

        $member_html = velvet_render_email_template('email-member-interest-confirmation.php', $member_args);
        $member_subj = '✓ Application Confirmed: ' . $ad_title;
        velvet_send_email($applicant_email, $member_subj, $member_html);
    }

    // 5. Send Admin Monitoring Alert
    $admin_email = get_option('admin_email');
    if (!empty($admin_email)) {
        $admin_args = array(
            'ad_title'              => $ad_title,
            'ad_id'                 => $ad_id,
            'ad_url'                => $ad_url,
            'advertiser_name'       => $advertiser_name,
            'advertiser_email'      => $advertiser_email,
            'applicant_name'        => $applicant_name,
            'applicant_username'    => $applicant_username,
            'applicant_email'       => $applicant_email,
            'applicant_phone'       => $applicant_phone,
            'applicant_profile_url' => $applicant_profile_url,
            'submission_time'       => $submission_time,
        );

        $admin_html = velvet_render_email_template('email-admin-interest-alert.php', $admin_args);
        $admin_subj = '[Admin Alert] New Interest on Classified #' . $ad_id . ' - ' . $ad_title;
        velvet_send_email($admin_email, $admin_subj, $admin_html);
    }

    return true;
}

/**
 * Send branded OTP verification email
 *
 * @param string $email
 * @param string $otp_code
 * @param string $type ('signup', 'reset_password', 'general')
 * @param string $recipient_name
 * @param int $expiry_minutes
 * @return bool
 */
function velvet_send_otp_email($email, $otp_code, $type = 'signup', $recipient_name = '', $expiry_minutes = 10) {
    if (empty($email) || !is_email($email) || empty($otp_code)) {
        return false;
    }

    $template_args = array(
        'otp_code'          => $otp_code,
        'recipient_name'    => $recipient_name,
        'verification_type' => $type,
        'expiry_minutes'    => $expiry_minutes,
    );

    $html_content = velvet_render_email_template('email-otp-verification.php', $template_args);
    
    if ($type === 'reset_password') {
        $subject = 'Your VelvetReel Password Reset Code: ' . $otp_code;
    } else {
        $subject = 'Your VelvetReel Verification Code: ' . $otp_code;
    }

    return velvet_send_email($email, $subject, $html_content);
}

