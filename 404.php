<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>

<main id="content" class="site-main error-404-section">
	<!-- Ambient Background Glow Spheres -->
	<div class="error-404-bg-glow glow-1"></div>
	<div class="error-404-bg-glow glow-2"></div>
	<div class="error-404-bg-glow glow-3"></div>
	
	<div class="error-404-container">
		<div class="error-404-visual">
			<div class="reel-animation-box">
				<!-- Dual Animated Film Reel SVG -->
				<svg class="film-reel-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none">
					<!-- Outer Spinning Dash Border -->
					<circle cx="50" cy="50" r="44" stroke="url(#reelGradient1)" stroke-width="3" stroke-dasharray="8 6" class="spinning-reel-outer" />
					<!-- Inner Counter-Spinning Border -->
					<circle cx="50" cy="50" r="36" stroke="url(#reelGradient2)" stroke-width="2" stroke-dasharray="14 8" class="spinning-reel-inner" />
					
					<!-- Reel Core -->
					<circle cx="50" cy="50" r="28" fill="#13131c" stroke="#df1d3d" stroke-width="2.5" class="reel-core" />
					<circle cx="50" cy="50" r="7" fill="#e6b749" class="reel-center-dot" />
					
					<!-- Reel Spokes / Holes -->
					<circle cx="50" cy="30" r="4.5" fill="#222230" stroke="#df1d3d" stroke-width="1.5" />
					<circle cx="50" cy="70" r="4.5" fill="#222230" stroke="#df1d3d" stroke-width="1.5" />
					<circle cx="30" cy="50" r="4.5" fill="#222230" stroke="#df1d3d" stroke-width="1.5" />
					<circle cx="70" cy="50" r="4.5" fill="#222230" stroke="#df1d3d" stroke-width="1.5" />
					
					<defs>
						<linearGradient id="reelGradient1" x1="0%" y1="0%" x2="100%" y2="100%">
							<stop offset="0%" stop-color="#df1d3d" />
							<stop offset="50%" stop-color="#e6b749" />
							<stop offset="100%" stop-color="#df1d3d" />
						</linearGradient>
						<linearGradient id="reelGradient2" x1="100%" y1="0%" x2="0%" y2="100%">
							<stop offset="0%" stop-color="#e6b749" />
							<stop offset="100%" stop-color="#df1d3d" />
						</linearGradient>
					</defs>
				</svg>
				<span class="error-code">404</span>
			</div>
		</div>

		<div class="error-404-content">
			<div class="error-badge-wrapper">
				<span class="error-badge"><i class="fas fa-video-slash"></i> SCENE NOT FOUND • OUT OF FRAME</span>
			</div>
			
			<h1 class="error-title"><?php esc_html_e( 'Lost in the Edit Room', 'hello-elementor-child' ); ?></h1>
			
			<p class="error-description">
				<?php esc_html_e( 'This take didn\'t make the final cut. The page or scene you are looking for has been moved, renamed, or never existed in this project.', 'hello-elementor-child' ); ?>
			</p>

			<div class="error-search-box">
				<form role="search" method="get" class="error-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<div class="search-input-wrapper">
						<svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"></circle>
							<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
						</svg>
						<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search talent, projects, or categories...', 'hello-elementor-child' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
						<button type="submit" class="search-submit">
							<span><?php esc_html_e( 'Search', 'hello-elementor-child' ); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
						</button>
					</div>
				</form>
			</div>

			<div class="error-actions">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-404 btn-404-primary">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
					<span><?php esc_html_e( 'Back to Home', 'hello-elementor-child' ); ?></span>
				</a>
				<a href="<?php echo esc_url( home_url( '/talent/' ) ); ?>" class="btn-404 btn-404-secondary">
					<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
					<span><?php esc_html_e( 'Browse Talent', 'hello-elementor-child' ); ?></span>
				</a>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
