<?php
/**
 * Reusable Portfolio Gallery Component with Video Links
 *
 * This component expects the following variables to be set in the including scope:
 * - $role_key: The role key/slug (e.g., 'fashion-designer')
 * - $role_name: The display name of the role (e.g., 'Fashion Designer')
 */

$is_shared_instance = !empty($render_portfolio_gallery_shared_instance);

if ($is_shared_instance) {
    $role_key = isset($portfolio_role_key) ? (string) $portfolio_role_key : '';
    $role_name = isset($portfolio_role_name) ? (string) $portfolio_role_name : 'Selected Role';
    $input_id = 'portfolio-shared';
    $input_name = $role_key !== '' ? 'portfolio-' . $role_key . '[]' : 'portfolio[]';
    $preview_id = 'portfolioPreviewShared';
} else {
    // Ensure required variables are set
    if (!isset($role_key) || !isset($role_name)) {
        // Try to get from globals as fallback
        global $role_key_component, $role_name_component;
        if (isset($role_key_component) && isset($role_name_component)) {
            $role_key = $role_key_component;
            $role_name = $role_name_component;
        } else {
            // If we still don't have the required variables, we can't render the component
            return;
        }
    }

    // Convert role key to camelCase for IDs (with proper capitalization)
    $role_parts = explode('-', $role_key);
    $role_id = '';
    foreach ($role_parts as $part) {
        $role_id .= ucfirst($part);
    }

    $input_id = 'portfolio-' . $role_key;
    $input_name = $input_id . '[]';
    $preview_id = 'portfolioPreview' . $role_id;
}
?>

<div class="form-group full-width">
    <label>Portfolio Gallery</label>
    <div class="file-upload-area">
        <!-- Accept both images and videos -->
        <input type="file" 
               id="<?php echo esc_attr($input_id); ?>" 
               name="<?php echo esc_attr($input_name); ?>" 
               accept="image/*,video/*" 
               multiple
               <?php echo $is_shared_instance && $role_key === '' ? 'disabled' : ''; ?>>
        <div class="file-upload-instructions">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
            <p><strong>Drag & drop files here</strong> or click to browse</p>
            <?php if ($is_shared_instance): ?>
            <span class="file-upload-hint" id="portfolioSharedRoleHint"><?php echo $role_key === '' ? 'Choose a role first to enable uploads.' : 'Uploads will be saved for your selected role.'; ?></span>
            <?php endif; ?>
            <div class="upload-features">
                <span class="feature-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    Images (JPG, PNG, GIF)
                </span>
                <span class="feature-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="23 7 16 12 23 17 23 7"></polygon>
                        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                    </svg>
                    Videos (MP4, WebM, MOV)
                </span>
            </div>
            <span class="file-upload-hint">Upload up to 10 files (mix of images and videos). Max 50MB each.</span>
            <span class="file-upload-hint">After upload, mark one image below as the portfolio cover.</span>
        </div>
    </div>
    <div class="file-upload-preview" id="<?php echo esc_attr($preview_id); ?>"></div>
</div>
