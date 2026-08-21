<?php
/**
 * The template for displaying Advertisement archive pages
 * Redesigned to match ClassifiedAds.com style with Velvet3 dark theme
 *
 * @package HelloElementorChild
 */

get_header(); ?>

<?php
if (!function_exists('velvet_get_category_svg')) {
    function velvet_get_category_svg($cat_name) {
        if (stripos($cat_name, 'Casting') !== false) {
            return '<svg class="category-icon-svg" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2a5 5 0 0 0-5 5v3a5 5 0 0 0 10 0V7a5 5 0 0 0-5-5z"></path><path d="M17 14h.01"></path><path d="M7 14h.01"></path><path d="M12 18h.01"></path><path d="M2 22a10 10 0 0 1 20 0H2z"></path></svg>';
        } elseif (stripos($cat_name, 'Crew') !== false) {
            return '<svg class="category-icon-svg" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>';
        } elseif (stripos($cat_name, 'Rentals') !== false) {
            return '<svg class="category-icon-svg" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="8" rx="2.5"></rect><rect x="2" y="14" width="20" height="8" rx="2.5"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>';
        }
        return '<svg class="category-icon-svg" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>';
    }
}
?>

<?php
$can_post_ad = false;
if (!function_exists('has_membership_plan')) {
    require_once get_stylesheet_directory() . '/includes/access-control.php';
}
ensure_user_plan_tracking();
migrate_user_current_level_to_plans();
$plans_with_ad_access = [SIX_MONTH_PLAN_LEVEL, ONE_YEAR_PLAN_LEVEL, ADVERTISEMENT_PLAN_LEVEL];
foreach ($plans_with_ad_access as $plan_level) {
    if (has_membership_plan($plan_level)) {
        $can_post_ad = true;
        break;
    }
}
?>

