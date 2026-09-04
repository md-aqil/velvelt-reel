<?php
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

define('HELLO_ELEMENTOR_CHILD_VERSION', '2.4.0');

// Load the Plugin Update Checker library
require_once get_stylesheet_directory() . '/vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';

// Initialize the updater
$myUpdateChecker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/md-aqil/velvelt-reel', // The URL of your GitHub repository
    __FILE__, // Full path to the main theme file or functions.php
    'hello-elementor-child' // Your theme slug
);

// Set which branch to use for updates (defaults to master/main)
$myUpdateChecker->setBranch('main');

/**
 * SEO Hooks for Talent Single Pages
 */
add_action('wp_head', 'vr_talent_seo_meta_tags', 1);
function vr_talent_seo_meta_tags() {
    if (!is_singular('talent')) return;
    
    $post_id = get_the_ID();
    $title = get_the_title($post_id);
    $role = get_post_meta($post_id, '_talent_role', true);
    $state = get_post_meta($post_id, '_talent_state', true);
    $country = get_post_meta($post_id, '_talent_country', true);
    $location = ($state && $country) ? "{$state}, {$country}" : ($state ?: $country);
    $content = get_post_field('post_content', $post_id);
    
    $focus_keyword = $title . ' ' . ucwords(str_replace('-', ' ', $role));
    
    $seo_title = $title . ' - ' . ucwords(str_replace('-', ' ', $role)) . ' in ' . ($location ?: 'New Jersey, USA') . ' | The VelvetReel';
    
    $bio_text = strip_tags($content);
    if (empty($bio_text)) {
        $bio_text = $title . ' is a ' . ucwords(str_replace('-', ' ', $role)) . ' based in ' . ($location ?: 'New Jersey, USA') . '.';
    }
    $meta_description = substr($bio_text, 0, 155);
    
    echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
    echo '<meta name="keywords" content="' . esc_attr($focus_keyword) . ', ' . esc_attr(ucwords(str_replace('-', ' ', $role))) . ', actor, talent, The VelvetReel">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($seo_title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($meta_description) . '">' . "\n";
    echo '<meta property="og:type" content="profile">' . "\n";
    echo '<meta property="og:url" content="' . esc_url(get_permalink($post_id)) . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($seo_title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($meta_description) . '">' . "\n";
    
    if (has_post_thumbnail($post_id)) {
        $thumbnail_url = get_the_post_thumbnail_url($post_id, 'large');
        if ($thumbnail_url) {
            echo '<meta property="og:image" content="' . esc_url($thumbnail_url) . '">' . "\n";
            echo '<meta name="twitter:image" content="' . esc_url($thumbnail_url) . '">' . "\n";
        }
    }
    
    echo '<title>' . esc_html($seo_title) . '</title>' . "\n";
}

add_filter('document_title_parts', 'vr_talent_document_title');
function vr_talent_document_title($title) {
    if (!is_singular('talent')) return $title;
    
    $post_id = get_the_ID();
    $name = get_the_title($post_id);
    $role = get_post_meta($post_id, '_talent_role', true);
    $state = get_post_meta($post_id, '_talent_state', true);
    $country = get_post_meta($post_id, '_talent_country', true);
    $location = ($state && $country) ? "{$state}, {$country}" : ($state ?: $country);
    
    $title['title'] = $name . ' - ' . ucwords(str_replace('-', ' ', $role));
    $title['site'] = 'The VelvetReel';
    $title['tagline'] = '';
    
    return $title;
}

add_filter('document_title_separator', 'vr_talent_title_separator');
function vr_talent_title_separator($sep) {
    return '|';
}

/**
 * Register missing Elementor dependency scripts.
 *
 * Elementor's v2 editor loader (Editor_V2_Loader) does NOT register
 * "elementor-v2-editor-controls", "elementor-v2-editor-editing-panel",
 * and "elementor-v2-editor-props" because they are absent from the
 * LIBS and EXTENSIONS package lists. However, both Elementor core v2
 * packages and the "extended" variants from Pro Elements / Elementor Pro
 * declare these as dependencies. Without them, WordPress triggers
 * _doing_it_wrong() during dependency resolution.
 *
 * Additionally, Elementor's WebCli module (priority 5 on wp_enqueue_scripts)
 * registers "elementor-web-cli" with a dependency on "elementor-vendors-redux",
 * but Common App registers "elementor-vendors-redux" at priority 9 — after
 * WebCli's registration. This race condition means "elementor-vendors-redux"
 * is missing when dependency resolution runs.
 *
 * CRITICAL: Elementor's Editor::enqueue_scripts() resets the global
 * $wp_scripts (new WP_Scripts) at wp_enqueue_scripts priority 999999.
 * Hooks on admin_enqueue_scripts / wp_enqueue_scripts run BEFORE the reset
 * and their registrations are wiped. We must hook on Elementor's own
 * editor hooks, which fire AFTER the reset.
 */
function ensure_elementor_dependencies() {
    $missing = [
        'elementor-v2-editor-controls'       => [],
        'elementor-v2-editor-editing-panel'  => [],
        'elementor-v2-editor-props'          => [],
        'elementor-vendors-redux'            => ['react', 'react-dom'],
        'imagesLoaded'                       => ['jquery'],
        'jquery-chosen'                      => ['jquery'],
        'jet-plugins'                        => ['jquery'],
    ];

    foreach ($missing as $handle => $deps) {
        if (!wp_script_is($handle, 'registered')) {
            wp_register_script(
                $handle,
                get_stylesheet_directory_uri() . '/js/elementor-v2-placeholder.js',
                $deps,
                '6.9.1',
                true
            );
        }
    }

    // Alias imagesLoaded to WP core imagesloaded script if registered
    if (wp_script_is('imagesloaded', 'registered') && !wp_script_is('imagesLoaded', 'registered')) {
        $wp_scripts = wp_scripts();
        if (isset($wp_scripts->registered['imagesloaded'])) {
            $src = $wp_scripts->registered['imagesloaded']->src;
            $deps = $wp_scripts->registered['imagesloaded']->deps;
            $ver = $wp_scripts->registered['imagesloaded']->ver;
            wp_register_script('imagesLoaded', $src, $deps, $ver, true);
        }
    }

    // Ensure elementor-vendors-redux points to the real Elementor JS if available.
    if (defined('ELEMENTOR_ASSETS_URL')) {
        $real_js = ELEMENTOR_ASSETS_URL . 'js/vendors-redux.min.js';
        if (wp_script_is('elementor-vendors-redux', 'registered')) {
            wp_deregister_script('elementor-vendors-redux');
        }
        wp_register_script(
            'elementor-vendors-redux',
            $real_js,
            ['react', 'react-dom'],
            defined('ELEMENTOR_VERSION') ? ELEMENTOR_VERSION : '6.9.1',
            true
        );
    }
}
// Fires after Elementor resets $wp_scripts and registers its own v2 scripts,
// but before it enqueues them. This ensures the three base handles exist
// when Pro Elements enqueues the "-extended" variants and when all_deps()
// resolves the dependency tree.
add_action('elementor/editor/before_enqueue_scripts', 'ensure_elementor_dependencies', 1);
add_action('elementor/editor/v2/scripts/register', 'ensure_elementor_dependencies', 1);
// Fallbacks for non-editor contexts
add_action('admin_enqueue_scripts', 'ensure_elementor_dependencies', 5);
add_action('wp_enqueue_scripts', 'ensure_elementor_dependencies', 5);

/**
 * Add custom CSS to hide page header on membership-login page
 */
function hide_membership_login_header() {
    if (is_page('membership-login')) {
        echo '<style>.page-header { display: none !important; }</style>';
    }
}
add_action('wp_head', 'hide_membership_login_header');

/**
 * Flush rewrite rules on theme activation
 */
function hello_elementor_child_flush_rewrite_rules() {
    // Always flush rewrite rules on theme activation
    flush_rewrite_rules();
    update_option('hello_elementor_child_flushed_rewrite_rules', true);
}
add_action('after_switch_theme', 'hello_elementor_child_flush_rewrite_rules');



/**
 * Load all modular functionality files
 */

// Theme setup and basic configurations
require_once get_stylesheet_directory() . '/includes/theme-setup.php';

// Script and style enqueuing
require_once get_stylesheet_directory() . '/includes/scripts-styles.php';

// Custom post types and taxonomies
require_once get_stylesheet_directory() . '/includes/custom-post-types.php';

// Custom meta fields registration
require_once get_stylesheet_directory() . '/includes/meta-fields.php';

// Elementor integration
require_once get_stylesheet_directory() . '/includes/elementor-integration.php';

// Talent submission and update forms
require_once get_stylesheet_directory() . '/includes/talent-forms.php';

// Admin meta boxes for talent posts
require_once get_stylesheet_directory() . '/includes/talent-meta-boxes.php';

// Admin meta boxes for advertisement posts
require_once get_stylesheet_directory() . '/includes/advertisement-meta-boxes.php';

// All shortcodes
require_once get_stylesheet_directory() . '/includes/shortcodes.php';

// User management functionality
require_once get_stylesheet_directory() . '/includes/user-management.php';

// Access control and restrictions
require_once get_stylesheet_directory() . '/includes/access-control.php';

// AJAX request handlers
require_once get_stylesheet_directory() . '/includes/ajax-handlers.php';

// Third-party event integration
require_once get_stylesheet_directory() . '/includes/event-integration.php';

// Draft views functionality
require_once get_stylesheet_directory() . '/includes/draft-views.php';

// Role fields loader functionality
require_once get_stylesheet_directory() . '/includes/role-fields-consistent.php';

// Security hardening and newsletter AJAX handler
require_once get_stylesheet_directory() . '/includes/security-hardening.php';

// Custom high-performance video hero slider
require_once get_stylesheet_directory() . '/includes/hero-slider.php';

// Custom high-performance talent grid component
require_once get_stylesheet_directory() . '/includes/talent-grid.php';

// Custom Testimonials CPT
require_once get_stylesheet_directory() . '/includes/testimonials-cpt.php';

// Custom premium testimonials slider component
require_once get_stylesheet_directory() . '/includes/testimonials-slider.php';

// Custom rotating title animation component
require_once get_stylesheet_directory() . '/includes/rotating-title.php';

// Classified notifications and targeted email engine
require_once get_stylesheet_directory() . '/includes/classified-notifications.php';

// Contact us form handler, anti-spam and auto-acknowledgement engine
require_once get_stylesheet_directory() . '/includes/contact-form-handler.php';

// New-member welcome & staged portfolio/subscription reminders automation
require_once get_stylesheet_directory() . '/includes/member-onboarding-automation.php';

// Email Automation Sandbox & Live Preview Tool
require_once get_stylesheet_directory() . '/includes/admin-email-preview.php';

/**
 * Create Advertisement Submission page on theme activation
 */
function create_advertisement_submission_page() {
    // Check if page already exists
    $page_exists = get_page_by_path('submit-advertisement');
    
    if (!$page_exists) {
        // Create the page
        $page_data = array(
            'post_title'    => 'Submit Advertisement',
            'post_name'     => 'submit-advertisement',
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'page_template' => 'template-advertisement-submission.php'
        );
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id && !is_wp_error($page_id)) {
            // Set the page template
            update_post_meta($page_id, '_wp_page_template', 'template-advertisement-submission.php');
        }
    }
}
add_action('after_switch_theme', 'create_advertisement_submission_page');


