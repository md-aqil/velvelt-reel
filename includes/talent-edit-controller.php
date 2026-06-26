<?php
/**
 * Talent Edit Controller
 * 
 * Handles all PHP logic for editing an existing talent profile
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Talent_Edit_Controller Class
 * 
 * Handles all server-side logic for talent profile editing
 */
class Talent_Edit_Controller {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * The talent post ID being edited
     */
    private $talent_id = 0;
    
    /**
     * The talent post object
     */
    private $talent_post = null;
    
    /**
     * All meta data for the talent profile
     */
    private $talent_meta = array();
    
    /**
     * Template state data
     */
    private $state = array();
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor - Initialize controller
     */
    public function __construct() {
        $this->init();
    }
    
    /**
     * Initialize controller - run access checks and setup
     */
    public function init() {
        // Check access permissions
        $this->check_access_permissions();
        
        // Setup required data
        $this->load_talent_data();
        $this->setup_template_data();
    }
    
    /**
     * Check user access permissions
     */
    private function check_access_permissions() {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_redirect(home_url('/sign-in/'));
            exit;
        }
        
        // Get the talent ID from URL parameter
        $this->talent_id = isset($_GET['talent_id']) ? intval($_GET['talent_id']) : 0;
        
        // Check if talent ID is provided
        if (!$this->talent_id) {
            wp_redirect(get_post_type_archive_link('talent'));
            exit;
        }
        
        // Get the talent post
        $this->talent_post = get_post($this->talent_id);
        
        // Check if talent post exists and is of correct type
        if (!$this->talent_post || $this->talent_post->post_type !== 'talent') {
            wp_redirect(get_post_type_archive_link('talent'));
            exit;
        }
        