<!-- Hero Section with Search Box -->
<section class="classifieds-hero">
    <div class="hero-content">
        <h1 class="hero-title">Find What You Need in Our Classifieds</h1>
        <p class="hero-subtitle">Browse thousands of ads from trusted community members</p>

        <!-- Search Form -->
        <form role="search" method="get" class="classifieds-search-form"
            action="<?php echo get_post_type_archive_link('advertisement'); ?>">
            <div class="search-row">
                <div class="search-field keyword-field">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" name="s" placeholder="What are you looking for?"
                        value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>" />
                </div>
                <div class="search-field location-field">
                    <span class="search-icon"><i class="fas fa-map-marker-alt"></i></span>
                    <input type="text" name="location" placeholder="Location"
                        value="<?php echo isset($_GET['location']) ? esc_attr($_GET['location']) : ''; ?>" />
                </div>
                <div class="search-field category-field">
                    <span class="search-icon"><i class="fas fa-th"></i></span>
                    <select name="advertisement_category">
                        <option value="">All Categories</option>
                        <?php
                        $allowed_categories = array('Casting Calls', 'Crew Calls', 'Rentals');
                        $categories = get_terms(array(
                            'taxonomy' => 'advertisement_category',
                            'hide_empty' => false,
                        ));
                        $current_cat = isset($_GET['advertisement_category']) ? $_GET['advertisement_category'] : '';
                        
                        $found_categories = array();
                        if (!is_wp_error($categories) && !empty($categories)) {
                            foreach ($categories as $category) {
                                if (in_array($category->name, $allowed_categories)) {
                                    $selected = ($current_cat === $category->slug) ? 'selected' : '';
                                    echo '<option value="' . esc_attr($category->slug) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                                    $found_categories[] = $category->name;
                                }
                            }
                        }
                        
                        // Fallback: If any mandatory category was not found in DB, show it anyway
                        foreach ($allowed_categories as $cat_name) {
                            if (!in_array($cat_name, $found_categories)) {
                                $slug = sanitize_title($cat_name);
                                $selected = ($current_cat === $slug) ? 'selected' : '';
                                echo '<option value="' . esc_attr($slug) . '" ' . $selected . '>' . esc_html($cat_name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="search-submit-btn">Search</button>
            </div>
        </form>
    </div>
</section>

<!-- Categories Grid Section -->
<?php
// Allow both logged-in users AND Super Admins to see categories
$is_logged_in = is_user_logged_in();
$is_super_admin = is_super_admin(); // Super Admin has full access
?>
<?php if ($is_logged_in || $is_super_admin): ?>
    <section class="categories-section">
        <h2 class="section-title">Browse Categories</h2>
        <p class="section-subtitle">Find exactly what you need by exploring our categories</p>

        <div class="categories-grid">
            <?php
            $allowed_categories = array('Casting Calls', 'Crew Calls', 'Rentals');
            $categories = get_terms(array(
                'taxonomy' => 'advertisement_category',
                'hide_empty' => false,
            ));

            $found_categories = array();
            if (!empty($categories) && !is_wp_error($categories)):
                foreach ($categories as $category) {
                    if (in_array($category->name, $allowed_categories)) {
                        $count = $category->count;
                        $name = $category->name;
                        $link = get_term_link($category);
                        ?>
                        <a href="<?php echo esc_url($link); ?>" class="category-card" role="button">
                            <div class="category-icon-wrapper">
                                <?php echo velvet_get_category_svg($name); ?>
                            </div>
                            <div class="category-info">
                                <h3 class="category-name"><?php echo esc_html($name); ?></h3>
                                <span class="category-count"><?php echo number_format_i18n($count); ?> ads</span>
                            </div>
                        </a>
                        <?php
                        $found_categories[] = $category->name;
                    }
                }
            endif;

            // Fallback: Show missing categories from the grid
            foreach ($allowed_categories as $cat_name) {
                if (!in_array($cat_name, $found_categories)) {
                    $slug = sanitize_title($cat_name);
                    $link = get_term_link($slug, 'advertisement_category');
                    if (is_wp_error($link)) {
                        $link = home_url('/advertisement/?advertisement_category=' . $slug);
                    }
                    ?>
                    <a href="<?php echo esc_url($link); ?>" class="category-card" role="button">
                        <div class="category-icon-wrapper">
                            <?php echo velvet_get_category_svg($cat_name); ?>
                        </div>
                        <div class="category-info">
                            <h3 class="category-name"><?php echo esc_html($cat_name); ?></h3>
                            <span class="category-count">0 ads</span>
                        </div>
                    </a>
                    <?php
                }
            }
            ?>
        </div>
    </section>
<?php endif; ?>

<!-- Featured / Recent Ads Section -->
<section class="ads-section">
    <div class="ads-header">
        <h2 class="section-title">Latest Listings</h2>
    </div>

    <?php
    // Allow all logged-in members (including free) to view advertisements
    $can_view_advertisements = is_user_logged_in();
    ?>

    <?php if ($can_view_advertisements): ?>
        <?php
        // Build search query based on parameters
        $search_args = array(
            'post_type' => 'advertisement',
            'posts_per_page' => 12,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        );

        // Keyword search - use $_GET directly
        $search_keyword = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        if (!empty($search_keyword)) {
            $search_args['s'] = $search_keyword;
        }

        // Category filter - use $_GET directly
        $search_category = isset($_GET['advertisement_category']) ? sanitize_text_field($_GET['advertisement_category']) : '';
        if (!empty($search_category)) {
            $search_args['tax_query'] = array(
                array(
                    'taxonomy' => 'advertisement_category',
                    'field' => 'slug',
                    'terms' => $search_category,
                ),
            );
        }

        // Location filter (meta query) - use $_GET directly
        $search_location = isset($_GET['location']) ? sanitize_text_field($_GET['location']) : '';
        if (!empty($search_location)) {
            $search_args['meta_query'] = array(
                array(
                    'key' => 'ad_location',
                    'value' => $search_location,
                    'compare' => 'LIKE',
                ),
            );
        }

        $recent_ads = new WP_Query($search_args);
        ?>

        <?php if ($recent_ads->have_posts()): ?>
            <div class="ads-grid">
                <?php while ($recent_ads->have_posts()):
                    $recent_ads->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" class="ad-card">
                        <div class="ad-thumbnail">
                            <?php if (has_post_thumbnail()): ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                                </a>
                            <?php else: ?>
                                <div class="no-image">
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/ad-placeholder.png" alt="<?php echo esc_attr(get_the_title()); ?>" />
                                </div>
                            <?php endif; ?>

                            <?php
                            // Check if ad is marked as 'new' using meta field (falls back to post date check)
                            $is_new = get_post_meta(get_the_ID(), '_advertisement_is_new', true) === '1';

                            // Fallback: check if within 3 days (72 hours) using post date
                            if (!$is_new) {
                                $post_date = get_post_time('U', false, get_the_ID());
                                $current_time = current_time('timestamp');
                                $three_days_in_seconds = 3 * 24 * 60 * 60; // 3 days in seconds
                                $is_new = ($current_time - $post_date) <= $three_days_in_seconds;
                            }
                            ?>

                            <?php if ($is_new): ?>
                                <span class="ad-new-tag">New</span>
                            <?php endif; ?>

                            <span class="ad-category">
                                <?php
                                $ad_cats = get_the_terms(get_the_ID(), 'advertisement_category');
                                if ($ad_cats && !is_wp_error($ad_cats)) {
                                    echo esc_html($ad_cats[0]->name);
                                }
                                ?>
                            </span>
                        </div>
                        <div class="ad-content">
                            <h3 class="ad-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="ad-excerpt">
                                <?php
                                $excerpt = get_the_excerpt();
                                echo esc_html(wp_trim_words($excerpt, 15, '...'));
                                ?>
                            </div>
                            <div class="ad-meta">
                                <span class="ad-date"><i class="far fa-clock"></i> <?php echo get_the_date('M d, Y'); ?></span>
                                <span class="ad-location"><i class="fas fa-map-marker-alt"></i>
                                    <?php
                                    $location = get_post_meta(get_the_ID(), 'ad_location', true);
                                    echo $location ? esc_html($location) : 'N/A';
                                    ?>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        <?php else: ?>
            <div class="no-ads-message">
                <i class="fas fa-inbox"></i>
                <p>No advertisements found. Be the first to post!</p>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Login Required Message -->
        <div class="access-restricted-message">

            <h3>Login to View Advertisements</h3>
            <p>Please sign in to view our ad listings and connect with opportunities.</p>
            <div class="restricted-benefits">
                <div class="benefit-item">
                    <i class="fas fa-check"></i>
                    <span>Access to all classifieds</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-check"></i>
                    <span>Post your own ads</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-check"></i>
                    <span>Connect with talents & opportunities</span>
                </div>
            </div>
            <a href="<?php echo esc_url(home_url('/membership-login/')); ?>" class="btn-join-now">Sign In</a>
            <p style="margin-top: 15px; color: #666;">Don't have an account? <a
                    href="<?php echo esc_url(home_url('/membership-join/membership-registration/')); ?>"
                    style="color: #b2122d;">Sign Up Free</a></p>
        </div>
    <?php endif; ?>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <h2>Ready to Post Your Ad?</h2>
    <p>Reach thousands of potential customers in our community</p>
    <?php
    if ($can_post_ad) {
        echo '<a href="' . esc_url(home_url('/classified-create')) . '" class="btn-cta-primary">Post Your Ad Now</a>';
    } else {
        echo '<a href="' . esc_url(home_url('/membership-join/')) . '" class="btn-cta-primary">Get Started</a>';
    }
    ?>
</section>

</main>

<!-- Styles for Classifieds Landing Page - Dark Theme -->
<style>
    /* Main Background */
    .post-type-archive-advertisement .site-main,
    .classifieds-landing {
        background: #000 !important;
        margin: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
    }

    /* Hide categories for logged out users - CSS fallback */
    body:not(.logged-in) .categories-section {
        display: none !important;
    }

    /* Hero Section */
    .classifieds-hero {
        position: relative;
        background: linear-gradient(180deg, #000000 0%, #0a0a0a 50%, #111 100%);
        padding: 80px 5% 60px;
        text-align: center;
        min-height: 350px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid #1a1a1a;
    }

    .classifieds-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(178, 18, 45, 0.12) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(178, 18, 45, 0.08) 0%, transparent 50%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 900px;
        width: 100%;
    }

    .hero-title {
        color: #fff;
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 12px;
        font-family: 'Gantari', sans-serif;
    }

    .hero-subtitle {
        color: #888;
        font-size: 1rem;
        margin-bottom: 25px;
    }

    /* Search Form - Dark Theme */
    .classifieds-search-form {
        background: rgba(20, 20, 20, 0.95);
        border-radius: 16px;
        padding: 25px;
        border: 1px solid #222;
    }

    .search-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .search-field {
        flex: 1;
        min-width: 150px;
        position: relative;
    }

    .search-field.keyword-field {
        flex: 2;
        min-width: 250px;
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #555;
        z-index: 1;
    }

    .search-field input,
    .search-field select {
        width: 100%;
        padding: 16px;
        border: 2px solid #222;
        border-radius: 10px;
        font-size: 15px;
        background: #0a0a0a;
        color: #fff;
        transition: all 0.3s ease;
        margin: 0 !important;
    }

    .search-field input::placeholder {
        color: #555;
    }

    .search-field input:focus,
    .search-field select:focus {
        outline: none;
        border-color: #b2122d;
        background: #111;
    }

    .search-field select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        padding-right: 40px;
    }

    .search-field select option {
        background: #111;
        color: #fff;
    }

    .search-submit-btn {
        background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
        color: #fff;
        border: none;
        padding: 16px 40px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .search-submit-btn:hover {
        background: linear-gradient(135deg, #df1d3d 0%, #ff4d6d 100%);
        transform: translateY(-2px);
    }

    /* Hero CTA */
    .hero-cta {
        margin-top: 30px;
    }

    .btn-post-ad {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        color: #000;
        padding: 16px 35px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-post-ad:hover {
        background: #b2122d;
        color: #fff;
        transform: translateY(-2px);
    }

    .btn-post-ad i {
        font-size: 18px;
    }

    /* Trust Signals - Dark Theme */
    .trust-signals {
        background: #0a0a0a;
        padding: 40px 5%;
        border-bottom: 1px solid #1a1a1a;
    }

    .trust-container {
        max-width: 1000px;
        margin: 0 auto;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 40px;
    }

    .trust-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .trust-icon {
        font-size: 32px;
        color: #b2122d;
        margin-bottom: 10px;
    }

    .trust-count {
        font-size: 28px;
        font-weight: 700;
        color: #fff;
    }

    .trust-label {
        font-size: 13px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 5px;
    }

    /* Sections */
    .categories-section,
    .ads-section,
    .cta-section {
        padding: 40px 5%;
        max-width: 1400px;
        margin: 0 auto;
    }

    .section-title {
        font-size: 1.6rem;
        color: #fff;
        text-align: center;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .section-subtitle {
        text-align: center;
        color: #666;
        margin-bottom: 30px;
        font-size: 1rem;
    }

    /* Categories Section - Dark Theme */
    .categories-section {
        background: #0a0a0a;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .category-card {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 20px !important;
        background: rgba(18, 18, 20, 0.5) !important;
        backdrop-filter: blur(12px) !important;
        border-radius: 16px !important;
        padding: 24px 28px !important;
        text-align: left !important;
        text-decoration: none !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        border: 1px solid #1f1f23 !important;
        cursor: pointer !important;
        box-sizing: border-box !important;
        height: 100% !important;
        outline: none !important;
        pointer-events: auto !important;
    }

    .category-card:hover {
        transform: translateY(-4px) !important;
        border-color: rgba(254, 17, 75, 0.4) !important;
        background: rgba(254, 17, 75, 0.04) !important;
        box-shadow: 0 10px 30px rgba(254, 17, 75, 0.08) !important;
    }

    .category-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: rgba(254, 17, 75, 0.08);
        border: 1px solid rgba(254, 17, 75, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FE114B;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .category-card:hover .category-icon-wrapper {
        background: #FE114B;
        color: #fff;
        box-shadow: 0 0 15px rgba(254, 17, 75, 0.3);
    }

    .category-icon-svg {
        width: 24px;
        height: 24px;
        stroke: currentColor;
    }

    .category-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .category-name {
        color: #fff !important;
        font-size: 1.15rem !important;
        margin: 0 !important;
        font-weight: 700 !important;
        font-family: 'Outfit', sans-serif !important;
    }

    .category-count {
        color: #71717a !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
    }

    /* Ads Section */
    .ads-section {
        background: #000;
    }

    .ads-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto 35px;
    }

    .ads-header .section-title {
        text-align: left;
        margin: 0;
    }

    .view-all-link {
        color: #df1d3d;
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .view-all-link:hover {
        color: #ff4d6d;
    }

    .ads-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .ad-card {
        background: #111;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #1a1a1a;
    }

    .ad-card:hover {
        transform: translateY(-5px);
        border-color: #222;
    }

    .ad-thumbnail {
        position: relative;
        height: 150px;
        overflow: hidden;
        background: #0a0a0a;
    }

    .ad-thumbnail a {
        display: block;
        height: 100%;
    }

    .ad-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .ad-card:hover .ad-thumbnail img {
        transform: scale(1.05);
    }

    .ad-thumbnail .no-image {
        width: 100%;
        height: 100%;
        background: #0a0a0a;
    }

    .ad-thumbnail .no-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }

    .ad-card:hover .no-image img {
        opacity: 1;
    }

    .ad-category {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(178, 18, 45, 0.9);
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* New Post Tag */
    .ad-new-tag {
        position: absolute;
        top: 12px;
        right: 12px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
        animation: pulse-new 2s infinite;
    }

    @keyframes pulse-new {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5);
        }

        70% {
            box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .ad-content {
        padding: 16px;
    }

    .ad-title {
        font-size: 1.05rem;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .ad-title a {
        color: #fff;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .ad-title a:hover {
        color: #df1d3d;
    }

    .ad-excerpt {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .ad-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        color: #555;
        padding-top: 12px;
        border-top: 1px solid #1a1a1a;
    }

    .ad-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* No Ads Message */
    .no-ads-message {
        text-align: center;
        padding: 60px 20px;
        color: #555;
        max-width: 1200px;
        margin: 0 auto;
    }

    .no-ads-message i {
        font-size: 50px;
        color: #222;
        margin-bottom: 20px;
    }

    /* Access Restricted Message */
    .access-restricted-message {
        background: #111;
        border-radius: 20px;
        padding: 50px 30px;
        text-align: center;
        max-width: 600px;
        margin: 0 auto;
        border: 1px solid #1a1a1a;
    }

    .access-restricted-message .lock-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .access-restricted-message .lock-icon i {
        font-size: 30px;
        color: #fff;
    }

    .access-restricted-message h3 {
        font-size: 1.5rem;
        color: #fff;
        margin-bottom: 12px;
    }

    .access-restricted-message p {
        color: #666;
        margin-bottom: 25px;
        font-size: 1rem;
    }

    .restricted-benefits {
        display: flex;
        justify-content: center;
        gap: 25px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .benefit-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #4ade80;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .btn-join-now {
        display: inline-block;
        background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
        color: #fff;
        padding: 14px 45px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .btn-join-now:hover {
        background: linear-gradient(135deg, #df1d3d 0%, #ff4d6d 100%);
        transform: translateY(-2px);
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(180deg, #0a0a0a 0%, #050505 100%);
        text-align: center;
        border-top: 1px solid #1a1a1a;
        padding: 40px 5%;
    }

    .cta-section h2 {
        color: #fff;
        font-size: 1.6rem;
        margin-bottom: 10px;
    }

    .cta-section p {
        color: #666;
        margin-bottom: 20px;
        font-size: 1rem;
    }

    .btn-cta-primary {
        display: inline-block;
        background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
        color: #fff;
        padding: 16px 45px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .btn-cta-primary:hover {
        background: linear-gradient(135deg, #df1d3d 0%, #ff4d6d 100%);
        transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 1.8rem;
        }

        .hero-subtitle {
            font-size: 0.95rem;
        }

        .search-row {
            flex-direction: column;
        }

        .search-field,
        .search-field.keyword-field {
            min-width: 100%;
        }

        .search-submit-btn {
            width: 100%;
        }

        .categories-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .ads-grid {
            grid-template-columns: 1fr;
        }

        .restricted-benefits {
            flex-direction: column;
            align-items: center;
        }

        .ads-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .ads-header .section-title {
            text-align: center;
        }

        .trust-container {
            gap: 30px;
        }

        .trust-item {
            flex: 0 0 45%;
        }
    }

    @media (max-width: 480px) {
        .classifieds-hero {
            padding: 50px 4% 40px;
        }

        .hero-title {
            font-size: 1.4rem;
        }

        .categories-grid {
            grid-template-columns: 1fr;
        }

        .trust-item {
            flex: 0 0 100%;
        }

        .section-title {
            font-size: 1.3rem;
        }

        .classifieds-search-form {
            padding: 16px 12px;
        }

        .categories-section,
        .ads-section,
        .cta-section {
            padding: 30px 4%;
        }
    }
</style>

<?php get_footer(); ?>