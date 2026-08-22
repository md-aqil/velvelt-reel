<?php
/**
 * Custom High-Performance Talent Grid Component for The VelvetReel
 * Replaces Elementor Loop Builder with lightweight, 100% responsive code.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode to display custom Talent Grid with dynamic category filtering
 * Usage: [velvet_talent_grid]
 */
function velvet_talent_grid_shortcode( $atts ) {
	$atts = shortcode_atts( [
		'posts_per_page' => -1,
		'title'          => 'FIND ALL TALENTS',
	], $atts, 'velvet_talent_grid' );

	// Query published talent posts
	$args = [
		'post_type'      => 'talent',
		'post_status'    => 'publish',
		'posts_per_page' => intval( $atts['posts_per_page'] ),
		'orderby'        => 'date',
		'order'          => 'DESC',
	];

	$talent_query = new WP_Query( $args );

	if ( ! $talent_query->have_posts() ) {
		return '<div class="velvet-talent-empty"><p>No talents found.</p></div>';
	}

	// Extract unique roles & collect post data
	$roles      = [];
	$posts_data = [];

	while ( $talent_query->have_posts() ) {
		$talent_query->the_post();
		$post_id    = get_the_ID();
		$title      = get_the_title();
		$permalink  = get_permalink();
		$raw_role   = get_post_meta( $post_id, '_talent_role', true );
		$image_url  = get_the_post_thumbnail_url( $post_id, 'large' );

		// Fallback image if featured image is missing
		if ( ! $image_url ) {
			$image_url = home_url( '/wp-content/uploads/2025/11/856f47707399f5ed0e22af6918c4ec16-scaled.jpg' );
		}

		// Verification badge detection
		$has_paid_approval = get_post_meta( $post_id, '_talent_has_paid_approval', true );
		$is_verified       = get_post_meta( $post_id, '_talent_verified', true );
		$badge_text        = get_post_meta( $post_id, '_talent_badge_text', true );

		if ( ! $badge_text ) {
			if ( $has_paid_approval ) {
				$badge_text = 'VR SCOUTED+VERIFIED';
			} elseif ( $is_verified ) {
				$badge_text = 'VERIFIED';
			}
		}

		// Normalize role for category tabs
		$role_slug  = ! empty( $raw_role ) ? sanitize_title( $raw_role ) : 'other';
		$role_label = ! empty( $raw_role ) ? ucwords( str_replace( [ '-', '_' ], ' ', $raw_role ) ) : 'Talent';

		if ( ! empty( $raw_role ) && ! isset( $roles[ $role_slug ] ) ) {
			$roles[ $role_slug ] = $role_label;
		}

		$posts_data[] = [
			'id'         => $post_id,
			'title'      => $title,
			'permalink'  => $permalink,
			'role_slug'  => $role_slug,
			'role_label' => $role_label,
			'image_url'  => $image_url,
			'badge_text' => $badge_text,
		];
	}
	wp_reset_postdata();

	ob_start();
	?>
	<section id="velvet-talent-section" class="velvet-talent-section">
		<div class="velvet-talent-container">
			<?php if ( ! empty( $atts['title'] ) ) : ?>
				<h2 class="velvet-talent-heading"><?php echo esc_html( $atts['title'] ); ?></h2>
			<?php endif; ?>

			<!-- Filter Category Buttons -->
			<div class="velvet-talent-filter-bar">
				<button class="velvet-filter-btn active" data-filter="all">All</button>
				<?php foreach ( $roles as $slug => $label ) : ?>
					<button class="velvet-filter-btn" data-filter="<?php echo esc_attr( $slug ); ?>">
						<?php echo esc_html( $label ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<!-- Responsive Talent Grid -->
			<div class="velvet-talent-grid">
				<?php foreach ( $posts_data as $item ) : ?>
					<div class="velvet-talent-card" data-role="<?php echo esc_attr( $item['role_slug'] ); ?>">
						<div class="velvet-card-bg" style="background-image: url('<?php echo esc_url( $item['image_url'] ); ?>');"></div>
						<div class="velvet-card-overlay"></div>

						<?php if ( ! empty( $item['badge_text'] ) ) : ?>
							<div class="velvet-talent-badge">
								<?php echo esc_html( $item['badge_text'] ); ?>
							</div>
						<?php endif; ?>

						<div class="velvet-card-content">
							<h3 class="velvet-talent-name"><?php echo esc_html( $item['title'] ); ?></h3>
							<p class="velvet-talent-role"><?php echo esc_html( $item['role_label'] ); ?></p>
							<a href="<?php echo esc_url( $item['permalink'] ); ?>" class="velvet-talent-btn">View Details</a>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'velvet_talent_grid', 'velvet_talent_grid_shortcode' );
