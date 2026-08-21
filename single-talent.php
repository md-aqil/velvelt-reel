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
    // User is logged in - continue with the normal page content
    get_header();
    
    while (have_posts()):
        the_post();
    
        // --- Get all the meta data for the talent ---
        $post_id = get_the_ID();
        $role = get_post_meta($post_id, '_talent_role', true);
        $state = get_post_meta($post_id, '_talent_state', true);
        $country = get_post_meta($post_id, '_talent_country', true);
        $location = ($state && $country) ? "{$state}, {$country}" : ($state ?: $country);
        $work_type = get_post_meta($post_id, '_talent_affiliation', true);
        $agency_name = get_post_meta($post_id, '_talent_agency_name', true);
        $notable_works = get_post_meta($post_id, '_talent_notable_works', true);
    
        // --- Get all other meta fields ---
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
        
        // Physical Attributes
        $height = get_post_meta($post_id, '_talent_height', true);
        $height_unit = get_post_meta($post_id, '_talent_height_unit', true);
        $weight = get_post_meta($post_id, '_talent_weight', true);
        $weight_unit = get_post_meta($post_id, '_talent_weight_unit', true);
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
    
        // --- Get Role-Specific and Portfolio Data ---
        $fashion_categories = get_post_meta($post_id, '_talent_designCategories', true);
        $portfolio_image_ids = get_post_meta($post_id, '_talent_portfolio', true);
        
        // Debug: Check what we're getting from the meta field
        error_log('Portfolio meta data: ' . print_r($portfolio_image_ids, true));

        // Prepare an array to hold portfolio items (images and videos)
        $portfolio_items = [];
        
        // Helper function to get attachment URL with fallback
        $get_attachment_url_fallback = function($attachment_id) {
            // First try standard WordPress function
            $url = wp_get_attachment_url($attachment_id);
            
            // If that fails, try getting from post meta (GUID)
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
            
            // If still empty, check if post exists at all
            if (empty($url)) {
                $post_exists = get_post($attachment_id);
                if ($post_exists) {
                    // Post exists but has no URL - might be unattached
                    // Try to construct URL from uploads directory
                    $upload_dir = wp_upload_dir();
                    $url = $upload_dir['baseurl'] . '/' . basename($post_exists->guid);
                }
            }
            
            return $url;
        };

        // Helper function to get thumbnail URL with fallback
        $get_thumbnail_url_fallback = function($attachment_id, $size = 'medium') use ($get_attachment_url_fallback) {
            // First try standard WordPress function
            $url = wp_get_attachment_image_url($attachment_id, $size);
            
            if (empty($url)) {
                // Try to get the full URL
                $url = $get_attachment_url_fallback($attachment_id);
            }
            
            return $url;
        };
    
        // Prepare an array to hold portfolio items (images and videos)
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
                    
                    // Use fallback functions to get URLs
                    $attachment_url = $get_attachment_url_fallback($attachment_id);
                    $thumbnail_url = $get_thumbnail_url_fallback($attachment_id, 'full');
                    
                    // Skip items with invalid URLs
                    if (empty($attachment_url)) {
                        error_log('Skipping portfolio item ' . $attachment_id . ' - no URL found');
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
                        
                        // Use fallback functions to get URLs
                        $attachment_url = $get_attachment_url_fallback($id);
                        $thumbnail_url = $get_thumbnail_url_fallback($id, 'full');
                        
                        // Skip items with invalid URLs
                        if (empty($attachment_url)) {
                            error_log('Skipping portfolio item ' . $id . ' - no URL found');
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
                
                // Use fallback functions to get URLs
                $attachment_url = $get_attachment_url_fallback($attachment_id);
                $thumbnail_url = $get_thumbnail_url_fallback($attachment_id, 'full');
                
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
    
        ?>
        
        
    
        <main id="content" class="site-main talent-single-page">
    
            <div class="talent-profile-container">
    
                <!-- Top Section (Header Card) -->
                <header class="talent-header-card">
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
                            <h2 class="talent-role-subtitle"><?php echo esc_html(ucwords(str_replace('-', ' ', $role))); ?>
                            </h2>
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
                                        <path
                                            d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                                        </path>
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
                                        <path
                                            d="M23 9a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2m-9 3a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2m-9 3a2 2 0 0 1-2 2 2 2 0 0 1-2-2 2 2 0 0 1 2-2">
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
    
                        <?php 
                        // Check if any physical attributes exist
                        $has_physical_attributes = !empty($height) || !empty($weight) || !empty($complexion) || 
                                                   !empty($bust_size) || !empty($hair_color) || 
                                                   !empty($dress_size) || !empty($shirt_size);
                        ?>
                        
                        <?php if ($has_physical_attributes): ?>
                        <div class="details-section">
                            <h3 class="section-title">Physical Attributes</h3>
                            <ul class="details-list">
                                <?php if ($height): ?>
                                    <li><strong>Height:</strong> 
                                        <span><?php echo esc_html($height . ' ' . $height_unit); ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($weight): ?>
                                    <li><strong>Weight:</strong> 
                                        <span><?php echo esc_html($weight . ' ' . $weight_unit); ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($complexion): ?>
                                    <li><strong>Complexion:</strong> 
                                        <span><?php echo esc_html($complexion); ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($bust_size): ?>
                                    <li><strong>Bust Size:</strong> 
                                        <span><?php echo esc_html($bust_size); ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($hair_color): ?>
                                    <li><strong>Hair Color:</strong> 
                                        <span><?php echo esc_html($hair_color); ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($dress_size): ?>
                                    <li><strong>Dress Size:</strong> 
                                        <span><?php echo esc_html($dress_size); ?></span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($shirt_size): ?>
                                    <li><strong>Shirt Size:</strong> 
                                        <span><?php echo esc_html($shirt_size); ?></span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <div class="details-section">
                            <h3 class="section-title">Contact & Socials</h3>
                            <ul class="details-list">
                                <?php if ($email && !($hide_email === '1' || $hide_email === true)): ?>
                                    <li><strong>Email:</strong> <span><?php echo esc_html($email); ?></span></li>
                                <?php else: ?>
                                    <li><strong>Email:</strong> <span>contact@thevelvetreel.com</span></li>
                                <?php endif; ?>
                                <?php if ($phone && !($hide_phone === '1' || $hide_phone === true)): ?>
                                    <li><strong>Phone:</strong> <span><?php echo esc_html($phone); ?></span></li>
                                <?php else: ?>
                                    <li><strong>Phone:</strong> <span>+1-888-585-5396</span></li>
                                <?php endif; ?>
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
                                            // Handle date range display
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
    
                                            // Format the date range
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
    
                        <?php if ($interested_projects): ?>
                            <div class="details-section">
                                <h3 class="section-title">Seeking Projects</h3>
                                <p class="seeking-projects-text"><?php echo nl2br(esc_html($interested_projects)); ?></p>
                            </div>
                        <?php endif; ?>
    
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
                        
                        <!-- Removed separate Video Links section - videos now integrated in portfolio -->
                    </div>
                </section>
    
            </div>
            
            <!-- Admin Share Section -->
            <?php 
            $is_admin = current_user_can('manage_options');
            $is_owner = (get_post_field('post_author', $post_id) == get_current_user_id());
            
            if ($is_admin || $is_owner) : 
                $sharing_enabled = get_post_meta($post_id, '_talent_public_sharing_enabled', true);
                $share_token = get_post_meta($post_id, '_talent_shareable_token', true);
                $nonce = wp_create_nonce('shareable_token_nonce');
            ?>
            <div class="admin-share-section">
                <h2>Share This Profile</h2>
                <p>Generate a unique link to share this profile publicly (no login required)</p>
                
                <div class="sharing-controls">
                    <div class="sharing-controls-left">
                        <label class="ios-switch">
                            <input type="checkbox" class="sharing-toggle-switch" data-talent-id="<?php echo esc_attr($post_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" <?php checked($sharing_enabled, '1'); ?>>
                            <span class="switch-slider"></span>
                        </label>
                        <span class="switch-label"><?php echo ($sharing_enabled === '1') ? 'Public Sharing Enabled' : 'Enable Public Sharing'; ?></span>
                    </div>
                    
                    <button type="button" class="share-profile-btn" data-talent-id="<?php echo esc_attr($post_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" <?php echo ($sharing_enabled !== '1') ? 'disabled' : ''; ?>>
                        <?php echo ($sharing_enabled === '1') ? 'Share Profile' : 'Enable Sharing First'; ?>
                    </button>
                </div>
            </div>
            </div>
            <?php endif; ?>

            <div class="vaclav-contact-section">
                <h2>Contact Us</h2>
                <p>Have questions about our products? Reach out to us.</p>
    
                <?php echo do_shortcode('[contact-form-7 id="d81cd32" title="Contact form 1"]'); ?>
            </div>
    
    
        </main>
    
        <!-- Portfolio Gallery Modal -->
        <div id="portfolio-modal" class="portfolio-modal">
            <span class="portfolio-modal-close">&times;</span>
            <button class="portfolio-modal-prev">&#10094;</button>
            <button class="portfolio-modal-next">&#10095;</button>
            <div class="portfolio-modal-content">
                <img id="portfolio-modal-image" src="" alt="Portfolio image">
                <!-- Video container for modal -->
                <div id="portfolio-modal-video" class="portfolio-modal-video">
                    <iframe id="portfolio-modal-iframe" src="" frameborder="0" allow="autoplay; fullscreen; encrypted-media; accelerometer; gyroscope; picture-in-picture" allowfullscreen loading="eager"></iframe>
                </div>
                <div class="portfolio-modal-counter">
                    <span id="portfolio-modal-current">1</span> / <span id="portfolio-modal-total">0</span>
                </div>
            </div>
        </div>
    
        <!-- Share Profile Modal (Admin Only) -->
        <?php if (current_user_can('manage_options') || get_post_field('post_author', $post_id) == get_current_user_id()) : ?>
        <div class="share-modal">
            <div class="share-modal-content">
                <span class="close-share-modal">&times;</span>
                <h3>Share This Profile</h3>
                <p>Copy and share this link with anyone. They can view this profile without logging in.</p>
                
                <div class="share-url-section">
                    <label class="share-url-label">Public Profile URL:</label>
                    <div class="share-url-container">
                        <input type="text" id="share-profile-url" readonly class="share-url-input">
                        <button type="button" class="copy-share-url-btn share-url-btn">Copy Link</button>
                    </div>
                </div>
                
                <div class="share-modal-actions">
                    <button type="button" class="regenerate-token-btn" data-talent-id="<?php echo $post_id; ?>" data-nonce="<?php echo esc_attr($nonce); ?>">Regenerate Link</button>
                    <button type="button" class="close-share-modal share-modal-close-btn">Close</button>
                </div>
                
                <p class="share-modal-note"><strong>Note:</strong> Anyone with this link can view this public profile. You can disable sharing or regenerate the link anytime.</p>
            </div>
        </div>
        
        <div class="share-modal-overlay"></div>
        <?php endif; ?>
    
        <?php
    endwhile; // End of the loop.
    
    get_footer();
} else {
    // User is not logged in - redirect to login page
    wp_redirect(home_url('/membership-login/'));
    exit;
}
