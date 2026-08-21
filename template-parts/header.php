<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$header_nav_menu = wp_nav_menu( [
	'theme_location' => 'menu-1',
	'fallback_cb'    => false,
	'container'      => false,
	'menu_id'        => 'primary-menu',
	'echo'           => false,
] );
?>

	<header id="site-header" class="site-header custom-theme-header header-full-width">
	<div class="header-inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<div class="site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<?php bloginfo( 'name' ); ?>
					</a>
				</div>
				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<p class="site-description"><?php bloginfo( 'description' ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="primary-menu">
			<span class="hamburger-bar"></span>
			<span class="hamburger-bar"></span>
			<span class="hamburger-bar"></span>
		</button>

		<?php if ( $header_nav_menu ) : ?>
			<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Main menu', 'hello-elementor-child' ); ?>">
				<?php echo $header_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</nav>
		<?php endif; ?>

		<div class="user-menu">
			<?php echo do_shortcode('[login_status]'); ?>
		</div>
	</div>
</header>
