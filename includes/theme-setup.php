<?php
/**
 * Theme setup and basic configurations
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load child theme scripts & styles.
 */
function hello_elementor_child_scripts_styles() {
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

/**
 * Create user information table
 */
function create_userinformation_table() {
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

/**
 * Add Phone column to WordPress Users admin table
 */
// Add phone column header
add_filter('manage_users_columns', function($columns) {
    $columns['phone'] = 'Phone';
    return $columns;
});

// Add phone column content
add_action('manage_users_custom_column', function($value, $column_name, $user_id) {
    if ($column_name === 'phone') {
        // First try to get from user meta
        $phone = get_user_meta($user_id, 'phone', true);
        
        // If not in meta, try custom table
        if (empty($phone)) {
            global $wpdb;
            $table = $wpdb->prefix . 'userinformation';
            $phone = $wpdb->get_var($wpdb->prepare("SELECT phone FROM $table WHERE user_id = %d", $user_id));
        }
        
        return !empty($phone) ? esc_html($phone) : '—';
    }
    return $value;
}, 10, 3);

/**
 * Show admin bar for logged in users
 */
add_action('after_setup_theme', function () {
    if (is_user_logged_in() && !is_admin()) {
        show_admin_bar(true);
    }
});