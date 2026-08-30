<?php
/**
 * Advertisement Meta Boxes
 * 
 * Adds custom meta boxes to the advertisement post type with tabbed interface
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Interests submenu page to Advertisements
 */
function add_advertisement_interests_menu() {
    // Get count of advertisements with interests
    $interests_count = get_advertisements_with_interests_count();
    
    $menu_label = __('Interests', 'hello-elementor-child');
    if ($interests_count > 0) {
        $menu_label .= ' <span class="awaiting-mod count-' . $interests_count . '"><span class="pending-count" aria-hidden="true">' . $interests_count . '</span></span>';
    }
    
    add_submenu_page(
        'edit.php?post_type=advertisement',
        __('Expressed Interests', 'hello-elementor-child'),
        $menu_label,
        'manage_options',
        'advertisement-interests',
        'display_advertisement_interests_page'
    );
}
add_action('admin_menu', 'add_advertisement_interests_menu');

/**
 * Get count of advertisements that have interests expressed
 */
function get_advertisements_with_interests_count() {
    $advertisements = get_posts(array(
        'post_type' => 'advertisement',
        'post_status' => 'any',
        'numberposts' => -1,
        'meta_key' => '_advertisement_interests'
    ));
    
    $count = 0;
    foreach ($advertisements as $ad) {
        $interests = get_post_meta($ad->ID, '_advertisement_interests', true);
        if (is_array($interests) && count($interests) > 0) {
            $count++;
        }
    }
    return $count;
}

/**
 * Display the Interests admin page
 */
