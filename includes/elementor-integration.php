<?php
/**
 * Elementor integration functionality
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom talent fields to Elementor's dynamic tags dropdown.
 * This makes it easier to select custom fields without typing the key manually.
 */
function add_talent_custom_fields_to_elementor($controls_stack) {
    // Only add these to the 'talent' post type archives or single pages in the editor
    if (('talent' !== get_post_type() && 'talent' !== $controls_stack->get_name())) {
        return;
    }

    $talent_fields = [
        '_talent_state' => 'State',
        '_talent_country' => 'Country',
        '_talent_email' => 'Email',
        '_talent_phone' => 'Phone',
        '_talent_age_group' => 'Age Group',
        '_talent_gender' => 'Gender',
        '_talent_height' => 'Height',
        '_talent_height_unit' => 'Height Unit',
        '_talent_measurements' => 'Measurements',
        '_talent_years_active' => 'Years Active',
        '_talent_affiliation' => 'Affiliation',
        '_talent_education' => 'Education',
        '_talent_interested_projects' => 'Interested Projects',
        '_talent_willing_to_travel' => 'Willing to Travel',
        '_talent_preferred_locations' => 'Preferred Locations',
        '_talent_brand_collabs' => 'Brand Collabs',
        '_talent_domain' => 'Domain',
        '_talent_role' => 'Role',
        'Social Links' => [
            '_talent_instagram' => 'Instagram',
            '_talent_linkedin' => 'LinkedIn',
            '_talent_tiktok' => 'TikTok',
            '_talent_website' => 'Website',
        ],
        '_talent_profile_clicks' => 'Profile Clicks',
    ];

    $control = $controls_stack->get_controls('key');

    if (!empty($control['options'])) {
        $control['options'] = array_merge($control['options'], $talent_fields);
        $controls_stack->update_control('key', $control);
    }
}
add_action('elementor/dynamic_tags/post_custom_field/before_render', 'add_talent_custom_fields_to_elementor');

/**
 * Shortcode to display subscription status indicator for Elementor
 */
function elementor_subscription_status_shortcode() {
    // Only show for logged in users
    if (!is_user_logged_in()) {
        return '';
    }

    // Display subscription indicator for all logged-in users
    if (true) {
        ob_start();
        ?>
        <div class="subscription-status-indicator">
            <div class="subscription-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
                <span>You are a subscribed Member</span>
            </div>
        </div>
        <style>
            .subscription-status-indicator {
                margin: 15px 0;
                display: flex;
                justify-content: center;
            }

            .subscription-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                background-color: green;
                color: white;
                border-radius: 20px;
                font-weight: 600;
                font-size: 14px;
                border: 1px solid rgba(255, 255, 255, 0.4);
            }

            .subscription-badge svg {
                fill: white;
            }
        </style>
        <?php
        return ob_get_clean();
    }

    return ''; // No subscription badge for users without plans
}
add_shortcode('elementor_subscription_status', 'elementor_subscription_status_shortcode');