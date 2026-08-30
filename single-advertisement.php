<?php
/**
 * The template for displaying single Advertisement posts with a premium, high-fidelity universal layout.
 * Parses and formats casting roles into premium visual cards, while maintaining a robust fallback for crew calls and rentals.
 *
 * @package HelloElementorChild
 */

get_header();

/**
 * Intelligent content parser to dynamically extract structured fields and role descriptions
 * from raw post content HTML, with seamless custom styling.
 */
if (!function_exists('velvet_parse_ad_content')) {
    function velvet_parse_ad_content($content) {
        $parsed = [
            'intro' => '',
            'location' => '',
            'duration' => '',
            'dates' => '',
            'compensation' => '',
            'language' => '',
            'roles' => [],
            'has_structure' => false,
            'remaining_content' => ''
        ];

        // Normalize text block delimiters
        $clean = preg_replace('/<p[^>]*>/i', '', $content);
        $clean = str_replace(['</p>', '<br />', '<br>', '&nbsp;'], "\n", $clean);
        $lines = explode("\n", $clean);
        
        $current_role_type = 'Lead'; // default to Lead unless Supporting is detected

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Strip HTML tags for clean regex matching but keep html_entity decoded
            $line_plain = trim(html_entity_decode(strip_tags($line)));
            $line_plain = preg_replace('/\s+/', ' ', $line_plain);

            // Detect current section headers to dynamically format roles
            if (stripos($line_plain, 'Lead Roles') !== false) {
                $current_role_type = 'Lead';
                continue;
            } elseif (stripos($line_plain, 'Supporting Roles') !== false) {
                $current_role_type = 'Supporting';
                continue;
            }

            // 1. Detect Intro text (first bold strong paragraph that doesn't define keys)
            if (empty($parsed['intro']) && (strpos($line, '<strong>') !== false || strpos($line, '<b>') !== false)) {
                if (stripos($line_plain, 'Shoot') === false && stripos($line_plain, 'Lead') === false && stripos($line_plain, 'Supporting') === false && stripos($line_plain, 'Location') === false && stripos($line_plain, 'Compensation') === false) {
                    $parsed['intro'] = $line_plain;
                    continue;
                }
            }

            // 2. Extract Sidebar fields with flexible regex (matches colons, hyphens, spaces, any case)
            if (preg_match('/^(?:Shoot\s+)?Location\s*[-–:]\s*(.*)/i', $line_plain, $matches)) {
                $parsed['location'] = trim($matches[1]);
                $parsed['has_structure'] = true;
                continue;
            }
            if (preg_match('/^(?:Shoot\s+)?Duration\s*[-–:]\s*(.*)/i', $line_plain, $matches)) {
                $parsed['duration'] = trim($matches[1]);
                $parsed['has_structure'] = true;
                continue;
            }
            if (preg_match('/^(?:Shoot\s+)?Dates?\s*[-–:]\s*(.*)/i', $line_plain, $matches)) {
                $parsed['dates'] = trim($matches[1]);
                $parsed['has_structure'] = true;
                continue;
            }
            if (preg_match('/^(?:Remuneration|Compensation|Budget|Pay|Stipend)\s*[-–:]\s*(.*)/i', $line_plain, $matches)) {
                $parsed['compensation'] = trim($matches[1]);
                $parsed['has_structure'] = true;
                continue;
            }
            if (preg_match('/^(?:Language|Languages)\s*[-–:]\s*(.*)/i', $line_plain, $matches)) {
                $parsed['language'] = trim($matches[1]);
                $parsed['has_structure'] = true;
                continue;
            }

            // 3. Extract Roles with advanced, highly robust parsing (matches Male/Female/Boy/Girl, age ranges, and descriptors)
            if (preg_match('/^(?:(Lead|Supporting)\s*[-–]\s*)?(Male|Female|Boys?|Girls?)\s*[-–]\s*([0-9\s-]+(?:\s*(?:yrs|years?|to))?\s*[0-9\s-]*\s*(?:yrs|years?)?)\s*(?:[,.-]\s*|\s+)(.*)/i', $line_plain, $matches)) {
                $role_type = !empty($matches[1]) ? trim($matches[1]) : $current_role_type;
                $gender = trim($matches[2]);
                $age = trim($matches[3]);
                $desc = trim($matches[4]);
                
                $title = "{$role_type} · {$gender} ({$age})";

                $parsed['roles'][] = [
                    'title' => $title,
                    'desc' => $desc,
                    'type' => $role_type
                ];
                $parsed['has_structure'] = true;
                continue;
            }

            // Keep remaining lines as fallback description content
            if (stripos($line_plain, 'Lead Roles') === false && 
                stripos($line_plain, 'Supporting Roles') === false && 
                stripos($line_plain, 'Location') === false &&
                stripos($line_plain, 'Duration') === false &&
                stripos($line_plain, 'Dates') === false &&
                stripos($line_plain, 'Remuneration') === false &&
                stripos($line_plain, 'Compensation') === false &&
                stripos($line_plain, 'Language') === false &&
                stripos($line_plain, 'Release') === false &&
                stripos($line_plain, 'Experience') === false &&
                trim($line_plain) !== 'If interested , Please upload your all details/ works on the velvetreel platform , and our hiring team will get back to you soon for auditions .') {
                $parsed['remaining_content'] .= '<p>' . $line . '</p>';
            }
        }

        return $parsed;
    }
}
?>

