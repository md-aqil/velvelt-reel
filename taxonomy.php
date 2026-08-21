<?php
/**
 * The template for displaying Advertisement Category taxonomy archive pages.
 * Rebuilt to perfectly match the premium dark theme grid design on /advertisement/.
 *
 * @package HelloElementorChild
 */

get_header();

$term = get_queried_object();

// Custom query for advertisements in this category
$args = array(
    'post_type' => 'advertisement',
    'posts_per_page' => 12,
    'post_status' => 'publish',
    'tax_query' => array(
        array(
            'taxonomy' => 'advertisement_category',
            'field'    => 'term_id',
            'terms'    => $term->term_id,
        ),
    ),
);

$query = new WP_Query($args);
?>

<style>
/* Immersive Dark Theme Overrides for Advertisement Category Archive */
body.tax-advertisement_category {
    background-color: #000000 !important;
    background-image: radial-gradient(circle at 50% -10%, rgba(254, 17, 75, 0.05) 0%, transparent 50%) !important;
}

.taxonomy-archive-container {
    background-color: #000000 !important;
    margin: 0 !important;
    padding: 80px 5% 100px !important;
    max-width: 100% !important;
    min-height: 100vh;
    font-family: 'Outfit', 'Inter', sans-serif;
    box-sizing: border-box;
}

/* Category Page Header */
.category-page-header {
    text-align: center;
    margin-bottom: 48px;
}

.category-page-header .entry-title {
    font-size: 3.2rem;
    font-weight: 800;
    color: #ffffff !important;
    letter-spacing: -1.5px;
    margin: 0 0 12px 0;
    font-family: 'Outfit', sans-serif;
    text-transform: capitalize;
}

.category-page-header .taxonomy-description {
    color: #71717a;
    font-size: 1.1rem;
    max-width: 600px;
    margin: 0 auto;
    font-weight: 500;
}

/* Modern Horizontal Taxonomy Ribbon */
.advertisement-categories {
    display: flex;
    justify-content: center;
    margin-bottom: 56px;
    border-bottom: 1px solid #1f1f23;
    padding-bottom: 24px;
}

.category-filters {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: center;
}

.category-filters li {
    margin: 0;
}

.category-filters a {
    display: inline-block;
    padding: 10px 22px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid #1f1f23;
    color: #a1a1aa !important;
    border-radius: 100px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.category-filters a:hover {
    background: rgba(254, 17, 75, 0.06);
    border-color: rgba(254, 17, 75, 0.3);
    color: #ffffff !important;
    transform: translateY(-2px);
}

.category-filters .active a {
    background: #FE114B !important;
    border-color: #FE114B !important;
    color: #ffffff !important;
    box-shadow: 0 4px 15px rgba(254, 17, 75, 0.3);
}

/* Identical Premium listings grid (Matching Latest Listings) */
.ads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
}

.ad-card {
    background: #111113;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid #1f1f23;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.ad-card:hover {
    transform: translateY(-6px);
    border-color: rgba(254, 17, 75, 0.3);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
}

.ad-thumbnail {
    position: relative;
    height: 180px;
    overflow: hidden;
    background: #09090b;
}

.ad-thumbnail a {
    display: block;
    height: 100%;
}

.ad-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.ad-card:hover .ad-thumbnail img {
    transform: scale(1.06);
}

.ad-thumbnail .no-image {
    width: 100%;
    height: 100%;
    background: #09090b;
}

.ad-thumbnail .no-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.6;
}

