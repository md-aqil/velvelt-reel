<?php
/**
 * Talent submission and update forms
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Determine whether a repeatable checkbox row was checked.
 *
 * New submissions send the row index as the checkbox value. Older drafts may
 * still use sparse "on" values keyed by the original row index.
 *
 * @param mixed $submitted_values Raw checkbox payload.
 * @param int   $index            Row index.
 * @return bool
 */
function velvet_reel_repeater_checkbox_is_checked($submitted_values, $index) {
    if (!is_array($submitted_values)) {
        return false;
    }

    $index = (string) $index;

    foreach ($submitted_values as $submitted_index => $submitted_value) {
        if ((string) $submitted_value === $index) {
            return true;
        }

        if ((string) $submitted_index === $index && $submitted_value === 'on') {
            return true;
        }
    }

    return false;
}

/**
 * Handle frontend media uploads with reduced image processing pressure.
 *
 * Production failures on large images are commonly caused by generating every
 * registered image sub-size during upload. Frontend draft/profile uploads only
 * need a small preview, so we keep thumbnail generation and skip the larger
 * derivatives to reduce memory spikes on shared hosting.
 *
 * @param string $file_id   Input name in $_FILES.
 * @param int    $post_id   Parent post ID.
 * @param array  $post_data Optional attachment post data.
 * @param array  $overrides Optional upload overrides.
 * @return int|\WP_Error
 */
function velvet_reel_media_handle_upload($file_id, $post_id = 0, $post_data = array(), $overrides = array()) {
    if (!function_exists('media_handle_upload')) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
    }

    $limit_intermediate_sizes = static function ($sizes) {
        if (!is_array($sizes)) {
            return array();
        }

        if (isset($sizes['thumbnail'])) {
            return array('thumbnail' => $sizes['thumbnail']);
        }

        return array();
    };

    $disable_big_image_scaling = static function () {
        return false;
    };

    add_filter('intermediate_image_sizes_advanced', $limit_intermediate_sizes, 999);
    add_filter('big_image_size_threshold', $disable_big_image_scaling, 999);

    try {
        $overrides = wp_parse_args($overrides, array('test_form' => false));
        return media_handle_upload($file_id, $post_id, $post_data, $overrides);
    } finally {
        remove_filter('intermediate_image_sizes_advanced', $limit_intermediate_sizes, 999);
        remove_filter('big_image_size_threshold', $disable_big_image_scaling, 999);
    }
}

/**
 * Collect all submitted portfolio upload groups from the request.
 *
 * Frontend role fields use names such as `portfolio-fashion-designer[]`.
 * Older templates used a smaller set of aliases. Matching by the common
 * `portfolio` prefix keeps the backend aligned with both current and future
 * role-specific upload inputs.
 *
 * @return array<string, array>
 */
function velvet_reel_get_portfolio_upload_groups() {
    if (empty($_FILES) || !is_array($_FILES)) {
        return array();
    }

    $portfolio_groups = array();

    foreach ($_FILES as $field_name => $file_group) {
        if ($field_name !== 'portfolio' && strpos($field_name, 'portfolio-') !== 0) {
            continue;
        }

        if (!is_array($file_group) || !isset($file_group['name'])) {
            continue;
        }

        $names = $file_group['name'];
        $has_files = false;

        if (is_array($names)) {
            foreach ($names as $name) {
                if (!empty($name)) {
                    $has_files = true;
                    break;
                }
            }
        } else {
            $has_files = !empty($names);
        }

        if ($has_files) {
            $portfolio_groups[$field_name] = $file_group;
        }
    }

    return $portfolio_groups;
}

/**
 * Return portfolio upload rules shared across create, update, and draft save.
 *
 * @return array
 */
function velvet_reel_get_portfolio_upload_rules() {
    return array(
        'max_items' => 10,
        'max_file_size' => 50 * 1024 * 1024,
        'allowed_mimes' => array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'heic|heif' => 'image/heic',
            'mp4|m4v' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
        ),
    );
}

