<?php
/**
 * Template Name: Talent Submission Form
 *
 * @package HelloElementorChild
 */

// Check for the success query parameter
if ( isset( $_GET['success'] ) && 'true' === $_GET['success'] ) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thank You!</title>
        
       
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/form-style.css?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>">
    </head>
    <body data-theme="dark">
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <h2 class="success-title">Thank You!</h2>
            <p class="success-message">Your talent profile has been submitted successfully and is now pending review. We will notify you once it has been approved.</p>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'talent' ) ); ?>" class="btn btn-primary">View Portfolios</a>
        </div>
    </div>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/global.js?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>"></script>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/talent--submission.js?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>"></script>
    <script>
    // Test if global.js is loaded
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof showNotification === 'function') {
            console.log('Global JS is loaded and working on success page');
        } else {
            console.log('Global JS is not loaded on success page');
        }
    });
    </script>
    </body></html><?php
    // We use return here to stop the rest of the form template from loading
    return;
}

// Check if user is logged in
if (!is_user_logged_in()) {
    // Redirect to login page
    wp_redirect(home_url('/sign-up/'));
    exit;
}

// Check if user already has a talent profile
if (function_exists('user_has_talent_profile') && user_has_talent_profile()) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile Exists</title>
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/form-style.css?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>">
    </head>
    <body data-theme="dark">
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <h2 class="success-title">Profile Already Exists</h2>
            <p class="success-message">You already have a talent profile in our system. You can view and edit your existing profile.</p>
            <a href="<?php echo esc_url(get_post_type_archive_link('talent')); ?>" class="btn btn-primary">View My Profile</a>
        </div>
    </div>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/global.js?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>"></script>
    <script>
    // Test if global.js is loaded
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof showNotification === 'function') {
            console.log('Global JS is loaded and working on profile exists page');
        } else {
            console.log('Global JS is not loaded on profile exists page');
        }
    });
    </script>
    </body></html><?php
    // We use return here to stop the rest of the form template from loading
    return;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VelvetReel Talent Registration</title>    
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/form-style.css?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>">
</head>
<body>
    <div class="form-container">
        <!-- Progress Sidebar -->
        <div class="progress-sidebar">
            <div class="logo">
