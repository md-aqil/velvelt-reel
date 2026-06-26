<?php
/**
 * Functions for the portfolio gallery component
 */

/**
 * Include the portfolio gallery component with proper parameters
 * 
 * @param string $role_key The role key/slug
 * @param string $role_name The display name of the role
 */
function include_portfolio_gallery_component($role_key, $role_name) {
    // Set global variables for the component
    global $role_key_component, $role_name_component;
    $role_key_component = $role_key;
    $role_name_component = $role_name;
    
    // Include the component
    include get_stylesheet_directory() . '/components/portfolio-gallery.php';
}
?>