/**
 * Create Classified Pricing page on theme activation
 */
function create_classified_pricing_page() {
    // Check if page already exists
    $page_exists = get_page_by_path('classified');
    
    if (!$page_exists) {
        // Create the page
        $page_data = array(
            'post_title'    => 'Classified Pricing',
            'post_name'     => 'classified',
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'page_template' => 'page-classified.php'
        );
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id && !is_wp_error($page_id)) {
            // Set the page template
            update_post_meta($page_id, '_wp_page_template', 'page-classified.php');
        }
    }
}
add_action('after_switch_theme', 'create_classified_pricing_page');


/**
 * Create Verify Email page if not exists
 */
function create_verify_email_page() {
    $page_exists = get_page_by_path('verify-email');
    if (!$page_exists) {
        $page_data = array(
            'post_title'    => 'Verify Email',
            'post_name'     => 'verify-email',
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'page_template' => 'page-verify-email.php'
        );
        $page_id = wp_insert_post($page_data);
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-verify-email.php');
        }
    } else {
        update_post_meta($page_exists->ID, '_wp_page_template', 'page-verify-email.php');
    }
}
add_action('after_switch_theme', 'create_verify_email_page');
add_action('init', 'create_verify_email_page');

/**
 * Flush rewrite rules after theme activation to ensure custom URLs work
 */
function classified_page_flush_rewrite_rules() {
    create_classified_pricing_page();
    create_advertisement_submission_page();
    create_verify_email_page();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'classified_page_flush_rewrite_rules');


/**
 * Add rewrite rules for classified-create URL
 */
function add_classified_create_rewrite_rule() {
    add_rewrite_rule(
        '^classified-create/?$',
        'index.php?pagename=classified',
        'top'
    );
}
add_action('init', 'add_classified_create_rewrite_rule');

/**
 * Add rewrite rules for edit-talent-profile URL
 */
function add_edit_talent_profile_rewrite_rule() {
    add_rewrite_rule(
        '^edit-talent-profile/?$',
        'index.php?pagename=edit-talent-profile',
        'top'
    );
}
add_action('init', 'add_edit_talent_profile_rewrite_rule');

/**
 * Add rewrite rules for public talent profile URLs
 */
function add_public_talent_profile_rewrite_rule() {
    add_rewrite_rule(
        '^p/([a-zA-Z0-9]+)/?$',
        'index.php?public_talent_token=$matches[1]',
        'top'
    );
    
    // Add query var
    add_rewrite_tag('%public_talent_token%', '([^&]+)');
}
add_action('init', 'add_public_talent_profile_rewrite_rule');

/**
 * Handle edit-talent-profile URL redirect to use the talent edit template
 */
function handle_edit_talent_profile_redirect() {
    // Check if we're on the edit-talent-profile endpoint
    $requested_url = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($requested_url, '/edit-talent-profile') === 0) {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            // Redirect to login page
            wp_redirect(home_url('/sign-in/'));
            exit;
        }

        // Get the talent ID from the query parameter
        $talent_id = isset($_GET['talent_id']) ? intval($_GET['talent_id']) : 0;
        
        if ($talent_id) {
            // Check if the current user is the author of this talent profile
            $talent_post = get_post($talent_id);
            if ($talent_post && $talent_post->post_type === 'talent') {
                if (get_post_field('post_author', $talent_id) != get_current_user_id()) {
                    // User doesn't have permission to edit this profile
                    wp_die('You do not have permission to edit this profile.');
                }
            } else {
                // Talent post doesn't exist or isn't of correct type
                wp_redirect(get_post_type_archive_link('talent'));
                exit;
            }
        }
        
        // Include the template file directly
        include get_stylesheet_directory() . '/template-talent-edit.php';
        exit;
    }
}
add_action('template_redirect', 'handle_edit_talent_profile_redirect');

/**
 * Handle public talent profile URL redirect to use the public profile template
 */
function handle_public_talent_profile_redirect() {
    $public_token = get_query_var('public_talent_token');
    
    if (!empty($public_token)) {
        // Include the public profile template
        include get_stylesheet_directory() . '/template-public-profile.php';
        exit;
    }
}
add_action('template_redirect', 'handle_public_talent_profile_redirect');

/**
 * Handle classified-create URL redirect based on user subscription
 */
function handle_classified_create_redirect() {
    // Check if the current URL is /classified-create/
    global $wp_query;
    
    // Check if we're on the classified-create endpoint
    if (isset($wp_query->query_vars['pagename']) && $wp_query->query_vars['pagename'] === 'classified') {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            // Redirect to login page
            wp_redirect(home_url('/membership-login/'));
            exit;
        }

        // Direct all logged in users to the advertisement submission page
        wp_redirect(home_url('/submit-advertisement/'));
        exit;
    }
}
add_action('template_redirect', 'handle_classified_create_redirect');

/**
 * Replace hardcoded URLs with dynamic URLs in content
 */
