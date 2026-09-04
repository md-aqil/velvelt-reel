<?php
/**
 * The template for displaying a single Talent profile.
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Check if user is logged in - any logged in user can view talent profiles
$has_access = is_user_logged_in();

if ($has_access) {
    get_header();
    
    while (have_posts()):
        the_post();
    
        // --- Get all the meta data for the talent ---
        $post_id = get_the_ID();
        $talent_name = get_the_title();
        $role = get_post_meta($post_id, '_talent_role', true);
        $state = get_post_meta($post_id, '_talent_state', true);
        $country = get_post_meta($post_id, '_talent_country', true);
        $location = ($state && $country) ? "{$state}, {$country}" : ($state ?: $country);
        $work_type = get_post_meta($post_id, '_talent_affiliation', true);
        $agency_name = get_post_meta($post_id, '_talent_agency_name', true);
        $notable_works = get_post_meta($post_id, '_talent_notable_works', true);
    
        // --- Contact & Socials ---
        $email = get_post_meta($post_id, '_talent_email', true);
        $phone = get_post_meta($post_id, '_talent_phone', true);
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
        
        // --- Physical Attributes ---
        $height = get_post_meta($post_id, '_talent_height', true);
        $height_unit = get_post_meta($post_id, '_talent_height_unit', true) ?: 'cm';
        $weight = get_post_meta($post_id, '_talent_weight', true);
        $weight_unit = get_post_meta($post_id, '_talent_weight_unit', true) ?: 'kg';
        $complexion = get_post_meta($post_id, '_talent_complexion', true);
        $bust_size = get_post_meta($post_id, '_talent_bust_size', true);
        $hair_color = get_post_meta($post_id, '_talent_hair_color', true);
        $dress_size = get_post_meta($post_id, '_talent_dress_size', true);
        $shirt_size = get_post_meta($post_id, '_talent_shirt_size', true);
    
        // Privacy settings
        $hide_email = get_post_meta($post_id, '_talent_hide_email', true);
        $hide_phone = get_post_meta($post_id, '_talent_hide_phone', true);
        $hide_website = get_post_meta($post_id, '_talent_hide_website', true);
        $hide_instagram = get_post_meta($post_id, '_talent_hide_instagram', true);
        $hide_youtube = get_post_meta($post_id, '_talent_hide_youtube', true);
        $hide_tiktok = get_post_meta($post_id, '_talent_hide_tiktok', true);
    
        // --- Portfolio Data ---
        $fashion_categories = get_post_meta($post_id, '_talent_designCategories', true);
        $portfolio_image_ids = get_post_meta($post_id, '_talent_portfolio', true);
        
        // Helper function to get attachment URL with fallback
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

        // Helper function to get thumbnail URL with fallback
        $get_thumbnail_url_fallback = function($attachment_id, $size = 'medium') use ($get_attachment_url_fallback) {
            $url = wp_get_attachment_image_url($attachment_id, $size);
            if (empty($url)) {
                $url = $get_attachment_url_fallback($attachment_id);
            }
            return $url;
        };
    
        // Prepare portfolio items array
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
                    $mime_type = get_post_mime_type($attachment_id);
                    $is_video = $mime_type && strpos($mime_type, 'video/') === 0;
                    $attachment_url = $get_attachment_url_fallback($attachment_id);
                    $thumbnail_url = $get_thumbnail_url_fallback($attachment_id, 'large');
                    
                    if (!empty($attachment_url)) {
                        $portfolio_items[] = [
                            'id' => $attachment_id,
                            'url' => $attachment_url,
                            'thumbnail_url' => $thumbnail_url,
                            'is_video' => $is_video,
                            'mime_type' => $mime_type
                        ];
                    }
                }
            } elseif (is_string($portfolio_image_ids)) {
                $ids = explode(',', $portfolio_image_ids);
                foreach ($ids as $id) {
                    $id = intval(trim($id));
                    if ($id > 0) {
                        $mime_type = get_post_mime_type($id);
                        $is_video = $mime_type && strpos($mime_type, 'video/') === 0;
                        $attachment_url = $get_attachment_url_fallback($id);
                        $thumbnail_url = $get_thumbnail_url_fallback($id, 'large');
                        
                        if (!empty($attachment_url)) {
                            $portfolio_items[] = [
                                'id' => $id,
                                'url' => $attachment_url,
                                'thumbnail_url' => $thumbnail_url,
                                'is_video' => $is_video,
                                'mime_type' => $mime_type
                            ];
                        }
                    }
                }
            } elseif (is_numeric($portfolio_image_ids)) {
                $attachment_id = intval($portfolio_image_ids);
                $mime_type = get_post_mime_type($attachment_id);
                $is_video = $mime_type && strpos($mime_type, 'video/') === 0;
                $attachment_url = $get_attachment_url_fallback($attachment_id);
                $thumbnail_url = $get_thumbnail_url_fallback($attachment_id, 'large');
                
                if (!empty($attachment_url)) {
                    $portfolio_items[] = [
                        'id' => $attachment_id,
                        'url' => $attachment_url,
                        'thumbnail_url' => $thumbnail_url,
                        'is_video' => $is_video,
                        'mime_type' => $mime_type
                    ];
                }
            }
        }

        // Section Availability Flags
        $has_bio = !empty(get_the_content());
        $has_physical_attributes = !empty($height) || !empty($weight) || !empty($complexion) || 
                                   !empty($bust_size) || !empty($hair_color) || 
                                   !empty($dress_size) || !empty($shirt_size);
        $has_portfolio = !empty($portfolio_items);
        $has_experience = !empty($notable_works) && is_array($notable_works);
        $has_fashion = !empty($fashion_categories) && is_array($fashion_categories);
        $has_education = !empty($education);
        $has_preferences = (!empty($available_for) && is_array($available_for)) || !empty($willing_to_travel) || !empty($brand_collabs) || !empty($interested_projects);

        // Pluralized Role for Section Title
        $role_clean = $role ? ucwords(str_replace(['-', '_'], ' ', $role)) : 'Talent';
        if (preg_match('/(s|sh|ch|x|z)$/i', $role_clean)) {
            $role_plural = $role_clean . 'es';
        } else {
            $role_plural = $role_clean . 's';
        }
        ?>

        <main id="content" class="site-main talent-single-page">
            <div class="talent-profile-container">

                <!-- Hero Profile Header Card -->
                <header class="talent-hero-card">
                    <div class="talent-hero-media">
                        <div class="talent-avatar-wrap">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('large', array(
                                    'class' => 'talent-avatar-img',
                                    'alt' => $talent_name . ' - ' . $role_clean . ' Portfolio Photo'
                                )); ?>
                            <?php else: ?>
                                <div class="talent-avatar-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <span class="talent-badge-status" title="Verified VelvetReel Profile">
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

                        <!-- Social & Web Link Pills -->
                        <div class="talent-socials-bar">
                            <?php if ($website_url && !$hide_website): ?>
                                <a href="<?php echo esc_url($website_url); ?>" class="talent-social-pill" target="_blank" rel="noopener noreferrer" title="Official Website">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                    </svg>
                                    <span>Website</span>
                                </a>
                            <?php endif; ?>

                            <?php if ($instagram_url && !$hide_instagram): ?>
                                <a href="<?php echo esc_url('https://instagram.com/' . ltrim($instagram_url, '@')); ?>" class="talent-social-pill instagram" target="_blank" rel="noopener noreferrer" title="Instagram">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                    </svg>
                                    <span>Instagram</span>
                                </a>
                            <?php endif; ?>

                            <?php if ($youtube_url && !$hide_youtube): ?>
                                <a href="<?php echo esc_url($youtube_url); ?>" class="talent-social-pill youtube" target="_blank" rel="noopener noreferrer" title="YouTube">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/>
                                        <path d="M10 15l10.3-7.5L10 15Z"/>
                                    </svg>
                                    <span>YouTube</span>
                                </a>
                            <?php endif; ?>

                            <?php if ($tiktok_url && !$hide_tiktok): ?>
                                <a href="<?php echo esc_url('https://tiktok.com/@' . ltrim($tiktok_url, '@')); ?>" class="talent-social-pill tiktok" target="_blank" rel="noopener noreferrer" title="TikTok">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M23 9a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2m-9 3a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2m-9 3a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2"></path>
                                    </svg>
                                    <span>TikTok</span>
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
                            <li><a href="#more-talents" class="nav-pill">Similar <?php echo esc_html($role_plural); ?></a></li>
                            <li><a href="#contact-talent" class="nav-pill contact-pill">Contact Us</a></li>
                        </ul>
                    </div>
                </nav>

                <!-- Main Content 2-Column Grid -->
                <div class="talent-main-layout">
                    
                    <!-- Left Sidebar Column (Stats, Attributes, Representation, Preferences, Direct Contact, Contact Form) -->
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
                                    <?php if ($bust_size): ?>
                                        <div class="stat-item">
                                            <span class="stat-label">Bust / Chest</span>
                                            <span class="stat-value"><?php echo esc_html($bust_size); ?></span>
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

                        <!-- Spoken Languages & Specialities -->
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

                        <!-- Fashion / Creative Categories -->
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

                        <!-- Representation & Agency -->
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

                        <!-- Availability & Preferences Card -->
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

                        <!-- Direct Contact Card -->
                        <div class="profile-card contact-card" id="contact-info">
                            <div class="card-header">
                                <div class="card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                </div>
                                <h3 class="card-title">Direct Contact</h3>
                            </div>
                            <ul class="contact-info-list">
                                <li>
                                    <span class="contact-type">Email:</span>
                                    <?php if ($email && !($hide_email === '1' || $hide_email === true)): ?>
                                        <a href="mailto:<?php echo esc_attr($email); ?>" class="contact-val"><?php echo esc_html($email); ?></a>
                                    <?php else: ?>
                                        <span class="contact-val muted">contact@thevelvetreel.com</span>
                                    <?php endif; ?>
                                </li>
                                <li>
                                    <span class="contact-type">Phone:</span>
                                    <?php if ($phone && !($hide_phone === '1' || $hide_phone === true)): ?>
                                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" class="contact-val"><?php echo esc_html($phone); ?></a>
                                    <?php else: ?>
                                        <span class="contact-val muted">+1-888-585-5396</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>

                        <!-- Contact Us Form Card (Placed right below Direct Contact) -->
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

                    <!-- Right Main Column (Biography, Portfolio Showcase, Experience Timeline, Education) -->
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
                                    <?php the_content(); ?>
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
                                                <video poster="<?php echo esc_url($item['thumbnail_url']); ?>" playsinline muted preload="metadata">
                                                    <source src="<?php echo esc_url($item['url']); ?>" type="video/mp4">
                                                </video>
                                                <div class="portfolio-overlay">
                                                    <span class="play-btn-circle">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
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

                <!-- Related Talents Showcase ("More [Role]s Like [Name]") -->
                <section class="related-talents-showcase" id="more-talents">
                    <div class="related-section-header">
                        <div class="related-title-wrap">
                            <span class="section-eyebrow">Discover Talent</span>
                            <h2 class="related-title">More <?php echo esc_html($role_plural); ?> Like <?php echo esc_html($talent_name); ?></h2>
                        </div>
                        <a href="<?php echo esc_url(home_url('/talent/')); ?>" class="view-all-talents-link">
                            <span>Explore All Talents</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>

                    <div class="related-talents-grid">
                        <?php
                        $related_args = array(
                            'post_type' => 'talent',
                            'posts_per_page' => 4,
                            'post__not_in' => array($post_id),
                            'post_status' => 'publish',
                            'meta_query' => array(
                                array(
                                    'key' => '_talent_role',
                                    'value' => $role,
                                    'compare' => '='
                                )
                            )
                        );
                        $related_query = new WP_Query($related_args);

                        // If not enough talents in the same role, query latest talents as fallback
                        if ($related_query->post_count < 4) {
                            $existing_ids = array_merge([$post_id], wp_list_pluck($related_query->posts, 'ID'));
                            $needed = 4 - $related_query->post_count;
                            $fallback_query = new WP_Query(array(
                                'post_type' => 'talent',
                                'posts_per_page' => $needed,
                                'post__not_in' => $existing_ids,
                                'post_status' => 'publish'
                            ));
                        }

                        if ($related_query->have_posts() || (isset($fallback_query) && $fallback_query->have_posts())):
                            while ($related_query->have_posts()): $related_query->the_post();
                                $card_role = get_post_meta(get_the_ID(), '_talent_role', true);
                                $card_state = get_post_meta(get_the_ID(), '_talent_state', true);
                                $card_country = get_post_meta(get_the_ID(), '_talent_country', true);
                                $card_loc = ($card_state && $card_country) ? "{$card_state}, {$card_country}" : ($card_state ?: $card_country);
                        ?>
                            <a href="<?php the_permalink(); ?>" class="talent-spotlight-card">
                                <div class="talent-card-image-wrap">
                                    <?php if (has_post_thumbnail()): ?>
                                        <?php the_post_thumbnail('medium_large', array('alt' => get_the_title() . ' - Talent Profile')); ?>
                                    <?php else: ?>
                                        <div class="talent-photo-placeholder">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    <div class="talent-card-scrim"></div>
                                    <?php if ($card_role): ?>
                                        <span class="card-role-badge"><?php echo esc_html(ucwords(str_replace(['-', '_'], ' ', $card_role))); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="talent-card-content">
                                    <h4 class="talent-card-name"><?php the_title(); ?></h4>
                                    <?php if ($card_loc): ?>
                                        <p class="talent-card-loc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <span><?php echo esc_html($card_loc); ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php 
                            endwhile;
                            wp_reset_postdata();

                            // Output fallback posts if query was under 4
                            if (isset($fallback_query) && $fallback_query->have_posts()):
                                while ($fallback_query->have_posts()): $fallback_query->the_post();
                                    $card_role = get_post_meta(get_the_ID(), '_talent_role', true);
                                    $card_state = get_post_meta(get_the_ID(), '_talent_state', true);
                                    $card_country = get_post_meta(get_the_ID(), '_talent_country', true);
                                    $card_loc = ($card_state && $card_country) ? "{$card_state}, {$card_country}" : ($card_state ?: $card_country);
                        ?>
                            <a href="<?php the_permalink(); ?>" class="talent-spotlight-card">
                                <div class="talent-card-image-wrap">
                                    <?php if (has_post_thumbnail()): ?>
                                        <?php the_post_thumbnail('medium_large', array('alt' => get_the_title() . ' - Talent Profile')); ?>
                                    <?php else: ?>
                                        <div class="talent-photo-placeholder">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    <div class="talent-card-scrim"></div>
                                    <?php if ($card_role): ?>
                                        <span class="card-role-badge"><?php echo esc_html(ucwords(str_replace(['-', '_'], ' ', $card_role))); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="talent-card-content">
                                    <h4 class="talent-card-name"><?php the_title(); ?></h4>
                                    <?php if ($card_loc): ?>
                                        <p class="talent-card-loc">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            <span><?php echo esc_html($card_loc); ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php
                                endwhile;
                                wp_reset_postdata();
                            endif;
                        endif;
                        ?>
                    </div>
                </section>

                <!-- Admin & Owner Share Section -->
                <?php 
                $is_admin = current_user_can('manage_options');
                $is_owner = (get_post_field('post_author', $post_id) == get_current_user_id());
                
                if ($is_admin || $is_owner) : 
                    $sharing_enabled = get_post_meta($post_id, '_talent_public_sharing_enabled', true);
                    $share_token = get_post_meta($post_id, '_talent_shareable_token', true);
                    $nonce = wp_create_nonce('shareable_token_nonce');
                ?>
                    <div class="admin-share-section">
                        <div class="admin-share-header">
                            <div class="admin-share-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="18" cy="5" r="3"></circle>
                                    <circle cx="6" cy="12" r="3"></circle>
                                    <circle cx="18" cy="19" r="3"></circle>
                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                </svg>
                            </div>
                            <div class="admin-share-info">
                                <h3>Share This Profile Publicly</h3>
                                <p>Generate a secure, direct link to share this portfolio with casting directors & clients without requiring a login.</p>
                            </div>
                        </div>
                        
                        <div class="sharing-controls">
                            <div class="sharing-controls-left">
                                <label class="ios-switch">
                                    <input type="checkbox" class="sharing-toggle-switch" data-talent-id="<?php echo esc_attr($post_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" <?php checked($sharing_enabled, '1'); ?>>
                                    <span class="switch-slider"></span>
                                </label>
                                <span class="switch-label"><?php echo ($sharing_enabled === '1') ? 'Public Link Active' : 'Public Link Disabled'; ?></span>
                            </div>
                            
                            <button type="button" class="share-profile-btn" data-talent-id="<?php echo esc_attr($post_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" <?php echo ($sharing_enabled !== '1') ? 'disabled' : ''; ?>>
                                <?php echo ($sharing_enabled === '1') ? 'Get Shareable Link' : 'Enable Sharing First'; ?>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

            </div><!-- .talent-profile-container -->
        </main>

        <!-- Portfolio Lightbox Modal -->
        <div id="portfolio-modal" class="portfolio-modal" aria-hidden="true" role="dialog">
            <span class="portfolio-modal-close" aria-label="Close modal">&times;</span>
            <button class="portfolio-modal-prev" aria-label="Previous item">&#10094;</button>
            <button class="portfolio-modal-next" aria-label="Next item">&#10095;</button>
            <div class="portfolio-modal-content">
                <img id="portfolio-modal-image" src="" alt="Portfolio full preview">
                <div id="portfolio-modal-video" class="portfolio-modal-video">
                    <iframe id="portfolio-modal-iframe" src="" frameborder="0" allow="autoplay; fullscreen; encrypted-media; accelerometer; gyroscope; picture-in-picture" allowfullscreen loading="eager"></iframe>
                </div>
                <div class="portfolio-modal-counter">
                    <span id="portfolio-modal-current">1</span> / <span id="portfolio-modal-total">0</span>
                </div>
            </div>
        </div>

        <!-- Share Profile Modal (Admin / Owner Only) -->
        <?php if ($is_admin || $is_owner) : ?>
            <div class="share-modal" id="share-profile-modal">
                <div class="share-modal-content">
                    <span class="close-share-modal">&times;</span>
                    <h3>Share This Profile</h3>
                    <p>Copy and send this unique link to clients and casting directors. They can view this profile instantly without logging in.</p>
                    
                    <div class="share-url-section">
                        <label class="share-url-label" for="share-profile-url">Public Link URL:</label>
                        <div class="share-url-container">
                            <input type="text" id="share-profile-url" readonly class="share-url-input">
                            <button type="button" class="copy-share-url-btn share-url-btn">Copy Link</button>
                        </div>
                    </div>
                    
                    <div class="share-modal-actions">
                        <button type="button" class="regenerate-token-btn" data-talent-id="<?php echo $post_id; ?>" data-nonce="<?php echo esc_attr($nonce); ?>">Generate New Link</button>
                        <button type="button" class="close-share-modal share-modal-close-btn">Done</button>
                    </div>
                    
                    <p class="share-modal-note"><strong>Privacy Note:</strong> Direct phone numbers and email addresses are automatically protected on the public link.</p>
                </div>
            </div>
            <div class="share-modal-overlay"></div>
        <?php endif; ?>

        <?php
    endwhile;
    
    get_footer();
} else {
    // User is not logged in - redirect to login page
    wp_redirect(home_url('/membership-login/'));
    exit;
}
