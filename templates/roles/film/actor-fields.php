<?php
/**
 * Actor Role Fields Template
 */

// Get the role data from the config
$role_data = get_domain_role('film', 'actor');

if ($role_data):
    $role_key = 'actor';
    $role_name = htmlspecialchars($role_data['name'], ENT_QUOTES, 'UTF-8');
    ?>
    <div class="role-specific-fields" data-role-specific="<?php echo htmlspecialchars($role_key, ENT_QUOTES, 'UTF-8'); ?>" style="display: none;">
        <div class="form-group full-width">
            <label><?php echo $role_name; ?> Categories</label>
            <div class="checkbox-grid">
                <?php foreach ($role_data['categories'] as $category_key => $category_value): ?>
                <label class="checkbox-label">
                    <input type="checkbox" name="designCategories[]" value="<?php echo htmlspecialchars($category_key, ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="control-indicator"></span>
                    <span><?php echo htmlspecialchars($category_value, ENT_QUOTES, 'UTF-8'); ?></span>
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