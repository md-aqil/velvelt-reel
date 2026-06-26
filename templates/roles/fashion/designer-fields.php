<?php
/**
 * Fashion Designer Role Specific Fields
 */
?>
<div class="role-specific-fields" data-role-specific="fashion-designer" style="display: none;">
    <div class="form-group full-width">
        <label>Fashion Designer Categories </label>
        <div class="checkbox-grid">
            <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Menswear"><span class="control-indicator"></span><span>Menswear</span></label>
            <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Womenswear"><span class="control-indicator"></span><span>Womenswear</span></label>
            <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Kidswear"><span class="control-indicator"></span><span>Kidswear</span></label>
            <label class="checkbox-label"><input type="checkbox" name="designCategories[]" value="Unisex"><span class="control-indicator"></span><span>Unisex</span></label>
        </div>
    </div>
    <div class="form-group full-width">
        <label>Portfolio Gallery </label>
        <div class="file-upload-area">
            <input type="file" id="portfolio" name="portfolio[]" accept="image/*" multiple>
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
        <div class="file-upload-preview" id="portfolioPreview"></div>
    </div>
</div>