<style>
/* Page Level Overrides for Immersive Universal Dark Aesthetic */
body.single-advertisement {
    background-color: #050507 !important;
    background-image: radial-gradient(circle at 50% -10%, rgba(254, 17, 75, 0.08) 0%, transparent 60%) !important;
    color: #e4e4e7 !important;
}

.advertisement-single-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 60px 24px 80px;
    font-family: 'Outfit', 'Inter', 'Poppins', sans-serif;
}

.advertisement-single-content {
    background: transparent;
    border: none;
    box-shadow: none;
    padding: 0;
    margin-top: 0;
}

/* Back Link styling */
.back-to-ads-container {
    margin-bottom: 32px;
}

.back-to-ads-btn {
    font-size: 14px;
    font-weight: 600;
    color: #a1a1aa;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #121214;
    border: 1px solid #1f1f23;
    border-radius: 100px;
}

.back-to-ads-btn:hover {
    color: #ffffff;
    background: #18181b;
    border-color: #FE114B;
    box-shadow: 0 4px 15px rgba(254, 17, 75, 0.15);
}

/* Header styling */
.premium-ad-header {
    margin-bottom: 32px;
}

.premium-ad-category-tag {
    font-size: 12px;
    text-transform: uppercase;
    color: #FE114B;
    font-weight: 800;
    letter-spacing: 2.5px;
    display: inline-block;
    margin-bottom: 16px;
    padding: 3px 12px;
    background: rgba(254, 17, 75, 0.08);
    border: 1px solid rgba(254, 17, 75, 0.2);
    border-radius: 100px;
}

.premium-ad-title {
    font-size: 44px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.2;
    letter-spacing: -1.2px;
    margin: 0 0 16px 0;
    font-family: 'Outfit', sans-serif;
}

/* Header Metadata Meta row */
.premium-ad-meta-row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
    color: #71717a;
    font-size: 14px;
    font-weight: 500;
    border-bottom: 1px solid #1f1f23;
    padding-bottom: 24px;
}

.premium-ad-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.premium-ad-meta-item svg {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    fill: none;
}

.premium-ad-meta-divider {
    color: #27272a;
}

/* Split Columns Grid & Adaptive Layouts */
.premium-ad-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 48px;
    align-items: start;
    margin-bottom: 48px;
}

.premium-ad-main-column {
    min-width: 0;
}

.premium-ad-sidebar {
    position: sticky;
    top: 100px;
}

/* Image styling */
.premium-ad-media-container {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    background: #121214;
    border: 1px solid #1f1f23;
    aspect-ratio: 16 / 9;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
    margin-bottom: 40px;
}

