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
$current_user = wp_get_current_user();
$last_submission_key = 'last_ad_submission_' . $user_id;
$last_submission = get_user_meta($user_id, $last_submission_key, true);

$error_message = '';
if ($last_submission && (time() - $last_submission) < 180) { // 3 minutes cooldown
    $error_message = 'Please wait a moment before submitting another classified advertisement.';
}

$show_payment = false;
$form_data = array();
$show_success = false;

// Check if returning from payment
$payment_success = isset($_GET['payment']) && $_GET['payment'] === 'success';
$url_success = isset($_GET['success']) && $_GET['success'] === 'true';
$stripe_session = isset($_GET['session_id']) && !empty($_GET['session_id']);

// Check membership ad access
$has_paid = false;
if (!function_exists('has_membership_plan')) {
    require_once get_stylesheet_directory() . '/includes/access-control.php';
}
ensure_user_plan_tracking();
migrate_user_current_level_to_plans();

$plans_with_ad_access = [SIX_MONTH_PLAN_LEVEL, ONE_YEAR_PLAN_LEVEL, ADVERTISEMENT_PLAN_LEVEL];
foreach ($plans_with_ad_access as $plan_level) {
    if (has_membership_plan($plan_level)) {
        $has_paid = true;
        break;
    }
}

if ($payment_success || $stripe_session) {
    $has_paid = true;
}

if (!$has_paid) {
    $paid_ad_posts = get_posts(array(
        'post_type' => 'advertisement',
        'author' => $current_user->ID,
        'posts_per_page' => 1,
        'meta_key' => '_classifieds_fee_paid',
        'meta_value' => '1'
    ));

    foreach ($paid_ad_posts as $ad) {
        $post_date = strtotime($ad->post_date);
        $days_since_post = (time() - $post_date) / (60 * 60 * 24);
        if ($days_since_post <= 15) {
            $has_paid = true;
            break;
        }
    }
}

/**
 * Validates uploaded image file
 */
