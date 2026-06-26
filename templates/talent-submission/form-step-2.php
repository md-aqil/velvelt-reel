<?php
/**
 * Form Step 2: Experience Template Part
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

<!-- STEP 2: Experience -->
<div class="form-step" data-step="2">
    <h2>Experience</h2>
    <p class="step-description">Tell us about your professional background</p>

    <div class="form-group full-width">
        <label for="yearsActive">Years of Experience</label>
        <select id="yearsActive" name="yearsActive">
            <option value="">Select Experience</option>
            <option value="Less than 1 year" <?php selected(isset($values['yearsActive']) && $values['yearsActive'], 'Less than 1 year'); ?>>Less than 1 year</option>
            <option value="1-2 years" <?php selected(isset($values['yearsActive']) && $values['yearsActive'], '1-2 years'); ?>>1-2 years</option>
            <option value="3-5 years" <?php selected(isset($values['yearsActive']) && $values['yearsActive'], '3-5 years'); ?>>3-5 years</option>
            <option value="5-10 years" <?php selected(isset($values['yearsActive']) && $values['yearsActive'], '5-10 years'); ?>>5-10 years</option>
            <option value="10-15 years" <?php selected(isset($values['yearsActive']) && $values['yearsActive'], '10-15 years'); ?>>10-15 years</option>
            <option value="15+ years" <?php selected(isset($values['yearsActive']) && $values['yearsActive'], '15+ years'); ?>>15+ years</option>
        </select>
    </div>

    <div class="form-group full-width">
        <label>Current Affiliation</label>
        <div class="radio-cards">
            <label class="radio-card">
                <input type="radio" name="affiliation" value="Freelancer" <?php checked(isset($values['affiliation']) && $values['affiliation'], 'Freelancer'); ?>>
                <div class="card-content">
                    <span>Freelancer</span>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="affiliation" value="Agency" <?php checked(isset($values['affiliation']) && $values['affiliation'], 'Agency'); ?>>
                <div class="card-content">
                    <span>Agency</span>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="affiliation" value="Brand/Studio" <?php checked(isset($values['affiliation']) && $values['affiliation'], 'Brand/Studio'); ?>>
                <div class="card-content">
                    <span>Brand/Studio</span>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="affiliation" value="Self-Employed" <?php checked(isset($values['affiliation']) && $values['affiliation'], 'Self-Employed'); ?>>
                <div class="card-content">
                    <span>Self-Employed</span>
                </div>
            </label>
        </div>
    </div>

    <div class="form-group full-width">
        <label for="agencyBrandStudio">Agency/Brand/Studio Name</label>
        <input type="text" id="agencyBrandStudio" name="agencyBrandStudio"
            value="<?php echo isset($values['agencyBrandStudio']) ? esc_attr($values['agencyBrandStudio']) : ''; ?>"
            placeholder="Enter agency, brand, or studio name">
    </div>

    <div class="form-group full-width">
        <label for="agencyName">Talent's Agency Name</label>
        <input type="text" id="agencyName" name="agencyName"
            value="<?php echo isset($values['agencyName']) ? esc_attr($values['agencyName']) : ''; ?>"
            placeholder="Enter your agency name">
    </div>

    <div class="form-group full-width">
        <label for="education">Education / Training</label>
        <textarea id="education" name="education" rows="4" maxlength="500"
            placeholder="List institutes, mentors, certifications, or training programs"><?php echo isset($values['education']) ? esc_textarea($values['education']) : ''; ?></textarea>
        <div class="char-counter"><span id="educationCount"><?php echo isset($values['education']) ? strlen($values['education']) : 0; ?></span>/500</div>
    </div>
</div>