function replace_hardcoded_urls_with_dynamic($content) {
    // Only process if content contains potential hardcoded URLs
    if (strpos($content, 'http://') !== false || strpos($content, 'https://') !== false) {
        // Replace hardcoded base URLs with dynamic home_url for membership-related paths
        $site_url = home_url();
        
        // Pattern to match URLs with membership-related paths
        $pattern = '/(href|src)=(["\'])(https?:\/\/[^\s"\'\/]+\/)(membership-join|membership-login|sign-in|sign-up|membership-register|register)([^\s"\'<>]*)\2/i';
        $replacement = '$1=$2' . $site_url . '/$4$5$2';
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    return $content;
}

// Apply the filter to post content
add_filter('the_content', 'replace_hardcoded_urls_with_dynamic');

// Apply the filter to widget content (which may include Elementor widgets)
add_filter('widget_text', 'replace_hardcoded_urls_with_dynamic');

// Apply the filter to nav menu items
add_filter('wp_nav_menu', 'replace_hardcoded_urls_with_dynamic');


/**
 * Add custom columns to talent post type admin screen
 */
function add_talent_admin_columns($columns) {
    // Add 'Author Email' and 'Approval Payment' columns after the 'title' column
    $new_columns = array(); 
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['author_email'] = 'Author Email';
            $new_columns['approval_payment'] = 'Approval Payment';
        }
    }
    return $new_columns;
}
add_filter('manage_talent_posts_columns', 'add_talent_admin_columns');

/**
 * Populate the custom column with payment information
 */
function populate_talent_admin_columns($column, $post_id) {
    if ($column === 'author_email') {
        // Get the author ID for this post
        $author_id = get_post_field('post_author', $post_id);
        
        // Get the author's email
        $author_email = get_the_author_meta('user_email', $author_id);
        
        // Display the author's email
        if (!empty($author_email)) {
            echo '<span style="font-size: 12px;">' . esc_html($author_email) . '</span>';
        } else {
            echo '<span style="color: #999; font-size: 12px;">No email</span>';
        }
    } elseif ($column === 'approval_payment') {
        // Check if user has paid for faster approval using plan ID 6958
        $payment_info = get_post_meta($post_id, '_talent_approval_payment_info', true);
        $has_paid = get_post_meta($post_id, '_talent_has_paid_approval', true);
        
        if ($has_paid) {
            // Display payment information
            $payment_date = get_post_meta($post_id, '_talent_approval_payment_date', true);
            $payment_amount = get_post_meta($post_id, '_talent_approval_payment_amount', true);
            
            if (!empty($payment_info)) {
                echo '<span style="color: green; font-weight: bold;">Paid</span><br>';
                echo '<small>Date: ' . esc_html($payment_date) . '</small><br>';
                if (!empty($payment_amount)) {
                    echo '<small style="color: #00a32a; font-weight: 500;">Amount: $' . esc_html($payment_amount) . '</small>';
                } else {
                    echo '<small>Amount: $5.00</small>'; // Default amount based on your payment data
                }
            } else {
                if (!empty($payment_amount)) {
                    echo '<span style="color: green; font-weight: bold;">Paid: $' . esc_html($payment_amount) . '</span>';
                } else {
                    echo '<span style="color: green; font-weight: bold;">Paid: $5.00</span>'; // Default amount
                }
            }
        } else {
            echo '<span style="color: #999;">Not Paid</span>';
        }
    }
}
add_action('manage_talent_posts_custom_column', 'populate_talent_admin_columns', 10, 2);

/**
 * Make the approval payment column sortable
 */
function make_talent_admin_columns_sortable($columns) {
    $columns['approval_payment'] = 'approval_payment';
    return $columns;
}
add_filter('manage_edit-talent_sortable_columns', 'make_talent_admin_columns_sortable');

/**
 * Save payment information when a talent post is updated
 * This function can be called when payment is processed to record payment details
 */
function record_talent_approval_payment($post_id, $payment_data = array()) {
    if (get_post_type($post_id) !== 'talent') {
        return;
    }
    
    // Mark that user has paid for approval
    update_post_meta($post_id, '_talent_has_paid_approval', true);
    
    // Record payment information
    if (isset($payment_data['date'])) {
        update_post_meta($post_id, '_talent_approval_payment_date', $payment_data['date']);
    }
    
    if (isset($payment_data['amount'])) {
        update_post_meta($post_id, '_talent_approval_payment_amount', $payment_data['amount']);
    }
    
    if (isset($payment_data['info'])) {
        update_post_meta($post_id, '_talent_approval_payment_info', $payment_data['info']);
    }
}


/**
 * Alternative function to manually trigger payment recording
 * This can be used if SWPM hooks don't work as expected
 */