/**
 * Convert upload error codes into readable messages.
 *
 * @param int $error_code PHP upload error code.
 * @return string
 */
function velvet_reel_get_upload_error_message($error_code) {
    $messages = array(
        UPLOAD_ERR_INI_SIZE => 'file is larger than the server upload limit',
        UPLOAD_ERR_FORM_SIZE => 'file is larger than the allowed form limit',
        UPLOAD_ERR_PARTIAL => 'file uploaded only partially',
        UPLOAD_ERR_NO_FILE => 'no file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'server is missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'server failed to write the file',
        UPLOAD_ERR_EXTENSION => 'upload was stopped by a PHP extension',
    );

    return isset($messages[$error_code]) ? $messages[$error_code] : 'unknown upload error';
}

/**
 * Normalize submitted existing portfolio order against current attachment IDs.
 *
 * Passing `null` preserves the current order. Passing an empty string removes
 * all existing portfolio items.
 *
 * @param string|array|null $submitted_order       Submitted order payload.
 * @param array             $current_attachment_ids Current portfolio attachment IDs.
 * @return array
 */
function velvet_reel_normalize_existing_portfolio_order($submitted_order, $current_attachment_ids) {
    $current_attachment_ids = array_values(array_unique(array_filter(array_map('intval', (array) $current_attachment_ids))));

    if ($submitted_order === null) {
        return $current_attachment_ids;
    }

    if (is_string($submitted_order)) {
        $submitted_order = trim($submitted_order) === ''
            ? array()
            : explode(',', $submitted_order);
    }

    if (!is_array($submitted_order)) {
        return $current_attachment_ids;
    }

    $submitted_ids = array_values(array_unique(array_filter(array_map('intval', $submitted_order))));
    $current_lookup = array_fill_keys($current_attachment_ids, true);
    $normalized = array();

    foreach ($submitted_ids as $attachment_id) {
        if (isset($current_lookup[$attachment_id])) {
            $normalized[] = $attachment_id;
        }
    }

    return $normalized;
}

/**
 * Validate all portfolio uploads before any post mutation occurs.
 *
 * @param array             $existing_attachment_ids Current portfolio attachment IDs.
 * @param string|array|null $submitted_existing_order Submitted existing order payload.
 * @return true|WP_Error
 */
