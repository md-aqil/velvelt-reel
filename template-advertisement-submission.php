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
        
        <!-- Header & Steps Indicator -->
        <header class="velvet-form-header">
            <div class="velvet-form-badge">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                The VelvetReel Classifieds
            </div>
            <h1>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                Post Your Advertisement
            </h1>
            <p>Publish your casting call, crew requirement, or equipment rental listing to thousands of verified industry professionals.</p>

            <?php if (!$show_success): ?>
            <!-- 2-Step Navigation Tabs -->
            <div class="velvet-form-steps">
                <button type="button" class="velvet-form-step-item <?php echo $show_payment ? '' : 'active'; ?>" id="stepTab1" onclick="switchAdStep(1)">
                    <span class="velvet-form-step-num">1</span>
                    <span class="velvet-form-step-text">Ad Details & Category</span>
                </button>
                <button type="button" class="velvet-form-step-item <?php echo $show_payment ? 'active' : ''; ?>" id="stepTab2" onclick="switchAdStep(2)">
                    <span class="velvet-form-step-num">2</span>
                    <span class="velvet-form-step-text">Review & Membership</span>
                </button>
            </div>
            <?php endif; ?>
        </header>

        <!-- Main Form Card -->
        <div class="velvet-form-card">
            <?php if (!empty($error_message)): ?>
                <div class="velvet-alert error">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div><?php echo esc_html($error_message); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($show_success): ?>
                <!-- In-Page Success State -->
                <div class="velvet-success-state-box">
                    <div class="velvet-success-icon-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h2>Advertisement Submitted!</h2>
                    <p>Your classified advertisement has been submitted successfully and is currently under review by our moderation team. It will be published to the directory shortly.</p>

                    <div class="velvet-success-actions">
                        <a href="<?php echo esc_url(get_post_type_archive_link('advertisement')); ?>" class="velvet-btn-primary">
                            <span>View All Classifieds</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="velvet-btn-secondary">
                            Post Another Advertisement
                        </a>
                    </div>
                </div>
            <?php else: ?>

            <form method="post" enctype="multipart/form-data" id="velvetAdForm">
                <?php wp_nonce_field('submit_advertisement', 'advertisement_nonce'); ?>
                <input type="hidden" name="submit_advertisement" value="1">

                <!-- STEP 1 PANE: Ad Details & Category -->
                <div class="velvet-step-pane <?php echo $show_payment ? '' : 'active'; ?>" id="stepPane1">
                    
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
                    <div class="velvet-audience-match-box">
                        <div class="velvet-audience-match-header">
                            <div class="velvet-audience-icon-wrap">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
                            </div>
                            <div>
                                <h3 class="velvet-audience-title">Target Audience & Smart Matching</h3>
                                <p class="velvet-audience-desc">
                                    Specify your target criteria so VelvetReel can automatically match and notify verified talents matching your listing.
                                </p>
                            </div>
                        </div>

                        <div class="velvet-grid-2col">
                            <!-- Target Domain / Discipline -->
                            <div class="velvet-form-group">
                                <label class="velvet-form-label" for="target_domain">Target Discipline</label>
                                <select id="target_domain" name="target_domain" class="velvet-select">
                                    <option value="">All Disciplines / Open</option>
                                    <option value="Fashion & Design" <?php selected($form_data['target_domain'] ?? '', 'Fashion & Design'); ?>>Fashion & Design</option>
                                    <option value="Film & Creative Arts" <?php selected($form_data['target_domain'] ?? '', 'Film & Creative Arts'); ?>>Film & Creative Arts</option>
                                    <option value="Talent Coach" <?php selected($form_data['target_domain'] ?? '', 'Talent Coach'); ?>>Talent Coach / Trainer</option>
                                </select>
                            </div>

                            <!-- Target Role -->
                            <div class="velvet-form-group">
                                <label class="velvet-form-label" for="target_role">Target Specific Role</label>
                                <select id="target_role" name="target_role" class="velvet-select">
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
                            <div class="velvet-form-group">
                                <label class="velvet-form-label" for="target_gender">Target Gender</label>
                                <select id="target_gender" name="target_gender" class="velvet-select">
                                    <option value="all" <?php selected($form_data['target_gender'] ?? 'all', 'all'); ?>>All / Any Gender</option>
                                    <option value="female" <?php selected($form_data['target_gender'] ?? '', 'female'); ?>>Female</option>
                                    <option value="male" <?php selected($form_data['target_gender'] ?? '', 'male'); ?>>Male</option>
                                    <option value="non-binary" <?php selected($form_data['target_gender'] ?? '', 'non-binary'); ?>>Non-Binary / Other</option>
                                </select>
                            </div>

                            <!-- Target Age Range -->
                            <div class="velvet-form-group">
                                <label class="velvet-form-label" for="target_age_group">Target Age Range</label>
                                <select id="target_age_group" name="target_age_group" class="velvet-select">
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

                    <!-- Compensation & Contact Info -->
                    <div class="velvet-grid-2col">
                        <!-- Budget / Compensation -->
                        <div class="velvet-form-group">
                            <label class="velvet-form-label" for="advertisement_budget">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                Compensation / Budget
                            </label>
                            <input type="text" id="advertisement_budget" name="advertisement_budget" class="velvet-input" placeholder="e.g. $500 / Day or Paid Project / Negotiable" value="<?php echo isset($form_data['budget']) ? esc_attr($form_data['budget']) : ''; ?>">
                        </div>

                        <!-- Location Field -->
                        <div class="velvet-form-group">
                            <label class="velvet-form-label" for="advertisement_location">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                Location / Shoot City
                            </label>
                            <input type="text" id="advertisement_location" name="advertisement_location" class="velvet-input" placeholder="e.g. 101 Hudson St., Jersey City, NJ 07304 or Remote" value="<?php echo isset($form_data['location']) ? esc_attr($form_data['location']) : ''; ?>">
                        </div>
                    </div>

                    <div class="velvet-grid-2col">
                        <!-- Contact Email -->
                        <div class="velvet-form-group">
                            <label class="velvet-form-label" for="advertisement_contact_email">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                Inquiry / Contact Email <span class="required">*</span>
                            </label>
                            <input type="email" id="advertisement_contact_email" name="advertisement_contact_email" class="velvet-input" placeholder="e.g. casting@production.com" value="<?php echo isset($form_data['contact_email']) ? esc_attr($form_data['contact_email']) : esc_attr($current_user->user_email); ?>" required>
                        </div>

                        <!-- Contact Phone -->
                        <div class="velvet-form-group">
                            <label class="velvet-form-label" for="advertisement_contact_phone">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                Contact Phone (Optional)
                            </label>
                            <input type="text" id="advertisement_contact_phone" name="advertisement_contact_phone" class="velvet-input" placeholder="e.g. +1 (555) 000-0000" value="<?php echo isset($form_data['contact_phone']) ? esc_attr($form_data['contact_phone']) : ''; ?>">
                        </div>
                    </div>

                    <!-- Description / Content Field -->
                    <div class="velvet-form-group">
                        <label class="velvet-form-label" for="advertisement_content">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="3" y2="18"></line></svg>
                            Advertisement Description & Requirements <span class="required">*</span>
                        </label>
                        <textarea id="advertisement_content" name="advertisement_content" class="velvet-textarea" rows="6" placeholder="Provide full details regarding role criteria, project timeline, remuneration/rates, shoot dates, contact instructions or submission links..." required><?php echo isset($form_data['content']) ? esc_html($form_data['content']) : ''; ?></textarea>
                    </div>

                    <!-- Media Upload Field with Live Preview -->
                    <div class="velvet-form-group">
                        <label class="velvet-form-label">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            Featured Ad Artwork / Poster (Optional)
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

                    <!-- Step 1 Actions -->
                    <div class="velvet-form-actions">
                        <a href="<?php echo esc_url(get_post_type_archive_link('advertisement')); ?>" class="velvet-btn-secondary">
                            Cancel
                        </a>
                        <button type="button" class="velvet-btn-primary" id="btnGoToStep2" onclick="validateAndGoToReview()">
                            <span>Review & Membership</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                    </div>

                </div>
                <!-- END STEP 1 PANE -->

                <!-- STEP 2 PANE: In-Page Review & Membership -->
                <div class="velvet-step-pane <?php echo $show_payment ? 'active' : ''; ?>" id="stepPane2" style="<?php echo $show_payment ? '' : 'display: none;'; ?>">
                    
                    <div class="velvet-review-container">
                        
                        <!-- Review Summary Card -->
                        <div class="velvet-review-summary-card">
                            <div class="velvet-review-card-header">
                                <span class="velvet-review-badge" id="previewCatBadge"><?php echo esc_html($curr_cat); ?></span>
                                <h2 class="velvet-review-title" id="previewTitle"><?php echo esc_html($form_data['title'] ?? 'Advertisement Title'); ?></h2>
                            </div>

                            <div class="velvet-review-meta-grid">
                                <div class="velvet-review-meta-item" id="previewMetaLocationWrap">
                                    <span class="velvet-review-meta-label">Location:</span>
                                    <strong class="velvet-review-meta-value" id="previewLocation"><?php echo esc_html(!empty($form_data['location']) ? $form_data['location'] : 'Not specified'); ?></strong>
                                </div>
                                <div class="velvet-review-meta-item" id="previewMetaBudgetWrap">
                                    <span class="velvet-review-meta-label">Compensation:</span>
                                    <strong class="velvet-review-meta-value" id="previewBudget"><?php echo esc_html(!empty($form_data['budget']) ? $form_data['budget'] : 'Not specified'); ?></strong>
                                </div>
                                <div class="velvet-review-meta-item">
                                    <span class="velvet-review-meta-label">Contact Email:</span>
                                    <strong class="velvet-review-meta-value" id="previewEmail"><?php echo esc_html(!empty($form_data['contact_email']) ? $form_data['contact_email'] : $current_user->user_email); ?></strong>
                                </div>
                                <div class="velvet-review-meta-item" id="previewMetaPhoneWrap">
                                    <span class="velvet-review-meta-label">Contact Phone:</span>
                                    <strong class="velvet-review-meta-value" id="previewPhone"><?php echo esc_html(!empty($form_data['contact_phone']) ? $form_data['contact_phone'] : 'Not provided'); ?></strong>
                                </div>
                            </div>

                            <!-- Target Matching Summary Tags -->
                            <div class="velvet-review-tags-wrap" id="previewTagsWrap">
                                <span class="velvet-review-tag" id="previewTagDomain">Discipline: Open</span>
                                <span class="velvet-review-tag" id="previewTagRole">Role: Open</span>
                                <span class="velvet-review-tag" id="previewTagGender">Gender: All</span>
                                <span class="velvet-review-tag" id="previewTagAge">Age: All</span>
                            </div>

                            <!-- Description Preview -->
                            <div class="velvet-review-description-wrap">
                                <h4 class="velvet-review-section-heading">Description & Requirements:</h4>
                                <div class="velvet-review-description-text" id="previewDescription">
                                    <?php echo nl2br(esc_html($form_data['content'] ?? 'No description provided.')); ?>
                                </div>
                            </div>

                            <!-- Image Thumbnail Preview in Step 2 -->
                            <div class="velvet-review-image-preview" id="previewStep2ImageWrap" style="display: none;">
                                <h4 class="velvet-review-section-heading">Attached Poster Artwork:</h4>
                                <img id="previewStep2Image" src="" alt="Poster Preview">
                            </div>
                        </div>

                        <!-- Membership & Publishing Plan Card -->
                        <div class="velvet-membership-publishing-card">
                            <?php if ($has_paid): ?>
                                <!-- User has active paid membership -->
                                <div class="velvet-membership-status-box active">
                                    <div class="velvet-status-badge-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                    <div>
                                        <h3 style="margin: 0 0 4px 0; font-size: 16px; color: #ffffff; font-weight: 700;">Active Membership Verified</h3>
                                        <p style="margin: 0; font-size: 13px; color: #a1a1aa; line-height: 1.5;">Your active membership includes unlimited classified posting. Click below to submit for instant admin moderation.</p>
                                    </div>
                                </div>

                                <div class="velvet-step2-action-btns">
                                    <button type="button" class="velvet-btn-secondary" onclick="switchAdStep(1)">
                                        &larr; Back to Edit Details
                                    </button>
                                    <button type="submit" class="velvet-btn-primary" id="velvetConfirmPublishBtn">
                                        <span>Confirm & Publish Advertisement</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </button>
                                </div>
                            <?php else: ?>
                                <!-- User needs 15-day listing payment -->
                                <div class="velvet-publishing-plan-box">
                                    <div class="velvet-plan-header">
                                        <div class="velvet-plan-title-wrap">
                                            <span class="velvet-plan-eyebrow">Classified Publication</span>
                                            <h3 class="velvet-plan-title">15-Day Featured Listing</h3>
                                        </div>
                                        <div class="velvet-plan-price">
                                            <span class="velvet-currency">$</span>3.00
                                        </div>
                                    </div>

                                    <ul class="velvet-plan-perks">
                                        <li><span class="perk-check">✓</span> <strong>15-Day Featured Classified</strong> active across directory</li>
                                        <li><span class="perk-check">✓</span> <strong>Smart Audience Email Alerts</strong> matching verified talents</li>
                                        <li><span class="perk-check">✓</span> <strong>Direct Applicant Contact</strong> & candidate review</li>
                                        <li><span class="perk-check">✓</span> <strong>Priority Review & Fast-Track Publishing</strong></li>
                                    </ul>

                                    <div class="velvet-plan-checkout-wrap">
                                        <?php 
                                        $success_url = add_query_arg(array('payment' => 'success', 'success' => 'true'), get_permalink());
                                        $stripe_btn = do_shortcode('[wp_stripe_checkout_session name="Classifieds-fee" price="3.00" button_text="Pay $3.00 & Publish" success_url="' . esc_url($success_url) . '"]');
                                        if (!empty(trim($stripe_btn))) {
                                            echo $stripe_btn;
                                        } else {
                                            echo '<button type="submit" class="velvet-btn-primary" style="width: 100%;"><span>Pay $3.00 & Publish</span></button>';
                                        }
                                        ?>
                                    </div>

                                    <div class="velvet-secure-badge">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                        <span>Secure 256-bit encrypted checkout via Stripe</span>
                                    </div>
                                </div>

                                <div class="velvet-step2-action-btns">
                                    <button type="button" class="velvet-btn-secondary" onclick="switchAdStep(1)">
                                        &larr; Back to Edit Details
                                    </button>
                                </div>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>
                <!-- END STEP 2 PANE -->

            </form>
            <?php endif; ?>

        </div>
    </div>
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
            if (radio) {
                radio.checked = true;
                updateReviewData();
            }
        });
    });

    // Image Upload & Live Preview
    const fileInput = document.getElementById('advertisement_image');
    const dropzone = document.getElementById('velvetDropzone');
    const previewBox = document.getElementById('imagePreviewBox');
    const previewImg = document.getElementById('imagePreviewImg');
    const removeBtn = document.getElementById('removeImageBtn');
    const step2ImgWrap = document.getElementById('previewStep2ImageWrap');
    const step2Img = document.getElementById('previewStep2Image');

    if (fileInput && previewBox && previewImg) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    previewImg.src = evt.target.result;
                    previewBox.style.display = 'block';
                    if (dropzone) dropzone.style.display = 'none';

                    if (step2Img && step2ImgWrap) {
                        step2Img.src = evt.target.result;
                        step2ImgWrap.style.display = 'block';
                    }
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

                if (step2Img && step2ImgWrap) {
                    step2Img.src = '';
                    step2ImgWrap.style.display = 'none';
                }
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

    // Listen to changes on inputs to keep review sync'd
    const inputsToSync = ['advertisement_title', 'advertisement_location', 'advertisement_budget', 'advertisement_contact_email', 'advertisement_contact_phone', 'advertisement_content', 'target_domain', 'target_role', 'target_gender', 'target_age_group'];
    inputsToSync.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updateReviewData);
            el.addEventListener('change', updateReviewData);
        }
    });
});

