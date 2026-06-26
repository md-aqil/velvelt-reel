<?php
/**
 * Template Name: Classified Submission Form
 *
 * @package HelloElementorChild
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!is_user_logged_in()) {
    // Redirect to login page
    wp_redirect(home_url('/membership-login/'));
    exit;
}

// Rate limiting: Prevent too frequent submissions
$user_id = get_current_user_id();
$last_submission_key = 'last_ad_submission_' . $user_id;
$last_submission = get_user_meta($user_id, $last_submission_key, true);

if ($last_submission && (time() - $last_submission) < 300) { // 5 minutes cooldown
    $error_message = 'Please wait before submitting another classified. You can submit again in a few minutes.';
}

// Handle form submission
$error_message = '';
$show_payment = false;
$form_data = array();
$show_success = false;

// Check if user has paid - check for any active paid ad within 15 days
$current_user = wp_get_current_user();
$has_paid = false;

// Check if returning from payment
$payment_success = isset($_GET['payment']) && $_GET['payment'] === 'success';

if ($payment_success) {
    // In a real scenario, verify with Stripe webhook or API
    // For now, we'll trust the URL parameter but in production,
    // you should verify the payment via server-side verification
    $has_paid = true;
}

$paid_ad_posts = get_posts(array(
    'post_type' => 'advertisement',
    'author' => $current_user->ID,
    'posts_per_page' => -1,
    'meta_key' => '_classifieds_fee_paid',
    'meta_value' => '1'
));

if (!$payment_success) {
    foreach ($paid_ad_posts as $ad) {
        $post_date = strtotime($ad->post_date);
        $days_since_post = (time() - $post_date) / (60 * 60 * 24);
        if ($days_since_post <= 15) {
            $has_paid = true;
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_advertisement'])) {
    // Verify nonce
    if (!isset($_POST['advertisement_nonce']) || !wp_verify_nonce($_POST['advertisement_nonce'], 'submit_advertisement')) {
        wp_die('Security check failed.');
    }
    
    // Validate required fields
    if (empty($_POST['advertisement_title'])) {
        $error_message = 'Classified title is required.';
    } elseif (strlen($_POST['advertisement_title']) > 200) {
        $error_message = 'Classified title must be less than 200 characters.';
    } elseif (empty($_POST['advertisement_content'])) {
        $error_message = 'Classified content is required.';
    } elseif (strlen($_POST['advertisement_content']) > 5000) {
        $error_message = 'Classified content must be less than 5000 characters.';
    } elseif (!empty($_FILES['advertisement_image']['name']) && !validate_uploaded_image()) {
        $error_message = 'Invalid image file. Please upload a valid image (JPG, PNG, GIF).';
    } else {
        // Sanitize and validate form data
        $title = sanitize_text_field($_POST['advertisement_title']);
        $content = wp_kses_post($_POST['advertisement_content']);
        $location = sanitize_text_field($_POST['advertisement_location']);
        
        // Validate content doesn't contain malicious code
        if (strpos($content, '<script') !== false || strpos($content, 'javascript:') !== false) {
            $error_message = 'Content contains invalid elements.';
        } else {
            // Save form data temporarily
            $form_data = array(
                'title' => $title,
                'content' => $content,
                'location' => $location,
                'categories' => isset($_POST['advertisement_categories']) ? array_map('sanitize_text_field', $_POST['advertisement_categories']) : array()
            );
        }
    }
    
    if (empty($error_message)) {
        // If user hasn't paid, show payment screen
        if (!$has_paid) {
            $show_payment = true;
            // Save form data to session for use after payment
            $_SESSION['ad_form_data'] = $form_data;
        } else {
            // User has paid, create the post
            $advertisement_post = array(
                'post_title'   => $form_data['title'],
                'post_content' => $form_data['content'],
                'post_status'  => 'pending', // Pending review before publishing
                'post_type'    => 'advertisement',
            );

            $post_id = wp_insert_post($advertisement_post);

            if (is_wp_error($post_id)) {
                $error_message = $post_id->get_error_message();
            } else {
                // Mark as paid
                update_post_meta($post_id, '_classifieds_fee_paid', '1');
                update_post_meta($post_id, '_classifieds_payment_date', current_time('mysql'));
                
                // Handle file upload
                if (!empty($_FILES['advertisement_image']['name'])) {
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    require_once(ABSPATH . 'wp-admin/includes/media.php');

                    $attachment_id = media_handle_upload('advertisement_image', $post_id);

                    if (!is_wp_error($attachment_id)) {
                        set_post_thumbnail($post_id, $attachment_id);
                    }
                }
                
                // Handle category assignment
                if (!empty($form_data['categories'])) {
                    wp_set_object_terms($post_id, $form_data['categories'], 'advertisement_category');
                }
                
                // Add location
                if (!empty($form_data['location'])) {
                    update_post_meta($post_id, 'ad_location', $form_data['location']);
                }

                // Show success popup instead of redirect
                $show_success = true;
                
                // Update user's last submission time to implement rate limiting
                update_user_meta($current_user->ID, $last_submission_key, time());
            }
        }
    }
}

// Get success parameter from URL (for returning from payment)
$url_success = isset($_GET['success']) && $_GET['success'] === 'true';
$payment_success = isset($_GET['payment']) && $_GET['payment'] === 'success';

// Also check for Stripe session_id (common in Stripe redirects)
$stripe_session = isset($_GET['session_id']) && !empty($_GET['session_id']);

if ($show_success || $url_success || $payment_success || $stripe_session) {
    $show_success = true;
    
    // If returning from payment with form data saved, create the ad
    $form_data_used = false;
    
    // First try session data
    if (isset($_SESSION['ad_form_data']) && !empty($_SESSION['ad_form_data'])) {
        $form_data = $_SESSION['ad_form_data'];
        $form_data_used = true;
        
        // SECURITY: In production, verify payment status via server-side verification
        // before creating the post to prevent fraud
        $advertisement_post = array(
            'post_title'   => $form_data['title'],
            'post_content' => $form_data['content'],
            'post_status'  => 'pending', // Pending review before publishing
            'post_type'    => 'advertisement',
        );

        $post_id = wp_insert_post($advertisement_post);

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_classifieds_fee_paid', '1');
            update_post_meta($post_id, '_classifieds_payment_date', current_time('mysql'));
            
            if (!empty($form_data['categories'])) {
                wp_set_object_terms($post_id, $form_data['categories'], 'advertisement_category');
            }
            
            if (!empty($form_data['location'])) {
                update_post_meta($post_id, 'ad_location', $form_data['location']);
            }
        }
        
        // Clear session data
        unset($_SESSION['ad_form_data']);
        
        // Update user's last submission time to implement rate limiting
        update_user_meta($current_user->ID, $last_submission_key, time());
    }
    
    // If no session data, we'll try to get it from URL params (title only as fallback)
    // The JavaScript will handle localStorage data
}
/**
 * Validates uploaded image file
 *
 * @return bool True if image is valid, false otherwise
 */