function validate_uploaded_ad_image() {
    if (!isset($_FILES['advertisement_image']) || $_FILES['advertisement_image']['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $file = $_FILES['advertisement_image'];
    $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
    $file_info = @getimagesize($file['tmp_name']);
    
    if ($file_info === false) {
        return false;
    }
    
    $mime_type = $file_info['mime'];
    if (!in_array($mime_type, $allowed_types)) {
        return false;
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    if (!in_array($file_extension, $allowed_extensions)) {
        return false;
    }
    
    if ($file['size'] > 6 * 1024 * 1024) {
        return false;
    }
    
    return true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_advertisement'])) {
    if (!isset($_POST['advertisement_nonce']) || !wp_verify_nonce($_POST['advertisement_nonce'], 'submit_advertisement')) {
        $error_message = 'Security token expired. Please try refreshing and submitting again.';
    } elseif (empty($_POST['advertisement_title'])) {
        $error_message = 'Advertisement title is required.';
    } elseif (strlen($_POST['advertisement_title']) > 200) {
        $error_message = 'Advertisement title must be less than 200 characters.';
    } elseif (empty($_POST['advertisement_content'])) {
        $error_message = 'Advertisement description is required.';
    } elseif (strlen($_POST['advertisement_content']) > 5000) {
        $error_message = 'Advertisement description must be less than 5000 characters.';
    } elseif (!empty($_FILES['advertisement_image']['name']) && !validate_uploaded_ad_image()) {
        $error_message = 'Invalid image file. Please upload a valid JPG, PNG, WEBP or GIF under 6MB.';
    } else {
        $title = sanitize_text_field($_POST['advertisement_title']);
        $content = wp_kses_post($_POST['advertisement_content']);
        $location = sanitize_text_field($_POST['advertisement_location'] ?? '');
        $selected_cat = sanitize_text_field($_POST['advertisement_category'] ?? '');
        $target_domain = sanitize_text_field($_POST['target_domain'] ?? '');
        $target_role = sanitize_text_field($_POST['target_role'] ?? '');
        $target_gender = sanitize_text_field($_POST['target_gender'] ?? 'all');
        $target_age_group = sanitize_text_field($_POST['target_age_group'] ?? 'all');
        $target_experience = sanitize_text_field($_POST['target_experience'] ?? 'all');
        $budget = sanitize_text_field($_POST['advertisement_budget'] ?? '');
        $contact_email = sanitize_email($_POST['advertisement_contact_email'] ?? $current_user->user_email);
        $contact_phone = sanitize_text_field($_POST['advertisement_contact_phone'] ?? '');
        
        if (strpos($content, '<script') !== false || strpos($content, 'javascript:') !== false) {
            $error_message = 'Content contains invalid or disallowed script tags.';
        } else {
            $form_data = array(
                'title' => $title,
                'content' => $content,
                'location' => $location,
                'categories' => $selected_cat ? array($selected_cat) : array(),
                'target_domain' => $target_domain,
                'target_role' => $target_role,
                'target_gender' => $target_gender,
                'target_age_group' => $target_age_group,
                'target_experience' => $target_experience,
                'budget' => $budget,
                'contact_email' => $contact_email,
                'contact_phone' => $contact_phone,
            );

            if (!$has_paid) {
                $show_payment = true;
                $_SESSION['ad_form_data'] = $form_data;
            } else {
                $advertisement_post = array(
                    'post_title'   => $form_data['title'],
                    'post_content' => $form_data['content'],
                    'post_status'  => 'pending',
                    'post_type'    => 'advertisement',
                    'post_author'  => $current_user->ID,
                );

                $post_id = wp_insert_post($advertisement_post);

                if (is_wp_error($post_id)) {
                    $error_message = $post_id->get_error_message();
                } else {
                    update_post_meta($post_id, '_classifieds_fee_paid', '1');
                    update_post_meta($post_id, '_classifieds_payment_date', current_time('mysql'));
                    
                    if (!empty($_FILES['advertisement_image']['name'])) {
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                        require_once(ABSPATH . 'wp-admin/includes/media.php');

                        $attachment_id = media_handle_upload('advertisement_image', $post_id);
                        if (!is_wp_error($attachment_id)) {
                            set_post_thumbnail($post_id, $attachment_id);
                        }
                    }
                    
                    if (!empty($form_data['categories'])) {
                        wp_set_object_terms($post_id, $form_data['categories'], 'advertisement_category');
                    }
                    
                    if (!empty($form_data['location'])) {
                        update_post_meta($post_id, 'ad_location', $form_data['location']);
                        update_post_meta($post_id, '_advertisement_location', $form_data['location']);
                        update_post_meta($post_id, '_target_location', $form_data['location']);
                    }
                    if (!empty($form_data['target_domain'])) {
                        update_post_meta($post_id, '_target_domains', array($form_data['target_domain']));
                    }
                    if (!empty($form_data['target_role'])) {
                        update_post_meta($post_id, '_target_roles', array($form_data['target_role']));
                    }
                    if (!empty($form_data['target_gender'])) {
                        update_post_meta($post_id, '_target_gender', $form_data['target_gender']);
                    }
                    if (!empty($form_data['target_age_group'])) {
                        update_post_meta($post_id, '_target_age_group', $form_data['target_age_group']);
                    }
                    if (!empty($form_data['target_experience'])) {
                        update_post_meta($post_id, '_target_experience', $form_data['target_experience']);
                    }
                    if (!empty($form_data['budget'])) {
                        update_post_meta($post_id, '_advertisement_budget', $form_data['budget']);
                    }
                    if (!empty($form_data['contact_email'])) {
                        update_post_meta($post_id, '_advertisement_contact_email', $form_data['contact_email']);
                    }
                    if (!empty($form_data['contact_phone'])) {
                        update_post_meta($post_id, '_advertisement_contact_phone', $form_data['contact_phone']);
                    }

                    $show_success = true;
                    update_user_meta($current_user->ID, $last_submission_key, time());
                }
            }
        }
    }
}

// Handle returning from external checkout
if ($payment_success || $stripe_session || $url_success) {
    $show_success = true;
    if (isset($_SESSION['ad_form_data']) && !empty($_SESSION['ad_form_data'])) {
        $saved_form_data = $_SESSION['ad_form_data'];
        $advertisement_post = array(
            'post_title'   => $saved_form_data['title'],
            'post_content' => $saved_form_data['content'],
            'post_status'  => 'pending',
            'post_type'    => 'advertisement',
            'post_author'  => $current_user->ID,
        );

        $post_id = wp_insert_post($advertisement_post);
        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, '_classifieds_fee_paid', '1');
            update_post_meta($post_id, '_classifieds_payment_date', current_time('mysql'));
            
            if (!empty($saved_form_data['categories'])) {
                wp_set_object_terms($post_id, $saved_form_data['categories'], 'advertisement_category');
            }
            if (!empty($saved_form_data['location'])) {
                update_post_meta($post_id, 'ad_location', $saved_form_data['location']);
                update_post_meta($post_id, '_advertisement_location', $saved_form_data['location']);
                update_post_meta($post_id, '_target_location', $saved_form_data['location']);
            }
            if (!empty($saved_form_data['target_domain'])) {
                update_post_meta($post_id, '_target_domains', array($saved_form_data['target_domain']));
            }
            if (!empty($saved_form_data['target_role'])) {
                update_post_meta($post_id, '_target_roles', array($saved_form_data['target_role']));
            }
            if (!empty($saved_form_data['target_gender'])) {
                update_post_meta($post_id, '_target_gender', $saved_form_data['target_gender']);
            }
            if (!empty($saved_form_data['target_age_group'])) {
                update_post_meta($post_id, '_target_age_group', $saved_form_data['target_age_group']);
            }
            if (!empty($saved_form_data['target_experience'])) {
                update_post_meta($post_id, '_target_experience', $saved_form_data['target_experience']);
            }
            if (!empty($saved_form_data['budget'])) {
                update_post_meta($post_id, '_advertisement_budget', $saved_form_data['budget']);
            }
            if (!empty($saved_form_data['contact_email'])) {
                update_post_meta($post_id, '_advertisement_contact_email', $saved_form_data['contact_email']);
            }
            if (!empty($saved_form_data['contact_phone'])) {
                update_post_meta($post_id, '_advertisement_contact_phone', $saved_form_data['contact_phone']);
            }
        }
        unset($_SESSION['ad_form_data']);
        update_user_meta($current_user->ID, $last_submission_key, time());
    }
}

// Enqueue styles
wp_enqueue_style('hello-elementor-child-form-style', get_stylesheet_directory_uri() . '/form-style.css', array(), HELLO_ELEMENTOR_CHILD_VERSION);

get_header();
?>

<main class="velvet-form-page advertisement-submission-page">
    <div class="velvet-form-container">
        
        <!-- Header & Breadcrumbs -->
        <header class="velvet-form-header">
            <div class="velvet-form-badge">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                VelvetReel Classifieds
            </div>
            <h1>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Post Your Advertisement
            </h1>
            <p>Publish your casting call, crew requirement, or equipment rental listing to thousands of industry professionals.</p>

            <!-- Steps Indicator -->
            <div class="velvet-form-steps">
                <div class="velvet-form-step-item active">
                    <span class="velvet-form-step-num">1</span>
                    <span>Ad Details & Category</span>
                </div>
                <div class="velvet-form-step-item <?php echo $has_paid ? 'completed' : ''; ?>">
                    <span class="velvet-form-step-num">2</span>
                    <span>Review & Membership</span>
                </div>
                <div class="velvet-form-step-item">
                    <span class="velvet-form-step-num">3</span>
                    <span>Live Moderation</span>
                </div>
            </div>
        </header>

        <!-- Main Form Card -->
        <div class="velvet-form-card">
            <?php if (!empty($error_message)): ?>
                <div class="velvet-alert error">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div><?php echo esc_html($error_message); ?></div>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" id="velvetAdForm">
                <?php wp_nonce_field('submit_advertisement', 'advertisement_nonce'); ?>
                <input type="hidden" name="submit_advertisement" value="1">

                <!-- Title Field -->
                <div class="velvet-form-group">
                    <label class="velvet-form-label" for="advertisement_title">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h16v3M9 20h6M12 4v16"/></svg>
                        Classified Title <span class="required">*</span>
                    </label>
                    <input type="text" id="advertisement_title" name="advertisement_title" class="velvet-input" placeholder="e.g. Lead Female Actor Needed for Feature Film / RED V-Raptor Kit for Rent" value="<?php echo isset($form_data['title']) ? esc_attr($form_data['title']) : ''; ?>" maxlength="200" required>
                    <div class="velvet-form-hint">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        Clear, concise titles receive up to 3x more views and responses.
                    </div>
                </div>

                <!-- Category Field (Interactive Tiles) -->
                <div class="velvet-form-group">
                    <label class="velvet-form-label">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        Select Category <span class="required">*</span>
                    </label>
                    <?php 
                    $curr_cat = isset($form_data['categories'][0]) ? $form_data['categories'][0] : 'Casting Calls';
                    ?>
                    <div class="velvet-choice-grid">
                        <label class="velvet-choice-card <?php echo $curr_cat === 'Casting Calls' ? 'selected' : ''; ?>">
                            <input type="radio" name="advertisement_category" value="Casting Calls" <?php checked($curr_cat, 'Casting Calls'); ?> required>
                            <div class="velvet-choice-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a5 5 0 0 0-5 5v3a5 5 0 0 0 10 0V7a5 5 0 0 0-5-5z"></path><path d="M17 14h.01"></path><path d="M7 14h.01"></path><path d="M12 18h.01"></path><path d="M2 22a10 10 0 0 1 20 0H2z"></path></svg>
                            </div>
                            <span class="velvet-choice-title">Casting Calls</span>
                        </label>

                        <label class="velvet-choice-card <?php echo $curr_cat === 'Crew Calls' ? 'selected' : ''; ?>">
                            <input type="radio" name="advertisement_category" value="Crew Calls" <?php checked($curr_cat, 'Crew Calls'); ?>>
                            <div class="velvet-choice-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            </div>
                            <span class="velvet-choice-title">Crew Calls</span>
                        </label>

                        <label class="velvet-choice-card <?php echo $curr_cat === 'Rentals' ? 'selected' : ''; ?>">
                            <input type="radio" name="advertisement_category" value="Rentals" <?php checked($curr_cat, 'Rentals'); ?>>
                            <div class="velvet-choice-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2.5"></rect><rect x="2" y="14" width="20" height="8" rx="2.5"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                            </div>
                            <span class="velvet-choice-title">Rentals & Gear</span>
                        </label>
                    </div>
                </div>

                <!-- Target Audience & Smart Matching Criteria -->
                <div style="background: rgba(254, 17, 75, 0.04); border: 1px solid rgba(254, 17, 75, 0.15); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#FE114B" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #ffffff;">Target Audience & Smart Matching (Optional)</h3>
                    </div>
                    <p style="margin: 0 0 16px 0; font-size: 13px; color: #a1a1aa; line-height: 1.5;">
                        Specify your target candidate criteria so VelvetReel can automatically match and notify relevant talent profiles via targeted email upon publishing.
                    </p>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                        <!-- Target Domain / Discipline -->
                        <div class="velvet-form-group" style="margin-bottom: 0;">
                            <label class="velvet-form-label" for="target_domain" style="font-size: 13px;">Target Discipline</label>
                            <select id="target_domain" name="target_domain" class="velvet-input">
                                <option value="">All Disciplines / Open</option>
                                <option value="Fashion & Design" <?php selected($form_data['target_domain'] ?? '', 'Fashion & Design'); ?>>Fashion & Design</option>
                                <option value="Film & Creative Arts" <?php selected($form_data['target_domain'] ?? '', 'Film & Creative Arts'); ?>>Film & Creative Arts</option>
                                <option value="Talent Coach" <?php selected($form_data['target_domain'] ?? '', 'Talent Coach'); ?>>Talent Coach / Trainer</option>
                            </select>
                        </div>

                        <!-- Target Role -->
                        <div class="velvet-form-group" style="margin-bottom: 0;">
                            <label class="velvet-form-label" for="target_role" style="font-size: 13px;">Target Specific Role</label>
                            <select id="target_role" name="target_role" class="velvet-input">
                                <option value="">All Roles / Open</option>
                                <optgroup label="Acting & Performance">
                                    <option value="Actor/Actress" <?php selected($form_data['target_role'] ?? '', 'Actor/Actress'); ?>>Actor / Actress</option>
                                    <option value="Voice Actor" <?php selected($form_data['target_role'] ?? '', 'Voice Actor'); ?>>Voice Actor</option>
                                    <option value="Dancer/Choreographer" <?php selected($form_data['target_role'] ?? '', 'Dancer/Choreographer'); ?>>Dancer / Choreographer</option>
                                    <option value="Singer" <?php selected($form_data['target_role'] ?? '', 'Singer'); ?>>Singer / Vocalist</option>
                                </optgroup>
                                <optgroup label="Modeling & Fashion">
                                    <option value="Fashion Model" <?php selected($form_data['target_role'] ?? '', 'Fashion Model'); ?>>Fashion Model</option>
                                    <option value="Fashion Stylist" <?php selected($form_data['target_role'] ?? '', 'Fashion Stylist'); ?>>Fashion Stylist</option>
                                    <option value="Fashion Designer" <?php selected($form_data['target_role'] ?? '', 'Fashion Designer'); ?>>Fashion Designer</option>
                                    <option value="Makeup Artist" <?php selected($form_data['target_role'] ?? '', 'Makeup Artist'); ?>>Makeup Artist</option>
                                    <option value="Ramp Choreographer" <?php selected($form_data['target_role'] ?? '', 'Ramp Choreographer'); ?>>Ramp Choreographer</option>
                                </optgroup>
                                <optgroup label="Crew & Production">
                                    <option value="Director" <?php selected($form_data['target_role'] ?? '', 'Director'); ?>>Director</option>
                                    <option value="Assistant Director" <?php selected($form_data['target_role'] ?? '', 'Assistant Director'); ?>>Assistant Director</option>
                                    <option value="DOP/Camera Crew" <?php selected($form_data['target_role'] ?? '', 'DOP/Camera Crew'); ?>>DOP / Camera Crew</option>
                                    <option value="Editor/VFX Artist" <?php selected($form_data['target_role'] ?? '', 'Editor/VFX Artist'); ?>>Editor / VFX Artist</option>
                                    <option value="Screenwriter" <?php selected($form_data['target_role'] ?? '', 'Screenwriter'); ?>>Screenwriter</option>
                                    <option value="Music Director" <?php selected($form_data['target_role'] ?? '', 'Music Director'); ?>>Music Director</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Target Gender -->
                        <div class="velvet-form-group" style="margin-bottom: 0;">
                            <label class="velvet-form-label" for="target_gender" style="font-size: 13px;">Target Gender</label>
                            <select id="target_gender" name="target_gender" class="velvet-input">
                                <option value="all" <?php selected($form_data['target_gender'] ?? 'all', 'all'); ?>>All / Any Gender</option>
                                <option value="female" <?php selected($form_data['target_gender'] ?? '', 'female'); ?>>Female</option>
                                <option value="male" <?php selected($form_data['target_gender'] ?? '', 'male'); ?>>Male</option>
                                <option value="non-binary" <?php selected($form_data['target_gender'] ?? '', 'non-binary'); ?>>Non-Binary / Other</option>
                            </select>
                        </div>

                        <!-- Target Age Range -->
                        <div class="velvet-form-group" style="margin-bottom: 0;">
                            <label class="velvet-form-label" for="target_age_group" style="font-size: 13px;">Target Age Range</label>
                            <select id="target_age_group" name="target_age_group" class="velvet-input">
                                <option value="all" <?php selected($form_data['target_age_group'] ?? 'all', 'all'); ?>>All Age Groups</option>
                                <option value="Under 18" <?php selected($form_data['target_age_group'] ?? '', 'Under 18'); ?>>Kids / Teens (Under 18)</option>
                                <option value="18-25" <?php selected($form_data['target_age_group'] ?? '', '18-25'); ?>>18 - 25 Years</option>
                                <option value="26-35" <?php selected($form_data['target_age_group'] ?? '', '26-35'); ?>>26 - 35 Years</option>
                                <option value="36-50" <?php selected($form_data['target_age_group'] ?? '', '36-50'); ?>>36 - 50 Years</option>
                                <option value="50+" <?php selected($form_data['target_age_group'] ?? '', '50+'); ?>>50+ Years</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Compensation & Official Contact -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    <!-- Budget / Compensation -->
                    <div class="velvet-form-group" style="margin-bottom: 0;">
                        <label class="velvet-form-label" for="advertisement_budget">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                            Compensation / Budget
                        </label>
                        <input type="text" id="advertisement_budget" name="advertisement_budget" class="velvet-input" placeholder="e.g. ₹25,000 / Day or Paid Project / Negotiable" value="<?php echo isset($form_data['budget']) ? esc_attr($form_data['budget']) : ''; ?>">
                    </div>

                    <!-- Contact Email -->
                    <div class="velvet-form-group" style="margin-bottom: 0;">
                        <label class="velvet-form-label" for="advertisement_contact_email">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            Inquiry / Contact Email <span class="required">*</span>
                        </label>
                        <input type="email" id="advertisement_contact_email" name="advertisement_contact_email" class="velvet-input" placeholder="e.g. casting@production.com" value="<?php echo isset($form_data['contact_email']) ? esc_attr($form_data['contact_email']) : esc_attr($current_user->user_email); ?>" required>
                    </div>

                    <!-- Contact Phone -->
                    <div class="velvet-form-group" style="margin-bottom: 0;">
                        <label class="velvet-form-label" for="advertisement_contact_phone">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            Contact Phone / WhatsApp
                        </label>
                        <input type="text" id="advertisement_contact_phone" name="advertisement_contact_phone" class="velvet-input" placeholder="e.g. +91 98765 43210 (Optional)" value="<?php echo isset($form_data['contact_phone']) ? esc_attr($form_data['contact_phone']) : ''; ?>">
                    </div>
                </div>

                <!-- Location Field -->
                <div class="velvet-form-group">
                    <label class="velvet-form-label" for="advertisement_location">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        Location / Shoot City
                    </label>
                    <input type="text" id="advertisement_location" name="advertisement_location" class="velvet-input" placeholder="e.g. Mumbai, Maharashtra or London, UK or Remote" value="<?php echo isset($form_data['location']) ? esc_attr($form_data['location']) : ''; ?>">
                </div>

                <!-- Description / Content Field -->
                <div class="velvet-form-group">
                    <label class="velvet-form-label" for="advertisement_content">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="3" y2="18"></line></svg>
                        Advertisement Description & Requirements <span class="required">*</span>
                    </label>
                    <textarea id="advertisement_content" name="advertisement_content" class="velvet-textarea" rows="7" placeholder="Provide full details regarding role criteria, project timeline, remuneration/rates, shoot dates, contact instructions or submission links..." required><?php echo isset($form_data['content']) ? esc_html($form_data['content']) : ''; ?></textarea>
                </div>

                <!-- Media Upload Field with Live Preview -->
                <div class="velvet-form-group">
                    <label class="velvet-form-label">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        Featured Ad Artwork / Poster
                    </label>
                    <div class="velvet-dropzone" id="velvetDropzone">
                        <input type="file" id="advertisement_image" name="advertisement_image" class="velvet-file-input" accept="image/jpeg,image/png,image/webp,image/gif">
                        <div class="velvet-dropzone-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        </div>
                        <div class="velvet-dropzone-title">Click or drag & drop poster artwork</div>
                        <div class="velvet-dropzone-desc">JPG, PNG, WEBP or GIF (Max 6MB, 1200x630px recommended)</div>
                    </div>

                    <!-- Image Preview Container -->
                    <div class="velvet-image-preview-box" id="imagePreviewBox">
                        <img id="imagePreviewImg" src="" alt="Ad Artwork Preview">
                        <button type="button" class="velvet-preview-remove-btn" id="removeImageBtn" title="Remove Image">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="velvet-form-actions">
                    <a href="<?php echo esc_url(get_post_type_archive_link('advertisement')); ?>" class="velvet-btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="velvet-btn-primary" id="velvetSubmitBtn">
                        <span>Submit Advertisement</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Modal (Triggered if un-paid & submitting) -->
    <?php if ($show_payment): ?>
    <div class="velvet-modal-backdrop" id="velvetPaymentModal">
        <div class="velvet-modal-card">
            <button type="button" class="velvet-modal-close-btn" onclick="closeVelvetModal('velvetPaymentModal')">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="velvet-modal-icon-badge payment">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
            </div>
            <h2>Publish Advertisement</h2>
            <p>Publish your ad listing across the VelvetReel community network.</p>

            <div style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 20px; margin-bottom: 24px; text-align: left;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="color: #a1a1aa; font-size: 14px;">15-Day Featured Classified</span>
                    <span style="color: #fff; font-size: 20px; font-weight: 700;">$3.00</span>
                </div>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 13px; color: #d4d4d8; display: flex; flex-direction: column; gap: 8px;">
                    <li style="display: flex; align-items: center; gap: 8px;"><span style="color: #DF1D3D;">✓</span> Active 15-day featured listing</li>
                    <li style="display: flex; align-items: center; gap: 8px;"><span style="color: #DF1D3D;">✓</span> Search & category discovery</li>
                    <li style="display: flex; align-items: center; gap: 8px;"><span style="color: #DF1D3D;">✓</span> Direct messaging & candidate reach</li>
                </ul>
            </div>

            <div class="payment-action-group">
                <?php 
                $success_url = add_query_arg(array('payment' => 'success', 'success' => 'true'), get_permalink());
                $stripe_btn = do_shortcode('[wp_stripe_checkout_session name="Classifieds-fee" price="3.00" button_text="Pay $3.00 & Publish" success_url="' . esc_url($success_url) . '"]');
                if (!empty(trim($stripe_btn))) {
                    echo $stripe_btn;
                } else {
                    echo '<a href="' . esc_url($success_url) . '" class="velvet-btn-primary" style="width: 100%; box-sizing: border-box;">Pay $3.00 with Stripe</a>';
                }
                ?>
            </div>
            
            <div style="margin-top: 16px; font-size: 12px; color: #71717a; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                Secure 256-bit encrypted payment
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Success Confirmation Modal -->
    <?php if ($show_success): ?>
    <div class="velvet-modal-backdrop" id="velvetSuccessModal">
        <div class="velvet-modal-card">
            <div class="velvet-modal-icon-badge success">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
            <h2>Advertisement Submitted!</h2>
            <p>Your classified advertisement has been submitted successfully and is currently in review. It will be published as soon as approved by our moderation team.</p>

            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo esc_url(get_post_type_archive_link('advertisement')); ?>" class="velvet-btn-primary">
                    View Classifieds
                </a>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="velvet-btn-secondary">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Interactive Category Tiles Selection
    const categoryCards = document.querySelectorAll('.velvet-choice-card');
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            categoryCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

    // Image Upload & Live Preview
    const fileInput = document.getElementById('advertisement_image');
    const dropzone = document.getElementById('velvetDropzone');
    const previewBox = document.getElementById('imagePreviewBox');
    const previewImg = document.getElementById('imagePreviewImg');
    const removeBtn = document.getElementById('removeImageBtn');

    if (fileInput && previewBox && previewImg) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    previewImg.src = evt.target.result;
                    previewBox.style.display = 'block';
                    if (dropzone) dropzone.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                fileInput.value = '';
                previewImg.src = '';
                previewBox.style.display = 'none';
                if (dropzone) dropzone.style.display = 'block';
            });
        }
    }

    // Drag & Drop Styling
    if (dropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
        });
    }

    // Cache form data before payment redirect
    const submitBtn = document.getElementById('velvetSubmitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
            const form = document.getElementById('velvetAdForm');
            if (form && form.checkValidity()) {
                const checkedCat = document.querySelector('input[name="advertisement_category"]:checked');
                const formData = {
                    title: document.getElementById('advertisement_title')?.value || '',
                    content: document.getElementById('advertisement_content')?.value || '',
                    location: document.getElementById('advertisement_location')?.value || '',
                    categories: checkedCat ? [checkedCat.value] : [],
                    target_domain: document.getElementById('target_domain')?.value || '',
                    target_role: document.getElementById('target_role')?.value || '',
                    target_gender: document.getElementById('target_gender')?.value || 'all',
                    target_age_group: document.getElementById('target_age_group')?.value || 'all',
                    budget: document.getElementById('advertisement_budget')?.value || '',
                    contact_email: document.getElementById('advertisement_contact_email')?.value || '',
                    contact_phone: document.getElementById('advertisement_contact_phone')?.value || ''
                };
                localStorage.setItem('velvet_ad_form_data', JSON.stringify(formData));
            }
        });
    }
});

function closeVelvetModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'none';
}
</script>

<?php
get_footer();