function updateReviewData() {
    const title = document.getElementById('advertisement_title')?.value || 'Your Advertisement Title';
    const checkedCat = document.querySelector('input[name="advertisement_category"]:checked')?.value || 'Casting Calls';
    const location = document.getElementById('advertisement_location')?.value || 'Not specified';
    const budget = document.getElementById('advertisement_budget')?.value || 'Not specified';
    const email = document.getElementById('advertisement_contact_email')?.value || 'Not specified';
    const phone = document.getElementById('advertisement_contact_phone')?.value || 'Not provided';
    const content = document.getElementById('advertisement_content')?.value || 'No description provided.';
    const domain = document.getElementById('target_domain')?.value;
    const role = document.getElementById('target_role')?.value;
    const gender = document.getElementById('target_gender')?.value;
    const age = document.getElementById('target_age_group')?.value;

    const previewTitle = document.getElementById('previewTitle');
    const previewCat = document.getElementById('previewCatBadge');
    const previewLoc = document.getElementById('previewLocation');
    const previewBud = document.getElementById('previewBudget');
    const previewEmail = document.getElementById('previewEmail');
    const previewPhone = document.getElementById('previewPhone');
    const previewDesc = document.getElementById('previewDescription');

    if (previewTitle) previewTitle.textContent = title;
    if (previewCat) previewCat.textContent = checkedCat;
    if (previewLoc) previewLoc.textContent = location;
    if (previewBud) previewBud.textContent = budget;
    if (previewEmail) previewEmail.textContent = email;
    if (previewPhone) previewPhone.textContent = phone;
    if (previewDesc) previewDesc.textContent = content;

    const tagDomain = document.getElementById('previewTagDomain');
    const tagRole = document.getElementById('previewTagRole');
    const tagGender = document.getElementById('previewTagGender');
    const tagAge = document.getElementById('previewTagAge');

    if (tagDomain) tagDomain.textContent = 'Discipline: ' + (domain || 'Open');
    if (tagRole) tagRole.textContent = 'Role: ' + (role || 'Open');
    if (tagGender) tagGender.textContent = 'Gender: ' + (gender === 'all' || !gender ? 'All' : gender);
    if (tagAge) tagAge.textContent = 'Age: ' + (age === 'all' || !age ? 'All' : age);
}