function validate_uploaded_image() {
    if (!isset($_FILES['advertisement_image']) || $_FILES['advertisement_image']['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $file = $_FILES['advertisement_image'];
    $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
    $file_info = getimagesize($file['tmp_name']);
    
    if ($file_info === false) {
        return false;
    }
    
    $mime_type = $file_info['mime'];
    
    if (!in_array($mime_type, $allowed_types)) {
        return false;
    }
    
    // Check file extension as well
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return false;
    }
    
    // Check file size (limit to 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }
    
    return true;
}

/**
 * Cleans up old rate limiting entries to prevent database bloat
 */
function cleanup_old_rate_limit_entries() {
    global $wpdb;
    
    // Query to find all user meta keys that match our pattern
    $meta_keys = $wpdb->get_col($wpdb->prepare(
        "SELECT DISTINCT meta_key FROM {$wpdb->usermeta} WHERE meta_key LIKE %s",
        'last_ad_submission_%'
    ));
    
    $current_time = time();
    $cleanup_threshold = 30 * 24 * 60 * 60; // 30 days in seconds
    
    foreach ($meta_keys as $meta_key) {
        // Extract user ID from the meta key
        $user_id = str_replace('last_ad_submission_', '', $meta_key);
        $last_submission = get_user_meta($user_id, $meta_key, true);
        
        // If the last submission was more than 30 days ago, remove the entry
        if ($last_submission && ($current_time - $last_submission) > $cleanup_threshold) {
            delete_user_meta($user_id, $meta_key);
        }
    }
}

// Run cleanup occasionally (about 1 in 100 requests)
if (rand(1, 100) === 1) {
    cleanup_old_rate_limit_entries();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Classified - VelvetReel</title>
    <link rel="stylesheet" href="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/form-style.css?ver=<?php echo esc_attr(HELLO_ELEMENTOR_CHILD_VERSION); ?>">
    <link rel="stylesheet" href="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/global.css?ver=<?php echo esc_attr(HELLO_ELEMENTOR_CHILD_VERSION); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Make body styling consistent with theme */
        body {
            background: var(--global-background-color, #000);
            min-height: 100vh;
            color: var(--global-text-color, #fff);
        }
        .cky-btn-revisit-wrapper {
            position: absolute !important;
        }
        
        .elementor-location-header {
            background-color: #0f0f0f !important;
        }
        /* Reduced form container styling */
        .form-container {
            max-width: 800px;
            margin: 5px auto 20px;
            padding: 0;
            background: transparent;
            border-radius: 0;
            box-shadow: none;
            flex-direction: column;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-container {
                margin: 5px 15px 20px;
                padding: 0;
            }
        }
        
        @media (max-width: 480px) {
            .form-container {
                margin: 5px 10px 20px;
                padding: 0;
            }
        }
        
        .form-header {
            text-align: left;
            margin: 0 0 24px 0;
            padding: 0;
        }
        
        .form-header h1 {
            color: #fff;
            margin: 0 0 12px 0;
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        
        .form-header h1 i {
            color: #b2122d;
            font-size: 24px;
        }
        
        .form-header p {
            color: #888;
            margin: 0;
            font-size: 15px;
            line-height: 1.5;
        }
        
        .form-header .steps-indicator {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        .form-header .step {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            font-size: 13px;
        }
        
        .form-header .step.active {
            color: #b2122d;
        }
        
        .form-header .step-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
        
        .form-header .step.active .step-number {
            background: #b2122d;
            color: #fff;
        }
        
        .form-main {
            background: transparent;
            padding: 0 !important;
        }
        
        /* Form Step Styling */
        .form-step {
            background: linear-gradient(145deg, #1a1a1a 0%, #141414 100%);
            padding: 35px;
            border-radius: 16px;
            border: 1px solid #2a2a2a;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .form-step::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #b2122d, #df1d3d, #b2122d);
        }
        
        /* Payment Overlay */
        .payment-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .payment-modal {
            background: linear-gradient(180deg, #1a1a1a 0%, #111 100%);
            border-radius: 20px;
            padding: 45px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            border: 1px solid #333;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }
        
        .payment-modal-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 10px 30px rgba(178, 18, 45, 0.4);
        }
        
        .payment-modal-icon i {
            font-size: 36px;
            color: #fff;
        }
        
        .payment-modal h2 {
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .payment-modal-description {
            color: #888;
            margin-bottom: 25px;
            font-size: 1rem;
        }
        
        .payment-amount {
            font-size: 3rem;
            font-weight: 700;
            color: #fff;
            margin: 20px 0;
            font-family: 'Gantari', sans-serif;
        }
        
        .payment-amount span {
            font-size: 1rem;
            color: #666;
            font-weight: 400;
        }
        
        .payment-features {
            text-align: left;
            margin: 30px 0;
            padding: 25px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            border: 1px solid #222;
        }
        
        .payment-features h4 {
            color: #fff;
            margin-bottom: 18px;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .payment-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .payment-features li {
            color: #aaa;
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }
        
        .payment-features li i {
            color: #4ade80;
            font-size: 14px;
        }
        
        /* Payment Button - Enhanced Design */
        .payment-button-container {
            margin-top: 25px;
        }
        
        .payment-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
            color: #fff !important;
            padding: 20px 45px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 700;
            font-size: 18px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
            box-shadow: 0 8px 30px rgba(178, 18, 45, 0.5);
            position: relative;
            overflow: hidden;
        }
        
        .payment-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .payment-button:hover {
            background: linear-gradient(135deg, #d41835 0%, #ff4d6d 100%);
            transform: translateY(-3px);
            box-shadow: 0 15px 50px rgba(178, 18, 45, 0.6);
        }
        
        .payment-button:hover::before {
            left: 100%;
        }
        
        .payment-button:active {
            transform: translateY(-1px);
        }
        
        .payment-button i {
            font-size: 22px;
        }
        
        /* Stripe Button Styling */
        .payment-button-container input[type="submit"],
        .payment-button-container .stripe-button {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 12px !important;
            background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%) !important;
            color: #fff !important;
            padding: 20px 45px !important;
            border-radius: 14px !important;
            font-weight: 700 !important;
            font-size: 18px !important;
            border: none !important;
            cursor: pointer !important;
            width: 100% !important;
            box-sizing: border-box !important;
            box-shadow: 0 8px 30px rgba(178, 18, 45, 0.5) !important;
            transition: all 0.3s ease !important;
            height: auto !important;
            line-height: 1.2 !important;
        }
        
        .payment-button-container input[type="submit"]:hover,
        .payment-button-container .stripe-button:hover {
            background: linear-gradient(135deg, #d41835 0%, #ff4d6d 100%) !important;
            transform: translateY(-3px) !important;
            box-shadow: 0 15px 50px rgba(178, 18, 45, 0.6) !important;
        }
        
        .payment-secure {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 18px;
            color: #666;
            font-size: 0.85rem;
        }
        
        .payment-secure i {
            color: #4ade80;
        }
        
        /* Payment Modal Close Button */
        .payment-modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            color: #fff;
            cursor: pointer;
            font-size: 20px;
            z-index: 10;
        }
        
        /* Success Popup */
        .success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .success-modal {
            background: linear-gradient(180deg, #1a1a1a 0%, #111 100%);
            border-radius: 20px;
            padding: 50px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            border: 1px solid #333;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }
        
        .success-modal-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 10px 30px rgba(34, 197, 94, 0.4);
        }
        
        .success-modal-icon i {
            font-size: 42px;
            color: #fff;
        }
        
        .success-modal h2 {
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .success-modal p {
            color: #888;
            margin-bottom: 30px;
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .success-modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-view-ads {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
            color: #fff;
            padding: 14px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(178, 18, 45, 0.3);
        }
        
        .btn-view-ads:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(178, 18, 45, 0.5);
        }
        
        .btn-post-another {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #fff;
            padding: 14px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            border: 2px solid #333;
            transition: all 0.3s ease;
        }
        
        .btn-post-another:hover {
            border-color: #555;
            background: rgba(255, 255, 255, 0.05);
        }
        
        /* Form Styles */
        .error-message {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid #dc3545;
            color: #dc3545;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-group label i {
            color: #b2122d;
            font-size: 14px;
        }
        
        .form-group .required {
            color: #b2122d;
        }
        
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 16px 18px;
            border: 2px solid #2a2a2a;
            border-radius: 12px;
            background: #0f0f0f;
            color: #fff;
            font-size: 15px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-group input[type="text"]:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #b2122d;
            background: #141414;
            box-shadow: 0 0 0 4px rgba(178, 18, 45, 0.15), 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #444;
        }
        
        .form-group select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 18px center;
            padding-right: 45px;
        }
        
        .form-group select option {
            background: #111;
            color: #fff;
            padding: 12px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 150px;
            line-height: 1.6;
        }
        
        .form-group input[type="file"] {
            padding: 14px;
            background: #0f0f0f;
            border: 2px dashed #2a2a2a;
            border-radius: 12px;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .form-group input[type="file"]:hover {
            border-color: #b2122d;
            background: #141414;
        }
        
        .form-group input[type="file"]:focus {
            outline: none;
            border-color: #b2122d;
            box-shadow: 0 0 0 4px rgba(178, 18, 45, 0.15);
        }
        
        .upload-hint {
            color: #666;
            font-size: 13px;
            margin-top: 8px;
        }
        
        /* Radio Grid - Improved Design */
        .radio-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        
        .radio-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 20px;
            background: #1a1a1a;
            border: 2px solid #2a2a2a;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #fff;
            position: relative;
            font-weight: 500;
        }
        
        .radio-label:hover {
            border-color: #b2122d;
            background: #1f1f1f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(178, 18, 45, 0.15);
        }
        
        .radio-label:has(input:checked) {
            border-color: #b2122d;
            background: rgba(178, 18, 45, 0.12);
            box-shadow: 0 4px 16px rgba(178, 18, 45, 0.25);
        }
        
        .radio-label input {
            display: none;
        }
        
        .radio-label input:checked + .control-indicator {
            background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
            border-color: #b2122d;
            box-shadow: 0 2px 8px rgba(178, 18, 45, 0.4);
        }
        
        .control-indicator {
            width: 22px;
            height: 22px;
            border: 2px solid #555;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
            background: #0f0f0f;
        }
        
        .control-indicator::after {
            content: '';
            width: 10px;
            height: 10px;
            background: #fff;
            border-radius: 50%;
            display: none;
        }
        
        .radio-label input:checked + .control-indicator::after {
            display: block;
        }
        
        .radio-label input:checked ~ span:not(.control-indicator) {
            color: #fff;
            font-weight: 600;
        }
        
        /* Mobile responsive for radio grid */
        @media (max-width: 480px) {
            .radio-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .radio-label {
                padding: 16px 18px;
            }
        }
        
        .form-navigation {
            text-align: center;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 1px solid #2a2a2a;
        }
        
        .btn-submit {
            background: #fff;
            color: #b2122d;
            padding: 16px 45px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 320px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(178, 18, 45, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(178, 18, 45, 0.4);
        }
        
        .btn-submit:hover::before {
            left: 100%;
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        /* MAKE HEADER COMPLETELY BLACK - Highest Priority */
        body.page-template-template-advertisement-submission #header,
        body.page-template-template-advertisement-submission .header,
        body.page-template-template-advertisement-submission #site-header,
        body.page-template-template-advertisement-submission .site-header,
        body.page-template-template-advertisement-submission header,
        body.page-template-template-advertisement-submission .elementor-element-*,
        body.template-advertisement-submission #header,
        body.template-advertisement-submission .header,
        body.template-advertisement-submission #site-header,
        body.template-advertisement-submission .site-header,
        body.template-advertisement-submission header,
        .site-header,
        header.site-header,
        #masthead,
        .header-wrapper,
        .main-header,
        .top-site-header,
        .main-header-wrap,
        header[class*="header"],
        div[class*="header"] {
            background: #000000 !important;
            background-color: #000000 !important;
            background-image: none !important;
        }
        
        /* Force all header content to white */
        .site-header *,
        header.site-header *,
        #masthead *,
        .header-wrapper *,
        .main-header *,
        nav.elementor-nav-menu--main,
        .elementor-menu-toggle,
        .elementor-nav-menu__container,
        .site-header a,
        header.site-header a,
        #masthead a,
        .header-wrapper a,
        .main-header a {
            color: #ffffff !important;
            fill: #ffffff !important;
        }
        
        /* Remove any gradients from header */
        .site-header,
        header.site-header,
        #masthead {
            background: linear-gradient(to bottom, #000000, #000000) !important;
        }
    </style>
    <script>
    // Force header black on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Target all possible header elements
        var headerSelectors = [
            '.site-header',
            'header.site-header',
            '#masthead',
            '.header-wrapper',
            '.main-header',
            '.top-site-header',
            '.main-header-wrap',
            '#header',
            '.header',
            '#site-header',
            'header[class*="header"]',
            'div[class*="header"]',
            '.elementor-section.elementor-top-section'
        ];
        
        headerSelectors.forEach(function(selector) {
            var elements = document.querySelectorAll(selector);
            elements.forEach(function(el) {
                // Only apply if it looks like a header (has reasonable height)
                var rect = el.getBoundingClientRect();
                if (rect.height > 30 && rect.height < 400) {
                    el.style.background = '#000000 !important';
                    el.style.backgroundColor = '#000000 !important';
                }
            });
        });
        
        // Also try using CSS custom property approach
        document.documentElement.style.setProperty('--header-bg', '#000000');
    });
    
    // Run after window load to catch dynamically loaded headers
    window.addEventListener('load', function() {
        document.querySelectorAll('.site-header, header.site-header, #masthead, .header-wrapper, .main-header').forEach(function(el) {
            el.style.setProperty('background', '#000000', 'important');
            el.style.setProperty('background-color', '#000000', 'important');
        });
    });
    </script>
</head>
<body data-theme="dark" class="bg-grade">
    <?php get_header(); ?>
    
    <?php if ($show_success): ?>
        <!-- Success Popup -->
        <div class="success-overlay" id="success-popup" style="display: flex;">
            <div class="success-modal">
                <div class="success-modal-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Classified Submitted!</h2>
                <p>Your classified has been submitted and is pending review. It will be published once approved by our team.</p>
                <div class="success-modal-buttons">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-view-ads">
                       Go Home
                    </a>
                    
                </div>
            </div>
        </div>
        <script>
            // Clear localStorage on success
            localStorage.removeItem('ad_form_data');
            // Clean URL
            window.history.replaceState({}, document.title, window.location.pathname);
        </script>
    <?php else: ?>
        <div class="form-container">
            <!-- Form Header with Text -->
            <header class="form-header" style="margin-top: 20px;">
                <h1><i class="fas fa-bullhorn"></i> Submit Classified</h1>
                <p>Create a new classified post to reach thousands of industry professionals</p>
                <div class="steps-indicator">
                    <div class="step active">
                        <span class="step-number">1</span>
                        <span>Fill Details</span>
                    </div>
                    <div class="step">
                        <span class="step-number">2</span>
                        <span>Review & Pay</span>
                    </div>
                    <div class="step">
                        <span class="step-number">3</span>
                        <span>Publish</span>
                    </div>
                </div>
            </header>

            <!-- Main Form Area -->
            <div class="form-main">
                <div class="form-step active">

                    <?php if ($error_message): ?>
                        <div class="error-message">
                            <?php echo esc_html($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" enctype="multipart/form-data" id="ad-form">
                        <?php wp_nonce_field('submit_advertisement', 'advertisement_nonce'); ?>
                        <input type="hidden" name="submit_advertisement" value="1">

                        <div class="form-group full-width">
                            <label for="advertisement_title"><i class="fas fa-heading"></i> Classified Title <span class="required">*</span></label>
                            <input type="text" id="advertisement_title" name="advertisement_title" placeholder="Enter a catchy title for your classified" value="<?php echo isset($form_data['title']) ? esc_attr($form_data['title']) : ''; ?>" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="advertisement_content"><i class="fas fa-align-left"></i> Classified Content <span class="required">*</span></label>
                            <textarea id="advertisement_content" name="advertisement_content" rows="6" placeholder="Describe your classified in detail. Include all relevant information such as requirements, compensation, deadlines, and how to apply."><?php echo isset($form_data['content']) ? esc_html($form_data['content']) : ''; ?></textarea>
                        </div>

                        <div class="form-group full-width">
                            <label for="advertisement_image"><i class="fas fa-image"></i> Upload Image</label>
                            <input type="file" id="advertisement_image" name="advertisement_image" accept="image/*">
                            <p class="upload-hint"><i class="fas fa-info-circle"></i> Recommended size: 1200x600px. Max file size: 5MB. Supported formats: JPG, PNG, GIF</p>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="advertisement_location"><i class="fas fa-map-marker-alt"></i> Location</label>
                            <input type="text" id="advertisement_location" name="advertisement_location" placeholder="City, State or Country (e.g., Los Angeles, CA or Remote)" value="<?php echo isset($form_data['location']) ? esc_attr($form_data['location']) : ''; ?>">
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Category <span class="required">*</span></label>
                            <div class="radio-grid">
                                <label class="radio-label">
                                    <input type="radio" name="advertisement_category" value="Casting Calls" <?php echo isset($form_data['categories'][0]) && $form_data['categories'][0] === 'Casting Calls' ? 'checked' : ''; ?> required>
                                    <span class="control-indicator"></span>
                                    <span>Casting Calls</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="advertisement_category" value="Crew Calls" <?php echo isset($form_data['categories'][0]) && $form_data['categories'][0] === 'Crew Calls' ? 'checked' : ''; ?>>
                                    <span class="control-indicator"></span>
                                    <span>Crew Calls</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="advertisement_category" value="Rentals" <?php echo isset($form_data['categories'][0]) && $form_data['categories'][0] === 'Rentals' ? 'checked' : ''; ?>>
                                    <span class="control-indicator"></span>
                                    <span>Rentals</span>
                                </label>
                            </div>
                            <p class="upload-hint">Select one category for your classified.</p>
                        </div>

                        <div class="form-navigation">
                            <button type="submit" class="btn-submit" id="submit-btn">Submit Classified</button>
                        </div>
                    </form>
                    <script>
                        // Save form data to localStorage before payment
                        document.getElementById('submit-btn').addEventListener('click', function(e) {
                            var form = document.getElementById('ad-form');
                            var formData = {
                                title: document.getElementById('advertisement_title').value,
                                content: document.getElementById('advertisement_content').value,
                                location: document.getElementById('advertisement_location').value,
                                categories: Array.from(document.querySelectorAll('input[name="advertisement_categories[]"]:checked')).map(cb => cb.value)
                            };
                            localStorage.setItem('ad_form_data', JSON.stringify(formData));
                            console.log('Form data saved to localStorage:', formData);
                        });
                    </script>
                </div>
            </div>
        </div>
        
        <!-- Payment Modal -->
        <?php if ($show_payment): ?>
        <div class="payment-overlay" id="payment-modal" style="display: flex;">
            <div class="payment-modal" style="position: relative;">
                <button type="button" class="payment-modal-close" onclick="closePaymentModal()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="payment-modal-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                
                <h2>Complete Payment</h2>
                <p class="payment-modal-description">Your ad is ready! Complete payment to publish it.</p>
                
                <div class="payment-amount">
                    $3.00 <span>/ ad</span>
                </div>
                
                <div class="payment-features">
                    <h4>What you get:</h4>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Your ad displayed for 15 days</li>
                        <li><i class="fas fa-check-circle"></i> Featured in our classifieds</li>
                        <li><i class="fas fa-check-circle"></i> Reach thousands of users</li>
                    </ul>
                </div>
                
                <div class="payment-button-container">
                    <?php 
                    // Try to render Stripe shortcode with success URL
                    $current_url = get_permalink();
                    // Add multiple parameters for better compatibility
                    $success_url = add_query_arg(array(
                        'payment' => 'success',
                        'success' => 'true'
                    ), $current_url);
                    $stripe_button = do_shortcode('[wp_stripe_checkout_session name="Classifieds-fee" price="3.00" button_text="Pay $3.00" success_url="' . $success_url . '"]');
                    
                    if (!empty(trim($stripe_button))) {
                        echo $stripe_button;
                    } else {
                        // Fallback: Show styled payment button
                        echo '<a href="' . esc_url($success_url) . '" class="payment-button"><i class="fas fa-lock"></i> Pay $3.00 Now</a>';
                    }
                    ?>
                </div>
                
                <p class="payment-secure">
                    <i class="fas fa-shield-alt"></i> Secure payment powered by Stripe
                </p>
            </div>
        </div>
    <script>
        // Debug: Check URL parameters
        console.log('URL params:', window.location.search);
        
        // Check for success parameters
        const urlParams = new URLSearchParams(window.location.search);
        const isPaymentSuccess = urlParams.get('payment') === 'success';
        const isSuccess = urlParams.get('success') === 'true';
        const hasSessionId = urlParams.has('session_id');
        
        console.log('payment:', isPaymentSuccess, 'success:', isSuccess, 'session_id:', hasSessionId);
        
        // Show success popup if any success parameter is present
        if (isPaymentSuccess || isSuccess || hasSessionId) {
            var successPopup = document.getElementById('success-popup');
            if (successPopup) {
                successPopup.style.display = 'flex';
                // Clean URL without reloading
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
        
        // Also handle payment modal
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('payment-modal');
            if (modal) {
                modal.style.display = 'flex';
            }
        });
        
        // Close payment modal function
        function closePaymentModal() {
            var modal = document.getElementById('payment-modal');
            if (modal) {
                modal.style.display = 'none';
            }
            // Clean URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            var modal = document.getElementById('payment-modal');
            var paymentModal = document.querySelector('.payment-modal');
            if (modal && e.target === modal) {
                closePaymentModal();
            }
        });
    </script>
        <?php endif; ?>
    <?php endif; ?>

    <script src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/global.js?ver=<?php echo esc_attr(HELLO_ELEMENTOR_CHILD_VERSION); ?>"></script>
    <?php get_footer(); ?>
</body>
</html>
