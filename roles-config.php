<?php
/**
 * Domain and Role Configuration
 * 
 * This file defines the structure of domains and roles for the talent submission form.
 * It allows for easy addition of new domains and roles without modifying the main template.
 */

// Define the domains and their roles
$domains_and_roles = array(
    'fashion' => array(
        'name' => 'Fashion & Design',
        'description' => 'Designers, Models, Stylists, and Fashion Creatives',
        'icon' => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line>',
        'roles' => array(
            'fashion-designer' => array(
                'name' => 'Fashion Designer',
                'description' => 'Create clothing & collections',
                'categories' => array(
                    'Menswear' => 'Menswear',
                    'Womenswear' => 'Womenswear',
                    'Kidswear' => 'Kidswear',
                    'Unisex' => 'Unisex'
                )
            ),
            'textile-designer' => array(
                'name' => 'Textile Designer',
                'description' => 'Print & surface design',
                'categories' => array(
                    'Apparel Textiles' => 'Apparel Textiles',
                    'Home Furnishing' => 'Home Furnishing',
                    'Technical Textiles' => 'Technical Textiles',
                    'Sustainable Materials' => 'Sustainable Materials'
                )
            ),
            'accessory-designer' => array(
                'name' => 'Accessory Designer',
                'description' => 'Bags, shoes, jewelry',
                'categories' => array(
                    'Jewelry' => 'Jewelry',
                    'Bags & Handbags' => 'Bags & Handbags',
                    'Shoes & Footwear' => 'Shoes & Footwear',
                    'Watches & Accessories' => 'Watches & Accessories'
                )
            ),
            'fashion-illustrator' => array(
                'name' => 'Fashion Illustrator',
                'description' => 'Sketches & visual concepts',
                'categories' => array(
                    'Fashion Sketches' => 'Fashion Sketches',
                    'Technical Drawings' => 'Technical Drawings',
                    'Digital Illustrations' => 'Digital Illustrations',
                    'Concept Art' => 'Concept Art'
                )
            ),
            'fashion-model' => array(
                'name' => 'Fashion Model',
                'description' => 'Runway, print, commercial',
                'categories' => array(
                    'Runway' => 'Runway',
                    'Editorial' => 'Editorial',
                    'Commercial' => 'Commercial',
                    'Fitness & Lifestyle' => 'Fitness & Lifestyle'
                )
            ),
            'ramp-choreographer' => array(
                'name' => 'Ramp Choreographer',
                'description' => 'Show direction & staging',
                'categories' => array(
                    'Fashion Shows' => 'Fashion Shows',
                    'Music Videos' => 'Music Videos',
                    'Commercial Events' => 'Commercial Events',
                    'Wedding Choreography' => 'Wedding Choreography'
                )
            ),
            'fashion-stylist' => array(
                'name' => 'Fashion Stylist',
                'description' => 'Editorial & personal styling',
                'categories' => array(
                    'Editorial Styling' => 'Editorial Styling',
                    'Personal Styling' => 'Personal Styling',
                    'Celebrity Styling' => 'Celebrity Styling',
                    'Commercial Styling' => 'Commercial Styling'
                )
            ),
            'makeup-artist' => array(
                'name' => 'Makeup Artist',
                'description' => 'Bridal, film, editorial',
                'categories' => array(
                    'Bridal Makeup' => 'Bridal Makeup',
                    'Editorial Makeup' => 'Editorial Makeup',
                    'Special Effects' => 'Special Effects',
                    'Beauty & Cosmetics' => 'Beauty & Cosmetics'
                )
            )
        )
    ),
    'film' => array(
        'name' => 'Film & Creative Arts',
        'description' => 'Directors, Actors, Crew, and Production Specialists',
        'icon' => '<rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line>',
        'roles' => array(
            'director' => array(
                'name' => 'Director',
                'description' => 'Film & creative direction',
                'categories' => array(
                    'Feature Films' => 'Feature Films',
                    'Short Films' => 'Short Films',
                    'Documentaries' => 'Documentaries',
                    'Commercials' => 'Commercials'
                )
            ),
            'assistant-director' => array(
                'name' => 'Assistant Director',
                'description' => 'Supporting director in film production',
                'categories' => array(
                    'Feature Films' => 'Feature Films',
                    'Short Films' => 'Short Films',
                    'Documentaries' => 'Documentaries',
                    'Commercials' => 'Commercials'
                )
            ),
            'screenwriter' => array(
                'name' => 'Screenwriter',
                'description' => 'Script and story development',
                'categories' => array(
                    'Feature Films' => 'Feature Films',
                    'Short Films' => 'Short Films',
                    'Documentaries' => 'Documentaries',
                    'TV Scripts' => 'TV Scripts'
                )
            ),
            'storyboard-artist' => array(
                'name' => 'Storyboard Artist',
                'description' => 'Visual script planning',
                'categories' => array(
                    'Film Storyboards' => 'Film Storyboards',
                    'Animation Storyboards' => 'Animation Storyboards',
                    'Advertising Boards' => 'Advertising Boards',
                    'Game Design' => 'Game Design'
                )
            ),
            'actor' => array(
                'name' => 'Actor/Actress',
                'description' => 'Film, TV, theatre',
                'categories' => array(
                    'Film Acting' => 'Film Acting',
                    'Theater Acting' => 'Theater Acting',
                    'TV Series' => 'TV Series',
                    'Voice Acting' => 'Voice Acting'
                )
            ),
            'voice-actor' => array(
                'name' => 'Voice Actor',
                'description' => 'Dubbing & narration',
                'categories' => array(
                    'Animation Voices' => 'Animation Voices',
                    'Audiobook Narration' => 'Audiobook Narration',
                    'Commercial Voiceovers' => 'Commercial Voiceovers',
                    'Video Game Voices' => 'Video Game Voices'
                )
            ),
            'dancer' => array(
                'name' => 'Dancer/Choreographer',
                'description' => 'Performance & teaching',
                'categories' => array(
                    'Ballet' => 'Ballet',
                    'Contemporary' => 'Contemporary',
                    'Hip Hop' => 'Hip Hop',
                    'Classical Dance' => 'Classical Dance'
                )
            ),
            'dop' => array(
                'name' => 'DOP/Camera Crew',
                'description' => 'Cinematography & lighting',
                'categories' => array(
                    'Cinematography' => 'Cinematography',
                    'Drone Photography' => 'Drone Photography',
                    'Studio Photography' => 'Studio Photography',
                    'Event Coverage' => 'Event Coverage'
                )
            ),
            'editor' => array(
                'name' => 'Editor/VFX Artist',
                'description' => 'Post-production specialist',
                'categories' => array(
                    'Video Editing' => 'Video Editing',
                    'Color Grading' => 'Color Grading',
                    'Visual Effects' => 'Visual Effects',
                    'Motion Graphics' => 'Motion Graphics'
                )
            ),
            'music-director' => array(
                'name' => 'Music Director',
                'description' => 'Compose and conduct music for films',
                'categories' => array(
                    'Film Music' => 'Film Music',
                    'Background Score' => 'Background Score',
                    'Song Composition' => 'Song Composition',
                    'Orchestration' => 'Orchestration'
                )
            ),
            'singer' => array(
                'name' => 'Singer',
                'description' => 'Vocalist for films and recordings',
                'categories' => array(
                    'Playback Singing' => 'Playback Singing',
                    'Original Songs' => 'Original Songs',
                    'Live Performances' => 'Live Performances',
                    'Dubbing' => 'Dubbing'
                )
            )
        )
    ),
    'coach' => array(
        'name' => 'Talent Coach',
        'description' => 'Coaches and develops various types of talent',
        'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
        'roles' => array(
            'runway-coach' => array(
                'name' => 'Runway Coach',
                'description' => 'Expert in runway walking techniques',
                'categories' => array(
                    'High Fashion Runway Walk' => 'High Fashion Runway Walk',
                    'Commercial Runway Walk' => 'Commercial Runway Walk',
                    'Streetwear Runway Walk' => 'Streetwear Runway Walk',
                    'Bridal Runway Walk' => 'Bridal Runway Walk'
                )
            ),
            'model-development-coach' => array(
                'name' => 'Model Development Coach',
                'description' => 'Develops modeling skills and professionalism',
                'categories' => array(
                    'Runway Development' => 'Runway Development',
                    'Professional Development' => 'Professional Development',
                    'Posing Development' => 'Posing Development'
                )
            ),
            'acting-coach' => array(
                'name' => 'Acting Coach',
                'description' => 'Trains actors in various acting techniques',
                'categories' => array(
                    'On-camera Acting Coach' => 'On-camera Acting Coach',
                    'Scene Study Coach' => 'Scene Study Coach',
                    'Movement Coach' => 'Movement Coach'
                )
            ),
            'voice-diction-coach' => array(
                'name' => 'Voice and Diction Coach',
                'description' => 'Specializes in voice and speech training',
                'categories' => array(
                    'Acting and Film Voice Coach' => 'Acting and Film Voice Coach',
                    'Public Speaking and Hosting' => 'Public Speaking and Hosting',
                    'Singing Based Diction Coach' => 'Singing Based Diction Coach',
                    'Accent and Dialect Coach' => 'Accent and Dialect Coach'
                )
            )
        )
    )
);

