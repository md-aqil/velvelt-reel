<?php
/**
 * The template for displaying the blog posts index (home.php).
 *
 * @package HelloElementorChild
 */

get_header();

// Custom helper function to calculate reading time
if (!function_exists('velvet_get_reading_time')) {
    function velvet_get_reading_time($post_id) {
        $content = get_post_field('post_content', $post_id);
        $word_count = str_word_count(strip_tags($content));
        $reading_time = ceil($word_count / 200); // 200 words per minute average
        return $reading_time > 0 ? $reading_time : 1;
    }
}
?>

<style>
/* Blog Index Immersive Dark Theme Styles */
body.blog {
    background-color: #09090b !important;
    color: #f4f4f5 !important;
    font-family: 'Outfit', 'Inter', 'Poppins', sans-serif !important;
}

.velvet-blog-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 60px 24px 80px;
}

/* Hero Section Styling */
.velvet-blog-hero {
    text-align: center;
    margin-bottom: 60px;
    padding-bottom: 40px;
    border-bottom: 1px solid #1f1f23;
}

.velvet-blog-journal-tag {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 3px;
    color: #FE114B;
    display: inline-block;
    margin-bottom: 16px;
    padding: 4px 12px;
    background: rgba(254, 17, 75, 0.08);
    border: 1px solid rgba(254, 17, 75, 0.2);
    border-radius: 100px;
}

.velvet-blog-hero h1 {
    font-size: 48px;
    font-weight: 800;
    letter-spacing: -1px;
    color: #ffffff;
    margin: 0 0 16px;
    line-height: 1.15;
}

