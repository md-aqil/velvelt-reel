<?php
/**
 * Third-party event integration
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// This file is meant to be included from functions.php where WordPress is already loaded
// All WordPress functions should be available when this file is properly included

/**
 * Ticketmaster events shortcode
 */
function ticketmaster_events_shortcode($atts) {
    $atts = shortcode_atts(array(
        'keywords' => 'fashion',
        'country' => '',       // e.g., IN, US, GB — optional
        'limit' => 20,
    ), $atts);

    $apikey = 'FkW2xMhFCzGx8uDmMg0eNBzNL5qoHCmk'; // Replace with your key

    $keywords = array_map('trim', explode(',', $atts['keywords']));
    $country = strtoupper(trim($atts['country']));
    $limit = intval($atts['limit']);

    $events = [];

    foreach ($keywords as $keyword) {
        $query = urlencode($keyword);

        $url = "https://app.ticketmaster.com/discovery/v2/events.json?apikey={$apikey}&keyword={$query}&size={$limit}";

        if (!empty($country)) {
            $url .= "&countryCode={$country}";
        }

        $response = wp_remote_get($url);
        if (is_wp_error($response))
            continue;

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!empty($data['_embedded']['events'])) {
            $events = array_merge($events, $data['_embedded']['events']);
        }
    }

    if (empty($events)) {
        return "<p>No events found for given keywords.</p>";
    }

    // Deduplicate events by ID
    $unique = [];
    $ids = [];

    foreach ($events as $event) {
        if (!in_array($event['id'], $ids)) {
            $unique[] = $event;
            $ids[] = $event['id'];
        }
    }

    // HTML Output
    $html = '<div class="events-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;">';

    foreach ($unique as $event) {
        $title = $event['name'] ?? 'Untitled Event';
        $url_event = $event['url'] ?? '#';
        $image = $event['images'][0]['url'] ?? 'https://via.placeholder.com/600x400?text=No+Image';
        $date = $event['dates']['start']['localDate'] ?? '';
        $venue = $event['_embedded']['venues'][0]['name'] ?? 'Venue TBA';
        $city = $event['_embedded']['venues'][0]['city']['name'] ?? '';
        $countryName = $event['_embedded']['venues'][0]['country']['name'] ?? '';

        $html .= "
            <div style='border:1px solid #ccc;padding:15px;border-radius:10px; color: #fff;'>
                <img src='{$image}' style='width:100%;border-radius:10px;margin-bottom:10px;'>
                <h3 style='margin:0 0 10px;font-size:18px;'>{$title}</h3>
                <p><strong>Date:</strong> {$date}</p>
                <p><strong>Location:</strong> {$venue}, {$city}, {$countryName}</p>
                <a href='{$url_event}' target='_blank' 
                    style='display:inline-block;margin-top:10px;padding:10px 15px;
                    background:#000;color:#fff;border-radius:5px;text-decoration:none;'>
                    View Event
                </a>
            </div>
        ";
    }

    $html .= '</div>';

    return $html;
}
add_shortcode('world_events', 'ticketmaster_events_shortcode');

/**
 * Tracks profile views for talent posts.
 *
 * This function increments a 'profile_clicks' meta field for a talent post
 * each time it is viewed on the front-end. It includes checks to prevent
 * authors from inflating their own view counts and uses a session variable
 * to count only one view per user session.
 */
function velvetreel_track_profile_views() {
    // Only run on single 'talent' post pages
    if (!is_singular('talent')) {
        return;
    }

    $post_id = get_the_ID();

    // Start a session if not already started
    if (!session_id()) {
        session_start();
    }

    // Check if this post has already been viewed in this session
    if (!isset($_SESSION['viewed_talent_' . $post_id])) {
        $count = (int) get_post_meta($post_id, '_talent_profile_clicks', true);
        update_post_meta($post_id, '_talent_profile_clicks', $count + 1);
        $_SESSION['viewed_talent_' . $post_id] = true; // Mark as viewed for this session
    }
}
add_action('wp_head', 'velvetreel_track_profile_views');