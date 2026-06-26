<?php
/**
 * User management functionality
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for dashboard image upload
 */
function handle_dashboard_image_upload() {
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

/**
 * AJAX handler for profile image upload
 */
function handle_profile_image_ajax() {
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
add_action('wp_ajax_upload_profile_image', 'handle_profile_image_ajax');

/**
 * Profile picture menu shortcode
 */
function rifat_elementor_profile_picture_menu() {
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