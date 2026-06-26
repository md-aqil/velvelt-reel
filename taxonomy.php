<?php
/**
 * The template for displaying Advertisement Category archive pages
 *
 * @package HelloElementorChild
 */

get_header();

$term = get_queried_object();

// Custom query for advertisements in this category
$args = array(
    'post_type' => 'advertisement',
    'posts_per_page' => -1,
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

<main class="site-main" role="main">
    <header class="page-header">
        <h1 class="entry-title" style="color: #fff; margin-top: 50px;">
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
        <?php endif; ?>
    </header>

    <?php if ($query->have_posts()) : ?>
        <?php
        // Get all advertisement categories
        $categories = get_terms(array(
            'taxonomy' => 'advertisement_category',
            'hide_empty' => false,
        ));
        ?>
        
        <?php if (!empty($categories)) : ?>
            <div class="advertisement-categories">
                <ul class="category-filters">
                    <li class="<?php echo (!is_tax('advertisement_category')) ? 'active' : ''; ?>">
                        <a href="<?php echo get_post_type_archive_link('advertisement'); ?>"><?php _e('All Categories', 'hello-elementor-child'); ?></a>
                    </li>
                    <?php foreach ($categories as $category) : ?>
                        <li class="<?php echo (is_tax('advertisement_category', $category->term_id)) ? 'active' : ''; ?>">
                            <a href="<?php echo get_term_link($category); ?>"><?php echo esc_html($category->name); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="advertisements-grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('advertisement-item'); ?>>
                    <div class="advertisement-thumbnail">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="advertisement-content">
                        <h2 class="advertisement-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <div class="advertisement-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <div class="advertisement-meta">
                            <span class="advertisement-date"><?php echo get_the_date(); ?></span>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <?php the_posts_pagination(); ?>
        
    <?php else : ?>
        <p><?php _e('No advertisements found in this category.', 'hello-elementor-child'); ?></p>
    <?php endif; ?>
    
    <?php wp_reset_postdata(); ?>
</main>

<?php get_footer(); ?>