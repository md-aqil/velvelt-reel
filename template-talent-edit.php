<?php
/**
 * Template Name: Talent Edit Form (Refactored)
 * 
 * A modular, maintainable version using controller and template parts
 *
 * @package HelloElementorChild
 */

// Include the controller to handle all PHP logic (access control, data loading, etc.)
require_once get_stylesheet_directory() . '/includes/talent-edit-controller.php';

// Get controller instance
$controller = talent_edit_controller();
$theme_version = $controller->get_theme_version();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Talent Profile - VelvetReel</title>
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
        <?php get_template_part('templates/talent-submission/progress-sidebar'); ?>

        <!-- Main Form Area -->
        <div class="form-main">
            <form id="velvetReelEditForm" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('talent_update', 'talent_update_nonce'); ?>
                <input type="hidden" name="action" value="update_talent_profile">
                <input type="hidden" name="post_id" value="<?php echo esc_attr($controller->get_talent_id()); ?>">
                <input type="hidden" name="portfolio_cover_choice" value="<?php echo esc_attr($controller->get('portfolio_cover_choice')); ?>">
                <input type="hidden" name="portfolio_existing_order" value="<?php echo esc_attr(implode(',', array_map('intval', (array) $controller->get('portfolio_images')))); ?>">
                <input type="hidden" name="talent_ajax_url" id="talent_ajax_url"
                    value="<?php echo esc_url($controller->get_ajax_url()); ?>">

                <!-- Include edit-specific form steps -->
                <?php 
                get_template_part('templates/talent-edit/form-step-1');
                get_template_part('templates/talent-edit/form-step-2');
                ?>

                <!-- STEP 3: Portfolio Summary -->
                <div class="form-step" data-step="3">
                    <h2>Portfolio Summary</h2>
                    <p class="step-description">Showcase your unique creative identity</p>

                    <div class="form-group full-width">
                        <label for="styleDescription">Describe your style or specialization</label>
                        <textarea id="styleDescription" name="styleDescription" rows="4" maxlength="500"
                            placeholder="Describe your unique creative approach, aesthetic, or what sets your work apart"
                            ><?php echo esc_textarea($controller->get('style_description')); ?></textarea>
                        <div class="char-counter"><span
                                id="styleCount"><?php echo strlen($controller->get('style_description')); ?></span>/500</div>
                    </div>

                    <div class="form-group full-width">
                        <label for="interestedProjects">What kind of projects are you interested in next?</label>
                        <textarea id="interestedProjects" name="interestedProjects" rows="3" maxlength="300"
                            placeholder="What kind of opportunities or collaborations are you seeking next?"><?php echo esc_textarea($controller->get('interested_projects')); ?></textarea>
                        <div class="char-counter"><span
                                id="projectsCount"><?php echo strlen($controller->get('interested_projects')); ?></span>/300</div>
                    </div>

                    <div class="form-group full-width">
                        <label>Notable Works (up to 10)</label>
                        <div id="notableWorksContainer">
                            <?php 
                            $notable_works = $controller->get('notable_works');
                            if (!empty($notable_works) && is_array($notable_works)): 
                                foreach ($notable_works as $index => $work):
                            ?>
                                <div class="notable-work-item">
                                    <button type="button" class="remove-work-btn">&times;</button>
                                    <div class="form-group full-width">
                                        <input type="text" name="workTitle[]" placeholder="Project Title"
                                            value="<?php echo esc_attr(isset($work['title']) ? $work['title'] : ''); ?>">
                                    </div>
                                    <div class="form-group full-width">
                                        <input type="text" name="workRole[]"
                                            placeholder="Your Role (e.g., Director, Lead Actor)"
                                            value="<?php echo esc_attr(isset($work['role']) ? $work['role'] : ''); ?>">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label>Start Date</label>
                                            <input type="month" name="workStartDate[]"
                                                value="<?php echo esc_attr(isset($work['startDate']) ? $work['startDate'] : ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>End Date</label>
                                            <input type="month" name="workEndDate[]" class="work-end-date"
                                                value="<?php echo esc_attr(isset($work['endDate']) ? $work['endDate'] : ''); ?>" 
                                                <?php if (isset($work['present']) && $work['present'] === 'on') echo 'disabled'; ?>>
                                        </div>
                                    </div>
                                    <div class="form-group full-width">
                                        <label class="checkbox-label" style="font-weight: normal;">
                                            <input type="checkbox" name="workPresent[]" value="<?php echo esc_attr($index); ?>"
                                                class="work-present-checkbox" 
                                                <?php if (isset($work['present']) && $work['present'] === 'on') echo 'checked'; ?>>
                                            <span class="control-indicator"></span><span>I currently work here</span>
                                        </label>
                                    </div>
                                    <div class="form-group full-width">
                                        <textarea name="workDescription[]" rows="2" maxlength="150"
                                            placeholder="Brief Description (150 char)"><?php echo esc_textarea(isset($work['description']) ? $work['description'] : ''); ?></textarea>
                                    </div>
                                </div>
                            <?php 
                                endforeach; 
                            else:
                            ?>
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
                            <?php endif; ?>
                        </div>
                        <button type="button" class="add-work-btn" id="addWorkBtn">+ Add Another Work</button>
                    </div>
                </div>

                <!-- STEP 4: Availability & Preferences -->
                <div class="form-step" data-step="4">
                    <h2>Availability & Preferences</h2>
                    <p class="step-description">Let us know your work preferences</p>

                    <?php 
                    $available_for = $controller->get('available_for');
                    $willing_to_travel = $controller->get('willing_to_travel');
                    $brand_collabs = $controller->get('brand_collabs');
                    ?>

                    <div class="form-group full-width">
                        <label>Available For</label>
                        <div class="checkbox-grid">
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Freelance Projects" 
                                    <?php if (in_array('Freelance Projects', (array)$available_for)) echo 'checked'; ?>><span
                                    class="control-indicator"></span>
                                <span>Freelance Projects</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Full-time Opportunities" 
                                    <?php if (in_array('Full-time Opportunities', (array)$available_for)) echo 'checked'; ?>><span
                                    class="control-indicator"></span>
                                <span>Full-time Opportunities</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Collaborations" 
                                    <?php if (in_array('Collaborations', (array)$available_for)) echo 'checked'; ?>><span
                                    class="control-indicator"></span>
                                <span>Collaborations</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Consulting" 
                                    <?php if (in_array('Consulting', (array)$available_for)) echo 'checked'; ?>><span
                                    class="control-indicator"></span>
                                <span>Consulting</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Willing to Travel?</label>
                        <div class="toggle-group">
                            <label class="toggle-switch">
                                <input type="checkbox" id="willingToTravel" name="willingToTravel" value="on" 
                                    <?php checked($willing_to_travel, 'on'); ?>>
                                <span class="toggle-slider"></span>
                                <span class="toggle-label">Yes, I'm willing to travel</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group full-width" id="preferredLocationsSection"
                        style="<?php echo ($willing_to_travel === 'on') ? '' : 'display: none;'; ?>">
                        <label for="preferredLocations">Preferred Work Locations</label>
                        <input type="text" id="preferredLocations" name="preferredLocations" placeholder="New York, LA"
                            value="<?php echo esc_attr($controller->get('preferred_locations')); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label>Open to Brand Collaborations?</label>
                        <div class="toggle-group">
                            <label class="toggle-switch">
                                <input type="checkbox" id="brandCollabs" name="brandCollabs" value="on" 
                                    <?php checked($brand_collabs, 'on'); ?>>
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
                    <input type="hidden" id="selectedDomain" name="domain" value="<?php echo esc_attr($controller->get('domain')); ?>">
                </div>

                <!-- STEP 6: Role Selection -->
                <div class="form-step" data-step="6">
                    <h2>Select Your Role</h2>
                    <p class="step-description">Choose your specific specialization</p>

                    <div id="roleContainer">
                        <?php
                        if (function_exists('get_domains') && function_exists('render_role_cards')) {
                            $domains = get_domains();
                            $current_domain = $controller->get('domain');
                            foreach ($domains as $domain_key => $domain_data):
                        ?>
                        <div class="role-category" data-domain="<?php echo esc_attr($domain_key); ?>" 
                             <?php echo ($current_domain === $domain_key) ? '' : 'hidden'; ?>>
                            <?php render_role_cards($domain_key); ?>
                        </div>
                        <?php
                            endforeach;
                        }
                        ?>
                    </div>
                    <input type="hidden" id="selectedRole" name="role" value="<?php echo esc_attr($controller->get('role')); ?>">
                </div>

                <!-- STEP 7: Role-Specific Details -->
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
                    $portfolio_role_key = (string) $controller->get('role');
                    $portfolio_role_name = '';
                    include get_stylesheet_directory() . '/components/portfolio-gallery.php';
                    unset($render_portfolio_gallery_shared_instance, $portfolio_role_key, $portfolio_role_name);
                    ?>

                    <!-- Physical Details Section (only for models, actors, dancers, etc.) -->
                    <h3 id="physicalDetailsHeading" style="margin-top: 20px; margin-bottom: 15px;">Physical Details</h3>

                    <div class="form-row conditional-fields" id="physicalDetailsSection">
                        <div class="form-group">
                            <label for="height">Height</label>
                            <div class="input-with-unit">
                                <input type="text" id="height" name="height" placeholder="5'3&quot; or 170"
                                    value="<?php echo esc_attr($controller->get('height')); ?>">
                                <select id="heightUnit" name="heightUnit">
                                    <option value="cm" <?php selected($controller->get('height_unit'), 'cm'); ?>>cm</option>
                                    <option value="ft" <?php selected($controller->get('height_unit'), 'ft'); ?>>ft</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="weight">Weight</label>
                            <div class="input-with-unit">
                                <input type="number" id="weight" name="weight" placeholder="65"
                                    value="<?php echo esc_attr($controller->get('weight')); ?>">
                                <select id="weightUnit" name="weightUnit">
                                    <option value="kg" <?php selected($controller->get('weight_unit'), 'kg'); ?>>kg</option>
                                    <option value="lbs" <?php selected($controller->get('weight_unit'), 'lbs'); ?>>lbs</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="complexion">Complexion</label>
                            <select id="complexion" name="complexion">
                                <option value="">Select Complexion</option>
                                <option value="Fair" <?php selected($controller->get('complexion'), 'Fair'); ?>>Fair</option>
                                <option value="Light" <?php selected($controller->get('complexion'), 'Light'); ?>>Light</option>
                                <option value="Medium" <?php selected($controller->get('complexion'), 'Medium'); ?>>Medium</option>
                                <option value="Olive" <?php selected($controller->get('complexion'), 'Olive'); ?>>Olive</option>
                                <option value="Tan" <?php selected($controller->get('complexion'), 'Tan'); ?>>Tan</option>
                                <option value="Brown" <?php selected($controller->get('complexion'), 'Brown'); ?>>Brown</option>
                                <option value="Dark Brown" <?php selected($controller->get('complexion'), 'Dark Brown'); ?>>Dark Brown</option>
                                <option value="Ebony" <?php selected($controller->get('complexion'), 'Ebony'); ?>>Ebony</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bustSize">Bust Size</label>
                            <input type="text" id="bustSize" name="bustSize" placeholder="34B"
                                value="<?php echo esc_attr($controller->get('bust_size')); ?>">
                        </div>
                        <div class="form-group">
                            <label for="hairColor">Hair Color</label>
                            <select id="hairColor" name="hairColor">
                                <option value="">Select Hair Color</option>
                                <option value="Black" <?php selected($controller->get('hair_color'), 'Black'); ?>>Black</option>
                                <option value="Dark Brown" <?php selected($controller->get('hair_color'), 'Dark Brown'); ?>>Dark Brown</option>
                                <option value="Light Brown" <?php selected($controller->get('hair_color'), 'Light Brown'); ?>>Light Brown</option>
                                <option value="Blonde" <?php selected($controller->get('hair_color'), 'Blonde'); ?>>Blonde</option>
                                <option value="Auburn" <?php selected($controller->get('hair_color'), 'Auburn'); ?>>Auburn</option>
                                <option value="Red" <?php selected($controller->get('hair_color'), 'Red'); ?>>Red</option>
                                <option value="Gray" <?php selected($controller->get('hair_color'), 'Gray'); ?>>Gray</option>
                                <option value="White" <?php selected($controller->get('hair_color'), 'White'); ?>>White</option>
                                <option value="Dyed - Other" <?php selected($controller->get('hair_color'), 'Dyed - Other'); ?>>Dyed - Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="dressSize">Dress Size</label>
                            <input type="text" id="dressSize" name="dressSize" placeholder="M or 8"
                                value="<?php echo esc_attr($controller->get('dress_size')); ?>">
                        </div>
                        <div class="form-group">
                            <label for="shirtSize">Shirt Size</label>
                            <select id="shirtSize" name="shirtSize">
                                <option value="">Select Shirt Size</option>
                                <option value="XS" <?php selected($controller->get('shirt_size'), 'XS'); ?>>XS (Extra Small)</option>
                                <option value="S" <?php selected($controller->get('shirt_size'), 'S'); ?>>S (Small)</option>
                                <option value="M" <?php selected($controller->get('shirt_size'), 'M'); ?>>M (Medium)</option>
                                <option value="L" <?php selected($controller->get('shirt_size'), 'L'); ?>>L (Large)</option>
                                <option value="XL" <?php selected($controller->get('shirt_size'), 'XL'); ?>>XL (Extra Large)</option>
                                <option value="XXL" <?php selected($controller->get('shirt_size'), 'XXL'); ?>>XXL (2XL)</option>
                                <option value="XXXL" <?php selected($controller->get('shirt_size'), 'XXXL'); ?>>XXXL (3XL)</option>
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
                                <input type="text" id="instagram" name="instagram" placeholder="@username"
                                    value="<?php echo esc_attr($controller->get('instagram')); ?>">
                                <label class="checkbox-label" style="font-weight: normal; margin-top: 5px;">
                                    <input type="checkbox" name="hideInstagram" value="1" 
                                        <?php checked($controller->get('hide_instagram'), '1'); ?>>
                                    <span class="control-indicator"></span>
                                    <span>Hide my Instagram from public view</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="tiktok">TikTok</label>
                                <input type="text" id="tiktok" name="tiktok" placeholder="@username"
                                    value="<?php echo esc_attr($controller->get('tiktok')); ?>">
                                <label class="checkbox-label" style="font-weight: normal; margin-top: 5px;">
                                    <input type="checkbox" name="hideTiktok" value="1" 
                                        <?php checked($controller->get('hide_tiktok'), '1'); ?>>
                                    <span class="control-indicator"></span>
                                    <span>Hide my TikTok from public view</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="website">Personal Website</label>
                                <input type="url" id="website" name="website" placeholder="https://..."
                                    value="<?php echo esc_attr($controller->get('website')); ?>">
                                <label class="checkbox-label" style="font-weight: normal; margin-top: 5px;">
                                    <input type="checkbox" name="hideWebsite" value="1" 
                                        <?php checked($controller->get('hide_website'), '1'); ?>>
                                    <span class="control-indicator"></span>
                                    <span>Hide my website from public view</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <?php 
                    $has_paid_approval = get_post_meta($controller->get_talent_id(), '_talent_has_paid_approval', true);
                    if (!$has_paid_approval) : 
                    ?>
                    <!-- Verification Fee Notification -->
                    <div class="verification-fee-notification">
                        <div class="notification-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <div class="notification-content">
                            <h3>Profile Verification Required</h3>
                            <p>By proceeding, you agree to pay <strong>$5 </strong>for profile verification. for review
                                and approval.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Navigation Controls -->
                <?php get_template_part('templates/talent-submission/navigation-controls'); ?>
                
            </form>
        </div>
    </div>

    <script>
        // Portfolio data for edit page
        const portfolioData = <?php echo wp_json_encode($controller->get('portfolio_images')); ?>;
        const existingPortfolioCoverChoice = <?php echo wp_json_encode($controller->get('portfolio_cover_choice')); ?>;
        
        // Pre-fetch existing portfolio attachments server-side for instant display
        const existingPortfolioAttachments = [];
        <?php 
        $portfolio_image_ids = $controller->get('portfolio_images');
        if (!empty($portfolio_image_ids) && is_array($portfolio_image_ids)) {
            foreach ($portfolio_image_ids as $attach_id) {
                $attach_id = intval($attach_id);
                if ($attach_id > 0) {
                    $mime = get_post_mime_type($attach_id);
                    $is_video = $mime && strpos($mime, 'video/') === 0;
                    $thumb_url = wp_get_attachment_image_url($attach_id, 'thumbnail');
                    $title = get_the_title($attach_id);
                    $filename = basename(get_attached_file($attach_id));
        ?>
        existingPortfolioAttachments.push({id:<?php echo $attach_id;?>,title:<?php echo wp_json_encode($title);?>,filename:<?php echo wp_json_encode($filename);?>,thumbnail_url:<?php echo wp_json_encode($thumb_url);?>,is_video:<?php echo $is_video?'true':'false';?>});
        <?php
                }
            }
        }
        ?>
        
        // Create talentData object for the form - encapsulated in template
        // This is required for save progress functionality
        var talentData = {
            ajaxurl: '<?php echo esc_url($controller->get_ajax_url()); ?>',
            nonce: '<?php echo esc_attr($controller->get_nonce()); ?>',
            draft: null,
            isEditPage: 1
        };
        
        document.addEventListener('DOMContentLoaded', function () {
            const portfolioCoverChoiceInput = document.querySelector('input[name="portfolio_cover_choice"]');
            const portfolioExistingOrderInput = document.querySelector('input[name="portfolio_existing_order"]');
            if (portfolioCoverChoiceInput && !portfolioCoverChoiceInput.value && existingPortfolioCoverChoice) {
                portfolioCoverChoiceInput.value = existingPortfolioCoverChoice;
            }
            if (portfolioExistingOrderInput && !portfolioExistingOrderInput.value && Array.isArray(portfolioData)) {
                portfolioExistingOrderInput.value = portfolioData.join(',');
            }

            if (typeof showNotification === 'function') {
                console.log('Global JS is loaded and working on talent edit page');
            }
            
            // Load portfolio after role fields are shown
            setTimeout(function() {
                loadExistingPortfolio();
            }, 250);

            document.addEventListener('click', function(event) {
                const actionButton = event.target.closest('.portfolio-action-btn');
                if (!actionButton) {
                    return;
                }

                const item = actionButton.closest('.file-preview-item[data-portfolio-item-type="existing"]');
                if (!item) {
                    return;
                }

                const previewContainer = item.parentElement;
                const action = actionButton.dataset.portfolioAction;

                if (action === 'remove') {
                    item.remove();
                    updateExistingPortfolioOrder(previewContainer);
                    if (typeof window.syncPortfolioCoverSelection === 'function') {
                        window.syncPortfolioCoverSelection();
                    }
                    if (typeof window.updatePortfolioFeedback === 'function') {
                        window.updatePortfolioFeedback(previewContainer);
                    }
                    if (typeof window.syncTalentPortfolioDraftState === 'function') {
                        window.syncTalentPortfolioDraftState();
                    }
                    if (typeof window.saveTalentFormStateToLocalStorage === 'function') {
                        window.saveTalentFormStateToLocalStorage();
                    }
                    if (typeof window.queueTalentPortfolioAutosave === 'function') {
                        window.queueTalentPortfolioAutosave();
                    }
                }
            });
        });

        function escapePortfolioPreviewText(text) {
            return String(text || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function updateExistingPortfolioOrder(previewContainer) {
            const portfolioExistingOrderInput = document.querySelector('input[name="portfolio_existing_order"]');
            if (!portfolioExistingOrderInput || !previewContainer) {
                return;
            }

            const orderedIds = Array.from(
                previewContainer.querySelectorAll('.file-preview-item[data-portfolio-item-type="existing"]')
            ).map(item => item.dataset.attachmentId).filter(Boolean);

            portfolioExistingOrderInput.value = orderedIds.join(',');
        }
        
        // Function to load existing portfolio items
        function loadExistingPortfolio(retries = 0) {
            console.log('loadExistingPortfolio called, retry:', retries);
            console.log('PHP portfolioData value:', portfolioData);
            
            // Get portfolio data from either the PHP variable or the hidden input (which may have been restored from localStorage)
            let portfolioIds = portfolioData;
            const portfolioExistingOrderInput = document.querySelector('input[name="portfolio_existing_order"]');
            
            // If there's a value in the hidden input (restored from localStorage), use that instead
            if (portfolioExistingOrderInput && portfolioExistingOrderInput.value) {
                portfolioIds = portfolioExistingOrderInput.value.split(',').map(id => parseInt(id, 10)).filter(id => !isNaN(id));
                console.log('Using portfolio IDs from hidden input:', portfolioIds);
            } else {
                console.log('Using portfolio IDs from PHP variable:', portfolioIds);
            }
            
            if (!portfolioIds || portfolioIds.length === 0) {
                console.log('No existing portfolio items found');
                return;
            }

            let previewContainer = document.getElementById('portfolioPreviewShared');
            if (!previewContainer && retries < 3) {
                console.log('Preview container not found, retrying in 500ms...');
                setTimeout(() => loadExistingPortfolio(retries + 1), 500);
                return;
            }
            if (!previewContainer) {
                console.error('Preview container not found after retries');
                return;
            }

            const portfolioCoverChoiceInput = document.querySelector('input[name="portfolio_cover_choice"]');
            
            previewContainer.innerHTML = '';

            // Use pre-fetched attachment data from PHP server-side
            console.log('Using pre-fetched portfolio attachments:', existingPortfolioAttachments);
            
            if (existingPortfolioAttachments.length > 0) {
                existingPortfolioAttachments.forEach((attachment) => {
                    const item = document.createElement('div');
                    item.className = 'file-preview-item';
                    item.dataset.portfolioItemType = 'existing';
                    item.dataset.attachmentId = attachment.id;

                    const actionControls = `
                        <div class="portfolio-item-actions">
                            <button type="button" class="portfolio-action-btn danger" data-portfolio-action="remove" aria-label="Remove portfolio item">&times;</button>
                        </div>
                    `;

                    if (attachment.is_video) {
                        item.innerHTML = `
                            <div class="video-thumbnail-wrapper">
                                <img src="${attachment.thumbnail_url}" alt="${escapePortfolioPreviewText(attachment.title)}">
                                <div class="play-overlay">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="2">
                                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                    </svg>
                                </div>
                            </div>
                            <span class="file-preview-name">${escapePortfolioPreviewText(attachment.filename)}</span>
                            ${actionControls}
                        `;
                    } else {
                        const coverChoice = `existing:${attachment.id}`;
                        const isSelected = portfolioCoverChoiceInput && portfolioCoverChoiceInput.value === coverChoice;
                        item.innerHTML = `
                            <img src="${attachment.thumbnail_url}" alt="${escapePortfolioPreviewText(attachment.title)}">
                            <span class="file-preview-name">${escapePortfolioPreviewText(attachment.filename)}</span>
                            <label class="portfolio-cover-option ${isSelected ? 'selected' : ''}">
                                <input type="radio" class="portfolio-cover-radio" name="portfolio_cover_visual" value="${coverChoice}" ${isSelected ? 'checked' : ''}>
                                <span>Use as cover</span>
                            </label>
                            ${actionControls}
                        `;
                    }

                    previewContainer.appendChild(item);
                });

                updateExistingPortfolioOrder(previewContainer);
                if (typeof window.syncPortfolioCoverSelection === 'function') {
                    window.syncPortfolioCoverSelection();
                }
                if (typeof window.updatePortfolioFeedback === 'function') {
                    window.updatePortfolioFeedback(previewContainer);
                }
                if (typeof window.syncTalentPortfolioDraftState === 'function') {
                    window.syncTalentPortfolioDraftState();
                }
                if (typeof window.queueTalentPortfolioAutosave === 'function') {
                    window.queueTalentPortfolioAutosave();
                }
            } else {
                console.log('No pre-fetched attachments available, falling back to AJAX');
                // Fallback: use AJAX if no pre-fetched data
                const ajaxUrl = typeof talentData !== 'undefined' && talentData.ajaxurl 
                    ? talentData.ajaxurl 
                    : '<?php echo admin_url('admin-ajax.php'); ?>';
                
                Promise.all(
                    portfolioIds.map((attachmentId) =>
                        fetch(ajaxUrl + '?action=get_attachment_data&id=' + attachmentId, {
                            credentials: 'same-origin'
                        })
                            .then(response => response.ok ? response.json() : null)
                            .then(data => (data && data.success ? data.data : null))
                            .catch(() => null)
                    )
                ).then((attachments) => {
                    attachments.filter(Boolean).forEach((attachment) => {
                        const item = document.createElement('div');
                        item.className = 'file-preview-item';
                        item.dataset.portfolioItemType = 'existing';
                        item.dataset.attachmentId = attachment.id;

                        const actionControls = `
                            <div class="portfolio-item-actions">
                                <button type="button" class="portfolio-action-btn danger" data-portfolio-action="remove" aria-label="Remove portfolio item">&times;</button>
                            </div>
                        `;

                        if (attachment.is_video) {
                            item.innerHTML = `
                                <div class="video-thumbnail-wrapper">
                                    <img src="${attachment.thumbnail_url}" alt="${escapePortfolioPreviewText(attachment.title)}">
                                    <div class="play-overlay">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="2">
                                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                        </svg>
                                    </div>
                                </div>
                                <span class="file-preview-name">${escapePortfolioPreviewText(attachment.filename)}</span>
                                ${actionControls}
                            `;
                        } else {
                            const coverChoice = 'existing:' + attachment.id;
                            const isSelected = portfolioCoverChoiceInput && portfolioCoverChoiceInput.value === coverChoice;
                            item.innerHTML = `
                                <img src="${attachment.thumbnail_url}" alt="${escapePortfolioPreviewText(attachment.title)}">
                                <span class="file-preview-name">${escapePortfolioPreviewText(attachment.filename)}</span>
                                <label class="portfolio-cover-option ${isSelected ? 'selected' : ''}">
                                    <input type="radio" class="portfolio-cover-radio" name="portfolio_cover_visual" value="${coverChoice}" ${isSelected ? 'checked' : ''}>
                                    <span>Use as cover</span>
                                </label>
                                ${actionControls}
                            `;
                        }

                        previewContainer.appendChild(item);
                    });

                    updateExistingPortfolioOrder(previewContainer);
                    if (typeof window.syncPortfolioCoverSelection === 'function') {
                        window.syncPortfolioCoverSelection();
                    }
                    if (typeof window.updatePortfolioFeedback === 'function') {
                        window.updatePortfolioFeedback(previewContainer);
                    }
                    if (typeof window.syncTalentPortfolioDraftState === 'function') {
                        window.syncTalentPortfolioDraftState();
                    }
                    if (typeof window.queueTalentPortfolioAutosave === 'function') {
                        window.queueTalentPortfolioAutosave();
                    }
                });
            }
        }
        
        // Make the function globally available so it can be called from talent--submission.js
        window.loadExistingPortfolio = loadExistingPortfolio;
    </script>
    <?php wp_footer(); ?>
</body>

</html>