.premium-ad-media-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.premium-ad-media-caption {
    position: absolute;
    bottom: 24px;
    left: 24px;
    font-size: 11px;
    font-weight: 800;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 2px;
    background: rgba(18, 18, 20, 0.75);
    padding: 6px 14px;
    border-radius: 6px;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Rich Editorial Content Body */
.premium-ad-body-content {
    font-size: 16px;
    line-height: 1.8;
    color: #d4d4d8;
    font-family: 'Inter', sans-serif;
}

.premium-ad-body-content p {
    margin: 0 0 24px 0;
}

.premium-ad-body-content p:last-child {
    margin-bottom: 0;
}

.premium-ad-body-content h1, 
.premium-ad-body-content h2, 
.premium-ad-body-content h3, 
.premium-ad-body-content h4 {
    color: #ffffff;
    font-family: 'Outfit', sans-serif;
    font-weight: 700;
    margin: 36px 0 16px 0;
}

.premium-ad-body-content h2 { font-size: 24px; }
.premium-ad-body-content h3 { font-size: 20px; }

.premium-ad-body-content strong {
    color: #ffffff;
}

.premium-ad-body-content ul, 
.premium-ad-body-content ol {
    margin: 0 0 24px 24px;
    padding: 0;
}

.premium-ad-body-content li {
    margin-bottom: 8px;
}

.premium-ad-body-content blockquote {
    margin: 32px 0;
    padding-left: 24px;
    border-left: 3px solid #FE114B;
    font-style: italic;
    color: #a1a1aa;
}

/* At a Glance Sidebar Widget */
.at-a-glance-widget {
    background: rgba(18, 18, 20, 0.6);
    backdrop-filter: blur(12px);
    border: 1px solid #1f1f23;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
}

.at-a-glance-title {
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    color: #ffffff;
    letter-spacing: 1.5px;
    margin: 0 0 24px 0;
    padding-bottom: 12px;
    border-bottom: 1px solid #1f1f23;
}

.at-a-glance-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.at-a-glance-item {
    display: flex;
    gap: 16px;
    align-items: center;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
}

.at-a-glance-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.at-a-glance-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: rgba(254, 17, 75, 0.06);
    border: 1px solid rgba(254, 17, 75, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FE114B;
    flex-shrink: 0;
}

.at-a-glance-icon svg {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    fill: none;
}

.at-a-glance-details {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
}

.at-a-glance-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    color: #71717a;
    letter-spacing: 1px;
}

.at-a-glance-value {
    font-size: 16px;
    font-weight: 700;
    color: #ffffff;
    word-break: break-word;
}

/* Pulsing Status Dot */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
}

.status-dot {
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    position: relative;
}

.status-dot::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: #10b981;
    border-radius: 50%;
    animation: statusPulse 1.8s infinite ease-in-out;
}

@keyframes statusPulse {
    0% { transform: scale(1); opacity: 0.8; }
    100% { transform: scale(2.8); opacity: 0; }
}

/* Roles We're Casting Card Grid styling */
.roles-section {
    margin-bottom: 64px;
    border-top: 1px solid #1f1f23;
    padding-top: 48px;
}

.roles-section-title {
    font-size: 22px;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.5px;
    margin: 0 0 24px 0;
    font-family: 'Outfit', sans-serif;
}

.roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 24px;
}

.role-card {
    background: #121214;
    border: 1px solid #1f1f23;
    border-radius: 16px;
    padding: 28px;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.role-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: #FE114B;
    opacity: 0.85;
}

.role-card:hover {
    border-color: rgba(254, 17, 75, 0.4);
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
}

.role-title {
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 12px 0;
    text-transform: capitalize;
    font-family: 'Outfit', sans-serif;
}

.role-desc {
    font-size: 14px;
    line-height: 1.6;
    color: #a1a1aa;
    margin: 0;
}

/* Ready to be Considered CTA Container */
.ready-to-be-considered-cta {
    background: linear-gradient(135deg, #121214 0%, #18181b 100%);
    border: 1px solid #1f1f23;
    border-radius: 20px;
    padding: 48px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    position: relative;
    overflow: hidden;
}

.ready-to-be-considered-cta::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(254, 17, 75, 0.1) 0%, transparent 70%);
    filter: blur(30px);
    pointer-events: none;
}

