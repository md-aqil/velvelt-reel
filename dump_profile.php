<?php
require_once('../../../wp-load.php');
if (!function_exists('wp_set_current_user')) {
    require_once(ABSPATH . 'wp-includes/pluggable.php');
}

// Log in as admin via API artificially
wp_set_current_user(1);
// Note: Simple membership requires SWPM session tracking for profile. 
// It relies on SwpmAuth. So setting WP current user might not trick SWPM.
// Let's force it over via a hack or directly call the swpm view.

// We can just dump the code by invoking the SWPM class if it's there.
if (class_exists('SwpmFrontEndRegistration')) {
    // If not possible, I'll just scrape the plugin files. Oh wait, my plugin paths are NOT in the workspace!
}

// Actually, I can just use system commands to run wp-cli or grep the plugin files directly for the HTML structure.
