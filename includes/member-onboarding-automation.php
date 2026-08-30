<?php
/**
 * Member Onboarding, Welcome & Staged Reminder Automation Engine
 *
 * Handles:
 * 1. Immediate welcome email on user registration with duplicate safeguards.
 * 2. Staged portfolio completion reminders (Day 2, Day 5).
 * 3. Staged subscription/payment reminders (Day 10, Day 14).
 * 4. Exit/Stop rules: Automatically stops reminders once portfolio is complete and paid subscription is active.
 * 5. Unsubscribe & suppression safeguards.
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Check if a talent profile is complete
 *
 * @param int $user_id
 * @return bool
 */
function velvet_is_talent_profile_complete($user_id) {
    if (!$user_id) {
        return false;
    }

    $talent_posts = get_posts(array(
        'post_type'      => 'talent',
        'author'         => $user_id,
        'post_status'    => array('publish', 'pending'),
        'posts_per_page' => 1,
    ));

    if (empty($talent_posts)) {
        return false;
    }

    $talent_id = $talent_posts[0]->ID;
    $role      = get_post_meta($talent_id, '_talent_role', true);
    $domain    = get_post_meta($talent_id, '_talent_domain', true);

    // Profile is considered complete if it has published/pending status and domain/role
    return !empty($role) || !empty($domain);
}

/**
 * Check if a user has an active paid subscription or approval
 *
 * @param int $user_id
 * @return bool
 */
function velvet_is_user_subscribed($user_id) {
    if (!$user_id) {
        return false;
    }

    // Check membership plans array
    $user_plans = get_user_meta($user_id, 'membership_plans', true);
    if (!is_array($user_plans)) {
        $user_plans = array();
    }

    $paid_levels = array(
        defined('ADVERTISEMENT_PLAN_LEVEL') ? ADVERTISEMENT_PLAN_LEVEL : 5,
        defined('PORTFOLIO_PLAN_LEVEL') ? PORTFOLIO_PLAN_LEVEL : 2,
        defined('PORTFOLIO_PLAN_LEVEL_UPGRADE') ? PORTFOLIO_PLAN_LEVEL_UPGRADE : 3,
        defined('SIX_MONTH_PLAN_LEVEL') ? SIX_MONTH_PLAN_LEVEL : 2,
        defined('ONE_YEAR_PLAN_LEVEL') ? ONE_YEAR_PLAN_LEVEL : 3,
    );

    foreach ($paid_levels as $lvl) {
        if (in_array($lvl, $user_plans)) {
            return true;
        }
    }

    // Check Simple WordPress Membership level if class exists
    if (class_exists('SwpmMemberUtils')) {
        $member = SwpmMemberUtils::get_user_by_id($user_id);
        if ($member && !empty($member->membership_level)) {
            if (in_array((int)$member->membership_level, $paid_levels)) {
                return true;
            }
        }
    }

    // Check talent post paid approval
    $talent_posts = get_posts(array(
        'post_type'      => 'talent',
        'author'         => $user_id,
        'post_status'    => 'any',
        'posts_per_page' => 1,
    ));

    if (!empty($talent_posts)) {
        $has_paid = get_post_meta($talent_posts[0]->ID, '_talent_has_paid_approval', true);
        if ($has_paid) {
            return true;
        }
    }

    return false;
}

/**
 * Send New Member Welcome Email
 *
 * @param int $user_id
 * @return bool
 */
function velvet_send_welcome_email($user_id) {
    $user = get_userdata($user_id);
    if (!$user || empty($user->user_email)) {
        return false;
    }

    // Duplicate prevention check
    if (get_user_meta($user_id, '_velvet_welcome_email_sent', true)) {
        return false;
    }

    // Check unsubscribe suppression
    if (get_user_meta($user_id, '_velvet_notifications_optout', true)) {
        return false;
    }

    $member_name = !empty($user->display_name) ? $user->display_name : (!empty($user->first_name) ? $user->first_name : $user->user_login);

    $template_args = array(
        'member_name'      => $member_name,
        'member_username'  => $user->user_login,
        'member_email'     => $user->user_email,
        'profile_edit_url' => home_url('/submit-talent/'),
        'classifieds_url'  => get_post_type_archive_link('advertisement'),
        'unsubscribe_url'  => function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($user_id) : home_url('/'),
    );

    $html_content = velvet_render_email_template('email-member-welcome.php', $template_args);
    $subject      = 'Welcome to VelvetReel – Next Steps to Launch Your Profile';

    $sent = velvet_send_email($user->user_email, $subject, $html_content);

    if ($sent) {
        update_user_meta($user_id, '_velvet_welcome_email_sent', time());
        if (!get_user_meta($user_id, '_velvet_registered_time', true)) {
            update_user_meta($user_id, '_velvet_registered_time', time());
        }
    }

    return $sent;
}

/**
 * Hook into standard user registration
 */
function velvet_on_user_registered_hook($user_id) {
    if (!$user_id) {
        return;
    }

    // Set registered timestamp for onboarding sequence
    update_user_meta($user_id, '_velvet_registered_time', time());
    
    // Dispatch welcome email immediately
    velvet_send_welcome_email($user_id);
}
add_action('user_register', 'velvet_on_user_registered_hook', 10, 1);

/**
 * Main Staged Reminder Sequence Processor
 * Evaluates all registered members and sends Day 2, Day 5, Day 10, Day 14 reminders.
 *
 * @return array Summary of actions performed
 */
