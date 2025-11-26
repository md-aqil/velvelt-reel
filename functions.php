<?php
ob_start();
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0');

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles()
{

    wp_enqueue_style(
        'hello-elementor-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        [
            'hello-elementor-theme-style',
        ],
        HELLO_ELEMENTOR_CHILD_VERSION
    );

    // Enqueue global CSS file
    wp_enqueue_style(
        'theme-global-css',
        get_stylesheet_directory_uri() . '/global.css',
        [],
        HELLO_ELEMENTOR_CHILD_VERSION
    );

}
add_action('wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20);

function custom_enqueue_scripts()
{
    // Enqueue the global JS file
    wp_enqueue_script(
        'theme-global-js', // Handle name
        get_stylesheet_directory_uri() . '/global.js', // File path
        array(), // Dependencies (optional)
        HELLO_ELEMENTOR_CHILD_VERSION, // Version number
        true // Load in footer (true = before </body>)
    );

    // Enqueue your custom JS file
    wp_enqueue_script(
        'talent-submission-js', // Handle name
        get_stylesheet_directory_uri() . '/talent--submission.js', // File path
        array('jquery', 'theme-global-js'), // Dependencies - now includes global.js
        HELLO_ELEMENTOR_CHILD_VERSION, // Version number
        true // Load in footer (true = before </body>)
    );

    // Debug: Log enqueued scripts
    if (defined('WP_DEBUG') && WP_DEBUG) {
        global $wp_scripts;
        error_log('Enqueued scripts: ' . print_r($wp_scripts->queue, true));
    }
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');


function create_userinformation_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'userinformation';
    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        dob DATE NOT NULL,
        address TEXT NOT NULL,
        region VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
    ) $charset_collate;";
    dbDelta($sql);
}
add_action('after_switch_theme', 'create_userinformation_table');

function velvetreel_signup_form_shortcode()
{
    ob_start();

    // Include the signup processing script
    include_once get_template_directory() . '/sign-up.php';

    return ob_get_clean();
}
add_shortcode('velvetreel_signup', 'velvetreel_signup_form_shortcode');

add_action('after_setup_theme', function () {
    if (is_user_logged_in() && !is_admin()) {
        show_admin_bar(true);
    }
});




add_action('wp_ajax_upload_profile_image', 'handle_profile_image_ajax');
function handle_profile_image_ajax()
{
    // Kill any prior output (safely)
    if (ob_get_length())
        ob_end_clean();

    header('Content-Type: application/json; charset=utf-8');

    // Authenticate
    if (!is_user_logged_in()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        wp_die();
    }

    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'profile_image_nonce')) {
        echo json_encode(['success' => false, 'message' => 'Invalid nonce.']);
        wp_die();
    }

    // Required WordPress includes
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    // Check file exists
    if (empty($_FILES['profile_image'])) {
        echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
        wp_die();
    }

    $upload_id = media_handle_upload('profile_image', 0);

    if (is_wp_error($upload_id)) {
        echo json_encode(['success' => false, 'message' => $upload_id->get_error_message()]);
        wp_die();
    }

    $url = wp_get_attachment_url($upload_id);

    if (!$url) {
        echo json_encode(['success' => false, 'message' => 'Failed to get image URL.']);
        wp_die();
    }

    update_user_meta(get_current_user_id(), 'profile_picture', esc_url_raw($url));

    echo json_encode(['success' => true, 'url' => esc_url($url)]);
    wp_die();
}

//sadi