<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <img src="https://chaitu.livewebsite.space/wp-content/uploads/2025/04/velvetreel.png" alt="VelvetReel Logo">
                <!-- Theme Switcher -->
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
            <form id="velvetReelForm" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field( 'talent_submission', 'talent_submission_nonce' ); ?>
                <input type="hidden" name="action" value="submit_talent_profile">
                
                <!-- STEP 1: Profile Basics -->
                <div class="form-step active" data-step="1">
                    <h2>Profile Basics</h2>
                    <p class="step-description">Let's start with your essential information</p>
                    
                    <div class="profile-photo-section">
                        <div class="photo-upload">
                            <div class="photo-preview" id="photoPreview">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" style="display: none;">
                            <button type="button" class="upload-btn" onclick="document.getElementById('profilePhoto').click()">Upload Photo</button>
                            <p class="upload-hint">500x500px, Max 5MB</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="fullName">Full Name <span class="required">*</span></label>
                            <input type="text" id="fullName" name="fullName" placeholder="Enter your full name">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City <span class="required">*</span></label>
                            <input type="text" id="city" name="city" placeholder="Mumbai">
                        </div>
                        <div class="form-group">
                            <label for="country">Country <span class="required">*</span></label>
                            <select id="country" name="country">
                                <option value="">Select Country</option>
                                <option value="India">India</option>
                                <option value="USA">USA</option>
                                <option value="UK">UK</option>
                                <option value="UAE">UAE</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Contact Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="your.email@example.com">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" placeholder="+91 98765 43210">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ageGroup">Age Group</label>
                            <select id="ageGroup" name="ageGroup">
                                <option value="">Select Age Group</option>
                                <option value="18-25">18-25</option>
                                <option value="26-35">26-35</option>
                                <option value="36-45">36-45</option>
                                <option value="46-55">46-55</option>
                                <option value="56+">56+</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="gender" value="Male"><span class="control-indicator"></span>
                                    <span>Male</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="gender" value="Female"><span class="control-indicator"></span>
                                    <span>Female</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="gender" value="Non-binary"><span class="control-indicator"></span>
                                    <span>Non-binary</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-row conditional-fields" id="physicalDetailsSection" style="display: none;">
                        <div class="form-group">
                            <label for="height">Height</label>
                            <div class="input-with-unit">
                                <input type="number" id="height" name="height" placeholder="170">
                                <select id="heightUnit" name="heightUnit">
                                    <option value="cm">cm</option>
                                    <option value="ft">ft</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="measurements">Measurements</label>
                            <input type="text" id="measurements" name="measurements" placeholder="34-26-36">
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
                                        <input type="checkbox" name="languages[]" value="English">
                                        <span class="option-text">English</span>
                                    </label>
                                   
                                    <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="Spanish">
                                        <span class="option-text">Spanish</span>
                                    </label>
                                    <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="French">
                                        <span class="option-text">French</span>
                                    </label>
                                    <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="Russian">
                                        <span class="option-text">Russian</span>
                                    </label>
                                    <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="Japanese">
                                        <span class="option-text">Japanese</span>
                                    </label>
                                     <label class="select-option">
                                        <input type="checkbox" name="languages[]" value="Hindi">
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
                                                <select id="yearsActive" name="yearsActive">
                            <option value="">Select Experience</option>
                            <option value="Less than 1 year">Less than 1 year</option>
                            <option value="1-2 years">1-2 years</option>
                            <option value="3-5 years">3-5 years</option>
                            <option value="5-10 years">5-10 years</option>
                            <option value="10-15 years">10-15 years</option>
                            <option value="15+ years">15+ years</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label>Current Affiliation <span class="required">*</span></label>
                        <div class="radio-cards">
                            <label class="radio-card">
                                <input type="radio" name="affiliation" value="Freelancer">
                                <div class="card-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <span>Freelancer</span>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="affiliation" value="Agency">
                                <div class="card-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                    </svg>
                                    <span>Agency</span>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="affiliation" value="Brand/Studio">
                                <div class="card-content">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    </svg>
                                    <span>Brand/Studio</span>
                                </div>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="affiliation" value="Self-Employed">
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
                        <textarea id="education" name="education" rows="4" maxlength="500" placeholder="List institutes, mentors, certifications, or training programs"></textarea>
                        <div class="char-counter"><span id="educationCount">0</span>/500</div>
                    </div>
                </div>

                <!-- STEP 3: Portfolio Summary -->
                <div class="form-step" data-step="3">
                    <h2>Portfolio Summary</h2>
                    <p class="step-description">Showcase your unique creative identity</p>

                    <div class="form-group full-width">
                        <label for="styleDescription">Describe your style or specialization <span class="required">*</span></label>
                        <textarea id="styleDescription" name="styleDescription" rows="4" maxlength="500" placeholder="Describe your unique creative approach, aesthetic, or what sets your work apart"></textarea>
                        <div class="char-counter"><span id="styleCount">0</span>/500</div>
                    </div>

                    <div class="form-group full-width">
                        <label for="interestedProjects">What kind of projects are you interested in next?</label>
                        <textarea id="interestedProjects" name="interestedProjects" rows="3" maxlength="300" placeholder="What kind of opportunities or collaborations are you seeking next?"></textarea>
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
                                <input type="checkbox" name="availableFor[]" value="Freelance Projects"><span class="control-indicator"></span>
                                <span>Freelance Projects</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Full-time Opportunities" ><span class="control-indicator"></span>
                                <span>Full-time Opportunities</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Collaborations" ><span class="control-indicator"></span>
                                <span>Collaborations</span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="availableFor[]" value="Consulting" ><span class="control-indicator"></span>
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
                        <div class="domain-card" data-domain="fashion">
                            <div class="domain-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                </svg>
                            </div>
                            <h3>Fashion & Design</h3>
                            <p>Designers, Models, Stylists, and Fashion Creatives</p>
                        </div>
                        <div class="domain-card" data-domain="film">
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
                    <input type="hidden" id="selectedDomain" name="domain">
                </div>


                <!-- STEP 6: Role Selection -->
                <div class="form-step" data-step="6">
                    <h2>Select Your Role</h2>
                    <p class="step-description">Choose your specific specialization</p>


                    <div id="roleContainer">
                        <!-- Fashion Roles -->
                        <div class="role-category" data-domain="fashion">
                            <h3>Design & Creative</h3>
                            <div class="role-grid">
                                <div class="role-card" data-role="fashion-designer">
                                    <h4>Fashion Designer</h4>
                                    <p>Create clothing & collections</p>
                                </div>
                                <div class="role-card" data-role="textile-designer">
                                    <h4>Textile Designer</h4>
                                    <p>Print & surface design</p>
                                </div>
                                <div class="role-card" data-role="accessory-designer">
                                    <h4>Accessory Designer</h4>
                                    <p>Bags, shoes, jewelry</p>
                                </div>
                                <div class="role-card" data-role="fashion-illustrator">
                                    <h4>Fashion Illustrator</h4>
                                    <p>Sketches & visual concepts</p>
                                </div>
                            </div>

                            <h3>Modeling & Presentation</h3>
                            <div class="role-grid">
                                <div class="role-card" data-role="fashion-model">
                                    <h4>Fashion Model</h4>
                                    <p>Runway, print, commercial</p>
                                </div>
                                <div class="role-card" data-role="ramp-choreographer">
                                    <h4>Ramp Choreographer</h4>
                                    <p>Show direction & staging</p>
                                </div>
                            </div>

                            <h3>Styling & Beauty</h3>
                            <div class="role-grid">
                                <div class="role-card" data-role="fashion-stylist">
                                    <h4>Fashion Stylist</h4>
                                    <p>Editorial & personal styling</p>
                                </div>
                                <div class="role-card" data-role="makeup-artist">
                                    <h4>Makeup Artist</h4>
                                    <p>Bridal, film, editorial</p>
                                </div>
                            </div>
                        </div>

                        <!-- Film Roles -->
                        <div class="role-category" data-domain="film">
                            <h3>Direction & Creative</h3>
                            <div class="role-grid">
                                <div class="role-card" data-role="director">
                                    <h4>Director</h4>
                                    <p>Film & creative direction</p>
                                </div>
                                <div class="role-card" data-role="storyboard-artist">
                                    <h4>Storyboard Artist</h4>
                                    <p>Visual script planning</p>
                                </div>
                            </div>

                            <h3>Acting & Performance</h3>
                            <div class="role-grid">
                                <div class="role-card" data-role="actor">
                                    <h4>Actor/Actress</h4>
                                    <p>Film, TV, theatre</p>
                                </div>
                                <div class="role-card" data-role="voice-actor">
                                    <h4>Voice Actor</h4>
                                    <p>Dubbing & narration</p>
                                </div>
                                <div class="role-card" data-role="dancer">
                                    <h4>Dancer/Choreographer</h4>
                                    <p>Performance & teaching</p>
                                </div>
                            </div>

                            <h3>Cinematography & Visual</h3>
                            <div class="role-grid">
                                <div class="role-card" data-role="dop">
                                    <h4>DOP/Camera Crew</h4>
                                    <p>Cinematography & lighting</p>
                                </div>
                                <div class="role-card" data-role="editor">
                                    <h4>Editor/VFX Artist</h4>
                                    <p>Post-production specialist</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selectedRole" name="role">
                </div>

                <!-- STEP 7: Role-Specific Questions (Dynamic) -->
                <div class="form-step" data-step="7">
                    <h2 id="roleSpecificTitle">Role-Specific Details</h2>
                    <p class="step-description">Tell us more about your specialization</p>

                    <!-- Fashion Designer Fields -->
                    <div class="role-specific-fields" data-role-specific="fashion-designer" style="display: none;">
                        <div class="form-group full-width">
                            <label>Fashion Designer Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Menswear"><span class="control-indicator"></span><span>Menswear</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Womenswear"><span class="control-indicator"></span><span>Womenswear</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Kidswear"><span class="control-indicator"></span><span>Kidswear</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Unisex"><span class="control-indicator"></span><span>Unisex</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreview"></div>
                        </div>
                    </div>
                    
                    <!-- Textile Designer Fields -->
                    <div class="role-specific-fields" data-role-specific="textile-designer" style="display: none;">
                        <div class="form-group full-width">
                            <label>Textile Designer Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Apparel Textiles"><span class="control-indicator"></span><span>Apparel Textiles</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Home Furnishing"><span class="control-indicator"></span><span>Home Furnishing</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Technical Textiles"><span class="control-indicator"></span><span>Technical Textiles</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Sustainable Materials"><span class="control-indicator"></span><span>Sustainable Materials</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewTextile"></div>
                        </div>
                    </div>
                    
                    <!-- Accessory Designer Fields -->
                    <div class="role-specific-fields" data-role-specific="accessory-designer" style="display: none;">
                        <div class="form-group full-width">
                            <label>Accessory Designer Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Jewelry"><span class="control-indicator"></span><span>Jewelry</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Bags & Handbags"><span class="control-indicator"></span><span>Bags & Handbags</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Shoes & Footwear"><span class="control-indicator"></span><span>Shoes & Footwear</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Watches & Accessories"><span class="control-indicator"></span><span>Watches & Accessories</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewAccessory"></div>
                        </div>
                    </div>
                    
                    <!-- Fashion Illustrator Fields -->
                    <div class="role-specific-fields" data-role-specific="fashion-illustrator" style="display: none;">
                        <div class="form-group full-width">
                            <label>Fashion Illustrator Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Fashion Sketches"><span class="control-indicator"></span><span>Fashion Sketches</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Technical Drawings"><span class="control-indicator"></span><span>Technical Drawings</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Digital Illustrations"><span class="control-indicator"></span><span>Digital Illustrations</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Concept Art"><span class="control-indicator"></span><span>Concept Art</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewIllustrator"></div>
                        </div>
                    </div>
                    
                    <!-- Fashion Model Fields -->
                    <div class="role-specific-fields" data-role-specific="fashion-model" style="display: none;">
                        <div class="form-group full-width">
                            <label>Fashion Model Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Runway"><span class="control-indicator"></span><span>Runway</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Editorial"><span class="control-indicator"></span><span>Editorial</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Commercial"><span class="control-indicator"></span><span>Commercial</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Fitness & Lifestyle"><span class="control-indicator"></span><span>Fitness & Lifestyle</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewModel"></div>
                        </div>
                    </div>
                    
                    <!-- Ramp Choreographer Fields -->
                    <div class="role-specific-fields" data-role-specific="ramp-choreographer" style="display: none;">
                        <div class="form-group full-width">
                            <label>Ramp Choreographer Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Fashion Shows"><span class="control-indicator"></span><span>Fashion Shows</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Music Videos"><span class="control-indicator"></span><span>Music Videos</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Commercial Events"><span class="control-indicator"></span><span>Commercial Events</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Wedding Choreography"><span class="control-indicator"></span><span>Wedding Choreography</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewChoreographer"></div>
                        </div>
                    </div>
                    
                    <!-- Fashion Stylist Fields -->
                    <div class="role-specific-fields" data-role-specific="fashion-stylist" style="display: none;">
                        <div class="form-group full-width">
                            <label>Fashion Stylist Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Editorial Styling"><span class="control-indicator"></span><span>Editorial Styling</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Personal Styling"><span class="control-indicator"></span><span>Personal Styling</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Celebrity Styling"><span class="control-indicator"></span><span>Celebrity Styling</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Commercial Styling"><span class="control-indicator"></span><span>Commercial Styling</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewStylist"></div>
                        </div>
                    </div>
                    
                    <!-- Makeup Artist Fields -->
                    <div class="role-specific-fields" data-role-specific="makeup-artist" style="display: none;">
                        <div class="form-group full-width">
                            <label>Makeup Artist Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Bridal Makeup"><span class="control-indicator"></span><span>Bridal Makeup</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Editorial Makeup"><span class="control-indicator"></span><span>Editorial Makeup</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Special Effects"><span class="control-indicator"></span><span>Special Effects</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Beauty & Cosmetics"><span class="control-indicator"></span><span>Beauty & Cosmetics</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewMakeup"></div>
                        </div>
                    </div>
                    
                    <!-- Director Fields -->
                    <div class="role-specific-fields" data-role-specific="director" style="display: none;">
                        <div class="form-group full-width">
                            <label>Director Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Feature Films"><span class="control-indicator"></span><span>Feature Films</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Short Films"><span class="control-indicator"></span><span>Short Films</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Documentaries"><span class="control-indicator"></span><span>Documentaries</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Commercials"><span class="control-indicator"></span><span>Commercials</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewDirector"></div>
                        </div>
                    </div>
                    
                    <!-- Storyboard Artist Fields -->
                    <div class="role-specific-fields" data-role-specific="storyboard-artist" style="display: none;">
                        <div class="form-group full-width">
                            <label>Storyboard Artist Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Film Storyboards"><span class="control-indicator"></span><span>Film Storyboards</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Animation Storyboards"><span class="control-indicator"></span><span>Animation Storyboards</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Advertising Boards"><span class="control-indicator"></span><span>Advertising Boards</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Game Design"><span class="control-indicator"></span><span>Game Design</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewStoryboard"></div>
                        </div>
                    </div>
                    
                    <!-- Actor/Actress Fields -->
                    <div class="role-specific-fields" data-role-specific="actor" style="display: none;">
                        <div class="form-group full-width">
                            <label>Actor/Actress Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Film Acting"><span class="control-indicator"></span><span>Film Acting</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Theater Acting"><span class="control-indicator"></span><span>Theater Acting</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="TV Series"><span class="control-indicator"></span><span>TV Series</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Voice Acting"><span class="control-indicator"></span><span>Voice Acting</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewActor"></div>
                        </div>
                    </div>
                    
                    <!-- Voice Actor Fields -->
                    <div class="role-specific-fields" data-role-specific="voice-actor" style="display: none;">
                        <div class="form-group full-width">
                            <label>Voice Actor Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Animation Voices"><span class="control-indicator"></span><span>Animation Voices</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Audiobook Narration"><span class="control-indicator"></span><span>Audiobook Narration</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Commercial Voiceovers"><span class="control-indicator"></span><span>Commercial Voiceovers</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Video Game Voices"><span class="control-indicator"></span><span>Video Game Voices</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewVoice"></div>
                        </div>
                    </div>
                    
                    <!-- Dancer/Choreographer Fields -->
                    <div class="role-specific-fields" data-role-specific="dancer" style="display: none;">
                        <div class="form-group full-width">
                            <label>Dancer/Choreographer Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Ballet"><span class="control-indicator"></span><span>Ballet</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Contemporary"><span class="control-indicator"></span><span>Contemporary</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Hip Hop"><span class="control-indicator"></span><span>Hip Hop</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Classical Dance"><span class="control-indicator"></span><span>Classical Dance</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewDancer"></div>
                        </div>
                    </div>
                    
                    <!-- DOP/Camera Crew Fields -->
                    <div class="role-specific-fields" data-role-specific="dop" style="display: none;">
                        <div class="form-group full-width">
                            <label>DOP/Camera Crew Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Cinematography"><span class="control-indicator"></span><span>Cinematography</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Drone Photography"><span class="control-indicator"></span><span>Drone Photography</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Studio Photography"><span class="control-indicator"></span><span>Studio Photography</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Event Coverage"><span class="control-indicator"></span><span>Event Coverage</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewDop"></div>
                        </div>
                    </div>
                    
                    <!-- Editor/VFX Artist Fields -->
                    <div class="role-specific-fields" data-role-specific="editor" style="display: none;">
                        <div class="form-group full-width">
                            <label>Editor/VFX Artist Categories </label>
                            <div class="checkbox-grid">
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Video Editing"><span class="control-indicator"></span><span>Video Editing</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Color Grading"><span class="control-indicator"></span><span>Color Grading</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Visual Effects"><span class="control-indicator"></span><span>Visual Effects</span></label>
                                <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Motion Graphics"><span class="control-indicator"></span><span>Motion Graphics</span></label>
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
                            <div class="file-upload-preview" id="portfolioPreviewEditor"></div>
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
                            </div>
                            <div class="form-group">
                                <label for="linkedin">Youtube</label>
                                <input type="url" id="linkedin" name="linkedin" placeholder="https://youtube.com/...">
                            </div>
                        </div>
                         <div class="form-row">
                            <div class="form-group full-width">
                                <label for="website">Personal Website</label>
                                <input type="url" id="website" name="website" placeholder="https://...">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navigation Buttons -->
                <div class="form-navigation">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Next Step</button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">Submit for Review</button>
                </div>
            </form>
        </div>
    </div>


<script>



</script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/global.js?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/talent--submission.js?ver=<?php echo HELLO_ELEMENTOR_CHILD_VERSION; ?>"></script>
<script>
// Test if global.js is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (typeof showNotification === 'function') {
        console.log('Global JS is loaded and working on talent submission page');
    } else {
        console.log('Global JS is not loaded on talent submission page');
    }
});
</script>
</body>
</html>
