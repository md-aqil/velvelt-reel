<?php
/**
 * Edit Form Step 2: Experience
 * 
 * @package HelloElementorChild
 */

// Get controller
$controller = talent_edit_controller();
?>

<!-- STEP 2: Experience -->
<div class="form-step" data-step="2">
    <h2>Experience</h2>
    <p class="step-description">Tell us about your professional background</p>

    <div class="form-group full-width">
        <label for="yearsActive">How long have you been active in this field?</label>
        <select id="yearsActive" name="yearsActive">
            <option value="">Select Experience</option>
            <option value="Less than 1 year" <?php selected($controller->get('years_active'), 'Less than 1 year'); ?>>Less
                than 1 year</option>
            <option value="1-2 years" <?php selected($controller->get('years_active'), '1-2 years'); ?>>1-2 years</option>
            <option value="3-5 years" <?php selected($controller->get('years_active'), '3-5 years'); ?>>3-5 years</option>
            <option value="5-10 years" <?php selected($controller->get('years_active'), '5-10 years'); ?>>5-10 years
            </option>
            <option value="10-15 years" <?php selected($controller->get('years_active'), '10-15 years'); ?>>10-15 years
            </option>
            <option value="15+ years" <?php selected($controller->get('years_active'), '15+ years'); ?>>15+ years</option>
        </select>
    </div>

    <div class="form-group full-width">
        <label>Current Affiliation</label>
        <div class="radio-cards">
            <label class="radio-card">
                <input type="radio" name="affiliation" value="Freelancer" <?php checked($controller->get('affiliation'), 'Freelancer'); ?>>
                <div class="card-content">
                    <span>Freelancer</span>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="affiliation" value="Agency" <?php checked($controller->get('affiliation'), 'Agency'); ?>>
                <div class="card-content">
                    <span>Agency</span>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="affiliation" value="Brand/Studio" <?php checked($controller->get('affiliation'), 'Brand/Studio'); ?>>
                <div class="card-content">
                    <span>Brand/Studio</span>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="affiliation" value="Self-Employed" <?php checked($controller->get('affiliation'), 'Self-Employed'); ?>>
                <div class="card-content">
                    <span>Self-Employed</span>
                </div>
            </label>
        </div>
    </div>

    <div class="form-group full-width">
        <label for="agencyName">Talent's Agency Name</label>
        <input type="text" id="agencyName" name="agencyName" placeholder="Enter your agency name" 
            value="<?php echo esc_attr($controller->get('agency_name')); ?>">
    </div>

    <div class="form-group full-width">
        <label for="education">Education / Training</label>
        <textarea id="education" name="education" rows="4" maxlength="500"
            placeholder="List institutes, mentors, certifications, or training programs"><?php echo esc_textarea($controller->get('education')); ?></textarea>
        <div class="char-counter"><span id="educationCount"><?php echo strlen($controller->get('education')); ?></span>/500
        </div>
    </div>
</div>
