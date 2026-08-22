<?php
/**
 * Custom High-Performance Hero Video Slider for The VelvetReel
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the Hero Video Slider HTML
 */
function velvet_hero_slider_shortcode() {
	$slides = [
		[
			'video'       => home_url( '/wp-content/uploads/2025/10/velvetReel.mp4' ),
			'subtitle'    => 'Welcome To The VelvetReel',
			'title'       => 'DISCOVER TOMORROW’S ICONS, TODAY',
			'description' => 'We connect visionary projects with extraordinary talents in fashion and film.',
			'button_text' => 'EXPLORE TALENTS',
			'button_url'  => home_url( '/talent/' ),
		],
		[
			'video'       => home_url( '/wp-content/uploads/2025/10/9512045-hd_1366_720_25fps.mp4' ),
			'subtitle'    => 'Welcome To The Velvetreel',
			'title'       => 'YOUR STORY DESERVES THE PERFECT CAST',
			'description' => 'From fresh faces to seasoned artists, we bring creative visions to life with the right people.',
			'button_text' => 'CREATE PORTFOLIO',
			'button_url'  => home_url( '/sign-up/' ),
		],
		[
			'video'       => home_url( '/wp-content/uploads/2025/10/5098913-hd_1280_720_60fps.mp4' ),
			'subtitle'    => 'Welcome To The Velvetreel',
			'title'       => 'WHERE PASSION MEETS OPPORTUNITY',
			'description' => 'Join a network that values creativity, authenticity, and breakthrough performances.',
			'button_text' => 'REGISTER NOW',
			'button_url'  => home_url( '/sign-up/' ),
		],
	];

	ob_start();
	?>
	<section id="velvet-hero-slider" class="velvet-hero-slider" aria-label="Hero Video Slider">
		<div class="velvet-slider-wrapper">
			<?php foreach ( $slides as $index => $slide ) : ?>
				<div class="velvet-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo esc_attr( $index ); ?>">
					<!-- Video Background -->
					<div class="velvet-video-container">
						<video class="velvet-slide-video" autoplay loop muted playsinline preload="auto">
							<source src="<?php echo esc_url( $slide['video'] ); ?>" type="video/mp4">
						</video>
						<div class="velvet-video-overlay"></div>
					</div>

					<!-- Content Container -->
					<div class="velvet-slide-content">
						<div class="velvet-slide-inner">
							<p class="velvet-slide-subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></p>
							<h1 class="velvet-slide-title"><?php echo esc_html( $slide['title'] ); ?></h1>
							<p class="velvet-slide-description"><?php echo esc_html( $slide['description'] ); ?></p>
							<div class="velvet-slide-button-wrap">
								<a href="<?php echo esc_url( $slide['button_url'] ); ?>" class="velvet-slide-btn">
									<?php echo esc_html( $slide['button_text'] ); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Navigation Arrows -->
		<button class="velvet-slider-arrow velvet-arrow-prev" aria-label="Previous Slide">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="24" height="24" fill="currentColor"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L74.7 256 246.6 84.7c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/></svg>
		</button>
		<button class="velvet-slider-arrow velvet-arrow-next" aria-label="Next Slide">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="24" height="24" fill="currentColor"><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L245.3 256 73.4 84.7c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg>
		</button>

		<!-- Pagination Dots -->
		<div class="velvet-slider-dots">
			<?php foreach ( $slides as $index => $slide ) : ?>
				<button class="velvet-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide-target="<?php echo esc_attr( $index ); ?>" aria-label="Go to slide <?php echo esc_attr( $index + 1 ); ?>"></button>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'velvet_hero_slider', 'velvet_hero_slider_shortcode' );
