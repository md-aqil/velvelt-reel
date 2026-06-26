<?php
/**
 * Admin functionality to manage draft talent submissions
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add submenu page for draft submissions
 */
function add_draft_submissions_menu() {
    add_submenu_page(
        'edit.php?post_type=talent',
        'Draft Submissions',
        'Draft Submissions',
        'manage_options',
        'draft-submissions',
        'draft_submissions_page_callback'
    );
}
add_action('admin_menu', 'add_draft_submissions_menu');



/**
 * Callback function to render the draft submissions page
 */
function draft_submissions_page_callback() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get draft submissions
    $draft_submissions = get_draft_talent_submissions();

    // Display the page
    ?>
    <div class="wrap">
        <h1>Draft Talent Submissions</h1>
        <p>These are talent profiles that have been partially completed and saved as drafts.</p>

        <?php if (!empty($draft_submissions)) : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Last Modified</th>
                        <th>Current Step</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($draft_submissions as $submission) : ?>
                        <tr>
                            <td><?php echo esc_html($submission->ID); ?></td>
                            <td><?php echo esc_html($submission->post_title); ?></td>
                            <td>
                                <?php
                                $author = get_user_by('ID', $submission->post_author);
                                echo esc_html($author ? $author->display_name : 'Unknown');
                                ?>
                            </td>
                            <td><?php echo esc_html(get_the_modified_date('F j, Y g:i A', $submission->ID)); ?></td>
                            <td>
                                <?php
                                // Get current step from post meta if available
                                $current_step = get_post_meta($submission->ID, '_talent_current_step', true);
                                if ($current_step) {
                                    echo esc_html("Step " . $current_step);
                                } else {
                                    echo esc_html('Unknown');
                                }
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(get_edit_post_link($submission->ID)); ?>" class="button button-primary">Edit</a>
                                <a href="<?php echo esc_url(get_permalink($submission->ID)); ?>" class="button" target="_blank">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No draft submissions found.</p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Get all draft talent submissions
 *
 * @return array Array of draft talent posts
 */
function get_draft_talent_submissions() {
    $args = array(
        'post_type' => 'talent',
        'post_status' => 'draft',
        'posts_per_page' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
    );

    $drafts = get_posts($args);
    return $drafts;
}

/**
 * Update the current step for a talent submission
 *
 * @param int $post_id The post ID
 * @param int $step The current step
 */
function update_talent_current_step($post_id, $step) {
    if (get_post_type($post_id) === 'talent') {
        update_post_meta($post_id, '_talent_current_step', $step);
    }
}

/**
 * Add a column to the talent posts list to show draft status
 */
function add_draft_status_column($columns) {
    $columns['draft_status'] = 'Draft Status';
    return $columns;
}
add_filter('manage_talent_posts_columns', 'add_draft_status_column');

/**
 * Populate the draft status column with content
 */
function populate_draft_status_column($column, $post_id) {
    if ($column === 'draft_status') {
        $post_status = get_post_status($post_id);
        if ($post_status === 'draft') {
            $current_step = get_post_meta($post_id, '_talent_current_step', true);
            if ($current_step) {
                echo 'Draft - Step ' . esc_html($current_step);
            } else {
                echo 'Draft - Incomplete';
            }
        } else {
            echo esc_html(ucfirst($post_status));
        }
    }
}
add_action('manage_talent_posts_custom_column', 'populate_draft_status_column', 10, 2);

/**
 * Add the current step to the draft when saving progress via AJAX
 */
function save_current_step_to_draft($post_id) {
    if (isset($_POST['current_step']) && $_POST['current_step']) {
        $current_step = intval($_POST['current_step']);
        update_talent_current_step($post_id, $current_step);
    }
}
add_action('save_post_talent', 'save_current_step_to_draft');

/**
 * Modify the main query for talent posts to include drafts for users with edit capabilities
 */
function modify_talent_posts_query($query) {
    // Only modify the admin query for talent posts
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'talent') {
        // Check if we're on the main talent posts page (not the draft submissions page)
        if (!isset($_GET['page']) || $_GET['page'] !== 'draft-submissions') {
            // Modify the post status to include drafts for users who can edit them
            if (!isset($_GET['post_status']) && current_user_can('edit_others_posts')) {
                $query->set('post_status', array('publish', 'pending', 'draft', 'private'));
            }
        }
    }
}
add_action('pre_get_posts', 'modify_talent_posts_query');