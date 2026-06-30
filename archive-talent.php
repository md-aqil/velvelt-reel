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

   <?php 
    // Check if user is logged in
    $is_logged_in = is_user_logged_in();
    
    // If not logged in, check if this is a redirect from login with cache-busting parameter
    if ( ! $is_logged_in && isset($_GET['login']) && $_GET['login'] === 'success' ) {
        // Force WordPress to re-check authentication
        wp_get_current_user();
        $is_logged_in = is_user_logged_in();
    }
    
    if ( ! $is_logged_in ) : ?>
        <header class="page-header spacer-top">
            <h1 class="entry-title">
                <?php esc_html_e( 'My Portfolios', 'hello-elementor-child' ); ?>
            </h1>
            <p class="page-description">
                <?php esc_html_e( 'Please log in to view your portfolios.', 'hello-elementor-child' ); ?>
            </p>
            <a href="<?php echo esc_url( home_url( '/membership-join' ) ); ?>" class="btn-create-portfolio">Sign Up</a>
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
        'posts_per_page' => 1, // Fix Duplicate Portfolio issue by ensuring only one shows
        'post_status' => array('publish', 'draft', 'pending')
    );
    
    $user_talent_query = new WP_Query($args);
    $wp_query = $user_talent_query;
    ?>

    <header class="page-header">
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
            <?php esc_html_e( 'Create your FREE Portfolio', 'hello-elementor-child' ); ?>
        </p>

        <?php
        // Include meta-fields to use user_has_talent_profile function
        if (!function_exists('user_has_talent_profile')) {
            require_once get_stylesheet_directory() . '/includes/meta-fields.php';
        }
        
        // Include access control functions if not already included
        if (!function_exists('has_membership_plan')) {
            require_once get_stylesheet_directory() . '/includes/access-control.php';
        }
        
        // Ensure user's plan tracking is properly initialized
        ensure_user_plan_tracking();
        
        // Also migrate current level to plans for backward compatibility
        migrate_user_current_level_to_plans();
        
        // Check if user already has a talent profile
        if (user_has_talent_profile(null, true)) {
            // Get the first talent post to check status
            $user_talent_query = new WP_Query(array(
                'post_type' => 'talent',
                'author' => get_current_user_id(),
                'posts_per_page' => 1,
                'post_status' => array('publish', 'draft', 'pending')
            ));
            if ($user_talent_query->have_posts()) {
                $first_talent_post = $user_talent_query->posts[0];
                        
                // Show different messages based on portfolio status
                if ($first_talent_post->post_status === 'pending') {
                    // Check if user has already paid for the approval plan
                    $has_paid_approval = get_post_meta($first_talent_post->ID, '_talent_has_paid_approval', true);
                    
                    if ($has_paid_approval) {
                        echo '<div class="pending-approval-message" style="background:rgba(240, 248, 255, 0.2); padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #0073aa;">
                            <h3 style="margin-top: 0; color: #0073aa;">Admin Approval Pending</h3>
                            <p style="margin-bottom: 15px;">Relax, your profile is completed and under admin review.</p>';
                        
                        // Show success message with payment details
                        $payment_date = get_post_meta($first_talent_post->ID, '_talent_approval_payment_date', true);
                        $payment_amount = get_post_meta($first_talent_post->ID, '_talent_approval_payment_amount', true);
                        $payment_info = get_post_meta($first_talent_post->ID, '_talent_approval_payment_info', true);
                        
                        echo '<div class="approval-paid-success" style="background: rgba(178, 18, 45, 0.15); padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #b2122d; box-shadow: 0 4px 8px rgba(178, 18, 45, 0.1);">
                            <h3 style="margin-top: 0; color: #b2122d; font-size: 1.3em;"><i class="fas fa-check-circle" style="margin-right: 10px;"></i>Approval Payment Confirmed!</h3>
                            <p style="margin-bottom: 10px; color: #333;">Great news! You\'ve successfully paid for faster portfolio approval. Our team will prioritize reviewing and publishing your portfolio shortly.</p>';

                        // Display payment details
                        echo '<div class="payment-details" style="background: rgba(255, 255, 255, 0.7); padding: 12px; border-radius: 5px; margin-top: 10px; border: 1px solid rgba(178, 18, 45, 0.2);">
                            <strong style="color: #b2122d; display: block; margin-bottom: 8px; font-size: 1.1em;">Payment Information:</strong>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px;">';
                        if ($payment_amount) {
                            echo '<span style="color: #b2122d; font-weight: 500;"><i class="fas fa-dollar-sign" style="margin-right: 5px;"></i>Amount: $' . esc_html($payment_amount) . '</span>';
                        }
                        if ($payment_date) {
                            echo '<span style="color: #333;"><i class="fas fa-calendar-alt" style="margin-right: 5px;"></i>' . esc_html(date('M j, Y', strtotime($payment_date))) . '</span>';
                        }
                        if ($payment_info) {
                            echo '<span style="color: #555; word-wrap: break-word;"><i class="fas fa-credit-card" style="margin-right: 5px;"></i>' . esc_html($payment_info) . '</span>';
                        }
                        echo '</div>';
                        echo '</div>';

                        echo '</div>';
                        echo '</div>';
                    } else {
                        // Show payment button for users who haven't paid yet
                        // Use WP Stripe Checkout shortcode for portfolio approval
                        $stripe_button = do_shortcode('[wp_stripe_checkout_session name="Portfolio Approval" price="5.00" button_text="Pay Now" success_url="https://www.thevelvetreel.com/talent/?session_id={CHECKOUT_SESSION_ID}"]');
                        
                        echo '<div class="pending-verification-card">
                            <div class="notification-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                            </div>
                            <div class="notification-content">
                                <h3>Congratulations, you\'re just one step away !</h3>
                                <p>Finish the one time payment verification fee - <strong>$5</strong> to lock in your portfolio submission by admin.</p>';
                        
                        if (!empty($stripe_button)) {
                            echo '<div class="stripe-payment-container">';
                            echo $stripe_button;
                            echo '</div>';
                        }
                        
                        echo '</div>
                        </div>';
                    }
                } elseif ($first_talent_post->post_status === 'draft') {
                    echo '<div class="draft-message" style="background:rgba(255, 248, 220, 0.18); padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #ffa500;">
                        <h3 style="margin-top: 0; color: #ffa500;">Portfolio Incomplete</h3>
                        <p style="margin-bottom: 0;">Your portfolio is partially complete, finish it to publish.</p>';
                    
                    // Check if user has already paid for the approval plan
                    $has_paid_approval = get_post_meta($first_talent_post->ID, '_talent_has_paid_approval', true);
                    
                    if ($has_paid_approval) {
                        // Show success message with payment details
                        $payment_date = get_post_meta($first_talent_post->ID, '_talent_approval_payment_date', true);
                        $payment_amount = get_post_meta($first_talent_post->ID, '_talent_approval_payment_amount', true);
                        $payment_info = get_post_meta($first_talent_post->ID, '_talent_approval_payment_info', true);
                        
                        echo '<div class="approval-paid-success" style="background: rgba(76, 175, 80, 0.1); padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #4CAF50;">
                            <h3 style="margin-top: 0; color: #4CAF50;">Approval Payment Confirmed!</h3>
                            <p style="margin-bottom: 10px;">Great news! You\'ve successfully paid for faster portfolio approval. Our team will prioritize reviewing and publishing your portfolio shortly.</p>';
                            
                        // Display payment details
                        echo '<div class="payment-details" style="background: rgba(255, 255, 255, 0.2); padding: 10px; border-radius: 5px; margin-top: 10px;">';
                        echo '<strong>Payment Information:</strong><br>';
                        if ($payment_amount) {
                            echo '<span style="color: #2E7D32;">Amount: $' . esc_html($payment_amount) . '</span><br>';
                        }
                        if ($payment_date) {
                            echo '<span style="color: #1976D2;">Date: ' . esc_html(date('M j, Y g:i A', strtotime($payment_date))) . '</span><br>';
                        }
                        if ($payment_info) {
                            echo '<span style="color: #5D4037;">Method: ' . esc_html($payment_info) . '</span>';
                        }
                        echo '</div>';
                        
                        echo '</div>';
                    } else {
                        // Show payment button for users who haven't paid yet
                        // Use WP Stripe Checkout shortcode for portfolio approval
                        $stripe_button = do_shortcode('[wp_stripe_checkout_session name="Portfolio Approval" price="5.00" button_text="Pay Now" success_url="https://www.thevelvetreel.com/talent/?session_id={CHECKOUT_SESSION_ID}"]');
                        if (!empty($stripe_button)) {
                            echo '<div class="portfolio-approval-payment" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ccc;">';
                            echo '<p style="margin-bottom: 10px; font-weight: bold;">Need faster approval? Pay with this plan:</p>';
                            echo $stripe_button;
                            echo '</div>';
                        }
                    }
                    
                    echo '</div>';
                }
                        
                $edit_url = add_query_arg('talent_id', $first_talent_post->ID, home_url('/edit-talent-profile/'));
                echo '<a href="' . esc_url($edit_url) . '" class="btn-create-portfolio">Edit Portfolio</a>';
            }
        } else {
            // Only show create button if user doesn't have a profile
            echo '<a href="' . esc_url( home_url( '/talent-registration' ) ) . '" class="btn-create-portfolio">Create Your Portfolio</a>';
        }
        ?>
    </header>
