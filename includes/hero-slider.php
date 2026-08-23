<?php
/**
 * Custom High-Performance Hero Video Slider for The VelvetReel
 * Includes 3D Rotating Title, Dual Mindful CTAs, Floating Trust Badges, and 100dvh Mobile UX.
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
			'video'          => home_url( '/wp-content/uploads/2025/10/velvetReel.mp4' ),
			'subtitle'       => 'Welcome To The VelvetReel',
			'has_rotating'   => true,
			'static_title'   => 'DISCOVER TOMORROW’S',
			'rotating_words' => [ 'ACTORS', 'MODELS', 'FILMMAKERS', 'ICONS' ],
			'description'    => 'We connect visionary projects with extraordinary talents in fashion and film.',
			'primary_btn'    => [
				'text' => 'EXPLORE TALENTS',
				'url'  => home_url( '/talent/' ),
			],
			'secondary_btn'  => [
				'text' => 'JOIN AS TALENT',
				'url'  => home_url( '/sign-up/' ),
			],
		],
		[
			'video'          => home_url( '/wp-content/uploads/2025/10/9512045-hd_1366_720_25fps.mp4' ),
			'subtitle'       => 'Welcome To The Velvetreel',
			'has_rotating'   => false,
			'title'          => 'YOUR STORY DESERVES THE PERFECT CAST',
			'description'    => 'From fresh faces to seasoned artists, we bring creative visions to life with the right people.',
			'primary_btn'    => [
				'text' => 'CREATE PORTFOLIO',
				'url'  => home_url( '/talent/' ),
			],
			'secondary_btn'  => [
				'text' => 'EXPLORE TALENTS',
				'url'  => home_url( '/talent/' ),
			],
		],
		[
			'video'          => home_url( '/wp-content/uploads/2025/10/5098913-hd_1280_720_60fps.mp4' ),
			'subtitle'       => 'Welcome To The Velvetreel',
			'has_rotating'   => false,
			'title'          => 'WHERE PASSION MEETS OPPORTUNITY',
			'description'    => 'Join a network that values creativity, authenticity, and breakthrough performances.',
			'primary_btn'    => [
				'text' => 'REGISTER NOW',
				'url'  => home_url( '/sign-up/' ),
			],
			'secondary_btn'  => null,
		],
	];

	ob_start();
	?>
	<section id="velvet-hero-slider" class="velvet-hero-slider" aria-label="Hero Video Slider">
		<div class="velvet-slider-wrapper">
			<?php foreach ( $slides as $index => $slide ) : ?>
				<div class="velvet-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo esc_attr( $index ); ?>">
					<!-- Video Background & Multi-layer Overlay -->
					<div class="velvet-video-container">
						<video class="velvet-slide-video" autoplay loop muted playsinline preload="auto">
							<source src="<?php echo esc_url( $slide['video'] ); ?>" type="video/mp4">
						</video>
						<div class="velvet-video-overlay"></div>
					</div>

					<!-- Hero Slide Content -->
					<div class="velvet-slide-content">
						<div class="velvet-slide-inner">
							<p class="velvet-slide-subtitle"><?php echo esc_html( $slide['subtitle'] ); ?></p>
							
							<?php if ( ! empty( $slide['has_rotating'] ) ) : ?>
								<h1 class="velvet-slide-title velvet-has-rotating">
									<span class="velvet-static-part"><?php echo esc_html( $slide['static_title'] ); ?></span>
									<span class="velvet-hero-word-viewport">
										<?php foreach ( $slide['rotating_words'] as $w_idx => $word ) : ?>
											<span class="velvet-hero-word <?php echo $w_idx === 0 ? 'is-active' : ''; ?>">
												<?php echo esc_html( $word ); ?>
											</span>
										<?php endforeach; ?>
									</span>
								</h1>
							<?php else : ?>
								<h1 class="velvet-slide-title"><?php echo esc_html( $slide['title'] ); ?></h1>
							<?php endif; ?>

							<p class="velvet-slide-description"><?php echo esc_html( $slide['description'] ); ?></p>
							
							<!-- Mindful Dual CTA Buttons -->
							<div class="velvet-slide-button-wrap">
								<a href="<?php echo esc_url( $slide['primary_btn']['url'] ); ?>" class="velvet-slide-btn velvet-btn-primary">
									<?php echo esc_html( $slide['primary_btn']['text'] ); ?>
								</a>

								<?php if ( ! empty( $slide['secondary_btn'] ) ) : ?>
									<a href="<?php echo esc_url( $slide['secondary_btn']['url'] ); ?>" class="velvet-slide-btn velvet-btn-secondary">
										<?php echo esc_html( $slide['secondary_btn']['text'] ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Floating Social Proof Trust Badges -->
		<div class="velvet-hero-trust-bar">
			<div class="velvet-trust-badge">
				<span class="badge-icon">⭐</span> 500+ Verified Portfolios
			</div>
			<div class="velvet-trust-badge">
				<span class="badge-icon">🎬</span> Top Production Houses & Brands
			</div>
		</div>

		<!-- Navigation Controls -->
		<button class="velvet-slider-arrow velvet-arrow-prev" aria-label="Previous Slide">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="22" height="22" fill="currentColor"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L74.7 256 246.6 84.7c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/></svg>
		</button>
		<button class="velvet-slider-arrow velvet-arrow-next" aria-label="Next Slide">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="22" height="22" fill="currentColor"><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L245.3 256 73.4 84.7c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg>
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
