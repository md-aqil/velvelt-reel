<?php
/**
 * All shortcodes
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shortcode to display talent archive
 */
function talent_archive_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'posts_per_page' => -1,
        'post_status' => 'publish,draft,pending'
    ), $atts);

    // Check if user is logged in
    if (!is_user_logged_in()) {
        return '<div style="padding:40px;text-align:center;background:#3a3a3a;border-radius:8px;color:white;">
                    <p style="font-size:18px;margin-bottom:20px;">Please login to view talent portfolios.</p>
                    <a href="' . esc_url(wp_login_url(get_permalink())) . '" style="display:inline-block;padding:12px 24px;background:#df1d3d;color:white;border-radius:6px;text-decoration:none;font-weight:bold;">Login Now</a>
                </div>';
    }

    // Get current user ID
    $current_user_id = get_current_user_id();
    
    // Set up query arguments
    $args = array(
        'post_type' => 'talent',
        'author' => $current_user_id,
        'posts_per_page' => intval($atts['posts_per_page']),
        'post_status' => explode(',', $atts['post_status'])
    );
    
    // Create new WP_Query
    $talent_query = new WP_Query($args);
    
    // Start output buffering
    ob_start();
    
    if ($talent_query->have_posts()) :
        ?>
        <div class="talent-archive-shortcode">
            <div class="talent-archive-grid">
                <?php while ($talent_query->have_posts()) : $talent_query->the_post();
                    // Get meta data
                    $role = get_post_meta(get_the_ID(), '_talent_role', true);
                    $project_views = (int) get_post_meta(get_the_ID(), '_talent_project_views', true);
                    $saved_to_lists = (int) get_post_meta(get_the_ID(), '_talent_saved_to_lists', true);
                    $profile_clicks = (int) get_post_meta(get_the_ID(), '_talent_profile_clicks', true);
                    ?>
                    <div class="talent-card">
                        <div class="talent-card-header">
                            <?php if (get_post_status() === 'publish') : ?>
                                <span class="status-badge">Published</span>
                            <?php else : ?>
                                <span class="status-badge pending"><?php echo esc_html(ucfirst(get_post_status())); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="talent-card-body">
                            <h3 class="talent-role"><?php echo esc_html(ucwords(str_replace('-', ' ', $role))); ?></h3>
                            <p class="talent-name"><?php the_title(); ?></p>
                        </div>
                        <div class="talent-card-footer">
                            <div class="talent-stats">
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo number_format_i18n($project_views); ?></span>
                                    <span class="stat-label">Project Views</span>
                                </div>
                            </div>
                            <div class="talent-card-actions">
                                <?php
                                // Instead of using get_page_by_path, let's construct the URL directly
                                // This avoids issues if the page doesn't exist in the database
                                $edit_page_url = home_url('/edit-talent-profile/');
                                if ($edit_page_url) :
                                    $edit_url_with_id = add_query_arg('talent_id', get_the_ID(), $edit_page_url);
                                    ?>
                                    <a href="<?php echo esc_url($edit_url_with_id); ?>" class="view-button edit-button">Edit</a>
                                <?php endif; ?>
                                <?php if (get_post_status() === 'publish') : ?>
                                <a href="<?php the_permalink(); ?>" class="view-button">View</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php
        wp_reset_postdata();
    else :
        ?>
        <div class="no-talent-profiles">
            <div class="no-talent-card">
                <div class="no-talent-icon-wrapper">
                    <div class="no-talent-icon-glow"></div>
                    <div class="no-talent-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                </div>
                <h2 class="no-talent-title"><?php esc_html_e('No Talent Profiles Found', 'hello-elementor-child'); ?></h2>
                <p class="no-talent-description">
                    <?php esc_html_e('You haven\'t created a talent portfolio yet. Build your portfolio to showcase your creative work, experience, and get discovered by industry professionals.', 'hello-elementor-child'); ?>
                </p>
                <a href="<?php echo esc_url(home_url('/talent-registration')); ?>" class="btn-create-portfolio btn-create-portfolio-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 6px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="12" y1="12" x2="16" y2="12"></line></svg>
                    <?php esc_html_e('Create Your Portfolio', 'hello-elementor-child'); ?>
                </a>
            </div>
        </div>
        <?php
    endif;
    
    return ob_get_clean();
}
add_shortcode('talent_archive', 'talent_archive_shortcode');



/**
 * User Dashboard Shortcode for Elementor
 */
