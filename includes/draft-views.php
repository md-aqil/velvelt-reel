<?php
/**
 * Add draft views functionality for talent post type
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add draft post status views to talent post type admin screen
 */
function add_talent_draft_views($views) {
    global $current_screen, $wpdb;
    
    if ($current_screen->post_type !== 'talent') {
        return $views;
    }
    
    // Get counts for each post status
    $query = $wpdb->prepare(
        "SELECT post_status, COUNT(*) as count 
        FROM {$wpdb->posts} 
        WHERE post_type = %s 
        GROUP BY post_status",
        'talent'
    );
    $post_status_counts = $wpdb->get_results($query);
    
    $draft_count = 0;
    $all_count = 0;
    
    foreach ($post_status_counts as $status_count) {
        if ($status_count->post_status === 'draft') {
            $draft_count = $status_count->count;
        }
        if (in_array($status_count->post_status, array('publish', 'pending', 'draft', 'private'))) {
            $all_count += $status_count->count;
        }
    }
    
    // Add draft view
    if ($draft_count > 0) {
        $class = (isset($_GET['post_status']) && $_GET['post_status'] === 'draft') ? ' class="current"' : '';
        $draft_url = add_query_arg(array(
            'post_type' => 'talent',
            'post_status' => 'draft'
        ), admin_url('edit.php'));
        $views['draft'] = sprintf(
            '<a href="%s"%s>Draft <span class="count">(%d)</span></a>',
            esc_url($draft_url),
            $class,
            $draft_count
        );
    }
    
    // Update all view to include drafts
    $all_url = add_query_arg(array(
        'post_type' => 'talent'
    ), admin_url('edit.php'));
    $class = (isset($_GET['post_status']) && $_GET['post_status'] === '' && !isset($_GET['post_status'])) ? ' class="current"' : '';
    $views['all'] = sprintf(
        '<a href="%s"%s>All <span class="count">(%d)</span></a>',
        esc_url($all_url),
        $class,
        $all_count
    );
    
    return $views;
}
add_filter('views_edit-talent', 'add_talent_draft_views');