function trigger_talent_approval_payment_check($user_id) {
    // Check if the user has membership level 7 (based on your payment data)
    if (class_exists('SwpmMemberUtils')) {
        $user_level = SwpmMemberUtils::get_member_field('membership_level', $user_id);
        
        if ($user_level == '7') { // Level 7 is the approval plan based on your payment data
            // Find talent post associated with this user
            $talent_posts = get_posts(array(
                'post_type' => 'talent',
                'author' => $user_id,
                'posts_per_page' => 1,
                'post_status' => array('pending', 'draft', 'publish'),
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            if (!empty($talent_posts)) {
                $post_id = $talent_posts[0]->ID;
                
                // Check if payment info already exists to avoid duplicates
                $existing_payment_info = get_post_meta($post_id, '_talent_approval_payment_info', true);
                
                if (empty($existing_payment_info)) {
                    // Record payment information
                    $payment_record = array(
                        'date' => current_time('mysql'),
                        'amount' => '5.00', // Default amount based on your payment data
                        'info' => 'Plan ID 7 - Faster Approval (Manually Verified)'
                    );
                    
                    record_talent_approval_payment($post_id, $payment_record);
                    error_log('Manually recorded talent approval payment for user ID: ' . $user_id . ', post ID: ' . $post_id);
                }
            }
        }
    }
}

/**
 * Function to manually update a talent post's payment status with specific details
 */
function manually_update_talent_payment_status($post_id, $amount = '', $payment_info = '') {
    if (get_post_type($post_id) !== 'talent') {
        return false;
    }
    
    $payment_record = array(
        'date' => current_time('mysql'),
        'amount' => $amount,
        'info' => $payment_info
    );
    
    record_talent_approval_payment($post_id, $payment_record);
    
    return true;
}


/**
 * Add a meta box to talent posts for admins to manually mark as paid for approval
 */
function add_talent_approval_payment_meta_box() {
    add_meta_box(
        'talent-approval-payment',
        'Approval Payment Status',
        'render_talent_approval_payment_meta_box',
        'talent',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_talent_approval_payment_meta_box');

/**
 * Render the approval payment meta box
 */
function render_talent_approval_payment_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('talent_approval_payment_meta_box', 'talent_approval_payment_nonce');
    
    // Get current payment status
    $has_paid = get_post_meta($post->ID, '_talent_has_paid_approval', true);
    $payment_date = get_post_meta($post->ID, '_talent_approval_payment_date', true);
    $payment_amount = get_post_meta($post->ID, '_talent_approval_payment_amount', true);
    $payment_info = get_post_meta($post->ID, '_talent_approval_payment_info', true);
    
    echo '<div style="margin-bottom: 10px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Mark as Paid for Approval:</label>
            <input type="checkbox" name="talent_has_paid_approval" value="1" ' . checked($has_paid, true, false) . '> Yes, this user paid for faster approval
        </div>';
    
    echo '<div style="margin-bottom: 10px;">
            <label style="display: block; margin-bottom: 5px;">Payment Date:</label>
            <input type="text" name="talent_approval_payment_date" value="' . esc_attr($payment_date) . '" style="width: 100%;" placeholder="YYYY-MM-DD">
        </div>';
    
    echo '<div style="margin-bottom: 10px;">
            <label style="display: block; margin-bottom: 5px;">Payment Amount:</label>
            <input type="text" name="talent_approval_payment_amount" value="' . esc_attr($payment_amount) . '" style="width: 100%;" placeholder="Amount">
        </div>';
    
    echo '<div style="margin-bottom: 10px;">
            <label style="display: block; margin-bottom: 5px;">Payment Info:</label>
            <input type="text" name="talent_approval_payment_info" value="' . esc_attr($payment_info) . '" style="width: 100%;" placeholder="Info">
        </div>';
}

/**
 * Save the approval payment meta box data
 */
function save_talent_approval_payment_meta_box($post_id) {
    // Verify nonce
    if (!isset($_POST['talent_approval_payment_nonce']) || !wp_verify_nonce($_POST['talent_approval_payment_nonce'], 'talent_approval_payment_meta_box')) {
        return;
    }
    
    // Check if user has permission to edit post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Save payment status
    if (isset($_POST['talent_has_paid_approval'])) {
        update_post_meta($post_id, '_talent_has_paid_approval', true);
    } else {
        delete_post_meta($post_id, '_talent_has_paid_approval');
    }
    
    // Save payment date
    if (isset($_POST['talent_approval_payment_date']) && !empty($_POST['talent_approval_payment_date'])) {
        update_post_meta($post_id, '_talent_approval_payment_date', sanitize_text_field($_POST['talent_approval_payment_date']));
    } else {
        delete_post_meta($post_id, '_talent_approval_payment_date');
    }
    
    // Save payment amount
    if (isset($_POST['talent_approval_payment_amount']) && !empty($_POST['talent_approval_payment_amount'])) {
        update_post_meta($post_id, '_talent_approval_payment_amount', sanitize_text_field($_POST['talent_approval_payment_amount']));
    } else {
        delete_post_meta($post_id, '_talent_approval_payment_amount');
    }
    
    // Save payment info
    if (isset($_POST['talent_approval_payment_info']) && !empty($_POST['talent_approval_payment_info'])) {
        update_post_meta($post_id, '_talent_approval_payment_info', sanitize_text_field($_POST['talent_approval_payment_info']));
    } else {
        delete_post_meta($post_id, '_talent_approval_payment_info');
    }
}
add_action('save_post', 'save_talent_approval_payment_meta_box');


/**
 * Add image protection headers to prevent direct downloading
 */
function add_image_protection_headers($headers, $attachment_id) {
    // Check if this is an image attachment
    if (wp_attachment_is_image($attachment_id)) {
        // Add headers to prevent right-click and direct access
        $headers['X-Content-Type-Options'] = 'nosniff';
        $headers['X-Frame-Options'] = 'DENY';
        $headers['X-XSS-Protection'] = '1; mode=block';
        
        // Add Referrer-Policy to prevent access from external sites
        $headers['Referrer-Policy'] = 'same-origin';
    }
    return $headers;
}
add_filter('wp_get_attachment_image_attributes', 'add_image_protection_headers', 10, 2);


/**
 * Protect media uploads by restricting direct access based on referrer and user capability
 */
function protect_media_files() {
    if (is_admin() || current_user_can('manage_options')) {
        return; // Allow access for admins
    }
    
    // Check if this is a request for an attachment
    if (is_attachment()) {
        // Check referrer to ensure it's coming from our site
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $site_url = home_url();
        
        // If referrer is not from our site, redirect or deny access
        if ($referrer && strpos($referrer, $site_url) !== 0) {
            // Redirect to homepage or show a message
            wp_redirect(home_url());
            exit;
        }
    }
}
add_action('template_redirect', 'protect_media_files');


/**
 * Add JavaScript to all pages to prevent image downloading
 */
function add_image_protection_script() {
    if (!is_admin()) {
        ?>
        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Disable right-click on images
            document.addEventListener('contextmenu', function(e) {
                if (e.target.tagName === 'IMG') {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Disable drag-and-drop for images
            document.addEventListener('dragstart', function(e) {
                if (e.target.tagName === 'IMG') {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Disable image selection
            document.addEventListener('selectstart', function(e) {
                if (e.target.tagName === 'IMG') {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Disable keyboard shortcuts that might affect images
            document.addEventListener('keydown', function(e) {
                // Disable F12 (Developer Tools), Ctrl+Shift+I (Inspect), Ctrl+U (View Source)
                if (
                    e.keyCode === 123 || // F12
                    (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
                    (e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
                    (e.ctrlKey && e.keyCode === 85) || // Ctrl+U
                    (e.ctrlKey && e.keyCode === 83) // Ctrl+S
                ) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Additional protection: Prevent copying images
            document.addEventListener('copy', function(e) {
                const selection = window.getSelection();
                if (selection) {
                    const range = selection.getRangeAt(0);
                    const img = range.startContainer.parentElement.querySelector('img');
                    if (img) {
                        e.preventDefault();
                    }
                }
            });
            
            // Protect all images on the page with attributes
            const allImages = document.querySelectorAll('img');
            allImages.forEach(img => {
                img.setAttribute('oncontextmenu', 'return false;');
                img.setAttribute('ondragstart', 'return false;');
                img.setAttribute('onselectstart', 'return false;');
                img.style.userSelect = 'none';
                img.style.webkitUserSelect = 'none';
                img.style.mozUserSelect = 'none';
                img.style.msUserSelect = 'none';
                img.style.webkitTouchCallout = 'none';
                img.style.webkitUserDrag = 'none';
            });
        });
        </script>
        <?php
    }
}
add_action('wp_head', 'add_image_protection_script');


/**
 * Add CSS to make images harder to download
 */
function add_image_protection_css() {
    if (!is_admin()) {
        ?>
        <style type="text/css">
        /* Prevent image selection and context menu */
        img {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
            -webkit-user-drag: none;
            pointer-events: auto;
        }
        
        /* Additional protection for image containers */
        .wp-block-image img,
        .gallery img,
        .portfolio-item img {
            position: relative;
        }
        
        .wp-block-image img::before,
        .gallery img::before,
        .portfolio-item img::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: transparent;
            z-index: 1;
        }
        </style>
        <?php
    }
}
add_action('wp_head', 'add_image_protection_css');


/**
 * Modify image output to add protection attributes
 */
function modify_image_output($html, $attachment_id = null, $size = null, $icon = null, $attr = null) {
    if ($attachment_id && wp_attachment_is_image($attachment_id)) {
        // Add attributes to make images harder to download
        $protection_attrs = ' oncontextmenu="return false;" ondragstart="return false;" onselectstart="return false;"';
        
        // Find where the opening img tag ends and add our attributes
        if (strpos($html, '<img') !== false && !strpos($html, 'oncontextmenu')) {
            $html = str_replace('<img', '<img' . $protection_attrs, $html);
        }
    }
    return $html;
}
add_filter('wp_get_attachment_image', 'modify_image_output', 20, 5);


/**
 * Protect uploaded images by adding .htaccess rules (conceptual - would need actual .htaccess modification)
 * Note: This function adds a conceptual approach - actual implementation would require filesystem access
 */
function add_htaccess_image_protection() {
    // This is a conceptual function - actual implementation would need filesystem write access
    // which is often restricted on shared hosting
    
    // The idea would be to add rules to .htaccess to prevent hotlinking and direct access
    // RewriteEngine On
    // RewriteCond %{HTTP_REFERER} !^$
    // RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?yourdomain.com [NC]
    // RewriteRule \.(jpg|jpeg|png|gif)$ - [NC,F,L]
}

/**
 * Additional server-side image protection by filtering image URLs
 */
function secure_image_urls($url, $attachment_id) {
    // Add query parameters to image URLs to make them harder to access directly
    if (wp_attachment_is_image($attachment_id)) {
        $parsed_url = parse_url($url);
        if (isset($parsed_url['path'])) {
            $extension = strtolower(pathinfo($parsed_url['path'], PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                // Add a security token as a query parameter
                $token = md5($attachment_id . $GLOBALS['wp']->request . wp_salt());
                $url = add_query_arg('token', substr($token, 0, 8), $url);
            }
        }
    }
    return $url;
}
add_filter('wp_get_attachment_url', 'secure_image_urls', 10, 2);

/**
 * Validate image requests with tokens
 */
function validate_image_requests() {
    if (isset($_GET['token']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $_SERVER['REQUEST_URI'])) {
        // Extract attachment ID from URL if possible
        $attachment_id = attachment_url_to_postid($_SERVER['REQUEST_URI']);
        if ($attachment_id) {
            $expected_token = substr(md5($attachment_id . $_SERVER['HTTP_REFERER'] . wp_salt()), 0, 8);
            if ($_GET['token'] !== $expected_token) {
                // Invalid token, redirect to home or show error
                wp_redirect(home_url());
                exit;
            }
        }
    }
}
add_action('init', 'validate_image_requests');

/**
 * Handle Stripe payment completion for talent approval
 * This function checks if the current page load includes payment completion parameters
 * and updates the talent post accordingly
 */
function handle_stripe_talent_approval_payment() {
    // Check if we're on the talent archive page and have session ID parameter
    if (is_post_type_archive('talent') && isset($_GET['session_id'])) {
        $session_id = sanitize_text_field($_GET['session_id']);
        $current_user_id = get_current_user_id();
        if (!$current_user_id) {
            return;
        }

        // We need to verify the payment status with Stripe
        // For now, let's just store the session ID temporarily
        update_user_meta($current_user_id, '_last_stripe_session_id', $session_id);
        
        // Find the user's talent post
        $talent_posts = get_posts(array(
            'post_type' => 'talent',
            'author' => $current_user_id,
            'posts_per_page' => 1,
            'post_status' => array('pending', 'draft', 'publish'),
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        if (!empty($talent_posts)) {
            $post_id = $talent_posts[0]->ID;
            
            // Update the post with the session ID
            update_post_meta($post_id, '_stripe_session_id', $session_id);
            
            // Check if payment info already exists to avoid duplicates
            $existing_payment_info = get_post_meta($post_id, '_talent_has_paid_approval', true);
            
            // If we don't have payment confirmation yet, we can mark that payment was initiated
            if (empty($existing_payment_info)) {
                // For now, just update the session ID - actual payment confirmation will happen via webhook
                update_post_meta($post_id, '_stripe_payment_initiated', true);
                update_post_meta($post_id, '_stripe_payment_initiated_date', current_time('mysql'));
            }
        }
    }
}

// Hook into template redirect to handle payment completion
add_action('template_redirect', 'handle_stripe_talent_approval_payment');

/**
 * Function to verify Stripe payment status using the session ID
 * This would typically be called by a webhook, but for now we'll simulate it
 */
function verify_stripe_payment_status($session_id, $post_id) {
    // In a real implementation, you would call the Stripe API to verify the payment status
    // For now, we'll just return true to simulate a successful payment
    // In production, you would use the Stripe PHP library to verify the session
    
    // $stripe = new \Stripe\StripeClient('your-secret-key');
    // $session = $stripe->checkout->sessions->retrieve($session_id);
    // return $session->payment_status === 'paid';
    
    // For now, simulate successful payment
    return true;
}

/**
 * Process Stripe payment confirmation when user returns from Stripe
 */
function process_stripe_payment_confirmation() {
    if (is_post_type_archive('talent') && isset($_GET['session_id'])) {
        $session_id = sanitize_text_field($_GET['session_id']);
        $current_user_id = get_current_user_id();
        
        if (!$current_user_id) {
            return;
        }
        
        // Find the user's talent post
        $talent_posts = get_posts(array(
            'post_type' => 'talent',
            'author' => $current_user_id,
            'posts_per_page' => 1,
            'post_status' => array('pending', 'draft', 'publish'),
            'orderby' => 'date',
            'order' => 'DESC'
        ));

        if (!empty($talent_posts)) {
            $post_id = $talent_posts[0]->ID;
            
            // Verify payment status (in real implementation, this would call Stripe API)
            if (verify_stripe_payment_status($session_id, $post_id)) {
                // Check if payment info already exists to avoid duplicates
                $existing_payment_info = get_post_meta($post_id, '_talent_has_paid_approval', true);
                
                if (empty($existing_payment_info)) {
                    // Record payment information
                    $payment_record = array(
                        'date' => current_time('mysql'),
                        'amount' => '5.00', // Amount based on your Stripe configuration
                        'info' => 'Stripe - Portfolio Approval Payment (Session: ' . $session_id . ')'
                    );
                    
                    record_talent_approval_payment($post_id, $payment_record);
                    
                    // Remove the session_id parameter from URL to avoid re-processing
                    $redirect_url = remove_query_arg('session_id', $_SERVER['REQUEST_URI']);
                    wp_safe_redirect($redirect_url);
                    exit;
                }
            }
        }
    }
}

// Hook to process payment confirmation when user returns from Stripe
add_action('template_redirect', 'process_stripe_payment_confirmation');

/**
 * Register REST API endpoint for Stripe webhook
 */
function register_stripe_webhook_endpoint() {
    register_rest_route('stripe', '/webhook', array(
        'methods' => 'POST',
        'callback' => 'handle_stripe_webhook',
        'permission_callback' => '__return_true'
    ));
}
add_action('rest_api_init', 'register_stripe_webhook_endpoint');

/**
 * Handle Stripe webhook
 */
function handle_stripe_webhook($request) {
    $input = file_get_contents('php://input');
    $event = json_decode($input, true);
    
    if ($event && isset($event['type']) && $event['type'] === 'checkout.session.completed') {
        $session = $event['data']['object'];
        $session_id = $session['id'];
        
        // Look up the talent post associated with this session
        $posts = get_posts(array(
            'post_type' => 'talent',
            'meta_query' => array(
                array(
                    'key' => '_stripe_session_id',
                    'value' => $session_id,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (!empty($posts)) {
            $post_id = $posts[0]->ID;
            $existing_payment_info = get_post_meta($post_id, '_talent_has_paid_approval', true);
            
            if (empty($existing_payment_info)) {
                // Record payment information
                $payment_record = array(
                    'date' => current_time('mysql'),
                    'amount' => $session['amount_total'] / 100, // Convert from cents to dollars
                    'info' => 'Stripe Webhook - Portfolio Approval Payment (Session: ' . $session_id . ')'
                );
                
                record_talent_approval_payment($post_id, $payment_record);
                
                // Log the successful payment
                error_log('Stripe webhook processed successfully for session: ' . $session_id . ', post ID: ' . $post_id);
                
                return new WP_REST_Response(array('status' => 'success'), 200);
            } else {
                // Payment already recorded
                error_log('Payment already recorded for session: ' . $session_id);
                return new WP_REST_Response(array('status' => 'already_processed'), 200);
            }
        } else {
            // No talent post found for this session
            error_log('No talent post found for session: ' . $session_id);
            return new WP_REST_Response(array('status' => 'no_post_found'), 200);
        }
    }
    
    return new WP_REST_Response(array('status' => 'ignored'), 200);
}

/**
 * AJAX handler to save talent form progress
 */
function wp_ajax_save_talent_progress()
{
    try {
        // Verify nonce - accept both talent_submission (create) and talent_update (edit) nonces
        $nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
        $nonce_valid = wp_verify_nonce($nonce, 'talent_submission') || wp_verify_nonce($nonce, 'talent_update');
    
    if (!$nonce_valid) {
        error_log('AJAX save_talent_progress: Nonce verification failed. POST: ' . print_r($_POST, true));
        wp_send_json_error('Security check failed. Please refresh the page and try again.');
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
    }
    
    $user_id = get_current_user_id();
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

    // Collect form data
    $form_data = isset($_POST['form_data']) ? $_POST['form_data'] : [];

    // Parse the serialized form data into an associative array
    $parsed_data = [];
    parse_str($form_data, $parsed_data);

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

    $existing_portfolio_ids = get_post_meta($post_id, '_talent_portfolio', true);
    $submitted_existing_order = isset($parsed_data['portfolio_existing_order']) ? $parsed_data['portfolio_existing_order'] : null;
    $portfolio_validation = velvet_reel_validate_portfolio_upload_groups($existing_portfolio_ids, $submitted_existing_order);
    if (is_wp_error($portfolio_validation)) {
        wp_send_json_error(array(
            'message' => 'Portfolio validation failed.',
            'errors' => $portfolio_validation->get_error_data(),
        ));
    }

    // --- Handle File Upload (Profile Photo) ---
    $profile_photo_uploaded = false;
    if (!empty($_FILES['profilePhoto']['name'])) {
        error_log('Profile photo upload attempt: ' . print_r($_FILES['profilePhoto'], true));
        
        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }

        $current_thumbnail_id = (int) get_post_thumbnail_id($post_id);
        $duplicate_id = 0;
        if ($current_thumbnail_id > 0) {
            $duplicate_id = velvet_reel_find_duplicate_attachment(
                $_FILES['profilePhoto']['name'],
                $_FILES['profilePhoto']['size'],
                array($current_thumbnail_id)
            );
        }

        if ($duplicate_id > 0) {
            $attachment_id = $duplicate_id;
            $profile_photo_uploaded = true;
        } else {
            // Delete old thumbnail if it exists and is different
            if ($current_thumbnail_id > 0) {
                delete_post_thumbnail($post_id);
            }

            $attachment_id = velvet_reel_media_handle_upload('profilePhoto', $post_id);
            
            if (is_wp_error($attachment_id)) {
                error_log('Profile photo upload result: WP_Error: ' . $attachment_id->get_error_message());
            } else {
                error_log('Profile photo upload result: ' . $attachment_id);
            }
            if (is_wp_error($attachment_id)) {
                error_log('Profile photo upload WP_Error: ' . $attachment_id->get_error_message());
                wp_send_json_error('Profile Photo Upload Error: ' . $attachment_id->get_error_message());
            } elseif ($attachment_id > 0) {
                set_post_thumbnail($post_id, $attachment_id);
                $profile_photo_uploaded = true;
            }
        }
    }

    // --- Save Custom Fields (Meta Data) ---
    $meta_fields = [
        '_talent_state' => 'city',
        '_talent_country' => 'country',
        '_talent_email' => 'email',
        '_talent_phone' => 'phone',
        '_talent_country_code' => 'countryCode',
        '_talent_age_group' => 'ageGroup',
        '_talent_gender' => 'gender',
        '_talent_height' => 'height',
        '_talent_height_unit' => 'heightUnit',
        '_talent_weight' => 'weight',
        '_talent_weight_unit' => 'weightUnit',
        '_talent_complexion' => 'complexion',
        '_talent_bust_size' => 'bustSize',
        '_talent_hair_color' => 'hairColor',
        '_talent_dress_size' => 'dressSize',
        '_talent_shirt_size' => 'shirtSize',
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
        '_talent_youtube' => 'youtube',
        '_talent_tiktok' => 'tiktok',
        '_talent_website' => 'website',
        // Privacy settings
        '_talent_hide_email' => 'hideEmail',
        '_talent_hide_phone' => 'hidePhone',
        '_talent_hide_instagram' => 'hideInstagram',
        '_talent_hide_youtube' => 'hideYoutube',
        '_talent_hide_tiktok' => 'hideTiktok',
        '_talent_hide_website' => 'hideWebsite',
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
        $available_for = is_array($parsed_data['availableFor'])
            ? array_map('sanitize_text_field', $parsed_data['availableFor'])
            : [sanitize_text_field($parsed_data['availableFor'])];
        update_post_meta($post_id, '_talent_available_for', $available_for);
    }

    if (isset($parsed_data['designCategories'])) {
        $design_categories = is_array($parsed_data['designCategories'])
            ? array_map('sanitize_text_field', $parsed_data['designCategories'])
            : [sanitize_text_field($parsed_data['designCategories'])];
        update_post_meta($post_id, '_talent_designCategories', $design_categories);
    }

    // Handle Notable Works
    if (isset($parsed_data['workTitle']) && is_array($parsed_data['workTitle'])) {
        $notable_works = [];
        for ($i = 0; $i < count($parsed_data['workTitle']); $i++) {
            if (!empty($parsed_data['workTitle'][$i])) {
                $is_present = velvet_reel_repeater_checkbox_is_checked($parsed_data['workPresent'] ?? [], $i);
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

    velvet_reel_handle_portfolio_uploads(
        $post_id,
        array(
            'cover_choice' => isset($parsed_data['portfolio_cover_choice']) ? $parsed_data['portfolio_cover_choice'] : '',
            'existing_order' => $submitted_existing_order,
            'allow_thumbnail_override' => empty($_FILES['profilePhoto']['name']),
        )
    );
    
    // Handle Video Links
    if (isset($parsed_data['videoLinks'])) {
        if (is_array($parsed_data['videoLinks']) && !empty($parsed_data['videoLinks'])) {
            $video_links = [];
            $unique_urls = []; // Track unique URLs to prevent duplicates
            
            for ($i = 0; $i < count($parsed_data['videoLinks']); $i++) {
                if (!empty($parsed_data['videoLinks'][$i])) {
                    $url = esc_url_raw($parsed_data['videoLinks'][$i]);
                    // Only add the URL if it's not already in our unique list
                    if (!in_array($url, $unique_urls)) {
                        $unique_urls[] = $url;
                        $video_links[] = [
                            'url' => $url,
                            'title' => ''
                        ];
                    }
                }
            }
            update_post_meta($post_id, '_talent_video_links', $video_links);
        } else {
            // If videoLinks is set but empty or not an array, remove all video links
            delete_post_meta($post_id, '_talent_video_links');
        }
    }

    wp_send_json_success(['post_id' => $post_id]);
    } catch (\Throwable $e) {
        error_log('Fatal Error in wp_ajax_save_talent_progress: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        wp_send_json_error('A critical error occurred: ' . $e->getMessage());
    }
}
add_action('wp_ajax_save_talent_progress', 'wp_ajax_save_talent_progress');
add_action('wp_ajax_nopriv_save_talent_progress', 'wp_ajax_save_talent_progress');

/**
 * AJAX handler to refresh nonce for form submission
 * This helps prevent stale nonce errors when the form is submitted after
 * the page has been open for a long time
 */
function wp_ajax_get_refreshed_nonce() {
    $nonce_action = isset($_POST['nonce_action']) ? sanitize_text_field($_POST['nonce_action']) : 'talent_submission';
    
    // Validate the nonce action
    $allowed_actions = ['talent_submission', 'talent_update'];
    if (!in_array($nonce_action, $allowed_actions)) {
        wp_send_json_error('Invalid nonce action');
    }
    
    // Generate a fresh nonce
    $nonce = wp_create_nonce($nonce_action);
    
    wp_send_json_success(['nonce' => $nonce]);
}
add_action('wp_ajax_get_refreshed_nonce', 'wp_ajax_get_refreshed_nonce');

/**
 * Shortcode to display dynamic login/signup or username
 * Shows "Login/Sign Up" when logged out, username when logged in
 * Usage: [login_status] or [login_status login_text="Login" signup_text="Sign Up"]
 */
function display_login_status_shortcode($atts) {
    // Get shortcode attributes
    $atts = shortcode_atts(array(
        'login_text' => 'Login',
        'signup_text' => 'Sign Up',
        'separator' => ' / ',
        'format' => 'full', // Options: 'full', 'first', 'last', 'display'
        'prefix' => '',
        'suffix' => '',
        'default' => 'User',
        'login_url' => home_url('/membership-login/'),
        'profile_url' => home_url('/membership-login/'),
        'signup_url' => home_url('/membership-join/membership-registration/')
    ), $atts);
    
    // Check if user is logged in
    if (is_user_logged_in()) {
        // User is logged in - show username with link to profile
        $current_user = wp_get_current_user();
        
        // Get user name based on format
        $user_name = '';
        
        switch (strtolower($atts['format'])) {
            case 'first':
                $user_name = $current_user->first_name;
                if (empty($user_name)) {
                    $user_name = !empty($current_user->display_name) ? 
                        explode(' ', $current_user->display_name)[0] : 
                        $current_user->user_login;
                }
                break;
                
            case 'last':
                $user_name = $current_user->last_name;
                if (empty($user_name)) {
                    $display_name_parts = explode(' ', $current_user->display_name);
                    $user_name = count($display_name_parts) > 1 ? 
                        end($display_name_parts) : 
                        $current_user->user_login;
                }
                break;
                
            case 'display':
                $user_name = $current_user->display_name;
                if (empty($user_name)) {
                    $user_name = $current_user->user_login;
                }
                break;
                
            case 'full':
            default:
                if (!empty($current_user->first_name) && !empty($current_user->last_name)) {
                    $user_name = $current_user->first_name . ' ' . $current_user->last_name;
                } elseif (!empty($current_user->first_name)) {
                    $user_name = $current_user->first_name;
                } elseif (!empty($current_user->last_name)) {
                    $user_name = $current_user->last_name;
                } elseif (!empty($current_user->display_name)) {
                    $user_name = $current_user->display_name;
                } else {
                    $user_name = $current_user->user_login;
                }
                break;
        }
        
        // Use default if name is still empty
        if (empty($user_name)) {
            $user_name = $atts['default'];
        }
        
        // Apply prefix and suffix with profile link
        $profile_url = esc_url($atts['profile_url']);
        $output = $atts['prefix'] . '<a href="' . $profile_url . '" class="user-profile-link">' . esc_html($user_name) . '</a>' . $atts['suffix'];
        
        return $output;
    } else {
        // User is logged out - show login/signup links
        $login_link = '<a href="' . esc_url($atts['login_url']) . '">' . esc_html($atts['login_text']) . '</a>';
        $signup_link = '<a href="' . esc_url($atts['signup_url']) . '">' . esc_html($atts['signup_text']) . '</a>';
        
        return $login_link . $atts['separator'] . $signup_link;
    }
}
add_shortcode('login_status', 'display_login_status_shortcode');

/**
 * Shortcode to display logged-in user's name
 * Usage: [user_name] or [user_name format="full"] or [user_name format="first"]
 */
function display_user_name_shortcode($atts) {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        return ''; // Return empty string if not logged in
    }
    
    // Get shortcode attributes
    $atts = shortcode_atts(array(
        'format' => 'full', // Options: 'full', 'first', 'last', 'display'
        'prefix' => '',     // Text to add before name
        'suffix' => '',     // Text to add after name
        'default' => 'User' // Default text if name is empty
    ), $atts);
    
    $current_user = wp_get_current_user();
    
    // Get user name based on format
    $user_name = '';
    
    switch (strtolower($atts['format'])) {
        case 'first':
            $user_name = $current_user->first_name;
            if (empty($user_name)) {
                // Fallback to display name or username
                $user_name = !empty($current_user->display_name) ? 
                    explode(' ', $current_user->display_name)[0] : 
                    $current_user->user_login;
            }
            break;
            
        case 'last':
            $user_name = $current_user->last_name;
            if (empty($user_name)) {
                // Fallback to display name
                $display_name_parts = explode(' ', $current_user->display_name);
                $user_name = count($display_name_parts) > 1 ? 
                    end($display_name_parts) : 
                    $current_user->user_login;
            }
            break;
            
        case 'display':
            $user_name = $current_user->display_name;
            if (empty($user_name)) {
                $user_name = $current_user->user_login;
            }
            break;
            
        case 'full':
        default:
            // Try first name + last name
            if (!empty($current_user->first_name) && !empty($current_user->last_name)) {
                $user_name = $current_user->first_name . ' ' . $current_user->last_name;
            } elseif (!empty($current_user->first_name)) {
                $user_name = $current_user->first_name;
            } elseif (!empty($current_user->last_name)) {
                $user_name = $current_user->last_name;
            } elseif (!empty($current_user->display_name)) {
                $user_name = $current_user->display_name;
            } else {
                $user_name = $current_user->user_login;
            }
            break;
    }
    
    // Use default if name is still empty
    if (empty($user_name)) {
        $user_name = $atts['default'];
    }
    
    // Apply prefix and suffix
    $output = $atts['prefix'] . esc_html($user_name) . $atts['suffix'];
    
    return $output;
}
add_shortcode('user_name', 'display_user_name_shortcode');

/**
 * Alternative shortcode for username only
 * Usage: [username]
 */
function display_username_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }
    
    $current_user = wp_get_current_user();
    return esc_html($current_user->user_login);
}
add_shortcode('username', 'display_username_shortcode');

/**
 * Shortcode to display user's display name
 * Usage: [display_name]
 */
function display_user_display_name_shortcode($atts) {
    if (!is_user_logged_in()) {
        return '';
    }
    
    $current_user = wp_get_current_user();
    $display_name = !empty($current_user->display_name) ? 
        $current_user->display_name : 
        $current_user->user_login;
    
    return esc_html($display_name);
}
add_shortcode('display_name', 'display_user_display_name_shortcode');



/**
 * Register cron job to add 'new' badge to recent advertisements
 * (Disabled auto-draft functionality - advertisements stay published)
 */
function register_new_ad_badge_cron() {
    // Keeping the hook registered but not scheduling to disable auto-draft
    // If needed in future, uncomment the line below:
    // if (!wp_next_scheduled('add_new_badge_to_ads')) {
    //     wp_schedule_event(time(), 'hourly', 'add_new_badge_to_ads');
    // }
}
add_action('init', 'register_new_ad_badge_cron');

/**
 * Mark advertisements as 'new' when they're first published
 */
function mark_advertisement_as_new($post_id, $post, $update) {
    // Only for advertisements
    if ($post->post_type !== 'advertisement') {
        return;
    }
    
    // Only when first published (not on updates)
    if ($post->post_status === 'publish' && !$update) {
        // Mark as new with timestamp
        update_post_meta($post_id, '_advertisement_is_new', '1');
        update_post_meta($post_id, '_advertisement_published_date', current_time('mysql'));
    }
}
add_action('wp_insert_post', 'mark_advertisement_as_new', 10, 3);

/**
 * Notify admin when a new advertisement is submitted
 */
function notify_admin_on_new_advertisement($post_id, $post, $update) {
    // Only for advertisements
    if ($post->post_type !== 'advertisement') {
        return;
    }

    // Only for new posts (not updates)
    if ($update) {
        return;
    }

    // Get author details
    $author_id = $post->post_author;
    $author = get_userdata($author_id);
    
    if (!$author) {
        return;
    }

    $first_name = $author->first_name;
    $last_name = $author->last_name;
    $email = $author->user_email;
    
    // Get phone number from user meta (checking common keys)
    $phone = get_user_meta($author_id, 'phone_number', true);
    if (empty($phone)) {
        $phone = get_user_meta($author_id, 'phone', true);
    }
    if (empty($phone)) {
        $phone = get_user_meta($author_id, 'user_phone', true);
    }
    if (empty($phone)) {
        $phone = 'Not provided';
    }

    // Get advertisement details
    $ad_title = $post->post_title;
    $ad_url = get_permalink($post_id);
    $admin_email = get_option('admin_email');

    // Build the email
    $subject = '[Admin Notification] New Advertisement Posted - ' . get_bloginfo('name');
    
    $message = "Hello Admin,\n\n";
    $message .= "A new advertisement has been posted on " . get_bloginfo('name') . ".\n\n";
    $message .= "Hiring Agent Details:\n";
    $message .= "- Name: {$first_name} {$last_name}\n";
    $message .= "- Email: {$email}\n";
    $message .= "- Phone: {$phone}\n\n";
    $message .= "Advertisement Details:\n";
    $message .= "- Title: {$ad_title}\n";
    $message .= "- URL: {$ad_url}\n";
    $message .= "- Status: " . ucfirst($post->post_status) . "\n\n";
    $message .= "Please review this advertisement in the dashboard: " . admin_url('post.php?post=' . $post_id . '&action=edit') . "\n\n";
    $message .= "This is an automated notification.";

    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    // Send the email
    wp_mail($admin_email, $subject, $message, $headers);
}
add_action('wp_insert_post', 'notify_admin_on_new_advertisement', 20, 3);

/**
 * Check and update 'new' status - remove after 72 hours
 * This can be called periodically or checked at display time
 */
function check_advertisement_new_status($post_id) {
    $published_date = get_post_meta($post_id, '_advertisement_published_date', true);
    
    if (!$published_date) {
        return false;
    }
    
    $published_timestamp = strtotime($published_date);
    $current_timestamp = current_time('timestamp');
    $hours_since_published = ($current_timestamp - $published_timestamp) / 3600;
    
    // Remove new status after 72 hours
    if ($hours_since_published > 72) {
        delete_post_meta($post_id, '_advertisement_is_new');
        return false;
    }
    
    return get_post_meta($post_id, '_advertisement_is_new', true) === '1';
}

// Allow HEIC/HEIF image uploads
add_filter('upload_mimes', function ($mimes) {
    $mimes['heic|heif'] = 'image/heic';
    return $mimes;
});

// Add Account Type field to WordPress admin user profile
add_action('show_user_profile', 'render_account_type_field');
add_action('edit_user_profile', 'render_account_type_field');

function render_account_type_field($user) {
    $account_type = get_user_meta($user->ID, 'account_type', true);
    ?>
    <h3>Account Information</h3>
    <table class="form-table">
        <tr>
            <th><label for="account_type">Account Type</label></th>
            <td>
                <select name="account_type" id="account_type">
                    <option value="">Not set</option>
                    <option value="talent" <?php selected($account_type, 'talent'); ?>>Talent</option>
                    <option value="hiring" <?php selected($account_type, 'hiring'); ?>>Hiring</option>
                </select>
                <p class="description">Determines the user's account type on the platform.</p>
            </td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'save_account_type_field');
add_action('edit_user_profile_update', 'save_account_type_field');

function save_account_type_field($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
    if (isset($_POST['account_type'])) {
        $type = sanitize_text_field($_POST['account_type']);
        if (in_array($type, ['talent', 'hiring', ''])) {
            update_user_meta($user_id, 'account_type', $type);
        }
    }
}

// Add Account Type column to users admin table
add_filter('manage_users_columns', function ($columns) {
    $new_columns = [];
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'phone') {
            $new_columns['account_type'] = 'Account Type';
        }
    }
    return $new_columns;
});

add_filter('manage_users_custom_column', function ($output, $column_name, $user_id) {
    if ($column_name === 'account_type') {
        $type = get_user_meta($user_id, 'account_type', true);
        if ($type === 'talent') {
            return '<span style="color: #b2122d; font-weight: 600;">Talent</span>';
        } elseif ($type === 'hiring') {
            return '<span style="color: #3498db; font-weight: 600;">Hiring</span>';
        }
        return '<span style="color: #888;">—</span>';
    }
    return $output;
}, 10, 3);

/**
 * Retrieve the active talent profile associated with the current public token request.
 */
function get_public_talent_by_token() {
    static $talent_post = null;
    if ($talent_post !== null) {
        return $talent_post;
    }
    
    $public_token = get_query_var('public_talent_token');
    if (empty($public_token)) {
        $talent_post = false;
        return false;
    }
    
    $args = array(
        'post_type' => 'talent',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_talent_shareable_token',
                'value' => $public_token,
                'compare' => '='
            ),
            array(
                'key' => '_talent_public_sharing_enabled',
                'value' => '1',
                'compare' => '='
            )
        ),
        'posts_per_page' => 1
    );
    
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        $talent_post = $query->posts[0];
    } else {
        $talent_post = false;
    }
    
    return $talent_post;
}

/**
 * Filter the browser document title
 */
add_filter('document_title_parts', function($title_parts) {
    $talent = get_public_talent_by_token();
    if ($talent) {
        $title_parts['title'] = esc_html($talent->post_title) . ' - Portfolio';
        $title_parts['site'] = 'The VelvetReel';
    }
    return $title_parts;
});

/**
 * Filter Rank Math titles and metadata for public talent shares
 */
add_filter('rank_math/frontend/title', function($title) {
    $talent = get_public_talent_by_token();
    if ($talent) {
        return esc_html($talent->post_title) . ' - Portfolio | The VelvetReel';
    }
    return $title;
});

add_filter('rank_math/frontend/description', function($desc) {
    $talent = get_public_talent_by_token();
    if ($talent) {
        $excerpt = get_the_excerpt($talent);
        if (!$excerpt) {
            $excerpt = "View " . esc_html($talent->post_title) . "'s professional talent portfolio, photos, and resume on The VelvetReel.";
        }
        return esc_attr(wp_strip_all_tags($excerpt));
    }
    return $desc;
});

// Facebook OpenGraph tags
add_filter('rank_math/opengraph/facebook/title', function($title) {
    $talent = get_public_talent_by_token();
    if ($talent) {
        return esc_html($talent->post_title) . ' - Portfolio | The VelvetReel';
    }
    return $title;
});

add_filter('rank_math/opengraph/facebook/description', function($desc) {
    $talent = get_public_talent_by_token();
    if ($talent) {
        $excerpt = get_the_excerpt($talent);
        if (!$excerpt) {
            $excerpt = "View " . esc_html($talent->post_title) . "'s professional talent portfolio, photos, and resume on The VelvetReel.";
        }
        return esc_attr(wp_strip_all_tags($excerpt));
    }
    return $desc;
});

add_filter('rank_math/opengraph/facebook/image', function($img) {
    $talent = get_public_talent_by_token();
    if ($talent) {
        $img_id = get_post_thumbnail_id($talent->ID);
        if ($img_id) {
            $img_url = wp_get_attachment_image_url($img_id, 'large');
            if ($img_url) {
                return $img_url;
            }
        }
    }
    return $img;
});

// Twitter cards tags
add_filter('rank_math/opengraph/twitter/title', function($title) {
    $talent = get_public_talent_by_token();
    if ($talent) {
        return esc_html($talent->post_title) . ' - Portfolio | The VelvetReel';
    }
    return $title;
});

add_filter('rank_math/opengraph/twitter/description', function($desc) {
    $talent = get_public_talent_by_token();
    if ($talent) {
        $excerpt = get_the_excerpt($talent);
        if (!$excerpt) {
            $excerpt = "View " . esc_html($talent->post_title) . "'s professional talent portfolio, photos, and resume on The VelvetReel.";
        }
        return esc_attr(wp_strip_all_tags($excerpt));
    }
    return $desc;
});

add_filter('rank_math/opengraph/twitter/image', function($img) {
    $talent = get_public_talent_by_token();
    if ($talent) {
        $img_id = get_post_thumbnail_id($talent->ID);
        if ($img_id) {
            $img_url = wp_get_attachment_image_url($img_id, 'large');
            if ($img_url) {
                return $img_url;
            }
        }
    }
    return $img;
});

// Canonical URL
add_filter('rank_math/frontend/canonical', function($canonical) {
    $talent = get_public_talent_by_token();
    if ($talent) {
        return home_url('/p/' . get_query_var('public_talent_token') . '/');
    }
    return $canonical;
});

/**
 * Get talent profile image URL with fallback to portfolio image or default avatar SVG
 *
 * @param int $post_id
 * @param string $size
 * @return string
 */
if (!function_exists('get_talent_profile_image_url')) {
    function get_talent_profile_image_url($post_id, $size = 'medium_large') {
        $post_id = (int) $post_id;
        if ($post_id <= 0) {
            return get_stylesheet_directory_uri() . '/assets/images/default-talent-avatar.svg';
        }

        // 1. Try featured image (headshot/profile photo)
        $image_url = get_the_post_thumbnail_url($post_id, $size);
        if ($image_url) {
            return $image_url;
        }

        // 2. Try headshot/profile photo meta fields
        $headshot_meta = get_post_meta($post_id, '_talent_headshot', true);
        if (!$headshot_meta) {
            $headshot_meta = get_post_meta($post_id, '_talent_profile_photo', true);
        }
        if ($headshot_meta) {
            if (is_numeric($headshot_meta) && (int) $headshot_meta > 0) {
                $url = wp_get_attachment_image_url((int) $headshot_meta, $size);
                if ($url) return $url;
            } elseif (is_string($headshot_meta) && filter_var($headshot_meta, FILTER_VALIDATE_URL)) {
                return esc_url_raw($headshot_meta);
            }
        }

        // 3. Try first image from portfolio meta (_talent_portfolio)
        $portfolio_meta = get_post_meta($post_id, '_talent_portfolio', true);
        if (is_string($portfolio_meta)) {
            $decoded = json_decode($portfolio_meta, true);
            if (is_array($decoded)) {
                $portfolio_meta = $decoded;
            }
        }

        if (is_array($portfolio_meta) && !empty($portfolio_meta)) {
            foreach ($portfolio_meta as $item) {
                if (is_numeric($item) && (int) $item > 0) {
                    $url = wp_get_attachment_image_url((int) $item, $size);
                    if ($url) return $url;
                } elseif (is_array($item) && !empty($item['id'])) {
                    $url = wp_get_attachment_image_url((int) $item['id'], $size);
                    if ($url) return $url;
                } elseif (is_array($item) && !empty($item['url'])) {
                    return esc_url_raw($item['url']);
                } elseif (is_string($item) && filter_var($item, FILTER_VALIDATE_URL)) {
                    return esc_url_raw($item);
                }
            }
        }

        // 4. Search for any media attachments associated with this talent post
        $attachments = get_posts(array(
            'post_type'      => 'attachment',
            'posts_per_page' => 1,
            'post_parent'    => $post_id,
            'post_mime_type' => 'image',
            'orderby'        => 'menu_order ID',
            'order'          => 'ASC'
        ));

        if (!empty($attachments) && isset($attachments[0]->ID)) {
            $url = wp_get_attachment_image_url($attachments[0]->ID, $size);
            if ($url) return $url;
        }

        // 5. Default fallback avatar SVG
        return get_stylesheet_directory_uri() . '/assets/images/default-talent-avatar.svg';
    }
}

?>
