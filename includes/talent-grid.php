<?php
/**
 * Custom High-Performance Talent Grid Component for The VelvetReel
 * Replaces Elementor Loop Builder with lightweight, 100% responsive code.
 * Includes Search by Name/Role, Country Filter, and Native LazyLoading.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode to display custom Talent Grid with dynamic search & category filtering
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

	// Extract unique roles, countries & collect post data
	$roles      = [];
	$countries  = [];
	$posts_data = [];

	while ( $talent_query->have_posts() ) {
		$talent_query->the_post();
		$post_id    = get_the_ID();
		$title      = get_the_title();
		$permalink  = get_permalink();
		$raw_role   = get_post_meta( $post_id, '_talent_role', true );
		$country    = get_post_meta( $post_id, '_talent_country', true );
		$image_url  = get_the_post_thumbnail_url( $post_id, 'medium_large' );

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

		// Normalize country for dropdown
		$country_clean = ! empty( $country ) ? trim( $country ) : '';
		$country_slug  = ! empty( $country_clean ) ? sanitize_title( $country_clean ) : '';

		if ( ! empty( $country_clean ) && ! isset( $countries[ $country_slug ] ) ) {
			$countries[ $country_slug ] = $country_clean;
		}

		$posts_data[] = [
			'id'           => $post_id,
			'title'        => $title,
			'permalink'    => $permalink,
			'role_slug'    => $role_slug,
			'role_label'   => $role_label,
			'country_slug' => $country_slug,
			'country_name' => $country_clean,
			'image_url'    => $image_url,
			'badge_text'   => $badge_text,
		];
	}
	wp_reset_postdata();

	ksort( $countries );

	ob_start();
	?>
	<section id="velvet-talent-section" class="velvet-talent-section">
		<div class="velvet-talent-container">
			<?php if ( ! empty( $atts['title'] ) ) : ?>
				<h2 class="velvet-talent-heading"><?php echo esc_html( $atts['title'] ); ?></h2>
			<?php endif; ?>

			<!-- Search Bar UI (Search by Name/Role + Search by Country) -->
			<div class="velvet-talent-search-bar">
				<div class="velvet-search-input-wrap">
					<input type="text" id="velvet-talent-search-input" class="velvet-search-input" placeholder="Search For Talents by Name/Role" autocomplete="off" />
				</div>
				<div class="velvet-search-country-wrap">
					<select id="velvet-talent-country-select" class="velvet-country-select">
						<option value="all">Country</option>
						<?php foreach ( $countries as $c_slug => $c_name ) : ?>
							<option value="<?php echo esc_attr( $c_slug ); ?>"><?php echo esc_html( $c_name ); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="velvet-select-arrow">▼</span>
				</div>
				<button type="button" id="velvet-talent-search-btn" class="velvet-search-btn" aria-label="Search">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16" height="16" fill="currentColor"><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.1-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0s208 93.1 208 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
				</button>
			</div>

			<!-- Category Filter Buttons -->
			<div class="velvet-talent-filter-bar">
				<button class="velvet-filter-btn active" data-filter="all">All</button>
				<?php foreach ( $roles as $slug => $label ) : ?>
					<button class="velvet-filter-btn" data-filter="<?php echo esc_attr( $slug ); ?>">
						<?php echo esc_html( $label ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<!-- Responsive Talent Grid with Native Lazy Loading -->
			<div class="velvet-talent-grid" id="velvet-talent-grid-list">
				<?php foreach ( $posts_data as $item ) : ?>
					<div class="velvet-talent-card" 
						 data-role="<?php echo esc_attr( $item['role_slug'] ); ?>" 
						 data-country="<?php echo esc_attr( $item['country_slug'] ); ?>" 
						 data-search="<?php echo esc_attr( strtolower( $item['title'] . ' ' . $item['role_label'] . ' ' . $item['country_name'] ) ); ?>">
						
						<!-- Lazyloaded Card Image -->
						<div class="velvet-card-bg">
							<img loading="lazy" decodings="async" src="<?php echo esc_url( $item['image_url'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>" class="velvet-card-img" />
						</div>

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

			<!-- No Results Message -->
			<div id="velvet-no-results" class="velvet-no-results" style="display: none;">
				<p>No talents found matching your search criteria.</p>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'velvet_talent_grid', 'velvet_talent_grid_shortcode' );
