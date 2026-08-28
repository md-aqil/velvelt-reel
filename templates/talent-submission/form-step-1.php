<?php
/**
 * Form Step 1: Profile Basics Template Part
 * 
 * @package HelloElementorChild
 */

$values = array();
if (isset($args['values']) && is_array($args['values'])) {
    $values = $args['values'];
} elseif (isset($template_data['values']) && is_array($template_data['values'])) {
    $values = $template_data['values'];
}
?>

<!-- STEP 1: Profile Basics -->
<div class="form-step active" data-step="1">
    <h2>Profile Basics</h2>
    <p class="step-description">Let's start with your essential information</p>

    <div class="profile-photo-section">
        <div class="photo-upload">
            <div class="photo-preview" id="photoPreview">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" style="display: none;">
            <button type="button" class="upload-btn" onclick="document.getElementById('profilePhoto').click()">Upload
                Photo</button>
            <p class="upload-hint">500x500px, Max 5MB</p>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group full-width">
            <label for="fullName">Full Name <span class="required">*</span></label>
            <input type="text" id="fullName" name="fullName"
                value="<?php echo isset($values['fullName']) ? esc_attr($values['fullName']) : ''; ?>"
                placeholder="Enter your full name" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="city">State <span class="required">*</span></label>
            <input type="text" id="city" name="city"
                value="<?php echo isset($values['city']) ? esc_attr($values['city']) : ''; ?>" placeholder="Maharashtra"
                required>
        </div>
        <div class="form-group">
            <label for="country">Country <span class="required">*</span></label>
            <input type="text" id="country" name="country"
                value="<?php echo isset($values['country']) ? esc_attr($values['country']) : ''; ?>"
                placeholder="Enter your country" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="email">Contact Email <span class="required">*</span></label>
            <input type="email" id="email" name="email"
                value="<?php echo isset($values['email']) ? esc_attr($values['email']) : ''; ?>"
                placeholder="your.email@example.com" required>
            <label class="checkbox-label" style="font-weight: normal; margin-top: 5px;">
                <input type="checkbox" name="hideEmail" value="1" <?php checked(isset($values['hideEmail']) && $values['hideEmail']); ?>>
                <span class="control-indicator"></span>
                <span>Hide my email from public view</span>
            </label>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number <span class="required">*</span></label>
            <div class="phone-input-group">
                <select id="countryCode" name="countryCode" class="country-code-select" required>
                    <option value="">Code</option>
                    <option value="+1" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+1'); ?>>+1 (US)</option>
                    <option value="+91" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+91'); ?>>+91 (IN)</option>
                    <option value="+44" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+44'); ?>>+44 (UK)</option>
                    <option value="+61" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+61'); ?>>+61 (AU)</option>
                    <option value="+86" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+86'); ?>>+86 (CN)</option>
                    <option value="+49" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+49'); ?>>+49 (DE)</option>
                    <option value="+33" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+33'); ?>>+33 (FR)</option>
                    <option value="+81" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+81'); ?>>+81 (JP)</option>
                    <option value="+82" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+82'); ?>>+82 (KR)</option>
                    <option value="+55" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+55'); ?>>+55 (BR)</option>
                    <option value="+7" <?php selected(isset($values['countryCode']) && $values['countryCode'], '+7'); ?>>+7 (RU)</option>
                </select>
                <input type="tel" id="phone" name="phone"
                    value="<?php echo isset($values['phone']) ? esc_attr($values['phone']) : ''; ?>"
                    placeholder="9876543210" maxlength="15" pattern="[0-9]{10,15}" inputmode="numeric" required>
            </div>
            <label class="checkbox-label" style="font-weight: normal; margin-top: 5px;">
                <input type="checkbox" name="hidePhone" value="1" <?php checked(isset($values['hidePhone']) && $values['hidePhone']); ?>>
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
                <option value="18-25" <?php selected(isset($values['ageGroup']) && $values['ageGroup'], '18-25'); ?>>
                    18-25</option>
                <option value="26-35" <?php selected(isset($values['ageGroup']) && $values['ageGroup'], '26-35'); ?>>
                    26-35</option>
                <option value="36-45" <?php selected(isset($values['ageGroup']) && $values['ageGroup'], '36-45'); ?>>
                    36-45</option>
                <option value="46-55" <?php selected(isset($values['ageGroup']) && $values['ageGroup'], '46-55'); ?>>
                    46-55</option>
                <option value="56+" <?php selected(isset($values['ageGroup']) && $values['ageGroup'], '56+'); ?>>56+
                </option>
            </select>
        </div>
        <div class="form-group">
            <label>Gender <span class="required">*</span></label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="gender" value="Male" <?php checked(isset($values['gender']) && $values['gender'], 'Male'); ?> required>
                    <span class="control-indicator"></span>
                    <span>Male</span>
                </label>
                <label class="radio-label">
                    <input type="radio" name="gender" value="Female" <?php checked(isset($values['gender']) && $values['gender'], 'Female'); ?>>
                    <span class="control-indicator"></span>
                    <span>Female</span>
                </label>
                <label class="radio-label">
                    <input type="radio" name="gender" value="Non-binary" <?php checked(isset($values['gender']) && $values['gender'], 'Non-binary'); ?>>
                    <span class="control-indicator"></span>
                    <span>Non-binary</span>
                </label>
            </div>
        </div>
    </div>

    <div class="form-group full-width">
        <label for="languages">Languages Known <span class="required">*</span></label>
        <input type="text" id="languages" name="languages"
            value="<?php echo isset($values['languages']) ? esc_attr($values['languages']) : ''; ?>"
            placeholder="e.g. English, Spanish, French" required />
        <small class="hint">Please separate multiple languages with commas</small>
    </div>

    <!-- SEO Hint Box -->
    <div class="seo-hint-box">
        <h4><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg> SEO Tips for Better Ranking</h4>
        <ul>
            <li><strong>Focus Keyword:</strong> Use "Your Name + Role" (e.g., "Gangadhar Azmeera Actor")</li>
            <li><strong>Bio Length:</strong> Write at least 600 words about your experience</li>
            <li><strong>Alt Text:</strong> Add descriptive alt text to your images</li>
            <li><strong>External Links:</strong> Add IMDb, portfolio, or press links</li>
        </ul>
        <small>These tips help your profile rank higher in search results.</small>
    </div>

    <!-- SEO Focus Keyword Field -->
    <div class="form-group full-width">
        <label for="focusKeyword">Focus Keyword (for SEO)</label>
        <input type="text" id="focusKeyword" name="focusKeyword"
            value="<?php echo isset($values['focusKeyword']) ? esc_attr($values['focusKeyword']) : ''; ?>"
            placeholder="e.g., Gangadhar Azmeera Actor">
        <small class="hint">This keyword helps your profile rank in search results. Use: Your Name + Your Role</small>
    </div>

    <!-- Bio/Biography Field -->
    <div class="form-group full-width">
        <label for="biography">Biography <span class="required">*</span></label>
        <textarea id="biography" name="biography" rows="6" maxlength="5000"
            placeholder="Tell us about your background, experience, achievements, and what makes you unique..." required><?php echo isset($values['biography']) ? esc_textarea($values['biography']) : ''; ?></textarea>
        <small class="hint"><strong>SEO Tip:</strong> Write at least 600 words. Include your focus keyword naturally in the first paragraph and throughout the text.</small>
    </div>
</div>