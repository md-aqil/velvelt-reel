<?php
/**
 * Admin meta boxes for talent posts
 *
 * @package HelloElementorChild
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adds a meta box to the "Talent" post type editor.
 */
function talent_add_meta_boxes() {
    add_meta_box(
        'talent_details_meta_box',          // ID
        'Talent Details',                   // Title
        'talent_details_meta_box_callback', // Callback function
        'talent',                           // Post type
        'normal',                           // Context
        'high'                              // Priority
    );
}
add_action('add_meta_boxes', 'talent_add_meta_boxes');

/**
 * Callback function to render the talent details meta box content.
 *
 * @param WP_Post $post The post object.
 */
function talent_details_meta_box_callback($post) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field('talent_save_meta_box_data', 'talent_meta_box_nonce');

    // --- Define all fields ---
    $fields = [
        'Profile Basics' => [
            '_talent_email' => 'Contact Email',
            '_talent_phone' => 'Phone Number',
            '_talent_state' => 'State',
            '_talent_country' => 'Country',
            '_talent_age_group' => 'Age Group',
            '_talent_gender' => 'Gender',
            '_talent_height' => 'Height',
            '_talent_height_unit' => 'Height Unit',
            '_talent_weight' => 'Weight',
            '_talent_weight_unit' => 'Weight Unit',
            '_talent_complexion' => 'Complexion',
            '_talent_bust_size' => 'Bust Size',
            '_talent_hair_color' => 'Hair Color',
            '_talent_dress_size' => 'Dress Size',
            '_talent_shirt_size' => 'Shirt Size',
            '_talent_measurements' => 'Measurements',
            '_talent_languages' => 'Languages Known',
        ],
        'Experience & Portfolio' => [
            '_talent_years_active' => 'Years Active',
            '_talent_affiliation' => 'Affiliation',
            '_talent_education' => 'Education / Training',
            '_talent_ied_projects' => 'Interested Projects',
            '_talent_notable_works' => 'Notable Works',
        ],
        'Availability & Preferences' => [
            '_talent_available_for' => 'Available For',
            '_talent_willing_to_travel' => 'Willing to Travel',
            '_talent_preferred_locations' => 'Preferred Locations',
            '_talent_brand_collabs' => 'Open to Brand Collaborations',
        ],
        'Domain & Role' => [
            '_talent_domain' => 'Domain',
            '_talent_role' => 'Role',
        ],
        'Social Links' => [
            '_talent_instagram' => 'Instagram',
            '_talent_youtube' => 'YouTube',
            '_talent_tiktok' => 'TikTok',
            '_talent_website' => 'Website',
        ],
        'Privacy Settings' => [
            '_talent_hide_email' => 'Hide Email',
            '_talent_hide_phone' => 'Hide Phone',
            '_talent_hide_instagram' => 'Hide Instagram',
            '_talent_hide_youtube' => 'Hide YouTube',
            '_talent_hide_tiktok' => 'Hide TikTok',
            '_talent_hide_website' => 'Hide Website',
        ],
        'Portfolio' => [
            '_talent_portfolio' => 'Portfolio Gallery',
        ],
    ];

    echo '<style>
        .talent-meta-box-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; color: #333; }
        .talent-meta-box-field { margin-bottom: 15px; background: #f9f9f9; padding: 10px; border-left: 3px solid #b2122d; }
        .talent-meta-box-field.full-width { grid-column: 1 / -1; }
        .talent-meta-box-field label { font-weight: bold; display: block; margin-bottom: 5px; }
        .talent-meta-box-field input[type="text"], 
        .talent-meta-box-field input[type="email"], 
        .talent-meta-box-field input[type="url"],
        .talent-meta-box-field textarea,
        .talent-meta-box-field select { 
            width: 100%; 
            padding: 8px; 
            background: #fff; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
        }
        .talent-meta-box-field textarea { 
            min-height: 80px; 
            resize: vertical; 
        }
        .talent-meta-box h3 { border-bottom: 2px solid #b2122d; padding-bottom: 10px; margin-top: 30px; margin-bottom: 20px; }
        .talent-meta-box h3:first-of-type { margin-top: 0; }
        .portfolio-image-preview { max-width: 150px; height: auto; margin: 5px; display: inline-block; }
        .portfolio-images-container { display: flex; flex-wrap: wrap; }
        .checkbox-field { display: flex; align-items: center; }
        .checkbox-field input[type="checkbox"] { width: auto; margin-right: 10px; }
    </style>';

    echo '<div class="talent-meta-box">';

    foreach ($fields as $section_title => $section_fields) {
        echo '<h3>' . esc_html($section_title) . '</h3>';
        echo '<div class="talent-meta-box-grid">';

        foreach ($section_fields as $meta_key => $label) {
            $value = get_post_meta($post->ID, $meta_key, true);
            $is_full_width = in_array($meta_key, ['_talent_education', '_talent_interested_projects', '_talent_notable_works', '_talent_portfolio']);

            echo '<div class="talent-meta-box-field ' . ($is_full_width ? 'full-width' : '') . '">';
            echo '<label for="' . esc_attr($meta_key) . '">' . esc_html($label) . ':</label>';

            if ($meta_key === '_talent_portfolio') {
                // Handle portfolio gallery using WordPress media library
                echo '<div class="value">';
                echo '<div class="portfolio-gallery-container">';

                $val_str = '';
                if (!empty($value)) {
                    if (is_array($value)) {
                        $val_str = implode(',', array_filter(array_map('intval', (array) $value)));
                    } elseif (is_string($value)) {
                        $val_str = $value;
                    } elseif (is_numeric($value)) {
                        $val_str = (string) $value;
                    }
                }

                // Hidden input to store attachment IDs
                echo '<input type="hidden" id="_talent_portfolio" name="_talent_portfolio" value="' . esc_attr($val_str) . '" />';

                echo '<style>
                    .admin-portfolio-grid { display: flex; flex-wrap: wrap; gap: 12px; margin: 12px 0; min-height: 50px; }
                    .admin-portfolio-item { position: relative; width: 120px; height: 120px; border-radius: 8px; overflow: hidden; background: #222; border: 1px solid #ccc; box-shadow: 0 2px 6px rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center; }
                    .admin-portfolio-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
                    .admin-portfolio-item .admin-media-badge { position: absolute; bottom: 4px; left: 4px; background: rgba(0,0,0,0.75); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; pointer-events: none; }
                    .admin-portfolio-item .admin-media-badge.video { background: #b2122d; }
                    .admin-portfolio-item .admin-remove-media { position: absolute; top: 4px; right: 4px; width: 24px; height: 24px; background: #dc3545; color: #fff; border: 1px solid #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; cursor: pointer; line-height: 1; transition: background 0.2s, transform 0.2s; }
                    .admin-portfolio-item .admin-remove-media:hover { background: #a71d2a; transform: scale(1.15); }
                    .admin-portfolio-controls { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
                    .admin-portfolio-empty-msg { color: #666; font-style: italic; padding: 10px 0; }
                </style>';

                // Gallery display area
                echo '<div id="portfolio-gallery-preview" class="admin-portfolio-grid">';
                
                $image_ids = [];
                if (!empty($value)) {
                    if (is_array($value)) {
                        $image_ids = is_array($value[0] ?? null) ? array_merge(...$value) : $value;
                    } elseif (is_string($value)) {
                        $image_ids = explode(',', $value);
                    } elseif (is_numeric($value)) {
                        $image_ids = [$value];
                    }
                }

                $image_ids = array_filter(array_map('intval', $image_ids));

                if (!empty($image_ids)) {
                    foreach ($image_ids as $image_id) {
                        if ($image_id > 0) {
                            $mime_type = get_post_mime_type($image_id) ?: '';
                            $is_video = $mime_type && strpos($mime_type, 'video/') === 0;
                            
                            if ($is_video) {
                                $v_thumb = get_post_meta($image_id, '_thumbnail_id', true);
                                $thumb_url = $v_thumb ? wp_get_attachment_image_url($v_thumb, 'medium') : '';
                                if (empty($thumb_url)) {
                                    $med = wp_get_attachment_image_url($image_id, 'medium');
                                    if ($med && !preg_match('/\.(mp4|webm|mov|m4v|ogv)$/i', $med)) {
                                        $thumb_url = $med;
                                    }
                                }
                                $video_src = wp_get_attachment_url($image_id);

                                echo '<div class="admin-portfolio-item" data-id="' . esc_attr($image_id) . '">';
                                if (!empty($thumb_url) && !preg_match('/\.(mp4|webm|mov|m4v|ogv)$/i', $thumb_url)) {
                                    echo '<img src="' . esc_url($thumb_url) . '" alt="Portfolio video">';
                                } elseif (!empty($video_src)) {
                                    echo '<video class="admin-video-preview" src="' . esc_url($video_src) . '#t=0.5" preload="auto" muted playsinline style="width:100%;height:100%;object-fit:cover;pointer-events:none;background:#111;"></video>';
                                } else {
                                    echo '<div style="color:#aaa;font-size:12px;text-align:center;padding:5px;">🎬 Video</div>';
                                }
                                echo '<span class="admin-media-badge video">VIDEO</span>';
                                echo '<button type="button" class="admin-remove-media" title="Remove media" data-id="' . esc_attr($image_id) . '">&times;</button>';
                                echo '</div>';
                            } else {
                                $img_url = wp_get_attachment_image_url($image_id, 'medium') ?: wp_get_attachment_url($image_id);
                                echo '<div class="admin-portfolio-item" data-id="' . esc_attr($image_id) . '">';
                                if (!empty($img_url)) {
                                    echo '<img src="' . esc_url($img_url) . '" alt="Portfolio image">';
                                }
                                echo '<span class="admin-media-badge image">IMG</span>';
                                echo '<button type="button" class="admin-remove-media" title="Remove media" data-id="' . esc_attr($image_id) . '">&times;</button>';
                                echo '</div>';
                            }
                        }
                    }
                } else {
                    echo '<span class="admin-portfolio-empty-msg">No media files currently attached. Click below to add images or videos.</span>';
                }
                echo '</div>';

                // Add gallery management buttons
                echo '<div class="admin-portfolio-controls">';
                echo '<button type="button" id="add-portfolio-images" class="button button-primary">+ Add / Select Media</button>';
                echo '<button type="button" id="clear-portfolio-images" class="button button-secondary" ' . (empty($image_ids) ? 'style="display:none;"' : '') . '>Remove All</button>';
                echo '<span id="admin-portfolio-count" style="color:#555;font-size:12px;margin-left:8px;">' . count($image_ids) . ' item(s)</span>';
                echo '</div>';
                echo '<p class="description">Select or upload images & videos for the talent portfolio gallery. Click &times; on any item to remove it.</p>';

                echo '</div>';

                // Add JavaScript for WordPress media library integration
                ?>
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    var addButton = document.getElementById("add-portfolio-images");
                    var clearButton = document.getElementById("clear-portfolio-images");
                    var galleryInput = document.getElementById("_talent_portfolio");
                    var galleryPreview = document.getElementById("portfolio-gallery-preview");
                    var countSpan = document.getElementById("admin-portfolio-count");

                    function setupAdminVideoThumb(vid) {
                        if (!vid) return;
                        function seekVideo() {
                            try {
                                if (vid.currentTime < 0.1) {
                                    vid.currentTime = 0.5;
                                }
                            } catch (e) {}
                        }
                        vid.addEventListener("loadedmetadata", seekVideo);
                        vid.addEventListener("loadeddata", seekVideo);
                        if (vid.readyState >= 1) {
                            seekVideo();
                        }
                        vid.addEventListener("seeked", function() {
                            try {
                                if (vid.videoWidth > 0 && vid.videoHeight > 0) {
                                    var canvas = document.createElement("canvas");
                                    canvas.width = vid.videoWidth;
                                    canvas.height = vid.videoHeight;
                                    var ctx = canvas.getContext("2d");
                                    ctx.drawImage(vid, 0, 0, canvas.width, canvas.height);
                                    var dataUrl = canvas.toDataURL("image/jpeg", 0.85);
                                    if (dataUrl && dataUrl.length > 200) {
                                        var img = document.createElement("img");
                                        img.src = dataUrl;
                                        img.alt = "Video thumbnail";
                                        img.style.cssText = "width:100%;height:100%;object-fit:cover;display:block;";
                                        if (vid.parentNode) {
                                            vid.parentNode.insertBefore(img, vid);
                                            vid.remove();
                                        }
                                    }
                                }
                            } catch (e) {}
                        });
                    }

                    function updateState() {
                        var items = galleryPreview.querySelectorAll(".admin-portfolio-item");
                        var ids = [];
                        items.forEach(function(item) {
                            var id = item.getAttribute("data-id");
                            if (id) ids.push(parseInt(id));
                        });
                        galleryInput.value = ids.join(",");
                        if (countSpan) {
                            countSpan.textContent = ids.length + " item(s)";
                        }
                        if (clearButton) {
                            clearButton.style.display = ids.length > 0 ? "inline-block" : "none";
                        }
                        var emptyMsg = galleryPreview.querySelector(".admin-portfolio-empty-msg");
                        if (ids.length === 0) {
                            if (!emptyMsg) {
                                var span = document.createElement("span");
                                span.className = "admin-portfolio-empty-msg";
                                span.textContent = "No media files currently attached. Click below to add images or videos.";
                                galleryPreview.appendChild(span);
                            }
                        } else {
                            if (emptyMsg) {
                                emptyMsg.remove();
                            }
                        }
                    }

                    // Delegate remove button clicks
                    galleryPreview.addEventListener("click", function(e) {
                        if (e.target && (e.target.classList.contains("admin-remove-media") || e.target.closest(".admin-remove-media"))) {
                            var btn = e.target.classList.contains("admin-remove-media") ? e.target : e.target.closest(".admin-remove-media");
                            var item = btn.closest(".admin-portfolio-item");
                            if (item) {
                                item.remove();
                                updateState();
                            }
                        }
                    });

                    if (clearButton) {
                        clearButton.addEventListener("click", function() {
                            if (confirm("Are you sure you want to remove all portfolio media items?")) {
                                galleryPreview.innerHTML = "";
                                updateState();
                            }
                        });
                    }

                    if (addButton) {
                        addButton.addEventListener("click", function() {
                            var frame = wp.media({
                                title: "Select Portfolio Images & Videos",
                                multiple: true,
                                library: { type: ["image", "video"] },
                                button: { text: "Add Selected to Portfolio" }
                            });

                            frame.on("select", function() {
                                var attachments = frame.state().get("selection").toJSON();
                                var emptyMsg = galleryPreview.querySelector(".admin-portfolio-empty-msg");
                                if (emptyMsg) emptyMsg.remove();

                                attachments.forEach(function(attachment) {
                                    var existing = galleryPreview.querySelector('.admin-portfolio-item[data-id="' + attachment.id + '"]');
                                    if (!existing) {
                                        var isVideo = attachment.type === "video";
                                        var thumbUrl = "";
                                        if (attachment.sizes && attachment.sizes.thumbnail) {
                                            thumbUrl = attachment.sizes.thumbnail.url;
                                        } else if (attachment.sizes && attachment.sizes.medium) {
                                            thumbUrl = attachment.sizes.medium.url;
                                        } else if (attachment.icon) {
                                            thumbUrl = attachment.icon;
                                        }

                                        var itemDiv = document.createElement("div");
                                        itemDiv.className = "admin-portfolio-item";
                                        itemDiv.setAttribute("data-id", attachment.id);

                                        if (isVideo) {
                                            if (thumbUrl && !thumbUrl.match(/\.(mp4|webm|mov|m4v|ogv)$/i)) {
                                                var img = document.createElement("img");
                                                img.src = thumbUrl;
                                                img.alt = "Portfolio video";
                                                itemDiv.appendChild(img);
                                            } else if (attachment.url) {
                                                var vid = document.createElement("video");
                                                vid.className = "admin-video-preview";
                                                vid.src = attachment.url + "#t=0.5";
                                                vid.preload = "auto";
                                                vid.muted = true;
                                                vid.playsInline = true;
                                                vid.style.cssText = "width:100%;height:100%;object-fit:cover;pointer-events:none;background:#111;";
                                                itemDiv.appendChild(vid);
                                                setupAdminVideoThumb(vid);
                                            } else {
                                                var iconDiv = document.createElement("div");
                                                iconDiv.style.cssText = "color:#aaa;font-size:12px;text-align:center;padding:5px;";
                                                iconDiv.textContent = "🎬 Video";
                                                itemDiv.appendChild(iconDiv);
                                            }
                                        } else {
                                            var img = document.createElement("img");
                                            img.src = thumbUrl || attachment.url;
                                            img.alt = "Portfolio image";
                                            itemDiv.appendChild(img);
                                        }

                                        var badge = document.createElement("span");
                                        badge.className = "admin-media-badge " + (isVideo ? "video" : "image");
                                        badge.textContent = isVideo ? "VIDEO" : "IMG";
                                        itemDiv.appendChild(badge);

                                        var removeBtn = document.createElement("button");
                                        removeBtn.type = "button";
                                        removeBtn.className = "admin-remove-media";
                                        removeBtn.title = "Remove media";
                                        removeBtn.innerHTML = "&times;";
                                        removeBtn.setAttribute("data-id", attachment.id);
                                        itemDiv.appendChild(removeBtn);

                                        galleryPreview.appendChild(itemDiv);
                                    }
                                });

                                updateState();
                            });

                            frame.open();
                        });
                    }

                    // Process existing video elements on page load
                    galleryPreview.querySelectorAll("video").forEach(function(v) {
                        setupAdminVideoThumb(v);
                    });
                });
                </script>
                <?php
                echo '</div>';
            } elseif ($meta_key === '_talent_notable_works') {
                // Handle notable works with editable fields
                echo '<div class="value">';
                echo '<div id="notable-works-container">';

                if (!empty($value) && is_array($value)) {
                    foreach ($value as $index => $work) {
                        echo '<div class="notable-work-item">';
                        echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; padding: 10px; background: #f0f0f0; border-radius: 5px;">';
                        echo '<div><label>Title:</label><input type="text" name="_talent_notable_works_titles[]" value="' . esc_attr($work['title'] ?? '') . '" style="width: 100%;" /></div>';
                        echo '<div><label>Role:</label><input type="text" name="_talent_notable_works_roles[]" value="' . esc_attr($work['role'] ?? '') . '" style="width: 100%;" /></div>';
                        echo '<div><label>Start Date:</label><input type="month" name="_talent_notable_works_start_dates[]" value="' . esc_attr($work['startDate'] ?? '') . '" style="width: 100%;" /></div>';
                        echo '<div><label>End Date:</label><input type="month" name="_talent_notable_works_end_dates[]" value="' . esc_attr($work['endDate'] ?? '') . '" style="width: 100%;" /></div>';
                        echo '<div><label>Present:</label><input type="checkbox" name="_talent_notable_works_present[]" ' . ((isset($work['present']) && $work['present'] === 'on') ? 'checked' : '') . ' value="on" /></div>';
                        echo '<div style="grid-column: 1 / -1;"><label>Description:</label><textarea name="_talent_notable_works_descriptions[]" style="width: 100%;">' . esc_textarea($work['description'] ?? '') . '</textarea></div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    // Add empty fields for new entry
                    echo '<div class="notable-work-item">';
                    echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; padding: 10px; background: #f0f0f0; border-radius: 5px;">';
                    echo '<div><label>Title:</label><input type="text" name="_talent_notable_works_titles[]" value="" style="width: 100%;" /></div>';
                    echo '<div><label>Role:</label><input type="text" name="_talent_notable_works_roles[]" value="" style="width: 100%;" /></div>';
                    echo '<div><label>Start Date:</label><input type="month" name="_talent_notable_works_start_dates[]" value="" style="width: 100%;" /></div>';
                    echo '<div><label>End Date:</label><input type="month" name="_talent_notable_works_end_dates[]" value="" style="width: 100%;" /></div>';
                    echo '<div><label>Present:</label><input type="checkbox" name="_talent_notable_works_present[]" value="on" /></div>';
                    echo '<div style="grid-column: 1 / -1;"><label>Description:</label><textarea name="_talent_notable_works_descriptions[]" style="width: 100%;"></textarea></div>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
                echo '<button type="button" id="add-notable-work" style="margin-top: 10px; padding: 5px 10px;">Add Another Work</button>';
                echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    var addButton = document.getElementById("add-notable-work");
                    if (addButton) {
                        addButton.addEventListener("click", function() {
                            var container = document.getElementById("notable-works-container");
                            var newItem = document.createElement("div");
                            newItem.className = "notable-work-item";
                            newItem.innerHTML = \'<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px; padding: 10px; background: #f0f0f0; border-radius: 5px;"><div><label>Title:</label><input type="text" name="_talent_notable_works_titles[]" value="" style="width: 100%;" /></div><div><label>Role:</label><input type="text" name="_talent_notable_works_roles[]" value="" style="width: 100%;" /></div><div><label>Start Date:</label><input type="month" name="_talent_notable_works_start_dates[]" value="" style="width: 100%;" /></div><div><label>End Date:</label><input type="month" name="_talent_notable_works_end_dates[]" value="" style="width: 100%;" /></div><div><label>Present:</label><input type="checkbox" name="_talent_notable_works_present[]" value="on" /></div><div style="grid-column: 1 / -1;"><label>Description:</label><textarea name="_talent_notable_works_descriptions[]" style="width: 100%;"></textarea></div></div>\';
                            container.appendChild(newItem);
                        });
                    }
                });
                </script>';
                echo '</div>';
            } elseif ($meta_key === '_talent_languages' || $meta_key === '_talent_available_for') {
                // Handle array fields as comma-separated inputs
                $value_str = is_array($value) ? implode(', ', $value) : $value;
                echo '<input type="text" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value_str) . '" />';
                echo '<p class="description">Enter values separated by commas</p>';
            } elseif (strpos($meta_key, '_talent_hide_') === 0) {
                // Handle privacy settings - show as checkboxes
                echo '<div class="checkbox-field">';
                echo '<input type="checkbox" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="1" ' . checked($value, '1', false) . ' />';
                echo '<label for="' . esc_attr($meta_key) . '">Hide this information from public view</label>';
                echo '</div>';
            } elseif ($meta_key === '_talent_willing_to_travel' || $meta_key === '_talent_brand_collabs') {
                // Handle yes/no fields as checkboxes
                echo '<div class="checkbox-field">';
                echo '<input type="checkbox" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="on" ' . checked($value, 'on', false) . ' />';
                echo '<label for="' . esc_attr($meta_key) . '">Yes</label>';
                echo '</div>';
            } elseif ($meta_key === '_talent_education' || $meta_key === '_talent_interested_projects') {
                // Handle textareas for longer text fields
                echo '<textarea id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '">' . esc_textarea($value) . '</textarea>';
            } elseif ($meta_key === '_talent_email') {
                // Handle email fields
                echo '<input type="email" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value) . '" />';
            } elseif ($meta_key === '_talent_website' || strpos($meta_key, '_talent_') === 0 && strpos($meta_key, '_link') !== false) {
                // Handle URL fields
                echo '<input type="url" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value) . '" />';
            } else {
                // Handle simple text values
                echo '<input type="text" id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '" value="' . esc_attr($value) . '" />';
            }
            echo '</div>';
        }
        echo '</div>'; // end .talent-meta-box-grid
    }
    echo '</div>'; // end .talent-meta-box
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function talent_save_meta_box_data($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['talent_meta_box_nonce'])) {
        return;
    }
    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['talent_meta_box_nonce'], 'talent_save_meta_box_data')) {
        return;
    }
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'talent' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Save portfolio gallery images
    if (isset($_POST['_talent_portfolio'])) {
        $attachment_ids = sanitize_text_field($_POST['_talent_portfolio']);
        if (!empty($attachment_ids)) {
            $attachment_ids = explode(',', $attachment_ids);
            $attachment_ids = array_map('intval', $attachment_ids);
        } else {
            $attachment_ids = [];
        }
        update_post_meta($post_id, '_talent_portfolio', $attachment_ids);
    }

    // Save other fields
    $meta_fields = [
        '_talent_email' => 'Contact Email',
        '_talent_phone' => 'Phone Number',
        '_talent_state' => 'State',
        '_talent_country' => 'Country',
        '_talent_age_group' => 'Age Group',
        '_talent_gender' => 'Gender',
        '_talent_height' => 'Height',
        '_talent_height_unit' => 'Height Unit',
        '_talent_weight' => 'Weight',
        '_talent_weight_unit' => 'Weight Unit',
        '_talent_complexion' => 'Complexion',
        '_talent_bust_size' => 'Bust Size',
        '_talent_hair_color' => 'Hair Color',
        '_talent_dress_size' => 'Dress Size',
        '_talent_shirt_size' => 'Shirt Size',
        '_talent_measurements' => 'Measurements',
        '_talent_years_active' => 'Years Active',
        '_talent_affiliation' => 'Affiliation',
        '_talent_education' => 'Education / Training',
        '_talent_interested_projects' => 'Interested Projects',
        '_talent_available_for' => 'Available For',
        '_talent_willing_to_travel' => 'Willing to Travel',
        '_talent_preferred_locations' => 'Preferred Locations',
        '_talent_brand_collabs' => 'Open to Brand Collaborations',
        '_talent_domain' => 'Domain',
        '_talent_role' => 'Role',
        '_talent_instagram' => 'Instagram',
        '_talent_youtube' => 'YouTube',
        '_talent_tiktok' => 'TikTok',
        '_talent_website' => 'Website',
        '_talent_hide_email' => 'Hide Email',
        '_talent_hide_phone' => 'Hide Phone',
        '_talent_hide_instagram' => 'Hide Instagram',
        '_talent_hide_youtube' => 'Hide YouTube',
        '_talent_hide_tiktok' => 'Hide TikTok',
        '_talent_hide_website' => 'Hide Website',
    ];

    // Process non-array fields
    foreach ($meta_fields as $meta_key => $label) {
        if (isset($_POST[$meta_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$meta_key]));
        } else {
            // For checkboxes, if not set, save as empty string
            if (strpos($meta_key, '_talent_hide_') === 0 || 
                $meta_key === '_talent_willing_to_travel' || 
                $meta_key === '_talent_brand_collabs') {
                update_post_meta($post_id, $meta_key, '');
            }
        }
    }

    // Handle array fields separately
    if (isset($_POST['_talent_languages'])) {
        if (is_array($_POST['_talent_languages'])) {
            update_post_meta($post_id, '_talent_languages', array_map('sanitize_text_field', $_POST['_talent_languages']));
        } else {
            // Handle comma-separated string
            $languages = sanitize_text_field($_POST['_talent_languages']);
            $languages_array = array_filter(array_map('trim', explode(',', $languages)));
            update_post_meta($post_id, '_talent_languages', $languages_array);
        }
    }
    
    if (isset($_POST['_talent_available_for'])) {
        if (is_array($_POST['_talent_available_for'])) {
            update_post_meta($post_id, '_talent_available_for', array_map('sanitize_text_field', $_POST['_talent_available_for']));
        } else {
            // Handle comma-separated string
            $available_for = sanitize_text_field($_POST['_talent_available_for']);
            $available_for_array = array_filter(array_map('trim', explode(',', $available_for)));
            update_post_meta($post_id, '_talent_available_for', $available_for_array);
        }
    }

    // Handle notable works
    if (isset($_POST['_talent_notable_works_titles']) && is_array($_POST['_talent_notable_works_titles'])) {
        $notable_works = [];
        $titles = $_POST['_talent_notable_works_titles'];
        $roles = $_POST['_talent_notable_works_roles'] ?? [];
        $start_dates = $_POST['_talent_notable_works_start_dates'] ?? [];
        $end_dates = $_POST['_talent_notable_works_end_dates'] ?? [];
        $present = $_POST['_talent_notable_works_present'] ?? [];
        $descriptions = $_POST['_talent_notable_works_descriptions'] ?? [];

        for ($i = 0; $i < count($titles); $i++) {
            // Skip empty titles
            if (empty($titles[$i])) {
                continue;
            }

            $is_present = isset($present[$i]) && $present[$i] === 'on';
            $notable_works[] = [
                'title' => sanitize_text_field($titles[$i]),
                'role' => sanitize_text_field($roles[$i] ?? ''),
                'startDate' => sanitize_text_field($start_dates[$i] ?? ''),
                'endDate' => $is_present ? '' : sanitize_text_field($end_dates[$i] ?? ''),
                'present' => $is_present ? 'on' : 'off',
                'description' => sanitize_textarea_field($descriptions[$i] ?? ''),
            ];
        }
        update_post_meta($post_id, '_talent_notable_works', $notable_works);
    }
}
add_action('save_post', 'talent_save_meta_box_data');