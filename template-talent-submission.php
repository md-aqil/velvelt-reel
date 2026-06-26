<?php
/**
 * Template Name: Talent Submission Form (Refactored)
 * 
 * A modular, maintainable version using controller and template parts
 *
 * @package HelloElementorChild
 */

// Include the controller to handle all PHP logic (access control, etc.)
require_once get_stylesheet_directory() . '/includes/talent-submission-controller.php';

// Get controller instance for template data
$controller = talent_submission_controller();
$theme_version = $controller->get_theme_version();
$form_values = $controller->get_form_values();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VelvetReel Talent Registration</title>
    <?php wp_head(); ?>
</head>

<body>
    <!-- Mobile Alert -->
    <div class="mobile-alert">
        <div class="alert-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div class="alert-content">
            <h3>For Better Experience</h3>
            <p>Kindly open this page on a web browser for better experience</p>
        </div>
    </div>

    <div class="form-container">
        <!-- Progress Sidebar -->
        <?php
// Include modular progress sidebar
get_template_part('templates/talent-submission/progress-sidebar');
?>

        <!-- Main Form Area -->
        <div class="form-main">
            <form id="velvetReelForm" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('talent_submission', 'talent_submission_nonce'); ?>
                <input type="hidden" name="action" value="submit_talent_profile">
                <input type="hidden" name="portfolio_cover_choice" value="">
                <input type="hidden" name="portfolio_existing_order" value="">
                <input type="hidden" name="talent_ajax_url" id="talent_ajax_url"
                    value="<?php echo esc_url($controller->get_ajax_url()); ?>">

                <!-- Include form steps using output buffering for cleaner template -->
                <?php
// Step 1: Profile Basics
get_template_part('templates/talent-submission/form-step-1', null, array(
    'values' => $form_values,
));

// Step 2: Experience  
get_template_part('templates/talent-submission/form-step-2', null, array(
    'values' => $form_values,
));
?>

                <!-- STEP 3: Portfolio Summary -->
                <div class="form-step" data-step="3">
                    <h2>Portfolio Summary</h2>
                    <p class="step-description">Showcase your unique creative identity</p>

                    <div class="form-group full-width">
                        <label for="styleDescription">Describe your style or specialization</label>
                        <textarea id="styleDescription" name="styleDescription" rows="4" maxlength="500"
                            placeholder="Describe your unique creative approach, aesthetic, or what sets your work apart"></textarea>
                        <div class="char-counter"><span id="styleCount">0</span>/500</div>
                    </div>

                    <div class="form-group full-width">
                        <label for="interestedProjects">What kind of projects are you interested in next?</label>
                        <textarea id="interestedProjects" name="interestedProjects" rows="3" maxlength="300"
                            placeholder="What kind of opportunities or collaborations are you seeking next?"></textarea>
                        <div class="char-counter"><span id="projectsCount">0</span>/300</div>
                    </div>

                    <div class="form-group full-width">
                        <label>Notable Works (up to 10)</label>
                        <div id="notableWorksContainer">
                            <div class="notable-work-item">
                                <button type="button" class="remove-work-btn" style="display: none;">&times;</button>
                                <div class="form-group full-width">
                                    <input type="text" name="workTitle[]" placeholder="Project Title">
                                </div>
                                <div class="form-group full-width">
                                    <input type="text" name="workRole[]"
                                        placeholder="Your Role (e.g., Director, Lead Actor)">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="month" name="workStartDate[]">
                                    </div>
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="month" name="workEndDate[]" class="work-end-date">
                                    </div>
                                </div>
                                <div class="form-group full-width">
                                    <label class="checkbox-label" style="font-weight: normal;">
                                        <input type="checkbox" name="workPresent[]" value="0"
                                            class="work-present-checkbox">
                                        <span class="control-indicator"></span><span>I currently work here</span>
                                    </label>
                                </div>
                                <div class="form-group full-width">
                                    <textarea name="workDescription[]" rows="2" maxlength="150"
                                        placeholder="Brief Description (150 char)"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="add-work-btn" id="addWorkBtn">+ Add Another Work</button>
                    </div>
                </div>

                <!-- STEP 4: Availability & Preferences -->
                <div class="form-step" data-step="4">
                    <h2>Availability & Preferences</h2>
                    <p class="step-description">Let us know your work preferences</p>

                    <div class="form-group full-width">
                        <label>Available For</label>
                        <div class="checkbox-grid">
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Freelance Projects"><span
                                    class="control-indicator"></span>
                                <span>Freelance Projects</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Full-time Opportunities"><span
                                    class="control-indicator"></span>
                                <span>Full-time Opportunities</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Collaborations"><span
                                    class="control-indicator"></span>
                                <span>Collaborations</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Consulting"><span
                                    class="control-indicator"></span>
                                <span>Consulting</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Willing to Travel?</label>
                        <div class="toggle-group">
                            <label class="toggle-switch">
                                <input type="checkbox" id="willingToTravel" name="willingToTravel" value="on">
                                <span class="toggle-slider"></span>
                                <span class="toggle-label">Yes, I'm willing to travel</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group full-width" id="preferredLocationsSection" style="display: none;">
                        <label for="preferredLocations">Preferred Work Locations</label>
                        <input type="text" id="preferredLocations" name="preferredLocations" placeholder="New York, LA">
                    </div>

                    <div class="form-group full-width">
                        <label>Open to Brand Collaborations?</label>
                        <div class="toggle-group">
                            <label class="toggle-switch">
                                <input type="checkbox" id="brandCollabs" name="brandCollabs" value="on">
                                <span class="toggle-slider"></span>
                                <span class="toggle-label">Yes, open to brand collaborations</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- STEP 5: Domain Selection -->
                <div class="form-step" data-step="5">
                    <h2>Choose Your Domain</h2>
                    <p class="step-description">Select your primary field of work</p>

                    <div class="domain-cards">
                        <?php