function rifat_elementor_profile_picture_menu()
{
    if (!is_user_logged_in()) {
        // Return a login link for logged-out users instead of an empty string
        // This provides a better user experience
        $login_url = home_url('/sign-in/');
        return '<a href="' . esc_url($login_url) . '" class="login-menu-link">Login</a>';
    }

    $user_id = get_current_user_id();
    $profile_picture = get_user_meta($user_id, 'profile_picture', true);

    if (!$profile_picture) {
        $profile_picture = 'http://www.thevelvetreel.com/wp-content/uploads/2025/08/20171206_01-scaled.jpg';
    }

    $profile_url = home_url('/my-profile');
    $logout_url = wp_logout_url(home_url());

    ob_start(); ?>
    <div class="rifat-profile-wrapper">

        <a href="<?= esc_url($profile_url); ?>" class="profile-link">
            <img src="<?= !empty($profile_picture)
                ? esc_url($profile_picture)
                : esc_url(home_url('/wp-content/uploads/2025/08/20171206_01-scaled.jpg')); ?>" alt="Profile"
                class="rifat-profile-img">

        </a>
        <div class="rifat-logout">
            <a class="logout-text" href="<?= esc_url($logout_url); ?>">🔓 Logout</a>
        </div>
    </div>



    <style>
        .rifat-profile-wrapper {
            position: relative;
            display: inline-block;
        }

        .rifat-profile-img {
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            display: block !important;
        }



        .rifat-profile-wrapper:hover .rifat-profile-img {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        }

        .logout-text {
            color: white;
        }

        .rifat-logout {
            position: absolute;
            top: 100%;
            right: 0;
            align-items: center;
            padding: 16px 16px;
            border-radius: 8px;
            z-index: 999;
            display: none;
            white-space: nowrap;
        }

        .rifat-profile-wrapper:hover .rifat-logout {
            display: block;
            color: white;
        }

        .rifat-logout a {

            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            color: white;
        }

        .login-menu-link {
            display: inline-block;
            padding: 8px 15px;
            background-color: #b2122d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
        }

        .login-menu-link:hover {
            background-color: #8a0e22;
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('user_profile_menu', 'rifat_elementor_profile_picture_menu');



/**
 * Register a custom post type called "Talent".
 *
 * @see get_post_type_labels() for label keys.
 */
function create_talent_cpt()
{
    $labels = array(
        'name' => _x('Talents', 'Post type general name', 'hello-elementor-child'),
        'singular_name' => _x('Talent', 'Post type singular name', 'hello-elementor-child'),
        'menu_name' => _x('Talents', 'Admin Menu text', 'hello-elementor-child'),
        'name_admin_bar' => _x('Talent', 'Add New on Toolbar', 'hello-elementor-child'),
        'add_new' => __('Add New', 'hello-elementor-child'),
        'add_new_item' => __('Add New Talent', 'hello-elementor-child'),
        'new_item' => __('New Talent', 'hello-elementor-child'),
        'edit_item' => __('Edit Talent', 'hello-elementor-child'),
        'view_item' => __('View Talent', 'hello-elementor-child'),
        'all_items' => __('All Talents', 'hello-elementor-child'),
        'search_items' => __('Search Talents', 'hello-elementor-child'),
        'parent_item_colon' => __('Parent Talents:', 'hello-elementor-child'),
        'not_found' => __('No talents found.', 'hello-elementor-child'),
        'not_found_in_trash' => __('No talents found in Trash.', 'hello-elementor-child'),
        'featured_image' => _x('Talent Profile Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'set_featured_image' => _x('Set profile image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'remove_featured_image' => _x('Remove profile image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'use_featured_image' => _x('Use as profile image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'archives' => _x('Talent archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'hello-elementor-child'),
        'insert_into_item' => _x('Insert into talent', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'hello-elementor-child'),
        'uploaded_to_this_item' => _x('Uploaded to this talent', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'hello-elementor-child'),
        'filter_items_list' => _x('Filter talents list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'hello-elementor-child'),
        'items_list_navigation' => _x('Talents list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'hello-elementor-child'),
        'items_list' => _x('Talents list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'hello-elementor-child'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'talent'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields'),
        'menu_icon' => 'dashicons-groups',
    );

    register_post_type('talent', $args);
}
add_action('init', 'create_talent_cpt');

/**
 * Create a custom taxonomy for the "Talent" post type.
 */
function create_talent_taxonomy()
{
    $labels = array(
        'name' => _x('Talent Types', 'taxonomy general name', 'hello-elementor-child'),
        'singular_name' => _x('Talent Type', 'taxonomy singular name', 'hello-elementor-child'),
        'search_items' => __('Search Talent Types', 'hello-elementor-child'),
        'all_items' => __('All Talent Types', 'hello-elementor-child'),
        'parent_item' => __('Parent Talent Type', 'hello-elementor-child'),
        'parent_item_colon' => __('Parent Talent Type:', 'hello-elementor-child'),
        'edit_item' => __('Edit Talent Type', 'hello-elementor-child'),
        'update_item' => __('Update Talent Type', 'hello-elementor-child'),
        'add_new_item' => __('Add New Talent Type', 'hello-elementor-child'),
        'new_item_name' => __('New Talent Type Name', 'hello-elementor-child'),
        'menu_name' => __('Talent Types', 'hello-elementor-child'),
    );

    $args = array(
        'hierarchical' => true, // Like categories
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'talent-type'),
    );

    register_taxonomy('talent_type', array('talent'), $args);
}
add_action('init', 'create_talent_taxonomy');

/**
 * Create a custom taxonomy for "Talent Tagging".
 */
function create_talent_tagging_taxonomy()
{
    $labels = array(
        'name' => _x('Talent Tags', 'taxonomy general name', 'hello-elementor-child'),
        'singular_name' => _x('Talent Tag', 'taxonomy singular name', 'hello-elementor-child'),
        'search_items' => __('Search Talent Tags', 'hello-elementor-child'),
        'all_items' => __('All Talent Tags', 'hello-elementor-child'),
        'parent_item' => __('Parent Talent Tag', 'hello-elementor-child'),
        'parent_item_colon' => __('Parent Talent Tag:', 'hello-elementor-child'),
        'edit_item' => __('Edit Talent Tag', 'hello-elementor-child'),
        'update_item' => __('Update Talent Tag', 'hello-elementor-child'),
        'add_new_item' => __('Add New Talent Tag', 'hello-elementor-child'),
        'new_item_name' => __('New Talent Tag Name', 'hello-elementor-child'),
        'menu_name' => __('Talent Tags', 'hello-elementor-child'),
    );

    $args = array(
        'hierarchical' => false, // Like tags
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'talent-tagging'),
    );

    register_taxonomy('talent_tagging', array('talent'), $args);
}
add_action('init', 'create_talent_tagging_taxonomy');

/**
 * Create a custom taxonomy for "Talent State".
 */
function create_talent_state_taxonomy()
{
    $labels = array(
        'name' => _x('Talent States', 'taxonomy general name', 'hello-elementor-child'),
        'singular_name' => _x('Talent State', 'taxonomy singular name', 'hello-elementor-child'),
        'search_items' => __('Search Talent States', 'hello-elementor-child'),
        'all_items' => __('All Talent States', 'hello-elementor-child'),
        'parent_item' => __('Parent Talent State', 'hello-elementor-child'),
        'parent_item_colon' => __('Parent Talent State:', 'hello-elementor-child'),
        'edit_item' => __('Edit Talent State', 'hello-elementor-child'),
        'update_item' => __('Update Talent State', 'hello-elementor-child'),
        'add_new_item' => __('Add New Talent State', 'hello-elementor-child'),
        'new_item_name' => __('New Talent State Name', 'hello-elementor-child'),
        'menu_name' => __('Talent States', 'hello-elementor-child'),
    );

    $args = array(
        'hierarchical' => false, // Like tags
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'talent-state'),
    );

    register_taxonomy('talent_state', array('talent'), $args);
}
add_action('init', 'create_talent_state_taxonomy');

/**
 * Create a custom taxonomy for "Talent Country".
 */
function create_talent_country_taxonomy()
{
    $labels = array(
        'name' => _x('Talent Countries', 'taxonomy general name', 'hello-elementor-child'),
        'singular_name' => _x('Talent Country', 'taxonomy singular name', 'hello-elementor-child'),
        'search_items' => __('Search Talent Countries', 'hello-elementor-child'),
        'all_items' => __('All Talent Countries', 'hello-elementor-child'),
        'parent_item' => __('Parent Talent Country', 'hello-elementor-child'),
        'parent_item_colon' => __('Parent Talent Country:', 'hello-elementor-child'),
        'edit_item' => __('Edit Talent Country', 'hello-elementor-child'),
        'update_item' => __('Update Talent Country', 'hello-elementor-child'),
        'add_new_item' => __('Add New Talent Country', 'hello-elementor-child'),
        'new_item_name' => __('New Talent Country Name', 'hello-elementor-child'),
        'menu_name' => __('Talent Countries', 'hello-elementor-child'),
    );

    $args = array(
        'hierarchical' => false, // Like tags
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'talent-country'),
    );

    register_taxonomy('talent_country', array('talent'), $args);
}
add_action('init', 'create_talent_country_taxonomy');

/**
 * Add taxonomy support to the 'talent' CPT.
 */
function add_talent_taxonomy_support($args, $post_type)
{
    if ('talent' === $post_type) {
        $args['taxonomies'] = array('talent_type', 'talent_tagging');
    }
    return $args;
}
add_filter('register_post_type_args', 'add_talent_taxonomy_support', 10, 2);

/**
 * Register custom meta fields for the "Talent" post type to make them available to the REST API and Elementor.
 */
function register_talent_meta_fields()
{
    $meta_fields = [
        '_talent_state' => 'string',
        '_talent_country' => 'string',
        '_talent_email' => 'string',
        '_talent_phone' => 'string',
        '_talent_age_group' => 'string',
        '_talent_gender' => 'string',
        '_talent_height' => 'string',
        '_talent_height_unit' => 'string',
        '_talent_measurements' => 'string',
        '_talent_years_active' => 'string',
        '_talent_affiliation' => 'string',
        '_talent_education' => 'string',
        '_talent_interested_projects' => 'string',
        '_talent_willing_to_travel' => 'string',
        '_talent_preferred_locations' => 'string',
        '_talent_brand_collabs' => 'string',
        '_talent_domain' => 'string',
        '_talent_role' => 'string',
        '_talent_instagram' => 'string',
        '_talent_linkedin' => 'string',
        '_talent_website' => 'string',
        '_talent_profile_clicks' => 'integer',
    ];

    foreach ($meta_fields as $meta_key => $type) {
        register_post_meta('talent', $meta_key, [
            'show_in_rest' => true,
            'single' => true,
            'type' => $type,
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
    }

    // Register array-based meta fields
    $array_meta_fields = [
        '_talent_languages',
        '_talent_available_for',
        '_talent_notable_works',
        '_talent_designCategories',
        '_talent_portfolio',
    ];

    foreach ($array_meta_fields as $meta_key) {
        register_post_meta('talent', $meta_key, [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'array',
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
    }
}
add_action('init', 'register_talent_meta_fields');

/**
 * Add custom talent fields to Elementor's dynamic tags dropdown.
 * This makes it easier to select custom fields without typing the key manually.
 */
function add_talent_custom_fields_to_elementor($controls_stack)
{
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
 * Handle the front-end talent submission form.
 */
function handle_talent_submission()
{
    // Check if our form is submitted
    if ('POST' !== $_SERVER['REQUEST_METHOD'] || !isset($_POST['action']) || 'submit_talent_profile' !== $_POST['action']) {
        return;
    }

    // Verify nonce
    if (!isset($_POST['talent_submission_nonce']) || !wp_verify_nonce($_POST['talent_submission_nonce'], 'talent_submission')) {
        wp_die('Security check failed.');
    }

    // Sanitize and prepare post data
    $full_name = sanitize_text_field($_POST['fullName']);
    $style_description = wp_kses_post($_POST['styleDescription']);

    $talent_post = array(
        'post_title' => $full_name,
        'post_content' => $style_description,
        'post_status' => 'pending', // Set to 'publish' to auto-publish, 'pending' for review
        'post_type' => 'talent',
    );

    // Insert the post into the database
    $post_id = wp_insert_post($talent_post);

    if (is_wp_error($post_id)) {
        wp_die('Error creating talent profile: ' . $post_id->get_error_message());
    }

    // --- Handle File Upload (Profile Photo) ---
    if (!empty($_FILES['profilePhoto']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload('profilePhoto', $post_id);

        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    // --- Save Custom Fields (Meta Data) ---
    $meta_fields = [
        '_talent_state' => 'city',  // Form uses 'city' as field name for state
        '_talent_country' => 'country',
        '_talent_email' => 'email',
        '_talent_phone' => 'phone',
        '_talent_age_group' => 'ageGroup',
        '_talent_gender' => 'gender',
        '_talent_height' => 'height',
        '_talent_height_unit' => 'heightUnit',
        '_talent_measurements' => 'measurements',
        '_talent_years_active' => 'yearsActive',
        '_talent_affiliation' => 'affiliation',
        '_talent_education' => 'education',
        '_talent_interested_projects' => 'interestedProjects',
        '_talent_willing_to_travel' => 'willingToTravel',
        '_talent_preferred_locations' => 'preferredLocations',
        '_talent_brand_collabs' => 'brandCollabs',
        '_talent_domain' => 'domain',
        '_talent_role' => 'role',
        '_talent_instagram' => 'instagram',
        '_talent_linkedin' => 'linkedin',
        '_talent_tiktok' => 'tiktok',
        '_talent_website' => 'website',
    ];

    foreach ($meta_fields as $meta_key => $post_key) {
        if (isset($_POST[$post_key])) {
            $value = sanitize_text_field($_POST[$post_key]);
            update_post_meta($post_id, $meta_key, $value);
        }
    }

    // Handle languages field (comma-separated string)
    if (isset($_POST['languages'])) {
        if (is_array($_POST['languages'])) {
            // If it's already an array (from JS processing)
            $languages = array_map('sanitize_text_field', $_POST['languages']);
        } else {
            // If it's a comma-separated string
            $languages = array_map('trim', explode(',', sanitize_text_field($_POST['languages'])));
        }
        update_post_meta($post_id, '_talent_languages', $languages);
    }
    if (isset($_POST['availableFor']) && is_array($_POST['availableFor'])) {
        $available_for = array_map('sanitize_text_field', $_POST['availableFor']);
        update_post_meta($post_id, '_talent_available_for', $available_for);
    }

    // --- Set Taxonomy Term for Talent Type ---
    if (isset($_POST['role'])) {
        $role_slug = sanitize_title($_POST['role']);
        if (!empty($role_slug)) {
            // Create a user-friendly name from the slug
            $role_name = ucwords(str_replace('-', ' ', $role_slug));
            // This will create the term if it doesn't exist, and assign it.
            wp_set_object_terms($post_id, $role_name, 'talent_type');
        }
    }

    // --- Set Taxonomy Term for Talent State ---
    if (isset($_POST['state'])) {
        $state = sanitize_text_field($_POST['state']);
        if (!empty($state)) {
            wp_set_object_terms($post_id, $state, 'talent_state');
        }
    }

    // --- Set Taxonomy Term for Talent Country ---
    if (isset($_POST['country'])) {
        $country = sanitize_text_field($_POST['country']);
        if (!empty($country)) {
            wp_set_object_terms($post_id, $country, 'talent_country');
        }
    }


    // Handle repeatable fields (Notable Works)
    if (isset($_POST['workTitle']) && is_array($_POST['workTitle'])) {
        $notable_works = [];
        $work_titles = $_POST['workTitle'];
        // $work_years = $_POST['workYear']; // This field is no longer in use
        $work_start_dates = $_POST['workStartDate'] ?? [];
        $work_end_dates = $_POST['workEndDate'] ?? [];
        $work_present = $_POST['workPresent'] ?? [];
        $work_roles = $_POST['workRole'];
        $work_descriptions = $_POST['workDescription'];

        for ($i = 0; $i < count($work_titles); $i++) {
            if (!empty($work_titles[$i])) {
                $notable_works[] = [
                    'title' => sanitize_text_field($work_titles[$i]),
                    'role' => sanitize_text_field($work_roles[$i]),
                    'startDate' => sanitize_text_field($work_start_dates[$i]),
                    'endDate' => isset($work_present[$i]) && $work_present[$i] === 'on' ? '' : sanitize_text_field($work_end_dates[$i]),
                    'present' => isset($work_present[$i]) && $work_present[$i] === 'on' ? 'on' : 'off',
                    'description' => sanitize_textarea_field($work_descriptions[$i]),
                ];
            }
        }
        update_post_meta($post_id, '_talent_notable_works', $notable_works);
    }

    // --- Handle Role-Specific Fields & Portfolio Gallery ---
    // This part needs to be expanded for each role.

    // Example for Fashion Designer
    if (isset($_POST['designCategories']) && is_array($_POST['designCategories'])) {
        $design_categories = array_map('sanitize_text_field', $_POST['designCategories']);
        update_post_meta($post_id, '_talent_designCategories', $design_categories);
    }

    // Handle Portfolio Gallery Upload (for roles that have it)
    // Check for all possible portfolio input names
    $portfolio_files = null;
    if (!empty($_FILES['portfolio']['name'][0])) {
        $portfolio_files = $_FILES['portfolio'];
    } elseif (!empty($_FILES['portfolio-textile']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-textile'];
    } elseif (!empty($_FILES['portfolio-accessory']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-accessory'];
    } elseif (!empty($_FILES['portfolio-illustrator']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-illustrator'];
    } elseif (!empty($_FILES['portfolio-model']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-model'];
    } elseif (!empty($_FILES['portfolio-choreographer']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-choreographer'];
    } elseif (!empty($_FILES['portfolio-stylist']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-stylist'];
    } elseif (!empty($_FILES['portfolio-makeup']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-makeup'];
    } elseif (!empty($_FILES['portfolio-director']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-director'];
    } elseif (!empty($_FILES['portfolio-storyboard']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-storyboard'];
    } elseif (!empty($_FILES['portfolio-actor']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-actor'];
    } elseif (!empty($_FILES['portfolio-voice']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-voice'];
    } elseif (!empty($_FILES['portfolio-dancer']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-dancer'];
    } elseif (!empty($_FILES['portfolio-dop']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-dop'];
    } elseif (!empty($_FILES['portfolio-assistant-director']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-assistant-director'];
    } elseif (!empty($_FILES['portfolio-screenwriter']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-screenwriter'];
    } elseif (!empty($_FILES['portfolio-editor']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-editor'];
    }

    if ($portfolio_files) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_ids = [];

        foreach ($portfolio_files['name'] as $key => $value) {
            if ($portfolio_files['name'][$key]) {
                // Create a temporary file array for media_handle_upload
                $_FILES['single_portfolio_image'] = [
                    'name' => $portfolio_files['name'][$key],
                    'type' => $portfolio_files['type'][$key],
                    'tmp_name' => $portfolio_files['tmp_name'][$key],
                    'error' => $portfolio_files['error'][$key],
                    'size' => $portfolio_files['size'][$key]
                ];
                $attachment_id = media_handle_upload('single_portfolio_image', $post_id);
                if (!is_wp_error($attachment_id)) {
                    $attachment_ids[] = $attachment_id;
                }
            }
        }
        update_post_meta($post_id, '_talent_portfolio', $attachment_ids);
    }

    // --- Redirect after submission ---
    // You can create a "Thank You" page and redirect there.
    $redirect_url = add_query_arg('success', 'true', get_permalink()); // Redirect back to the form page with a success message
    wp_redirect($redirect_url);
    exit;
}
add_action('template_redirect', 'handle_talent_submission');

/**
 * Handle the front-end talent profile update form.
 */

/**
 * Handle the front-end talent profile update form.
 */
function handle_talent_update()
{
    // Check if our update form is submitted
    if ('POST' !== $_SERVER['REQUEST_METHOD'] || !isset($_POST['action']) || 'update_talent_profile' !== $_POST['action']) {
        return;
    }

    // Verify nonce
    if (!isset($_POST['talent_update_nonce']) || !wp_verify_nonce($_POST['talent_update_nonce'], 'talent_update')) {
        wp_die('Security check failed.');
    }

    // Check user is logged in and is the author of the post
    $post_id = (int) $_POST['post_id'];
    if (!is_user_logged_in() || get_post_field('post_author', $post_id) != get_current_user_id()) {
        wp_die('You do not have permission to edit this profile.');
    }

    // Sanitize and prepare post data
    $full_name = sanitize_text_field($_POST['fullName']);
    $style_description = wp_kses_post($_POST['styleDescription']);

    $talent_post_update = array(
        'ID' => $post_id,
        'post_title' => $full_name,
        'post_content' => $style_description,
        'post_status' => 'pending', // Set back to pending for re-approval
    );

    // Update the post in the database
    $result = wp_update_post($talent_post_update);

    // Check if post update was successful
    if (is_wp_error($result)) {
        error_log('Talent profile update failed: ' . $result->get_error_message());
        // Continue with the rest of the update instead of dying
    }

    // --- Handle File Upload (Profile Photo) ---
    if (!empty($_FILES['profilePhoto']['name'])) {
        // Ensure required functions are available
        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }

        // Delete old thumbnail if it exists
        if (has_post_thumbnail($post_id)) {
            delete_post_thumbnail($post_id);
        }

        $attachment_id = media_handle_upload('profilePhoto', $post_id);

        if (is_wp_error($attachment_id)) {
            error_log('Profile photo upload failed: ' . $attachment_id->get_error_message());
            // Don't die here, just continue with the rest of the update
        } else {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }

    // --- Save Custom Fields (Meta Data) ---
    $meta_fields = [
        '_talent_state' => 'state',
        '_talent_country' => 'country',
        '_talent_email' => 'email',
        '_talent_phone' => 'phone',
        '_talent_age_group' => 'ageGroup',
        '_talent_gender' => 'gender',
        '_talent_height' => 'height',
        '_talent_height_unit' => 'heightUnit',
        '_talent_measurements' => 'measurements',
        '_talent_years_active' => 'yearsActive',
        '_talent_affiliation' => 'affiliation',
        '_talent_education' => 'education',
        '_talent_interested_projects' => 'interestedProjects',
        '_talent_willing_to_travel' => 'willingToTravel',
        '_talent_preferred_locations' => 'preferredLocations',
        '_talent_brand_collabs' => 'brandCollabs',
        '_talent_domain' => 'domain',
        '_talent_role' => 'role',
        '_talent_instagram' => 'instagram',
        '_talent_linkedin' => 'linkedin',
        '_talent_tiktok' => 'tiktok',
        '_talent_website' => 'website',
    ];

    foreach ($meta_fields as $meta_key => $post_key) {
        if (isset($_POST[$post_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$post_key]));
        }
    }

    // Handle languages field (comma-separated string)
    if (isset($_POST['languages'])) {
        if (is_array($_POST['languages'])) {
            // If it's already an array (from JS processing)
            $languages = array_map('sanitize_text_field', $_POST['languages']);
        } else {
            // If it's a comma-separated string
            $languages = array_map('trim', explode(',', sanitize_text_field($_POST['languages'])));
        }
        update_post_meta($post_id, '_talent_languages', $languages);
    }

    if (isset($_POST['availableFor']) && is_array($_POST['availableFor'])) {
        update_post_meta($post_id, '_talent_available_for', array_map('sanitize_text_field', $_POST['availableFor']));
    }

    if (isset($_POST['workTitle']) && is_array($_POST['workTitle'])) {
        // --- Set Taxonomy Term for Talent Type on Update ---
        if (isset($_POST['role'])) {
            $role_slug = sanitize_title($_POST['role']);
            if (!empty($role_slug)) {
                // Create a user-friendly name from the slug
                $role_name = ucwords(str_replace('-', ' ', $role_slug));
                // This will create the term if it doesn't exist, and assign it.
                // The `false` for the third parameter replaces existing terms in this taxonomy.
                wp_set_object_terms($post_id, $role_name, 'talent_type', false);
            }
        }

        $notable_works = [];
        for ($i = 0; $i < count($_POST['workTitle']); $i++) {
            if (!empty($_POST['workTitle'][$i])) {
                $is_present = isset($_POST['workPresent']) && is_array($_POST['workPresent']) && isset($_POST['workPresent'][$i]) && $_POST['workPresent'][$i] === 'on';
                $notable_works[] = [
                    'title' => sanitize_text_field($_POST['workTitle'][$i]),
                    'role' => sanitize_text_field($_POST['workRole'][$i]),
                    'startDate' => sanitize_text_field($_POST['workStartDate'][$i]),
                    'endDate' => $is_present ? '' : sanitize_text_field($_POST['workEndDate'][$i]),
                    'present' => $is_present ? 'on' : 'off',
                    'description' => sanitize_textarea_field($_POST['workDescription'][$i]),
                ];
            }
        }
        update_post_meta($post_id, '_talent_notable_works', $notable_works);
    }

    // --- Handle Role-Specific Fields & Portfolio Gallery Update ---
    if (isset($_POST['designCategories']) && is_array($_POST['designCategories'])) {
        $design_categories = array_map('sanitize_text_field', $_POST['designCategories']);
        update_post_meta($post_id, '_talent_designCategories', $design_categories);
    }

    // Handle Portfolio Gallery Upload (for roles that have it)
    // This will ADD to existing images. For a replacement logic, you'd first delete old attachments.
    // Check for all possible portfolio input names
    $portfolio_files = null;
    if (!empty($_FILES['portfolio']['name'][0])) {
        $portfolio_files = $_FILES['portfolio'];
    } elseif (!empty($_FILES['portfolio-textile']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-textile'];
    } elseif (!empty($_FILES['portfolio-accessory']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-accessory'];
    } elseif (!empty($_FILES['portfolio-illustrator']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-illustrator'];
    } elseif (!empty($_FILES['portfolio-model']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-model'];
    } elseif (!empty($_FILES['portfolio-choreographer']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-choreographer'];
    } elseif (!empty($_FILES['portfolio-stylist']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-stylist'];
    } elseif (!empty($_FILES['portfolio-makeup']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-makeup'];
    } elseif (!empty($_FILES['portfolio-director']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-director'];
    } elseif (!empty($_FILES['portfolio-storyboard']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-storyboard'];
    } elseif (!empty($_FILES['portfolio-actor']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-actor'];
    } elseif (!empty($_FILES['portfolio-voice']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-voice'];
    } elseif (!empty($_FILES['portfolio-dancer']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-dancer'];
    } elseif (!empty($_FILES['portfolio-dop']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-dop'];
    } elseif (!empty($_FILES['portfolio-assistant-director']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-assistant-director'];
    } elseif (!empty($_FILES['portfolio-screenwriter']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-screenwriter'];
    } elseif (!empty($_FILES['portfolio-editor']['name'][0])) {
        $portfolio_files = $_FILES['portfolio-editor'];
    }

    if ($portfolio_files) {
        // Ensure required functions are available
        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }

        $new_attachment_ids = get_post_meta($post_id, '_talent_portfolio', true) ?: [];
        if (!is_array($new_attachment_ids))
            $new_attachment_ids = [];

        foreach ($portfolio_files['name'] as $key => $value) {
            if ($portfolio_files['name'][$key]) {
                $_FILES['single_portfolio_image'] = [
                    'name' => $portfolio_files['name'][$key],
                    'type' => $portfolio_files['type'][$key],
                    'tmp_name' => $portfolio_files['tmp_name'][$key],
                    'error' => $portfolio_files['error'][$key],
                    'size' => $portfolio_files['size'][$key]
                ];
                $attachment_id = media_handle_upload('single_portfolio_image', $post_id);
                if (!is_wp_error($attachment_id)) {
                    $new_attachment_ids[] = $attachment_id;
                } else {
                    error_log('Portfolio image upload failed: ' . $attachment_id->get_error_message());
                }
            }
        }
        update_post_meta($post_id, '_talent_portfolio', $new_attachment_ids);
    }

    // --- Redirect after update ---
    $redirect_url = add_query_arg('updated', 'true', get_permalink());
    wp_redirect($redirect_url);
    exit;
}

add_action('template_redirect', 'handle_talent_update');

/**
 * Adds a meta box to the "Talent" post type editor.
 */
function talent_add_meta_boxes()
{
    add_meta_box(
        'talent_details_meta_box',          // ID
        'Talent Details',                   // Title
        'talent_details_meta_box_callback', // Callback function
        'talent',                           // Post type
        'normal',                           // Context
        'high'                              // Priority
    );
}
add_action('add_meta_boxes', 'talent_add_meta_boxes');

/**
 * Enqueue media scripts for portfolio gallery
 */
function talent_admin_scripts($hook)
{
    global $post;

    if ($hook == 'post-new.php' || $hook == 'post.php') {
        if ('talent' === $post->post_type) {
            wp_enqueue_media();
        }
    }
}
add_action('admin_enqueue_scripts', 'talent_admin_scripts');



/**
 * Callback function to render the talent details meta box content.
 *
 * @param WP_Post $post The post object.
 */
function talent_details_meta_box_callback($post)
{
    // Add a nonce field so we can check for it later.
    wp_nonce_field('talent_save_meta_box_data', 'talent_meta_box_nonce');

    // --- Define all fields ---
    $fields = [
        'Profile Basics' => [
            '_talent_email' => 'Contact Email',
            '_talent_phone' => 'Phone Number',
            '_talent_state' => 'State',
            '_talent_country' => 'Country',
            '_talent_age_group' => 'Age Group',
            '_talent_gender' => 'Gender',
            '_talent_height' => 'Height',
            '_talent_height_unit' => 'Height Unit',
            '_talent_measurements' => 'Measurements',
            '_talent_languages' => 'Languages Known',
        ],
        'Experience & Portfolio' => [
            '_talent_years_active' => 'Years Active',
            '_talent_affiliation' => 'Affiliation',
            '_talent_education' => 'Education / Training',
            '_talent_ied_projects' => 'Interested Projects',
            '_talent_notable_works' => 'Notable Works',
        ],
        'Availability & Preferences' => [
            '_talent_available_for' => 'Available For',
            '_talent_willing_to_travel' => 'Willing to Travel',
            '_talent_preferred_locations' => 'Preferred Locations',
            '_talent_brand_collabs' => 'Open to Brand Collaborations',
        ],
        'Domain & Role' => [
            '_talent_domain' => 'Domain',
            '_talent_role' => 'Role',
        ],
        'Social Links' => [
            '_talent_instagram' => 'Instagram',
            '_talent_linkedin' => 'LinkedIn',
            '_talent_tiktok' => 'TikTok',
            '_talent_website' => 'Website',
        ],
        'Portfolio' => [
            '_talent_portfolio' => 'Portfolio Gallery',
        ],
    ];

    echo '<style>
        .talent-meta-box-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; color: #333; }
        .talent-meta-box-field { margin-bottom: 15px; background: #f9f9f9; padding: 10px; border-left: 3px solid #b2122d; }
        .talent-meta-box-field.full-width { grid-column: 1 / -1; }
        .talent-meta-box-field label { font-weight: bold; display: block; margin-bottom: 5px; }
        .talent-meta-box-field .value { font-size: 14px; padding: 8px; background: #fff; border: 1px solid #ddd; border-radius: 4px; }
        .talent-meta-box-field pre { white-space: pre-wrap; word-wrap: break-word; background: #fff; padding: 10px; border: 1px solid #ddd; max-height: 200px; overflow-y: auto; }
        .talent-meta-box h3 { border-bottom: 2px solid #b2122d; padding-bottom: 10px; margin-top: 30px; margin-bottom: 20px; }
        .talent-meta-box h3:first-of-type { margin-top: 0; }
        .portfolio-image-preview { max-width: 150px; height: auto; margin: 5px; display: inline-block; }
        .portfolio-images-container { display: flex; flex-wrap: wrap; }
    </style>';

    echo '<div class="talent-meta-box">';

    foreach ($fields as $section_title => $section_fields) {
        echo '<h3>' . esc_html($section_title) . '</h3>';
        echo '<div class="talent-meta-box-grid">';

        foreach ($section_fields as $meta_key => $label) {
            $value = get_post_meta($post->ID, $meta_key, true);
            $is_full_width = in_array($meta_key, ['_talent_education', '_talent_interested_projects', '_talent_notable_works', '_talent_portfolio']);

            echo '<div class="talent-meta-box-field ' . ($is_full_width ? 'full-width' : '') . '">';
            echo '<label for="' . esc_attr($meta_key) . '">' . esc_html($label) . ':</label>';

            if ($meta_key === '_talent_portfolio') {
                // Handle portfolio gallery using WordPress media library
                echo '<div class="value">';
                echo '<div class="portfolio-gallery-container">';

                // Hidden input to store attachment IDs
                echo '<input type="hidden" id="_talent_portfolio" name="_talent_portfolio" value="' . esc_attr(is_array($value) ? implode(',', $value) : $value) . '" />';

                // Gallery display area
                echo '<div id="portfolio-gallery-preview" class="portfolio-images-container">';
                if (!empty($value) && is_array($value)) {
                    foreach ($value as $image_id) {
                        if (is_array($image_id)) {
                            foreach ($image_id as $id) {
                                $image_url = wp_get_attachment_image_url($id, 'thumbnail');
                                if ($image_url) {
                                    echo '<img src="' . esc_url($image_url) . '" class="portfolio-image-preview" alt="Portfolio image" data-id="' . esc_attr($id) . '" />';
                                }
                            }
                        } else {
                            $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                            if ($image_url) {
                                echo '<img src="' . esc_url($image_url) . '" class="portfolio-image-preview" alt="Portfolio image" data-id="' . esc_attr($image_id) . '" />';
                            }
                        }
                    }
                }
                echo '</div>';

                // Add gallery management buttons
                echo '<div style="margin-top: 15px;">';
                echo '<button type="button" id="add-portfolio-images" class="button">Manage Portfolio Gallery</button>';
                echo '<p class="description">Click to open WordPress Media Library and select images for the portfolio gallery.</p>';
                echo '</div>';

                echo '</div>';

                // Add JavaScript for WordPress media library integration
                echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var addButton = document.getElementById("add-portfolio-images");
                    var galleryInput = document.getElementById("_talent_portfolio");
                    var galleryPreview = document.getElementById("portfolio-gallery-preview");
                    
                    if (addButton) {
                        addButton.addEventListener("click", function() {
                            // Create a new media frame
                            var frame = wp.media({
                                title: "Select Portfolio Images",
                                multiple: true,
                                library: { type: "image" },
                                button: { text: "Use Selected Images" }
                            });
                            
                            // When an image is selected in the media frame...
                            frame.on("select", function() {
                                // Get media attachment details from the frame state
                                var attachments = frame.state().get("selection").toJSON();
                                
                                // Update hidden input with attachment IDs
                                var ids = attachments.map(function(attachment) {
                                    return attachment.id;
                                });
                                galleryInput.value = ids.join(",");
                                
                                // Update preview area
                                galleryPreview.innerHTML = "";
                                attachments.forEach(function(attachment) {
                                    var img = document.createElement("img");
                                    img.src = attachment.sizes.thumbnail.url;
                                    img.className = "portfolio-image-preview";
                                    img.alt = "Portfolio image";
                                    img.setAttribute("data-id", attachment.id);
                                    galleryPreview.appendChild(img);
                                });
                            });
                            
                            // Open the media frame
                            frame.open();
                        });
                    }
                });
                </script>';
                echo '</div>';
            } elseif ($meta_key === '_talent_notable_works') {
                // Handle notable works with editable fields
                echo '<div class="value">';
                echo '<div id="notable-works-container">';

                if (!empty($value) && is_array($value)) {
                    foreach ($value as $index => $work) {
                        echo '<div class="notable-work-item">';
                        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; padding: 10px; background: #f0f0f0; border-radius: 5px;">';
                        echo '<div><label>Title:</label><input type="text" name="_talent_notable_works_titles[]" value="' . esc_attr($work['title'] ?? '') . '" style="width: 100%;" /></div>';
                        echo '<div><label>Role:</label><input type="text" name="_talent_notable_works_roles[]" value="' . esc_attr($work['role'] ?? '') . '" style="width: 100%;" /></div>';
                        echo '<div><label>Start Date:</label><input type="month" name="_talent_notable_works_start_dates[]" value="' . esc_attr($work['startDate'] ?? '') . '" style="width: 100%;" /></div>';
                        echo '<div><label>End Date:</label><input type="month" name="_talent_notable_works_end_dates[]" value="' . esc_attr($work['endDate'] ?? '') . '" style="width: 100%;" /></div>';
                        echo '<div><label>Present:</label><input type="checkbox" name="_talent_notable_works_present[]" ' . ((isset($work['present']) && $work['present'] === 'on') ? 'checked' : '') . ' value="on" /></div>';
                        echo '<div style="grid-column: 1 / -1;"><label>Description:</label><textarea name="_talent_notable_works_descriptions[]" style="width: 100%;">' . esc_textarea($work['description'] ?? '') . '</textarea></div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    // Add empty fields for new entry
                    echo '<div class="notable-work-item">';
                    echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; padding: 10px; background: #f0f0f0; border-radius: 5px;">';
                    echo '<div><label>Title:</label><input type="text" name="_talent_notable_works_titles[]" value="" style="width: 100%;" /></div>';
                    echo '<div><label>Role:</label><input type="text" name="_talent_notable_works_roles[]" value="" style="width: 100%;" /></div>';
                    echo '<div><label>Start Date:</label><input type="month" name="_talent_notable_works_start_dates[]" value="" style="width: 100%;" /></div>';
                    echo '<div><label>End Date:</label><input type="month" name="_talent_notable_works_end_dates[]" value="" style="width: 100%;" /></div>';
                    echo '<div><label>Present:</label><input type="checkbox" name="_talent_notable_works_present[]" value="on" /></div>';
                    echo '<div style="grid-column: 1 / -1;"><label>Description:</label><textarea name="_talent_notable_works_descriptions[]" style="width: 100%;"></textarea></div>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
                echo '<button type="button" id="add-notable-work" style="margin-top: 10px; padding: 5px 10px;">Add Another Work</button>';
                echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var addButton = document.getElementById("add-notable-work");
                    if (addButton) {
                        addButton.addEventListener("click", function() {
                            var container = document.getElementById("notable-works-container");
                            var newItem = document.createElement("div");
                            newItem.className = "notable-work-item";
                            newItem.innerHTML = \'<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; padding: 10px; background: #f0f0f0; border-radius: 5px;"><div><label>Title:</label><input type="text" name="_talent_notable_works_titles[]" value="" style="width: 100%;" /></div><div><label>Role:</label><input type="text" name="_talent_notable_works_roles[]" value="" style="width: 100%;" /></div><div><label>Start Date:</label><input type="month" name="_talent_notable_works_start_dates[]" value="" style="width: 100%;" /></div><div><label>End Date:</label><input type="month" name="_talent_notable_works_end_dates[]" value="" style="width: 100%;" /></div><div><label>Present:</label><input type="checkbox" name="_talent_notable_works_present[]" value="on" /></div><div style="grid-column: 1 / -1;"><label>Description:</label><textarea name="_talent_notable_works_descriptions[]" style="width: 100%;"></textarea></div></div>\';
                            container.appendChild(newItem);
                        });
                    }
                });
                </script>';
                echo '</div>';
            } elseif (is_array($value)) {
                // Handle arrays (like checkboxes or repeatable fields)
                echo '<div class="value">' . esc_html(implode(', ', $value)) . '</div>';
            } elseif (!empty($value)) {
                // Handle simple text values
                if ($meta_key === '_talent_willing_to_travel' || $meta_key === '_talent_brand_collabs') {
                    echo '<div class="value">' . ($value === 'on' ? 'Yes' : 'No') . '</div>';
                } else {
                    echo '<div class="value">' . esc_html($value) . '</div>';
                }
            } else {
                echo '<div class="value"><em>Not provided.</em></div>';
            }
            echo '</div>';
        }
        echo '</div>'; // end .talent-meta-box-grid
    }
    echo '</div>'; // end .talent-meta-box
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function talent_save_meta_box_data($post_id)
{
    // Check if our nonce is set.
    if (!isset($_POST['talent_meta_box_nonce'])) {
        return;
    }
    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['talent_meta_box_nonce'], 'talent_save_meta_box_data')) {
        return;
    }
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'talent' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Save portfolio gallery images
    if (isset($_POST['_talent_portfolio'])) {
        $attachment_ids = sanitize_text_field($_POST['_talent_portfolio']);
        if (!empty($attachment_ids)) {
            $attachment_ids = explode(',', $attachment_ids);
            $attachment_ids = array_map('intval', $attachment_ids);
        } else {
            $attachment_ids = [];
        }
        update_post_meta($post_id, '_talent_portfolio', $attachment_ids);
    }

    // Save other fields
    $meta_fields = [
        '_talent_email' => 'Contact Email',
        '_talent_phone' => 'Phone Number',
        '_talent_state' => 'State',
        '_talent_country' => 'Country',
        '_talent_age_group' => 'Age Group',
        '_talent_gender' => 'Gender',
        '_talent_height' => 'Height',
        '_talent_height_unit' => 'Height Unit',
        '_talent_measurements' => 'Measurements',
        '_talent_languages' => 'Languages Known',
        '_talent_years_active' => 'Years Active',
        '_talent_affiliation' => 'Affiliation',
        '_talent_education' => 'Education / Training',
        '_talent_interested_projects' => 'Interested Projects',
        '_talent_available_for' => 'Available For',
        '_talent_willing_to_travel' => 'Willing to Travel',
        '_talent_preferred_locations' => 'Preferred Locations',
        '_talent_brand_collabs' => 'Open to Brand Collaborations',
        '_talent_domain' => 'Domain',
        '_talent_role' => 'Role',
        '_talent_instagram' => 'Instagram',
        '_talent_linkedin' => 'LinkedIn',
        '_talent_tiktok' => 'TikTok',
        '_talent_website' => 'Website',
    ];

    foreach ($meta_fields as $meta_key => $label) {
        if (isset($_POST[$meta_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$meta_key]));
        }
    }

    // Handle array fields
    if (isset($_POST['_talent_languages']) && is_array($_POST['_talent_languages'])) {
        update_post_meta($post_id, '_talent_languages', array_map('sanitize_text_field', $_POST['_talent_languages']));
    }
    if (isset($_POST['_talent_available_for']) && is_array($_POST['_talent_available_for'])) {
        update_post_meta($post_id, '_talent_available_for', array_map('sanitize_text_field', $_POST['_talent_available_for']));
    }

    // Handle notable works
    if (isset($_POST['_talent_notable_works_titles']) && is_array($_POST['_talent_notable_works_titles'])) {
        $notable_works = [];
        $titles = $_POST['_talent_notable_works_titles'];
        $roles = $_POST['_talent_notable_works_roles'] ?? [];
        $start_dates = $_POST['_talent_notable_works_start_dates'] ?? [];
        $end_dates = $_POST['_talent_notable_works_end_dates'] ?? [];
        $present = $_POST['_talent_notable_works_present'] ?? [];
        $descriptions = $_POST['_talent_notable_works_descriptions'] ?? [];

        for ($i = 0; $i < count($titles); $i++) {
            // Skip empty titles
            if (empty($titles[$i])) {
                continue;
            }

            $is_present = isset($present[$i]) && $present[$i] === 'on';
            $notable_works[] = [
                'title' => sanitize_text_field($titles[$i]),
                'role' => sanitize_text_field($roles[$i] ?? ''),
                'startDate' => sanitize_text_field($start_dates[$i] ?? ''),
                'endDate' => $is_present ? '' : sanitize_text_field($end_dates[$i] ?? ''),
                'present' => $is_present ? 'on' : 'off',
                'description' => sanitize_textarea_field($descriptions[$i] ?? ''),
            ];
        }
        update_post_meta($post_id, '_talent_notable_works', $notable_works);
    }
}
add_action('save_post', 'talent_save_meta_box_data');

/**
 * Shortcode to display a success message and a link to the talent archive.
 * This will only display if 'success=true' is in the URL.
 */
function talent_submission_success_message_shortcode()
{
    // Check if the 'success' query parameter is set to 'true'
    if (!isset($_GET['success']) || 'true' !== $_GET['success']) {
        return ''; // If not, return nothing.
    }

    // Get the URL for the 'talent' post type archive
    $portfolio_archive_url = get_post_type_archive_link('talent');

    ob_start();
    ?>
    <div
        style="text-align: center; margin: 20px 0; padding: 20px; border: 1px solid #4CAF50; background-color: #f0fff0; border-radius: 8px; color: #333;">
        <h3 style="color: #4CAF50;">Profile Submitted Successfully!</h3>
        <p>Your profile has been submitted for review. Thank you!</p>
        <?php if ($portfolio_archive_url): ?>
            <a href="<?php echo esc_url($portfolio_archive_url); ?>"
                style="display: inline-block; padding: 12px 24px; font-size: 16px; font-weight: bold; color: #fff; background-color: #b2122d; border-radius: 5px; text-decoration: none; margin-top: 10px;">
                View All Portfolios
            </a>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('talent_submission_success', 'talent_submission_success_message_shortcode');

/**
 * Tracks profile views for talent posts.
 *
 * This function increments a 'profile_clicks' meta field for a talent post
 * each time it is viewed on the front-end. It includes checks to prevent
 * authors from inflating their own view counts and uses a session variable
 * to count only one view per user session.
 */
function velvetreel_track_profile_views()
{
    // Only run on single 'talent' post pages
    if (!is_singular('talent')) {
        return;
    }

    $post_id = get_the_ID();

    // Start a session if not already started
    if (!session_id()) {
        session_start();
    }

    // Check if this post has already been viewed in this session
    if (!isset($_SESSION['viewed_talent_' . $post_id])) {
        $count = (int) get_post_meta($post_id, '_talent_profile_clicks', true);
        update_post_meta($post_id, '_talent_profile_clicks', $count + 1);
        $_SESSION['viewed_talent_' . $post_id] = true; // Mark as viewed for this session
    }
}
add_action('wp_head', 'velvetreel_track_profile_views');

/**
 * Restrict access to the talent submission page
 */
function restrict_talent_submission_access()
{
    // Only apply to the talent submission page
    if (is_page_template('template-talent-submission.php')) {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            // Redirect to sign-in page for non-logged in users
            $redirect_url = home_url('/sign-up/');
            wp_redirect($redirect_url);
            exit;
        }

        // Get current user ID
        $user_id = get_current_user_id();


    }

    // Also check by page ID if template check fails
    // Replace 5327 with your actual talent submission page ID
    $talent_submission_page_id = 5327; // Default ID from your original code
    if (is_page($talent_submission_page_id)) {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            // Redirect to sign-in page for non-logged in users
            $redirect_url = home_url('/sign-up/');
            wp_redirect($redirect_url);
            exit;
        }

        // Get current user ID
        $user_id = get_current_user_id();


    }
}
add_action('template_redirect', 'restrict_talent_submission_access');

/**
 * Restrict access to single talent profiles
 */
function restrict_single_talent_access()
{
    // Only apply to single talent posts
    if (is_singular('talent')) {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            // Check if this is a redirect from login with cache-busting parameter
            if (isset($_GET['login']) && $_GET['login'] === 'success') {
                // Force WordPress to re-check authentication
                wp_get_current_user();

                // If user is now logged in, don't redirect
                if (is_user_logged_in()) {
                    return;
                }
            }

            // Redirect to sign-in page for non-logged in users
            $redirect_url = home_url('/sign-in/');
            wp_redirect($redirect_url);
            exit;
        }
    }
}
add_action('template_redirect', 'restrict_single_talent_access');





// ... existing code ...

/**
 * Restrict access to classified ad posting plan product page
 */
function restrict_classified_ad_posting_plan_access()
{
    // Check if we're on the specific product page by URL path
    // This approach works regardless of domain/base URL changes
    if (strpos($_SERVER['REQUEST_URI'], '/product/classified-ad-posting-plan') === 0) {
        // Check if user is not logged in
        if (!is_user_logged_in()) {
            // Redirect to login page (works with any domain)
            wp_redirect(home_url('/sign-up/'));
            exit;
        }
    }
}
add_action('template_redirect', 'restrict_classified_ad_posting_plan_access');

/**
 * Shortcode to display subscription status indicator for Elementor
 */
function elementor_subscription_status_shortcode()
{
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




/**
 * User Dashboard Shortcode for Elementor
 */
function user_dashboard_shortcode()
{
    // Check if user is logged in
    if (!is_user_logged_in()) {
        return '<div style="padding:40px;text-align:center;background:#3a3a3a;border-radius:8px;color:white;">
                    <p style="font-size:18px;margin-bottom:20px;">Please login to view your dashboard.</p>
                    <a href="' . esc_url(wp_login_url(get_permalink())) . '" style="display:inline-block;padding:12px 24px;background:#df1d3d;color:white;border-radius:6px;text-decoration:none;font-weight:bold;">Login Now</a>
                </div>';
    }

    // Start output buffering
    ob_start();

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $first_name = $current_user->first_name;
    $last_name = $current_user->last_name;
    $email = $current_user->user_email;
    $username = $current_user->user_login;

    // Get extra user information from custom table
    global $wpdb;
    $table = $wpdb->prefix . 'userinformation';
    $extra = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id), ARRAY_A);

    $dob = $extra['dob'] ?? '';
    $address = $extra['address'] ?? '';
    $region = $extra['region'] ?? '';
    $phone = $extra['phone'] ?? '';
    $street_address = $extra['street_address'] ?? '';
    $zip_code = $extra['zip_code'] ?? '';

    $profile_img = get_user_meta($user_id, 'profile_picture', true);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_dashboard']) && isset($_POST['dashboard_nonce'])) {
        if (wp_verify_nonce($_POST['dashboard_nonce'], 'update_user_dashboard')) {
            $new_first = sanitize_text_field($_POST['first_name']);
            $new_last = sanitize_text_field($_POST['last_name']);
            $new_dob = sanitize_text_field($_POST['dob']);
            $new_email = sanitize_email($_POST['email']);
            $new_phone = sanitize_text_field($_POST['phone']);
            $new_address = sanitize_text_field($_POST['address']);
            $new_region = sanitize_text_field($_POST['region']);
            $new_street = sanitize_text_field($_POST['street_address']);
            $new_zip = sanitize_text_field($_POST['zip_code']);

            wp_update_user([
                'ID' => $user_id,
                'user_email' => $new_email,
                'first_name' => $new_first,
                'last_name' => $new_last
            ]);

            $data = [
                'dob' => $new_dob,
                'phone' => $new_phone,
                'address' => $new_address,
                'region' => $new_region,
                'street_address' => $new_street,
                'zip_code' => $new_zip
            ];

            if ($extra) {
                $wpdb->update($table, $data, ['user_id' => $user_id]);
            } else {
                $data['user_id'] = $user_id;
                $wpdb->insert($table, $data);
            }

            wp_redirect(add_query_arg('updated', '1', get_permalink()));
            exit;
        }
    }

    ?>
    <style>
        .user-dashboard-container h2 {
            margin-top: 0;
            color: white;
        }

        .user-dashboard-container .form-group {
            margin-bottom: 20px;
        }

        .user-dashboard-container label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: white;
        }

        .user-dashboard-container input,
        .user-dashboard-container select {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: none;
            font-size: 16px;
            box-sizing: border-box;
        }

        .user-dashboard-container input[type="submit"] {
            background: #df1d3d;
            color: white;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            margin-top: 10px;
        }

        .user-dashboard-container input[type="submit"]:hover {
            background: #c0392b;
        }

        .user-dashboard-container img.profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            background-color: #666;
            display: block;
        }

        .user-dashboard-container button#editBtn {
            width: 100%;
            background: #3498db;
            color: white;
            padding: 10px;
            border: none;
            font-weight: bold;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
        }

        .user-dashboard-container button#editBtn:hover {
            background: #2980b9;
        }

        .user-dashboard-container input[readonly] {
            color: #fff !important;
            opacity: 1 !important;
            background-color: #4a4a4a;
        }

        .success-message {
            color: #2ecc71;
            padding: 15px;
            background: rgba(46, 204, 113, 0.1);
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #2ecc71;
        }
    </style>

    <div class="user-dashboard-wrapper">
        <div class="user-dashboard-container">
            <h2>Welcome, <?= esc_html($first_name ?: $username) ?> 👋</h2>


            <?php if (isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
                <div class="success-message">✓ Dashboard updated successfully!</div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('update_user_dashboard', 'dashboard_nonce'); ?>

                <?php if ($profile_img): ?>
                    <img id="profileImage" src="<?= esc_url($profile_img) ?>" class="profile-pic" alt="Profile Picture">
                <?php else: ?>
                    <img id="profileImage" src="<?= esc_url(get_avatar_url($user_id)) ?>" class="profile-pic"
                        alt="Profile Picture">
                <?php endif; ?>

                <div class="form-group">
                    <label for="profile_image">Change Profile Picture</label>
                    <input type="file" id="fileInput" name="profile_image" accept="image/*" readonly>
                </div>

                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?= esc_attr($first_name) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?= esc_attr($last_name) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?= esc_attr($dob) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= esc_attr($email) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= esc_attr($phone) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?= esc_attr($address) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Region</label>
                    <input type="text" name="region" value="<?= esc_attr($region) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Street Address</label>
                    <input type="text" name="street_address" value="<?= esc_attr($street_address) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>ZIP Code</label>
                    <input type="text" name="zip_code" value="<?= esc_attr($zip_code) ?>" readonly>
                </div>

                <button type="button" id="editBtn">✏️ Edit Profile</button>
                <input type="submit" name="update_dashboard" value="💾 Save Profile" style="display:none;" id="saveBtn">
            </form>
        </div>
    </div>

    <script>
        (function () {
            const editBtn = document.getElementById('editBtn');
            const saveBtn = document.getElementById('saveBtn');

            if (editBtn) {
                editBtn.addEventListener('click', function () {
                    const inputs = document.querySelectorAll('.user-dashboard-container form input');
                    inputs.forEach(input => {
                        if (input.hasAttribute('readonly')) {
                            input.removeAttribute('readonly');
                        }
                    });
                    this.style.display = 'none';
                    saveBtn.style.display = 'block';
                });
            }

            const fileInput = document.getElementById('fileInput');
            if (fileInput) {
                fileInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (!file || !file.type.startsWith('image/')) return;

                    const formData = new FormData();
                    formData.append('action', 'upload_dashboard_image');
                    formData.append('profile_image', file);
                    formData.append('_wpnonce', '<?php echo wp_create_nonce('dashboard_image_nonce'); ?>');

                    const profileImg = document.getElementById('profileImage');
                    profileImg.style.opacity = 0.5;

                    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            profileImg.style.opacity = 1;
                            if (data.success && data.url) {
                                profileImg.src = data.url + '?t=' + new Date().getTime();
                            } else {
                                alert(data.message || 'Upload failed.');
                            }
                        })
                        .catch(err => {
                            profileImg.style.opacity = 1;
                            alert('Upload failed. Please try again.');
                            console.error(err);
                        });
                });
            }
        })();
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('user_dashboard', 'user_dashboard_shortcode');


/**
 * AJAX handler for dashboard image upload
 */
function handle_dashboard_image_upload()
{
    check_ajax_referer('dashboard_image_nonce', '_wpnonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in.']);
    }

    $user_id = get_current_user_id();

    if (!isset($_FILES['profile_image'])) {
        wp_send_json_error(['message' => 'No file uploaded.']);
    }

    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $file = $_FILES['profile_image'];

    // Validate image
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        wp_send_json_error(['message' => 'Invalid file type. Only JPG, PNG, and GIF allowed.']);
    }

    // Upload file
    $upload = wp_handle_upload($file, ['test_form' => false]);

    if (isset($upload['error'])) {
        wp_send_json_error(['message' => $upload['error']]);
    }

    // Save to user meta
    update_user_meta($user_id, 'profile_picture', $upload['url']);

    wp_send_json_success([
        'url' => $upload['url'],
        'message' => 'Profile picture updated successfully!'
    ]);


}
add_action('wp_ajax_upload_dashboard_image', 'handle_dashboard_image_upload');
// ... existing code ...

/**
 * Check if a user already has a talent profile
 *
 * @param int $user_id The user ID to check
 * @param bool $include_drafts Whether to include draft posts in the check
 * @return bool True if user has a talent profile, false otherwise
 */
function user_has_talent_profile($user_id = null, $include_drafts = true)
{
    // Display subscription indicator for all logged-in users
    if (true) {
        // If no user ID provided, use current user
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        // If no user is logged in, return false
        if (!$user_id) {
            return false;
        }

        $post_statuses = array('publish', 'pending');
        if ($include_drafts) {
            $post_statuses[] = 'draft';
        }

        // Query for talent posts by this user
        $args = array(
            'post_type' => 'talent',
            'author' => $user_id,
            'post_status' => $post_statuses,
            'posts_per_page' => 1
        );
    }

    $talent_posts = get_posts($args);

    // Return true if any talent posts found, false otherwise
    return !empty($talent_posts);
}

/**
 * Get the user's draft talent profile if it exists
 *
 * @param int $user_id The user ID to check
 * @return WP_Post|null The draft post object or null
 */
function get_user_draft_profile($user_id = null)
{
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return null;
    }

    $args = array(
        'post_type' => 'talent',
        'author' => $user_id,
        'post_status' => 'draft',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $posts = get_posts($args);
    return !empty($posts) ? $posts[0] : null;
}

/**
 * AJAX handler to save talent form progress
 */
function wp_ajax_save_talent_progress()
{
    // Log that the function was called
    error_log('wp_ajax_save_talent_progress called');
    
    // Log the POST data for debugging
    error_log('POST data: ' . print_r($_POST, true));
    
    // Check if nonce is present
    if (!isset($_POST['nonce'])) {
        error_log('Nonce not present in request');
        wp_send_json_error('Nonce not present');
    }
    
    // Verify nonce
    $nonce_verified = check_ajax_referer('talent_submission_nonce', 'nonce', false);
    if (!$nonce_verified) {
        error_log('Nonce verification failed');
        wp_send_json_error('Nonce verification failed');
    }
    
    error_log('Nonce verified successfully');

    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
    }

    $user_id = get_current_user_id();
    error_log('User ID: ' . $user_id);
    
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    error_log('Post ID: ' . $post_id);

    // Collect form data
    $form_data = isset($_POST['form_data']) ? $_POST['form_data'] : [];
    error_log('Form data received: ' . print_r($form_data, true));

    // Parse the serialized form data into an associative array
    $parsed_data = [];
    parse_str($form_data, $parsed_data);
    error_log('Parsed data: ' . print_r($parsed_data, true));

    // Basic post data
    $post_title = isset($parsed_data['fullName']) ? sanitize_text_field($parsed_data['fullName']) : 'Draft Profile - ' . date('Y-m-d H:i:s');
    $post_content = isset($parsed_data['styleDescription']) ? wp_kses_post($parsed_data['styleDescription']) : '';

    $post_args = array(
        'post_title' => $post_title,
        'post_content' => $post_content,
        'post_status' => 'draft',
        'post_type' => 'talent',
        'post_author' => $user_id
    );

    if ($post_id > 0) {
        // Update existing draft
        $post_args['ID'] = $post_id;

        // Verify ownership
        $post = get_post($post_id);
        if (!$post || $post->post_author != $user_id) {
            wp_send_json_error('Invalid post ID or not authorized to edit.');
        }
        $post_id = wp_update_post($post_args);
    } else {
        $post_id = wp_insert_post($post_args);
    }

    if (is_wp_error($post_id)) {
        wp_send_json_error($post_id->get_error_message());
    }

    // Save Meta Fields
    // We reuse the same meta keys as the main submission handler
    $meta_fields = [
        '_talent_state' => 'city',  // Form uses 'city' as field name for state
        '_talent_country' => 'country',
        '_talent_email' => 'email',
        '_talent_phone' => 'phone',
        '_talent_age_group' => 'ageGroup',
        '_talent_gender' => 'gender',
        '_talent_height' => 'height',
        '_talent_height_unit' => 'heightUnit',
        '_talent_measurements' => 'measurements',
        '_talent_years_active' => 'yearsActive',
        '_talent_affiliation' => 'affiliation',
        '_talent_education' => 'education',
        '_talent_interested_projects' => 'interestedProjects',
        '_talent_willing_to_travel' => 'willingToTravel',
        '_talent_preferred_locations' => 'preferredLocations',
        '_talent_brand_collabs' => 'brandCollabs',
        '_talent_domain' => 'domain',
        '_talent_role' => 'role',
        '_talent_instagram' => 'instagram',
        '_talent_linkedin' => 'linkedin',
        '_talent_tiktok' => 'tiktok',
        '_talent_website' => 'website',
    ];

    foreach ($meta_fields as $meta_key => $post_key) {
        if (isset($parsed_data[$post_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($parsed_data[$post_key]));
        }
    }

    // Handle Arrays (Languages, Available For, Design Categories)
    if (isset($parsed_data['languages'])) {
        // languages might be an array or comma string depending on how it's sent
        // parse_str usually handles array syntax like languages[]
        $languages = $parsed_data['languages'];
        if (!is_array($languages)) {
            $languages = array_map('trim', explode(',', $languages));
        }
        update_post_meta($post_id, '_talent_languages', $languages);
    }

    if (isset($parsed_data['availableFor'])) {
        update_post_meta($post_id, '_talent_available_for', $parsed_data['availableFor']);
    }

    if (isset($parsed_data['designCategories'])) {
        update_post_meta($post_id, '_talent_designCategories', $parsed_data['designCategories']);
    }

    // Handle Notable Works
    if (isset($parsed_data['workTitle']) && is_array($parsed_data['workTitle'])) {
        $notable_works = [];
        for ($i = 0; $i < count($parsed_data['workTitle']); $i++) {
            if (!empty($parsed_data['workTitle'][$i])) {
                $is_present = isset($parsed_data['workPresent']) && isset($parsed_data['workPresent'][$i]) && $parsed_data['workPresent'][$i] === 'on';
                $notable_works[] = [
                    'title' => sanitize_text_field($parsed_data['workTitle'][$i]),
                    'role' => sanitize_text_field($parsed_data['workRole'][$i]),
                    'startDate' => sanitize_text_field($parsed_data['workStartDate'][$i]),
                    'endDate' => $is_present ? '' : sanitize_text_field($parsed_data['workEndDate'][$i]),
                    'present' => $is_present ? 'on' : 'off',
                    'description' => sanitize_textarea_field($parsed_data['workDescription'][$i]),
                ];
            }
        }
        update_post_meta($post_id, '_talent_notable_works', $notable_works);
    }

    // Handle Taxonomies
    // Note: Form uses 'city' as field name for state data
    if (isset($parsed_data['city'])) {
        $state = sanitize_text_field($parsed_data['city']);
        if (!empty($state)) {
            wp_set_object_terms($post_id, $state, 'talent_state');
        }
    }

    if (isset($parsed_data['country'])) {
        $country = sanitize_text_field($parsed_data['country']);
        if (!empty($country)) {
            wp_set_object_terms($post_id, $country, 'talent_country');
        }
    }

    if (isset($parsed_data['role'])) {
        $role_slug = sanitize_title($parsed_data['role']);
        if (!empty($role_slug)) {
            $role_name = ucwords(str_replace('-', ' ', $role_slug));
            wp_set_object_terms($post_id, $role_name, 'talent_type', false);
        }
    }

    error_log('About to send success response with post_id: ' . $post_id);
    wp_send_json_success(['post_id' => $post_id]);
}
add_action('wp_ajax_save_talent_progress', 'wp_ajax_save_talent_progress');
// Restrict access to talent submission page if user already has a profile
function restrict_talent_submission_if_profile_exists()
{
    // Only apply to the talent submission page
    if (is_page_template('template-talent-submission.php')) {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return; // Let the existing restriction handle this
        }

        // Check if user already has a talent profile (excluding drafts)
        if (user_has_talent_profile(null, false)) {
            // Add a message
            add_action('wp', function () {
                add_action('the_content', function ($content) {
                    $message = '<div class="talent-warning" style="padding:15px;background:#fff3cd;border:1px solid #ffeeba;color:#856404;margin-bottom:20px;border-radius:5px;">
                        You already have a profile. You cannot create multiple profiles.
                    </div>';
                    return $message . $content;
                });
            });
        }
    }
}
add_action('template_redirect', 'restrict_talent_submission_if_profile_exists');
// ... existing code ...

/**
 * Redirect default WordPress lost password page to custom forgot password page
 */
function redirect_lost_password_page()
{
    // Check if we're on the default lost password page
    global $wp;

    // Check for both WooCommerce and WordPress default lost password URLs
    if (
        isset($wp->request) && (
            strpos($wp->request, 'my-account/lost-password') !== false ||
            strpos($wp->request, 'wp-login.php?action=lostpassword') !== false ||
            (isset($_GET['action']) && $_GET['action'] === 'lostpassword')
        )
    ) {
        // Redirect to custom forgot password page
        wp_redirect(home_url('/forgot-password/'));
        exit;
    }
}
add_action('template_redirect', 'redirect_lost_password_page', 5);

// Also hook into the login URL filter to change the lost password URL
function custom_lostpassword_url($lostpassword_url)
{
    return home_url('/forgot-password/');
}
add_filter('lostpassword_url', 'custom_lostpassword_url', 10, 1);

/**
 * Redirect users to sign-in page after WooCommerce logout
 */
function custom_woocommerce_logout_redirect($redirect_url)
{
    return home_url('/sign-in/');
}
add_filter('woocommerce_logout_default_redirect_url', 'custom_woocommerce_logout_redirect');

/**
 * Enqueue styles and scripts for talent submission and edit pages
 */
function enqueue_talent_form_assets()
{
    // Always enqueue for testing
    error_log('enqueue_talent_form_assets called');
    
    // Enqueue the form CSS
    wp_enqueue_style(
        'talent-form-style',
        get_stylesheet_directory_uri() . '/form-style.css',
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
        array('jquery'),
        HELLO_ELEMENTOR_CHILD_VERSION,
        true
    );

    // Localize script with draft data and AJAX info
    $draft_post = get_user_draft_profile();
    $draft_data = [];

    if ($draft_post) {
        $draft_data = [
            'post_id' => $draft_post->ID,
            'fullName' => $draft_post->post_title,
            'styleDescription' => $draft_post->post_content,
            // Meta fields
            'state' => get_post_meta($draft_post->ID, '_talent_state', true),
            'country' => get_post_meta($draft_post->ID, '_talent_country', true),
            'email' => get_post_meta($draft_post->ID, '_talent_email', true),
            'phone' => get_post_meta($draft_post->ID, '_talent_phone', true),
            'ageGroup' => get_post_meta($draft_post->ID, '_talent_age_group', true),
            'gender' => get_post_meta($draft_post->ID, '_talent_gender', true),
            'height' => get_post_meta($draft_post->ID, '_talent_height', true),
            'heightUnit' => get_post_meta($draft_post->ID, '_talent_height_unit', true),
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

    error_log('Localizing talentData script');
    wp_localize_script('talent-submission-js', 'talentData', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('talent_submission_nonce'),
        'draft' => $draft_data
    ]);
    error_log('Finished localizing talentData script');
}
add_action('wp_enqueue_scripts', 'enqueue_talent_form_assets');