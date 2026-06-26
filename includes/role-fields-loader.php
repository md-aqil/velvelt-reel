<?php
/**
 * Role Fields Loader
 * 
 * This file contains functions to dynamically load role-specific fields
 * based on the selected domain and role.
 */

/**
 * Get the path to a role-specific fields template file
 * 
 * @param string $domain The domain (fashion, film, etc.)
 * @param string $role The role (fashion-designer, actor, etc.)
 * @return string|false The path to the template file or false if not found
 */
function get_role_fields_template_path($domain, $role) {
    $template_path = get_stylesheet_directory() . "/templates/roles/{$domain}/{$role}-fields.php";
    
    if (file_exists($template_path)) {
        return $template_path;
    }
    
    return false;
}

/**
 * Load and render role-specific fields for a given domain and role
 * 
 * @param string $domain The domain (fashion, film, etc.)
 * @param string $role The role (fashion-designer, actor, etc.)
 * @return void
 */
function render_role_specific_fields_template($domain, $role) {
    $template_path = get_role_fields_template_path($domain, $role);
    
    if ($template_path && file_exists($template_path)) {
        include $template_path;
    }
}

/**
 * Get all available roles for a domain
 * 
 * @param string $domain The domain to get roles for
 * @return array Associative array of roles [role_key => role_name]
 */
function get_domain_roles($domain) {
    $roles = array();
    
    // Include roles-config.php if not already included
    if (!function_exists('get_domains')) {
        require_once dirname(dirname(__FILE__)) . '/roles-config.php';
    }
    
    $domains = get_domains();
    
    if (isset($domains[$domain]) && isset($domains[$domain]['roles'])) {
        foreach ($domains[$domain]['roles'] as $role_key => $role_data) {
            $roles[$role_key] = $role_data['name'];
        }
    }
    
    return $roles;
}

/**
 * Render all role-specific fields for a domain
 * 
 * @param string $domain The domain to render fields for
 * @return void
 */
function render_all_role_fields_for_domain($domain) {
    $roles = get_domain_roles($domain);
    
    foreach ($roles as $role_key => $role_name) {
        render_role_specific_fields_template($domain, $role_key);
    }
}