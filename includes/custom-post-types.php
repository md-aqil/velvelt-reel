<?php
/**
 * Custom post types and taxonomies
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register a custom post type called "Talent".
 */
function create_talent_cpt() {
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
        'featured_image' => _x('Talent Profile Image', 'Overrides the "Featured Image" phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'set_featured_image' => _x('Set profile image', 'Overrides the "Set featured image" phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'remove_featured_image' => _x('Remove profile image', 'Overrides the "Remove featured image" phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'use_featured_image' => _x('Use as profile image', 'Overrides the "Use as featured image" phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'archives' => _x('Talent archives', 'The post type archive label used in nav menus. Default "Post Archives". Added in 4.4', 'hello-elementor-child'),
        'insert_into_item' => _x('Insert into talent', 'Overrides the "Insert into post"/"Insert into page" phrase (used when inserting media into a post). Added in 4.4', 'hello-elementor-child'),
        'uploaded_to_this_item' => _x('Uploaded to this talent', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase (used when viewing media attached to a post). Added in 4.4', 'hello-elementor-child'),
        'filter_items_list' => _x('Filter talents list', 'Screen reader text for the filter links heading on the post type listing screen. Default "Filter posts list"/"Filter pages list". Added in 4.4', 'hello-elementor-child'),
        'items_list_navigation' => _x('Talents list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default "Posts list navigation"/"Pages list navigation". Added in 4.4', 'hello-elementor-child'),
        'items_list' => _x('Talents list', 'Screen reader text for the items list heading on the post type listing screen. Default "Posts list"/"Pages list". Added in 4.4', 'hello-elementor-child'),
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
function create_talent_taxonomy() {
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
function create_talent_tagging_taxonomy() {
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
function create_talent_state_taxonomy() {
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
function create_talent_country_taxonomy() {
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
 * Register a custom post type called "Advertisement".
 */
function create_advertisement_cpt() {
    $labels = array(
        'name' => _x('Advertisements', 'Post type general name', 'hello-elementor-child'),
        'singular_name' => _x('Advertisement', 'Post type singular name', 'hello-elementor-child'),
        'menu_name' => _x('Advertisements', 'Admin Menu text', 'hello-elementor-child'),
        'name_admin_bar' => _x('Advertisement', 'Add New on Toolbar', 'hello-elementor-child'),
        'add_new' => __('Add New', 'hello-elementor-child'),
        'add_new_item' => __('Add New Advertisement', 'hello-elementor-child'),
        'new_item' => __('New Advertisement', 'hello-elementor-child'),
        'edit_item' => __('Edit Advertisement', 'hello-elementor-child'),
        'view_item' => __('View Advertisement', 'hello-elementor-child'),
        'all_items' => __('All Advertisements', 'hello-elementor-child'),
        'search_items' => __('Search Advertisements', 'hello-elementor-child'),
        'parent_item_colon' => __('Parent Advertisements:', 'hello-elementor-child'),
        'not_found' => __('No advertisements found.', 'hello-elementor-child'),
        'not_found_in_trash' => __('No advertisements found in Trash.', 'hello-elementor-child'),
        'featured_image' => _x('Advertisement Image', 'Overrides the "Featured Image" phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'set_featured_image' => _x('Set advertisement image', 'Overrides the "Set featured image" phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'remove_featured_image' => _x('Remove advertisement image', 'Overrides the "Remove featured image" phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'use_featured_image' => _x('Use as advertisement image', 'Overrides the "Use as featured image" phrase for this post type. Added in 4.3', 'hello-elementor-child'),
        'archives' => _x('Advertisement archives', 'The post type archive label used in nav menus. Default "Post Archives". Added in 4.4', 'hello-elementor-child'),
        'insert_into_item' => _x('Insert into advertisement', 'Overrides the "Insert into post"/"Insert into page" phrase (used when inserting media into a post). Added in 4.4', 'hello-elementor-child'),
        'uploaded_to_this_item' => _x('Uploaded to this advertisement', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase (used when viewing media attached to a post). Added in 4.4', 'hello-elementor-child'),
        'filter_items_list' => _x('Filter advertisements list', 'Screen reader text for the filter links heading on the post type listing screen. Default "Filter posts list"/"Filter pages list". Added in 4.4', 'hello-elementor-child'),
        'items_list_navigation' => _x('Advertisements list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default "Posts list navigation"/"Pages list navigation". Added in 4.4', 'hello-elementor-child'),
        'items_list' => _x('Advertisements list', 'Screen reader text for the items list heading on the post type listing screen. Default "Posts list"/"Pages list". Added in 4.4', 'hello-elementor-child'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_nav_menus' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'advertisement'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 6,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_icon' => 'dashicons-megaphone',
    );

    register_post_type('advertisement', $args);
}
add_action('init', 'create_advertisement_cpt');

/**
 * Create a custom taxonomy for the "Advertisement" post type.
 */
function create_advertisement_taxonomy() {
    $labels = array(
        'name' => _x('Advertisement Categories', 'taxonomy general name', 'hello-elementor-child'),
        'singular_name' => _x('Advertisement Category', 'taxonomy singular name', 'hello-elementor-child'),
        'search_items' => __('Search Advertisement Categories', 'hello-elementor-child'),
        'all_items' => __('All Advertisement Categories', 'hello-elementor-child'),
        'parent_item' => __('Parent Advertisement Category', 'hello-elementor-child'),
        'parent_item_colon' => __('Parent Advertisement Category:', 'hello-elementor-child'),
        'edit_item' => __('Edit Advertisement Category', 'hello-elementor-child'),
        'update_item' => __('Update Advertisement Category', 'hello-elementor-child'),
        'add_new_item' => __('Add New Advertisement Category', 'hello-elementor-child'),
        'new_item_name' => __('New Advertisement Category Name', 'hello-elementor-child'),
        'menu_name' => __('Categories', 'hello-elementor-child'),
    );

    $args = array(
        'hierarchical' => true, // Like categories
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'advertisement-category', 'with_front' => false),
        'public' => true,
        'show_in_nav_menus' => true,
    );

    register_taxonomy('advertisement_category', array('advertisement'), $args);
}
add_action('init', 'create_advertisement_taxonomy', 20);

/**
 * Ensure mandatory advertisement categories exist
 */
function ensure_mandatory_advertisement_categories() {
    $mandatory_categories = array('Casting Calls', 'Crew Calls', 'Rentals');
    
    foreach ($mandatory_categories as $cat_name) {
        if (!term_exists($cat_name, 'advertisement_category')) {
            wp_insert_term($cat_name, 'advertisement_category');
        }
    }
}
add_action('init', 'ensure_mandatory_advertisement_categories', 30);

/**
 * Remove unwanted taxonomies from the advertisement post type
 */
function remove_unwanted_advertisement_taxonomies() {
    // Remove the 'space-rentals' taxonomy if it exists
    unregister_taxonomy_for_object_type('space-rentals', 'advertisement');
    
    // Also remove any other unwanted taxonomies
    // unregister_taxonomy_for_object_type('unwanted_taxonomy_name', 'advertisement');
    
    // Clean up any existing terms in unwanted taxonomies
    if (taxonomy_exists('space-rentals')) {
        $ads = get_posts(array(
            'post_type' => 'advertisement',
            'numberposts' => -1,
            'post_status' => 'any'
        ));
        
        foreach ($ads as $ad) {
            wp_delete_object_term_relationships($ad->ID, 'space-rentals');
        }
    }
}
add_action('init', 'remove_unwanted_advertisement_taxonomies', 999);

/**
 * Remove unwanted taxonomy metaboxes from the advertisement post editor
 */
function remove_unwanted_advertisement_metaboxes() {
    // Remove the 'space-rentals' metabox if it exists
    remove_meta_box('tagsdiv-space-rentals', 'advertisement', 'side');
    remove_meta_box('space-rentalsdiv', 'advertisement', 'side');
}
add_action('admin_menu', 'remove_unwanted_advertisement_metaboxes');

/**
 * Create default advertisement categories
 */
function create_default_advertisement_categories() {
    // Define the default categories
    $default_categories = array('Casting Calls', 'Equipment Rentals', 'Events', 'Space Rental');
    
    // Create each category if it doesn't exist
    foreach ($default_categories as $category) {
        if (!term_exists($category, 'advertisement_category')) {
            wp_insert_term($category, 'advertisement_category');
        }
    }
}
add_action('init', 'create_default_advertisement_categories');

/**
 * Add taxonomy support to the 'talent' CPT.
 */
function add_talent_taxonomy_support($args, $post_type) {
    if ('talent' === $post_type) {
        $args['taxonomies'] = array('talent_type', 'talent_tagging');
    }
    return $args;
}
add_filter('register_post_type_args', 'add_talent_taxonomy_support', 10, 2);