function display_advertisement_interests_page() {
    // Get all advertisements
    $advertisements = get_posts(array(
        'post_type' => 'advertisement',
        'post_status' => 'any',
        'numberposts' => -1
    ));
    
    ?>
    <div class="wrap">
        <h1><?php _e('Expressed Interests', 'hello-elementor-child'); ?></h1>
        <p><?php _e('View all users who have expressed interest in advertisements.', 'hello-elementor-child'); ?></p>
        
        <?php if (empty($advertisements)) : ?>
            <p><?php _e('No advertisements found.', 'hello-elementor-child'); ?></p>
        <?php else : ?>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Advertisement', 'hello-elementor-child'); ?></th>
                        <th><?php _e('Status', 'hello-elementor-child'); ?></th>
                        <th><?php _e('Interested Users', 'hello-elementor-child'); ?></th>
                        <th><?php _e('Actions', 'hello-elementor-child'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($advertisements as $ad) : 
                        $interests = get_post_meta($ad->ID, '_advertisement_interests', true);
                        $interest_dates = get_post_meta($ad->ID, '_advertisement_interest_dates', true);
                        $status = get_post_meta($ad->ID, '_advertisement_status', true);
                        
                        if (!is_array($interests)) {
                            $interests = array();
                        }
                        
                        $count = count($interests);
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($ad->post_title); ?></strong>
                            <br><small><?php echo get_the_date('M j, Y', $ad); ?></small>
                        </td>
                        <td>
                            <?php if ($status) : ?>
                                <span class="advt-status advt-status-<?php echo esc_attr($status); ?>">
                                    <?php echo ucfirst(esc_html($status)); ?>
                                </span>
                            <?php else : ?>
                                <span class="advt-status advt-status-open">Open</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($count > 0) : ?>
                                <span class="advt-interest-count"><?php echo $count; ?></span>
                                <?php echo $count == 1 ? __('person', 'hello-elementor-child') : __('people', 'hello-elementor-child'); ?>
                            <?php else : ?>
                                <span class="advt-no-interest"><?php _e('No interest yet', 'hello-elementor-child'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo get_edit_post_link($ad->ID); ?>" class="button button-small">
                                <?php _e('View Details', 'hello-elementor-child'); ?>
                            </a>
                            <?php if ($count > 0) : ?>
                                <button type="button" class="button button-small" onclick="jQuery('#advt-interests-<?php echo $ad->ID; ?>').toggle();">
                                    <?php _e('Show Users', 'hello-elementor-child'); ?>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if ($count > 0) : ?>
                    <tr id="advt-interests-<?php echo $ad->ID; ?>" style="display: none;">
                        <td colspan="4">
                            <table class="widefat fixed">
                                <thead>
                                    <tr>
                                        <th><?php _e('User', 'hello-elementor-child'); ?></th>
                                        <th><?php _e('Email', 'hello-elementor-child'); ?></th>
                                        <th><?php _e('Date Expressed', 'hello-elementor-child'); ?></th>
                                        <th><?php _e('Actions', 'hello-elementor-child'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($interests as $user_id) : 
                                        $user = get_userdata($user_id);
                                        if ($user) {
                                            // Ensure interest_dates is properly handled as array
                                    $interest_dates_array = is_array($interest_dates) ? $interest_dates : array();
                                    $date_expressed = isset($interest_dates_array[$user_id]) ? $interest_dates_array[$user_id] : '';
                                            $formatted_date = $date_expressed ? date('M j, Y g:i A', strtotime($date_expressed)) : 'Unknown';
                                            $profile_url = home_url('/talent-profile/?user_id=' . $user_id);
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo esc_html($user->display_name); ?></strong>
                                            <br><small><?php echo esc_html($user->user_login); ?></small>
                                        </td>
                                        <td><?php echo esc_html($user->user_email); ?></td>
                                        <td><?php echo esc_html($formatted_date); ?></td>
                                        <td>
                                            <a href="<?php echo esc_url($profile_url); ?>" target="_blank" class="button button-small">
                                                <?php _e('View Profile', 'hello-elementor-child'); ?>
                                            </a>
                                            <a href="mailto:<?php echo esc_attr($user->user_email); ?>" class="button button-small">
                                                <?php _e('Email', 'hello-elementor-child'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <style>
            .advt-status {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 3px;
                font-size: 12px;
                font-weight: 500;
            }
            .advt-status-open { background: #d4edda; color: #155724; }
            .advt-status-pending { background: #fff3cd; color: #856404; }
            .advt-status-closed { background: #f8d7da; color: #721c24; }
            .advt-status-filled { background: #cce5ff; color: #004085; }
            .advt-interest-count {
                font-weight: bold;
                color: #2199e8;
                font-size: 16px;
            }
            .advt-no-interest {
                color: #666;
                font-style: italic;
            }
        </style>
    </div>
    <?php
}

/**
 * Register the main Advertisement Details meta box with tabs
 */
function register_advertisement_meta_box() {
    add_meta_box(
        'advertisement_details',
        __('Advertisement Details', 'hello-elementor-child'),
        'display_advertisement_meta_box',
        'advertisement',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'register_advertisement_meta_box');

/**
 * Also register interests meta box separately for compatibility
 */
function register_advertisement_interests_compat_meta_box() {
    add_meta_box(
        'advertisement_interests_compat',
        __('Expressed Interests', 'hello-elementor-child'),
        'display_advertisement_interests_compat_meta_box',
        'advertisement',
        'side',
        'low'
    );
}
add_action('add_meta_boxes', 'register_advertisement_interests_compat_meta_box');

/**
 * Display the interests meta box (compat/standalone version)
 */
function display_advertisement_interests_compat_meta_box($post) {
    $interests = get_post_meta($post->ID, '_advertisement_interests', true);
    $interest_dates = get_post_meta($post->ID, '_advertisement_interest_dates', true);
    
    if (!is_array($interests)) {
        $interests = array();
    }
    if (!is_array($interest_dates)) {
        $interest_dates = array();
    }
    
    echo '<div class="advt-interests-compat">';
    
    if (empty($interests)) {
        echo '<p class="no-interests">' . __('No interest expressed yet.', 'hello-elementor-child') . '</p>';
    } else {
        echo '<p><strong>' . count($interests) . '</strong> ' . __('interested', 'hello-elementor-child') . '</p>';
        echo '<ul style="margin: 0; padding-left: 20px;">';
        foreach ($interests as $user_id) {
            $user = get_userdata($user_id);
            if ($user) {
                echo '<li>' . esc_html($user->display_name) . ' (' . esc_html($user->user_email) . ')</li>';
            }
        }
        echo '</ul>';
    }
    
    echo '</div>';
}

/**
 * Display the main meta box with tabs
 */
function display_advertisement_meta_box($post) {
    // Get current tab
    $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'details';
    
    // Get all meta values
    $advertisement_type = get_post_meta($post->ID, '_advertisement_type', true);
    $contact_email = get_post_meta($post->ID, '_advertisement_contact_email', true);
    $contact_phone = get_post_meta($post->ID, '_advertisement_contact_phone', true);
    $location = get_post_meta($post->ID, '_advertisement_location', true);
    $budget = get_post_meta($post->ID, '_advertisement_budget', true);
    $deadline = get_post_meta($post->ID, '_advertisement_deadline', true);
    $status = get_post_meta($post->ID, '_advertisement_status', true);
    $interests = get_post_meta($post->ID, '_advertisement_interests', true);
    $interest_dates = get_post_meta($post->ID, '_advertisement_interest_dates', true);

    // Audience targeting values
    $target_domains = get_post_meta($post->ID, '_target_domains', true);
    $target_domain = is_array($target_domains) ? reset($target_domains) : $target_domains;
    $target_roles = get_post_meta($post->ID, '_target_roles', true);
    $target_role = is_array($target_roles) ? reset($target_roles) : $target_roles;
    $target_gender = get_post_meta($post->ID, '_target_gender', true);
    $target_age_group = get_post_meta($post->ID, '_target_age_group', true);
    $target_location = get_post_meta($post->ID, '_target_location', true);
    if (empty($target_location)) {
        $target_location = $location;
    }

    $notifications_sent = get_post_meta($post->ID, '_advertisement_notifications_sent', true);
    $notifications_count = get_post_meta($post->ID, '_advertisement_notifications_count', true);

    // Calculate matching talents count if function exists
    $matched_talents_count = 0;
    if (function_exists('velvet_get_matching_talents_for_ad')) {
        $matches = velvet_get_matching_talents_for_ad($post->ID);
        $matched_talents_count = count($matches);
    }
    
    // Ensure they're arrays
    if (!is_array($interests)) {
        $interests = array();
    }
    if (!is_array($interest_dates)) {
        $interest_dates = array();
    }
    
    // Nonce for security
    wp_nonce_field('advertisement_details_nonce', 'advertisement_details_nonce');
    
    ?>
    <div class="advt-metabox-tabs">
        <h2 class="advt-nav-tab-wrapper">
            <a href="#advt-tab-details" class="advt-nav-tab <?php echo $active_tab === 'details' ? 'advt-nav-tab-active' : ''; ?>">
                <?php _e('Details', 'hello-elementor-child'); ?>
            </a>
            <a href="#advt-tab-targeting" class="advt-nav-tab <?php echo $active_tab === 'targeting' ? 'advt-nav-tab-active' : ''; ?>">
                <?php _e('Audience Targeting & Alerts', 'hello-elementor-child'); ?>
                <span class="advt-badge" style="background:#22c55e; color:#fff;"><?php echo $matched_talents_count; ?> matches</span>
            </a>
            <a href="#advt-tab-interests" class="advt-nav-tab <?php echo $active_tab === 'interests' ? 'advt-nav-tab-active' : ''; ?>">
                <?php _e('Interests', 'hello-elementor-child'); 
                if (!empty($interests)) {
                    echo ' <span class="advt-badge">' . count($interests) . '</span>';
                }
                ?>
            </a>
        </h2>
        
        <!-- Tab 1: Details -->
        <div id="advt-tab-details" class="advt-tab-content <?php echo $active_tab === 'details' ? 'advt-active' : ''; ?>">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="advertisement_type"><?php _e('Advertisement Type', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <select name="advertisement_type" id="advertisement_type">
                            <option value="casting" <?php selected($advertisement_type, 'casting'); ?>><?php _e('Casting Call', 'hello-elementor-child'); ?></option>
                            <option value="job" <?php selected($advertisement_type, 'job'); ?>><?php _e('Job Opening', 'hello-elementor-child'); ?></option>
                            <option value="collaboration" <?php selected($advertisement_type, 'collaboration'); ?>><?php _e('Collaboration', 'hello-elementor-child'); ?></option>
                            <option value="event" <?php selected($advertisement_type, 'event'); ?>><?php _e('Event', 'hello-elementor-child'); ?></option>
                            <option value="other" <?php selected($advertisement_type, 'other'); ?>><?php _e('Other', 'hello-elementor-child'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="advertisement_status"><?php _e('Status', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <select name="advertisement_status" id="advertisement_status">
                            <option value="open" <?php selected($status, 'open'); ?>><?php _e('Open', 'hello-elementor-child'); ?></option>
                            <option value="pending" <?php selected($status, 'pending'); ?>><?php _e('Pending Review', 'hello-elementor-child'); ?></option>
                            <option value="closed" <?php selected($status, 'closed'); ?>><?php _e('Closed', 'hello-elementor-child'); ?></option>
                            <option value="filled" <?php selected($status, 'filled'); ?>><?php _e('Position Filled', 'hello-elementor-child'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="advertisement_contact_email"><?php _e('Contact Email', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <input type="email" name="advertisement_contact_email" id="advertisement_contact_email" value="<?php echo esc_attr($contact_email); ?>" class="regular-text" />
                        <p class="description"><?php _e('Advertiser contact email where member interest notifications are delivered.', 'hello-elementor-child'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="advertisement_contact_phone"><?php _e('Contact Phone', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="advertisement_contact_phone" id="advertisement_contact_phone" value="<?php echo esc_attr($contact_phone); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="advertisement_location"><?php _e('Location', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="advertisement_location" id="advertisement_location" value="<?php echo esc_attr($location); ?>" class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="advertisement_budget"><?php _e('Budget/Compensation', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="advertisement_budget" id="advertisement_budget" value="<?php echo esc_attr($budget); ?>" class="regular-text" placeholder="e.g., $500/day or Negotiable" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="advertisement_deadline"><?php _e('Application Deadline', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <input type="date" name="advertisement_deadline" id="advertisement_deadline" value="<?php echo esc_attr($deadline); ?>" class="regular-text" />
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tab 2: Audience Targeting & Alerts -->
        <div id="advt-tab-targeting" class="advt-tab-content <?php echo $active_tab === 'targeting' ? 'advt-active' : ''; ?>">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <h3 style="margin-top: 0; color: #b2122d;">🎯 <?php _e('Automated Notification Engine', 'hello-elementor-child'); ?></h3>
                <p><?php _e('When this advertisement is published, VelvetReel automatically sends targeted HTML email notifications to all active, opted-in talent profiles matching the criteria below.', 'hello-elementor-child'); ?></p>
                
                <p>
                    <strong><?php _e('Audience Match Status:', 'hello-elementor-child'); ?></strong> 
                    <span style="display: inline-block; padding: 4px 10px; background: #e0f2fe; color: #0369a1; border-radius: 4px; font-weight: bold;">
                        <?php echo $matched_talents_count; ?> <?php _e('Eligible Talent Profiles currently match', 'hello-elementor-child'); ?>
                    </span>
                </p>

                <?php if ($notifications_sent) : ?>
                    <p style="color: #15803d;">
                        ✓ <?php printf(__('Last notifications sent on <strong>%s</strong> to <strong>%d</strong> talents.', 'hello-elementor-child'), date('M j, Y g:i A', $notifications_sent), intval($notifications_count)); ?>
                    </p>
                <?php else : ?>
                    <p style="color: #64748b;">
                        ⏳ <?php _e('Notifications have not been sent yet. They will automatically trigger when post status becomes Published.', 'hello-elementor-child'); ?>
                    </p>
                <?php endif; ?>
            </div>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="target_domain"><?php _e('Target Discipline / Domain', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <select name="target_domain" id="target_domain" class="regular-text">
                            <option value=""><?php _e('All Disciplines / Open', 'hello-elementor-child'); ?></option>
                            <option value="Fashion & Design" <?php selected($target_domain, 'Fashion & Design'); ?>>Fashion & Design</option>
                            <option value="Film & Creative Arts" <?php selected($target_domain, 'Film & Creative Arts'); ?>>Film & Creative Arts</option>
                            <option value="Talent Coach" <?php selected($target_domain, 'Talent Coach'); ?>>Talent Coach / Trainer</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="target_role"><?php _e('Target Specific Role', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="target_role" id="target_role" value="<?php echo esc_attr($target_role); ?>" class="regular-text" placeholder="e.g. Actor/Actress, Fashion Model, Director, Stylist, etc." />
                        <p class="description"><?php _e('Leave blank to match all talents in the discipline, or specify a role like Fashion Model or Actor.', 'hello-elementor-child'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="target_gender"><?php _e('Target Gender', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <select name="target_gender" id="target_gender">
                            <option value="all" <?php selected($target_gender, 'all'); ?>><?php _e('All / Any Gender', 'hello-elementor-child'); ?></option>
                            <option value="female" <?php selected($target_gender, 'female'); ?>><?php _e('Female', 'hello-elementor-child'); ?></option>
                            <option value="male" <?php selected($target_gender, 'male'); ?>><?php _e('Male', 'hello-elementor-child'); ?></option>
                            <option value="non-binary" <?php selected($target_gender, 'non-binary'); ?>><?php _e('Non-Binary / Other', 'hello-elementor-child'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="target_age_group"><?php _e('Target Age Group', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <select name="target_age_group" id="target_age_group">
                            <option value="all" <?php selected($target_age_group, 'all'); ?>><?php _e('All Age Groups', 'hello-elementor-child'); ?></option>
                            <option value="Under 18" <?php selected($target_age_group, 'Under 18'); ?>>Under 18 / Teens</option>
                            <option value="18-25" <?php selected($target_age_group, '18-25'); ?>>18 - 25 Years</option>
                            <option value="26-35" <?php selected($target_age_group, '26-35'); ?>>26 - 35 Years</option>
                            <option value="36-50" <?php selected($target_age_group, '36-50'); ?>>36 - 50 Years</option>
                            <option value="50+" <?php selected($target_age_group, '50+'); ?>>50+ Years</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="target_location"><?php _e('Target Location Match', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="target_location" id="target_location" value="<?php echo esc_attr($target_location); ?>" class="regular-text" placeholder="e.g. Mumbai or Maharashtra or Remote" />
                        <p class="description"><?php _e('Leave blank for all locations.', 'hello-elementor-child'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="force_resend_notifications"><?php _e('Resend Notifications', 'hello-elementor-child'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="force_resend_notifications" id="force_resend_notifications" value="1" />
                            <strong><?php _e('Resend email notifications to matching audience upon saving', 'hello-elementor-child'); ?></strong>
                        </label>
                        <p class="description"><?php _e('By default, duplicate notifications are prevented when saving or updating. Check this box only if you want to broadcast this classified again.', 'hello-elementor-child'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Tab 3: Interests -->
        <div id="advt-tab-interests" class="advt-tab-content <?php echo $active_tab === 'interests' ? 'advt-active' : ''; ?>">
            <?php
            echo '<div class="advertisement-interests-metabox">';
            
            if (empty($interests)) {
                echo '<p class="no-interests">' . __('No one has expressed interest in this advertisement yet.', 'hello-elementor-child') . '</p>';
            } else {
                echo '<p class="interests-count"><strong>' . count($interests) . '</strong> ' . (count($interests) == 1 ? __('person has', 'hello-elementor-child') : __('people have', 'hello-elementor-child')) . __(' expressed interest', 'hello-elementor-child') . '</p>';
                
                echo '<table class="widefat fixed striped interests-table">';
                echo '<thead><tr>';
                echo '<th>' . __('User', 'hello-elementor-child') . '</th>';
                echo '<th>' . __('Email', 'hello-elementor-child') . '</th>';
                echo '<th>' . __('Date Expressed', 'hello-elementor-child') . '</th>';
                echo '<th>' . __('Actions', 'hello-elementor-child') . '</th>';
                echo '</tr></thead>';
                echo '<tbody>';
                
                foreach ($interests as $user_id) {
                    $user = get_userdata($user_id);
                    
                    if ($user) {
                        // Ensure interest_dates is properly handled as array
                        $interest_dates_array = is_array($interest_dates) ? $interest_dates : array();
                        $date_expressed = isset($interest_dates_array[$user_id]) ? $interest_dates_array[$user_id] : '';
                        $formatted_date = $date_expressed ? date('M j, Y g:i A', strtotime($date_expressed)) : __('Unknown', 'hello-elementor-child');
                        
                        // Get user profile URL
                        $profile_url = home_url('/talent-profile/?user_id=' . $user_id);
                        
                        echo '<tr>';
                        echo '<td>';
                        echo '<strong>' . esc_html($user->display_name) . '</strong>';
                        echo '<br><small>' . __('Username: ', 'hello-elementor-child') . esc_html($user->user_login) . '</small>';
                        echo '</td>';
                        echo '<td>' . esc_html($user->user_email) . '</td>';
                        echo '<td>' . esc_html($formatted_date) . '</td>';
                        echo '<td>';
                        echo '<a href="' . esc_url($profile_url) . '" target="_blank" class="button button-small">' . __('View Profile', 'hello-elementor-child') . '</a>';
                        echo ' <a href="mailto:' . esc_attr($user->user_email) . '" class="button button-small">' . __('Email', 'hello-elementor-child') . '</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                }
                
                echo '</tbody>';
                echo '</table>';
            }
            
            echo '</div>';
            ?>
        </div>
    </div>
    
    <style>
        .advt-metabox-tabs {
            margin-top: 10px;
        }
        .advt-metabox-tabs .advt-nav-tab-wrapper {
            border-bottom: 1px solid #ccc;
            margin-bottom: 15px;
        }
        .advt-metabox-tabs .advt-nav-tab {
            background: #f0f0f0;
            border: 1px solid #ccc;
            border-bottom: none;
            padding: 8px 15px;
            text-decoration: none;
            color: #555;
            display: inline-block;
            margin-right: 5px;
            border-radius: 3px 3px 0 0;
        }
        .advt-metabox-tabs .advt-nav-tab:hover {
            background: #e0e0e0;
        }
        .advt-metabox-tabs .advt-nav-tab-active {
            background: #fff;
            border-bottom: 1px solid #fff;
            color: #000;
            font-weight: bold;
            margin-bottom: -1px;
        }
        .advt-metabox-tabs .advt-tab-content {
            display: none;
            padding: 10px 0;
        }
        .advt-metabox-tabs .advt-tab-content.advt-active {
            display: block;
        }
        .advt-metabox-tabs .advt-badge {
            background: #b2122d;
            color: #fff;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: normal;
        }
        .advt-metabox-tabs .no-interests {
            padding: 20px;
            text-align: center;
            color: #666;
            font-style: italic;
            background: #f9f9f9;
            border-radius: 4px;
        }
        .advt-metabox-tabs .interests-count {
            margin-bottom: 15px;
            padding: 10px;
            background: #eef7ff;
            border-radius: 4px;
            border-left: 4px solid #2199e8;
        }
        .advt-metabox-tabs .interests-table {
            margin-top: 15px;
        }
        .advt-metabox-tabs .interests-table th {
            background: #f1f1f1;
        }
        .advt-metabox-tabs .interests-table td {
            vertical-align: middle;
        }
    </style>
    
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var tabs = document.querySelectorAll('.advt-metabox-tabs .advt-nav-tab');
            tabs.forEach(function(tab) {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    var target = this.getAttribute('href');
                    
                    // Update active tab
                    document.querySelectorAll('.advt-metabox-tabs .advt-nav-tab').forEach(function(t) {
                        t.classList.remove('advt-nav-tab-active');
                    });
                    this.classList.add('advt-nav-tab-active');
                    
                    // Show/hide content
                    document.querySelectorAll('.advt-metabox-tabs .advt-tab-content').forEach(function(content) {
                        content.classList.remove('advt-active');
                    });
                    document.querySelector(target).classList.add('advt-active');
                });
            });
        });
    </script>
    <?php
}

/**
 * Save advertisement meta box data
 */
function save_advertisement_meta_box($post_id) {
    // Check if nonce is set
    if (!isset($_POST['advertisement_details_nonce'])) {
        return $post_id;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['advertisement_details_nonce'], 'advertisement_details_nonce')) {
        return $post_id;
    }
    
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    
    // Check user permission
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    
    // Mark as new when published
    $post = get_post($post_id);
    if ($post && $post->post_status === 'publish') {
        update_post_meta($post_id, '_advertisement_is_new', '1');
        if (!get_post_meta($post_id, '_advertisement_published_date', true)) {
            update_post_meta($post_id, '_advertisement_published_date', current_time('mysql'));
        }
    }
    
    // Save fields
    if (isset($_POST['advertisement_type'])) {
        update_post_meta($post_id, '_advertisement_type', sanitize_text_field($_POST['advertisement_type']));
    }
    
    if (isset($_POST['advertisement_status'])) {
        update_post_meta($post_id, '_advertisement_status', sanitize_text_field($_POST['advertisement_status']));
    }
    
    if (isset($_POST['advertisement_contact_email'])) {
        update_post_meta($post_id, '_advertisement_contact_email', sanitize_email($_POST['advertisement_contact_email']));
    }
    
    if (isset($_POST['advertisement_contact_phone'])) {
        update_post_meta($post_id, '_advertisement_contact_phone', sanitize_text_field($_POST['advertisement_contact_phone']));
    }
    
    if (isset($_POST['advertisement_location'])) {
        update_post_meta($post_id, '_advertisement_location', sanitize_text_field($_POST['advertisement_location']));
    }
    
    if (isset($_POST['advertisement_budget'])) {
        update_post_meta($post_id, '_advertisement_budget', sanitize_text_field($_POST['advertisement_budget']));
    }
    
    if (isset($_POST['advertisement_deadline'])) {
        update_post_meta($post_id, '_advertisement_deadline', sanitize_text_field($_POST['advertisement_deadline']));
    }

    // Save Audience Targeting Fields
    if (isset($_POST['target_domain'])) {
        $dom = sanitize_text_field($_POST['target_domain']);
        update_post_meta($post_id, '_target_domains', !empty($dom) ? array($dom) : array());
    }
    if (isset($_POST['target_role'])) {
        $role = sanitize_text_field($_POST['target_role']);
        update_post_meta($post_id, '_target_roles', !empty($role) ? array($role) : array());
    }
    if (isset($_POST['target_gender'])) {
        update_post_meta($post_id, '_target_gender', sanitize_text_field($_POST['target_gender']));
    }
    if (isset($_POST['target_age_group'])) {
        update_post_meta($post_id, '_target_age_group', sanitize_text_field($_POST['target_age_group']));
    }
    if (isset($_POST['target_location'])) {
        update_post_meta($post_id, '_target_location', sanitize_text_field($_POST['target_location']));
    }

    // Check if explicit resend was requested
    if (!empty($_POST['force_resend_notifications']) && function_exists('velvet_send_targeted_classified_notifications')) {
        velvet_send_targeted_classified_notifications($post_id, true);
    }
}
add_action('save_post', 'save_advertisement_meta_box');

/**
 * Save interest dates when interest is expressed
 */
function save_advertisement_interest_date($post_id, $user_id) {
    $interest_dates = get_post_meta($post_id, '_advertisement_interest_dates', true);
    
    if (!is_array($interest_dates)) {
        $interest_dates = array();
    }
    
    // Add current timestamp for this user if not already set
    if (!isset($interest_dates[$user_id])) {
        $interest_dates[$user_id] = current_time('mysql');
        update_post_meta($post_id, '_advertisement_interest_dates', $interest_dates);
    }
}