if (function_exists('render_domain_cards')) {
    render_domain_cards();
}
?>
                    </div>
                    <input type="hidden" id="selectedDomain" name="domain">
                </div>


                <!-- STEP 6: Role Selection -->
                <div class="form-step" data-step="6">
                    <h2>Select Your Role</h2>
                    <p class="step-description">Choose your specific specialization</p>

                    <div id="roleContainer">
                        <?php
if (function_exists('get_domains') && function_exists('render_role_cards')) {
    $domains = get_domains();
    foreach ($domains as $domain_key => $domain_data):
?>
                        <div class="role-category" data-domain="<?php echo esc_attr($domain_key); ?>">
                            <?php render_role_cards($domain_key); ?>
                        </div>
                        <?php
    endforeach;
}
?>
                    </div>
                    <input type="hidden" id="selectedRole" name="role">
                </div>

                <!-- STEP 7: Role-Specific Questions (Dynamic) -->
                <div class="form-step" data-step="7">
                    <h2 id="roleSpecificTitle">Role-Specific Details</h2>
                    <p class="step-description">Tell us more about your specialization</p>

                    <div id="roleFieldsContainer">
                        <?php
if (function_exists('render_all_role_fields_clean')) {
    render_all_role_fields_clean();
}
?>
                    </div>

                    <?php