        // Check if user is the author of this talent profile
        if (get_post_field('post_author', $this->talent_id) != get_current_user_id()) {
            wp_die('You do not have permission to edit this profile.');
        }
    }
    
    /**
     * Load all talent data from database
     */
    private function load_talent_data() {
        // Get all meta data
        $this->talent_meta = get_post_meta($this->talent_id);
        
        // Build state array with all values
        $this->state = array(
            // Basic info
            'talent_id' => $this->talent_id,
            'full_name' => $this->talent_post->post_title,
            'style_description' => $this->talent_post->post_content,
            
            // Profile basics
            'state' => get_post_meta($this->talent_id, '_talent_state', true),
            'country' => get_post_meta($this->talent_id, '_talent_country', true),
            'email' => get_post_meta($this->talent_id, '_talent_email', true),
            'phone' => get_post_meta($this->talent_id, '_talent_phone', true),
            'country_code' => get_post_meta($this->talent_id, '_talent_country_code', true),
            'age_group' => get_post_meta($this->talent_id, '_talent_age_group', true),
            'gender' => get_post_meta($this->talent_id, '_talent_gender', true),
            'height' => get_post_meta($this->talent_id, '_talent_height', true),
            'height_unit' => get_post_meta($this->talent_id, '_talent_height_unit', true),
            'weight' => get_post_meta($this->talent_id, '_talent_weight', true),
            'weight_unit' => get_post_meta($this->talent_id, '_talent_weight_unit', true),
            'complexion' => get_post_meta($this->talent_id, '_talent_complexion', true),
            'bust_size' => get_post_meta($this->talent_id, '_talent_bust_size', true),
            'hair_color' => get_post_meta($this->talent_id, '_talent_hair_color', true),
            'dress_size' => get_post_meta($this->talent_id, '_talent_dress_size', true),
            'shirt_size' => get_post_meta($this->talent_id, '_talent_shirt_size', true),
            'measurements' => get_post_meta($this->talent_id, '_talent_measurements', true),
            'languages' => $this->get_array_meta('_talent_languages'),
            'agency_name' => get_post_meta($this->talent_id, '_talent_agency_name', true),
            
            // Experience
            'years_active' => get_post_meta($this->talent_id, '_talent_years_active', true),
            'affiliation' => get_post_meta($this->talent_id, '_talent_affiliation', true),
            'education' => get_post_meta($this->talent_id, '_talent_education', true),
            'interested_projects' => get_post_meta($this->talent_id, '_talent_interested_projects', true),
            
            // Portfolio Summary
            'notable_works' => $this->get_array_meta('_talent_notable_works'),
            
            // Availability & Preferences
            'available_for' => $this->get_array_meta('_talent_available_for'),
            'willing_to_travel' => get_post_meta($this->talent_id, '_talent_willing_to_travel', true),
            'preferred_locations' => get_post_meta($this->talent_id, '_talent_preferred_locations', true),
            'brand_collabs' => get_post_meta($this->talent_id, '_talent_brand_collabs', true),
            
            // Domain & Role
            'domain' => get_post_meta($this->talent_id, '_talent_domain', true),
            'role' => get_post_meta($this->talent_id, '_talent_role', true),
            
            // Role-specific details
            'design_categories' => $this->get_array_meta('_talent_designCategories'),
            
            // Social Links
            'instagram' => get_post_meta($this->talent_id, '_talent_instagram', true),
            'tiktok' => get_post_meta($this->talent_id, '_talent_tiktok', true),
            'website' => get_post_meta($this->talent_id, '_talent_website', true),
            
            // Privacy settings
            'hide_email' => get_post_meta($this->talent_id, '_talent_hide_email', true),
            'hide_phone' => get_post_meta($this->talent_id, '_talent_hide_phone', true),
            'hide_instagram' => get_post_meta($this->talent_id, '_talent_hide_instagram', true),
            'hide_tiktok' => get_post_meta($this->talent_id, '_talent_hide_tiktok', true),
            'hide_website' => get_post_meta($this->talent_id, '_talent_hide_website', true),
            
            // Portfolio gallery
            'portfolio_images' => $this->get_array_meta('_talent_portfolio'),
            'portfolio_cover_choice' => $this->get_portfolio_cover_choice(),
            
            // Template settings
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('talent_update'),
            'theme_version' => defined('HELLO_ELEMENTOR_CHILD_VERSION') ? HELLO_ELEMENTOR_CHILD_VERSION : '1.0.0'
        );
        
        // Load required files
        $this->load_required_files();
    }
    
    /**
     * Get meta value as array
     */
    private function get_array_meta($key) {
        $value = isset($this->talent_meta[$key]) ? $this->talent_meta[$key] : array();
        
        // If not set, return empty array
        if (empty($value)) {
            return array();
        }
        
        // get_post_meta returns an array of values for this key
        // If it's a single value (not array), handle directly
        if (!is_array($value)) {
            $unserialized = maybe_unserialize($value);
            return is_array($unserialized) ? $unserialized : array($value);
        }
        
        // If it's an array with one element (most common case - WordPress auto-unserializes)
        if (count($value) === 1) {
            $first = reset($value);
            // Already unserialized by WordPress
            if (is_array($first)) {
                return $first;
            }
            // Still serialized - try to unserialize
            if (is_string($first)) {
                $unserialized = maybe_unserialize($first);
                if (is_array($unserialized)) {
                    return $unserialized;
                }
                // Could be comma-separated IDs
                if (strpos($first, ',') !== false) {
                    $ids = array_map('intval', explode(',', $first));
                    return array_filter($ids);
                }
                // Single ID
                return array(intval($first));
            }
            return array($first);
        }
        
        // Multiple values - find first array
        foreach ($value as $item) {
            if (is_array($item)) {
                return $item;
            }
            if (is_string($item)) {
                $unserialized = maybe_unserialize($item);
                if (is_array($unserialized)) {
                    return $unserialized;
                }
            }
        }
        
        return array();
    }

    /**
     * Get the selected portfolio cover choice token for the edit form.
     *
     * @return string
     */
    private function get_portfolio_cover_choice() {
        $thumbnail_id = get_post_thumbnail_id($this->talent_id);
        $portfolio_images = $this->get_array_meta('_talent_portfolio');

        if (
            $thumbnail_id &&
            in_array((int) $thumbnail_id, array_map('intval', $portfolio_images), true) &&
            wp_attachment_is_image($thumbnail_id)
        ) {
            return 'existing:' . (int) $thumbnail_id;
        }

        return '';
    }
    
    /**
     * Load required files for template
     */
    private function load_required_files() {
        if (!function_exists('get_domains')) {
            require_once get_stylesheet_directory() . '/roles-config.php';
        }
        
        if (!function_exists('render_role_fields_consistent')) {
            require_once get_stylesheet_directory() . '/includes/role-fields-loader.php';
        }
        
        if (!function_exists('render_domain_cards')) {
            require_once get_stylesheet_directory() . '/includes/role-fields-loader.php';
        }
        
        if (!function_exists('render_role_cards')) {
            require_once get_stylesheet_directory() . '/includes/role-fields-loader.php';
        }
    }
    
    /**
     * Setup template data
     */
    private function setup_template_data() {
        // State is already populated in load_talent_data()
    }
    
    /**
     * Get state value
     */
    public function get($key) {
        return isset($this->state[$key]) ? $this->state[$key] : '';
    }
    
    /**
     * Get entire state array
     */
    public function get_state() {
        return $this->state;
    }
    
    /**
     * Get talent ID
     */
    public function get_talent_id() {
        return $this->talent_id;
    }
    
    /**
     * Check if user has a profile photo
     */
    public function has_profile_photo() {
        return has_post_thumbnail($this->talent_id);
    }
    
    /**
     * Get profile photo thumbnail
     */
    public function get_profile_photo() {
        if (has_post_thumbnail($this->talent_id)) {
            return get_the_post_thumbnail($this->talent_id, 'thumbnail', array('id' => 'currentPhoto'));
        }
        return '';
    }
    
    /**
     * Get languages as comma-separated string
     */
    public function get_languages_string() {
        $languages = $this->get('languages');
        if (is_array($languages)) {
            return implode(', ', $languages);
        }
        return '';
    }
    
    /**
     * Get roles that need physical details
     */
    public function roles_needing_physical_details() {
        return array('fashion-model', 'actor', 'dancer', 'ramp-choreographer', 'model-development-coach', 'runway-coach');
    }
    
    /**
     * Check if current role needs physical details
     */
    public function needs_physical_details() {
        $role = $this->get('role');
        return in_array($role, $this->roles_needing_physical_details());
    }
    
    /**
     * Check if domain should be shown
     */
    public function show_role_category($domain_key) {
        $domain = $this->get('domain');
        return $domain === $domain_key;
    }
    
    /**
     * Get AJAX URL
     */
    public function get_ajax_url() {
        return $this->state['ajax_url'];
    }
    
    /**
     * Get nonce
     */
    public function get_nonce() {
        return $this->state['nonce'];
    }
    
    /**
     * Get theme version
     */
    public function get_theme_version() {
        return $this->state['theme_version'];
    }
}

/**
 * Initialize and get controller instance
 */
function talent_edit_controller() {
    return Talent_Edit_Controller::get_instance();
}

// Auto-initialize when file is loaded
talent_edit_controller();
