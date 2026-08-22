<?php
/**
 * Testimonial Custom Post Type and Meta Boxes for WP-Admin
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Testimonials Custom Post Type
 */
function velvet_register_testimonial_cpt() {
	$labels = [
		'name'               => 'Testimonials',
		'singular_name'      => 'Testimonial',
		'menu_name'          => 'Testimonials',
		'name_admin_bar'     => 'Testimonial',
		'add_new'            => 'Add New Testimonial',
		'add_new_item'       => 'Add New Testimonial',
		'new_item'           => 'New Testimonial',
		'edit_item'          => 'Edit Testimonial',
		'view_item'          => 'View Testimonial',
		'all_items'          => 'All Testimonials',
		'search_items'       => 'Search Testimonials',
		'not_found'          => 'No testimonials found.',
		'not_found_in_trash' => 'No testimonials found in Trash.',
	];

	$args = [
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 25,
		'menu_icon'          => 'dashicons-testimonial',
		'supports'           => [ 'title', 'editor', 'thumbnail' ],
	];

	register_post_type( 'testimonial', $args );
}
add_action( 'init', 'velvet_register_testimonial_cpt' );

/**
 * Add Meta Boxes for Testimonial CPT
 */
function velvet_add_testimonial_meta_boxes() {
	add_meta_box(
		'velvet_testimonial_details',
		'Testimonial Details',
		'velvet_render_testimonial_meta_box',
		'testimonial',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'velvet_add_testimonial_meta_boxes' );

/**
 * Render Meta Box HTML
 */
function velvet_render_testimonial_meta_box( $post ) {
	wp_nonce_field( 'velvet_save_testimonial_meta', 'velvet_testimonial_nonce' );

	$role   = get_post_meta( $post->ID, '_testimonial_role', true );
	$rating = get_post_meta( $post->ID, '_testimonial_rating', true );
	if ( empty( $rating ) ) {
		$rating = '5';
	}
	?>
	<div style="padding: 10px 0;">
		<p style="margin-bottom: 8px;">
			<label for="velvet_testimonial_role" style="font-weight: 600; display: block; margin-bottom: 4px;">Role / Designation:</label>
			<input type="text" id="velvet_testimonial_role" name="velvet_testimonial_role" value="<?php echo esc_attr( $role ); ?>" placeholder="e.g. Actor, Dancer, Filmmaker" style="width: 100%; max-width: 400px; padding: 8px;" />
		</p>

		<p style="margin-top: 16px;">
			<label for="velvet_testimonial_rating" style="font-weight: 600; display: block; margin-bottom: 4px;">Star Rating:</label>
			<select id="velvet_testimonial_rating" name="velvet_testimonial_rating" style="padding: 6px 12px;">
				<option value="5" <?php selected( $rating, '5' ); ?>>5 Stars (★★★★★)</option>
				<option value="4" <?php selected( $rating, '4' ); ?>>4 Stars (★★★★)</option>
				<option value="3" <?php selected( $rating, '3' ); ?>>3 Stars (★★★)</option>
				<option value="2" <?php selected( $rating, '2' ); ?>>2 Stars (★★)</option>
				<option value="1" <?php selected( $rating, '1' ); ?>>1 Star (★)</option>
			</select>
		</p>
	</div>
	<?php
}

/**
 * Save Meta Box Data
 */
function velvet_save_testimonial_meta( $post_id ) {
	if ( ! isset( $_POST['velvet_testimonial_nonce'] ) || ! wp_verify_nonce( $_POST['velvet_testimonial_nonce'], 'velvet_save_testimonial_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['velvet_testimonial_role'] ) ) {
		update_post_meta( $post_id, '_testimonial_role', sanitize_text_field( $_POST['velvet_testimonial_role'] ) );
	}

	if ( isset( $_POST['velvet_testimonial_rating'] ) ) {
		update_post_meta( $post_id, '_testimonial_rating', sanitize_text_field( $_POST['velvet_testimonial_rating'] ) );
	}
}
add_action( 'save_post_testimonial', 'velvet_save_testimonial_meta' );

/**
 * Add Columns to Testimonials Admin Table List
 */
function velvet_testimonial_admin_columns( $columns ) {
	$new_columns = [
		'cb'                 => $columns['cb'],
		'title'              => 'Author Name',
		'featured_image'     => 'Avatar',
		'testimonial_role'   => 'Role / Designation',
		'testimonial_rating' => 'Rating',
		'date'               => $columns['date'],
	];
	return $new_columns;
}
add_filter( 'manage_testimonial_posts_columns', 'velvet_testimonial_admin_columns' );

function velvet_testimonial_admin_column_content( $column, $post_id ) {
	if ( 'featured_image' === $column ) {
		if ( has_post_thumbnail( $post_id ) ) {
			echo get_the_post_thumbnail( $post_id, [ 40, 40 ], [ 'style' => 'border-radius:50%; object-fit:cover;' ] );
		} else {
			echo '<span style="color:#aaa;">No Avatar</span>';
		}
	} elseif ( 'testimonial_role' === $column ) {
		$role = get_post_meta( $post_id, '_testimonial_role', true );
		echo esc_html( $role ? $role : '—' );
	} elseif ( 'testimonial_rating' === $column ) {
		$rating = get_post_meta( $post_id, '_testimonial_rating', true );
		echo esc_html( ( $rating ? $rating : '5' ) . ' Stars' );
	}
}
add_action( 'manage_testimonial_posts_custom_column', 'velvet_testimonial_admin_column_content', 10, 2 );
