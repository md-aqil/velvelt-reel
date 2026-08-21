<?php
/**
 * The template for displaying single blog posts (single.php).
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

while (have_posts()) : the_post();
    $post_id = get_the_ID();
    $categories = get_the_category();
    $cat_name = !empty($categories) ? $categories[0]->name : 'Insights';
    $read_time = velvet_get_reading_time($post_id);
    $author_id = get_the_author_meta('ID');
    $author_avatar_url = get_avatar_url($author_id, array('size' => 120));
    $author_bio = get_the_author_meta('description');
    if (empty($author_bio)) {
        $author_bio = 'Industry veteran and resident expert contributor at VelvetReel. Crafting masterclasses and editorial guides to elevate creative standards.';
    }
    
    // Get share URLs
    $permalink = esc_url(get_permalink());
    $title_esc = esc_attr(get_the_title());
    $twitter_url = "https://twitter.com/intent/tweet?text=" . urlencode(get_the_title() . " - " . get_permalink());
    $linkedin_url = "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode(get_permalink());
    $whatsapp_url = "https://api.whatsapp.com/send?text=" . urlencode(get_the_title() . " " . get_permalink());
?>

<style>
/* Single Blog Post Premium Dark Layout */
body.single-post {
    background-color: #09090b !important;
    color: #e4e4e7 !important;
    font-family: 'Outfit', 'Inter', 'Poppins', sans-serif !important;
}

/* Scroll reading progress bar */
.velvet-progress-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: transparent;
    z-index: 99999;
}

.velvet-progress-bar {
    height: 100%;
    background: #FE114B;
    width: 0%;
    box-shadow: 0 0 10px rgba(254, 17, 75, 0.8);
    transition: width 0.1s ease-out;
}

.velvet-single-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 60px 24px 80px;
}

/* Header section styling */
.velvet-post-header {
    text-align: center;
    margin-bottom: 48px;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}

.velvet-post-cat-tag {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2.5px;
    color: #FE114B;
    display: inline-block;
    margin-bottom: 18px;
    padding: 3px 12px;
    background: rgba(254, 17, 75, 0.08);
    border: 1px solid rgba(254, 17, 75, 0.2);
    border-radius: 100px;
}

.velvet-single-title {
    font-size: 44px;
    font-weight: 800;
    letter-spacing: -1px;
    color: #ffffff;
    line-height: 1.2;
    margin: 0 0 24px;
}

.velvet-author-meta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    font-size: 14px;
    color: #a1a1aa;
    font-weight: 500;
}

.velvet-meta-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid #27272a;
}

.velvet-dot {
    width: 4px;
    height: 4px;
    background: #52525b;
    border-radius: 50%;
}

/* Cinematic featured image */
.velvet-cinematic-banner {
    width: 100%;
    max-height: 520px;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 56px;
    border: 1px solid #1f1f23;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
}

.velvet-cinematic-banner img {
    width: 100%;
    height: 100%;
    max-height: 520px;
    object-fit: cover;
}

/* Two-column layout grid */
.velvet-article-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 56px;
}

/* Left main content styling */
.velvet-post-body {
    font-size: 17px;
    line-height: 1.75;
    color: #d4d4d8;
}

/* WordPress post content elements override */
.velvet-post-body p {
    margin-bottom: 28px;
}

.velvet-post-body h2 {
    font-size: 28px;
    font-weight: 700;
    color: #ffffff;
    margin: 44px 0 20px;
    line-height: 1.3;
}

.velvet-post-body h3 {
    font-size: 22px;
    font-weight: 600;
    color: #ffffff;
    margin: 36px 0 16px;
}

.velvet-post-body blockquote {
    border-left: 4px solid #FE114B;
    background: rgba(254, 17, 75, 0.03);
    padding: 24px 32px;
    margin: 40px 0;
    font-style: italic;
    font-size: 19px;
    line-height: 1.6;
    color: #ffffff;
    border-radius: 0 16px 16px 0;
}

.velvet-post-body blockquote p:last-child {
    margin-bottom: 0;
}

.velvet-post-body ul, .velvet-post-body ol {
    margin-bottom: 28px;
    padding-left: 24px;
}

.velvet-post-body li {
    margin-bottom: 10px;
}

.velvet-post-body img {
    border-radius: 12px;
    max-width: 100%;
    height: auto;
    margin: 24px 0;
    border: 1px solid #1f1f23;
}

/* Right sticky sidebar */
.velvet-post-sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
    align-self: start; /* Standard CSS Grid fix for sticky sidebars */
}

.velvet-sidebar-widget {
    background: #121214;
    border: 1px solid #1f1f23;
    border-radius: 16px;
    padding: 28px;
    margin-bottom: 32px;
}

.velvet-widget-title {
    font-size: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #ffffff;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #1f1f23;
}

/* Author widget profile card */
.velvet-sidebar-author-profile {
    text-align: center;
}

