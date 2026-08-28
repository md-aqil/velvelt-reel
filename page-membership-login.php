<?php
/**
 * Template Name: Membership Login
 * Template for /membership-login/ page
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Start session if not started
if ( ! session_id() ) {
	session_start();
}

$login_errors = array();
$redirect_to = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( $_REQUEST['redirect_to'] ) : '';

// Handle Login Submission
if ( 'POST' === $_SERVER['REQUEST_METHOD'] && ( isset( $_POST['velvet_login_nonce'] ) || isset( $_POST['swpm-login'] ) ) ) {
	$username_or_email = sanitize_text_field( trim( $_POST['log'] ?? $_POST['swpm_user_name'] ?? '' ) );
	$password          = $_POST['pwd'] ?? $_POST['swpm_password'] ?? '';
	$remember          = ! empty( $_POST['rememberme'] );

	if ( empty( $username_or_email ) ) {
		$login_errors[] = __( 'Username or Email is required.', 'hello-elementor-child' );
	}
	if ( empty( $password ) ) {
		$login_errors[] = __( 'Password is required.', 'hello-elementor-child' );
	}

	if ( empty( $login_errors ) ) {
		// Determine if login is by email or username
		$user = is_email( $username_or_email ) ? get_user_by( 'email', $username_or_email ) : get_user_by( 'login', $username_or_email );

		if ( $user && wp_check_password( $password, $user->user_pass, $user->ID ) ) {
			// Credentials are valid - log user in
			$creds = array(
				'user_login'    => $user->user_login,
				'user_password' => $password,
				'remember'      => $remember,
			);

			$signon = wp_signon( $creds, is_ssl() );

			if ( ! is_wp_error( $signon ) ) {
				wp_set_current_user( $user->ID );
				wp_set_auth_cookie( $user->ID, $remember, is_ssl() );

				// Synchronize SWPM session if plugin is active
				if ( class_exists( 'SwpmMemberUtils' ) && class_exists( 'SwpmAuth' ) ) {
					SwpmAuth::get_instance()->login( $user->user_login, $password, $remember );
				}

				// Safe Redirect
				if ( empty( $redirect_to ) ) {
					$redirect_to = add_query_arg( 'login', 'success', home_url( '/talent/' ) );
				}

				wp_safe_redirect( $redirect_to );
				exit;
			} else {
				$login_errors[] = $signon->get_error_message();
			}
		} else {
			$login_errors[] = __( 'Invalid username/email or password. Please try again.', 'hello-elementor-child' );
		}
	}
}

get_header();

$is_logged_in = is_user_logged_in();
$current_user = $is_logged_in ? wp_get_current_user() : null;
?>

<main id="content" class="site-main velvet-auth-section">
	<!-- Ambient Background Glow Elements -->
	<div class="auth-bg-glow glow-1"></div>
	<div class="auth-bg-glow glow-2"></div>
	<div class="auth-bg-glow glow-3"></div>

	<div class="velvet-auth-container">
		<?php if ( $is_logged_in && $current_user ) : ?>
			<!-- Logged In State Card -->
			<div class="velvet-auth-card logged-in-card">
				<div class="auth-user-avatar-wrap">
					<?php
					$avatar_url = '';
					if ( function_exists( 'get_talent_profile_image_url' ) ) {
						$user_talent_query = new WP_Query( array(
							'post_type'      => 'talent',
							'author'         => $current_user->ID,
							'posts_per_page' => 1,
							'post_status'    => array( 'publish', 'draft', 'pending' ),
						) );
						if ( $user_talent_query->have_posts() ) {
							$avatar_url = get_talent_profile_image_url( $user_talent_query->posts[0]->ID );
						}
					}
					if ( empty( $avatar_url ) ) {
						$avatar_url = get_stylesheet_directory_uri() . '/assets/images/default-talent-avatar.svg';
					}
					?>
					<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $current_user->display_name ); ?>" class="auth-user-avatar" />
				</div>

				<div class="auth-badge-wrapper">
					<span class="auth-badge logged-in-badge"><i class="fas fa-check-circle"></i> <?php esc_html_e( 'Active Session', 'hello-elementor-child' ); ?></span>
				</div>

				<h1 class="auth-title"><?php printf( esc_html__( 'Hello, %s', 'hello-elementor-child' ), esc_html( $current_user->display_name ) ); ?></h1>
				<p class="auth-subtitle"><?php printf( esc_html__( 'You are currently signed in as %s.', 'hello-elementor-child' ), '<strong>' . esc_html( $current_user->user_email ) . '</strong>' ); ?></p>

				<div class="auth-actions-stacked">
					<a href="<?php echo esc_url( home_url( '/talent/' ) ); ?>" class="btn-auth btn-auth-primary">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="M7 15h4M7 11h8M7 7h10"></path></svg>
						<span><?php esc_html_e( 'View My Portfolios', 'hello-elementor-child' ); ?></span>
					</a>

					<a href="<?php echo esc_url( home_url( '/membership-login/membership-profile/' ) ); ?>" class="btn-auth btn-auth-secondary">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
						<span><?php esc_html_e( 'Edit Membership Profile', 'hello-elementor-child' ); ?></span>
					</a>

					<a href="<?php echo esc_url( wp_logout_url( home_url( '/membership-login/' ) ) ); ?>" class="btn-auth btn-auth-logout">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
						<span><?php esc_html_e( 'Sign Out', 'hello-elementor-child' ); ?></span>
					</a>
				</div>
			</div>

		<?php else : ?>
			<!-- Logged Out Form Card -->
			<div class="velvet-auth-card">
				<div class="auth-badge-wrapper">
					<span class="auth-badge"><i class="fas fa-lock"></i> <?php esc_html_e( 'SECURE MEMBER ACCESS', 'hello-elementor-child' ); ?></span>
				</div>

				<h1 class="auth-title"><?php esc_html_e( 'Welcome Back', 'hello-elementor-child' ); ?></h1>
				<p class="auth-subtitle"><?php esc_html_e( 'Sign in to access your talent portfolio, auditions & creative dashboard.', 'hello-elementor-child' ); ?></p>

				<?php if ( ! empty( $login_errors ) ) : ?>
					<div class="auth-alert-banner">
						<svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="12" cy="12" r="10"></circle>
							<line x1="12" y1="8" x2="12" y2="12"></line>
							<line x1="12" y1="16" x2="12.01" y2="16"></line>
						</svg>
						<div class="alert-content">
							<?php foreach ( $login_errors as $error ) : ?>
								<p><?php echo esc_html( $error ); ?></p>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<form id="velvetLoginForm" class="velvet-auth-form" method="post" action="">
					<?php wp_nonce_field( 'velvet_login_action', 'velvet_login_nonce' ); ?>
					<?php if ( ! empty( $redirect_to ) ) : ?>
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
					<?php endif; ?>

					<!-- Username / Email Field -->
					<div class="auth-field-group">
						<label for="user_login" class="auth-field-label"><?php esc_html_e( 'Username or Email', 'hello-elementor-child' ); ?></label>
						<div class="auth-input-wrapper">
							<svg class="auth-input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
								<circle cx="12" cy="7" r="4"></circle>
							</svg>
							<input type="text" name="log" id="user_login" class="auth-input-field" placeholder="<?php esc_attr_e( 'Enter your username or email', 'hello-elementor-child' ); ?>" value="<?php echo esc_attr( $_POST['log'] ?? $_POST['swpm_user_name'] ?? '' ); ?>" required autocomplete="username" />
						</div>
					</div>

					<!-- Password Field -->
					<div class="auth-field-group">
						<div class="auth-label-row">
							<label for="user_pass" class="auth-field-label"><?php esc_html_e( 'Password', 'hello-elementor-child' ); ?></label>
							<a href="<?php echo esc_url( home_url( '/forgot-password/' ) ); ?>" class="auth-forgot-link"><?php esc_html_e( 'Forgot Password?', 'hello-elementor-child' ); ?></a>
						</div>
						<div class="auth-input-wrapper">
							<svg class="auth-input-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
								<path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
							</svg>
							<input type="password" name="pwd" id="user_pass" class="auth-input-field" placeholder="<?php esc_attr_e( 'Enter your password', 'hello-elementor-child' ); ?>" required autocomplete="current-password" />
							<button type="button" class="auth-toggle-pwd" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'hello-elementor-child' ); ?>">
								<svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
								<svg class="eye-closed" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
							</button>
						</div>
					</div>

					<!-- Remember Me Checkbox -->
					<div class="auth-remember-row">
						<label class="auth-checkbox-label">
							<input type="checkbox" name="rememberme" id="rememberme" value="forever" checked />
							<span class="custom-checkbox"></span>
							<span class="checkbox-text"><?php esc_html_e( 'Remember this device', 'hello-elementor-child' ); ?></span>
						</label>
					</div>

					<!-- Submit Button -->
					<div class="auth-submit-group">
						<button type="submit" name="velvet_login_submit" class="btn-auth-submit">
							<span><?php esc_html_e( 'Sign In to Account', 'hello-elementor-child' ); ?></span>
							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
						</button>
					</div>
				</form>

				<!-- Sign Up Prompt Footer -->
				<div class="auth-card-footer">
					<p class="auth-footer-text">
						<?php esc_html_e( 'Don\'t have an account yet?', 'hello-elementor-child' ); ?>
						<a href="<?php echo esc_url( home_url( '/membership-join/' ) ); ?>" class="auth-signup-link"><?php esc_html_e( 'Join Membership', 'hello-elementor-child' ); ?></a>
					</p>
				</div>
			</div>
		<?php endif; ?>
	</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Password visibility toggle
	var toggleBtn = document.querySelector('.auth-toggle-pwd');
	var pwdInput = document.getElementById('user_pass');
	if (toggleBtn && pwdInput) {
		toggleBtn.addEventListener('click', function(e) {
			e.preventDefault();
			var eyeOpen = toggleBtn.querySelector('.eye-open');
			var eyeClosed = toggleBtn.querySelector('.eye-closed');
			if (pwdInput.type === 'password') {
				pwdInput.type = 'text';
				if (eyeOpen) eyeOpen.style.display = 'none';
				if (eyeClosed) eyeClosed.style.display = 'block';
			} else {
				pwdInput.type = 'password';
				if (eyeOpen) eyeOpen.style.display = 'block';
				if (eyeClosed) eyeClosed.style.display = 'none';
			}
		});
	}
});
</script>

<?php
get_footer();