function user_dashboard_shortcode() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        return '<div style="padding:40px;text-align:center;background:#3a3a3a;border-radius:8px;color:white;">
                    <p style="font-size:18px;margin-bottom:20px;">Please login to view your dashboard.</p>
                    <a href="' . esc_url(wp_login_url(get_permalink())) . '" style="display:inline-block;padding:12px 24px;background:#df1d3d;color:white;border-radius:6px;text-decoration:none;font-weight:bold;">Login Now</a>
                </div>';
    }

    // Start output buffering
    ob_start();

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $first_name = $current_user->first_name;
    $last_name = $current_user->last_name;
    $email = $current_user->user_email;
    $username = $current_user->user_login;

    // Get extra user information from custom table
    global $wpdb;
    $table = $wpdb->prefix . 'userinformation';
    $extra = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id), ARRAY_A);

    $dob = $extra['dob'] ?? '';
    $address = $extra['address'] ?? '';
    $region = $extra['region'] ?? '';
    $phone = $extra['phone'] ?? '';
    $street_address = $extra['street_address'] ?? '';
    $zip_code = $extra['zip_code'] ?? '';

    $profile_img = get_user_meta($user_id, 'profile_picture', true);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_dashboard']) && isset($_POST['dashboard_nonce'])) {
        if (wp_verify_nonce($_POST['dashboard_nonce'], 'update_user_dashboard')) {
            $new_first = sanitize_text_field($_POST['first_name']);
            $new_last = sanitize_text_field($_POST['last_name']);
            $new_dob = sanitize_text_field($_POST['dob']);
            $new_email = sanitize_email($_POST['email']);
            $new_phone = sanitize_text_field($_POST['phone']);
            $new_address = sanitize_text_field($_POST['address']);
            $new_region = sanitize_text_field($_POST['region']);
            $new_street = sanitize_text_field($_POST['street_address']);
            $new_zip = sanitize_text_field($_POST['zip_code']);

            wp_update_user([
                'ID' => $user_id,
                'user_email' => $new_email,
                'first_name' => $new_first,
                'last_name' => $new_last
            ]);

            $data = [
                'dob' => $new_dob,
                'phone' => $new_phone,
                'address' => $new_address,
                'region' => $new_region,
                'street_address' => $new_street,
                'zip_code' => $new_zip
            ];

            if ($extra) {
                $wpdb->update($table, $data, ['user_id' => $user_id]);
            } else {
                $data['user_id'] = $user_id;
                $wpdb->insert($table, $data);
            }

            wp_redirect(add_query_arg('updated', '1', get_permalink()));
            exit;
        }
    }

    ?>
    <style>
        .user-dashboard-container h2 {
            margin-top: 0;
            color: white;
        }

        .user-dashboard-container .form-group {
            margin-bottom: 20px;
        }

        .user-dashboard-container label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: white;
        }

        .user-dashboard-container input,
        .user-dashboard-container select {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: none;
            font-size: 16px;
            box-sizing: border-box;
        }

        .user-dashboard-container input[type="submit"] {
            background: #df1d3d;
            color: white;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            margin-top: 10px;
        }

        .user-dashboard-container input[type="submit"]:hover {
            background: #c0392b;
        }

        .user-dashboard-container img.profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            background-color: #666;
            display: block;
        }

        .user-dashboard-container button#editBtn {
            width: 100%;
            background: #3498db;
            color: white;
            padding: 10px;
            border: none;
            font-weight: bold;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 10px;
            cursor: pointer;
        }

        .user-dashboard-container button#editBtn:hover {
            background: #2980b9;
        }

        .user-dashboard-container input[readonly] {
            color: #fff !important;
            opacity: 1 !important;
            background-color: #4a4a4a;
        }

        .success-message {
            color: #2ecc71;
            padding: 15px;
            background: rgba(46, 204, 113, 0.1);
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #2ecc71;
        }
    </style>

    <div class="user-dashboard-wrapper">
        <div class="user-dashboard-container">
            <h2>Welcome, <?= esc_html($first_name ?: $username) ?> 👋</h2>

            <?php if (isset($_GET['updated']) && $_GET['updated'] == '1'): ?>
                <div class="success-message">✓ Dashboard updated successfully!</div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <?php wp_nonce_field('update_user_dashboard', 'dashboard_nonce'); ?>

                <?php if ($profile_img): ?>
                    <img id="profileImage" src="<?= esc_url($profile_img) ?>" class="profile-pic" alt="Profile Picture">
                <?php else: ?>
                    <img id="profileImage" src="<?= esc_url(get_avatar_url($user_id)) ?>" class="profile-pic"
                        alt="Profile Picture">
                <?php endif; ?>

                <div class="form-group">
                    <label for="profile_image">Change Profile Picture</label>
                    <input type="file" id="fileInput" name="profile_image" accept="image/*" readonly>
                </div>

                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?= esc_attr($first_name) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?= esc_attr($last_name) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?= esc_attr($dob) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= esc_attr($email) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= esc_attr($phone) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?= esc_attr($address) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Region</label>
                    <input type="text" name="region" value="<?= esc_attr($region) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Street Address</label>
                    <input type="text" name="street_address" value="<?= esc_attr($street_address) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>ZIP Code</label>
                    <input type="text" name="zip_code" value="<?= esc_attr($zip_code) ?>" readonly>
                </div>

                <button type="button" id="editBtn">✏️ Edit Profile</button>
                <input type="submit" name="update_dashboard" value="💾 Save Profile" style="display:none;" id="saveBtn">
            </form>
        </div>
    </div>

    <script>
        (function () {
            const editBtn = document.getElementById('editBtn');
            const saveBtn = document.getElementById('saveBtn');

            if (editBtn) {
                editBtn.addEventListener('click', function () {
                    const inputs = document.querySelectorAll('.user-dashboard-container form input');
                    inputs.forEach(input => {
                        if (input.hasAttribute('readonly')) {
                            input.removeAttribute('readonly');
                        }
                    });
                    this.style.display = 'none';
                    saveBtn.style.display = 'block';
                });
            }

            const fileInput = document.getElementById('fileInput');
            if (fileInput) {
                fileInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (!file || !file.type.startsWith('image/')) return;

                    const formData = new FormData();
                    formData.append('action', 'upload_dashboard_image');
                    formData.append('profile_image', file);
                    formData.append('_wpnonce', '<?php echo wp_create_nonce('dashboard_image_nonce'); ?>');

                    const profileImg = document.getElementById('profileImage');
                    profileImg.style.opacity = 0.5;

                    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            profileImg.style.opacity = 1;
                            if (data.success && data.url) {
                                profileImg.src = data.url + '?t=' + new Date().getTime();
                            } else {
                                alert(data.message || 'Upload failed.');
                            }
                        })
                        .catch(err => {
                            profileImg.style.opacity = 1;
                            alert('Upload failed. Please try again.');
                            console.error(err);
                        });
                });
            }
        })();
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('user_dashboard', 'user_dashboard_shortcode');

