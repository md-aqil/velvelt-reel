<?php
/**
 * The template for displaying a single Talent profile.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

while ( have_posts() ) :
	the_post();

	// --- Get all the meta data for the talent ---
	$post_id = get_the_ID();
	$role = get_post_meta( $post_id, '_talent_role', true );
	$city = get_post_meta( $post_id, '_talent_city', true );
	$country = get_post_meta( $post_id, '_talent_country', true );
	$location = ( $city && $country ) ? "{$city}, {$country}" : ( $city ?: $country );
	$work_type = get_post_meta( $post_id, '_talent_affiliation', true );
	$notable_works = get_post_meta( $post_id, '_talent_notable_works', true );

	// --- Get all other meta fields ---
	$email = get_post_meta( $post_id, '_talent_email', true );
	$phone = get_post_meta( $post_id, '_talent_phone', true );
	$website_url = get_post_meta( $post_id, '_talent_website', true );
	$instagram_url = get_post_meta( $post_id, '_talent_instagram', true );
	$linkedin_url = get_post_meta( $post_id, '_talent_linkedin', true );
	$years_active = get_post_meta( $post_id, '_talent_years_active', true );
	$education = get_post_meta( $post_id, '_talent_education', true );
	$languages = get_post_meta( $post_id, '_talent_languages', true );
	$available_for = get_post_meta( $post_id, '_talent_available_for', true );
	$willing_to_travel = get_post_meta( $post_id, '_talent_willing_to_travel', true );
	$brand_collabs = get_post_meta( $post_id, '_talent_brand_collabs', true );
	$interested_projects = get_post_meta( $post_id, '_talent_interested_projects', true );

	// --- Get Role-Specific and Portfolio Data ---
	$fashion_categories = get_post_meta( $post_id, '_talent_designCategories', true );
	$portfolio_image_ids = get_post_meta( $post_id, '_talent_portfolio', true );

	// Debug: Check what we're getting from the meta field
	error_log('Portfolio meta data: ' . print_r($portfolio_image_ids, true));

	// Prepare an array to hold portfolio image URLs
	$portfolio_images = [];
	if ( ! empty( $portfolio_image_ids ) && is_array( $portfolio_image_ids ) ) {
		foreach ( $portfolio_image_ids as $image_id ) {
			// Handle case where $image_id might be an array itself
			if (is_array($image_id)) {
				foreach ($image_id as $id) {
					$image_url = wp_get_attachment_image_url( $id, 'medium_large' );
					if ( $image_url ) {
						$portfolio_images[] = $image_url;
					}
				}
			} else {
				// Get the URL for a medium-large size image for the gallery
				$image_url = wp_get_attachment_image_url( $image_id, 'medium_large' );
				if ( $image_url ) {
					$portfolio_images[] = $image_url;
				}
			}
		}
	} else if (!empty($portfolio_image_ids) && !is_array($portfolio_image_ids)) {
		// Handle case where it might be stored as a single ID or comma-separated string
		$ids = is_string($portfolio_image_ids) ? explode(',', $portfolio_image_ids) : [$portfolio_image_ids];
		foreach ($ids as $id) {
			$id = trim($id);
			if (!empty($id) && is_numeric($id)) {
				$image_url = wp_get_attachment_image_url( $id, 'medium_large' );
				if ( $image_url ) {
					$portfolio_images[] = $image_url;
				}
			}
		}
	}

?>

<main id="content" class="site-main talent-single-page">

    <div class="talent-profile-container">

        <!-- Top Section (Header Card) -->
        <header class="talent-header-card" style="margin-top:40px;">
            <div class="talent-photo">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'large' ); ?>
                <?php else : ?>
                    <div class="talent-photo-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                <?php endif; ?>
            </div>
            <div class="talent-info">
                <h1 class="talent-name"><?php the_title(); ?></h1>
                <?php if ( $role ) : ?>
                    <h2 class="talent-role-subtitle"><?php echo esc_html( ucwords( str_replace( '-', ' ', $role ) ) ); ?></h2>
                <?php endif; ?>

                <div class="talent-tags">
                    <?php if ( $location ) : ?>
                        <span class="tag-pill location-tag"><?php echo esc_html( $location ); ?></span>
                    <?php endif; ?>
                    <?php if ( $work_type ) : ?>
                        <span class="tag-pill work-type-tag"><?php echo esc_html( $work_type ); ?></span>
                    <?php endif; ?>
                </div>

                <div class="talent-socials">
                    <?php if ( $website_url ) : ?>
                        <a href="<?php echo esc_url( $website_url ); ?>" class="talent-website" target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                            <span><?php echo esc_html( preg_replace( '(^https?://(www\.)?)', '', $website_url ) ); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if ( $instagram_url ) : ?>
                        <a href="<?php echo esc_url( 'https://instagram.com/' . ltrim($instagram_url, '@') ); ?>" class="talent-social-link" target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                            <span>Instagram</span>
                        </a>
                    <?php endif; ?>
                    <?php if ( $linkedin_url ) : ?>
                        <a href="<?php echo esc_url( $linkedin_url ); ?>" class="talent-social-link" target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                            <span>LinkedIn</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <!-- Biography Section -->
        <?php if ( get_the_content() ) : ?>
        <section class="talent-biography">
            <h3 class="section-title">Biography</h3>
            <div class="biography-content">
                <?php the_content(); ?>
            </div>
        </section>
        <hr class="soft-divider">
        <?php endif; ?>

        <!-- Details Grid -->
        <section class="talent-details-grid">
            <div class="details-left-column">

                <div class="details-section">
                    <h3 class="section-title">Contact & Socials</h3>
                    <ul class="details-list">
                        <?php if ( $email ) : ?>
                            <li><strong>Email:</strong> <span><?php echo esc_html( $email ); ?></span></li>
                        <?php endif; ?>
                        <?php if ( $phone ) : ?>
                            <li><strong>Phone:</strong> <span><?php echo esc_html( $phone ); ?></span></li>
                        <?php endif; ?>
                        <?php if ( ! empty( $languages ) && is_array( $languages ) ) : ?>
                            <li><strong>Languages:</strong> <span><?php echo esc_html( implode( ', ', $languages ) ); ?></span></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <?php if ( ! empty( $fashion_categories ) && is_array( $fashion_categories ) ) : ?>
                <div class="details-section">
                    <h3 class="section-title">Fashion Categories</h3>
                    <div class="text-chips-container">
                        <?php foreach ( $fashion_categories as $category ) : ?>
                            <span class="text-chip"><?php echo esc_html( $category ); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $notable_works ) && is_array( $notable_works ) ) : ?>
                <div class="details-section experience-section">
                    <h3 class="section-title">Experience</h3>
                    <?php if ( $years_active ) : ?>
                        <p class="years-active"><strong>Years Active:</strong> <?php echo esc_html( $years_active ); ?></p>
                    <?php endif; ?>
                    <ul class="experience-list">
                        <?php foreach ( $notable_works as $work ) : ?>
                            <?php if ( ! empty( $work['title'] ) ) : ?>
                            <?php
                                // Handle date range display
                                $start_date = '';
                                $end_date = '';
                                
                                if (!empty($work['startDate'])) {
                                    $start_date = date('M Y', strtotime($work['startDate']));
                                }
                                
                                if (isset($work['present']) && $work['present'] === 'on') {
                                    $end_date = 'Present';
                                } elseif (!empty($work['endDate'])) {
                                    $end_date = date('M Y', strtotime($work['endDate']));
                                } else {
                                    $end_date = 'N/A';
                                }
                                
                                // Format the date range
                                if ($start_date && $end_date) {
                                    $date_range = "{$start_date} - {$end_date}";
                                } elseif ($start_date) {
                                    $date_range = "{$start_date} - {$end_date}";
                                } else {
                                    $date_range = $end_date;
                                }
                            ?>
                            <li class="experience-item"> 
                                <span class="experience-role"><?php echo esc_html( $work['role'] ?? '' ); ?></span>
                                <span class="experience-company"><?php echo esc_html( $work['title'] ); ?></span>
                                <span class="experience-duration"><?php echo esc_html( $date_range ); ?></span>
                            </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( $education ) : ?>
                <div class="details-section">
                    <h3 class="section-title">Education & Training</h3>
                    <p class="education-text"><?php echo nl2br( esc_html( $education ) ); ?></p>
                </div>
                <?php endif; ?>

                <div class="details-section">
                    <h3 class="section-title">Availability & Preferences</h3>
                    <ul class="details-list">
                        <?php if ( ! empty( $available_for ) && is_array( $available_for ) ) : ?>
                            <li><strong>Available For:</strong> <span><?php echo esc_html( implode( ', ', $available_for ) ); ?></span></li>
                        <?php endif; ?>
                        <?php if ( $willing_to_travel ) : ?>
                            <li><strong>Willing to Travel:</strong> <span><?php echo ( $willing_to_travel === 'on' ) ? 'Yes' : 'No'; ?></span></li>
                        <?php endif; ?>
                        <?php if ( $brand_collabs ) : ?>
                            <li><strong>Brand Collaborations:</strong> <span><?php echo ( $brand_collabs === 'on' ) ? 'Yes' : 'No'; ?></span></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <?php if ( $interested_projects ) : ?>
                <div class="details-section">
                    <h3 class="section-title">Seeking Projects</h3>
                    <p class="seeking-projects-text"><?php echo nl2br( esc_html( $interested_projects ) ); ?></p>
                </div>
                <?php endif; ?>

            </div>
            <div class="details-right-column">
                <div class="details-section">
                    <h3 class="section-title">Portfolio</h3>
                    <?php if ( ! empty( $portfolio_images ) ) : ?>
                    <div class="portfolio-gallery">
                        <?php foreach ( $portfolio_images as $image_url ) : ?>
                            <div class="portfolio-image">
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="Portfolio image">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else : ?>
                        <p>No portfolio images have been uploaded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </div>

</main>

<?php
endwhile; // End of the loop.

get_footer();
?>