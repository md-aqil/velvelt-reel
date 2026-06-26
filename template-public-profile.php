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
                <h1>Profile Not Available</h1>
                <p>This profile is either private or does not exist.</p>
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
    
    $sharing_enabled = get_post_meta($post_id, '_talent_public_sharing_enabled', true);
    $saved_token = get_post_meta($post_id, '_talent_shareable_token', true);
    
    if ($sharing_enabled !== '1' || $saved_token !== $public_token) {
        // Sharing has been disabled, show 404
        status_header(404);
        get_header();
        ?>
        <main id="content" class="site-main" style="background: #000; color: #eee; min-height: 100vh;">
            <div class="talent-single-page">
                <div class="talent-profile-container">
                    <h1>Profile Not Available</h1>
                    <p>This profile is no longer available for public viewing.</p>
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

    // Privacy settings - IMPORTANT: Always respect these on public profiles
    $hide_email = get_post_meta($post_id, '_talent_hide_email', true);
    $hide_phone = get_post_meta($post_id, '_talent_hide_phone', true);
    $hide_website = get_post_meta($post_id, '_talent_hide_website', true);
    $hide_instagram = get_post_meta($post_id, '_talent_hide_instagram', true);
    $hide_youtube = get_post_meta($post_id, '_talent_hide_youtube', true);
    $hide_tiktok = get_post_meta($post_id, '_talent_hide_tiktok', true);

    // Portfolio data
    $fashion_categories = get_post_meta($post_id, '_talent_designCategories', true);
    $portfolio_image_ids = get_post_meta($post_id, '_talent_portfolio', true);
    $video_links = get_post_meta($post_id, '_talent_video_links', true);
    
    // Helper functions to get attachment URLs with fallback for orphaned attachments
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

    $get_thumbnail_url_fallback = function($attachment_id, $size = 'medium') use ($get_attachment_url_fallback) {
        $url = wp_get_attachment_image_url($attachment_id, $size);
        if (empty($url)) {
            $url = $get_attachment_url_fallback($attachment_id);
        }
        return $url;
    };
    
    // Prepare portfolio images array (matching single-talent.php structure)
    $portfolio_items = [];
    
    if (!empty($portfolio_image_ids)) {
        // If it's a serialized array or already an array
        if (is_array($portfolio_image_ids)) {
            // Flatten nested arrays if needed
            $flat_ids = [];
            array_walk_recursive($portfolio_image_ids, function($item) use (&$flat_ids) {
                if (is_numeric($item)) {
                    $flat_ids[] = intval($item);
                }
            });
            
            // Get attachment data for each ID
            foreach ($flat_ids as $attachment_id) {
                $mime_type = get_post_mime_type($attachment_id);
                $is_video = $mime_type && strpos($mime_type, 'video/') === 0;
                
                // Use fallback functions
                $attachment_url = $get_attachment_url_fallback($attachment_id);
                $thumbnail_url = $get_thumbnail_url_fallback($attachment_id, 'medium');
                
                // Skip items with invalid URLs
                if (empty($attachment_url)) {
                    continue;
                }
                
                $portfolio_items[] = [
                    'id' => $attachment_id,
                    'url' => $attachment_url,
                    'thumbnail_url' => $thumbnail_url,
                    'is_video' => $is_video,
                    'mime_type' => $mime_type
                ];
            }
        } 
        // If it's a comma-separated string
        elseif (is_string($portfolio_image_ids)) {
            $ids = explode(',', $portfolio_image_ids);
            foreach ($ids as $id) {
                $id = intval(trim($id));
                if ($id > 0) {
                    $mime_type = get_post_mime_type($id);
                    $is_video = $mime_type && strpos($mime_type, 'video/') === 0;
                    
                    // Use fallback functions
                    $attachment_url = $get_attachment_url_fallback($id);
                    $thumbnail_url = $get_thumbnail_url_fallback($id, 'medium');
                    
                    // Skip items with invalid URLs
                    if (empty($attachment_url)) {
                        continue;
                    }
                    
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
        // If it's a single ID
        elseif (is_numeric($portfolio_image_ids)) {
            $attachment_id = intval($portfolio_image_ids);
            $mime_type = get_post_mime_type($attachment_id);
            $is_video = $mime_type && strpos($mime_type, 'video/') === 0;
            
            // Use fallback functions
            $attachment_url = $get_attachment_url_fallback($attachment_id);
            $thumbnail_url = $get_thumbnail_url_fallback($attachment_id, 'medium');
            
            // Only add if URL is valid
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

    // Add public profile body class
    add_filter('body_class', function($classes) {
        $classes[] = 'public-profile';
        return $classes;
    });

    get_header();
    ?>

    <!-- Force Dark Theme Styles -->
    <style>
        :root {
            --e-global-color-primary: #FFFFFF;
    --e-global-color-secondary: #030303;
    --e-global-color-text: #F0F0F0;
    --e-global-color-accent: #DF1D3D;
    --e-global-color-afbf07f: #B2122D;
    --e-global-color-2866f87: #1C1C1C;
    
        }
        body, #page, .site-main{
            background-color: #000 !important;
            color: #eee !important;
        }
        .site-header, .main-navigation, .header-inner-wrap, .elementor-nav-menu--dropdown {
            background-color: #000 !important;
            border-bottom: 1px solid #333 !important;
        }
        .main-navigation a, .site-header .nav-text, .site-header i, .elementor-item {
            color: #fff !important;
        }
        .main-navigation a:hover, .elementor-item:hover {
            color: #df1d3d !important;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #fff !important;
        }
        a {
            color: #ff4d6d !important;
        }
        a:hover {
            color: #ff4d6d !important;
        }

      .elementor-element-3a8ff6c
        /* Ensure navbar is visible - Override elementor-invisible */
        {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            transform: none !important;
        }
        .elementor-invisible {
            animation: none !important;
            transition: none !important;
        }
        
    </style>

    <main id="content" class="site-main talent-single-page" style="background: #000; color: #eee; min-height: 100vh;">
        <div class="talent-profile-container">
            
            <!-- Top Section (Header Card) -->
            <header class="talent-header-card" style="margin-top:40px;">
                <div class="talent-photo">
                    <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php else: ?>
                        <div class="talent-photo-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.5">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="talent-info">
                    <h1 class="talent-name"><?php the_title(); ?></h1>
                    <?php if ($role): ?>
                        <h2 class="talent-role-subtitle"><?php echo esc_html(ucwords(str_replace('-', ' ', $role))); ?></h2>
                    <?php endif; ?>

                    <div class="talent-tags">
                        <?php if ($location): ?>
                            <span class="tag-pill location-tag"><?php echo esc_html($location); ?></span>
                        <?php endif; ?>
                        <?php if ($work_type): ?>
                            <span class="tag-pill work-type-tag"><?php echo esc_html($work_type); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="talent-socials">
                        <?php if ($website_url && !$hide_website): ?>
                            <a href="<?php echo esc_url($website_url); ?>" class="talent-website" target="_blank"
                                rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                </svg>
                                <span><?php echo esc_html(preg_replace('(^https?://(www\.)?)', '', $website_url)); ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($instagram_url && !$hide_instagram): ?>
                            <a href="<?php echo esc_url('https://instagram.com/' . ltrim($instagram_url, '@')); ?>"
                                class="talent-social-link" target="_blank" rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                </svg>
                                <span>Instagram</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($tiktok_url && !$hide_tiktok): ?>
                            <a href="<?php echo esc_url('https://tiktok.com/@' . ltrim($tiktok_url, '@')); ?>"
                                class="talent-social-link" target="_blank" rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M23 9a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2m-9 3a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2m-9 3a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2">
                                    </path>
                                </svg>
                                <span>TikTok</span>
                            </a>
                        <?php endif; ?>
                        <?php if ($youtube_url && !$hide_youtube): ?>
                            <a href="<?php echo esc_url($youtube_url); ?>" class="talent-social-link" target="_blank"
                                rel="noopener noreferrer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/>
                                    <path d="M10 15l10.3-7.5L10 15Z"/>
                                </svg>
                                <span>YouTube</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </header>

            <!-- Biography Section -->
            <?php if (get_the_content()): ?>
                <section class="talent-biography">
                    <h3 class="section-title">Biography</h3>
                    <div class="biography-content">
                        <?php the_content(); ?>
                    </div>
                </section>
                <hr class="soft-divider">
            <?php endif; ?>

            <!-- Details Grid -->
            <section class="talent-details-grid">
                <div class="details-left-column">

                    <div class="details-section">
                        <h3 class="section-title">Contact & Socials</h3>
                        <ul class="details-list">
                            <!-- Email is hidden on public profiles for privacy -->
                            <li><strong>Email:</strong> <span>Available upon request</span></li>
                            <!-- Phone is hidden on public profiles for privacy -->
                            <li><strong>Phone:</strong> <span>Available upon request</span></li>
                            <?php if (!empty($languages) && is_array($languages)): ?>
                                <li><strong>Languages:</strong>
                                    <span><?php echo esc_html(implode(', ', $languages)); ?></span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <?php if (!empty($fashion_categories) && is_array($fashion_categories)): ?>
                        <div class="details-section">
                            <h3 class="section-title">Fashion Categories</h3>
                            <div class="text-chips-container">
                                <?php foreach ($fashion_categories as $category): ?>
                                    <span class="text-chip"><?php echo esc_html($category); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($notable_works) && is_array($notable_works)): ?>
                        <div class="details-section experience-section">
                            <h3 class="section-title">Experience</h3>
                            <?php if ($years_active): ?>
                                <p class="years-active"><strong>Years Active:</strong> <?php echo esc_html($years_active); ?></p>
                            <?php endif; ?>
                            <ul class="experience-list">
                                <?php foreach ($notable_works as $work): ?>
                                    <?php if (!empty($work['title'])): ?>
                                        <?php
                                        $start_date = '';
                                        $end_date = '';

                                        if (!empty($work['startDate'])) {
                                            $start_date = date('M Y', strtotime($work['startDate']));
                                        }

                                        if (isset($work['present']) && $work['present'] === 'on') {
                                            $end_date = 'Present';
                                        } elseif (!empty($work['endDate'])) {
                                            $end_date = date('M Y', strtotime($work['endDate']));
                                        } else {
                                            $end_date = 'N/A';
                                        }

                                        if ($start_date && $end_date) {
                                            $date_range = "{$start_date} - {$end_date}";
                                        } elseif ($start_date) {
                                            $date_range = "{$start_date} - {$end_date}";
                                        } else {
                                            $date_range = $end_date;
                                        }
                                        ?>
                                        <li class="experience-item">
                                            <span class="experience-role"><?php echo esc_html($work['role'] ?? ''); ?></span>
                                            <span class="experience-company"><?php echo esc_html($work['title']); ?></span>
                                            <span class="experience-duration"><?php echo esc_html($date_range); ?></span>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($agency_name): ?>
                        <div class="details-section">
                            <h3 class="section-title">Agency</h3>
                            <p class="agency-text"><?php echo esc_html($agency_name); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($education): ?>
                        <div class="details-section">
                            <h3 class="section-title">Education & Training</h3>
                            <p class="education-text"><?php echo nl2br(esc_html($education)); ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="details-section">
                        <h3 class="section-title">Availability & Preferences</h3>
                        <ul class="details-list">
                            <?php if (!empty($available_for) && is_array($available_for)): ?>
                                <li><strong>Available For:</strong>
                                    <span><?php echo esc_html(implode(', ', $available_for)); ?></span>
                                </li>
                            <?php endif; ?>
                            <?php if ($willing_to_travel): ?>
                                <li><strong>Willing to Travel:</strong>
                                    <span><?php echo ($willing_to_travel === 'on') ? 'Yes' : 'No'; ?></span>
                                </li>
                            <?php endif; ?>
                            <?php if ($brand_collabs): ?>
                                <li><strong>Brand Collaborations:</strong>
                                    <span><?php echo ($brand_collabs === 'on') ? 'Yes' : 'No'; ?></span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                </div>
                <div class="details-right-column">
                    <div class="details-section">
                        <h3 class="section-title">Portfolio</h3>
                        <?php if (!empty($portfolio_items) && is_array($portfolio_items)): ?>
                            <div class="portfolio-gallery">
                                <?php 
                                foreach ($portfolio_items as $index => $item): 
                                ?>
                                    <?php if ($item['is_video']): ?>
                                        <!-- Video Item -->
                                        <div class="portfolio-item" data-index="<?php echo $index; ?>" data-type="video" data-attachment-id="<?php echo esc_attr($item['id']); ?>" data-video-url="<?php echo esc_url($item['url']); ?>">
                                            <video controls style="width: 100%; height: 100%; object-fit: cover;" poster="<?php echo esc_url($item['thumbnail_url']); ?>">
                                                <source src="<?php echo esc_url($item['url']); ?>" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    <?php else: ?>
                                        <!-- Image Item -->
                                        <div class="portfolio-item" data-index="<?php echo $index; ?>" data-type="image">
                                            <img src="<?php echo esc_url($item['thumbnail_url']); ?>" 
                                                 alt="Portfolio image" 
                                                 oncontextmenu="return false;" 
                                                 ondragstart="return false;" 
                                                 onselectstart="return false;"
                                                 onmousedown="if(event.button==2)return false;"
                                                 style="-webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; -webkit-touch-callout: none; -webkit-user-drag: none; pointer-events: auto;">
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No portfolio items have been uploaded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

        </div>
        
        <!-- Public Profile Notice -->
        <div class="public-profile-notice">
            <p>This is a public profile view. <a href="<?php echo home_url(); ?>">Back to VelvetReel</a></p>
        </div>

    </main>

    <!-- Portfolio Gallery Modal -->
    <div id="portfolio-modal" class="portfolio-modal">
        <span class="portfolio-modal-close">&times;</span>
        <button class="portfolio-modal-prev">&#10094;</button>
        <button class="portfolio-modal-next">&#10095;</button>
        <div class="portfolio-modal-content">
            <img id="portfolio-modal-image" src="" alt="Portfolio image">
            <div id="portfolio-modal-video" class="portfolio-modal-video" style="display: none;">
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
