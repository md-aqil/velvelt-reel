<?php
/**
 * Security Hardening & Newsletter AJAX Handler for Hello Elementor Child Theme
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Security HTTP Headers
 */
function velvet_add_security_headers() {
	if ( ! headers_sent() ) {
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: SAMEORIGIN' );
		header( 'X-XSS-Protection: 1; mode=block' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	}
}
add_action( 'send_headers', 'velvet_add_security_headers' );

/**
 * 2. Disable XML-RPC Pingback & Authentication Brute Force
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * 3. Remove WP Version Generator Meta Tag
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * 4. Newsletter Subscription AJAX Handler
 */
function velvet_ajax_newsletter_subscribe() {
	// Verify Nonce for security
	if ( ! isset( $_POST['newsletter_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['newsletter_nonce'] ) ), 'velvet_newsletter_nonce' ) ) {
		wp_send_json_error( [ 'message' => 'Security check failed. Please refresh the page.' ] );
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	if ( empty( $email ) || ! is_email( $email ) ) {
		wp_send_json_error( [ 'message' => 'Please enter a valid email address.' ] );
	}

	// Store subscriber safely in WP options
	$subscribers = get_option( 'velvet_newsletter_subscribers', [] );
	if ( ! is_array( $subscribers ) ) {
		$subscribers = [];
	}

	if ( in_array( $email, $subscribers, true ) ) {
		wp_send_json_success( [ 'message' => 'Thank you! You are already subscribed.' ] );
	}

	$subscribers[] = $email;
	update_option( 'velvet_newsletter_subscribers', $subscribers );

	// Send notification email to admin if configured
	$admin_email = get_option( 'admin_email' );
	if ( $admin_email ) {
		$subject = 'New Newsletter Subscription: ' . $email;
		$body    = "A new user has subscribed to the newsletter on The VelvetReel:\n\nEmail: " . $email . "\nDate: " . current_time( 'mysql' );
		$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];
		wp_mail( $admin_email, $subject, $body, $headers );
	}

	wp_send_json_success( [ 'message' => 'Thank you for subscribing!' ] );
}
add_action( 'wp_ajax_velvet_subscribe_newsletter', 'velvet_ajax_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_velvet_subscribe_newsletter', 'velvet_ajax_newsletter_subscribe' );

/**
 * 5. Enqueue Footer Newsletter JS
 */
function velvet_enqueue_footer_scripts() {
	wp_enqueue_script(
		'velvet-footer-js',
		get_stylesheet_directory_uri() . '/js/footer-newsletter.js',
		[ 'jquery' ],
		HELLO_ELEMENTOR_CHILD_VERSION,
		true
	);

	wp_localize_script(
		'velvet-footer-js',
		'VelvetFooter',
		[
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		]
	);
}
add_action( 'wp_enqueue_scripts', 'velvet_enqueue_footer_scripts', 25 );