function velvet_reel_validate_portfolio_upload_groups($existing_attachment_ids = array(), $submitted_existing_order = null) {
    $portfolio_groups = velvet_reel_get_portfolio_upload_groups();
    if (empty($portfolio_groups)) {
        return true;
    }

    if (!function_exists('wp_check_filetype_and_ext')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $rules = velvet_reel_get_portfolio_upload_rules();
    $allowed_mimes = $rules['allowed_mimes'];
    $allowed_types = array_values($allowed_mimes);
    $effective_existing_ids = velvet_reel_normalize_existing_portfolio_order($submitted_existing_order, $existing_attachment_ids);
    $existing_count = count($effective_existing_ids);
    $new_count = 0;
    $errors = array();

    foreach ($portfolio_groups as $portfolio_files) {
        $file_count = is_array($portfolio_files['name']) ? count($portfolio_files['name']) : 1;

        for ($key = 0; $key < $file_count; $key++) {
            $name = is_array($portfolio_files['name']) ? $portfolio_files['name'][$key] : $portfolio_files['name'];
            if (empty($name)) {
                continue;
            }

            $new_count++;
            $size = (int) (is_array($portfolio_files['size']) ? $portfolio_files['size'][$key] : $portfolio_files['size']);
            $tmp_name = is_array($portfolio_files['tmp_name']) ? $portfolio_files['tmp_name'][$key] : $portfolio_files['tmp_name'];
            $error = (int) (is_array($portfolio_files['error']) ? $portfolio_files['error'][$key] : $portfolio_files['error']);

            if ($error !== UPLOAD_ERR_OK) {
                $errors[] = sprintf('%s: %s.', basename($name), velvet_reel_get_upload_error_message($error));
                continue;
            }

            if ($size > $rules['max_file_size']) {
                $errors[] = sprintf('%s exceeds the 50MB file size limit.', basename($name));
                continue;
            }

            $filetype = wp_check_filetype_and_ext($tmp_name, $name, $allowed_mimes);
            $mime_type = isset($filetype['type']) ? $filetype['type'] : '';

            if (empty($mime_type) || !in_array($mime_type, $allowed_types, true)) {
                $errors[] = sprintf('%s is not an allowed portfolio file type.', basename($name));
            }
        }
    }

    if (($existing_count + $new_count) > $rules['max_items']) {
        $errors[] = sprintf(
            'Portfolio supports a maximum of %d items. You currently have %d and tried to add %d more.',
            $rules['max_items'],
            $existing_count,
            $new_count
        );
    }

    if (!empty($errors)) {
        return new WP_Error('invalid_portfolio_upload', 'Portfolio validation failed.', $errors);
    }

    return true;
}

/**
 * Render a validation error list for blocking form submissions.
 *
 * @param WP_Error $validation_error Validation error object.
 * @return string
 */
function velvet_reel_format_portfolio_validation_error($validation_error) {
    $errors = $validation_error->get_error_data();
    if (!is_array($errors)) {
        $errors = array($validation_error->get_error_message());
    }

    $items = array_map(
        static function ($message) {
            return '<li>' . esc_html($message) . '</li>';
        },
        $errors
    );

    return '<h2>Portfolio Upload Error</h2><p>Please fix these files and submit again.</p><ul>' . implode('', $items) . '</ul>';
}

/**
 * Return the first image attachment ID from a portfolio attachment list.
 *
 * @param array $attachment_ids Attachment IDs.
 * @return int
 */
function velvet_reel_get_first_portfolio_image_id($attachment_ids) {
    foreach ((array) $attachment_ids as $attachment_id) {
        $attachment_id = (int) $attachment_id;

        if ($attachment_id > 0 && wp_attachment_is_image($attachment_id)) {
            return $attachment_id;
        }
    }

    return 0;
}

/**
 * Check if a file is already uploaded for the given talent profile by matching filename and filesize.
 *
 * @param string $filename Original uploaded filename.
 * @param int    $filesize Size of the file in bytes.
 * @param array  $existing_ids Existing attachment IDs to check.
 * @return int|false Return attachment ID if duplicate, false otherwise.
 */
function velvet_reel_find_duplicate_attachment($filename, $filesize, $existing_ids) {
    if (empty($existing_ids) || !is_array($existing_ids)) {
        return false;
    }

    $uploaded_name_only = sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME));

    foreach ($existing_ids as $attachment_id) {
        $attachment_id = (int) $attachment_id;
        if ($attachment_id <= 0) {
            continue;
        }

        $file_path = get_attached_file($attachment_id);
        if (!$file_path || !file_exists($file_path)) {
            continue;
        }

        $existing_filename = basename($file_path);
        $existing_size = filesize($file_path);

        if ($existing_size === (int) $filesize) {
            $existing_name_only = pathinfo($existing_filename, PATHINFO_FILENAME);
            
            // Check if names match exactly or if one is a sanitize-version/suffixed version of the other
            if ($existing_filename === $filename || 
                strcasecmp($existing_name_only, $uploaded_name_only) === 0 ||
                ($uploaded_name_only !== '' && stripos($existing_name_only, $uploaded_name_only) === 0)) {
                return $attachment_id;
            }
        }
    }

    return false;
}

/**
 * Process portfolio uploads for any active role-specific portfolio input.
 *
 * @param int $post_id Talent post ID.
 * @return array Uploaded attachment IDs after merge/dedupe.
 */
