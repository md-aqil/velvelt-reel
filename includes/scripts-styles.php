<?php
/**
 * Script and style enqueuing
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue custom scripts
 */
function custom_enqueue_scripts() {
    // Enqueue the global JS file
    wp_enqueue_script(
        'theme-global-js', // Handle name
        get_stylesheet_directory_uri() . '/global.js', // File path
        array(), // Dependencies (optional)
        HELLO_ELEMENTOR_CHILD_VERSION, // Version number
        true // Load in footer (true = before </body>)
    );

    // Localize the global JS with AJAX URL and nonces
    wp_localize_script('theme-global-js', 'wpApiSettings', [
        'root' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('express_interest_nonce'),
        'isUserLoggedIn' => is_user_logged_in() ? 1 : 0
    ]);

    // Debug: Log enqueued scripts
    if (defined('WP_DEBUG') && WP_DEBUG) {
        global $wp_scripts;
        error_log('Enqueued scripts: ' . print_r($wp_scripts->queue, true));
    }
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');

/**
 * Enqueue image protection CSS
 */
function enqueue_image_protection_styles() {
    // Enqueue image protection CSS
    wp_enqueue_style(
        'image-protection-css',
        get_stylesheet_directory_uri() . '/image-protection.css',
        array(),
        HELLO_ELEMENTOR_CHILD_VERSION
    );
}
add_action('wp_enqueue_scripts', 'enqueue_image_protection_styles', 20); // Run after main styles to ensure specificity

/**
 * Enqueue styles and scripts for talent submission and edit pages
 */
function enqueue_talent_form_assets() {
    $is_submission_page = is_page_template('template-talent-submission.php');
    $is_edit_page = is_page_template('template-talent-edit.php');
    $should_enqueue = $is_submission_page || $is_edit_page;

    if ($should_enqueue) {
        // Enqueue the form CSS
        wp_enqueue_style(
            'talent-form-style',
            get_stylesheet_directory_uri() . '/form-style.css',
            array(),
            HELLO_ELEMENTOR_CHILD_VERSION
        );

        wp_enqueue_style(
            'theme-global-css',
            get_stylesheet_directory_uri() . '/global.css',
            array(),
            HELLO_ELEMENTOR_CHILD_VERSION
        );

        // Enqueue the global JS file
        wp_enqueue_script(
            'theme-global-js',
            get_stylesheet_directory_uri() . '/global.js',
            array(),
            HELLO_ELEMENTOR_CHILD_VERSION,
            true
        );

        // Enqueue the talent submission JS file
        wp_enqueue_script(
            'talent-submission-js',
            get_stylesheet_directory_uri() . '/talent--submission.js',
            array('jquery', 'theme-global-js'),
            HELLO_ELEMENTOR_CHILD_VERSION,
            true
        );

        // Localize script with draft data and AJAX info
        $draft_data = [];
        if (!$is_edit_page) {
            $draft_post = get_user_draft_profile();

            if ($draft_post) {
                $state = get_post_meta($draft_post->ID, '_talent_state', true);
                $draft_data = [
                    'post_id' => $draft_post->ID,
                    'fullName' => $draft_post->post_title,
                    'styleDescription' => $draft_post->post_content,
                    'currentStep' => get_post_meta($draft_post->ID, '_talent_current_step', true),
                    // Meta fields
                    'city' => $state,
                    'state' => $state,
                    'country' => get_post_meta($draft_post->ID, '_talent_country', true),
                    'email' => get_post_meta($draft_post->ID, '_talent_email', true),
                    'phone' => get_post_meta($draft_post->ID, '_talent_phone', true),
                    'ageGroup' => get_post_meta($draft_post->ID, '_talent_age_group', true),
                    'gender' => get_post_meta($draft_post->ID, '_talent_gender', true),
                    'height' => get_post_meta($draft_post->ID, '_talent_height', true),
                    'heightUnit' => get_post_meta($draft_post->ID, '_talent_height_unit', true),
                    'weight' => get_post_meta($draft_post->ID, '_talent_weight', true),
                    'weightUnit' => get_post_meta($draft_post->ID, '_talent_weight_unit', true),
                    'complexion' => get_post_meta($draft_post->ID, '_talent_complexion', true),
                    'bustSize' => get_post_meta($draft_post->ID, '_talent_bust_size', true),
                    'hairColor' => get_post_meta($draft_post->ID, '_talent_hair_color', true),
                    'dressSize' => get_post_meta($draft_post->ID, '_talent_dress_size', true),
                    'shirtSize' => get_post_meta($draft_post->ID, '_talent_shirt_size', true),
                    'measurements' => get_post_meta($draft_post->ID, '_talent_measurements', true),
                    'yearsActive' => get_post_meta($draft_post->ID, '_talent_years_active', true),
                    'affiliation' => get_post_meta($draft_post->ID, '_talent_affiliation', true),
                    'education' => get_post_meta($draft_post->ID, '_talent_education', true),
                    'interestedProjects' => get_post_meta($draft_post->ID, '_talent_interested_projects', true),
                    'willingToTravel' => get_post_meta($draft_post->ID, '_talent_willing_to_travel', true),
                    'preferredLocations' => get_post_meta($draft_post->ID, '_talent_preferred_locations', true),
                    'brandCollabs' => get_post_meta($draft_post->ID, '_talent_brand_collabs', true),
                    'domain' => get_post_meta($draft_post->ID, '_talent_domain', true),
                    'role' => get_post_meta($draft_post->ID, '_talent_role', true),
                    'instagram' => get_post_meta($draft_post->ID, '_talent_instagram', true),
                    'linkedin' => get_post_meta($draft_post->ID, '_talent_linkedin', true),
                    'tiktok' => get_post_meta($draft_post->ID, '_talent_tiktok', true),
                    'website' => get_post_meta($draft_post->ID, '_talent_website', true),
                    // Arrays
                    'languages' => get_post_meta($draft_post->ID, '_talent_languages', true),
                    'availableFor' => get_post_meta($draft_post->ID, '_talent_available_for', true),
                    'designCategories' => get_post_meta($draft_post->ID, '_talent_designCategories', true),
                    'notableWorks' => get_post_meta($draft_post->ID, '_talent_notable_works', true),
                ];
            }
        }

        $nonce_action = $is_edit_page ? 'talent_update' : 'talent_submission';
        
        // Always create the talentData object, even if there's no draft
        wp_localize_script('talent-submission-js', 'talentData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce($nonce_action),
            'draft' => empty($draft_data) ? null : $draft_data,
            'isEditPage' => $is_edit_page ? 1 : 0
        ]);
    }

    // Check if we're on a single talent page
    if (is_singular('talent')) {
        // Enqueue the global JS file for portfolio gallery functionality
        wp_enqueue_script(
            'theme-global-js',
            get_stylesheet_directory_uri() . '/global.js',
            array(),
            HELLO_ELEMENTOR_CHILD_VERSION,
            true
        );

        // Enqueue the global CSS file for styling
        wp_enqueue_style(
            'theme-global-css',
            get_stylesheet_directory_uri() . '/global.css',
            array(),
            HELLO_ELEMENTOR_CHILD_VERSION
        );
        
        // Enqueue share profile functionality for single talent pages
        wp_enqueue_script(
            'share-profile-js',
            get_stylesheet_directory_uri() . '/js/share-profile.js',
            array('jquery'),
            HELLO_ELEMENTOR_CHILD_VERSION,
            true
        );
        
        // Localize with AJAX URL
        wp_localize_script('share-profile-js', 'shareProfileData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('shareable_token_nonce')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_talent_form_assets', 20);

/**
 * Enqueue media scripts for portfolio gallery
 */
function talent_admin_scripts($hook) {
    global $post;

    if ($hook == 'post-new.php' || $hook == 'post.php') {
        if ('talent' === $post->post_type) {
            wp_enqueue_media();
        }
    }
}
add_action('admin_enqueue_scripts', 'talent_admin_scripts');

/**
 * Enqueue scripts for single advertisement pages
 */
function enqueue_advertisement_scripts() {
    if (is_singular('advertisement')) {
        // Enqueue the global JS file
        wp_enqueue_script(
            'theme-global-js',
            get_stylesheet_directory_uri() . '/global.js',
            array(),
            HELLO_ELEMENTOR_CHILD_VERSION,
            true
        );

        // Localize with AJAX URL and nonce
        wp_localize_script('theme-global-js', 'wpApiSettings', [
            'root' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('express_interest_nonce'),
            'isUserLoggedIn' => is_user_logged_in() ? 1 : 0
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_advertisement_scripts');