// Function to get all domains
function get_domains() {
    global $domains_and_roles;
    
    // If the global variable is not set or empty, try to define it
    if (!isset($domains_and_roles) || empty($domains_and_roles)) {
        // Re-defining the domains structure here as fallback
        $fallback_domains = array(
            'fashion' => array(
                'name' => 'Fashion & Design',
                'description' => 'Designers, Models, Stylists, and Fashion Creatives',
                'icon' => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line>',
                'roles' => array(
                    'fashion-designer' => array(
                        'name' => 'Fashion Designer',
                        'description' => 'Create clothing & collections',
                        'categories' => array(
                            'Menswear' => 'Menswear',
                            'Womenswear' => 'Womenswear',
                            'Kidswear' => 'Kidswear',
                            'Unisex' => 'Unisex'
                        )
                    ),
                    'textile-designer' => array(
                        'name' => 'Textile Designer',
                        'description' => 'Print & surface design',
                        'categories' => array(
                            'Apparel Textiles' => 'Apparel Textiles',
                            'Home Furnishing' => 'Home Furnishing',
                            'Technical Textiles' => 'Technical Textiles',
                            'Sustainable Materials' => 'Sustainable Materials'
                        )
                    ),
                    'accessory-designer' => array(
                        'name' => 'Accessory Designer',
                        'description' => 'Bags, shoes, jewelry',
                        'categories' => array(
                            'Jewelry' => 'Jewelry',
                            'Bags & Handbags' => 'Bags & Handbags',
                            'Shoes & Footwear' => 'Shoes & Footwear',
                            'Watches & Accessories' => 'Watches & Accessories'
                        )
                    ),
                    'fashion-illustrator' => array(
                        'name' => 'Fashion Illustrator',
                        'description' => 'Sketches & visual concepts',
                        'categories' => array(
                            'Fashion Sketches' => 'Fashion Sketches',
                            'Technical Drawings' => 'Technical Drawings',
                            'Digital Illustrations' => 'Digital Illustrations',
                            'Concept Art' => 'Concept Art'
                        )
                    ),
                    'fashion-model' => array(
                        'name' => 'Fashion Model',
                        'description' => 'Runway, print, commercial',
                        'categories' => array(
                            'Runway' => 'Runway',
                            'Editorial' => 'Editorial',
                            'Commercial' => 'Commercial',
                            'Fitness & Lifestyle' => 'Fitness & Lifestyle'
                        )
                    ),
                    'ramp-choreographer' => array(
                        'name' => 'Ramp Choreographer',
                        'description' => 'Show direction & staging',
                        'categories' => array(
                            'Fashion Shows' => 'Fashion Shows',
                            'Music Videos' => 'Music Videos',
                            'Commercial Events' => 'Commercial Events',
                            'Wedding Choreography' => 'Wedding Choreography'
                        )
                    ),
                    'fashion-stylist' => array(
                        'name' => 'Fashion Stylist',
                        'description' => 'Editorial & personal styling',
                        'categories' => array(
                            'Editorial Styling' => 'Editorial Styling',
                            'Personal Styling' => 'Personal Styling',
                            'Celebrity Styling' => 'Celebrity Styling',
                            'Commercial Styling' => 'Commercial Styling'
                        )
                    ),
                    'makeup-artist' => array(
                        'name' => 'Makeup Artist',
                        'description' => 'Bridal, film, editorial',
                        'categories' => array(
                            'Bridal Makeup' => 'Bridal Makeup',
                            'Editorial Makeup' => 'Editorial Makeup',
                            'Special Effects' => 'Special Effects',
                            'Beauty & Cosmetics' => 'Beauty & Cosmetics'
                        )
                    )
                )
            ),
            'film' => array(
                'name' => 'Film & Creative Arts',
                'description' => 'Directors, Actors, Crew, and Production Specialists',
                'icon' => '<rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line><line x1="2" y1="12" x2="22" y2="12"></line>',
                'roles' => array(
                    'director' => array(
                        'name' => 'Director',
                        'description' => 'Film & creative direction',
                        'categories' => array(
                            'Feature Films' => 'Feature Films',
                            'Short Films' => 'Short Films',
                            'Documentaries' => 'Documentaries',
                            'Commercials' => 'Commercials'
                        )
                    ),
                    'assistant-director' => array(
                        'name' => 'Assistant Director',
                        'description' => 'Supporting director in film production',
                        'categories' => array(
                            'Feature Films' => 'Feature Films',
                            'Short Films' => 'Short Films',
                            'Documentaries' => 'Documentaries',
                            'Commercials' => 'Commercials'
                        )
                    ),
                    'screenwriter' => array(
                        'name' => 'Screenwriter',
                        'description' => 'Script and story development',
                        'categories' => array(
                            'Feature Films' => 'Feature Films',
                            'Short Films' => 'Short Films',
                            'Documentaries' => 'Documentaries',
                            'TV Scripts' => 'TV Scripts'
                        )
                    ),
                    'storyboard-artist' => array(
                        'name' => 'Storyboard Artist',
                        'description' => 'Visual script planning',
                        'categories' => array(
                            'Film Storyboards' => 'Film Storyboards',
                            'Animation Storyboards' => 'Animation Storyboards',
                            'Advertising Boards' => 'Advertising Boards',
                            'Game Design' => 'Game Design'
                        )
                    ),
                    'actor' => array(
                        'name' => 'Actor/Actress',
                        'description' => 'Film, TV, theatre',
                        'categories' => array(
                            'Film Acting' => 'Film Acting',
                            'Theater Acting' => 'Theater Acting',
                            'TV Series' => 'TV Series',
                            'Voice Acting' => 'Voice Acting'
                        )
                    ),
                    'voice-actor' => array(
                        'name' => 'Voice Actor',
                        'description' => 'Dubbing & narration',
                        'categories' => array(
                            'Animation Voices' => 'Animation Voices',
                            'Audiobook Narration' => 'Audiobook Narration',
                            'Commercial Voiceovers' => 'Commercial Voiceovers',
                            'Video Game Voices' => 'Video Game Voices'
                        )
                    ),
                    'dancer' => array(
                        'name' => 'Dancer/Choreographer',
                        'description' => 'Performance & teaching',
                        'categories' => array(
                            'Ballet' => 'Ballet',
                            'Contemporary' => 'Contemporary',
                            'Hip Hop' => 'Hip Hop',
                            'Classical Dance' => 'Classical Dance'
                        )
                    ),
                    'dop' => array(
                        'name' => 'DOP/Camera Crew',
                        'description' => 'Cinematography & lighting',
                        'categories' => array(
                            'Cinematography' => 'Cinematography',
                            'Drone Photography' => 'Drone Photography',
                            'Studio Photography' => 'Studio Photography',
                            'Event Coverage' => 'Event Coverage'
                        )
                    ),
                    'editor' => array(
                        'name' => 'Editor/VFX Artist',
                        'description' => 'Post-production specialist',
                        'categories' => array(
                            'Video Editing' => 'Video Editing',
                            'Color Grading' => 'Color Grading',
                            'Visual Effects' => 'Visual Effects',
                            'Motion Graphics' => 'Motion Graphics'
                        )
                    ),
                    'music-director' => array(
                        'name' => 'Music Director',
                        'description' => 'Compose and conduct music for films',
                        'categories' => array(
                            'Film Music' => 'Film Music',
                            'Background Score' => 'Background Score',
                            'Song Composition' => 'Song Composition',
                            'Orchestration' => 'Orchestration'
                        )
                    ),
                    'singer' => array(
                        'name' => 'Singer',
                        'description' => 'Vocalist for films and recordings',
                        'categories' => array(
                            'Playback Singing' => 'Playback Singing',
                            'Original Songs' => 'Original Songs',
                            'Live Performances' => 'Live Performances',
                            'Dubbing' => 'Dubbing'
                        )
                    )
                )
            ),
            'coach' => array(
                'name' => 'Talent Coach',
                'description' => 'Coaches and develops various types of talent',
                'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
                'roles' => array(
                    'runway-coach' => array(
                        'name' => 'Runway Coach',
                        'description' => 'Expert in runway walking techniques',
                        'categories' => array(
                            'High Fashion Runway Walk' => 'High Fashion Runway Walk',
                            'Commercial Runway Walk' => 'Commercial Runway Walk',
                            'Streetwear Runway Walk' => 'Streetwear Runway Walk',
                            'Bridal Runway Walk' => 'Bridal Runway Walk'
                        )
                    ),
                    'model-development-coach' => array(
                        'name' => 'Model Development Coach',
                        'description' => 'Develops modeling skills and professionalism',
                        'categories' => array(
                            'Runway Development' => 'Runway Development',
                            'Professional Development' => 'Professional Development',
                            'Posing Development' => 'Posing Development'
                        )
                    ),
                    'acting-coach' => array(
                        'name' => 'Acting Coach',
                        'description' => 'Trains actors in various acting techniques',
                        'categories' => array(
                            'On-camera Acting Coach' => 'On-camera Acting Coach',
                            'Scene Study Coach' => 'Scene Study Coach',
                            'Movement Coach' => 'Movement Coach'
                        )
                    ),
                    'voice-diction-coach' => array(
                        'name' => 'Voice and Diction Coach',
                        'description' => 'Specializes in voice and speech training',
                        'categories' => array(
                            'Acting and Film Voice Coach' => 'Acting and Film Voice Coach',
                            'Public Speaking and Hosting' => 'Public Speaking and Hosting',
                            'Singing Based Diction Coach' => 'Singing Based Diction Coach',
                            'Accent and Dialect Coach' => 'Accent and Dialect Coach'
                        )
                    )
                )
            )
        );
        $domains_and_roles = $fallback_domains;
    }
    return $domains_and_roles;
}

