<?php
/**
 * Template for role-specific fields
 * 
 * This template is used to generate the role-specific fields dynamically
 * based on the role configuration.
 */

// Include the portfolio gallery functions
include_once get_stylesheet_directory() . '/components/portfolio-gallery-functions.php';

function render_generic_role_fields($domain, $role, $role_data) {
    $role_key = $role;
    $role_name = $role_data['name'];
    
    ?>
    <div class="role-specific-fields" data-role-specific="<?php echo esc_attr($role_key); ?>" style="display: none;">
        <div class="form-group full-width">
            <label><?php echo esc_html($role_name); ?> Categories </label>
            <div class="checkbox-grid">
                <?php foreach ($role_data['categories'] as $category_key => $category_value): ?>
                <label class="checkbox-label">
                    <input type="checkbox" name="designCategories[]" value="<?php echo esc_attr($category_key); ?>">
                    <span class="control-indicator"></span>
                    <span><?php echo esc_html($category_value); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php
        // Include the reusable portfolio gallery component (now includes video links)
        include_portfolio_gallery_component($role_key, $role_name);
        ?>
    </div>
    <?php
}
?>