<?php
/**
 * The template for displaying single Advertisement posts
 *
 * @package HelloElementorChild
 */

get_header();
?>

<style>
.advertisement-single-container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0px 20px;
    font-family: 'Inter', 'Poppins', sans-serif;
    color: #eee;
}

.advertisement-single-content {
    background: #1a1a1a;
    padding: 40px;
    border-radius: 16px;
    margin-top: 50px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    border: 1px solid #333;
}

.advertisement-single-header {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #333;
}

.advertisement-single-title {
    font-size: 42px;
    font-weight: 700;
    color: #fff;
    margin: 0 0 15px 0;
    line-height: 1.2;
    letter-spacing: -0.5px;
}

.advertisement-single-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 25px 0;
}

.category-tag {
    display: inline-block;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 20px;
    background-color: #b2122d;
    color: #fff;
    text-transform: capitalize;
}

/* New Post Tag for Single Page */
.new-post-tag-single {
    display: inline-block;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 20px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-right: 8px;
    animation: pulse-new-single 2s infinite;
}

@keyframes pulse-new-single {
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

.advertisement-single-image {
    margin: 30px 0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.advertisement-single-image img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: cover;
    transition: transform 0.3s ease;
    height: 500px;
}

.advertisement-single-image:hover img {
    transform: scale(1.02);
}

.advertisement-single-content-wrapper {
    font-size: 16px;
    line-height: 1.7;
    color: #ccc;
}

.advertisement-single-content-wrapper p {
    margin-bottom: 20px;
}

.advertisement-single-meta {
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #333;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.interests-count {
    display: none;
}

.advertisement-date {
    font-size: 15px;
    color: #aaa;
    font-weight: 500;
}

.advertisement-meta-info {
    display: flex;
    gap: 20px;
    align-items: center;
}

.advertisement-single-footer {
    margin-top: 30px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.back-to-ads-btn {
    display: inline-block;
    padding: 12px 28px;
    background-color: transparent;
    color: #b2122d;
    border: 2px solid #b2122d;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.back-to-ads-btn:hover {
    background-color: #b2122d;
    color: #fff;
}

/* Express Interest Section - Enhanced Design */
.express-interest-section {
    background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
    border-radius: 16px;
    padding: 30px;
    margin-top: 40px;
    border: 1px solid #3a3a3a;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.express-interest-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.express-interest-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #b2122d 0%, #d41835 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.express-interest-icon svg {
    width: 26px;
    height: 26px;
    color: #fff;
}

.express-interest-title {
    font-size: 20px;
    font-weight: 600;
    color: #fff;
    margin: 0;
}

.express-interest-subtitle {
    font-size: 14px;
    color: #999;
    margin: 4px 0 0 0;
}

.express-interest-wrapper {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.express-interest-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 32px;
    background-color: #b2122d;
    color: #fff;
    border: 2px solid #b2122d;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.express-interest-btn:hover {
    background-color: transparent;
    color: #b2122d;
    transform: translateY(-2px);
}

.express-interest-btn:active {
    transform: translateY(0);
}

.express-interest-btn:disabled {
    background-color: transparent;
    border-color: #555;
    color: #555;
    cursor: not-allowed;
    opacity: 0.8;
    transform: none;
}

.express-interest-btn.interested {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}

.express-interest-btn.interested:hover {
    background-color: transparent;
    color: #28a745;
}

.express-interest-btn svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.interests-count {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #aaa;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 20px;
}

.interests-count-icon {
    width: 18px;
    height: 18px;
    color: #28a745;
}

.express-interest-message {
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    margin-top: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.express-interest-message.success {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.express-interest-message.error {
    background: rgba(178, 18, 45, 0.15);
    color: #e74c3c;
    border: 1px solid rgba(178, 18, 45, 0.3);
}

.express-interest-message svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

/* Login prompt styling */
.login-prompt {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px dashed #444;
}

.login-prompt-icon {
    width: 40px;
    height: 40px;
    background: rgba(178, 18, 45, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.login-prompt-icon svg {
    width: 22px;
    height: 22px;
    color: #b2122d;
}

.login-prompt-text {
    flex: 1;
}

.login-prompt-text p {
    margin: 0;
    color: #aaa;
    font-size: 14px;
}

/* Payment prompt styling */
.payment-prompt {
    padding: 24px;
    background: linear-gradient(135deg, rgba(178, 18, 45, 0.1) 0%, rgba(212, 24, 53, 0.05) 100%);
    border-radius: 12px;
    border: 1px solid rgba(178, 18, 45, 0.3);
}

.payment-prompt-content {
    text-align: center;
}

.payment-prompt-content p {
    margin: 0 0 20px 0;
    color: #ccc;
    font-size: 15px;
    line-height: 1.6;
}

.payment-shortcode {
    margin: 20px 0;
    display: flex;
    justify-content: center;
}

.payment-note {
    font-size: 13px !important;
    color: #888 !important;
    margin-top: 15px !important;
}

/* Override express interest icon for payment section */
.express-interest-section.payment-section .express-interest-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.payment-shortcode button,
.payment-shortcode input[type="submit"],
.payment-shortcode .stripe-button-el {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 32px;
    background-color: #b2122d !important;
    background-image: none !important;
    color: #fff !important;
    border: 2px solid #b2122d !important;
    border-radius: 8px !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
    width: auto !important;
    min-width: 200px;
    box-shadow: none !important;
}

.payment-shortcode button:hover,
.payment-shortcode input[type="submit"]:hover,
.payment-shortcode .stripe-button-el:hover {
    background-color: transparent !important;
    color: #b2122d !important;
    transform: translateY(-2px) !important;
}

@media (max-width: 768px) {
    .express-interest-section {
        padding: 24px 20px;
    }
    
    .express-interest-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .express-interest-wrapper {
        flex-direction: column;
        align-items: stretch;
    }
    
    .express-interest-btn {
        justify-content: center;
        width: 100%;
    }
    
    .interests-count {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .express-interest-section {
        padding: 20px 16px;
    }
    
    .express-interest-title {
        font-size: 18px;
    }
    
    .express-interest-btn {
        padding: 14px 28px;
        font-size: 15px;
    }
}

@media (max-width: 768px) {
    .advertisement-single-container {
        padding: 30px 15px;
    }
    
    .advertisement-single-content {
        padding: 30px 20px;
        margin-top: 30px;
    }
    
    .advertisement-single-title {
        font-size: 32px;
    }
    
    .advertisement-single-meta {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .advertisement-meta-info {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .advertisement-single-title {
        font-size: 26px;
    }
    
    .advertisement-single-content {
        padding: 20px 15px;
    }
}</style>

<main class="site-main advertisement-single-container" role="main">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('advertisement-single-content'); ?>>
            <header class="advertisement-single-header">
                <h1 class="advertisement-single-title"><?php the_title(); ?></h1>
                <?php
                // Display categories
                $categories = get_the_terms(get_the_ID(), 'advertisement_category');
                if ($categories && !is_wp_error($categories)) {
                    echo '<div class="advertisement-single-categories">';
                    
                    // Check if ad is within 2 days (new post)
                    $post_date = get_post_time('U', false, get_the_ID());
                    $current_time = current_time('timestamp');
                    $two_days_in_seconds = 2 * 24 * 60 * 60; // 2 days in seconds
                    $is_new_post = ($current_time - $post_date) <= $two_days_in_seconds;
                    
                    if ($is_new_post) {
                        echo '<span class="new-post-tag-single">New Post</span>';
                    }
                    
                    foreach ($categories as $category) {
                        echo '<span class="category-tag">' . esc_html($category->name) . '</span> ';
                    }
                    echo '</div>';
                }
                ?>
            </header>
            
            <div class="entry-content advertisement-single-content-wrapper">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="advertisement-single-image">
                        <?php the_post_thumbnail('large', array('class' => 'advertisement-image-responsive')); ?>
                    </div>
                <?php else : ?>
                    <div class="advertisement-single-image">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/ad-placeholder.png" alt="<?php echo esc_attr(get_the_title()); ?>" class="advertisement-image-responsive" />
                    </div>
                <?php endif; ?>
                
                <?php the_content(); ?>
            </div>
            
            <footer class="entry-footer advertisement-single-footer">
                <div class="advertisement-meta advertisement-meta-info">
                    <span class="advertisement-date">Published on <?php echo get_the_date(); ?></span>
                    <a href="<?php echo home_url('/advertisement'); ?>" class="back-to-ads-btn">← Back to Advertisements</a>
                </div>
                
                <?php
                // Include access control functions and define constants
                if (!function_exists('has_membership_plan')) {
                    require_once get_stylesheet_directory() . '/includes/access-control.php';
                }
                
                // Define constants if not already defined (in case access-control.php wasn't loaded)
                if (!defined('ADVERTISEMENT_PLAN_LEVEL')) {
                    define('ADVERTISEMENT_PLAN_LEVEL', 5);  // Level 5 is for advertisement access
                }
                if (!defined('PORTFOLIO_PLAN_LEVEL')) {
                    define('PORTFOLIO_PLAN_LEVEL', 2);  // Level 2 is for portfolio access
                }
                if (!defined('PORTFOLIO_PLAN_LEVEL_UPGRADE')) {
                    define('PORTFOLIO_PLAN_LEVEL_UPGRADE', 3);  // Level 3 is also for portfolio access
                }
                
                // Ensure user's plan is tracked (for backward compatibility)
                if (function_exists('ensure_user_plan_tracking')) {
                    ensure_user_plan_tracking();
                }
                
                // Check if user is logged in
                $is_logged_in = is_user_logged_in();
                $current_user = $is_logged_in ? wp_get_current_user() : null;
                $advertisement_id = get_the_ID();
                
                // Check if current user is the author
                $is_author = $is_logged_in && get_post_field('post_author', $advertisement_id) == $current_user->ID;
                
                // Check if user has any paid membership (portfolio or advertisement plan), explicitly excluding FREE_PLAN_LEVEL (4)
                $is_paid_member = false;
                if ($is_logged_in) {
                    $user_id = get_current_user_id();
                    $user_plans = get_user_meta($user_id, 'membership_plans', true);
                    if (!is_array($user_plans)) $user_plans = array();
                    
                    $current_level = class_exists('SwpmMemberUtils') && SwpmMemberUtils::is_member_logged_in() ? SwpmMemberUtils::get_logged_in_members_level() : 0;
                    
                    $paid_levels = [ADVERTISEMENT_PLAN_LEVEL, PORTFOLIO_PLAN_LEVEL, PORTFOLIO_PLAN_LEVEL_UPGRADE];
                    
                    if (in_array($current_level, $paid_levels)) {
                        $is_paid_member = true;
                    } else {
                        foreach ($paid_levels as $level) {
                            if (in_array($level, $user_plans)) {
                                $is_paid_member = true;
                                break;
                            }
                        }
                    }
                }
                
                // Check if user has already expressed interest
                $interests = get_post_meta($advertisement_id, '_advertisement_interests', true);
                $has_expressed_interest = false;
                if ($is_logged_in && is_array($interests) && in_array($current_user->ID, $interests)) {
                    $has_expressed_interest = true;
                }
                
                // Get interests count
                $interests_count = is_array($interests) ? count($interests) : 0;
                
                // Build success URL for payment
                $success_url = get_permalink() . '?payment=success&advertisement_id=' . $advertisement_id;
                
                if ($is_logged_in && !$is_author && $is_paid_member) :
                ?>
                <div class="express-interest-section">
                    <div class="express-interest-header">
                        <div class="express-interest-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="express-interest-title">
                                <?php echo $has_expressed_interest ? "You're Interested!" : 'Interested in this opportunity?'; ?>
                            </h3>
                            <p class="express-interest-subtitle">
                                <?php echo $has_expressed_interest ? 'Your interest has been recorded' : 'Let the poster know you\'re interested'; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="express-interest-wrapper">
                        <button 
                            type="button" 
                            class="express-interest-btn <?php echo $has_expressed_interest ? 'interested' : ''; ?>" 
                            data-advertisement-id="<?php echo esc_attr($advertisement_id); ?>"
                            <?php echo $has_expressed_interest ? 'disabled' : ''; ?>
                        >
                            <?php if ($has_expressed_interest) : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Interest Expressed
                            <?php else : ?>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                                Express Interest
                            <?php endif; ?>
                        </button>

                    </div>
                </div>
                <?php elseif ($is_logged_in && !$is_author && !$is_paid_member) : ?>
                <div class="express-interest-section">
                    <div class="express-interest-header">
                        <div class="express-interest-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="express-interest-title">Interested in this opportunity?</h3>
                            <p class="express-interest-subtitle">Pay to express your interest</p>
                        </div>
                    </div>
                    
                    <div class="payment-prompt">
                        <div class="payment-prompt-content">
                            <p>Express your interest in this advertisement with a one-time payment.</p>
                            <div class="payment-shortcode">
                                <?php echo do_shortcode('[wp_stripe_checkout_session name="show-interest-fee" price="5.00" button_text="Pay $5.00" success_url="' . $success_url . '"]'); ?>
                            </div>
                            <p class="payment-note">One-time payment • Instant access</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </footer>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>