/**
 * Shortcode to display advertisements
 */
function advertisements_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'posts_per_page' => 10,
        'post_status' => 'publish',
        'category' => ''
    ), $atts);
    
    // Query advertisements
    $args = array(
        'post_type' => 'advertisement',
        'posts_per_page' => $atts['posts_per_page'],
        'post_status' => $atts['post_status']
    );
    
    // Add category filter if specified
    if (!empty($atts['category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'advertisement_category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ),
        );
    }
    
    $advertisements = new WP_Query($args);
    
    ob_start();
    
    if ($advertisements->have_posts()) :
        echo '<div class="advertisements-grid">';
        
        while ($advertisements->have_posts()) : $advertisements->the_post();
            echo '<div class="advertisement-item">';
            
            if (has_post_thumbnail()) {
                echo '<div class="advertisement-thumbnail">';
                echo '<a href="' . get_permalink() . '">';
                the_post_thumbnail('medium');
                echo '</a>';
                echo '</div>';
            }
            
            echo '<div class="advertisement-content">';
            echo '<h3 class="advertisement-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            
            // Display categories
            $categories = get_the_terms(get_the_ID(), 'advertisement_category');
            if ($categories && !is_wp_error($categories)) {
                echo '<div class="advertisement-categories">';
                foreach ($categories as $category) {
                    echo '<span class="category-tag">' . esc_html($category->name) . '</span> ';
                }
                echo '</div>';
            }
            
            if (has_excerpt()) {
                echo '<div class="advertisement-excerpt">';
                the_excerpt();
                echo '</div>';
            }
            
            echo '<div class="advertisement-meta">';
            echo '<span class="advertisement-date">' . get_the_date() . '</span>';
            echo '</div>';
            
            echo '</div>'; // .advertisement-content
            echo '</div>'; // .advertisement-item
        endwhile;
        
        echo '</div>'; // .advertisements-grid
        
        wp_reset_postdata();
    else :
        echo '<p>No advertisements found.</p>';
    endif;
    
    return ob_get_clean();
}
add_shortcode('advertisements', 'advertisements_shortcode');

/**
 * Shortcode for signup form
 */
function velvetreel_signup_form_shortcode() {
    ob_start();

    // Include the signup processing script
    include_once get_template_directory() . '/sign-up.php';

    return ob_get_clean();
}
add_shortcode('velvetreel_signup', 'velvetreel_signup_form_shortcode');