function switchAdStep(step) {
    const pane1 = document.getElementById('stepPane1');
    const pane2 = document.getElementById('stepPane2');
    const tab1 = document.getElementById('stepTab1');
    const tab2 = document.getElementById('stepTab2');

    if (step === 1) {
        if (pane1) {
            pane1.style.display = 'block';
            pane1.classList.add('active');
        }
        if (pane2) {
            pane2.style.display = 'none';
            pane2.classList.remove('active');
        }
        if (tab1) tab1.classList.add('active');
        if (tab2) tab2.classList.remove('active');
    } else if (step === 2) {
        updateReviewData();
        if (pane1) {
            pane1.style.display = 'none';
            pane1.classList.remove('active');
        }
        if (pane2) {
            pane2.style.display = 'block';
            pane2.classList.add('active');
        }
        if (tab1) {
            tab1.classList.remove('active');
            tab1.classList.add('completed');
        }
        if (tab2) tab2.classList.add('active');
    }

    const formCard = document.querySelector('.velvet-form-card');
    if (formCard) {
        formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function validateAndGoToReview() {
    const titleInput = document.getElementById('advertisement_title');
    const contentInput = document.getElementById('advertisement_content');
    const emailInput = document.getElementById('advertisement_contact_email');

    if (titleInput && !titleInput.checkValidity()) {
        titleInput.reportValidity();
        return;
    }
    if (contentInput && !contentInput.checkValidity()) {
        contentInput.reportValidity();
        return;
    }
    if (emailInput && !emailInput.checkValidity()) {
        emailInput.reportValidity();
        return;
    }

    // Cache to localStorage
    const checkedCat = document.querySelector('input[name="advertisement_category"]:checked');
    const formData = {
        title: titleInput?.value || '',
        content: contentInput?.value || '',
        location: document.getElementById('advertisement_location')?.value || '',
        categories: checkedCat ? [checkedCat.value] : [],
        target_domain: document.getElementById('target_domain')?.value || '',
        target_role: document.getElementById('target_role')?.value || '',
        target_gender: document.getElementById('target_gender')?.value || 'all',
        target_age_group: document.getElementById('target_age_group')?.value || 'all',
        budget: document.getElementById('advertisement_budget')?.value || '',
        contact_email: emailInput?.value || '',
        contact_phone: document.getElementById('advertisement_contact_phone')?.value || ''
    };
    localStorage.setItem('velvet_ad_form_data', JSON.stringify(formData));

    switchAdStep(2);
}
</script>

<?php
get_footer();