.velvet-sidebar-author-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid #FE114B;
    margin-bottom: 16px;
}

.velvet-sidebar-author-name {
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 4px;
    display: block;
}

.velvet-sidebar-author-title {
    font-size: 12px;
    color: #71717a;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 16px;
    display: block;
}

.velvet-sidebar-author-bio {
    font-size: 14px;
    color: #a1a1aa;
    line-height: 1.55;
    margin-bottom: 0;
}

/* Sharing widget icons */
.velvet-share-ribbon {
    display: flex;
    gap: 12px;
}

.velvet-share-button {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    height: 44px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #a1a1aa;
    text-decoration: none;
    border: 1px solid #1f1f23;
    background: #18181b;
    transition: all 0.3s ease;
}

.velvet-share-button svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}

.velvet-share-button:hover {
    color: #ffffff;
    border-color: #FE114B;
    background: rgba(254, 17, 75, 0.05);
}

/* Recommended reading links list */
.velvet-recommended-post {
    display: flex;
    gap: 16px;
    align-items: center;
    margin-bottom: 18px;
    text-decoration: none;
}

.velvet-recommended-post:last-child {
    margin-bottom: 0;
}

.velvet-recommended-thumb {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
    background: #18181b;
    border: 1px solid #1f1f23;
}

.velvet-recommended-meta {
    flex-grow: 1;
}

.velvet-recommended-title {
    font-size: 14px;
    font-weight: 600;
    color: #ffffff;
    line-height: 1.4;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
}

.velvet-recommended-post:hover .velvet-recommended-title {
    color: #FE114B;
}

.velvet-recommended-date {
    font-size: 12px;
    color: #71717a;
    font-weight: 500;
}

/* Custom premium comment styling */
.velvet-comments-container {
    margin-top: 64px;
    padding-top: 48px;
    border-top: 1px solid #1f1f23;
    max-width: 820px;
}

#comments-title {
    font-size: 24px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 32px;
}

.comment-respond {
    background: #121214;
    border: 1px solid #1f1f23;
    border-radius: 16px;
    padding: 32px;
    margin-top: 40px;
}

.comment-reply-title {
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 24px;
}

.comment-form input[type="text"],
.comment-form input[type="email"],
.comment-form textarea {
    width: 100%;
    background: #18181b;
    border: 1px solid #27272a;
    border-radius: 8px;
    padding: 12px 16px;
    color: #ffffff;
    font-size: 14px;
    margin-bottom: 16px;
    box-sizing: border-box;
    font-family: inherit;
    transition: all 0.3s ease;
}

.comment-form input[type="text"]:focus,
.comment-form input[type="email"]:focus,
.comment-form textarea:focus {
    border-color: #FE114B;
    outline: none;
    box-shadow: 0 0 0 1px rgba(254, 17, 75, 0.2);
}

.comment-form .submit {
    background: #FE114B;
    color: #ffffff;
    border: none;
    padding: 12px 28px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 14px rgba(254, 17, 75, 0.3);
}

.comment-form .submit:hover {
    background: #ff2145;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(254, 17, 75, 0.4);
}

/* Responsive Overrides */
@media (max-width: 1024px) {
    .velvet-article-grid {
        grid-template-columns: 1fr;
        gap: 48px;
    }
    .velvet-post-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .velvet-single-container {
        padding: 40px 16px;
    }
    .velvet-post-header {
        margin-bottom: 32px;
    }
    .velvet-single-title {
        font-size: 32px;
    }
    .velvet-cinematic-banner {
        margin-bottom: 36px;
        height: 260px;
    }
    .velvet-post-body {
        font-size: 16px;
        line-height: 1.65;
    }
    .comment-respond {
        padding: 20px;
    }
}
</style>

<!-- Scroll reading progress container -->
<div class="velvet-progress-container">
    <div id="velvet-reading-progress" class="velvet-progress-bar"></div>
</div>

