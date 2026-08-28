<?php
/**
 * Talent Submission Controller
 * 
 * Handles all PHP logic for the talent submission template
 * Separates logic from presentation for better maintainability
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Talent_Submission_Controller Class
 * 
 * Handles all server-side logic for talent submission form
 */
class Talent_Submission_Controller {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Controller state data to pass to template
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
        $this->setup_template_data();
    }
    
    /**
     * Check user access permissions
     * Handles login check, membership plan check, and existing profile redirect
     */
    private function check_access_permissions() {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_redirect(home_url('/membership-login/'));
            exit;
        }
        
        // Ensure access control functions are available
        $this->ensure_access_control_loaded();
        
        // Check membership plan access
        if (!$this->has_talent_access()) {
            $this->show_access_restricted();
            exit;
        }
        
        // Check if user already has a talent profile - redirect to edit
        $this->check_existing_profile_redirect();
    }
    
    /**
     * Ensure access control functions are available
     */
    private function ensure_access_control_loaded() {
        if (!function_exists('has_membership_plan')) {
            require_once get_stylesheet_directory() . '/includes/access-control.php';
        }
        
        // Ensure user's plan tracking is properly initialized
        if (function_exists('ensure_user_plan_tracking')) {
            ensure_user_plan_tracking();
        }
        
        // Migrate current level for backward compatibility
        if (function_exists('migrate_user_current_level_to_plans')) {
            migrate_user_current_level_to_plans();
        }
    }
    
    /**
     * Check if user has talent access
     * Allows any logged-in user with simple login to access talent submission
     */
    private function has_talent_access() {
        return is_user_logged_in();
    }
    
    /**
     * Show access restricted message and exit
     */
    private function show_access_restricted() {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Access Restricted</title>
            <link rel="stylesheet"
                href="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/form-style.css?ver=<?php echo esc_attr(HELLO_ELEMENTOR_CHILD_VERSION); ?>">
            <link rel="stylesheet"
                href="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/global.css?ver=<?php echo esc_attr(HELLO_ELEMENTOR_CHILD_VERSION); ?>">
        </head>
        <body data-theme="dark" class="bg-grade">
            <div class="success-container">
                <div class="success-card">
                    <div class="success-icon restricted">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h2 class="success-title">Access Restricted</h2>
                    <p class="success-message">This page is only accessible to users with membership level 2, 3, or 4. Please
                        upgrade your membership to access the talent submission form.</p>
                    <a href="<?php echo esc_url(home_url('/membership-join/')); ?>" class="btn btn-primary">Join Membership</a>
                </div>
            </div>
            <script
                src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/global.js?ver=<?php echo esc_attr(HELLO_ELEMENTOR_CHILD_VERSION); ?>"></script>
        </body>
        </html>
        <?php
    }
    
    /**
     * Check if user already has a talent profile and redirect to edit page
     */
    private function check_existing_profile_redirect() {
        // Check if function exists and user has profile
        if (!function_exists('user_has_talent_profile') || !user_has_talent_profile()) {
            return;
        }
        
        // Get the user's talent profile
        $user_id = get_current_user_id();
        $args = array(
            'post_type' => 'talent',
            'author' => $user_id,
            'post_status' => array('publish', 'pending', 'draft'),
            'posts_per_page' => 1
        );
        
        $talent_posts = get_posts($args);
        
        // If user has a profile, redirect to edit page
        if (!empty($talent_posts)) {
            $talent_id = $talent_posts[0]->ID;
            
            // Find the page that uses the talent edit template
            $edit_pages = get_pages(array(
                'meta_key' => '_wp_page_template',
                'meta_value' => 'template-talent-edit.php'
            ));
            
            if (!empty($edit_pages)) {
                $edit_page = $edit_pages[0];
                $redirect_url = get_permalink($edit_page->ID) . '?talent_id=' . $talent_id;
                
                // Add a check to prevent infinite loops
                if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'talent-edit') !== false) {
                    // If we're coming from the edit page, don't redirect again
                    return;
                }
                
                wp_redirect($redirect_url);
                exit;
            }
        }
    }
    
    /**
     * Setup data needed for the template
     */
    private function setup_template_data() {
        // Load required files
        $this->load_required_files();
        
        // Set up state for template
        $this->state = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('talent_submission'),
            'is_edit' => false,
            'draft' => null,
            'form_values' => $this->get_form_values_from_draft(),
            'theme_version' => defined('HELLO_ELEMENTOR_CHILD_VERSION') ? HELLO_ELEMENTOR_CHILD_VERSION : '1.0.0'
        );
    }

    /**
     * Build initial field values from the latest draft, if one exists.
     *
     * @return array
     */
    private function get_form_values_from_draft() {
        if (!function_exists('get_user_draft_profile')) {
            return array();
        }

        $draft_post = get_user_draft_profile();
        if (!$draft_post) {
            return array();
        }

        $languages = get_post_meta($draft_post->ID, '_talent_languages', true);
        if (is_array($languages)) {
            $languages = implode(', ', array_filter(array_map('sanitize_text_field', $languages)));
        }

        return array(
            'fullName' => $draft_post->post_title,
            'city' => get_post_meta($draft_post->ID, '_talent_state', true),
            'country' => get_post_meta($draft_post->ID, '_talent_country', true),
            'email' => get_post_meta($draft_post->ID, '_talent_email', true),
            'phone' => get_post_meta($draft_post->ID, '_talent_phone', true),
            'countryCode' => get_post_meta($draft_post->ID, '_talent_country_code', true),
            'hideEmail' => get_post_meta($draft_post->ID, '_talent_hide_email', true),
            'hidePhone' => get_post_meta($draft_post->ID, '_talent_hide_phone', true),
            'ageGroup' => get_post_meta($draft_post->ID, '_talent_age_group', true),
            'gender' => get_post_meta($draft_post->ID, '_talent_gender', true),
            'languages' => $languages,
            'yearsActive' => get_post_meta($draft_post->ID, '_talent_years_active', true),
            'affiliation' => get_post_meta($draft_post->ID, '_talent_affiliation', true),
            'agencyName' => get_post_meta($draft_post->ID, '_talent_agency_name', true),
            'education' => get_post_meta($draft_post->ID, '_talent_education', true),
        );
    }
    
    /**
     * Load required files for template
     */
    private function load_required_files() {
        // Only load if not already available
        if (!function_exists('get_domains')) {
            require_once get_stylesheet_directory() . '/roles-config.php';
        }
        
        if (!function_exists('render_role_fields_consistent')) {
            require_once get_stylesheet_directory() . '/includes/role-fields-loader.php';
        }
        
        if (!function_exists('render_all_role_fields_clean')) {
            require_once get_stylesheet_directory() . '/templates/roles/role-fields-template.php';
        }
    }
    
    /**
     * Get controller state for template
     */
    public function get_state() {
        return $this->state;
    }
    
    /**
     * Get AJAX URL for JavaScript
     */
    public function get_ajax_url() {
        return $this->state['ajax_url'];
    }
    
    /**
     * Get nonce for form submission
     */
    public function get_nonce() {
        return $this->state['nonce'];
    }
    
    /**
     * Get theme version for asset loading
     */
    public function get_theme_version() {
        return $this->state['theme_version'];
    }
    
    /**
     * Check if this is an edit page
     */
    public function is_edit_mode() {
        return $this->state['is_edit'];
    }

    /**
     * Get initial form values for server-rendered template parts.
     *
     * @return array
     */
    public function get_form_values() {
        return isset($this->state['form_values']) && is_array($this->state['form_values'])
            ? $this->state['form_values']
            : array();
    }
}

/**
 * Initialize and get controller instance
 */
function talent_submission_controller() {
    return Talent_Submission_Controller::get_instance();
}

// Auto-initialize when file is loaded
talent_submission_controller();