// Function to get roles for a specific domain
function get_roles_for_domain($domain) {
    global $domains_and_roles;
    return isset($domains_and_roles[$domain]['roles']) ? $domains_and_roles[$domain]['roles'] : array();
}

// Function to get a specific role
function get_domain_role($domain, $role) {
    global $domains_and_roles;
    return isset($domains_and_roles[$domain]['roles'][$role]) ? $domains_and_roles[$domain]['roles'][$role] : null;
}

// Function to render domain cards
function render_domain_cards() {
    // Try to get domains through the function first
    $domains = get_domains();
    
    // Check if domains is defined and is an array
    if (!isset($domains) || !is_array($domains) || empty($domains)) {
        return; // Exit early if the variable is not properly set
    }
    
    foreach ($domains as $domain_key => $domain_data) {
        echo '<div class="domain-card" data-domain="' . esc_attr($domain_key) . '">';
        echo '<div class="domain-icon">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
        echo $domain_data['icon'];
        echo '</svg>';
        echo '</div>';
        echo '<h3>' . esc_html($domain_data['name']) . '</h3>';
        echo '<p>' . esc_html($domain_data['description']) . '</p>';
        echo '</div>';
    }
}

// Function to render role cards for a domain
function render_role_cards($domain) {
    // Try to get domains through the function first
    $domains = get_domains();
    
    if (!isset($domains[$domain])) {
        return;
    }
    
    $domain_data = $domains[$domain];
    $roles = $domain_data['roles'];
    
    // Check if roles is defined and is an array
    if (!isset($roles) || !is_array($roles)) {
        return; // Exit early if the roles array is not properly set
    }
    
    // Group roles by category for better organization
    $categories = array();
    foreach ($roles as $role_key => $role_data) {
        // For now, we'll put all roles in a single category
        // In the future, we could add more sophisticated categorization
        $category = 'Roles';
        if (!isset($categories[$category])) {
            $categories[$category] = array();
        }
        $categories[$category][$role_key] = $role_data;
    }
    
    foreach ($categories as $category_name => $category_roles) {
        echo '<h3>' . esc_html($category_name) . '</h3>';
        echo '<div class="role-grid">';
        foreach ($category_roles as $role_key => $role_data) {
            $role_key_escaped = htmlspecialchars($role_key, ENT_QUOTES, 'UTF-8');
            $name_escaped = htmlspecialchars($role_data['name'], ENT_QUOTES, 'UTF-8');
            $desc_escaped = htmlspecialchars($role_data['description'], ENT_QUOTES, 'UTF-8');
            
            echo '<div class="role-card" data-role-specific="' . $role_key_escaped . '">';
            echo '<h4>' . $name_escaped . '</h4>';
            echo '<p>' . $desc_escaped . '</p>';
            echo '</div>';
        }
        echo '</div>';
    }
}