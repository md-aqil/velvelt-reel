<?php
/**
 * Template Name: Talent Edit Form
 *
 * @package HelloElementorChild
 */

// Check if user is logged in
if (function_exists('is_user_logged_in') && !is_user_logged_in()) {
    // Redirect to login page
    if (function_exists('home_url') && function_exists('wp_redirect')) {
        wp_redirect(home_url('/sign-in/'));
        exit;
    }
}

// Get the talent ID from the URL parameter
$talent_id = isset($_GET['talent_id']) ? intval($_GET['talent_id']) : 0;

// Check if talent ID is provided
if (!$talent_id) {
    if (function_exists('get_post_type_archive_link') && function_exists('wp_redirect')) {
        wp_redirect(get_post_type_archive_link('talent'));
        exit;
    }
}

// Check if user is the author of this talent profile
if (function_exists('get_post_field') && function_exists('get_current_user_id') && function_exists('wp_die')) {
    if (get_post_field('post_author', $talent_id) != get_current_user_id()) {
        wp_die('You do not have permission to edit this profile.');
    }
}

// Get the talent post
if (function_exists('get_post')) {
    $talent_post = get_post($talent_id);
} else {
    $talent_post = null;
}

// Check if talent post exists and is of correct type
if (!$talent_post || (isset($talent_post->post_type) && $talent_post->post_type !== 'talent')) {
    if (function_exists('get_post_type_archive_link') && function_exists('wp_redirect')) {
        wp_redirect(get_post_type_archive_link('talent'));
        exit;
    }
}

// Get all meta data for the talent profile
$talent_meta = get_post_meta($talent_id);

// Initialize arrays to prevent undefined variable warnings
$languages = array();
$available_for = array();
$notable_works = array();
$design_categories = array();
$portfolio_images = array();

// Extract individual meta values
$full_name = $talent_post->post_title;
$style_description = $talent_post->post_content;

// Profile Basics
$city = get_post_meta($talent_id, '_talent_city', true);
$country = get_post_meta($talent_id, '_talent_country', true);
$email = get_post_meta($talent_id, '_talent_email', true);
$phone = get_post_meta($talent_id, '_talent_phone', true);
$age_group = get_post_meta($talent_id, '_talent_age_group', true);
$gender = get_post_meta($talent_id, '_talent_gender', true);
$height = get_post_meta($talent_id, '_talent_height', true);
$height_unit = get_post_meta($talent_id, '_talent_height_unit', true);
$measurements = get_post_meta($talent_id, '_talent_measurements', true);
$languages = get_post_meta($talent_id, '_talent_languages', true);

// Experience
$years_active = get_post_meta($talent_id, '_talent_years_active', true);
$affiliation = get_post_meta($talent_id, '_talent_affiliation', true);
$education = get_post_meta($talent_id, '_talent_education', true);
$interested_projects = get_post_meta($talent_id, '_talent_interested_projects', true);

// Portfolio Summary
$notable_works = get_post_meta($talent_id, '_talent_notable_works', true);

// Availability & Preferences
$available_for = get_post_meta($talent_id, '_talent_available_for', true);
$willing_to_travel = get_post_meta($talent_id, '_talent_willing_to_travel', true);
$preferred_locations = get_post_meta($talent_id, '_talent_preferred_locations', true);
$brand_collabs = get_post_meta($talent_id, '_talent_brand_collabs', true);

// Domain & Role
$domain = get_post_meta($talent_id, '_talent_domain', true);
$role = get_post_meta($talent_id, '_talent_role', true);

// Role-specific details
$design_categories = get_post_meta($talent_id, '_talent_designCategories', true);

// Social Links
$instagram = get_post_meta($talent_id, '_talent_instagram', true);
$linkedin = get_post_meta($talent_id, '_talent_linkedin', true);
$website = get_post_meta($talent_id, '_talent_website', true);

// Portfolio gallery
$portfolio_images = get_post_meta($talent_id, '_talent_portfolio', true);

