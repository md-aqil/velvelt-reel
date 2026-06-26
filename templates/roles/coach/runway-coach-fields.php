<?php
/**
 * Runway Coach Role Fields Template
 */

// Get the role data from the config
$role_data = get_domain_role('coach', 'runway-coach');

if ($role_data):
    $role_key = 'runway-coach';
    $role_name = $role_data['name'];
    ?>
    <div class="role-specific-fields" data-role-specific="<?php echo esc_attr($role_key); ?>" style="display: none;">
        <div class="form-group full-width">
            <label><?php echo esc_html($role_name); ?> Categories</label>
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
        include get_stylesheet_directory() . '/components/portfolio-gallery.php';
        ?>
    </div>
<?php endif; ?>