function velvet_reel_handle_portfolio_uploads($post_id, $args = array()) {
    $portfolio_groups = velvet_reel_get_portfolio_upload_groups();
    $args = wp_parse_args(
        $args,
        array(
            'cover_choice' => '',
            'existing_order' => null,
            'allow_thumbnail_override' => false,
        )
    );
    $cover_choice = sanitize_text_field((string) $args['cover_choice']);
    $submitted_existing_order = $args['existing_order'];
    $allow_thumbnail_override = (bool) $args['allow_thumbnail_override'];
    $current_attachment_ids = get_post_meta($post_id, '_talent_portfolio', true);
    if (!is_array($current_attachment_ids)) {
        $current_attachment_ids = array();
    }

    $current_attachment_ids = array_values(array_unique(array_filter(array_map('intval', $current_attachment_ids))));
    $existing_attachment_ids = velvet_reel_normalize_existing_portfolio_order($submitted_existing_order, $current_attachment_ids);
    $current_featured_id = (int) get_post_thumbnail_id($post_id);
    $featured_was_portfolio = $current_featured_id > 0 && in_array($current_featured_id, $current_attachment_ids, true);

    if (empty($portfolio_groups)) {
        if ($submitted_existing_order !== null) {
            update_post_meta($post_id, '_talent_portfolio', $existing_attachment_ids);
        }

        if ($allow_thumbnail_override && strpos($cover_choice, 'existing:') === 0) {
            $existing_cover_id = (int) substr($cover_choice, strlen('existing:'));
            if (
                $existing_cover_id > 0 &&
                in_array($existing_cover_id, $existing_attachment_ids, true) &&
                wp_attachment_is_image($existing_cover_id)
            ) {
                set_post_thumbnail($post_id, $existing_cover_id);
            }
        } else {
            if ($featured_was_portfolio && !in_array($current_featured_id, $existing_attachment_ids, true)) {
                delete_post_thumbnail($post_id);
            }
        }

        if (!has_post_thumbnail($post_id)) {
            $first_image_id = velvet_reel_get_first_portfolio_image_id($existing_attachment_ids);
            if ($first_image_id) {
                set_post_thumbnail($post_id, $first_image_id);
            }
        }

        return $existing_attachment_ids;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $attachment_ids = $existing_attachment_ids;
    $selected_cover_attachment_id = 0;

    foreach ($portfolio_groups as $field_name => $portfolio_files) {
        $file_count = is_array($portfolio_files['name']) ? count($portfolio_files['name']) : 1;

        for ($key = 0; $key < $file_count; $key++) {
            $name = is_array($portfolio_files['name']) ? $portfolio_files['name'][$key] : $portfolio_files['name'];

            if (empty($name)) {
                continue;
            }

            $filesize = is_array($portfolio_files['size']) ? $portfolio_files['size'][$key] : $portfolio_files['size'];
            $duplicate_id = velvet_reel_find_duplicate_attachment($name, $filesize, $existing_attachment_ids);

            if ($duplicate_id > 0) {
                $attachment_id = $duplicate_id;
            } else {
                $_FILES['single_portfolio_image'] = array(
                    'name' => $name,
                    'type' => is_array($portfolio_files['type']) ? $portfolio_files['type'][$key] : $portfolio_files['type'],
                    'tmp_name' => is_array($portfolio_files['tmp_name']) ? $portfolio_files['tmp_name'][$key] : $portfolio_files['tmp_name'],
                    'error' => is_array($portfolio_files['error']) ? $portfolio_files['error'][$key] : $portfolio_files['error'],
                    'size' => $filesize,
                );

                $attachment_id = velvet_reel_media_handle_upload('single_portfolio_image', $post_id);
            }

            if (!is_wp_error($attachment_id)) {
                $attachment_ids[] = (int) $attachment_id;
                if (
                    $allow_thumbnail_override &&
                    $cover_choice === 'upload:' . $field_name . ':' . $key &&
                    wp_attachment_is_image($attachment_id)
                ) {
                    $selected_cover_attachment_id = (int) $attachment_id;
                }
            } else {
                error_log('Portfolio image upload failed: ' . $attachment_id->get_error_message());
            }
        }
    }

    $attachment_ids = array_values(array_unique(array_filter(array_map('intval', $attachment_ids))));
    update_post_meta($post_id, '_talent_portfolio', $attachment_ids);

    if (
        $allow_thumbnail_override &&
        $selected_cover_attachment_id === 0 &&
        strpos($cover_choice, 'existing:') === 0
    ) {
        $existing_cover_id = (int) substr($cover_choice, strlen('existing:'));
        if (
            $existing_cover_id > 0 &&
            in_array($existing_cover_id, $attachment_ids, true) &&
            wp_attachment_is_image($existing_cover_id)
        ) {
            $selected_cover_attachment_id = $existing_cover_id;
        }
    }

    if ($allow_thumbnail_override && $selected_cover_attachment_id > 0) {
        set_post_thumbnail($post_id, $selected_cover_attachment_id);
    } else {
        if ($featured_was_portfolio && !in_array($current_featured_id, $attachment_ids, true)) {
            delete_post_thumbnail($post_id);
        }
    }

    if (!has_post_thumbnail($post_id)) {
        $first_image_id = velvet_reel_get_first_portfolio_image_id($attachment_ids);
        if ($first_image_id) {
            set_post_thumbnail($post_id, $first_image_id);
        }
    }

    return $attachment_ids;
}

/**
 * Handle the front-end talent submission form.
 */
function handle_talent_submission() {
    // Check if our form is submitted
    if ('POST' !== $_SERVER['REQUEST_METHOD'] || !isset($_POST['action']) || 'submit_talent_profile' !== $_POST['action']) {
        return;
    }

    // Prevent timeout during file uploads
    if (function_exists('set_time_limit')) {
        @set_time_limit(300);
    }

    // Verify nonce
    if (!isset($_POST['talent_submission_nonce']) || !wp_verify_nonce($_POST['talent_submission_nonce'], 'talent_submission')) {
        error_log('Talent submission nonce verification failed. POST data: ' . print_r($_POST, true));
        wp_die('Security check failed. Please refresh the page and try again.');
    }

    $portfolio_validation = velvet_reel_validate_portfolio_upload_groups();
    if (is_wp_error($portfolio_validation)) {
        wp_die(velvet_reel_format_portfolio_validation_error($portfolio_validation));
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
    // Check if profile photo was uploaded directly or set from portfolio
    $profile_photo_uploaded = false;
    if (!empty($_FILES['profilePhoto']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = velvet_reel_media_handle_upload('profilePhoto', $post_id);

        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
            $profile_photo_uploaded = true;
        }
    }

    // --- Save Custom Fields (Meta Data) ---
    $meta_fields = [
        '_talent_state' => 'city',  // Form uses 'city' as field name for state
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
        '_talent_agency_name' => 'agencyName',
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
    if (isset($_POST['city'])) {
        $state = sanitize_text_field($_POST['city']);
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
                    'role' => sanitize_text_field($work_roles[$i] ?? ''),
                    'startDate' => sanitize_text_field($work_start_dates[$i] ?? ''),
                    'endDate' => velvet_reel_repeater_checkbox_is_checked($work_present, $i) ? '' : sanitize_text_field($work_end_dates[$i] ?? ''),
                    'present' => velvet_reel_repeater_checkbox_is_checked($work_present, $i) ? 'on' : 'off',
                    'description' => sanitize_textarea_field($work_descriptions[$i] ?? ''),
                ];
            }
        }
        update_post_meta($post_id, '_talent_notable_works', $notable_works);
    }

    // --- Handle Role-Specific Fields & Portfolio Gallery ---
    // Handle design categories for all roles that have them
    if (isset($_POST['designCategories']) && is_array($_POST['designCategories'])) {
        $design_categories = array_map('sanitize_text_field', $_POST['designCategories']);
        update_post_meta($post_id, '_talent_designCategories', $design_categories);
    }

    // Handle Portfolio Gallery Upload (for roles that have it)
    // This will ADD to existing images. For a replacement logic, you'd first delete old attachments.
    // Check for all possible portfolio input names
    velvet_reel_handle_portfolio_uploads(
        $post_id,
        array(
            'cover_choice' => isset($_POST['portfolio_cover_choice']) ? wp_unslash($_POST['portfolio_cover_choice']) : '',
            'allow_thumbnail_override' => !$profile_photo_uploaded,
        )
    );

    // --- Trigger Portfolio Unverified Email Notification ---
    if (function_exists('velvet_send_portfolio_unverified_email')) {
        velvet_send_portfolio_unverified_email(get_current_user_id(), $post_id);
    }

    // --- Redirect after submission ---
    // Redirect to talent archive page after successful submission
    $redirect_url = get_post_type_archive_link('talent');
    wp_redirect($redirect_url);
    exit;
}
add_action('template_redirect', 'handle_talent_submission');

