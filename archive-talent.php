<?php
/**
 * The template for displaying Talent archive pages.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>

<main id="content" class="site-main">

   <?php if ( ! is_user_logged_in() ) : ?>
        <header class="page-header spacer-top">
            <h1 class="entry-title">
                <?php esc_html_e( 'My Portfolios', 'hello-elementor-child' ); ?>
            </h1>
            <p class="page-description">
                <?php esc_html_e( 'Please log in to view your portfolios.', 'hello-elementor-child' ); ?>
            </p>
            <a href="<?php echo esc_url( home_url( '/sign-up' ) ); ?>" class="btn-create-portfolio">Sign Up</a>
        </header>
        
        
        </main>
        
        <?php
        get_footer();
        return;
    endif;
    
    
    // For logged-in users, modify the query to show only their talent posts
    $current_user_id = get_current_user_id();
    global $wp_query;
    
    // Store the original query
    $original_query = $wp_query;
    
    // Create a new query for user's talent posts
    $args = array(
        'post_type' => 'talent',
        'author' => $current_user_id,
        'posts_per_page' => -1, // Get all posts
        'post_status' => array('publish', 'draft', 'pending')
    );
    
    $user_talent_query = new WP_Query($args);
    $wp_query = $user_talent_query;
    ?>

    <header class="page-header spacer-top">
        <h1 class="entry-title">
            <?php
            $current_user = wp_get_current_user();
            printf(
                /* translators: %s: User's display name. */
                esc_html__( 'My Portfolios', 'hello-elementor-child' ),
                $current_user->display_name
            );
            ?>
        </h1>
        <p class="page-description">
            <?php esc_html_e( 'View and manage your talent portfolios.', 'hello-elementor-child' ); ?>
        </p>

        <?php
        echo '<a href="' . esc_url( home_url( '/talent-registration' ) ) . '" class="btn-create-portfolio">Create Your Portfolio</a>';
        ?>
    </header>
<style>

.subscription-status-indicator {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px 20px;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.05), rgba(46, 125, 50, 0.05));
    border-radius: 16px;
    position: relative;
    overflow: hidden;

    position: absolute;
    top: 130px;
    left: 0;
    right: 0;
}

/* Premium shine effect */
.subscription-status-indicator::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: shine 3s infinite;
}

@keyframes shine {
    to {
        left: 100%;
    }
}

.subscription-badge {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    /* background: linear-gradient(135deg, #4CAF50, #2E7D32); */
    color: white;
    border-radius: 50px;
    font-size: 14px;
    box-shadow: 0 8px 24px rgba(76, 175, 80, 0.4);
    position: relative;
    border: 2px solid rgba(255, 255, 255, 0.2);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background-color: #000 !important;
    color: #eee;
}

.subscription-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(76, 175, 80, 0.5);
}

/* Checkmark or crown icon styling */
.subscription-badge svg {
    fill: white;
    width: 24px;
    height: 24px;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

/* Add a premium label below the badge */
.subscription-benefits {
    margin-top: 20px;
    font-size: 14px;
    color: #4CAF50;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.post-type-archive-talent {
    background-color: #000;
    color: #eee;
}


</style>
    <?php echo do_shortcode('[elementor_subscription_status]'); ?>


    <div class="page-content">
        <div class="talent-archive-grid">
            <?php if ( have_posts() ) : ?>
                <?php
                while ( have_posts() ) :
                    the_post();

                    // --- Get Meta Data ---
                    $role = get_post_meta( get_the_ID(), '_talent_role', true );
                    // Mock data for stats as they are not in the form yet
                    $project_views = (int) get_post_meta( get_the_ID(), '_talent_project_views', true ); // Not implemented yet, defaults to 0
                    $saved_to_lists = (int) get_post_meta( get_the_ID(), '_talent_saved_to_lists', true ); // Not implemented yet, defaults to 0
                    $profile_clicks = (int) get_post_meta( get_the_ID(), '_talent_profile_clicks', true );
                    ?>
                    <div class="talent-card">
                        <div class="talent-card-header">
                            <?php if ( get_post_status() === 'publish' ) : ?>
                                <span class="status-badge">Published</span>
                            <?php else : ?>
                                <span class="status-badge pending"><?php echo esc_html( ucfirst( get_post_status() ) ); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="talent-card-body">
                            <h3 class="talent-role"><?php echo esc_html( ucwords( str_replace( '-', ' ', $role ) ) ); ?></h3>
                            <p class="talent-name"><?php the_title(); ?></p>
                        </div>
                        <div class="talent-card-footer">
                            <div class="talent-stats">
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo number_format_i18n( $project_views ); ?></span>
                                    <span class="stat-label">Project Views</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo number_format_i18n( $saved_to_lists ); ?></span>
                                    <span class="stat-label">Saved to Lists</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value"><?php echo number_format_i18n( $profile_clicks ); ?></span>
                                    <span class="stat-label">Profile Clicks</span>
                                </div>
                            </div>
                            <div class="talent-card-actions">
                           
                                <?php
                                $edit_page = get_page_by_path( 'edit-talent-profile' );
                                if ( $edit_page ) :
                                    $edit_page_url = get_permalink( $edit_page->ID );
                                    $edit_url_with_id = add_query_arg( 'talent_id', get_the_ID(), $edit_page_url );
                                ?>
                                <a href="<?php echo esc_url( $edit_url_with_id ); ?>" class="view-button edit-button">Edit</a>
                                <?php endif; ?>
                                 <a href="<?php the_permalink(); ?>" class="view-button">View</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <?php
                // Restore original query
                $wp_query = $original_query;
                wp_reset_postdata();
                ?>
            <?php else : ?>
                <p><?php esc_html_e( 'No talent profiles found.', 'hello-elementor-child' ); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="archive-pagination">
        <?php
        // Pagination
        the_posts_pagination( array(
            'prev_text' => '&laquo; Previous',
            'next_text' => 'Next &raquo;',
        ) );
        ?>
    </div>

</main>

<?php
get_footer();
?>