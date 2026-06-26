<?php
/**
 * Edit Form Step 1: Profile Basics
 * 
 * @package HelloElementorChild
 */

// Get controller
$controller = talent_edit_controller();
$state = $controller->get_state();
?>

<!-- STEP 1: Profile Basics -->
<div class="form-step active" data-step="1">
    <h2>Profile Basics</h2>
    <p class="step-description">Let's start with your essential information</p>

    <div class="profile-photo-section">
        <div class="photo-upload">
            <div class="photo-preview" id="photoPreview">
                <?php if ($controller->has_profile_photo()): ?>
                    <?php echo $controller->get_profile_photo(); ?>
                <?php else: ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                <?php endif; ?>
            </div>
            <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" style="display: none;">
            <button type="button" class="upload-btn" onclick="document.getElementById('profilePhoto').click()">Change
                Photo</button>
            <p class="upload-hint">500x500px, Max 5MB</p>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group full-width">
            <label for="fullName">Full Name <span class="required">*</span></label>
            <input type="text" id="fullName" name="fullName" placeholder="Enter your full name"
                value="<?php echo esc_attr($controller->get('full_name')); ?>" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="city">State <span class="required">*</span></label>
            <input type="text" id="city" name="city" placeholder="Maharashtra"
                value="<?php echo esc_attr($controller->get('state')); ?>" required>
        </div>
        <div class="form-group">
            <label for="country">Country <span class="required">*</span></label>
            <input type="text" id="country" name="country" placeholder="Enter your country"
                value="<?php echo esc_attr($controller->get('country')); ?>" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="email">Contact Email <span class="required">*</span></label>
            <input type="email" id="email" name="email" placeholder="your.email@example.com"
                value="<?php echo esc_attr($controller->get('email')); ?>" required>
            <label class="checkbox-label" style="font-weight: normal; margin-top: 5px;">
                <input type="checkbox" name="hideEmail" value="1" <?php checked($controller->get('hide_email'), '1'); ?>>
                <span class="control-indicator"></span>
                <span>Hide my email from public view</span>
            </label>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number <span class="required">*</span></label>
            <div class="phone-input-group">
                <select id="countryCode" name="countryCode" class="country-code-select" required>
                    <option value="">Code</option>
                    <option value="+1" <?php selected($controller->get('country_code'), '+1'); ?>>+1 (US)</option>
                    <option value="+91" <?php selected($controller->get('country_code'), '+91'); ?>>+91 (IN)</option>
                    <option value="+44" <?php selected($controller->get('country_code'), '+44'); ?>>+44 (UK)</option>
                    <option value="+61" <?php selected($controller->get('country_code'), '+61'); ?>>+61 (AU)</option>
                    <option value="+86" <?php selected($controller->get('country_code'), '+86'); ?>>+86 (CN)</option>
                    <option value="+49" <?php selected($controller->get('country_code'), '+49'); ?>>+49 (DE)</option>
                    <option value="+33" <?php selected($controller->get('country_code'), '+33'); ?>>+33 (FR)</option>
                    <option value="+81" <?php selected($controller->get('country_code'), '+81'); ?>>+81 (JP)</option>
                    <option value="+82" <?php selected($controller->get('country_code'), '+82'); ?>>+82 (KR)</option>
                    <option value="+55" <?php selected($controller->get('country_code'), '+55'); ?>>+55 (BR)</option>
                    <option value="+7" <?php selected($controller->get('country_code'), '+7'); ?>>+7 (RU)</option>
                </select>
                <input type="tel" id="phone" name="phone" placeholder="9876543210"
                    value="<?php echo esc_attr($controller->get('phone')); ?>" maxlength="15" pattern="[0-9]{10,15}"
                    inputmode="numeric" required>
            </div>
            <label class="checkbox-label" style="font-weight: normal; margin-top: 5px;">
                <input type="checkbox" name="hidePhone" value="1" <?php checked($controller->get('hide_phone'), '1'); ?>>
                <span class="control-indicator"></span>
                <span>Hide my phone number from public view</span>
            </label>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="ageGroup">Age Group <span class="required">*</span></label>
            <select id="ageGroup" name="ageGroup" required>
                <option value="">Select Age Group</option>
                <option value="18-25" <?php selected($controller->get('age_group'), '18-25'); ?>>18-25</option>
                <option value="26-35" <?php selected($controller->get('age_group'), '26-35'); ?>>26-35</option>
                <option value="36-45" <?php selected($controller->get('age_group'), '36-45'); ?>>36-45</option>
                <option value="46-55" <?php selected($controller->get('age_group'), '46-55'); ?>>46-55</option>
                <option value="56+" <?php selected($controller->get('age_group'), '56+'); ?>>56+</option>
            </select>
        </div>
        <div class="form-group">
            <label>Gender <span class="required">*</span></label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="gender" value="Male" <?php checked($controller->get('gender'), 'Male'); ?>
                        required><span class="control-indicator"></span>
                    <span>Male</span>
                </label>
                <label class="radio-label">
                    <input type="radio" name="gender" value="Female" <?php checked($controller->get('gender'), 'Female'); ?>><span class="control-indicator"></span>
                    <span>Female</span>
                </label>
                <label class="radio-label">
                    <input type="radio" name="gender" value="Non-binary" <?php checked($controller->get('gender'), 'Non-binary'); ?>><span class="control-indicator"></span>
                    <span>Non-binary</span>
                </label>
            </div>
        </div>
    </div>

    <div class="form-group full-width">
        <label for="languages">Languages Known <span class="required">*</span></label>
        <input type="text" id="languages" name="languages" placeholder="e.g. English, Spanish, French"
            value="<?php echo esc_attr($controller->get_languages_string()); ?>" required />
        <small class="hint">Please separate multiple languages with commas</small>
    </div>
</div>