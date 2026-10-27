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

/**
 * 6. Restrict WordPress REST API Users Endpoint (Prevent User Enumeration)
 */
add_filter( 'rest_endpoints', function( $endpoints ) {
	if ( isset( $endpoints['/wp/v2/users'] ) ) {
		unset( $endpoints['/wp/v2/users'] );
	}
	if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
		unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
} );

/**
 * 7. Block Native WordPress Registration (/wp-login.php?action=register)
 * The site uses a custom signup workflow (/sign-up/ with OTP verification).
 * Native WP registration is frequently abused by bots.
 */
add_action( 'login_form_register', function() {
	wp_safe_redirect( home_url( '/sign-up/' ) );
	exit;
} );

/**
 * 8. Hard Guard: Prevent Default Role Escalation
 * Even if an option update exploit attempts to change default_role to administrator,
 * this filter guarantees default_role always falls back to 'subscriber'.
 */
add_filter( 'pre_option_default_role', function( $default ) {
	$role = get_option( 'default_role', 'subscriber' );
	if ( 'administrator' === strtolower( $role ) ) {
		return 'subscriber';
	}
	return $role ? $role : 'subscriber';
} );

/**
 * 9. Block Author Query String Enumeration (?author=1)
 */
add_action( 'template_redirect', function() {
	if ( is_author() && ! is_admin() ) {
		wp_safe_redirect( home_url(), 301 );
		exit;
	}
} );

/**
 * 10. Administrator Role Whitelist Guard
 * Blocks any attempt to promote an unauthorized account to administrator.
 */
add_action( 'set_user_role', function( $user_id, $role, $old_roles ) {
	if ( 'administrator' === strtolower( $role ) ) {
		$allowed_admins = array( 'chaitu2025', 'chai', 'mjjukebox', 'vbmjtvr', 'mdaqil' );
		$user = get_userdata( $user_id );
		if ( $user && ! in_array( strtolower( $user->user_login ), $allowed_admins, true ) ) {
			// Demote immediately to subscriber
			$user->set_role( 'subscriber' );
			error_log( "SECURITY ALERT: Blocked rogue admin role escalation for user {$user->user_login} (ID: {$user_id})" );
		}
	}
}, 10, 3 );

/**
 * 11. Active Rogue Admin Watchdog & Auto-Purge
 * Runs an active database check against wp_users & wp_usermeta.
 * Any admin account NOT in the approved whitelist (e.g. manager_*, support_*)
 * is instantly deleted or stripped of privileges before an attacker can use it.
 */
add_action( 'init', function() {
	// Rate-limit check using transient: runs every 2 minutes
	if ( false === get_transient( 'velvet_admin_audit_timer' ) ) {
		set_transient( 'velvet_admin_audit_timer', 1, 120 );

		global $wpdb;
		$cap_key = $wpdb->prefix . 'capabilities';
		$admins = $wpdb->get_results( "
			SELECT u.ID, u.user_login 
			FROM {$wpdb->users} u
			JOIN {$wpdb->usermeta} m ON u.ID = m.user_id
			WHERE m.meta_key = '{$cap_key}' AND m.meta_value LIKE '%administrator%'
		" );

		$allowed_admins = array( 'chaitu2025', 'chai', 'mjjukebox', 'vbmjtvr', 'mdaqil' );
		if ( ! empty( $admins ) ) {
			foreach ( $admins as $admin ) {
				if ( ! in_array( strtolower( $admin->user_login ), $allowed_admins, true ) ) {
					// Rogue Administrator Detected: Delete account immediately
					if ( ! function_exists( 'wp_delete_user' ) ) {
						require_once ABSPATH . 'wp-admin/includes/user.php';
					}
					wp_delete_user( $admin->ID );
					error_log( "SECURITY WATCHDOG: Rogue administrator account deleted: {$admin->user_login} (ID: {$admin->ID})" );
				}
			}
		}
	}
} );

/**
 * 12. Upload Armor: Block Double Extensions & PHP Polyglot Payloads
 * Intercepts any file upload through WordPress to ensure malicious files
 * (e.g. avatar.php.jpg or image containing <?php) are blocked before saved.
 */
add_filter( 'wp_handle_upload_prefilter', function( $file ) {
	$name = isset( $file['name'] ) ? strtolower( $file['name'] ) : '';
	$tmp  = isset( $file['tmp_name'] ) ? $file['tmp_name'] : '';

	// A: Check for dangerous double extensions (e.g. file.php.jpg, shell.phtml.png)
	$dangerous_pattern = '/\.(php[0-9]?|phtml|phar|pl|py|cgi|sh|asp|aspx|jsp|inc|cgi)\./i';
	if ( preg_match( $dangerous_pattern, $name ) ) {
		$file['error'] = 'Security Block: Files with executable extensions are strictly prohibited.';
		error_log( "SECURITY ALERT: Blocked dangerous multi-extension upload: {$name}" );
		return $file;
	}

	// B: Deep inspect image contents for embedded PHP tags (Polyglots)
	if ( ! empty( $tmp ) && is_readable( $tmp ) ) {
		$header_bytes = @file_get_contents( $tmp, false, null, 0, 1024 );
		if ( $header_bytes && ( stripos( $header_bytes, '<?php' ) !== false || stripos( $header_bytes, '<?=' ) !== false || stripos( $header_bytes, '<script language="php"' ) !== false ) ) {
			$file['error'] = 'Security Block: File contains executable PHP code embedded inside media.';
			error_log( "SECURITY ALERT: Blocked embedded PHP polyglot payload: {$name}" );
			return $file;
		}
	}

	return $file;
} );

/**
 * 13. Lightweight Request Firewall Shield
 * Blocks common exploit payloads, null-byte injections, and path traversal
 * attempts before they can reach installed plugins.
 */
add_action( 'plugins_loaded', function() {
	if ( is_admin() ) {
		return;
	}

	$uri   = isset( $_SERVER['REQUEST_URI'] ) ? strtolower( rawurldecode( $_SERVER['REQUEST_URI'] ) ) : '';
	$query = isset( $_SERVER['QUERY_STRING'] ) ? strtolower( rawurldecode( $_SERVER['QUERY_STRING'] ) ) : '';

	// Block directory traversal and null-byte
	if ( strpos( $uri, '../' ) !== false || strpos( $uri, '..\\' ) !== false || strpos( $uri, "\0" ) !== false ) {
		wp_die( 'Access denied by VelvetReel Security Shield (Path Traversal detected).', 'Forbidden', [ 'response' => 403 ] );
	}

	// Block remote command injection signatures in query parameters
	$bad_signatures = [
		'base64_decode(',
		'eval(',
		'passthru(',
		'shell_exec(',
		'system(',
		'union select',
		'concat(',
		'information_schema',
		'<script',
		'etc/passwd',
	];

	foreach ( $bad_signatures as $sig ) {
		if ( strpos( $query, $sig ) !== false ) {
			error_log( "SECURITY WAF: Blocked suspicious request: {$sig} in query: {$query}" );
			wp_die( 'Access denied by VelvetReel Security Shield.', 'Forbidden', [ 'response' => 403 ] );
		}
	}
}, 1 );