// Ensure arrays are properly initialized
if (!is_array($languages)) $languages = array();
if (!is_array($available_for)) $available_for = array();
if (!is_array($notable_works)) $notable_works = array();
if (!is_array($design_categories)) $design_categories = array();
if (!is_array($portfolio_images)) $portfolio_images = array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Talent Profile - VelvetReel</title>    
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/form-style.css?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>">
</head>
<body>
    <div class="form-container">
        <!-- Progress Sidebar -->
        <div class="progress-sidebar">
            <div class="logo">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="https://chaitu.livewebsite.space/wp-content/uploads/2025/04/velvetreel.png" alt="VelvetReel Logo">
                </a>
                <div class="theme-switcher">
                    <div class="theme-toggle" id="themeToggle">
                        <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                        <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="progress-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-info">
                        <div class="step-title">Profile Basics</div>
                        <div class="step-subtitle">Personal information</div>
                    </div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-info">
                        <div class="step-title">Experience</div>
                        <div class="step-subtitle">Your background</div>
                    </div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-info">
                        <div class="step-title">Portfolio</div>
                        <div class="step-subtitle">Showcase work</div>
                    </div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-info">
                        <div class="step-title">Availability</div>
                        <div class="step-subtitle">Preferences</div>
                    </div>
                </div>
                <div class="step" data-step="5">
                    <div class="step-number">5</div>
                    <div class="step-info">
                        <div class="step-title">Domain</div>
                        <div class="step-subtitle">Your field</div>
                    </div>
                </div>
                <div class="step" data-step="6">
                    <div class="step-number">6</div>
                    <div class="step-info">
                        <div class="step-title">Role</div>
                        <div class="step-subtitle">Specialization</div>
                    </div>
                </div>
                <div class="step" data-step="7">
                    <div class="step-number">7</div>
                    <div class="step-info">
                        <div class="step-title">Details</div>
                        <div class="step-subtitle">Role-specific</div>
                    </div>
                </div>
                <div class="step" data-step="8">
                    <div class="step-number">8</div>
                    <div class="step-info">
                        <div class="step-title">Preview</div>
                        <div class="step-subtitle">Review & publish</div>
                    </div>
                </div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-percentage">0% Completed</div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <!-- Main Form Area -->
        <div class="form-main">
            <form id="velvetReelEditForm" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('talent_update', 'talent_update_nonce'); ?>
                <input type="hidden" name="action" value="update_talent_profile">
                <input type="hidden" name="post_id" value="<?php echo esc_attr($talent_id); ?>">
                
                <!-- STEP 1: Profile Basics -->
                <div class="form-step active" data-step="1">
                    <h2>Profile Basics</h2>
                    <p class="step-description">Edit your essential information</p>
                    
                    <div class="profile-photo-section">
                        <div class="photo-upload">
                            <div class="photo-preview" id="photoPreview">
                                <?php if (has_post_thumbnail($talent_id)) : ?>
                                    <?php echo get_the_post_thumbnail($talent_id, 'thumbnail', array('id' => 'currentPhoto')); ?>
                                <?php else : ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" style="display: none;">
                            <button type="button" class="upload-btn" onclick="document.getElementById('profilePhoto').click()">Change Photo</button>
                            <p class="upload-hint">500x500px, Max 5MB</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="fullName">Full Name <span class="required">*</span></label>
                            <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" value="<?php echo esc_attr($full_name); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City <span class="required">*</span></label>
                            <input type="text" id="city" name="city" placeholder="Mumbai" value="<?php echo esc_attr($city); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="country">Country <span class="required">*</span></label>
                            <select id="country" name="country" required>
                                <option value="">Select Country</option>
                                <option value="India" <?php selected($country, 'India'); ?>>India</option>
                                <option value="USA" <?php selected($country, 'USA'); ?>>USA</option>
                                <option value="UK" <?php selected($country, 'UK'); ?>>UK</option>
                                <option value="UAE" <?php selected($country, 'UAE'); ?>>UAE</option>
                                <option value="Other" <?php selected($country, 'Other'); ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Contact Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="your.email@example.com" value="<?php echo esc_attr($email); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" placeholder="+91 98765 43210" value="<?php echo esc_attr($phone); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ageGroup">Age Group</label>
                            <select id="ageGroup" name="ageGroup">
                                <option value="">Select Age Group</option>
                                <option value="18-25" <?php selected($age_group, '18-25'); ?>>18-25</option>
                                <option value="26-35" <?php selected($age_group, '26-35'); ?>>26-35</option>
                                <option value="36-45" <?php selected($age_group, '36-45'); ?>>36-45</option>
                                <option value="46-55" <?php selected($age_group, '46-55'); ?>>46-55</option>
                                <option value="56+" <?php selected($age_group, '56+'); ?>>56+</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="gender" value="Male" <?php checked($gender, 'Male'); ?>><span class="control-indicator"></span>
                                    <span>Male</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="gender" value="Female" <?php checked($gender, 'Female'); ?>><span class="control-indicator"></span>
                                    <span>Female</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="gender" value="Non-binary" <?php checked($gender, 'Non-binary'); ?>><span class="control-indicator"></span>
                                    <span>Non-binary</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-row conditional-fields" id="physicalDetailsSection" style="display: none;">
                        <div class="form-group">
                            <label for="height">Height</label>
                            <div class="input-with-unit">
                                <input type="number" id="height" name="height" placeholder="170" value="<?php echo esc_attr($height); ?>">
                                <select id="heightUnit" name="heightUnit">
                                    <option value="cm" <?php selected($height_unit, 'cm'); ?>>cm</option>
                                    <option value="ft" <?php selected($height_unit, 'ft'); ?>>ft</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="measurements">Measurements</label>
                            <input type="text" id="measurements" name="measurements" placeholder="34-26-36" value="<?php echo esc_attr($measurements); ?>">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Languages Known <span class="required">*</span></label>
                        <div class="custom-multi-select">
                            <div class="select-box" id="languageSelectBox">
                                <div class="select-box-header">
                                    <span class="placeholder">Select languages</span>
                                    <span class="arrow">▼</span>
                                </div>
                                <div class="select-box-options" id="languageOptions">
                                    <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="English" <?php if (in_array('English', $languages)) echo 'checked'; ?>>
                                        <span class="option-text">English</span>
                                    </label>
                                    <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="Spanish" <?php if (in_array('Spanish', $languages)) echo 'checked'; ?>>
                                        <span class="option-text">Spanish</span>
                                    </label>
                                    <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="French" <?php if (in_array('French', $languages)) echo 'checked'; ?>>
                                        <span class="option-text">French</span>
                                    </label>
                                    <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="Russian" <?php if (in_array('Russian', $languages)) echo 'checked'; ?>>
                                        <span class="option-text">Russian</span>
                                    </label>
                                    <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="Japanese" <?php if (in_array('Japanese', $languages)) echo 'checked'; ?>>
                                        <span class="option-text">Japanese</span>
                                    </label>
                                    <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="Hindi" <?php if (in_array('Hindi', $languages)) echo 'checked'; ?>>
                                        <span class="option-text">Hindi</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Experience -->
                <div class="form-step" data-step="2">
                    <h2>Experience</h2>
                    <p class="step-description">Tell us about your professional background</p>

                    <div class="form-group full-width">
                        <label for="yearsActive">How long have you been active in this field? <span class="required">*</span></label>
                        <select id="yearsActive" name="yearsActive" required>
                            <option value="">Select Experience</option>
                            <option value="Less than 1 year" <?php selected($years_active, 'Less than 1 year'); ?>>Less than 1 year</option>
                            <option value="1-2 years" <?php selected($years_active, '1-2 years'); ?>>1-2 years</option>
                            <option value="3-5 years" <?php selected($years_active, '3-5 years'); ?>>3-5 years</option>
                            <option value="5-10 years" <?php selected($years_active, '5-10 years'); ?>>5-10 years</option>
                            <option value="10-15 years" <?php selected($years_active, '10-15 years'); ?>>10-15 years</option>
                            <option value="15+ years" <?php selected($years_active, '15+ years'); ?>>15+ years</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label>Current Affiliation <span class="required">*</span></label>
                        <div class="radio-cards">
                            <label class="radio-card">
                                <input type="radio" name="affiliation" value="Freelancer" <?php checked($affiliation, 'Freelancer'); ?> required>
                                <div class="card-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <span>Freelancer</span>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="affiliation" value="Agency" <?php checked($affiliation, 'Agency'); ?> required>
                                <div class="card-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                    </svg>
                                    <span>Agency</span>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="affiliation" value="Brand/Studio" <?php checked($affiliation, 'Brand/Studio'); ?> required>
                                <div class="card-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    </svg>
                                    <span>Brand/Studio</span>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="affiliation" value="Self-Employed" <?php checked($affiliation, 'Self-Employed'); ?> required>
                                <div class="card-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="16"></line>
                                        <line x1="8" y1="12" x2="16" y2="12"></line>
                                    </svg>
                                    <span>Self-Employed</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="education">Education / Training</label>
                        <textarea id="education" name="education" rows="4" maxlength="500" placeholder="List institutes, mentors, certifications, or training programs"><?php echo esc_textarea($education); ?></textarea>
                        <div class="char-counter"><span id="educationCount"><?php echo strlen($education); ?></span>/500</div>
                    </div>
                </div>

                <!-- STEP 3: Portfolio Summary -->
                <div class="form-step" data-step="3">
                    <h2>Portfolio Summary</h2>
                    <p class="step-description">Showcase your unique creative identity</p>

                    <div class="form-group full-width">
                        <label for="styleDescription">Describe your style or specialization <span class="required">*</span></label>
                        <textarea id="styleDescription" name="styleDescription" rows="4" maxlength="500" placeholder="Describe your unique creative approach, aesthetic, or what sets your work apart" required><?php echo esc_textarea($style_description); ?></textarea>
                        <div class="char-counter"><span id="styleCount"><?php echo strlen($style_description); ?></span>/500</div>
                    </div>

                    <div class="form-group full-width">
                        <label for="interestedProjects">What kind of projects are you interested in next?</label>
                        <textarea id="interestedProjects" name="interestedProjects" rows="3" maxlength="300" placeholder="What kind of opportunities or collaborations are you seeking next?"><?php echo esc_textarea($interested_projects); ?></textarea>
                        <div class="char-counter"><span id="projectsCount"><?php echo strlen($interested_projects); ?></span>/300</div>
                    </div>

                    <div class="form-group full-width">
                        <label>Notable Works (up to 10)</label>
                        <div id="notableWorksContainer">
                            <?php if (!empty($notable_works) && is_array($notable_works)) : ?>
                                <?php foreach ($notable_works as $index => $work) : ?>
                                    <div class="notable-work-item">
                                        <button type="button" class="remove-work-btn">&times;</button>
                                        <div class="form-group full-width">
                                            <input type="text" name="workTitle[]" placeholder="Project Title" value="<?php echo esc_attr($work['title']); ?>">
                                        </div>
                                        <div class="form-group full-width">
                                            <input type="text" name="workRole[]" placeholder="Your Role (e.g., Director, Lead Actor)" value="<?php echo esc_attr($work['role']); ?>">
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <input type="month" name="workStartDate[]" value="<?php echo esc_attr($work['startDate']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>End Date</label>
                                                <input type="month" name="workEndDate[]" class="work-end-date" value="<?php echo esc_attr($work['endDate']); ?>" <?php if (isset($work['present']) && $work['present'] === 'on') echo 'disabled'; ?>>
                                            </div>
                                        </div>
                                        <div class="form-group full-width">
                                            <label class="checkbox-label" style="font-weight: normal;">
                                                <input type="checkbox" name="workPresent[]" value="on" class="work-present-checkbox" <?php if (isset($work['present']) && $work['present'] === 'on') echo 'checked'; ?>>
                                                <span class="control-indicator"></span><span>I currently work here</span>
                                            </label>
                                        </div>
                                        <div class="form-group full-width">
                                            <textarea name="workDescription[]" rows="2" maxlength="150" placeholder="Brief Description (150 char)"><?php echo esc_textarea($work['description']); ?></textarea>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="notable-work-item">
                                    <button type="button" class="remove-work-btn" style="display: none;">&times;</button>
                                    <div class="form-group full-width">
                                        <input type="text" name="workTitle[]" placeholder="Project Title">
                                    </div>
                                    <div class="form-group full-width">
                                        <input type="text" name="workRole[]" placeholder="Your Role (e.g., Director, Lead Actor)">
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
                                            <input type="checkbox" name="workPresent[]" value="on" class="work-present-checkbox">
                                            <span class="control-indicator"></span><span>I currently work here</span>
                                        </label>
                                    </div>
                                    <div class="form-group full-width">
                                        <textarea name="workDescription[]" rows="2" maxlength="150" placeholder="Brief Description (150 char)"></textarea>
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

                    <div class="form-group full-width">
                        <label>Available For <span class="required">*</span></label>
                        <div class="checkbox-grid">
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Freelance Projects" <?php if (in_array('Freelance Projects', $available_for)) echo 'checked'; ?>><span class="control-indicator"></span>
                                <span>Freelance Projects</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Full-time Opportunities" <?php if (in_array('Full-time Opportunities', $available_for)) echo 'checked'; ?>><span class="control-indicator"></span>
                                <span>Full-time Opportunities</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Collaborations" <?php if (in_array('Collaborations', $available_for)) echo 'checked'; ?>><span class="control-indicator"></span>
                                <span>Collaborations</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Consulting" <?php if (in_array('Consulting', $available_for)) echo 'checked'; ?>><span class="control-indicator"></span>
                                <span>Consulting</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Willing to Travel?</label>
                        <div class="toggle-group">
                            <label class="toggle-switch">
                                <input type="checkbox" id="willingToTravel" name="willingToTravel" value="on" <?php checked($willing_to_travel, 'on'); ?>>
                                <span class="toggle-slider"></span>
                                <span class="toggle-label">Yes, I'm willing to travel</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group full-width" id="preferredLocationsSection" style="<?php echo ($willing_to_travel === 'on') ? '' : 'display: none;'; ?>">
                        <label for="preferredLocations">Preferred Work Locations</label>
                        <input type="text" id="preferredLocations" name="preferredLocations" placeholder="New York, LA" value="<?php echo esc_attr($preferred_locations); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label>Open to Brand Collaborations?</label>
                        <div class="toggle-group">
                            <label class="toggle-switch">
                                <input type="checkbox" id="brandCollabs" name="brandCollabs" value="on" <?php checked($brand_collabs, 'on'); ?>>
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
                        <div class="domain-card <?php echo ($domain === 'fashion') ? 'selected' : ''; ?>" data-domain="fashion">
                            <div class="domain-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                </svg>
                            </div>
                            <h3>Fashion & Design</h3>
                            <p>Designers, Models, Stylists, and Fashion Creatives</p>
                        </div>
                        <div class="domain-card <?php echo ($domain === 'film') ? 'selected' : ''; ?>" data-domain="film">
                            <div class="domain-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect>
                                    <line x1="7" y1="2" x2="7" y2="22"></line>
                                    <line x1="17" y1="2" x2="17" y2="22"></line>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                </svg>
                            </div>
                            <h3>Film & Creative Arts</h3>
                            <p>Directors, Actors, Crew, and Production Specialists</p>
                        </div>
                    </div>
                    <input type="hidden" id="selectedDomain" name="domain" value="<?php echo esc_attr($domain); ?>">
                </div>

                <!-- STEP 6: Role Selection -->
                <div class="form-step" data-step="6">
                    <h2>Select Your Role</h2>
                    <p class="step-description">Choose your specific specialization</p>

                    <div id="roleContainer">
                        <!-- Fashion Roles -->
                        <div class="role-category" data-domain="fashion" style="<?php echo ($domain === 'fashion') ? '' : 'display: none;'; ?>">
                            <h3>Design & Creative</h3>
                            <div class="role-grid">
                                <div class="role-card <?php echo ($role === 'fashion-designer') ? 'selected' : ''; ?>" data-role="fashion-designer">
                                    <h4>Fashion Designer</h4>
                                    <p>Create clothing & collections</p>
                                </div>
                                <div class="role-card <?php echo ($role === 'textile-designer') ? 'selected' : ''; ?>" data-role="textile-designer">
                                    <h4>Textile Designer</h4>
                                    <p>Print & surface design</p>
                                </div>
                                <div class="role-card <?php echo ($role === 'accessory-designer') ? 'selected' : ''; ?>" data-role="accessory-designer">
                                    <h4>Accessory Designer</h4>
                                    <p>Bags, shoes, jewelry</p>
                                </div>
                                <div class="role-card <?php echo ($role === 'fashion-illustrator') ? 'selected' : ''; ?>" data-role="fashion-illustrator">
                                    <h4>Fashion Illustrator</h4>
                                    <p>Sketches & visual concepts</p>
                                </div>
                            </div>

                            <h3>Modeling & Presentation</h3>
                            <div class="role-grid">
                                <div class="role-card <?php echo ($role === 'fashion-model') ? 'selected' : ''; ?>" data-role="fashion-model">
                                    <h4>Fashion Model</h4>
                                    <p>Runway, print, commercial</p>
                                </div>
                                <div class="role-card <?php echo ($role === 'ramp-choreographer') ? 'selected' : ''; ?>" data-role="ramp-choreographer">
                                    <h4>Ramp Choreographer</h4>
                                    <p>Show direction & staging</p>
                                </div>
                            </div>

                            <h3>Styling & Beauty</h3>
                            <div class="role-grid">
                                <div class="role-card <?php echo ($role === 'fashion-stylist') ? 'selected' : ''; ?>" data-role="fashion-stylist">
                                    <h4>Fashion Stylist</h4>
                                    <p>Editorial & personal styling</p>
                                </div>
                                <div class="role-card <?php echo ($role === 'makeup-artist') ? 'selected' : ''; ?>" data-role="makeup-artist">
                                    <h4>Makeup Artist</h4>
                                    <p>Bridal, film, editorial</p>
                                </div>
                            </div>
                        </div>

                        <!-- Film Roles -->
                        <div class="role-category" data-domain="film" style="<?php echo ($domain === 'film') ? '' : 'display: none;'; ?>">
                            <h3>Direction & Creative</h3>
                            <div class="role-grid">
                                <div class="role-card <?php echo ($role === 'director') ? 'selected' : ''; ?>" data-role="director">
                                    <h4>Director</h4>
                                    <p>Film & creative direction</p>
                                </div>
                                <div class="role-card <?php echo ($role === 'storyboard-artist') ? 'selected' : ''; ?>" data-role="storyboard-artist">
                                    <h4>Storyboard Artist</h4>
                                    <p>Visual script planning</p>
                                </div>
                            </div>

                            <h3>Acting & Performance</h3>
                            <div class="role-grid">
                                <div class="role-card <?php echo ($role === 'actor') ? 'selected' : ''; ?>" data-role="actor">
                                    <h4>Actor/Actress</h4>
                                    <p>Film, TV, theatre</p>
                                </div>
                                <div class="role-card <?php echo ($role === 'voice-actor') ? 'selected' : ''; ?>" data-role="voice-actor">
                                    <h4>Voice Actor</h4>
                                    <p>Dubbing & narration</p>
                                </div>
                                <div class="role-card <?php echo ($role === 'dancer') ? 'selected' : ''; ?>" data-role="dancer">
                                    <h4>Dancer/Choreographer</h4>
                                    <p>Performance & teaching</p>
                                </div>
                            </div>

                            <h3>Cinematography & Visual</h3>
                            <div class="role-grid">
                                <div class="role-card <?php echo ($role === 'dop') ? 'selected' : ''; ?>" data-role="dop">
                                    <h4>DOP/Camera Crew</h4>
                                    <p>Cinematography & lighting</p>
                                </div>
                                <div class="role-card <?php echo ($role === 'editor') ? 'selected' : ''; ?>" data-role="editor">
                                    <h4>Editor/VFX Artist</h4>
                                    <p>Post-production specialist</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selectedRole" name="role" value="<?php echo esc_attr($role); ?>">
                </div>

                <!-- STEP 7: Role-Specific Questions (Dynamic) -->
                <div class="form-step" data-step="7">
                    <h2 id="roleSpecificTitle">Role-Specific Details</h2>
                    <p class="step-description">Tell us more about your specialization</p>

                    <!-- Fashion Designer Fields -->
                    <div class="role-specific-fields" data-role-specific="fashion-designer" style="<?php echo ($role === 'fashion-designer') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Fashion Designer Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Menswear" <?php if (in_array('Menswear', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Menswear</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Womenswear" <?php if (in_array('Womenswear', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Womenswear</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Kidswear" <?php if (in_array('Kidswear', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Kidswear</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Unisex" <?php if (in_array('Unisex', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Unisex</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio" name="portfolio[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreview">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Textile Designer Fields -->
                    <div class="role-specific-fields" data-role-specific="textile-designer" style="<?php echo ($role === 'textile-designer') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Textile Designer Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Apparel Textiles" <?php if (in_array('Apparel Textiles', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Apparel Textiles</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Home Furnishing" <?php if (in_array('Home Furnishing', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Home Furnishing</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Technical Textiles" <?php if (in_array('Technical Textiles', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Technical Textiles</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Sustainable Materials" <?php if (in_array('Sustainable Materials', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Sustainable Materials</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-textile" name="portfolio-textile[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewTextile">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Accessory Designer Fields -->
                    <div class="role-specific-fields" data-role-specific="accessory-designer" style="<?php echo ($role === 'accessory-designer') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Accessory Designer Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Jewelry" <?php if (in_array('Jewelry', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Jewelry</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Bags & Handbags" <?php if (in_array('Bags & Handbags', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Bags & Handbags</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Shoes & Footwear" <?php if (in_array('Shoes & Footwear', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Shoes & Footwear</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Watches & Accessories" <?php if (in_array('Watches & Accessories', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Watches & Accessories</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-accessory" name="portfolio-accessory[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewAccessory">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fashion Illustrator Fields -->
                    <div class="role-specific-fields" data-role-specific="fashion-illustrator" style="<?php echo ($role === 'fashion-illustrator') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Fashion Illustrator Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Fashion Sketches" <?php if (in_array('Fashion Sketches', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Fashion Sketches</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Technical Drawings" <?php if (in_array('Technical Drawings', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Technical Drawings</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Digital Illustrations" <?php if (in_array('Digital Illustrations', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Digital Illustrations</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Concept Art" <?php if (in_array('Concept Art', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Concept Art</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-illustrator" name="portfolio-illustrator[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewIllustrator">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fashion Model Fields -->
                    <div class="role-specific-fields" data-role-specific="fashion-model" style="<?php echo ($role === 'fashion-model') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Fashion Model Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Runway" <?php if (in_array('Runway', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Runway</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Editorial" <?php if (in_array('Editorial', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Editorial</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Commercial" <?php if (in_array('Commercial', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Commercial</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Fitness & Lifestyle" <?php if (in_array('Fitness & Lifestyle', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Fitness & Lifestyle</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-model" name="portfolio-model[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewModel">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ramp Choreographer Fields -->
                    <div class="role-specific-fields" data-role-specific="ramp-choreographer" style="<?php echo ($role === 'ramp-choreographer') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Ramp Choreographer Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Fashion Shows" <?php if (in_array('Fashion Shows', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Fashion Shows</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Music Videos" <?php if (in_array('Music Videos', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Music Videos</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Commercial Events" <?php if (in_array('Commercial Events', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Commercial Events</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Wedding Choreography" <?php if (in_array('Wedding Choreography', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Wedding Choreography</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-choreographer" name="portfolio-choreographer[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewChoreographer">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fashion Stylist Fields -->
                    <div class="role-specific-fields" data-role-specific="fashion-stylist" style="<?php echo ($role === 'fashion-stylist') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Fashion Stylist Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Editorial Styling" <?php if (in_array('Editorial Styling', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Editorial Styling</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Personal Styling" <?php if (in_array('Personal Styling', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Personal Styling</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Celebrity Styling" <?php if (in_array('Celebrity Styling', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Celebrity Styling</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Commercial Styling" <?php if (in_array('Commercial Styling', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Commercial Styling</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-stylist" name="portfolio-stylist[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewStylist">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Makeup Artist Fields -->
                    <div class="role-specific-fields" data-role-specific="makeup-artist" style="<?php echo ($role === 'makeup-artist') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Makeup Artist Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Bridal Makeup" <?php if (in_array('Bridal Makeup', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Bridal Makeup</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Editorial Makeup" <?php if (in_array('Editorial Makeup', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Editorial Makeup</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Special Effects" <?php if (in_array('Special Effects', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Special Effects</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Beauty & Cosmetics" <?php if (in_array('Beauty & Cosmetics', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Beauty & Cosmetics</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-makeup" name="portfolio-makeup[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewMakeup">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Director Fields -->
                    <div class="role-specific-fields" data-role-specific="director" style="<?php echo ($role === 'director') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Director Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Feature Films" <?php if (in_array('Feature Films', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Feature Films</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Short Films" <?php if (in_array('Short Films', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Short Films</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Documentaries" <?php if (in_array('Documentaries', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Documentaries</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Commercials" <?php if (in_array('Commercials', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Commercials</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-director" name="portfolio-director[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewDirector">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Storyboard Artist Fields -->
                    <div class="role-specific-fields" data-role-specific="storyboard-artist" style="<?php echo ($role === 'storyboard-artist') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Storyboard Artist Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Film Storyboards" <?php if (in_array('Film Storyboards', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Film Storyboards</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Animation Storyboards" <?php if (in_array('Animation Storyboards', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Animation Storyboards</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Advertising Boards" <?php if (in_array('Advertising Boards', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Advertising Boards</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Game Design" <?php if (in_array('Game Design', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Game Design</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-storyboard" name="portfolio-storyboard[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewStoryboard">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actor/Actress Fields -->
                    <div class="role-specific-fields" data-role-specific="actor" style="<?php echo ($role === 'actor') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Actor/Actress Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Film Acting" <?php if (in_array('Film Acting', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Film Acting</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Theater Acting" <?php if (in_array('Theater Acting', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Theater Acting</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="TV Series" <?php if (in_array('TV Series', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>TV Series</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Voice Acting" <?php if (in_array('Voice Acting', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Voice Acting</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-actor" name="portfolio-actor[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewActor">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Voice Actor Fields -->
                    <div class="role-specific-fields" data-role-specific="voice-actor" style="<?php echo ($role === 'voice-actor') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Voice Actor Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Animation Voices" <?php if (in_array('Animation Voices', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Animation Voices</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Audiobook Narration" <?php if (in_array('Audiobook Narration', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Audiobook Narration</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Commercial Voiceovers" <?php if (in_array('Commercial Voiceovers', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Commercial Voiceovers</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Video Game Voices" <?php if (in_array('Video Game Voices', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Video Game Voices</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-voice" name="portfolio-voice[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewVoice">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dancer/Choreographer Fields -->
                    <div class="role-specific-fields" data-role-specific="dancer" style="<?php echo ($role === 'dancer') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Dancer/Choreographer Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Ballet" <?php if (in_array('Ballet', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Ballet</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Contemporary" <?php if (in_array('Contemporary', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Contemporary</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Hip Hop" <?php if (in_array('Hip Hop', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Hip Hop</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Classical Dance" <?php if (in_array('Classical Dance', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Classical Dance</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-dancer" name="portfolio-dancer[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewDancer">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DOP/Camera Crew Fields -->
                    <div class="role-specific-fields" data-role-specific="dop" style="<?php echo ($role === 'dop') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>DOP/Camera Crew Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Cinematography" <?php if (in_array('Cinematography', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Cinematography</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Drone Photography" <?php if (in_array('Drone Photography', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Drone Photography</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Studio Photography" <?php if (in_array('Studio Photography', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Studio Photography</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Event Coverage" <?php if (in_array('Event Coverage', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Event Coverage</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-dop" name="portfolio-dop[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewDop">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Editor/VFX Artist Fields -->
                    <div class="role-specific-fields" data-role-specific="editor" style="<?php echo ($role === 'editor') ? '' : 'display: none;'; ?>">
                        <div class="form-group full-width">
                            <label>Editor/VFX Artist Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Video Editing" <?php if (in_array('Video Editing', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Video Editing</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Color Grading" <?php if (in_array('Color Grading', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Color Grading</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Visual Effects" <?php if (in_array('Visual Effects', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Visual Effects</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Motion Graphics" <?php if (in_array('Motion Graphics', $design_categories)) echo 'checked'; ?>><span class="control-indicator"></span><span>Motion Graphics</span></label>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label>Portfolio Gallery </label>
                            <div class="file-upload-area">
                                <input type="file" id="portfolio-editor" name="portfolio-editor[]" accept="image/*" multiple>
                                <div class="file-upload-instructions">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                    <p><strong>Drag & drop files here</strong> or click to browse</p>
                                    <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
                                </div>
                            </div>
                            <div class="file-upload-preview" id="portfolioPreviewEditor">
                                <?php if (!empty($portfolio_images) && is_array($portfolio_images)) : ?>
                                    <?php foreach ($portfolio_images as $image_id) : ?>
                                        <?php $image_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                                        <?php if ($image_url) : ?>
                                            <div class="preview-item" data-id="<?php echo esc_attr($image_id); ?>">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="Portfolio image">
                                                <button type="button" class="remove-preview">×</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
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
                                <input type="text" id="instagram" name="instagram" placeholder="@username" value="<?php echo esc_attr($instagram); ?>">
                            </div>
                            <div class="form-group">
                                <label for="linkedin">Youtube</label>
                                <input type="url" id="linkedin" name="linkedin" placeholder="https://youtube.com/..." value="<?php echo esc_attr($linkedin); ?>">
                            </div>
                        </div>
                         <div class="form-row">
                            <div class="form-group full-width">
                                <label for="website">Personal Website</label>
                                <input type="url" id="website" name="website" placeholder="https://..." value="<?php echo esc_attr($website); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navigation Buttons -->
                <div class="form-navigation">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Next Step</button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">Update Profile</button>
                </div>
            </form>
        </div>
    </div>


<script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/global.js?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/global.js?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/talent--submission.js?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>"></script>

<script>
// Test if global.js is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (typeof showNotification === 'function') {
        console.log('Global JS is loaded and working on talent edit page');
    } else {
        console.log('Global JS is not loaded on talent edit page');
    }
    
    // Pre-select domain and role
    var selectedDomain = document.getElementById('selectedDomain').value;
    var selectedRole = document.getElementById('selectedRole').value;
    
    if (selectedDomain) {
        var domainCard = document.querySelector('.domain-card[data-domain="' + selectedDomain + '"]');
        if (domainCard) {
            domainCard.classList.add('selected');
        }
    }
    
    if (selectedRole) {
        var roleCard = document.querySelector('.role-card[data-role="' + selectedRole + '"]');
        if (roleCard) {
            roleCard.classList.add('selected');
        }
    }
});
</script>
</body>
</html>