<style>

/* Premium design for the verification card */
.pending-verification-card {
    background: rgba(255, 255, 255, 0.05) !important;
    backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 12px !important;
    padding: 24px !important;
    margin: 20px auto 30px auto !important;
    max-width: 650px !important;
    display: flex !important;
    align-items: flex-start !important;
    gap: 20px !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3) !important;
    font-family: 'Inter', 'Poppins', sans-serif !important;
    text-align: left !important;
    transition: all 0.3s ease !important;
    color: #ffffff !important;
}
.pending-verification-card:hover {
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4) !important;
    transform: translateY(-2px) !important;
}
.pending-verification-card .notification-icon {
    flex-shrink: 0 !important;
    width: 48px !important;
    height: 48px !important;
    background: rgba(220, 53, 69, 0.15) !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #ff6b7a !important;
    box-shadow: 0 4px 10px rgba(220, 53, 69, 0.1) !important;
    border: 1px solid rgba(220, 53, 69, 0.3) !important;
}
.pending-verification-card .notification-icon svg {
    width: 24px !important;
    height: 24px !important;
}
.pending-verification-card .notification-content {
    flex: 1 !important;
}
.pending-verification-card h3 {
    margin: 0 0 10px 0 !important;
    font-size: 20px !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    letter-spacing: -0.3px !important;
    line-height: 1.3 !important;
}
.pending-verification-card p {
    margin: 0 0 20px 0 !important;
    color: #cccccc !important;
    font-size: 15.5px !important;
    line-height: 1.6 !important;
}
.pending-verification-card strong {
    color: #ff6b7a !important;
    font-weight: 700 !important;
    font-size: 1.1em !important;
}