function velvet_process_onboarding_reminders() {
    $now = time();

    // Query users registered within the past 30 days
    $args = array(
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key'     => '_velvet_onboarding_sequence_completed',
                'compare' => 'NOT EXISTS',
            ),
        ),
        'number' => 200,
    );

    $user_query = new WP_User_Query($args);
    $users      = $user_query->get_results();

    $stats = array(
        'evaluated' => count($users),
        'day_2_sent'  => 0,
        'day_5_sent'  => 0,
        'day_10_sent' => 0,
        'day_14_sent' => 0,
        'completed'   => 0,
        'skipped'     => 0,
    );

    if (empty($users)) {
        return $stats;
    }

    foreach ($users as $user) {
        $user_id = $user->ID;

        // Check if user has opted out of emails
        if ((int) get_user_meta($user_id, '_velvet_notifications_optout', true) === 1) {
            $stats['skipped']++;
            continue;
        }

        // Get registration time
        $registered_time = (int) get_user_meta($user_id, '_velvet_registered_time', true);
        if (!$registered_time) {
            $registered_time = strtotime($user->user_registered);
            update_user_meta($user_id, '_velvet_registered_time', $registered_time);
        }

        $days_elapsed = ($now - $registered_time) / DAY_IN_SECONDS;

        // Check exit rules
        $has_profile    = velvet_is_talent_profile_complete($user_id);
        $has_subscribed = velvet_is_user_subscribed($user_id);

        // If both portfolio is complete AND subscription is active, exit sequence
        if ($has_profile && $has_subscribed) {
            update_user_meta($user_id, '_velvet_onboarding_sequence_completed', 1);
            $stats['completed']++;
            continue;
        }

        // Day 21 Exit Rule: Stop sequence after Day 21
        if ($days_elapsed >= 21) {
            update_user_meta($user_id, '_velvet_onboarding_sequence_completed', 1);
            $stats['completed']++;
            continue;
        }

        $member_name = !empty($user->display_name) ? $user->display_name : $user->first_name;
        $unsub_url   = function_exists('velvet_get_unsubscribe_url') ? velvet_get_unsubscribe_url($user_id) : home_url('/');

        // 1. Day 14 Subscription Reminder #2 (Final Invitation)
        if ($days_elapsed >= 14 && !$has_subscribed && !get_user_meta($user_id, '_velvet_reminder_sent_day_14', true)) {
            $args = array(
                'member_name'     => $member_name,
                'upgrade_url'     => home_url('/membership-login/'),
                'unsubscribe_url' => $unsub_url,
            );
            $html    = velvet_render_email_template('email-reminder-subscription-day-14.php', $args);
            $subject = 'Special Invitation: Complete your VelvetReel upgrade';
            
            if (velvet_send_email($user->user_email, $subject, $html)) {
                update_user_meta($user_id, '_velvet_reminder_sent_day_14', $now);
                $stats['day_14_sent']++;
            }
            continue;
        }

        // 2. Day 10 Subscription Reminder #1
        if ($days_elapsed >= 10 && !$has_subscribed && !get_user_meta($user_id, '_velvet_reminder_sent_day_10', true)) {
            $args = array(
                'member_name'     => $member_name,
                'upgrade_url'     => home_url('/membership-login/'),
                'unsubscribe_url' => $unsub_url,
            );
            $html    = velvet_render_email_template('email-reminder-subscription-day-10.php', $args);
            $subject = 'Unlock Full Casting Access with VelvetReel Membership';

            if (velvet_send_email($user->user_email, $subject, $html)) {
                update_user_meta($user_id, '_velvet_reminder_sent_day_10', $now);
                $stats['day_10_sent']++;
            }
            continue;
        }

        // 3. Day 5 Portfolio Reminder #2
        if ($days_elapsed >= 5 && !$has_profile && !get_user_meta($user_id, '_velvet_reminder_sent_day_5', true)) {
            $args = array(
                'member_name'      => $member_name,
                'profile_edit_url' => home_url('/submit-talent/'),
                'unsubscribe_url'  => $unsub_url,
            );
            $html    = velvet_render_email_template('email-reminder-portfolio-day-5.php', $args);
            $subject = 'Don\'t miss out on casting calls on VelvetReel';

            if (velvet_send_email($user->user_email, $subject, $html)) {
                update_user_meta($user_id, '_velvet_reminder_sent_day_5', $now);
                $stats['day_5_sent']++;
            }
            continue;
        }

        // 4. Day 2 Portfolio Reminder #1
        if ($days_elapsed >= 2 && !$has_profile && !get_user_meta($user_id, '_velvet_reminder_sent_day_2', true)) {
            $args = array(
                'member_name'      => $member_name,
                'profile_edit_url' => home_url('/submit-talent/'),
                'unsubscribe_url'  => $unsub_url,
            );
            $html    = velvet_render_email_template('email-reminder-portfolio-day-2.php', $args);
            $subject = 'Complete your portfolio on VelvetReel – Opportunities are waiting';

            if (velvet_send_email($user->user_email, $subject, $html)) {
                update_user_meta($user_id, '_velvet_reminder_sent_day_2', $now);
                $stats['day_2_sent']++;
            }
            continue;
        }
    }

    // Save execution log
    update_option('velvet_onboarding_last_run_stats', array(
        'time'  => current_time('mysql'),
        'stats' => $stats,
    ));

    return $stats;
}

/**
 * Schedule WP-Cron recurring task for onboarding sequences
 */
function velvet_schedule_onboarding_cron() {
    if (!wp_next_scheduled('velvet_onboarding_sequence_cron_hook')) {
        wp_schedule_event(time(), 'twicedaily', 'velvet_onboarding_sequence_cron_hook');
    }
}
add_action('wp', 'velvet_schedule_onboarding_cron');
add_action('velvet_onboarding_sequence_cron_hook', 'velvet_process_onboarding_reminders');