/**
 * Handle the front-end talent profile update form.
 */
function handle_talent_update() {
    // Check if our update form is submitted
    if ('POST' !== $_SERVER['REQUEST_METHOD'] || !isset($_POST['action']) || 'update_talent_profile' !== $_POST['action']) {
        return;
    }

    // Prevent timeout during file uploads
    if (function_exists('set_time_limit')) {
        @set_time_limit(300);
    }

    // Verify nonce
    if (!isset($_POST['talent_update_nonce']) || !wp_verify_nonce($_POST['talent_update_nonce'], 'talent_update')) {
        error_log('Talent update nonce verification failed. POST data: ' . print_r($_POST, true));
        wp_die('Security check failed. Please refresh the page and try again.');
    }

    // Check user is logged in and is the author of the post
    $post_id = (int) $_POST['post_id'];
    if (!is_user_logged_in() || get_post_field('post_author', $post_id) != get_current_user_id()) {
        wp_die('You do not have permission to edit this profile.');
    }

    $existing_portfolio_ids = get_post_meta($post_id, '_talent_portfolio', true);
    $submitted_existing_order = isset($_POST['portfolio_existing_order']) ? wp_unslash($_POST['portfolio_existing_order']) : null;
    $portfolio_validation = velvet_reel_validate_portfolio_upload_groups($existing_portfolio_ids, $submitted_existing_order);
    if (is_wp_error($portfolio_validation)) {
        wp_die(velvet_reel_format_portfolio_validation_error($portfolio_validation));
    }

    // Sanitize and prepare post data
    $full_name = sanitize_text_field($_POST['fullName']);
    $style_description = wp_kses_post($_POST['styleDescription']);

    $talent_post_update = array(
        'ID' => $post_id,
        'post_title' => $full_name,
        'post_content' => $style_description,
        'post_status' => 'pending', // Set to pending for re-approval
    );

    // Update the post in the database
    $result = wp_update_post($talent_post_update);

    // Check if post update was successful
    if (is_wp_error($result)) {
        error_log('Talent profile update failed: ' . $result->get_error_message());
        // Continue with the rest of the update instead of dying
    }

    // --- Handle File Upload (Profile Photo) ---
    // Check if profile photo was uploaded directly or set from portfolio
    $profile_photo_uploaded = false;
    if (!empty($_FILES['profilePhoto']['name'])) {
        // Ensure required functions are available
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
                error_log('Profile photo upload failed: ' . $attachment_id->get_error_message());
                wp_die('<h1>Upload Error</h1><p>We could not upload your image: <strong>' . esc_html($attachment_id->get_error_message()) . '</strong></p><p>Please go back, ensure the file is an image (JPG, PNG), and try a smaller file size (e.g. under 2MB).</p>', 'Upload Error', array('response' => 200));
            } else {
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
        '_talent_agency_name' => 'agencyName',
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

    if (isset($_POST['role'])) {
        $role_slug = sanitize_title($_POST['role']);
        if (!empty($role_slug)) {
            $role_name = ucwords(str_replace('-', ' ', $role_slug));
            wp_set_object_terms($post_id, $role_name, 'talent_type', false);
        }
    }

    if (isset($_POST['city'])) {
        $state = sanitize_text_field($_POST['city']);
        if (!empty($state)) {
            wp_set_object_terms($post_id, $state, 'talent_state');
        }
    }

    if (isset($_POST['country'])) {
        $country = sanitize_text_field($_POST['country']);
        if (!empty($country)) {
            wp_set_object_terms($post_id, $country, 'talent_country');
        }
    }

    if (isset($_POST['workTitle']) && is_array($_POST['workTitle'])) {
        $notable_works = [];
        for ($i = 0; $i < count($_POST['workTitle']); $i++) {
            if (!empty($_POST['workTitle'][$i])) {
                $is_present = velvet_reel_repeater_checkbox_is_checked($_POST['workPresent'] ?? [], $i);
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
    velvet_reel_handle_portfolio_uploads(
        $post_id,
        array(
            'cover_choice' => isset($_POST['portfolio_cover_choice']) ? wp_unslash($_POST['portfolio_cover_choice']) : '',
            'existing_order' => $submitted_existing_order,
            'allow_thumbnail_override' => !$profile_photo_uploaded,
        )
    );

    // --- Redirect after update ---
    $archive_url = get_post_type_archive_link('talent');
    
    // Fallback to talent page if archive link is not available
    if (!$archive_url || is_wp_error($archive_url)) {
        // Try to find the talent archive page
        $talent_pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'template-talent-archive.php'
        ));
        
        if (!empty($talent_pages)) {
            $archive_url = get_permalink($talent_pages[0]->ID);
        } else {
            // Last resort fallback
            $archive_url = home_url('/talent/');
        }
    }
    
    // Ensure we have a valid URL before redirecting
    if ($archive_url && !is_wp_error($archive_url)) {
        $redirect_url = add_query_arg('updated', 'true', $archive_url);
        wp_redirect($redirect_url);
        exit;
    } else {
        // Emergency fallback - redirect to home page with success message
        error_log('Talent update redirect failed - using emergency fallback');
        wp_redirect(home_url('/?talent_updated=true'));
        exit;
    }
}
add_action('template_redirect', 'handle_talent_update');

// AJAX handler to get attachment data
function get_attachment_data_ajax() {
    $attachment_id = intval($_GET['id'] ?? 0);
    
    if (!$attachment_id) {
        wp_send_json_error('Invalid attachment ID');
    }
    
    $attachment = get_post($attachment_id);
    if (!$attachment || $attachment->post_type !== 'attachment') {
        wp_send_json_error('Attachment not found');
    }
    
    $mime_type = get_post_mime_type($attachment_id);
    $is_video = $mime_type && strpos($mime_type, 'video/') === 0;
    $full_url = wp_get_attachment_url($attachment_id);
    $thumb_url = '';

    if ($is_video) {
        $v_thumb = get_post_meta($attachment_id, '_thumbnail_id', true);
        if ($v_thumb) {
            $thumb_url = wp_get_attachment_image_url($v_thumb, 'medium') ?: '';
        }
        if (empty($thumb_url)) {
            $med = wp_get_attachment_image_url($attachment_id, 'medium');
            if ($med && !preg_match('/\.(mp4|webm|mov|m4v|ogv)$/i', $med)) {
                $thumb_url = $med;
            }
        }
    } else {
        $thumb_url = wp_get_attachment_image_url($attachment_id, 'thumbnail') ?: $full_url;
    }
    
    $response = array(
        'id' => $attachment_id,
        'title' => $attachment->post_title ?: $attachment->post_name,
        'filename' => basename(get_attached_file($attachment_id) ?: $full_url ?: $attachment->guid),
        'mime_type' => $mime_type,
        'is_video' => $is_video,
        'thumbnail_url' => $thumb_url,
        'video_url' => $is_video ? $full_url : '',
        'full_url' => $full_url
    );
    
    wp_send_json_success($response);
}
add_action('wp_ajax_get_attachment_data', 'get_attachment_data_ajax');
add_action('wp_ajax_nopriv_get_attachment_data', 'get_attachment_data_ajax');
// TEMP COMMENTED OUT - TESTING
// add_action('wp_ajax_submit_talent_profile', 'handle_talent_submission');
// add_action('wp_ajax_nopriv_submit_talent_profile', 'handle_talent_submission');
// add_action('wp_ajax_update_talent_profile', 'handle_talent_update');
// add_action('wp_ajax_nopriv_update_talent_profile', 'handle_talent_update');
