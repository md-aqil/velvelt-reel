<?php
/**
 * Template for displaying public talent profiles (no login required)
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Get the token from query var
$public_token = get_query_var('public_talent_token');

if (empty($public_token)) {
    // No token provided, show 404
    status_header(404);
    include get_query_var('template') ? get_query_var('template') : get_stylesheet_directory() . '/404.php';
    exit;
}

// Find the talent post with this token
$args = array(
    'post_type' => 'talent',
    'post_status' => 'publish',
    'meta_query' => array(
        array(
            'key' => '_talent_shareable_token',
            'value' => $public_token,
            'compare' => '='
        ),
        array(
            'key' => '_talent_public_sharing_enabled',
            'value' => '1',
            'compare' => '='
        )
    ),
    'posts_per_page' => 1
);

$query = new WP_Query($args);

if (!$query->have_posts()) {
    // Profile not found or sharing not enabled, show 404
    status_header(404);
    get_header();
    ?>
    <main id="content" class="site-main">
        <div class="talent-single-page">
            <div class="talent-profile-container">
                <div class="profile-card" style="text-align: center; padding: 60px 20px;">
                    <h1 style="color: #fff; margin-bottom: 12px;">Profile Not Available</h1>
                    <p style="color: #aaa;">This profile is either private or does not exist.</p>
                    <a href="<?php echo esc_url(home_url()); ?>" class="talent-cta-btn" style="margin: 20px auto 0; display: inline-flex;">Return to Home</a>
                </div>
            </div>
        </div>
    </main>
    <?php
    get_footer();
    exit;
}

// Load the post
while ($query->have_posts()):
    $query->the_post();
    $post_id = get_the_ID();
    $talent_name = get_the_title($post_id);
    
    $sharing_enabled = get_post_meta($post_id, '_talent_public_sharing_enabled', true);
    $saved_token = get_post_meta($post_id, '_talent_shareable_token', true);
    
    if ($sharing_enabled !== '1' || $saved_token !== $public_token) {
        status_header(404);
        get_header();
        ?>
        <main id="content" class="site-main" style="background: #000; color: #eee; min-height: 100vh;">
            <div class="talent-single-page">
                <div class="talent-profile-container">
                    <div class="profile-card" style="text-align: center; padding: 60px 20px;">
                        <h1 style="color: #fff; margin-bottom: 12px;">Profile Not Available</h1>
                        <p style="color: #aaa;">This profile is no longer available for public viewing.</p>
                        <a href="<?php echo esc_url(home_url()); ?>" class="talent-cta-btn" style="margin: 20px auto 0; display: inline-flex;">Return to Home</a>
                    </div>
                </div>
            </div>
        </main>
        <?php
        get_footer();
        exit;
    }

    // --- Get all the meta data for the talent (PUBLIC FIELDS ONLY) ---
    $role = get_post_meta($post_id, '_talent_role', true);
    $state = get_post_meta($post_id, '_talent_state', true);
    $country = get_post_meta($post_id, '_talent_country', true);
    $location = ($state && $country) ? "{$state}, {$country}" : ($state ?: $country);
    $work_type = get_post_meta($post_id, '_talent_affiliation', true);
    $agency_name = get_post_meta($post_id, '_talent_agency_name', true);
    $notable_works = get_post_meta($post_id, '_talent_notable_works', true);

    // Social Links (only if not hidden)
    $website_url = get_post_meta($post_id, '_talent_website', true);
    $instagram_url = get_post_meta($post_id, '_talent_instagram', true);
    $youtube_url = get_post_meta($post_id, '_talent_youtube', true);
    $tiktok_url = get_post_meta($post_id, '_talent_tiktok', true);
    $years_active = get_post_meta($post_id, '_talent_years_active', true);
    $education = get_post_meta($post_id, '_talent_education', true);
    $languages = get_post_meta($post_id, '_talent_languages', true);
    $available_for = get_post_meta($post_id, '_talent_available_for', true);
    $willing_to_travel = get_post_meta($post_id, '_talent_willing_to_travel', true);
    $brand_collabs = get_post_meta($post_id, '_talent_brand_collabs', true);
    $interested_projects = get_post_meta($post_id, '_talent_interested_projects', true);

    // Physical Attributes
    $height = get_post_meta($post_id, '_talent_height', true);
    $height_unit = get_post_meta($post_id, '_talent_height_unit', true) ?: 'cm';
    $weight = get_post_meta($post_id, '_talent_weight', true);
    $weight_unit = get_post_meta($post_id, '_talent_weight_unit', true) ?: 'kg';
    $complexion = get_post_meta($post_id, '_talent_complexion', true);
    $hair_color = get_post_meta($post_id, '_talent_hair_color', true);
    $dress_size = get_post_meta($post_id, '_talent_dress_size', true);
    $shirt_size = get_post_meta($post_id, '_talent_shirt_size', true);

    // Age
    $age = get_post_meta($post_id, '_talent_age', true);
    if (empty($age)) {
        $author_id = get_post_field('post_author', $post_id);
        $dob = get_post_meta($post_id, '_talent_dob', true);
        if (empty($dob) && $author_id) {
            $dob = get_user_meta($author_id, 'dob', true);
            if (empty($dob)) {
                global $wpdb;
                $info_table = $wpdb->prefix . 'userinformation';
                $dob = $wpdb->get_var($wpdb->prepare("SELECT dob FROM $info_table WHERE user_id = %d", $author_id));
            }
        }
        if (!empty($dob) && $dob !== '0000-00-00') {
            try {
                $birth_date = new DateTime($dob);
                $now = new DateTime('today');
                $diff = $birth_date->diff($now)->y;
                if ($diff > 0) {
                    $age = $diff;
                }
            } catch (Exception $e) {
                // Fallback
            }
        }
    }
    if (empty($age)) {
        $age = get_post_meta($post_id, '_talent_age_group', true);
    }

    // Privacy settings - IMPORTANT: Always respect these on public profiles
    $hide_website = get_post_meta($post_id, '_talent_hide_website', true);
    $hide_instagram = get_post_meta($post_id, '_talent_hide_instagram', true);
    $hide_youtube = get_post_meta($post_id, '_talent_hide_youtube', true);
    $hide_tiktok = get_post_meta($post_id, '_talent_hide_tiktok', true);

    // Portfolio data
    $fashion_categories = get_post_meta($post_id, '_talent_designCategories', true);
    $portfolio_image_ids = get_post_meta($post_id, '_talent_portfolio', true);
    
    // Helper functions
    $get_attachment_url_fallback = function($attachment_id) {
        $url = wp_get_attachment_url($attachment_id);
        if (empty($url)) {
            global $wpdb;
            $guid = $wpdb->get_var($wpdb->prepare(
                "SELECT guid FROM $wpdb->posts WHERE ID = %d AND post_type = 'attachment' LIMIT 1",
                $attachment_id
            ));
            if ($guid) {
                $url = $guid;
            }
        }
        if (empty($url)) {
            $post_exists = get_post($attachment_id);
            if ($post_exists) {
                $upload_dir = wp_upload_dir();
                $url = $upload_dir['baseurl'] . '/' . basename($post_exists->guid);
            }
        }
        return $url;
    };

    // Helper function to build item
    $build_portfolio_item = function($attachment_id) use ($get_attachment_url_fallback) {
        $attachment_id = intval($attachment_id);
        if ($attachment_id <= 0) {
            return null;
        }
        $mime_type = get_post_mime_type($attachment_id) ?: '';
        $is_video = $mime_type && strpos($mime_type, 'video/') === 0;
        $attachment_url = $get_attachment_url_fallback($attachment_id);
        if (empty($attachment_url)) {
            return null;
        }

        $thumbnail_url = '';
        if ($is_video) {
            $thumb_id = get_post_meta($attachment_id, '_thumbnail_id', true);
            if ($thumb_id) {
                $thumbnail_url = wp_get_attachment_image_url($thumb_id, 'large');
            }
            if (empty($thumbnail_url)) {
                $img = wp_get_attachment_image_url($attachment_id, 'large');
                if ($img && !preg_match('/\.(mp4|webm|mov|m4v|ogv)$/i', $img)) {
                    $thumbnail_url = $img;
                }
            }
        } else {
            $thumbnail_url = wp_get_attachment_image_url($attachment_id, 'large');
            if (empty($thumbnail_url)) {
                $thumbnail_url = $attachment_url;
            }
        }

        return [
            'id' => $attachment_id,
            'url' => $attachment_url,
            'thumbnail_url' => $thumbnail_url,
            'is_video' => $is_video,
            'mime_type' => $mime_type
        ];
    };
    
    // Prepare portfolio items
    $portfolio_items = [];
    if (!empty($portfolio_image_ids)) {
        if (is_array($portfolio_image_ids)) {
            $flat_ids = [];
            array_walk_recursive($portfolio_image_ids, function($item) use (&$flat_ids) {
                if (is_numeric($item)) {
                    $flat_ids[] = intval($item);
                }
            });
            
            foreach ($flat_ids as $attachment_id) {
                $item = $build_portfolio_item($attachment_id);
                if ($item) {
                    $portfolio_items[] = $item;
                }
            }
        } elseif (is_string($portfolio_image_ids)) {
            $ids = explode(',', $portfolio_image_ids);
            foreach ($ids as $id) {
                $item = $build_portfolio_item($id);
                if ($item) {
                    $portfolio_items[] = $item;
                }
            }
        } elseif (is_numeric($portfolio_image_ids)) {
            $item = $build_portfolio_item($portfolio_image_ids);
            if ($item) {
                $portfolio_items[] = $item;
            }
        }
    }

    // Section Availability Flags
    $biography = get_post_field('post_content', $post_id);
    $has_bio = !empty($biography);
    $has_physical_attributes = !empty($age) || !empty($height) || !empty($weight) || !empty($complexion) || 
                               !empty($hair_color) || !empty($dress_size) || !empty($shirt_size);
    $has_portfolio = !empty($portfolio_items);
    $has_experience = !empty($notable_works) && is_array($notable_works);
    $has_fashion = !empty($fashion_categories) && is_array($fashion_categories);
    $has_education = !empty($education);
    $has_preferences = (!empty($available_for) && is_array($available_for)) || !empty($willing_to_travel) || !empty($brand_collabs) || !empty($interested_projects);

    $role_clean = $role ? ucwords(str_replace(['-', '_'], ' ', $role)) : 'Talent';

    // Add public profile body class
    add_filter('body_class', function($classes) {
        $classes[] = 'public-profile';
        return $classes;
    });

    get_header();
    ?>

    <main id="content" class="site-main talent-single-page" style="background: #0a0a0a; color: #eee; min-height: 100vh;">
        <div class="talent-profile-container">
            
            <!-- Hero Profile Header Card -->
            <header class="talent-hero-card" style="margin-top:20px;">
                <div class="talent-hero-media">
                    <div class="talent-avatar-wrap">
                        <?php if (has_post_thumbnail($post_id)): ?>
                            <?php echo get_the_post_thumbnail($post_id, 'large', array('class' => 'talent-avatar-img', 'alt' => $talent_name)); ?>
                        <?php else: ?>
                            <div class="talent-avatar-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <span class="talent-badge-status" title="Verified Public Profile">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="talent-hero-details">
                    <div class="talent-meta-top">
                        <?php if ($role): ?>
                            <span class="talent-role-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                <?php echo esc_html($role_clean); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($work_type): ?>
                            <span class="talent-affiliation-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                                <?php echo esc_html($work_type); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($years_active): ?>
                            <span class="talent-experience-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <?php echo esc_html($years_active); ?> Experience
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="talent-name"><?php echo esc_html($talent_name); ?></h1>

                    <?php if ($location): ?>
                        <div class="talent-location">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span><?php echo esc_html($location); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="talent-socials-bar">
                        <?php if ($website_url && !$hide_website): ?>
                            <a href="<?php echo esc_url($website_url); ?>" class="talent-social-pill" target="_blank" rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                </svg>
                                <span>Website</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($instagram_url && !$hide_instagram): ?>
                            <a href="<?php echo esc_url('https://instagram.com/' . ltrim($instagram_url, '@')); ?>" class="talent-social-pill instagram" target="_blank" rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                </svg>
                                <span>Instagram</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($tiktok_url && !$hide_tiktok): ?>
                            <a href="<?php echo esc_url('https://tiktok.com/@' . ltrim($tiktok_url, '@')); ?>" class="talent-social-pill tiktok" target="_blank" rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M23 9a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2m-9 3a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2m-9 3a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2"></path>
                                </svg>
                                <span>TikTok</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($youtube_url && !$hide_youtube): ?>
                            <a href="<?php echo esc_url($youtube_url); ?>" class="talent-social-pill youtube" target="_blank" rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/>
                                    <path d="M10 15l10.3-7.5L10 15Z"/>
                                </svg>
                                <span>YouTube</span>
                            </a>
                        <?php endif; ?>

                        <a href="#contact-talent" class="talent-cta-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <span>Contact Us</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Sticky Quick Links Navigation Bar -->
            <nav class="talent-sticky-nav" aria-label="Profile Quick Navigation">
                <div class="talent-sticky-nav-inner">
                    <span class="sticky-nav-title">Quick Links:</span>
                    <ul class="sticky-nav-list">
                        <?php if ($has_bio): ?>
                            <li><a href="#biography" class="nav-pill">About</a></li>
                        <?php endif; ?>
                        <?php if ($has_portfolio): ?>
                            <li><a href="#portfolio" class="nav-pill">Portfolio <span class="pill-count"><?php echo count($portfolio_items); ?></span></a></li>
                        <?php endif; ?>
                        <?php if ($has_physical_attributes): ?>
                            <li><a href="#physical-attributes" class="nav-pill">Physical Stats</a></li>
                        <?php endif; ?>
                        <?php if ($has_experience): ?>
                            <li><a href="#experience" class="nav-pill">Experience</a></li>
                        <?php endif; ?>
                        <?php if ($has_fashion): ?>
                            <li><a href="#fashion-categories" class="nav-pill">Specialities</a></li>
                        <?php endif; ?>
                        <?php if ($has_education): ?>
                            <li><a href="#education" class="nav-pill">Education</a></li>
                        <?php endif; ?>
                        <li><a href="#contact-talent" class="nav-pill contact-pill">Contact Us</a></li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content 2-Column Grid -->
            <div class="talent-main-layout">
                
                <!-- Left Sidebar Column (Stats, Representation, Preferences, Contact Form) -->
                <aside class="talent-sidebar-column">

                    <!-- Physical Stats Card -->
                    <?php if ($has_physical_attributes): ?>
                        <div class="profile-card stats-card" id="physical-attributes">
                            <div class="card-header">
                                <div class="card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <h3 class="card-title">Physical Stats</h3>
                            </div>
                            <div class="stats-grid">
                                <?php if (!empty($age)): ?>
                                    <div class="stat-item">
                                        <span class="stat-label">Age</span>
                                        <span class="stat-value"><?php echo esc_html($age); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($height): ?>
                                    <div class="stat-item">
                                        <span class="stat-label">Height</span>
                                        <span class="stat-value"><?php echo esc_html($height . ' ' . $height_unit); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($weight): ?>
                                    <div class="stat-item">
                                        <span class="stat-label">Weight</span>
                                        <span class="stat-value"><?php echo esc_html($weight . ' ' . $weight_unit); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($complexion): ?>
                                    <div class="stat-item">
                                        <span class="stat-label">Complexion</span>
                                        <span class="stat-value"><?php echo esc_html($complexion); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($hair_color): ?>
                                    <div class="stat-item">
                                        <span class="stat-label">Hair Color</span>
                                        <span class="stat-value"><?php echo esc_html($hair_color); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($dress_size): ?>
                                    <div class="stat-item">
                                        <span class="stat-label">Dress Size</span>
                                        <span class="stat-value"><?php echo esc_html($dress_size); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($shirt_size): ?>
                                    <div class="stat-item">
                                        <span class="stat-label">Shirt Size</span>
                                        <span class="stat-value"><?php echo esc_html($shirt_size); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Languages -->
                    <?php if (!empty($languages) && is_array($languages)): ?>
                        <div class="profile-card languages-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                    </svg>
                                </div>
                                <h3 class="card-title">Languages</h3>
                            </div>
                            <div class="tag-chips-wrap">
                                <?php foreach ($languages as $lang): ?>
                                    <span class="tag-chip"><?php echo esc_html($lang); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Fashion Categories -->
                    <?php if ($has_fashion): ?>
                        <div class="profile-card fashion-card" id="fashion-categories">
                            <div class="card-header">
                                <div class="card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                        <polyline points="2 17 12 22 22 17"></polyline>
                                        <polyline points="2 12 12 17 22 12"></polyline>
                                    </svg>
                                </div>
                                <h3 class="card-title">Specialities & Styles</h3>
                            </div>
                            <div class="tag-chips-wrap">
                                <?php foreach ($fashion_categories as $category): ?>
                                    <span class="tag-chip accent"><?php echo esc_html($category); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Representation -->
                    <?php if ($agency_name || $work_type): ?>
                        <div class="profile-card agency-card" id="representation">
                            <div class="card-header">
                                <div class="card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                </div>
                                <h3 class="card-title">Representation</h3>
                            </div>
                            <div class="agency-details">
                                <?php if ($agency_name): ?>
                                    <div class="agency-badge-row">
                                        <span class="agency-label">Agency:</span>
                                        <strong class="agency-name"><?php echo esc_html($agency_name); ?></strong>
                                    </div>
                                <?php endif; ?>
                                <?php if ($work_type): ?>
                                    <div class="agency-badge-row">
                                        <span class="agency-label">Affiliation:</span>
                                        <span class="agency-val"><?php echo esc_html($work_type); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Availability & Preferences -->
                    <?php if ($has_preferences): ?>
                        <div class="profile-card preferences-card">
                            <div class="card-header">
                                <div class="card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 14 14"></polyline>
                                    </svg>
                                </div>
                                <h3 class="card-title">Availability & Projects</h3>
                            </div>
                            <div class="preferences-content">
                                <?php if (!empty($available_for) && is_array($available_for)): ?>
                                    <div class="pref-group">
                                        <span class="pref-label">Available For:</span>
                                        <div class="tag-chips-wrap sm">
                                            <?php foreach ($available_for as $item): ?>
                                                <span class="tag-chip sm"><?php echo esc_html($item); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="pref-flags-row">
                                    <?php if ($willing_to_travel): ?>
                                        <div class="pref-flag-item <?php echo ($willing_to_travel === 'on') ? 'active' : ''; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="2" y1="12" x2="22" y2="12"></line>
                                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                            </svg>
                                            <span>Travel: <?php echo ($willing_to_travel === 'on') ? 'Willing to Travel' : 'Local Only'; ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($brand_collabs): ?>
                                        <div class="pref-flag-item <?php echo ($brand_collabs === 'on') ? 'active' : ''; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                            </svg>
                                            <span>Collabs: <?php echo ($brand_collabs === 'on') ? 'Open to Collabs' : 'Not at this time'; ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($interested_projects): ?>
                                    <div class="pref-group projects-seeking">
                                        <span class="pref-label">Interested Projects:</span>
                                        <p class="seeking-text"><?php echo nl2br(esc_html($interested_projects)); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Direct Contact Card (Protected on Public Profile) -->
                    <div class="profile-card contact-card" id="contact-info">
                        <div class="card-header">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                            <h3 class="card-title">Booking Contact</h3>
                        </div>
                        <ul class="contact-info-list">
                            <li>
                                <span class="contact-type">Email:</span>
                                <span class="contact-val muted">Available upon request</span>
                            </li>
                            <li>
                                <span class="contact-type">Phone:</span>
                                <span class="contact-val muted">Available upon request</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Us Form Card (Placed in Sidebar) -->
                    <div class="profile-card booking-sidebar-card" id="contact-talent">
                        <div class="card-header">
                            <div class="card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <h3 class="card-title">Contact Us</h3>
                        </div>
                        <div class="booking-card-intro">
                            <p>Have questions about our products? Reach out to us.</p>
                        </div>
                        <div class="sidebar-contact-form">
                            <?php echo do_shortcode('[contact-form-7 id="d81cd32" title="Contact form 1"]'); ?>
                        </div>
                    </div>

                </aside>

                <!-- Right Main Column -->
                <div class="talent-content-column">

                    <!-- Biography Card -->
                    <?php if ($has_bio): ?>
                        <section class="profile-card biography-card" id="biography">
                            <div class="card-header">
                                <div class="card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                </div>
                                <h2 class="card-title">About <?php echo esc_html($talent_name); ?></h2>
                            </div>
                            <div class="biography-body">
                                <?php echo apply_filters('the_content', $biography); ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Portfolio Gallery Card -->
                    <?php if ($has_portfolio): ?>
                        <section class="profile-card portfolio-card" id="portfolio">
                            <div class="card-header space-between">
                                <div class="header-left">
                                    <div class="card-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                    </div>
                                    <h2 class="card-title">Portfolio & Media</h2>
                                </div>
                                <span class="gallery-count-badge"><?php echo count($portfolio_items); ?> Items</span>
                            </div>

                            <div class="portfolio-showcase-grid">
                                <?php foreach ($portfolio_items as $index => $item): ?>
                                    <?php if ($item['is_video']): ?>
                                        <!-- Video Item -->
                                        <div class="portfolio-item video-item" data-index="<?php echo $index; ?>" data-type="video" data-attachment-id="<?php echo esc_attr($item['id']); ?>" data-video-url="<?php echo esc_url($item['url']); ?>" title="Click to play video">
                                            <video <?php if (!empty($item['thumbnail_url'])): ?>poster="<?php echo esc_url($item['thumbnail_url']); ?>"<?php endif; ?> playsinline muted preload="auto" src="<?php echo esc_url($item['url']); ?>#t=0.001">
                                                <source src="<?php echo esc_url($item['url']); ?>#t=0.001" type="<?php echo esc_attr(!empty($item['mime_type']) ? $item['mime_type'] : 'video/mp4'); ?>">
                                            </video>
                                            <div class="portfolio-overlay">
                                                <span class="play-btn-circle">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                                                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                                    </svg>
                                                </span>
                                                <span class="media-type-tag">Video</span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- Image Item -->
                                        <div class="portfolio-item image-item" data-index="<?php echo $index; ?>" data-type="image" title="Click to view full photo">
                                            <img src="<?php echo esc_url($item['thumbnail_url']); ?>" 
                                                 alt="<?php echo esc_attr($talent_name); ?> - Portfolio media <?php echo $index + 1; ?>" 
                                                 loading="lazy"
                                                 oncontextmenu="return false;" 
                                                 ondragstart="return false;" 
                                                 onselectstart="return false;">
                                            <div class="portfolio-overlay">
                                                <span class="zoom-btn-circle">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <circle cx="11" cy="11" r="8"></circle>
                                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                                        <line x1="11" y1="8" x2="11" y2="14"></line>
                                                        <line x1="8" y1="11" x2="14" y2="11"></line>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Experience & Notable Works Timeline -->
                    <?php if ($has_experience): ?>
                        <section class="profile-card experience-card" id="experience">
                            <div class="card-header">
                                <div class="card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                    </svg>
                                </div>
                                <h2 class="card-title">Experience & Notable Works</h2>
                            </div>

                            <div class="credits-timeline">
                                <?php foreach ($notable_works as $work): ?>
                                    <?php if (!empty($work['title'])): ?>
                                        <?php
                                        $start_date = !empty($work['startDate']) ? date('M Y', strtotime($work['startDate'])) : '';
                                        if (isset($work['present']) && $work['present'] === 'on') {
                                            $end_date = 'Present';
                                        } elseif (!empty($work['endDate'])) {
                                            $end_date = date('M Y', strtotime($work['endDate']));
                                        } else {
                                            $end_date = '';
                                        }

                                        if ($start_date && $end_date) {
                                            $date_range = "{$start_date} – {$end_date}";
                                        } elseif ($start_date) {
                                            $date_range = "{$start_date} – Present";
                                        } elseif ($end_date) {
                                            $date_range = $end_date;
                                        } else {
                                            $date_range = '';
                                        }
                                        ?>
                                        <div class="timeline-credit-item">
                                            <div class="timeline-dot"></div>
                                            <div class="credit-main">
                                                <div class="credit-header">
                                                    <h4 class="credit-title"><?php echo esc_html($work['title']); ?></h4>
                                                    <?php if ($date_range): ?>
                                                        <span class="credit-date"><?php echo esc_html($date_range); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (!empty($work['role'])): ?>
                                                    <span class="credit-role-tag"><?php echo esc_html($work['role']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Education & Training Card -->
                    <?php if ($has_education): ?>
                        <section class="profile-card education-card" id="education">
                            <div class="card-header">
                                <div class="card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                    </svg>
                                </div>
                                <h2 class="card-title">Education & Professional Training</h2>
                            </div>
                            <div class="education-content">
                                <p class="education-text"><?php echo nl2br(esc_html($education)); ?></p>
                            </div>
                        </section>
                    <?php endif; ?>

                </div>
            </div>

        </div>
        
        <!-- Public Profile Notice -->
        <div class="public-profile-notice" style="text-align: center; padding: 24px 0 40px; color: #888; font-size: 13px;">
            <p>This is an official public profile verified by <a href="<?php echo esc_url(home_url()); ?>" style="color: #ff4d6d; text-decoration: none; font-weight: 600;">The VelvetReel</a>.</p>
        </div>

    </main>

    <!-- Portfolio Gallery Modal -->
    <div id="portfolio-modal" class="portfolio-modal" aria-hidden="true" role="dialog">
        <span class="portfolio-modal-close" aria-label="Close modal">&times;</span>
        <button class="portfolio-modal-prev" aria-label="Previous item">&#10094;</button>
        <button class="portfolio-modal-next" aria-label="Next item">&#10095;</button>
        <div class="portfolio-modal-content">
            <img id="portfolio-modal-image" src="" alt="Portfolio image">
            <div id="portfolio-modal-video" class="portfolio-modal-video">
                <video id="portfolio-modal-player" controls playsinline preload="auto"></video>
                <iframe id="portfolio-modal-iframe" src="" frameborder="0" allow="autoplay; fullscreen; encrypted-media; accelerometer; gyroscope; picture-in-picture" allowfullscreen loading="eager"></iframe>
            </div>
            <div class="portfolio-modal-counter">
                <span id="portfolio-modal-current">1</span> / <span id="portfolio-modal-total">0</span>
            </div>
        </div>
    </div>

    <?php
    get_footer();
endwhile;

wp_reset_postdata();