.velvet-blog-hero p {
    font-size: 18px;
    color: #a1a1aa;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Category Filter Ribbon */
.velvet-blog-categories-ribbon {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 32px;
    flex-wrap: wrap;
}

.velvet-category-filter-item {
    font-size: 14px;
    font-weight: 600;
    color: #a1a1aa;
    text-decoration: none;
    padding: 8px 18px;
    background: #121214;
    border: 1px solid #1f1f23;
    border-radius: 100px;
    transition: all 0.3s ease;
}

.velvet-category-filter-item:hover,
.velvet-category-filter-item.active {
    color: #ffffff;
    background: #FE114B;
    border-color: #FE114B;
    box-shadow: 0 4px 20px rgba(254, 17, 75, 0.25);
}

/* Featured Post Block */
.velvet-featured-post-card {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 40px;
    background: #121214;
    border: 1px solid #1f1f23;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 60px;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.velvet-featured-post-card:hover {
    border-color: rgba(254, 17, 75, 0.4);
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
}

.velvet-featured-thumb-container {
    position: relative;
    width: 100%;
    height: 100%;
    min-height: 380px;
    overflow: hidden;
}

.velvet-featured-thumb-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.velvet-featured-post-card:hover .velvet-featured-thumb-container img {
    transform: scale(1.04);
}

.velvet-featured-label {
    position: absolute;
    top: 24px;
    left: 24px;
    background: #FE114B;
    color: #ffffff;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 6px 14px;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(254, 17, 75, 0.3);
}

.velvet-featured-info-container {
    padding: 48px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.velvet-blog-meta-row {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 13px;
    color: #71717a;
    margin-bottom: 20px;
    font-weight: 500;
}

.velvet-meta-badge {
    background: rgba(255, 255, 255, 0.05);
    color: #e4e4e7;
    padding: 3px 10px;
    border-radius: 4px;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.velvet-dot-separator {
    width: 4px;
    height: 4px;
    background: #3f3f46;
    border-radius: 50%;
}

.velvet-featured-info-container h2 {
    font-size: 32px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 16px;
    line-height: 1.25;
}

.velvet-featured-info-container h2 a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.velvet-featured-info-container h2 a:hover {
    color: #FE114B;
}

.velvet-featured-excerpt {
    font-size: 16px;
    color: #a1a1aa;
    line-height: 1.6;
    margin: 0 0 32px;
}

.velvet-author-card {
    display: flex;
    align-items: center;
    gap: 12px;
}

.velvet-author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #27272a;
}

.velvet-author-name {
    font-size: 14px;
    font-weight: 600;
    color: #ffffff;
}

.velvet-author-title {
    font-size: 12px;
    color: #71717a;
    display: block;
}

/* Post Grid Columns */
.velvet-blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 32px;
    margin-bottom: 60px;
}

.velvet-grid-post-card {
    background: #121214;
    border: 1px solid #1f1f23;
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.velvet-grid-post-card:hover {
    border-color: rgba(254, 17, 75, 0.4);
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.35);
}

.velvet-grid-post-thumb {
    position: relative;
    width: 100%;
    aspect-ratio: 16/10;
    overflow: hidden;
    background: #18181b;
}

.velvet-grid-post-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.velvet-grid-post-card:hover .velvet-grid-post-thumb img {
    transform: scale(1.05);
}

.velvet-grid-post-info {
    padding: 28px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.velvet-grid-post-info h3 {
    font-size: 20px;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 12px;
    line-height: 1.4;
    height: 56px; /* consistent double-line layout height */
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.velvet-grid-post-info h3 a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.velvet-grid-post-info h3 a:hover {
    color: #FE114B;
}

.velvet-grid-post-excerpt {
    font-size: 14px;
    color: #a1a1aa;
    line-height: 1.55;
    margin-bottom: 24px;
    flex-grow: 1;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

/* Custom Premium Dark Pagination */
.velvet-blog-pagination {
    margin-top: 60px;
    text-align: center;
}

.velvet-blog-pagination .pagination {
    display: inline-flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
}

.velvet-blog-pagination .nav-links {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
}

.velvet-blog-pagination .page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    padding: 0 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    color: #a1a1aa;
    background: #121214;
    border: 1px solid #1f1f23;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.velvet-blog-pagination .page-numbers:hover {
    color: #ffffff;
    border-color: #FE114B;
    background: rgba(254, 17, 75, 0.05);
}

.velvet-blog-pagination .page-numbers.current {
    color: #ffffff;
    background: #FE114B;
    border-color: #FE114B;
    box-shadow: 0 4px 12px rgba(254, 17, 75, 0.25);
}

.velvet-blog-pagination .page-numbers.dots {
    border-color: transparent;
    background: transparent;
}

/* Responsive Overrides */
@media (max-width: 1024px) {
    .velvet-featured-post-card {
        grid-template-columns: 1fr;
        gap: 0;
    }
    .velvet-featured-thumb-container {
        height: 300px;
        min-height: auto;
    }
    .velvet-featured-info-container {
        padding: 32px;
    }
    .velvet-blog-hero h1 {
        font-size: 38px;
    }
}

@media (max-width: 768px) {
    .velvet-blog-container {
        padding: 40px 16px;
    }
    .velvet-blog-hero {
        margin-bottom: 40px;
    }
    .velvet-blog-hero h1 {
        font-size: 32px;
    }
    .velvet-blog-hero p {
        font-size: 15px;
    }
    .velvet-blog-categories-ribbon {
        margin-top: 24px;
        gap: 8px;
    }
    .velvet-category-filter-item {
        font-size: 13px;
        padding: 6px 14px;
    }
    .velvet-blog-grid {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    .velvet-featured-info-container h2 {
        font-size: 24px;
    }
}
</style>

<main class="velvet-blog-container">
    
    <!-- Hero Block -->
    <header class="velvet-blog-hero">
        <span class="velvet-blog-journal-tag">The VelvetReel Journal</span>
        <h1>Behind The Spotlight</h1>
        <p>Casting masterclasses, industry insights, and creative deep-dives straight from the active heart of media production.</p>
        
        <!-- Category Ribbon Filter -->
        <div class="velvet-blog-categories-ribbon">
            <a href="<?php echo esc_url(get_post_type_archive_link('post')); ?>" class="velvet-category-filter-item <?php echo (!is_category()) ? 'active' : ''; ?>">All Articles</a>
            <?php 
            $categories = get_categories(array(
                'orderby' => 'name', 
                'order' => 'ASC', 
                'number' => 8,
                'exclude' => array(1) // Exclude "Blog" category (term_id 1) to prevent double blog navigation
            ));
            foreach ($categories as $cat) {
                $is_active = is_category($cat->term_id) ? 'active' : '';
                echo '<a href="' . esc_url(get_category_link($cat->term_id)) . '" class="velvet-category-filter-item ' . $is_active . '">' . esc_html($cat->name) . '</a>';
            }
            ?>
        </div>
    </header>

    <?php if (have_posts()) : ?>
        
        <?php 
        // 1. Isolate the first post for our gorgeous full-width horizontal layout
        global $wp_query;
        $current_page = max(1, get_query_var('paged'));
        
        // Show horizontal featured post ONLY on first page
        if ($current_page === 1) :
            the_post();
            $feat_id = get_the_ID();
            $feat_categories = get_the_category();
            $feat_cat_name = !empty($feat_categories) ? $feat_categories[0]->name : 'Insights';
            $feat_read_time = velvet_get_reading_time($feat_id);
            $feat_author_id = get_the_author_meta('ID');
            $feat_avatar_url = get_avatar_url($feat_author_id, array('size' => 80));
            ?>
            
            <article class="velvet-featured-post-card">
                <div class="velvet-featured-thumb-container">
                    <?php if (has_post_thumbnail()) : ?>
                        <img src="<?php echo esc_url(get_the_post_thumbnail_url($feat_id, 'large')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                    <?php else : ?>
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/placeholder-ad.jpg'); ?>" alt="Featured Post" loading="lazy">
                    <?php endif; ?>
                    <span class="velvet-featured-label">Featured Insight</span>
                </div>
                
                <div class="velvet-featured-info-container">
                    <div class="velvet-blog-meta-row">
                        <span class="velvet-meta-badge"><?php echo esc_html($feat_cat_name); ?></span>
                        <span class="velvet-dot-separator"></span>
                        <span><?php echo esc_html(get_the_date()); ?></span>
                        <span class="velvet-dot-separator"></span>
                        <span><?php echo esc_html($feat_read_time); ?> min read</span>
                    </div>
                    
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    
                    <div class="velvet-featured-excerpt">
                        <?php echo wp_strip_all_tags(get_the_excerpt()); ?>
                    </div>
                    
                    <div class="velvet-author-card">
                        <img src="<?php echo esc_url($feat_avatar_url); ?>" class="velvet-author-avatar" alt="<?php echo esc_attr(get_the_author()); ?>">
                        <div>
                            <span class="velvet-author-name"><?php the_author(); ?></span>
                            <span class="velvet-author-title">Editor in Chief</span>
                        </div>
                    </div>
                </div>
            </article>
            
        <?php endif; ?>

        <!-- 2. Display secondary posts inside an elegant card layout grid -->
        <div class="velvet-blog-grid">
            <?php 
            while (have_posts()) : the_post(); 
                // Skip the featured post if we already rendered it on page 1
                if ($current_page === 1 && get_the_ID() === $feat_id) {
                    continue;
                }
                
                $post_id = get_the_ID();
                $post_categories = get_the_category();
                $post_cat_name = !empty($post_categories) ? $post_categories[0]->name : 'Article';
                $post_read_time = velvet_get_reading_time($post_id);
                $post_author_id = get_the_author_meta('ID');
                $post_avatar_url = get_avatar_url($post_author_id, array('size' => 80));
                ?>
                
                <article class="velvet-grid-post-card">
                    <div class="velvet-grid-post-thumb">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php echo esc_url(get_the_post_thumbnail_url($post_id, 'medium_large')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                            <?php else : ?>
                                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/placeholder-ad.jpg'); ?>" alt="Blog Post Thumbnail" loading="lazy">
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <div class="velvet-grid-post-info">
                        <div class="velvet-blog-meta-row">
                            <span class="velvet-meta-badge"><?php echo esc_html($post_cat_name); ?></span>
                            <span class="velvet-dot-separator"></span>
                            <span><?php echo esc_html(get_the_date('M d, Y')); ?></span>
                        </div>
                        
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        
                        <div class="velvet-grid-post-excerpt">
                            <?php echo wp_strip_all_tags(get_the_excerpt()); ?>
                        </div>
                        
                        <div class="velvet-author-card" style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #1f1f23;">
                            <img src="<?php echo esc_url($post_avatar_url); ?>" class="velvet-author-avatar" style="width: 32px; height: 32px;" alt="<?php echo esc_attr(get_the_author()); ?>">
                            <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; width: 100%;">
                                <span class="velvet-author-name" style="font-size: 13px;"><?php the_author(); ?></span>
                                <span style="font-size: 12px; color: #71717a; font-weight: 500;"><?php echo esc_html($post_read_time); ?> min read</span>
                            </div>
                        </div>
                    </div>
                </article>
                
            <?php endwhile; ?>
        </div>

        <!-- Custom Dark Pagination Links -->
        <footer class="velvet-blog-pagination">
            <div class="pagination">
                <?php
                echo paginate_links(array(
                    'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                    'format'       => '?paged=%#%',
                    'current'      => $current_page,
                    'total'        => $wp_query->max_num_pages,
                    'prev_text'    => '&larr; Prev',
                    'next_text'    => 'Next &rarr;',
                    'type'         => 'list',
                    'end_size'     => 1,
                    'mid_size'     => 1
                ));
                ?>
            </div>
        </footer>

    <?php else : ?>
        <div style="text-align: center; padding: 100px 24px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="#71717a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="margin-bottom: 16px;"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path><path d="M6 6h10"></path><path d="M6 10h10"></path></svg>
            <h2 style="color: #ffffff; font-weight: 700; font-size: 24px; margin-bottom: 8px;">No Journal Entries Found</h2>
            <p style="color: #71717a; font-size: 16px;">We haven't published any articles in this category yet. Check back soon!</p>
        </div>
    <?php endif; ?>

</main>

<?php
get_footer();