.cta-left {
    flex: 1;
}

.cta-left h3 {
    font-size: 32px;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 12px 0;
    letter-spacing: -0.5px;
    font-family: 'Outfit', sans-serif;
}

.cta-left p {
    font-size: 16px;
    color: #a1a1aa;
    margin: 0;
    line-height: 1.5;
}

.cta-right {
    flex-shrink: 0;
}

/* Overriding payment form & express interest system components */
.express-interest-section {
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    box-shadow: none !important;
}

.express-interest-header {
    display: none !important; /* Hide old redundant header */
}

/* Re-theme express-interest buttons to fit mockup CTA button */
.express-interest-btn, .payment-shortcode button, .payment-shortcode input[type="submit"], .payment-shortcode .stripe-button-el {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 10px !important;
    padding: 16px 36px !important;
    background-color: #FE114B !important;
    background-image: none !important;
    color: #ffffff !important;
    border: 2px solid #FE114B !important;
    border-radius: 12px !important;
    font-size: 16px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    text-decoration: none !important;
    text-transform: none !important;
    min-width: 220px;
    box-shadow: 0 6px 20px rgba(254, 17, 75, 0.35) !important;
    box-sizing: border-box !important;
}

.express-interest-btn:hover, .payment-shortcode button:hover, .payment-shortcode input[type="submit"]:hover, .payment-shortcode .stripe-button-el:hover {
    background-color: transparent !important;
    color: #FE114B !important;
    border-color: #FE114B !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 10px 25px rgba(254, 17, 75, 0.45) !important;
}

.express-interest-btn.interested {
    background-color: #10b981 !important;
    border-color: #10b981 !important;
    color: #ffffff !important;
    box-shadow: none !important;
    cursor: not-allowed !important;
}

.express-interest-btn.interested:hover {
    background-color: #10b981 !important;
    color: #ffffff !important;
    transform: none !important;
}

