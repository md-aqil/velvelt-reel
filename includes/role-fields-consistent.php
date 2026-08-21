<?php
/**
 * Clean role-specific fields template
 * This replaces the fragmented approach with a single, consistent template
 */

function render_role_fields_consistent($domain, $role_key)
{
    // Include the roles config
    if (!function_exists('get_domain_role')) {
        include_once get_stylesheet_directory() . '/roles-config.php';
    }

    $role_data = get_domain_role($domain, $role_key);

    if (!$role_data) {
        return;
    }

    $role_name = esc_html($role_data['name']);
    ?>
    <div class="role-specific-fields" data-role-specific="<?php echo esc_attr($role_key); ?>" hidden>
        <div class="form-group full-width">
            <label><?php echo $role_name; ?> Categories</label>
            <div class="radio-grid">
                <?php
                if (isset($role_data['categories']) && is_array($role_data['categories'])):
                    foreach ($role_data['categories'] as $category_key => $category_value):
                        ?>
                        <label class="radio-label">
                            <input type="radio" name="designCategories" value="<?php echo esc_attr($category_key); ?>">
                            <span class="control-indicator"></span>
                            <span><?php echo esc_html($category_value); ?></span>
                        </label>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render all role fields in a clean, consistent manner
 */
function render_all_role_fields_clean()
{
    $domains = get_domains();

    if (!$domains) {
        return;
    }

    foreach ($domains as $domain_key => $domain_data) {
        if (!isset($domain_data['roles']) || !is_array($domain_data['roles'])) {
            continue;
        }

        foreach ($domain_data['roles'] as $role_key => $role_data) {
            // First, try to use a specific template file if it exists
            $specific_template = get_stylesheet_directory() . "/templates/roles/{$domain_key}/{$role_key}-fields.php";

            if (file_exists($specific_template)) {
                // For the static files like singer-fields.php and music-director-fields.php
                if ($role_key === 'singer' || $role_key === 'music-director') {
                    include $specific_template;
                } else {
                    render_role_fields_consistent($domain_key, $role_key);
                }
            } else {
                // Use the generic template for all other roles
                render_role_fields_consistent($domain_key, $role_key);
            }
        }
    }
}
?>