$render_portfolio_gallery_shared_instance = true;
$portfolio_role_key = '';
$portfolio_role_name = '';
include get_stylesheet_directory() . '/components/portfolio-gallery.php';
unset($render_portfolio_gallery_shared_instance, $portfolio_role_key, $portfolio_role_name);
?>

                    <!-- Physical Details Section (only for models, actors, dancers, etc.) -->
                    <h3 id="physicalDetailsHeading" style="margin-top: 20px; margin-bottom: 15px;" hidden>Physical
                        Details</h3>

                    <div class="form-row conditional-fields" id="physicalDetailsSection" hidden>
                        <div class="form-group">
                            <label for="height">Height</label>
                            <div class="input-with-unit">
                                <input type="text" id="height" name="height" placeholder="5'3&quot; or 170">
                                <select id="heightUnit" name="heightUnit">
                                    <option value="cm">cm</option>
                                    <option value="ft">ft</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight</label>
                            <div class="input-with-unit">
                                <input type="number" id="weight" name="weight" placeholder="65">
                                <select id="weightUnit" name="weightUnit">
                                    <option value="kg">kg</option>
                                    <option value="lbs">lbs</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="complexion">Complexion</label>
                            <select id="complexion" name="complexion">
                                <option value="">Select Complexion</option>
                                <option value="Fair">Fair</option>
                                <option value="Light">Light</option>
                                <option value="Medium">Medium</option>
                                <option value="Olive">Olive</option>
                                <option value="Tan">Tan</option>
                                <option value="Brown">Brown</option>
                                <option value="Dark Brown">Dark Brown</option>
                                <option value="Ebony">Ebony</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bustSize">Bust Size</label>
                            <input type="text" id="bustSize" name="bustSize" placeholder="34B">
                        </div>
                        <div class="form-group">
                            <label for="hairColor">Hair Color</label>
                            <select id="hairColor" name="hairColor">
                                <option value="">Select Hair Color</option>
                                <option value="Black">Black</option>
                                <option value="Dark Brown">Dark Brown</option>
                                <option value="Light Brown">Light Brown</option>
                                <option value="Blonde">Blonde</option>
                                <option value="Auburn">Auburn</option>
                                <option value="Red">Red</option>
                                <option value="Gray">Gray</option>
                                <option value="White">White</option>
                                <option value="Dyed - Other">Dyed - Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dressSize">Dress Size</label>
                            <input type="text" id="dressSize" name="dressSize" placeholder="M or 8">
                        </div>
                        <div class="form-group">
                            <label for="shirtSize">Shirt Size</label>
                            <select id="shirtSize" name="shirtSize">
                                <option value="">Select Shirt Size</option>
                                <option value="XS">XS (Extra Small)</option>
                                <option value="S">S (Small)</option>
                                <option value="M">M (Medium)</option>
                                <option value="L">L (Large)</option>
                                <option value="XL">XL (Extra Large)</option>
                                <option value="XXL">XXL (2XL)</option>
                                <option value="XXXL">XXXL (3XL)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- STEP 8: Preview & Publish -->
                <div class="form-step" data-step="8">
                    <h2>Preview & Publish</h2>
                    <p class="step-description">Review your portfolio and add social links before publishing.</p>

                    <div class="form-group full-width">
                        <h3>Social Links</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="instagram">Instagram</label>
                                <input type="text" id="instagram" name="instagram" placeholder="@username">
                                <label class="checkbox-label" style="font-weight: normal; margin-top: 5px;">
                                    <input type="checkbox" name="hideInstagram" value="1">
                                    <span class="control-indicator"></span>
                                    <span>Hide my Instagram from public view</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="tiktok">TikTok</label>
                                <input type="text" id="tiktok" name="tiktok" placeholder="@username">
                                <label class="checkbox-label" style="font-weight: normal; margin-top: 5px;">
                                    <input type="checkbox" name="hideTiktok" value="1">
                                    <span class="control-indicator"></span>
                                    <span>Hide my TikTok from public view</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="website">Personal Website</label>
                                <input type="url" id="website" name="website" placeholder="https://...">
                                <label class="checkbox-label" style="font-weight: normal; margin-top: 5px;">
                                    <input type="checkbox" name="hideWebsite" value="1">
                                    <span class="control-indicator"></span>
                                    <span>Hide my website from public view</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- Verification Fee Notification -->
                    <div class="verification-fee-notification">
                        <div class="notification-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <div class="notification-content">
                            <h3>Profile Verification Required</h3>
                            <p>By proceeding, you agree to pay <strong>$ 5 </strong>for profile verification. for review
                                and approval.</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Controls -->
                <?php get_template_part('templates/talent-submission/navigation-controls'); ?>

            </form>
        </div>
    </div>

    <?php wp_footer(); ?>

    <script>
        // Create talentData object for the form - required for save progress functionality
        var talentData = {
            ajaxurl: '<?php echo esc_url($controller->get_ajax_url()); ?>',
            nonce: '<?php echo esc_attr($controller->get_nonce()); ?>',
            draft: null,
            isEditPage: 0
        };

        console.log('talentData initialized for submission page:', talentData);
    </script>
</body>

</html>