<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$is_logged_in = is_user_logged_in();

if ( ! $is_logged_in && isset($_GET['login']) && $_GET['login'] === 'success' ) {
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
<?php
get_footer();
return;
endif;

$current_user_id = get_current_user_id();
global $wp_query;
$original_query = $wp_query;

$args = array(
    'post_type' => 'talent',
    'author' => $current_user_id,
    'posts_per_page' => 1,
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
            esc_html__( 'My Portfolios', 'hello-elementor-child' ),
            $current_user->display_name
        );
        ?>
    </h1>
    <p class="page-description">
        <?php esc_html_e( 'Create your FREE Portfolio', 'hello-elementor-child' ); ?>
    </p>

    <?php
    if (!function_exists('user_has_talent_profile')) {
        require_once get_stylesheet_directory() . '/includes/meta-fields.php';
    }
    if (!function_exists('has_membership_plan')) {
        require_once get_stylesheet_directory() . '/includes/access-control.php';
    }
    ensure_user_plan_tracking();
    migrate_user_current_level_to_plans();

    if (user_has_talent_profile(null, true)) :
        $user_talent_query = new WP_Query(array(
            'post_type' => 'talent',
            'author' => get_current_user_id(),
            'posts_per_page' => 1,
            'post_status' => array('publish', 'draft', 'pending')
        ));
        if ($user_talent_query->have_posts()) :
            $first_talent_post = $user_talent_query->posts[0];

            if ($first_talent_post->post_status === 'pending') :
                $has_paid_approval = get_post_meta($first_talent_post->ID, '_talent_has_paid_approval', true);

                if ($has_paid_approval) :
                    $payment_date = get_post_meta($first_talent_post->ID, '_talent_approval_payment_date', true);
                    $payment_amount = get_post_meta($first_talent_post->ID, '_talent_approval_payment_amount', true);
                    $payment_info = get_post_meta($first_talent_post->ID, '_talent_approval_payment_info', true);
                    ?>

                    <div class="status-card pending">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <div class="card-content">
                            <h3>Admin Approval Pending</h3>
                            <p>Relax, your profile is completed and under admin review.</p>
                            <div class="approval-paid-success">
                                <h3><i class="fas fa-check-circle"></i>Approval Payment Confirmed!</h3>
                                <p>Great news! You've successfully paid for faster portfolio approval. Our team will prioritize reviewing and publishing your portfolio shortly.</p>
                                <div class="payment-details-block">
                                    <strong>Payment Information:</strong>
                                    <div class="payment-grid">
                                        <?php if ($payment_amount): ?>
                                            <span class="amount"><i class="fas fa-dollar-sign"></i>Amount: $<?php echo esc_html($payment_amount); ?></span>
                                        <?php endif; ?>
                                        <?php if ($payment_date): ?>
                                            <span class="date"><i class="fas fa-calendar-alt"></i><?php echo esc_html(date('M j, Y', strtotime($payment_date))); ?></span>
                                        <?php endif; ?>
                                        <?php if ($payment_info): ?>
                                            <span class="method"><i class="fas fa-credit-card"></i><?php echo esc_html($payment_info); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                else :
                    $stripe_button = do_shortcode('[wp_stripe_checkout_session name="Portfolio Approval" price="5.00" button_text="Pay Now" success_url="https://www.thevelvetreel.com/talent/?session_id={CHECKOUT_SESSION_ID}"]');
                    ?>
                    <div class="status-card pay">
                        <div class="card-icon pay">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <div class="card-content">
                            <h3>Congratulations, you're just one step away!</h3>
                            <p>Finish the one time payment verification fee - <strong>$5</strong> to lock in your portfolio submission by admin.</p>
                            <div class="stripe-payment-container">
                                <?php echo $stripe_button; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                endif;

            elseif ($first_talent_post->post_status === 'draft') :
                $has_paid_approval = get_post_meta($first_talent_post->ID, '_talent_has_paid_approval', true);
                ?>
                <div class="status-card draft">
                    <div class="card-icon draft">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </div>
                    <div class="card-content">
                        <h3>Portfolio Incomplete</h3>
                        <p>Your portfolio is partially complete, finish it to publish.</p>
                        <?php if ($has_paid_approval): ?>
                            <?php
                            $payment_date = get_post_meta($first_talent_post->ID, '_talent_approval_payment_date', true);
                            $payment_amount = get_post_meta($first_talent_post->ID, '_talent_approval_payment_amount', true);
                            $payment_info = get_post_meta($first_talent_post->ID, '_talent_approval_payment_info', true);
                            ?>
                            <div class="approval-paid-success draft">
                                <h3>Approval Payment Confirmed!</h3>
                                <p>Great news! You've successfully paid for faster portfolio approval. Our team will prioritize reviewing and publishing your portfolio shortly.</p>
                                <div class="payment-details-block">
                                    <strong>Payment Information:</strong>
                                    <div class="payment-grid">
                                        <?php if ($payment_amount): ?>
                                            <span class="amount"><i class="fas fa-dollar-sign"></i>$<?php echo esc_html($payment_amount); ?></span>
                                        <?php endif; ?>
                                        <?php if ($payment_date): ?>
                                            <span class="date"><i class="fas fa-calendar-alt"></i><?php echo esc_html(date('M j, Y g:i A', strtotime($payment_date))); ?></span>
                                        <?php endif; ?>
                                        <?php if ($payment_info): ?>
                                            <span class="method"><i class="fas fa-credit-card"></i><?php echo esc_html($payment_info); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php
                            $stripe_button = do_shortcode('[wp_stripe_checkout_session name="Portfolio Approval" price="5.00" button_text="Pay Now" success_url="https://www.thevelvetreel.com/talent/?session_id={CHECKOUT_SESSION_ID}"]');
                            if (!empty($stripe_button)):
                                ?>
                                <p class="pay-label">Need faster approval? Pay with this plan:</p>
                                <div class="stripe-payment-container">
                                    <?php echo $stripe_button; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            endif;

            $edit_url = add_query_arg('talent_id', $first_talent_post->ID, home_url('/edit-talent-profile/'));
            echo '<a href="' . esc_url($edit_url) . '" class="btn-create-portfolio">Edit Portfolio</a>';
        endif;
    else :
        echo '<a href="' . esc_url(home_url('/talent-registration')) . '" class="btn-create-portfolio">Create Your Portfolio</a>';
    endif;
    ?>
</header>

<div class="page-content">
    <div class="talent-archive-grid">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php
                $role = get_post_meta(get_the_ID(), '_talent_role', true);
                $project_views = (int) get_post_meta(get_the_ID(), '_talent_project_views', true);
                $saved_to_lists = (int) get_post_meta(get_the_ID(), '_talent_saved_to_lists', true);
                $profile_clicks = (int) get_post_meta(get_the_ID(), '_talent_profile_clicks', true);
                ?>
                <div class="talent-card">
                    <div class="talent-card-header">
                        <?php if (get_post_status() === 'publish'): ?>
                            <span class="status-badge published"><span class="status-dot"></span>Published</span>
                        <?php else: ?>
                            <?php
                            $status_class = get_post_status() === 'pending' ? 'pending' : 'draft';
                            ?>
                            <span class="status-badge <?php echo esc_attr($status_class); ?>"><span class="status-dot"></span><?php echo esc_html(ucfirst(get_post_status())); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="talent-card-body">
                        <h3 class="talent-role"><?php echo esc_html(ucwords(str_replace('-', ' ', $role))); ?></h3>
                        <p class="talent-name"><?php the_title(); ?></p>
                    </div>
                    <div class="talent-card-footer">
                        <div class="talent-stats">
                            <div class="stat-item">
                                <span class="stat-value"><?php echo number_format_i18n($project_views); ?></span>
                                <span class="stat-label">Project Views</span>
                            </div>
                        </div>
                        <div class="talent-card-actions">
                            <?php
                            $edit_page_url = home_url('/edit-talent-profile/');
                            $edit_url_with_id = add_query_arg('talent_id', get_the_ID(), $edit_page_url);
                            ?>
                            <a href="<?php echo esc_url($edit_url_with_id); ?>" class="view-button edit-button">Edit</a>
                            <?php if (get_post_status() === 'publish'): ?>
                                <a href="<?php the_permalink(); ?>" class="view-button">View</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            <?php
            $wp_query = $original_query;
            wp_reset_postdata();
            ?>
        <?php else: ?>
            <div class="no-talent-profiles">
                <p><?php esc_html_e('No talent profiles found.', 'hello-elementor-child'); ?></p>
                <a href="<?php echo esc_url(home_url('/talent-registration')); ?>" class="btn-create-portfolio">Create Your Portfolio</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
?>