<main class="velvet-single-container">
    
    <!-- Article Header -->
    <header class="velvet-post-header">
        <span class="velvet-post-cat-tag"><?php echo esc_html($cat_name); ?></span>
        <h1 class="velvet-single-title"><?php the_title(); ?></h1>
        
        <div class="velvet-author-meta">
            <img src="<?php echo esc_url($author_avatar_url); ?>" class="velvet-meta-avatar" alt="<?php echo esc_attr(get_the_author()); ?>">
            <span style="font-weight:600; color:#ffffff;"><?php the_author(); ?></span>
            <span class="velvet-dot"></span>
            <span><?php echo esc_html(get_the_date('M d, Y')); ?></span>
            <span class="velvet-dot"></span>
            <span><?php echo esc_html($read_time); ?> min read</span>
        </div>
    </header>

    <!-- Cinematic Rounded Hero Image -->
    <?php if (has_post_thumbnail()) : ?>
        <figure class="velvet-cinematic-banner">
            <?php the_post_thumbnail('full'); ?>
        </figure>
    <?php endif; ?>

    <!-- Two Column Reading Columns Layout -->
    <div class="velvet-article-grid">
        
        <!-- Left Pane: Article Body -->
        <article class="velvet-post-body">
            <?php the_content(); ?>
            
            <!-- Comment Section block nested inside the main reading stream flow -->
            <?php if (comments_open() || get_comments_number()) : ?>
                <section class="velvet-comments-container">
                    <?php comments_template(); ?>
                </section>
            <?php endif; ?>
        </article>

        <!-- Right Pane: Sticky Sidebar widgets -->
        <aside class="velvet-post-sidebar">
            
            <!-- Widget 1: Author Card -->
            <section class="velvet-sidebar-widget velvet-sidebar-author-profile">
                <img src="<?php echo esc_url($author_avatar_url); ?>" class="velvet-sidebar-author-avatar" alt="<?php echo esc_attr(get_the_author()); ?>">
                <span class="velvet-sidebar-author-name"><?php the_author(); ?></span>
                <span class="velvet-sidebar-author-title">Staff Writer</span>
                <p class="velvet-sidebar-author-bio"><?php echo esc_html($author_bio); ?></p>
            </section>
            
            <!-- Widget 2: Floating Social Share buttons -->
            <section class="velvet-sidebar-widget">
                <h4 class="velvet-widget-title">Spread the Word</h4>
                <div class="velvet-share-ribbon">
                    <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" class="velvet-share-button" title="Share on Twitter/X">
                        <svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 3.778 8.502 11.24H16.17l-5.214-6.817L4.99 17.25H1.68l7.73-8.035L1.254 2.25H8.08l4.713 6.231zm-1.161 13.02h1.833L7.084 4.126H5.117z"/></svg>
                        X
                    </a>
                    <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" class="velvet-share-button" title="Share on LinkedIn">
                        <svg viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0z"/></svg>
                        Share
                    </a>
                    <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" class="velvet-share-button" title="Share on WhatsApp">
                        <svg viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.753-1.464L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.743.002-2.602-1.01-5.05-2.85-6.892-1.84-1.84-4.291-2.853-6.891-2.854-5.445 0-9.873 4.37-9.877 9.744-.002 1.758.48 3.472 1.396 4.965l-.916 3.344 3.449-.875zm11.378-6.195c-.3-.149-1.772-.865-2.047-.964-.274-.099-.474-.149-.674.149-.199.297-.773.964-.947 1.162-.175.198-.349.223-.649.074-.3-.149-1.265-.461-2.41-1.472-.891-.786-1.492-1.756-1.667-2.053-.175-.297-.019-.458.131-.606.135-.133.3-.347.45-.52.149-.174.2-.298.3-.496.1-.198.05-.371-.025-.52-.075-.149-.674-1.595-.924-2.189-.244-.585-.491-.507-.674-.507-.174-.001-.374-.001-.573-.001-.199 0-.524.074-.799.371-.274.297-1.048 1.016-1.048 2.477 0 1.46 1.074 2.871 1.223 3.07.149.198 2.113 3.188 5.118 4.466.715.304 1.273.486 1.708.623.719.224 1.373.193 1.89.116.576-.085 1.772-.716 2.022-1.41.25-.693.25-1.287.175-1.41-.075-.124-.275-.198-.575-.347z"/></svg>
                        Send
                    </a>
                </div>
            </section>
            
            <!-- Widget 3: Popular Articles slider lists -->
            <section class="velvet-sidebar-widget">
                <h4 class="velvet-widget-title">Popular Reads</h4>
                <?php
                $recent_posts = get_posts(array(
                    'numberposts' => 4,
                    'post__not_in' => array($post_id),
                    'post_status' => 'publish'
                ));
                
                foreach ($recent_posts as $recent) :
                    $rec_thumb = get_the_post_thumbnail_url($recent->ID, 'thumbnail');
                    if (empty($rec_thumb)) {
                        $rec_thumb = get_stylesheet_directory_uri() . '/assets/placeholder-ad.jpg';
                    }
                    $rec_date = get_the_date('M d, Y', $recent->ID);
                ?>
                    <a href="<?php echo esc_url(get_permalink($recent->ID)); ?>" class="velvet-recommended-post">
                        <img src="<?php echo esc_url($rec_thumb); ?>" class="velvet-recommended-thumb" alt="<?php echo esc_attr($recent->post_title); ?>">
                        <div class="velvet-recommended-meta">
                            <span class="velvet-recommended-title"><?php echo esc_html($recent->post_title); ?></span>
                            <span class="velvet-recommended-date"><?php echo esc_html($rec_date); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </section>
            
        </aside>
    </div>

</main>

<script>
// Inline interactive reading scroll progress logic
document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('velvet-reading-progress');
    if (!progressBar) return;
    
    window.addEventListener('scroll', function() {
        const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        progressBar.style.width = scrolled + '%';
    });
});
</script>

<?php
endwhile;

get_footer();
