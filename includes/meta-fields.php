<?php
/**
 * Custom meta fields registration
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom meta fields for the "Talent" post type to make them available to the REST API and Elementor.
 */
function register_talent_meta_fields() {
    $meta_fields = [
        '_talent_state' => 'string',
        '_talent_country' => 'string',
        '_talent_email' => 'string',
        '_talent_phone' => 'string',
        '_talent_age_group' => 'string',
        '_talent_gender' => 'string',
        '_talent_height' => 'string',
        '_talent_height_unit' => 'string',
        '_talent_weight' => 'string',
        '_talent_weight_unit' => 'string',
        '_talent_complexion' => 'string',
        '_talent_bust_size' => 'string',
        '_talent_hair_color' => 'string',
        '_talent_dress_size' => 'string',
        '_talent_shirt_size' => 'string',
        '_talent_measurements' => 'string',
        '_talent_years_active' => 'string',
        '_talent_affiliation' => 'string',
        '_talent_agency_name' => 'string',
        '_talent_education' => 'string',
        '_talent_interested_projects' => 'string',
        '_talent_willing_to_travel' => 'string',
        '_talent_preferred_locations' => 'string',
        '_talent_brand_collabs' => 'string',
        '_talent_domain' => 'string',
        '_talent_role' => 'string',
        '_talent_instagram' => 'string',
        '_talent_youtube' => 'string',
        '_talent_website' => 'string',
        '_talent_profile_clicks' => 'integer',
    ];

    // Add YouTube to the meta fields
    $meta_fields['_talent_youtube'] = 'string';
    $meta_fields['_talent_hide_youtube'] = 'string';

    foreach ($meta_fields as $meta_key => $type) {
        register_post_meta('talent', $meta_key, [
            'show_in_rest' => true,
            'single' => true,
            'type' => $type,
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
    }

    // Register array-based meta fields
    // String arrays
    $string_array_meta_fields = [
        '_talent_languages',
        '_talent_available_for',
        '_talent_designCategories',
        '_talent_portfolio',
    ];

    foreach ($string_array_meta_fields as $meta_key) {
        register_post_meta('talent', $meta_key, [
            'show_in_rest' => [
                'schema' => [
                    'type'  => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
            ],
            'single' => true,
            'type' => 'array',
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);
    }

    // Object arrays (notable works)
    register_post_meta('talent', '_talent_notable_works', [
        'show_in_rest' => [
            'schema' => [
                'type'  => 'array',
                'items' => [
                    'type'       => 'object',
                    'properties' => [
                        'title'       => ['type' => 'string'],
                        'role'        => ['type' => 'string'],
                        'startDate'   => ['type' => 'string'],
                        'endDate'     => ['type' => 'string'],
                        'present'     => ['type' => 'string'],
                        'description' => ['type' => 'string'],
                    ],
                ],
            ],
        ],
        'single' => true,
        'type' => 'array',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        }
    ]);

    // Public sharing meta fields
    register_post_meta('talent', '_talent_shareable_token', [
        'show_in_rest' => false,
        'single' => true,
        'type' => 'string',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        }
    ]);

    register_post_meta('talent', '_talent_public_sharing_enabled', [
        'show_in_rest' => false,
        'single' => true,
        'type' => 'boolean',
        'default' => false,
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        }
    ]);
}
add_action('init', 'register_talent_meta_fields');

/**
 * Check if a user already has a talent profile
 *
 * @param int $user_id The user ID to check
 * @param bool $include_drafts Whether to include draft posts in the check
 * @return bool True if user has a talent profile, false otherwise
 */
function user_has_talent_profile($user_id = null, $include_drafts = true) {
    // If no user ID provided, use current user
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    // If no user is logged in, return false
    if (!$user_id) {
        return false;
    }

    $post_statuses = array('publish', 'pending');
    if ($include_drafts) {
        $post_statuses[] = 'draft';
    }

    // Query for talent posts by this user
    $args = array(
        'post_type' => 'talent',
        'author' => $user_id,
        'post_status' => $post_statuses,
        'posts_per_page' => 1
    );

    $talent_posts = get_posts($args);

    // Return true if any talent posts found, false otherwise
    return !empty($talent_posts);
}

/**
 * Get the user's draft talent profile if it exists
 *
 * @param int $user_id The user ID to check
 * @return WP_Post|null The draft post object or null
 */
function get_user_draft_profile($user_id = null) {
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    if (!$user_id) {
        return null;
    }

    $args = array(
        'post_type' => 'talent',
        'author' => $user_id,
        'post_status' => 'draft',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $posts = get_posts($args);
    return !empty($posts) ? $posts[0] : null;
}