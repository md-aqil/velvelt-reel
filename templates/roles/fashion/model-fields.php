<?php
/**
 * Fashion Model Role Specific Fields
 */
?>
<div class="role-specific-fields" data-role-specific="fashion-model" style="display: none;">
    <div class="form-group full-width">
        <label>Fashion Model Categories </label>
        <div class="checkbox-grid checkbox-grid-inline" style="display: flex !important; flex-wrap: wrap; gap: 8px;">
            <label class="checkbox-label" style="padding: 6px 10px; font-size: 12px;"><input type="checkbox" name="designCategories[]" value="Runway"><span class="control-indicator"></span><span>Runway</span></label>
            <label class="checkbox-label" style="padding: 6px 10px; font-size: 12px;"><input type="checkbox" name="designCategories[]" value="Editorial"><span class="control-indicator"></span><span>Editorial</span></label>
            <label class="checkbox-label" style="padding: 6px 10px; font-size: 12px;"><input type="checkbox" name="designCategories[]" value="Commercial"><span class="control-indicator"></span><span>Commercial</span></label>
            <label class="checkbox-label" style="padding: 6px 10px; font-size: 12px;"><input type="checkbox" name="designCategories[]" value="Fitness & Lifestyle"><span class="control-indicator"></span><span>Fitness & Lifestyle</span></label>
        </div>
    </div>
    <div class="form-group full-width">
        <label>Portfolio Gallery </label>
        <div class="file-upload-area">
            <input type="file" id="portfolio-model" name="portfolio-model[]" accept="image/*" multiple>
            <div class="file-upload-instructions">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <p><strong>Drag & drop files here</strong> or click to browse</p>
                <span class="file-upload-hint">Upload up to 10 images (JPG, PNG, GIF). Max 5MB each.</span>
            </div>
        </div>
        <div class="file-upload-preview" id="portfolioPreviewFashionModel"></div>
    </div>
</div>