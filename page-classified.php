<?php
/**
 * Template Name: Classified Pricing Page
 *
 * @package HelloElementorChild
 */

get_header();

// Check if user has advertisement plan
$has_access = false;

// Include access control functions if not already included
if (!function_exists('has_membership_plan')) {
    require_once get_stylesheet_directory() . '/includes/access-control.php';
}

// Ensure user's plan tracking is properly initialized
ensure_user_plan_tracking();

// Also migrate current level to plans for backward compatibility
migrate_user_current_level_to_plans();

// Define the plans that have access to create classified ads
$plans_with_ad_access = [
    SIX_MONTH_PLAN_LEVEL,  // 6-month plan
    ONE_YEAR_PLAN_LEVEL,   // 1-year plan
    ADVERTISEMENT_PLAN_LEVEL  // Original advertisement plan (level 5)
];

// Check if user has any of the plans with ad access
foreach ($plans_with_ad_access as $plan_level) {
    if (has_membership_plan($plan_level)) {
        $has_access = true;
        break; // Exit early if any qualifying plan is found
    }
}

// Ensure SWPM is loaded properly
if (!function_exists('swpm_payment_button')) {
    // Try to include SWPM files if they exist
    $swpm_path = ABSPATH . 'wp-content/plugins/simple-membership/';
    if (file_exists($swpm_path . 'simple-wp-membership.php')) {
        include_once($swpm_path . 'simple-wp-membership.php');
    }
    
    // Force reload of shortcodes
    if (function_exists('swpm_load_payment_buttons_shortcode')) {
        swpm_load_payment_buttons_shortcode();
    }
}

?>

<div id="classified-pricing">
    <style>
        .page-template-page-classified {
            background-color: #050505;
        }

        .classified-pricing-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            font-family: Arial, sans-serif;
            margin-top: 0;
            
        }

        .classified-pricing-content {
         background-color: #1C1C1C;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .classified-pricing-content h1 {
           color: #ffffff;
    font-family: "Gantari", Sans-serif;
    font-size: 2em;
    font-weight: 700;
        }

        .pricing-subtitle {
            color: #f7f7f7;
            font-size: 1.2em;
            margin-bottom: 30px;
        }

       
        .pricing-amount {
            font-size: 4em;
            font-weight: bold;
            margin-bottom: 10px;
            color: var(--e-global-color-accent);
        }

        .pricing-description {
            font-size: 16px;
            color: #fff;
        }

      

        .access-confirmation {
            color: #4CAF50;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .payment-required {
            font-size: 1.2em;
            margin: 20px 0;
            color: #fff;
        }

        .login-required {
            font-size: 1.2em;
            margin: 20px 0;
            color: #e74c3c;
        }

        .login-required a {
                        color: #e74c3c;

            text-decoration: none;
        }

        .login-required a:hover {
            text-decoration: underline;
        }

        .pricing-features {
            margin-top: 40px;
            text-align: left;
        }

        .pricing-features h3 {
            text-align: center;
            color:#fff;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .pricing-features ul {
            list-style: none;
            padding: 0;
        }

        .pricing-features li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
            font-size: 1.1em;
            color: #fff;
        }

        .pricing-features li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4CAF50;
            font-weight: bold;
        }

        .btn-create-ad {
            background-color: #FE114B;
    font-family: "Gantari", Sans-serif;
    font-weight: 400;
    border-style: solid;
    border-width: 2px 2px 2px 2px;
    border-color: #FE114B;
    border-radius: 0px 0px 0px 0px;
    font-size: 16px;
    padding: 10px 30px;
    color: #fff;
    display: inline-block;
    border-radius: 8px;
        }

        .btn-create-ad:hover {
            color: #fff;
        }

    </style>
    
    <div class="classified-pricing-container">
        <div class="classified-pricing-content">
            <h1>Advertisement Listings</h1>
                    <p class="pricing-subtitle">Create and publish your ads with our simple pricing</p>
           
            <div class="pricing-card">
               <div class="pricing-amount">$3</div>
                        <div class="pricing-description">Per ad, validity 15 days</div>
                        
                <?php 
                if ($has_access) {
                    // User has access, show Create Add button
                    echo '<p class="access-confirmation">You have an active advertisement plan. You can create ads anytime!</p>';
                    echo '<a href="' . esc_url(home_url('/classified-create')) . '" class="btn-create-ad">Create Advertisement</a>';
                } else {
                    // User doesn't have access (either not logged in or logged in without proper subscription)
                    echo '<p class="payment-required">Please purchase the advertisement plan to create ads:</p>';
                    
                    // Check if SWPM functions exist
                    if (function_exists('swpm_payment_button')) {
                        // Try to render the payment button
                        $payment_button = do_shortcode('[swpm_payment_button id="6433"]');
                        
                        // Check if the shortcode returned empty content
                        if (empty(trim($payment_button))) {
                            echo '<p>Payment button with ID 6433 was not found. Please check if the payment button exists in Simple Membership settings.</p>';
                        } else {
                            echo $payment_button;
                        }
                    } else {
                        // Try to manually initialize SWPM shortcodes
                        global $shortcode_tags;
                        
                        // Check if the SWPM shortcode is registered
                        if (!isset($shortcode_tags['swpm_payment_button'])) {
                            echo '<p>Simple Membership Plugin is not active or payment button shortcode is not registered. Please check that the plugin is active and the payment button ID 6433 exists.</p>';
                        } else {
                            // SWPM shortcode is registered, try to render it directly
                            $payment_button = do_shortcode('[swpm_payment_button id="6433"]');
                            if (empty(trim($payment_button))) {
                                echo '<p>Payment button with ID 6433 was not found. Please check if the payment button exists in Simple Membership settings.</p>';
                            } else {
                                echo $payment_button;
                            }
                        }
                    }
                }
                ?>
            </div>
            
            <div class="pricing-features">
                <h3>What you get:</h3>
                <ul>
                    <li>Your ad will be displayed for 15 days</li>
                    <li>Featured in our classified section</li>
                    <li>Reach our active community of users</li>
                    <li>Easy management of your ads</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
