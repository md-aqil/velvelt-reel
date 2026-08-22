<?php
/**
 * Custom High-Performance Rotating Title Component for The VelvetReel
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode to display dynamic Rotating Text Title with 3D Flip Animation
 * Usage: [velvet_rotating_title static="EXPLORE AWESOME" words="ACTORS, MODELS, FILMMAKERS, DANCERS, SINGERS, TALENTS"]
 */
function velvet_rotating_title_shortcode( $atts ) {
	$atts = shortcode_atts( [
		'static' => 'EXPLORE AWESOME',
		'words'  => 'ACTORS, MODELS, FILMMAKERS, DANCERS, SINGERS, TALENTS',
	], $atts, 'velvet_rotating_title' );

	$word_list = array_map( 'trim', explode( ',', $atts['words'] ) );

	if ( empty( $word_list ) ) {
		$word_list = [ 'ACTORS', 'MODELS', 'TALENTS' ];
	}

	ob_start();
	?>
	<div class="velvet-rotating-title-wrap">
		<h2 class="velvet-rotating-heading">
			<?php if ( ! empty( $atts['static'] ) ) : ?>
				<span class="velvet-static-text"><?php echo esc_html( $atts['static'] ); ?></span>
			<?php endif; ?>
			
			<span class="velvet-rotating-words-viewport">
				<?php foreach ( $word_list as $index => $word ) : ?>
					<span class="velvet-rotating-word <?php echo $index === 0 ? 'is-active' : ''; ?>">
						<?php echo esc_html( $word ); ?>
					</span>
				<?php endforeach; ?>
			</span>
		</h2>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'velvet_rotating_title', 'velvet_rotating_title_shortcode' );