/* Pay Now Stripe Button - Enhanced */
.pending-verification-card .stripe-payment-container {
    margin-top: 15px !important;
    padding-top: 15px !important;
    border-top: 1px dashed rgba(255, 255, 255, 0.1) !important;
}
.pending-verification-card .wp-stripe-checkout-button,
.pending-verification-card button,
.pending-verification-card input[type="submit"],
.pending-verification-card .stripe-button-inner {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 14px 36px !important;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    cursor: pointer !important;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3) !important;
    text-decoration: none !important;
}
.pending-verification-card button:hover,
.pending-verification-card input[type="submit"]:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4) !important;
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%) !important;
}
.pending-verification-card button:active,
.pending-verification-card input[type="submit"]:active {
    transform: translateY(0) !important;
    box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3) !important;
}

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
                               
                              
                            </div>
                            <div class="talent-card-actions">
                                
                                <?php
                                // Instead of using get_page_by_path, let's construct the URL directly
                                // This avoids issues if the page doesn't exist in the database
                                $edit_page_url = home_url('/edit-talent-profile/');
                                if ($edit_page_url) :
                                    $edit_url_with_id = add_query_arg('talent_id', get_the_ID(), $edit_page_url);
                                ?>
                                <a href="<?php echo esc_url($edit_url_with_id); ?>" class="view-button edit-button">Edit</a>
                                <?php endif; ?>
                                <?php if (get_post_status() === 'publish') : ?>
                                 <a href="<?php the_permalink(); ?>" class="view-button">View</a>
                                <?php endif; ?>
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
                <div class="no-talent-profiles">
                    <p><?php esc_html_e( 'No talent profiles found.', 'hello-elementor-child' ); ?></p>
                   
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="archive-pagination" style="display: none;">
        <?php
        // Pagination removed to prevent showing duplicate portfolios
        /* the_posts_pagination( array(
            'prev_text' => '&laquo; Previous',
            'next_text' => 'Next &raquo;',
        ) ); */
        ?>
    </div>

</main>

<?php
get_footer();
?>