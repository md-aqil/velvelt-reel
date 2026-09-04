<?php
/**
 * Upgraded Custom Glassmorphism Header Template Part
 * Features sticky backdrop blur, desktop active indicators, user profile capsule, and mobile drawer CTA.
 *
 * @package HelloElementorChild
 */

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

$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();
$user_first_name = '';
if ( $is_logged_in && $current_user ) {
	$user_first_name = ! empty( $current_user->first_name ) 
		? $current_user->first_name 
		: ( ! empty( $current_user->display_name ) ? explode( ' ', trim( $current_user->display_name ) )[0] : $current_user->user_login );
}
?>

<header id="site-header" class="site-header custom-theme-header header-full-width">
	<div class="header-inner">
		<!-- Site Branding / Logo -->
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<div class="site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
						<span class="brand-text-bold">THE VELVET</span><span class="brand-text-red">REEL</span>
					</a>
				</div>
			<?php endif; ?>
		</div>

		<!-- Desktop Navigation Menu -->
		<?php if ( $header_nav_menu ) : ?>
			<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Main menu', 'hello-elementor-child' ); ?>">
				<?php echo $header_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</nav>
		<?php endif; ?>

		<!-- Header Actions / User Capsule -->
		<div class="velvet-header-actions">
			<?php if ( $is_logged_in ) : ?>
				<div class="user-menu user-profile-capsule">
					<a href="<?php echo esc_url( home_url( '/membership-login/' ) ); ?>" class="user-profile-link" title="<?php echo esc_attr( $current_user->display_name ); ?>">
						<span class="user-avatar-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="13" height="13" fill="currentColor"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.3 304 0 383.3 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.3 368.7 304 269.7 304H178.3z"/></svg>
						</span>
						<span class="user-name-text"><?php echo esc_html( $user_first_name ); ?></span>
						<svg class="dropdown-chevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="9" height="9" fill="currentColor"><path d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"/></svg>
					</a>

					<!-- Hover Profile Dropdown -->
					<div class="velvet-profile-dropdown">
						<a href="<?php echo esc_url( home_url( '/membership-login/' ) ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="13" height="13" fill="currentColor"><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/></svg>
							<span>Edit Profile</span>
						</a>
						<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="logout-dropdown-item">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="13" height="13" fill="currentColor"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
							<span>Logout</span>
						</a>
					</div>
				</div>
			<?php else : ?>
				<div class="user-menu velvet-header-cta-group">
					<a href="<?php echo esc_url( home_url( '/talent/' ) ); ?>" class="velvet-header-btn velvet-btn-cta">
						+ CREATE PORTFOLIO
					</a>
				</div>
			<?php endif; ?>

			<!-- Mobile Hamburger Toggle Button -->
			<button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="primary-menu">
				<span class="hamburger-bar"></span>
				<span class="hamburger-bar"></span>
				<span class="hamburger-bar"></span>
			</button>
		</div>

		<!-- Mobile Menu Overlay -->
		<div id="mobile-menu-overlay" class="mobile-menu-overlay"></div>
	</div>
</header>