.ad-category {
    position: absolute;
    top: 14px;
    left: 14px;
    background: #FE114B;
    color: #ffffff;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Pulsing "New" Badge */
.ad-new-tag {
    position: absolute;
    top: 14px;
    right: 14px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    animation: pulse-new-tag 2s infinite;
}

@keyframes pulse-new-tag {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
    70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.ad-content {
    padding: 24px;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.ad-title {
    font-size: 1.25rem;
    margin: 0 0 12px 0;
    line-height: 1.4;
    font-weight: 700;
    font-family: 'Outfit', sans-serif;
}

.ad-title a {
    color: #ffffff;
    text-decoration: none;
    transition: color 0.2s ease;
}

.ad-title a:hover {
    color: #FE114B;
}

.ad-excerpt {
    color: #71717a;
    font-size: 0.95rem;
    margin-bottom: 20px;
    line-height: 1.6;
    flex: 1;
}

.ad-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #52525b;
    padding-top: 16px;
    border-top: 1px solid #1f1f23;
}

.ad-meta span {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Empty listings styling */
.no-ads-message {
    text-align: center;
    padding: 80px 20px;
    color: #71717a;
    max-width: 600px;
    margin: 0 auto;
}

.no-ads-message svg {
    width: 64px;
    height: 64px;
    stroke: #27272a;
    margin-bottom: 24px;
}

.no-ads-message p {
    font-size: 1.1rem;
    font-weight: 500;
}
</style>

<main class="site-main taxonomy-archive-container" role="main">
    <header class="category-page-header">
        <h1 class="entry-title">
            <?php 
            if ($term) {
                echo esc_html($term->name);
            } else {
                _e('Advertisement Categories', 'hello-elementor-child');
            }
            ?>
        </h1>
        <?php if ($term && $term->description) : ?>
            <div class="taxonomy-description"><?php echo esc_html($term->description); ?></div>
        <?php else : ?>
            <div class="taxonomy-description">Browse the latest <?php echo esc_html(strtolower($term->name)); ?> listed by our creative community</div>
        <?php endif; ?>
    </header>

    <?php 
    // Get all advertisement categories for the horizontal filter ribbon
    $categories = get_terms(array(
        'taxonomy' => 'advertisement_category',
        'hide_empty' => false,
    ));
    ?>
    
    <?php if (!empty($categories)) : ?>
        <div class="advertisement-categories">
            <ul class="category-filters">
                <li>
                    <a href="<?php echo get_post_type_archive_link('advertisement'); ?>"><?php _e('All Categories', 'hello-elementor-child'); ?></a>
                </li>
                <?php foreach ($categories as $category) : 
                    // Exclude redundant terms if needed, otherwise list
                    ?>
                    <li class="<?php echo (is_tax('advertisement_category', $category->term_id)) ? 'active' : ''; ?>">
                        <a href="<?php echo get_term_link($category); ?>"><?php echo esc_html($category->name); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($query->have_posts()) : ?>
        <div class="ads-grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" class="ad-card">
                    <div class="ad-thumbnail">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium', array('alt' => get_the_title())); ?>
                            </a>
                        <?php else : ?>
                            <div class="no-image">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/ad-placeholder.png" alt="<?php echo esc_attr(get_the_title()); ?>" />
                            </div>
                        <?php endif; ?>

                        <?php
                        // Check if ad is marked as 'new'
                        $is_new = get_post_meta(get_the_ID(), '_advertisement_is_new', true) === '1';

                        // Fallback: check if within 3 days (72 hours) using post date
                        if (!$is_new) {
                            $post_date = get_post_time('U', false, get_the_ID());
                            $current_time = current_time('timestamp');
                            $three_days_in_seconds = 3 * 24 * 60 * 60;
                            $is_new = ($current_time - $post_date) <= $three_days_in_seconds;
                        }
                        ?>

                        <?php if ($is_new) : ?>
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
                            <span class="ad-date">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?php echo get_the_date('M d, Y'); ?>
                            </span>
                            <span class="ad-location">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <?php
                                $location = get_post_meta(get_the_ID(), 'ad_location', true);
                                echo $location ? esc_html($location) : 'N/A';
                                ?>
                            </span>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <?php the_posts_pagination(); ?>
        
    <?php else : ?>
        <div class="no-ads-message">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            <p><?php _e('No advertisements found in this category.', 'hello-elementor-child'); ?></p>
        </div>
    <?php endif; ?>
    
    <?php wp_reset_postdata(); ?>
</main>

<?php get_footer(); ?>