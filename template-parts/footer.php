<?php
/**
 * Custom Footer Template Part for Hello Elementor Child Theme
 * Re-creating the exact theme footer responsive design
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<footer id="site-footer" class="velvet-site-footer">
	<div class="velvet-footer-container">
		<div class="velvet-footer-grid">
			
			<!-- Column 1: Brand & About -->
			<div class="velvet-footer-col velvet-footer-about">
				<div class="velvet-footer-logo">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<img src="https://www.thevelvetreel.com/wp-content/uploads/2025/10/velvetreel.png" alt="The VelvetReel" class="footer-logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
						<div class="footer-logo-fallback" style="display:none; font-size:22px; font-weight:800; color:#ffffff; letter-spacing:1px;">
							THE VELVET REEL
						</div>
					</a>
				</div>
				<p class="velvet-footer-description">
					The VelvetReel is a fashion and film talent platform specialize in discovering, nurturing, and showcasing creative individuals-models, actors, filmmakers, designers, and more-who are ready to make an impact in the industry.
				</p>
				<div class="velvet-footer-socials">
					<a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="social-icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="16" height="16" fill="currentColor"><path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V137.9c0-26.8 13.1-52.9 55.2-52.9h42.7V0h-68.7C143.5 0 96 49.3 96 128.4v73.1H32v97.8H80z"/></svg>
					</a>
					<a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="social-icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="16" height="16" fill="currentColor"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
					</a>
				</div>
			</div>

			<!-- Column 2: Quick Links -->
			<div class="velvet-footer-col velvet-footer-links">
				<h3 class="velvet-footer-heading">Quick Links</h3>
				<div class="velvet-footer-heading-line"></div>
				<ul class="velvet-footer-menu">
					<li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">About Us</a></li>
					<li><a href="<?php echo esc_url( home_url( '/clients/' ) ); ?>" class="highlight-link">Clients</a></li>
					<li><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Blog</a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Contact Us</a></li>
					<li><a href="<?php echo esc_url( home_url( '/scam-alert/' ) ); ?>">Scam Alert</a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>">Terms & Conditions</a></li>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a></li>
					<li><a href="<?php echo esc_url( home_url( '/sign-up/' ) ); ?>">Sign Up</a></li>
					<li><a href="<?php echo esc_url( home_url( '/sign-in/' ) ); ?>">Login</a></li>
					<li><a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>">Pricing</a></li>
				</ul>
			</div>

			<!-- Column 3: Contact Information -->
			<div class="velvet-footer-col velvet-footer-contact">
				<h3 class="velvet-footer-heading">Contact Information</h3>
				<div class="velvet-footer-heading-line"></div>
				<div class="velvet-contact-list">
					<div class="velvet-contact-item">
						<div class="velvet-contact-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="18" height="18" fill="currentColor"><path d="M464 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V80c0-26.51-21.49-48-48-48zM224 416H64v-32c0-35.35 28.65-64 64-64h32c35.35 0 64 28.65 64 64v32zm-80-128c-35.35 0-64-28.65-64-64s28.65-64 64-64 64 28.65 64 64-28.65 64-64 64zm304 128H288v-32h160v32zm0-64H288v-32h160v32zm0-64H288v-32h160v32z"/></svg>
						</div>
						<div class="velvet-contact-details">
							<span class="contact-label">New Jersey Address</span>
							<span class="contact-value">101 Hudson St., Jersey City, NJ 07304</span>
						</div>
					</div>

					<div class="velvet-contact-item">
						<div class="velvet-contact-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="18" height="18" fill="currentColor"><path d="M464 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V80c0-26.51-21.49-48-48-48zM224 416H64v-32c0-35.35 28.65-64 64-64h32c35.35 0 64 28.65 64 64v32zm-80-128c-35.35 0-64-28.65-64-64s28.65-64 64-64 64 28.65 64 64-28.65 64-64 64zm304 128H288v-32h160v32zm0-64H288v-32h160v32zm0-64H288v-32h160v32z"/></svg>
						</div>
						<div class="velvet-contact-details">
							<span class="contact-label">New York Address</span>
							<span class="contact-value">14 Penn Plaza, 34th Street, New York 10012</span>
						</div>
					</div>

					<div class="velvet-contact-item">
						<div class="velvet-contact-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="18" height="18" fill="currentColor"><path d="M497.39 361.8l-112-48a24 24 0 0 0-28 6.9l-49.6 60.6C215.7 335.5 176.5 296.3 132.7 204.3l60.6-49.6a24 24 0 0 0 6.9-28l-48-112A24.16 24.16 0 0 0 122.6 1H48A24 24 0 0 0 24 25c0 255.5 207.5 463 463 463a24 24 0 0 0 24-24v-74.6a24.16 24.16 0 0 0-13.61-22.6z"/></svg>
						</div>
						<div class="velvet-contact-details">
							<span class="contact-label">Call us (Toll Free)</span>
							<a href="tel:+18885855396" class="contact-value">+1-888-585-5396</a>
						</div>
					</div>

					<div class="velvet-contact-item">
						<div class="velvet-contact-icon">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="18" height="18" fill="currentColor"><path d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.53l-208 125-208-125V112h416zM48 400V210.3l193.6 116.16a32 32 0 0 0 32.8 0L464 210.3V400H48z"/></svg>
						</div>
						<div class="velvet-contact-details">
							<span class="contact-label">Email :</span>
							<a href="mailto:contact@thevelvetreel.com" class="contact-value">contact@thevelvetreel.com</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Column 4: Newsletter -->
			<div class="velvet-footer-col velvet-footer-newsletter">
				<h3 class="velvet-footer-heading">Subscribe Newsletter</h3>
				<div class="velvet-footer-heading-line"></div>
				<form id="velvet-newsletter-form" class="velvet-newsletter-form" method="post" action="#">
					<?php wp_nonce_field( 'velvet_newsletter_nonce', 'newsletter_nonce' ); ?>
					<div class="newsletter-input-wrap">
						<input type="email" name="email" placeholder="Email" required class="velvet-newsletter-input" />
					</div>
					<button type="submit" class="velvet-newsletter-submit">SUBMIT</button>
					<div class="velvet-newsletter-msg" style="display:none; margin-top:10px; font-size:14px; font-weight:500;"></div>
				</form>
			</div>

		</div>

		<!-- Bottom Copyright Line -->
		<div class="velvet-footer-bottom">
			<p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> The VelvetReel. All Rights Reserved.</p>
		</div>
	</div>
</footer>
