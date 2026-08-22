<?php
/**
 * Custom Premium Testimonials Slider Component for The VelvetReel
 * Fetches dynamic Testimonial CPT items managed from WP-Admin.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode to display custom Testimonials Slider
 * Usage: [velvet_testimonials_slider]
 */
function velvet_testimonials_slider_shortcode( $atts ) {
	$atts = shortcode_atts( [
		'title' => 'WHAT OUR TALENTS SAY',
	], $atts, 'velvet_testimonials_slider' );

	// Query published testimonial CPT items
	$args = [
		'post_type'      => 'testimonial',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	];

	$cpt_query    = new WP_Query( $args );
	$testimonials = [];

	if ( $cpt_query->have_posts() ) {
		while ( $cpt_query->have_posts() ) {
			$cpt_query->the_post();
			$post_id    = get_the_ID();
			$avatar_url = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
			if ( ! $avatar_url ) {
				$avatar_url = home_url( '/wp-content/uploads/2025/11/856f47707399f5ed0e22af6918c4ec16-scaled.jpg' );
			}

			$testimonials[] = [
				'quote'  => get_the_content(),
				'name'   => get_the_title(),
				'role'   => get_post_meta( $post_id, '_testimonial_role', true ),
				'rating' => intval( get_post_meta( $post_id, '_testimonial_rating', true ) ?: 5 ),
				'avatar' => $avatar_url,
			];
		}
		wp_reset_postdata();
	}

	// Fallback sample testimonials if no CPT posts added yet
	if ( empty( $testimonials ) ) {
		$testimonials = [
			[
				'quote'  => 'VelvetReel helped me turn my scattered social profiles into one clean portfolio, and I landed my first paid fashion campaign within two weeks.',
				'name'   => 'Christina',
				'role'   => 'Dancer',
				'rating' => 5,
				'avatar' => home_url( '/wp-content/uploads/2025/11/856f47707399f5ed0e22af6918c4ec16-scaled.jpg' ),
			],
			[
				'quote'  => 'I love how simple it is to update my reels and get genuine casting calls—no noise, just real opportunities from top brands and producers.',
				'name'   => 'Michela',
				'role'   => 'Actor',
				'rating' => 5,
				'avatar' => home_url( '/wp-content/uploads/2025/11/856f47707399f5ed0e22af6918c4ec16-scaled.jpg' ),
			],
			[
				'quote'  => 'The VelvetReel has completely changed how I’m seen in the industry. I’m getting approached for serious projects instead of random, mismatched auditions.',
				'name'   => 'Leyla',
				'role'   => 'Fashion Model & Actor',
				'rating' => 5,
				'avatar' => home_url( '/wp-content/uploads/2025/11/856f47707399f5ed0e22af6918c4ec16-scaled.jpg' ),
			],
			[
				'quote'  => 'As a director, finding authentic talent used to take weeks. VelvetReel allowed us to cast our lead within 48 hours. Absolute game changer.',
				'name'   => 'David Ross',
				'role'   => 'Filmmaker & Producer',
				'rating' => 5,
				'avatar' => home_url( '/wp-content/uploads/2025/11/856f47707399f5ed0e22af6918c4ec16-scaled.jpg' ),
			],
		];
	}

	ob_start();
	?>
	<section id="velvet-testimonials-section" class="velvet-testimonials-section">
		<div class="velvet-testimonials-container">
			<?php if ( ! empty( $atts['title'] ) ) : ?>
				<h2 class="velvet-testimonials-heading"><?php echo esc_html( $atts['title'] ); ?></h2>
			<?php endif; ?>

			<div class="velvet-testimonials-carousel">
				<!-- Prev Button -->
				<button class="velvet-testimonial-arrow velvet-testi-prev" aria-label="Previous Testimonial">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="18" height="18" fill="currentColor"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L74.7 256 246.6 84.7c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/></svg>
				</button>

				<!-- Track Wrapper -->
				<div class="velvet-testimonials-track-wrapper">
					<div class="velvet-testimonials-track">
						<?php foreach ( $testimonials as $item ) : ?>
							<div class="velvet-testimonial-card">
								<!-- Quote Icon -->
								<div class="velvet-testi-quote-icon">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="32" height="32" fill="currentColor"><path d="M0 216C0 149.7 53.7 96 120 96h8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8c-39.8 0-72 32.2-72 72v16h80c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V216zm256 0c0-66.3 53.7-120 120-120h8c13.3 0 24 10.7 24 24s-10.7 24-24 24h-8c-39.8 0-72 32.2-72 72v16h80c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48h-128c-26.5 0-48-21.5-48-48V216z"/></svg>
								</div>

								<!-- Rating Stars -->
								<div class="velvet-testi-stars">
									<?php for ( $s = 0; $s < $item['rating']; $s++ ) : ?>
										<span class="star">&#9733;</span>
									<?php endfor; ?>
								</div>

								<!-- Quote Text -->
								<p class="velvet-testi-text">
									&ldquo;<?php echo wp_strip_all_tags( $item['quote'] ); ?>&rdquo;
								</p>

								<!-- Author Profile -->
								<div class="velvet-testi-author">
									<div class="velvet-testi-avatar-wrap">
										<img src="<?php echo esc_url( $item['avatar'] ); ?>" alt="<?php echo esc_attr( $item['name'] ); ?>" class="velvet-testi-avatar" />
									</div>
									<div class="velvet-testi-info">
										<h4 class="velvet-testi-name"><?php echo esc_html( $item['name'] ); ?></h4>
										<span class="velvet-testi-role"><?php echo esc_html( $item['role'] ); ?></span>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- Next Button -->
				<button class="velvet-testimonial-arrow velvet-testi-next" aria-label="Next Testimonial">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="18" height="18" fill="currentColor"><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L245.3 256 73.4 84.7c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg>
				</button>
			</div>

			<!-- Pagination Dots -->
			<div class="velvet-testi-dots"></div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'velvet_testimonials_slider', 'velvet_testimonials_slider_shortcode' );