.express-interest-message {
    font-size: 13px;
    font-weight: 500;
    margin-top: 16px;
    border-radius: 8px;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.express-interest-message.success {
    background: rgba(16, 185, 129, 0.1) !important;
    color: #10b981 !important;
    border: 1px solid rgba(16, 185, 129, 0.2) !important;
}

.express-interest-message.error {
    background: rgba(254, 17, 75, 0.1) !important;
    color: #FE114B !important;
    border: 1px solid rgba(254, 17, 75, 0.2) !important;
}

/* Responsive Overrides */
@media (max-width: 1024px) {
    .premium-ad-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .premium-ad-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .advertisement-single-container {
        padding: 40px 16px 60px;
    }
    
    .premium-ad-title {
        font-size: 32px;
    }
    
    .premium-ad-meta-row {
        gap: 12px;
        font-size: 13px;
    }
    
    .premium-ad-meta-divider {
        display: none;
    }
    
    .ready-to-be-considered-cta {
        flex-direction: column;
        align-items: stretch;
        padding: 32px 24px;
        gap: 24px;
        text-align: center;
    }
    
    .cta-left h3 {
        font-size: 26px;
    }
    
    .express-interest-btn, .payment-shortcode button, .payment-shortcode input[type="submit"], .payment-shortcode .stripe-button-el {
        width: 100% !important;
    }
}
</style>

<main class="site-main advertisement-single-container" role="main">
    <div class="back-to-ads-container">
        <a href="<?php echo home_url('/advertisement'); ?>" class="back-to-ads-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Back to Classifieds
        </a>
    </div>

    <?php while (have_posts()) : the_post(); 
        // Run our dynamic content parser
        $parsed_data = velvet_parse_ad_content(get_the_content());
        
        // Resolve core creation taxonomy category (Casting Calls, Crew Calls, Rentals)
        $categories = get_the_terms(get_the_ID(), 'advertisement_category');
        $category_name = 'Classified Ad';
        $tag_line = 'CLASSIFIED AD';
        if ($categories && !is_wp_error($categories)) {
            $cat_names = array_map(function($c) { 
                return $c->name;
            }, $categories);
            $category_name = reset($cat_names);
            $tag_line = strtoupper($category_name);
        }
        
        // Resolve location with hybrid creation process fallback mapping
        $meta_location = !empty($parsed_data['location']) ? $parsed_data['location'] : get_post_meta(get_the_ID(), 'ad_location', true);
        if (empty($meta_location)) {
            $meta_location = get_post_meta(get_the_ID(), '_advertisement_location', true);
        }
        if (empty($meta_location)) {
            $meta_location = 'Not Specified';
        }

        // Additional administrative details
        $meta_budget = !empty($parsed_data['compensation']) ? $parsed_data['compensation'] : get_post_meta(get_the_ID(), '_advertisement_budget', true);
        $meta_deadline = get_post_meta(get_the_ID(), '_advertisement_deadline', true);
        $meta_status = get_post_meta(get_the_ID(), '_advertisement_status', true);
        $status_label = (!empty($meta_status) && strtolower($meta_status) === 'closed') ? 'Closed' : 'Active';
    ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('advertisement-single-content'); ?>>
            <!-- Page Header -->
            <header class="premium-ad-header">
                <span class="premium-ad-category-tag"><?php echo esc_html($tag_line); ?></span>
                <h1 class="premium-ad-title"><?php the_title(); ?></h1>
                
                <!-- High-End Creation-Process Metadata Row -->
                <div class="premium-ad-meta-row">
                    <div class="premium-ad-meta-item">
                        <svg viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span><?php echo esc_html($meta_location); ?></span>
                    </div>
                    <span class="premium-ad-meta-divider">|</span>
                    <div class="premium-ad-meta-item">
                        <svg viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>Posted on <?php echo get_the_date('M j, Y'); ?></span>
                    </div>
                    <span class="premium-ad-meta-divider">|</span>
                    <div class="premium-ad-meta-item">
                        <svg viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        <span><?php echo esc_html($category_name); ?></span>
                    </div>
                </div>
            </header>

            <!-- Main Universal Grid Layout -->
            <div class="premium-ad-grid">
                <!-- Main Media & Post Content Column -->
                <div class="premium-ad-main-column">
                    <!-- Cinematic Featured Widescreen Hero Banner -->
                    <div class="premium-ad-media-container">
                        <?php 
                        // Dynamically display featured post image or fallback placeholder
                        $hero_image_url = '';
                        if (has_post_thumbnail()) {
                            $hero_image_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
                        } else {
                            $hero_image_url = get_stylesheet_directory_uri() . '/assets/images/ad-placeholder.png';
                        }
                        ?>
                        <img src="<?php echo esc_url($hero_image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                        <span class="premium-ad-media-caption"><?php echo esc_html(strtoupper($category_name)); ?> MEDIA</span>
                    </div>

                    <!-- Universal Full Body Post Content (Excludes parsed casting roles) -->
                    <div class="premium-ad-body-content">
                        <?php if (!empty(trim($parsed_data['remaining_content']))) : ?>
                            <?php echo wp_kses_post($parsed_data['remaining_content']); ?>
                        <?php else : ?>
                            <?php the_content(); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Universal Facts & Stats Sidebar -->
                <aside class="premium-ad-sidebar">
                    <div class="at-a-glance-widget">
                        <h3 class="at-a-glance-title">Listing Details</h3>
                        <div class="at-a-glance-list">
                            <!-- Location -->
                            <div class="at-a-glance-item">
                                <div class="at-a-glance-icon">
                                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                </div>
                                <div class="at-a-glance-details">
                                    <span class="at-a-glance-label">Location</span>
                                    <span class="at-a-glance-value"><?php echo esc_html($meta_location); ?></span>
                                </div>
                            </div>
                            
                            <!-- Listing Type -->
                            <div class="at-a-glance-item">
                                <div class="at-a-glance-icon">
                                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                </div>
                                <div class="at-a-glance-details">
                                    <span class="at-a-glance-label">Listing Type</span>
                                    <span class="at-a-glance-value"><?php echo esc_html($category_name); ?></span>
                                </div>
                            </div>

                            <!-- Posted Date -->
                            <div class="at-a-glance-item">
                                <div class="at-a-glance-icon">
                                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </div>
                                <div class="at-a-glance-details">
                                    <span class="at-a-glance-label">Published On</span>
                                    <span class="at-a-glance-value"><?php echo get_the_date('M j, Y'); ?></span>
                                </div>
                            </div>

                            <!-- Compensation / Budget -->
                            <?php if (!empty($meta_budget)) : ?>
                                <div class="at-a-glance-item">
                                    <div class="at-a-glance-icon">
                                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                    </div>
                                    <div class="at-a-glance-details">
                                        <span class="at-a-glance-label">Compensation</span>
                                        <span class="at-a-glance-value"><?php echo esc_html(ucfirst($meta_budget)); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Deadline -->
                            <?php if (!empty($meta_deadline)) : ?>
                                <div class="at-a-glance-item">
                                    <div class="at-a-glance-icon">
                                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    </div>
                                    <div class="at-a-glance-details">
                                        <span class="at-a-glance-label">Apply Before</span>
                                        <span class="at-a-glance-value"><?php echo esc_html(date('M j, Y', strtotime($meta_deadline))); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Status Badge -->
                            <div class="at-a-glance-item">
                                <div class="at-a-glance-icon">
                                    <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                                </div>
                                <div class="at-a-glance-details">
                                    <span class="at-a-glance-label">Status</span>
                                    <span class="status-badge">
                                        <span class="status-dot" style="<?php echo $status_label === 'Closed' ? 'background-color: #71717a;' : ''; ?>"></span>
                                        <span style="<?php echo $status_label === 'Closed' ? 'color: #71717a;' : 'color: #10b981;'; ?>"><?php echo esc_html($status_label); ?></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <!-- High-Fidelity Casting Roles Section (Displayed as elegant cards if roles exist) -->
            <?php if (!empty($parsed_data['roles'])) : ?>
                <section class="roles-section">
                    <h3 class="roles-section-title">Roles We're Casting</h3>
                    <div class="roles-grid">
                        <?php foreach ($parsed_data['roles'] as $role) : ?>
                            <div class="role-card">
                                <h4 class="role-title"><?php echo esc_html($role['title']); ?></h4>
                                <p class="role-desc"><?php echo esc_html($role['desc']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Universal Call to Action Application Footer Banner -->
            <footer class="ready-to-be-considered-cta">
                <div class="cta-left">
                    <?php
                    // Dynamic CTA Text according to assigned category
                    if (strpos($tag_line, 'CREW CALL') !== false) {
                        $cta_title = 'Ready to join the crew?';
                        $cta_desc = 'Freshers and experienced professionals are encouraged to apply for this crew role.';
                    } elseif (strpos($tag_line, 'RENTALS') !== false) {
                        $cta_title = 'Interested in this rental?';
                        $cta_desc = 'Connect with the listing owner to discuss terms and details.';
                    } else {
                        $cta_title = 'Ready to be considered?';
                        $cta_desc = 'Freshers and experienced performers are encouraged to apply for this role.';
                    }
                    ?>
                    <h3><?php echo esc_html($cta_title); ?></h3>
                    <p><?php echo esc_html($cta_desc); ?></p>
                </div>
                
                <div class="cta-right">
                    <?php
                    // Include access control functions and define constants
                    if (!function_exists('has_membership_plan')) {
                        require_once get_stylesheet_directory() . '/includes/access-control.php';
                    }
                    
                    if (!defined('ADVERTISEMENT_PLAN_LEVEL')) {
                        define('ADVERTISEMENT_PLAN_LEVEL', 5);
                    }
                    if (!defined('PORTFOLIO_PLAN_LEVEL')) {
                        define('PORTFOLIO_PLAN_LEVEL', 2);
                    }
                    if (!defined('PORTFOLIO_PLAN_LEVEL_UPGRADE')) {
                        define('PORTFOLIO_PLAN_LEVEL_UPGRADE', 3);
                    }
                    
                    if (function_exists('ensure_user_plan_tracking')) {
                        ensure_user_plan_tracking();
                    }
                    
                    $is_logged_in = is_user_logged_in();
                    $current_user = $is_logged_in ? wp_get_current_user() : null;
                    $advertisement_id = get_the_ID();
                    $is_author = $is_logged_in && get_post_field('post_author', $advertisement_id) == $current_user->ID;
                    
                    $is_paid_member = false;
                    if ($is_logged_in) {
                        $user_id = get_current_user_id();
                        $user_plans = get_user_meta($user_id, 'membership_plans', true);
                        if (!is_array($user_plans)) $user_plans = array();
                        
                        $current_level = class_exists('SwpmMemberUtils') && SwpmMemberUtils::is_member_logged_in() ? SwpmMemberUtils::get_logged_in_members_level() : 0;
                        $paid_levels = [ADVERTISEMENT_PLAN_LEVEL, PORTFOLIO_PLAN_LEVEL, PORTFOLIO_PLAN_LEVEL_UPGRADE];
                        
                        if (in_array($current_level, $paid_levels)) {
                            $is_paid_member = true;
                        } else {
                            foreach ($paid_levels as $level) {
                                if (in_array($level, $user_plans)) {
                                    $is_paid_member = true;
                                    break;
                                }
                            }
                        }
                    }
                    
                    $interests = get_post_meta($advertisement_id, '_advertisement_interests', true);
                    if (!is_array($interests)) {
                        $interests = array();
                    }

                    // Handle payment success return to register interest & send emails
                    if ($is_logged_in && isset($_GET['payment']) && $_GET['payment'] === 'success') {
                        $user_id = $current_user->ID;
                        if (!in_array($user_id, $interests) && $user_id != get_post_field('post_author', $advertisement_id)) {
                            $interests[] = $user_id;
                            update_post_meta($advertisement_id, '_advertisement_interests', $interests);
                            
                            $interest_dates = get_post_meta($advertisement_id, '_advertisement_interest_dates', true);
                            if (!is_array($interest_dates)) {
                                $interest_dates = array();
                            }
                            $interest_dates[$user_id] = current_time('mysql');
                            update_post_meta($advertisement_id, '_advertisement_interest_dates', $interest_dates);

                            if (function_exists('velvet_send_interest_notifications')) {
                                velvet_send_interest_notifications($advertisement_id, $user_id);
                            }
                        }
                    }

                    $has_expressed_interest = false;
                    if ($is_logged_in && in_array($current_user->ID, $interests)) {
                        $has_expressed_interest = true;
                    }
                    
                    $success_url = get_permalink() . '?payment=success&advertisement_id=' . $advertisement_id;
                    
                    // Render Contextual Action Button
                    if (!$is_logged_in) :
                    ?>
                        <a href="<?php echo home_url('/membership-login/'); ?>" class="express-interest-btn">Login to Apply</a>
                    <?php 
                    elseif ($is_author) :
                    ?>
                        <span style="font-weight:700; color:#71717a; text-transform:uppercase; letter-spacing:1px; font-size:12px;">Your Classified Ad</span>
                    <?php 
                    elseif ($is_paid_member) :
                    ?>
                        <div class="express-interest-section">
                            <div class="express-interest-wrapper">
                                <button 
                                    type="button" 
                                    class="express-interest-btn <?php echo $has_expressed_interest ? 'interested' : ''; ?>" 
                                    data-advertisement-id="<?php echo esc_attr($advertisement_id); ?>"
                                    <?php echo $has_expressed_interest ? 'disabled' : ''; ?>
                                >
                                    <?php if ($has_expressed_interest) : ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Interest Expressed
                                    <?php else : ?>
                                        Apply through VelvetReel &rarr;
                                    <?php endif; ?>
                                </button>
                            </div>
                        </div>
                    <?php 
                    else : 
                    ?>
                        <div class="express-interest-section">
                            <div class="payment-prompt">
                                <div class="payment-shortcode">
                                    <?php echo do_shortcode('[wp_stripe_checkout_session name="show-interest-fee" price="5.00" button_text="Pay $5.00 to Apply" success_url="' . $success_url . '"]'); ?>
                                </div>
                            </div>
                        </div>
                    <?php 
                    endif; 
                    ?>
                </div>
            </footer>
        </article>
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>