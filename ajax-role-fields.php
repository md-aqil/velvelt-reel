<?php
/**
 * AJAX handler for loading role-specific fields
 */

// WordPress bootstrap
require_once('../../../wp-load.php');

// Check if user is logged in
if (!is_user_logged_in()) {
    wp_die('Access denied.');
}

// Check AJAX nonce
if (!wp_verify_nonce($_POST['nonce'], 'talent_submission')) {
    wp_die('Security check failed.');
}

// Get domain and role from request
$domain = sanitize_text_field($_POST['domain']);
$role = sanitize_text_field($_POST['role']);

// Include the role fields loader
require_once get_stylesheet_directory() . '/includes/role-fields-loader.php';

// Render the role-specific fields
render_role_specific_fields_template($domain, $role);

wp_die();