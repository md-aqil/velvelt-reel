document.addEventListener('DOMContentLoaded', function () {
    // Check if we're on the talent submission page by looking for the form
    const form = document.getElementById('velvetReelForm') || document.getElementById('velvetReelEditForm');
    if (!form) {
        // Not on talent submission or edit page, exit early
        return;
    }



    // --- FORM STATE & CONSTANTS ---
    let currentStep = 1;
    const totalSteps = document.querySelectorAll('.progress-sidebar .step').length;
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');

    const localStorageStepKey = 'talentForm_currentStep';
    const localStorageDataKey = 'talentForm_data';
    let isSubmittingForm = false;
    let autosaveTimer = null;
    let isRestoringFormState = false;
    const hasServerDraft = typeof talentData !== 'undefined'
        && talentData.draft
        && Object.keys(talentData.draft).length > 0;
    // --- THEME SWITCHER ---
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;

    const setTheme = (theme) => {
        body.dataset.theme = theme;
        localStorage.setItem('theme', theme);
    };

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const newTheme = body.dataset.theme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });
    }

    // Apply saved theme on page load, default to dark
    const savedTheme = localStorage.getItem('theme') || 'dark';
    setTheme(savedTheme);

    const setHiddenState = (element, shouldHide, activeClass = 'is-active') => {
        if (!element) {
            return;
        }

        element.hidden = !!shouldHide;
        if (activeClass) {
            element.classList.toggle(activeClass, !shouldHide);
        }
    };

    // --- NAVIGATION ---
    // Add a style to the head to make clickable steps have a pointer cursor
    const style = document.createElement('style');
    style.innerHTML = `
        .progress-sidebar .step {
            cursor: pointer;
        }
    `;
    document.head.appendChild(style);

    // Allow clicking on sidebar steps to navigate
    document.querySelectorAll('.progress-sidebar .step').forEach(stepEl => {
        stepEl.addEventListener('click', function () {
            const targetStep = parseInt(this.dataset.step, 10);

            // Navigate without auto-saving
            showStep(targetStep);
        });
    });

    const showStep = (stepNumber) => {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach((step) => {
            step.classList.remove('active');
            step.hidden = true;
        });

        // Show the correct step
        const activeStepEl = document.querySelector(`.form-step[data-step="${stepNumber}"]`);
        if (activeStepEl) {
            activeStepEl.classList.add('active');
            activeStepEl.hidden = false;
        }

        // Update sidebar
        document.querySelectorAll('.progress-sidebar .step').forEach((stepEl, index) => {
            stepEl.classList.remove('active', 'completed');
            if ((index + 1) < stepNumber) {
                stepEl.classList.add('completed');
            } else if ((index + 1) === stepNumber) {
                stepEl.classList.add('active');
            }
        });

        // Update navigation buttons
        prevBtn.style.display = (stepNumber === 1) ? 'none' : 'inline-block';
        nextBtn.style.display = (stepNumber === totalSteps) ? 'none' : 'inline-block';
        submitBtn.style.display = (stepNumber === totalSteps) ? 'inline-block' : 'none';

        currentStep = stepNumber;
        updateProgressBar();

        // Save the current step to localStorage
        localStorage.setItem(localStorageStepKey, stepNumber);

        if (stepNumber === 7) {
            toggleRoleSpecificFields(document.getElementById('selectedRole')?.value || '');
            // Re-render portfolio when returning to step 7 (Portfolio section)
            // This ensures existing portfolio images are properly displayed after navigating
            setTimeout(() => {
                const isEditPage = typeof talentData !== 'undefined' && Number(talentData.isEditPage) === 1;
                if (isEditPage && typeof loadExistingPortfolio === 'function') {
                    console.log('Navigating to step 7, loading existing portfolio...');
                    loadExistingPortfolio();
                }
            }, 100);
        } else {
            document.querySelectorAll('.role-specific-fields').forEach(field => {
                setHiddenState(field, true);
            });
        }
    };

    const updateProgressBar = () => {
        const progress = Math.min(((currentStep - 1) / (totalSteps - 1)) * 100, 100);
        document.querySelector('.progress-fill').style.width = `${progress}%`;
        document.querySelector('.progress-percentage').textContent = `${Math.round(progress)}% Completed`;
    };

    const validateStep = (stepNumber) => {
        const currentStepEl = document.querySelector(`.form-step[data-step="${stepNumber}"]`);
        if (!currentStepEl) return true;

        const requiredFields = currentStepEl.querySelectorAll('[required]');
        let isValid = true;

        if (stepNumber === 5 && !document.getElementById('selectedDomain')?.value) {
            alert('Please choose your domain.');
            return false;
        }

        if (stepNumber === 6 && !document.getElementById('selectedRole')?.value) {
            alert('Please choose your role.');
            return false;
        }

        for (const field of requiredFields) {
            field.classList.remove('error');
            let fieldValid = true;

            if (field.type === 'checkbox' || field.type === 'radio') {
                if (field.required) {
                    const name = field.name;
                    // Check if any radio/checkbox in the group is checked
                    if (!currentStepEl.querySelector(`[name="${name}"]:checked`)) {
                        fieldValid = false;
                    }
                }
            } else if (!field.value.trim()) {
                fieldValid = false;
            }

            if (!fieldValid) {
                isValid = false;
                if (field.type === 'checkbox' || field.type === 'radio') {
                    const group = field.closest('.form-group, .radio-cards, .checkbox-grid');
                    if (group) group.classList.add('error');
                } else {
                    field.classList.add('error');
                }
            }
        }

        if (!isValid) {
            alert('Please fill in all required fields.');
        }
        return isValid;
    };

    nextBtn.addEventListener('click', () => {
        if (validateStep(currentStep)) {
            // Navigate to next step without auto-saving
            if (currentStep < totalSteps) {
                if (currentStep === 5) { // After Domain selection
                    const selectedDomain = document.getElementById('selectedDomain').value;
                    filterRolesByDomain(selectedDomain);
                }
                if (currentStep === 6) { // After Role selection
                    const selectedRole = document.getElementById('selectedRole').value;
                    // Make sure role-specific fields are updated before moving to next step
                    if (selectedRole) {
                        toggleRoleSpecificFields(selectedRole);
                    }
                }
                // Show the next step after a brief delay to ensure role fields are updated
                setTimeout(() => {
                    showStep(currentStep + 1);
                }, 50);
            }
        }
    });

    // --- CLIENT SIDE IMAGE RESIZING ---
    const resizeImage = (file, maxWidth = 1920, maxHeight = 1920, quality = 0.85) => {
        return new Promise((resolve) => {
            if (!file.type.match(/image.*/) || file.type === 'image/gif' || file.type === 'image/svg+xml') {
                resolve(file); // Do not resize non-images, gifs, or svgs
                return;
            }

            const img = document.createElement('img');
            const reader = new FileReader();

            reader.onload = (e) => {
                img.src = e.target.result;
                img.onload = () => {
                    let width = img.width;
                    let height = img.height;

                    if (width <= maxWidth && height <= maxHeight) {
                        resolve(file); // No resize needed
                        return;
                    }

                    if (width > height) {
                        if (width > maxWidth) {
                            height = Math.round(height * maxWidth / width);
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width = Math.round(width * maxHeight / height);
                            height = maxHeight;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        if (blob) {
                            const newFile = new File([blob], file.name, {
                                type: blob.type || 'image/jpeg',
                                lastModified: Date.now()
                            });
                            resolve(newFile);
                        } else {
                            resolve(file); 
                        }
                    }, 'image/jpeg', quality);
                };
                img.onerror = () => resolve(file); // Failsafe
            };
            reader.onerror = () => resolve(file); // Failsafe
            reader.readAsDataURL(file);
        });
    };

    // --- SAVE & CONTINUE FUNCTIONALITY ---
    const buildDraftSaveRequestData = async (nonce) => {
        const formData = new FormData(form);
        const requestData = new FormData();
        const serializedFields = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value instanceof File) {
                if (value.name) {
                    try {
                        const resizedFile = await resizeImage(value);
                        requestData.append(key, resizedFile);
                    } catch (err) {
                        // Failsafe fallback to original if resize fails
                        requestData.append(key, value);
                    }
                }
                continue;
            }

            serializedFields.append(key, value);
        }

        requestData.append('action', 'save_talent_progress');
        requestData.append('nonce', nonce);
        requestData.append('current_step', currentStep);

        const postIdInput = form.querySelector('input[name="post_id"]');
        if (postIdInput && postIdInput.value) {
            requestData.append('post_id', postIdInput.value);
        }

        requestData.append('form_data', serializedFields.toString());

        return requestData;
    };

    const queuePortfolioAutosave = (delay = 900) => {
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(() => {
            if (isSubmittingForm || isRestoringFormState) {
                return;
            }

            saveStateToLocalStorage();

            if (typeof talentData !== 'undefined' && talentData.nonce && talentData.ajaxurl) {
                saveProgress({ silent: true });
            } else {
                initializeTalentDataAndSave(true);
            }
        }, delay);
    };

    const saveProgress = async (options = {}) => {
        const { silent = false } = options;
        console.log('saveProgress function called');

        // Check if talentData exists, if not, try to initialize it
        if (typeof talentData === 'undefined') {
            console.warn('talentData is not defined, attempting to initialize...');
            initializeTalentDataAndSave();
            return;
        }

        // Show saving popup early so long resizing operations don't freeze the UI without feedback
        if (!silent && typeof showSuccessPopup === 'function') {
            showSuccessPopup('Optimizing images and saving progress...', 'info');
        } else if (!silent) {
            console.log('showSuccessPopup function not available');
        }

        let requestData;
        try {
            requestData = await buildDraftSaveRequestData(talentData.nonce);
        } catch (err) {
            console.error("Error building request data:", err);
            if (!silent && typeof showSuccessPopup === 'function') {
                showSuccessPopup("Error compressing images. " + err.message, "error");
            }
            return;
        }

        console.log('Saving progress via multipart FormData');

        fetch(talentData.ajaxurl, {
            method: 'POST',
            body: requestData
        })
            .then(async response => {
                console.log('Response received:', response);
                if (!response.ok) {
                    let serverMessage = '';

                    try {
                        const rawResponse = await response.text();
                        if (rawResponse) {
                            try {
                                const parsedResponse = JSON.parse(rawResponse);
                                if (parsedResponse && typeof parsedResponse === 'object') {
                                    if (typeof parsedResponse.data === 'string') {
                                        serverMessage = parsedResponse.data;
                                    } else if (parsedResponse.data && typeof parsedResponse.data.message === 'string') {
                                        serverMessage = parsedResponse.data.message;
                                    }
                                }
                            } catch (parseError) {
                                serverMessage = rawResponse.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                            }
                        }
                    } catch (readError) {
                        console.warn('Unable to read error response body:', readError);
                    }

                    throw new Error(
                        serverMessage
                            ? `HTTP error! status: ${response.status} - ${serverMessage}`
                            : `HTTP error! status: ${response.status}`
                    );
                }
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                if (data.success && data.data.post_id) {
                    // Update post_id field if it's a new draft
                    let postIdInput = form.querySelector('input[name="post_id"]');
                    if (!postIdInput) {
                        postIdInput = document.createElement('input');
                        postIdInput.type = 'hidden';
                        postIdInput.name = 'post_id';
                        form.appendChild(postIdInput);
                    }
                    postIdInput.value = data.data.post_id;
                    console.log('Progress saved successfully, Post ID:', data.data.post_id);
                    saveStateToLocalStorage();

                    // Show success popup instead of notification
                    if (!silent && typeof showSuccessPopup === 'function') {
                        showSuccessPopup(buildPortfolioSaveMessage());
                    }
                } else {
                    const errorObj = data.data;
                    let errorMsg = (typeof errorObj === 'string') ? errorObj : (errorObj && errorObj.message ? errorObj.message : 'Error saving progress. Please try again.');
                    if (errorObj && Array.isArray(errorObj.errors)) {
                        errorMsg += '\\n' + errorObj.errors.join('\\n');
                    }
                    console.error('Error saving progress:', errorObj || 'Unknown error');
                    // Show error popup
                    if (!silent && typeof showSuccessPopup === 'function') {
                        showSuccessPopup(errorMsg, 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error saving progress:', error);
                // Show error popup
                if (!silent && typeof showSuccessPopup === 'function') {
                    showSuccessPopup(error.message || 'Error saving progress. Please try again.', 'error');
                }
            });
    };

    // Add event listener for the save progress button
    const saveProgressBtn = document.getElementById('saveProgressBtn');
    if (saveProgressBtn) {
        saveProgressBtn.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent any default form submission
            console.log('Save Progress button clicked');
            // Save to localStorage immediately for quick feedback
            saveStateToLocalStorage();
            // Then save to server if talentData is available
            if (typeof talentData !== 'undefined' && talentData.nonce && talentData.ajaxurl) {
                saveProgress();
            } else {
                console.warn('talentData is not available. Cannot save to server. Attempting to initialize talentData...');
                // Try to initialize talentData if it's not available
                initializeTalentDataAndSave();
            }
        });
    }

    // Function to initialize talentData and save if possible
    function initializeTalentDataAndSave(silent = false) {
        // Check if we're on the talent submission page
        const form = document.getElementById('velvetReelForm');
        if (!form) {
            console.error('Not on talent submission page');
            return;
        }

        // Check if talentData already exists (created in template)
        if (typeof talentData !== 'undefined' && talentData.ajaxurl && talentData.nonce) {
            console.log('Using existing talentData object');
            saveProgress({ silent });
            return;
        }

        // Try to get the AJAX URL and nonce from form hidden fields
        const nonceField = document.querySelector('[name="talent_submission_nonce"]') ||
            document.querySelector('[name="_wpnonce"]') ||
            document.querySelector('[name="nonce"]') ||
            document.querySelector('.nonce-field');

        const ajaxUrlField = document.getElementById('talent_ajax_url') ||
            document.querySelector('[name="talent_ajax_url"]');

        // Check if WordPress ajaxurl is available globally
        let ajaxUrl = typeof ajaxurl !== 'undefined' ? ajaxurl : null;
        let nonce = null;

        if (ajaxUrlField && ajaxUrlField.value) {
            ajaxUrl = ajaxUrlField.value;
        }

        if (nonceField && nonceField.value) {
            nonce = nonceField.value;
        } else {
            // Last resort: try to get nonce from the form's _wpnonce field if it exists
            const wpNonceField = document.querySelector('[name="_wpnonce"]');
            if (wpNonceField && wpNonceField.value) {
                nonce = wpNonceField.value;
            }
        }

        if (ajaxUrl && nonce) {
            // Create a temporary talentData object
            const tempTalentData = {
                ajaxurl: ajaxUrl,
                nonce: nonce,
                draft: null
            };

            // Use this temporary data to save progress
            console.log('Using temporary talentData to save progress');
            saveProgressWithTempData(tempTalentData, { silent });
        } else {
            console.error('Cannot initialize talentData. Missing AJAX URL or nonce.');
            console.log('ajaxUrl available:', typeof ajaxurl !== 'undefined' ? ajaxurl : 'not available');
            console.log('nonce field available:', !!nonceField);
            console.log('ajax url field available:', !!ajaxUrlField);
            // Show error popup
            if (typeof showSuccessPopup === 'function') {
                showSuccessPopup('Cannot save to server. Please refresh the page and try again.', 'error');
            }
        }
    }

    // Function to save progress with temporary talentData
    function saveProgressWithTempData(tempTalentData, options = {}) {
        const { silent = false } = options;
        console.log('saveProgressWithTempData function called');

        const requestData = buildDraftSaveRequestData(tempTalentData.nonce);
        console.log('Saving progress via multipart FormData');

        // Show saving popup
        if (!silent && typeof showSuccessPopup === 'function') {
            showSuccessPopup('Saving progress...', 'info');
        } else if (!silent) {
            console.log('showSuccessPopup function not available');
        }

        fetch(tempTalentData.ajaxurl, {
            method: 'POST',
            body: requestData
        })
            .then(async response => {
                console.log('Response received:', response);
                if (!response.ok) {
                    let serverMessage = '';

                    try {
                        const rawResponse = await response.text();
                        if (rawResponse) {
                            try {
                                const parsedResponse = JSON.parse(rawResponse);
                                if (parsedResponse && typeof parsedResponse === 'object') {
                                    if (typeof parsedResponse.data === 'string') {
                                        serverMessage = parsedResponse.data;
                                    } else if (parsedResponse.data && typeof parsedResponse.data.message === 'string') {
                                        serverMessage = parsedResponse.data.message;
                                    }
                                }
                            } catch (parseError) {
                                serverMessage = rawResponse.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                            }
                        }
                    } catch (readError) {
                        console.warn('Unable to read error response body:', readError);
                    }

                    throw new Error(
                        serverMessage
                            ? `HTTP error! status: ${response.status} - ${serverMessage}`
                            : `HTTP error! status: ${response.status}`
                    );
                }
                return response.json();
            })
            .then(data => {
                console.log('Server response:', data);
                if (data.success && data.data.post_id) {
                    // Update post_id field if it's a new draft
                    let postIdInput = form.querySelector('input[name="post_id"]');
                    if (!postIdInput) {
                        postIdInput = document.createElement('input');
                        postIdInput.type = 'hidden';
                        postIdInput.name = 'post_id';
                        form.appendChild(postIdInput);
                    }
                    postIdInput.value = data.data.post_id;
                    console.log('Progress saved successfully, Post ID:', data.data.post_id);
                    saveStateToLocalStorage();

                    // Show success popup instead of notification
                    if (!silent && typeof showSuccessPopup === 'function') {
                        showSuccessPopup(buildPortfolioSaveMessage());
                    }
                } else {
                    const errorObj = data.data;
                    let errorMsg = (typeof errorObj === 'string') ? errorObj : (errorObj && errorObj.message ? errorObj.message : 'Error saving progress. Please try again.');
                    if (errorObj && Array.isArray(errorObj.errors)) {
                        errorMsg += '\\n' + errorObj.errors.join('\\n');
                    }
                    console.error('Error saving progress:', errorObj || 'Unknown error');
                    // Show error popup
                    if (!silent && typeof showSuccessPopup === 'function') {
                        showSuccessPopup(errorMsg, 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error saving progress:', error);
                // Show error popup
                if (!silent && typeof showSuccessPopup === 'function') {
                    showSuccessPopup(error.message || 'Error saving progress. Please try again.', 'error');
                }
            });
    }



    prevBtn.addEventListener('click', () => {
        // Navigate to previous step without auto-saving
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    });

    // --- DYNAMIC FORM ELEMENTS ---

    // Profile Photo Preview
    document.getElementById('profilePhoto')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                // Display the selected image directly
                document.getElementById('photoPreview').innerHTML = `<img src="${event.target.result}" alt="Profile Preview" style="max-width: 100%; max-height: 100%;">`;
                
                // Also save to localStorage for persistence across refresh on submission page
                const isEditPage = typeof talentData !== 'undefined' && Number(talentData.isEditPage) === 1;
                if (!isEditPage) {
                    // On new submission, save the preview to localStorage
                    try {
                        const currentData = JSON.parse(localStorage.getItem(localStorageDataKey) || '{}');
                        currentData._profilePhotoPreview = event.target.result;
                        localStorage.setItem(localStorageDataKey, JSON.stringify(currentData));
                    } catch (err) {
                        console.error('Error saving profile photo preview to localStorage:', err);
                    }
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Phone Number Validation - Allow only digits
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Remove any non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');
            // Limit to max length (15 digits for international numbers)
            if (this.value.length > 15) {
                this.value = this.value.slice(0, 15);
            }
        });
        
        // Also validate on paste
        phoneInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digitsOnly = pastedText.replace(/[^0-9]/g, '').slice(0, 15);
            this.value = digitsOnly;
        });
    }

    // Character Counters
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const counterId = textarea.id + 'Count';
        const counter = document.getElementById(counterId);
        if (counter) {
            const updateCount = () => counter.textContent = textarea.value.length;
            textarea.addEventListener('input', updateCount);
            updateCount();
        }
    });

    // Conditional Fields (Travel)
    document.getElementById('willingToTravel')?.addEventListener('change', function () {
        const section = document.getElementById('preferredLocationsSection');
        if (section) {
            section.style.display = this.checked ? 'block' : 'none';
        }
    });

    // Conditional Fields (Physical Details for Models/Actors)
    const physicalDetailsSection = document.getElementById('physicalDetailsSection');
    const rolesNeedingPhysicalDetails = ['fashion-model', 'actor', 'dancer', 'ramp-choreographer', 'model-development-coach', 'runway-coach'];
    const roleDisplayNames = {
        'fashion-designer': 'Fashion Designer',
        'textile-designer': 'Textile Designer',
        'accessory-designer': 'Accessory Designer',
        'fashion-illustrator': 'Fashion Illustrator',
        'fashion-model': 'Fashion Model',
        'ramp-choreographer': 'Ramp Choreographer',
        'fashion-stylist': 'Fashion Stylist',
        'makeup-artist': 'Makeup Artist',
        'director': 'Director',
        'assistant-director': 'Assistant Director',
        'screenwriter': 'Screenwriter',
        'storyboard-artist': 'Storyboard Artist',
        'actor': 'Actor/Actress',
        'voice-actor': 'Voice Actor',
        'dancer': 'Dancer/Choreographer',
        'dop': 'DOP/Camera Crew',
        'editor': 'Editor/VFX Artist',
        'runway-coach': 'Runway Coach',
        'model-development-coach': 'Model Development Coach',
        'acting-coach': 'Acting Coach',
        'voice-diction-coach': 'Voice and Diction Coach',
        'singer': 'Singer',
        'music-director': 'Music Director'
    };

    const updatePhysicalDetailsVisibility = (role = document.getElementById('selectedRole')?.value || '') => {
        if (!physicalDetailsSection) {
            return;
        }

        const heading = document.getElementById('physicalDetailsHeading') || physicalDetailsSection.previousElementSibling;
        const isHeading = heading && heading.tagName === 'H3';
        const shouldShow = rolesNeedingPhysicalDetails.includes(role);

        setHiddenState(physicalDetailsSection, !shouldShow);
        if (isHeading) {
            setHiddenState(heading, !shouldShow);
        }
    };

    const updateSharedPortfolioBinding = (role = '', roleName = '') => {
        if (!sharedPortfolioInput) {
            return;
        }

        const normalizedRole = typeof role === 'string' ? role.trim() : '';
        sharedPortfolioInput.name = normalizedRole ? `portfolio-${normalizedRole}[]` : 'portfolio[]';
        sharedPortfolioInput.disabled = normalizedRole === '';
        sharedPortfolioInput.dataset.portfolioRole = normalizedRole;

        if (sharedPortfolioRoleHint) {
            sharedPortfolioRoleHint.textContent = normalizedRole
                ? `Uploads will be saved for your selected role${roleName ? `: ${roleName}` : ''}.`
                : 'Choose a role first to enable uploads.';
        }
    };

    const resetRoleSelection = () => {
        const selectedRoleInput = document.getElementById('selectedRole');
        if (selectedRoleInput) {
            selectedRoleInput.value = '';
        }

        document.querySelectorAll('.role-card').forEach((card) => {
            card.classList.remove('selected');
        });

        const titleEl = document.getElementById('roleSpecificTitle');
        if (titleEl) {
            titleEl.textContent = 'Role-Specific Details';
        }

        updateSharedPortfolioBinding('', '');
        toggleRoleSpecificFields('');
    };

    const roleBelongsToDomain = (role, domain) => {
        if (!role || !domain) {
            return false;
        }

        const roleCard = document.querySelector(`.role-card[data-role-specific="${role}"]`);
        if (!roleCard) {
            return false;
        }

        const category = roleCard.closest('.role-category');
        return !!category && category.dataset.domain === domain;
    };

    const filterRolesByDomain = (domain) => {
        document.querySelectorAll('.role-category').forEach(category => {
            const shouldShow = (domain === 'both' || category.dataset.domain === domain);
            setHiddenState(category, !shouldShow, '');
        });
    };

    const toggleRoleSpecificFields = (role) => {
        const allRoleFields = document.querySelectorAll('.role-specific-fields');
        allRoleFields.forEach((field) => {
            setHiddenState(field, true);
        });

        if (!role || typeof role !== 'string') {
            updatePhysicalDetailsVisibility('');
            return;
        }

        let roleField = document.querySelector(`.role-specific-fields[data-role-specific="${role}"]`);
        if (!roleField) {
            createDynamicRoleFields(role);
            roleField = document.querySelector(`.role-specific-fields[data-role-specific="${role}"]`);
        }

        if (roleField) {
            setHiddenState(roleField, false);
        }

        const titleEl = document.getElementById('roleSpecificTitle');
        if (titleEl) {
            const roleName = roleDisplayNames[role] || role.replace(/-/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
            titleEl.textContent = `${roleName} Details`;
        }

        updatePhysicalDetailsVisibility(role);
    };

    const setSelectedDomain = (domain) => {
        const selectedDomainInput = document.getElementById('selectedDomain');
        if (selectedDomainInput) {
            selectedDomainInput.value = domain || '';
        }

        document.querySelectorAll('.domain-card').forEach((card) => {
            card.classList.toggle('selected', card.dataset.domain === domain);
        });

        filterRolesByDomain(domain || '');

        const currentRole = document.getElementById('selectedRole')?.value || '';
        if (!roleBelongsToDomain(currentRole, domain || '')) {
            resetRoleSelection();
        }
    };

    const setSelectedRole = (role) => {
        const selectedRoleInput = document.getElementById('selectedRole');
        if (selectedRoleInput) {
            selectedRoleInput.value = role || '';
        }

        document.querySelectorAll('.role-card').forEach((card) => {
            card.classList.toggle('selected', card.dataset.roleSpecific === role);
        });

        const roleName = roleDisplayNames[role] || '';
        updateSharedPortfolioBinding(role || '', roleName);
        toggleRoleSpecificFields(role || '');
    };

    document.querySelectorAll('.domain-card').forEach((card) => {
        card.addEventListener('click', function () {
            setSelectedDomain(this.dataset.domain || '');
            saveStateToLocalStorage();
            queuePortfolioAutosave(1200);
        });
    });

    document.querySelectorAll('.role-card').forEach((card) => {
        card.addEventListener('click', function () {
            setSelectedRole(this.dataset.roleSpecific || '');
            saveStateToLocalStorage();
            queuePortfolioAutosave(1200);
        });
    });

    const syncSelectionStateFromInputs = () => {
        const selectedDomain = document.getElementById('selectedDomain')?.value || '';
        const selectedRole = document.getElementById('selectedRole')?.value || '';

        setSelectedDomain(selectedDomain);
        setSelectedRole(selectedRole);
    };

    window.toggleRoleSpecificFields = toggleRoleSpecificFields;
    window.setSelectedDomain = setSelectedDomain;
    window.setSelectedRole = setSelectedRole;

    // Fallback function to create dynamic role fields when template is missing
    const createDynamicRoleFields = (role) => {
        console.log('Creating dynamic fields for role:', role);

        const container = document.getElementById('roleFieldsContainer');
        if (!container) {
            console.error('Role fields container not found');
            return;
        }

        // Clear existing dynamic content
        const existingDynamic = container.querySelector('.dynamic-role-fields');
        if (existingDynamic) {
            existingDynamic.remove();
        }

        // Create new dynamic container
        const dynamicContainer = document.createElement('div');
        dynamicContainer.className = 'role-specific-fields dynamic-role-fields';
        dynamicContainer.dataset.roleSpecific = role;
        dynamicContainer.hidden = false;
        dynamicContainer.classList.add('is-active');

        // Add basic structure
        dynamicContainer.innerHTML = `
            <div class="form-group full-width">
                <div class="portfolio-gallery-placeholder">
                    <p>No extra role-specific fields are configured for ${role} yet.</p>
                    <p><em>You can still use the shared portfolio section below.</em></p>
                </div>
            </div>
        `;

        container.appendChild(dynamicContainer);
        console.log('Dynamic fields created for:', role);
    };

    // Notable Works Repeater
    const worksContainer = document.getElementById('notableWorksContainer');
    if (worksContainer) {
        const syncNotableWorkIndexes = () => {
            const workItems = worksContainer.querySelectorAll('.notable-work-item');

            workItems.forEach((workItem, index) => {
                const presentCheckbox = workItem.querySelector('.work-present-checkbox');
                const removeButton = workItem.querySelector('.remove-work-btn');

                if (presentCheckbox) {
                    presentCheckbox.value = String(index);
                }

                if (removeButton) {
                    removeButton.style.display = index === 0 && workItems.length === 1 ? 'none' : 'inline-block';
                }
            });
        };

        syncNotableWorkIndexes();

        document.getElementById('addWorkBtn')?.addEventListener('click', () => {
            if (worksContainer.children.length >= 10) {
                alert('You can add a maximum of 10 works.');
                return;
            }
            const firstWorkItem = worksContainer.querySelector('.notable-work-item');
            const newWorkItem = firstWorkItem.cloneNode(true);
            newWorkItem.querySelectorAll('input, textarea').forEach(input => input.value = '');
            newWorkItem.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
                if (cb.classList.contains('work-present-checkbox')) {
                    cb.value = '';
                }
            });
            const endDateInput = newWorkItem.querySelector('.work-end-date');
            if (endDateInput) {
                endDateInput.disabled = false;
            }
            worksContainer.appendChild(newWorkItem);
            syncNotableWorkIndexes();
        });

        worksContainer.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-work-btn')) {
                e.target.closest('.notable-work-item').remove();
                syncNotableWorkIndexes();
            }
            if (e.target.classList.contains('work-present-checkbox')) {
                const workItem = e.target.closest('.notable-work-item');
                const endDateInput = workItem.querySelector('.work-end-date');
                endDateInput.disabled = e.target.checked;
                if (e.target.checked) {
                    endDateInput.value = '';
                }
            }
        });

        worksContainer.syncNotableWorkIndexes = syncNotableWorkIndexes;
    }

    // Portfolio Preview for all roles
    const portfolioConfig = [
        { inputId: 'portfolio-shared', previewId: 'portfolioPreviewShared' }
    ];

    const portfolioCoverChoiceInput = form?.querySelector('input[name="portfolio_cover_choice"]');
    const sharedPortfolioInput = document.getElementById('portfolio-shared');
    const sharedPortfolioRoleHint = document.getElementById('portfolioSharedRoleHint');
    const portfolioPendingUploadsMetaKey = '__portfolioPendingUploads';
    const portfolioSelectedFiles = new Map();
    const portfolioValidationRules = {
        maxItems: 10,
        maxFileSize: 50 * 1024 * 1024,
        allowedMimeTypes: new Set([
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/heic',
            'image/heif',
            'video/mp4',
            'video/webm',
            'video/quicktime'
        ]),
        allowedExtensions: new Set(['jpg', 'jpeg', 'jpe', 'png', 'gif', 'webp', 'heic', 'heif', 'mp4', 'm4v', 'webm', 'mov'])
    };
    let restoredPortfolioPendingUploads = {};

    function escapePreviewText(text) {
        return String(text || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function getPortfolioFileExtension(filename) {
        const parts = String(filename || '').toLowerCase().split('.');
        return parts.length > 1 ? parts.pop() : '';
    }

    function isAllowedPortfolioFile(file) {
        if (!file) {
            return false;
        }

        if (portfolioValidationRules.allowedMimeTypes.has(file.type)) {
            return true;
        }

        return portfolioValidationRules.allowedExtensions.has(getPortfolioFileExtension(file.name));
    }

    function formatBytesToMb(bytes) {
        return `${(bytes / (1024 * 1024)).toFixed(0)}MB`;
    }

    function getPortfolioPreviewMetrics(previewContainer, excludedSource = null) {
        const items = Array.from(previewContainer.querySelectorAll('.file-preview-item'));
        let existingCount = 0;
        let newCount = 0;

        items.forEach((item) => {
            const itemType = item.dataset.portfolioItemType;
            if (itemType === 'existing') {
                existingCount += 1;
                return;
            }

            if (itemType === 'new') {
                if (excludedSource && item.dataset.portfolioSource === excludedSource) {
                    return;
                }
                newCount += 1;
            }
        });

        return {
            existingCount,
            newCount,
            total: existingCount + newCount
        };
    }

    function ensurePortfolioFeedbackElements(previewContainer) {
        const feedbackKey = previewContainer.id || 'portfolio-preview';
        const container = previewContainer.parentElement;
        if (!container) {
            return {};
        }

        let statusEl = container.querySelector(`.portfolio-feedback-status[data-feedback-for="${feedbackKey}"]`);
        if (!statusEl) {
            statusEl = document.createElement('div');
            statusEl.className = 'portfolio-feedback-status';
            statusEl.dataset.feedbackFor = feedbackKey;
            previewContainer.insertAdjacentElement('beforebegin', statusEl);
        }

        let errorEl = container.querySelector(`.portfolio-feedback-errors[data-feedback-for="${feedbackKey}"]`);
        if (!errorEl) {
            errorEl = document.createElement('ul');
            errorEl.className = 'portfolio-feedback-errors';
            errorEl.dataset.feedbackFor = feedbackKey;
            previewContainer.insertAdjacentElement('beforebegin', errorEl);
        }

        return { statusEl, errorEl };
    }

    function ensurePortfolioDraftNoticeElement(previewContainer) {
        const feedbackKey = previewContainer.id || 'portfolio-preview';
        const container = previewContainer.parentElement;
        if (!container) {
            return null;
        }

        let noticeEl = container.querySelector(`.portfolio-upload-persistence-notice[data-feedback-for="${feedbackKey}"]`);
        if (!noticeEl) {
            noticeEl = document.createElement('div');
            noticeEl.className = 'portfolio-upload-persistence-notice';
            noticeEl.dataset.feedbackFor = feedbackKey;
            previewContainer.insertAdjacentElement('beforebegin', noticeEl);
        }

        return noticeEl;
    }

    function updatePortfolioFeedback(previewContainer, errors = []) {
        const { statusEl, errorEl } = ensurePortfolioFeedbackElements(previewContainer);
        if (!statusEl || !errorEl) {
            return;
        }

        const metrics = getPortfolioPreviewMetrics(previewContainer);
        const remainingSlots = Math.max(0, portfolioValidationRules.maxItems - metrics.total);
        const itemLabel = metrics.total === 1 ? 'item' : 'items';

        statusEl.textContent = `${metrics.total}/${portfolioValidationRules.maxItems} portfolio ${itemLabel} selected. ${remainingSlots} slot${remainingSlots === 1 ? '' : 's'} remaining.`;
        statusEl.classList.toggle('is-full', remainingSlots === 0);

        errorEl.innerHTML = '';
        errorEl.style.display = errors.length ? 'block' : 'none';

        errors.forEach((message) => {
            const item = document.createElement('li');
            item.textContent = message;
            errorEl.appendChild(item);
        });
    }

    function buildPortfolioFileList(files) {
        if (typeof DataTransfer === 'undefined') {
            return null;
        }

        const dataTransfer = new DataTransfer();
        files.forEach((file) => dataTransfer.items.add(file));
        return dataTransfer.files;
    }

    function getPortfolioFileSignature(file) {
        if (!file) {
            return '';
        }

        return [
            file.name || '',
            file.size || 0,
            file.lastModified || 0,
            file.type || ''
        ].join('::');
    }

    function getSelectedUploadFileSignature(config, files) {
        if (!portfolioCoverChoiceInput) {
            return '';
        }

        const prefix = `upload:${config.inputId}:`;
        if (!portfolioCoverChoiceInput.value.startsWith(prefix)) {
            return '';
        }

        const selectedIndex = Number.parseInt(portfolioCoverChoiceInput.value.slice(prefix.length), 10);
        if (!Number.isInteger(selectedIndex) || !files[selectedIndex]) {
            return '';
        }

        return getPortfolioFileSignature(files[selectedIndex]);
    }

    function getTrackedPortfolioFiles(config, inputEl) {
        if (portfolioSelectedFiles.has(config.inputId)) {
            return [...portfolioSelectedFiles.get(config.inputId)];
        }

        return Array.from(inputEl?.files || []);
    }

    function setTrackedPortfolioFiles(config, inputEl, files) {
        const normalizedFiles = Array.isArray(files) ? files.filter(Boolean) : [];
        portfolioSelectedFiles.set(config.inputId, normalizedFiles);

        const nextFileList = buildPortfolioFileList(normalizedFiles);
        if (nextFileList) {
            inputEl.files = nextFileList;
        } else if (!normalizedFiles.length) {
            inputEl.value = '';
        }
    }

    function dedupePortfolioFiles(files) {
        const seenSignatures = new Set();
        const dedupedFiles = [];

        files.forEach((file) => {
            const signature = getPortfolioFileSignature(file);
            if (!signature || seenSignatures.has(signature)) {
                return;
            }

            seenSignatures.add(signature);
            dedupedFiles.push(file);
        });

        return dedupedFiles;
    }

    function getPendingPortfolioUploadsSnapshot() {
        const snapshot = {};

        portfolioConfig.forEach((config) => {
            const inputEl = document.getElementById(config.inputId);
            const files = Array.from(inputEl?.files || []);

            if (!files.length) {
                return;
            }

            snapshot[config.inputId] = {
                previewId: config.previewId,
                count: files.length,
                fileNames: files.map((file) => file.name).filter(Boolean)
            };
        });

        return snapshot;
    }

    function getPendingPortfolioUploadCount(snapshot = getPendingPortfolioUploadsSnapshot()) {
        return Object.values(snapshot).reduce((total, entry) => total + (entry?.count || 0), 0);
    }

    function buildPortfolioSaveMessage() {
        const pendingUploadCount = getPendingPortfolioUploadCount();
        if (!pendingUploadCount) {
            return 'Progress saved successfully!';
        }

        return `Progress saved. ${pendingUploadCount} new portfolio file${pendingUploadCount === 1 ? '' : 's'} will still need reattaching after refresh until final submit uploads them.`;
    }

    function syncPortfolioDraftNotices(restoredUploads = restoredPortfolioPendingUploads) {
        portfolioConfig.forEach((config) => {
            const previewContainer = document.getElementById(config.previewId);
            if (!previewContainer) {
                return;
            }

            const noticeEl = ensurePortfolioDraftNoticeElement(previewContainer);
            if (!noticeEl) {
                return;
            }

            const inputEl = document.getElementById(config.inputId);
            const currentFiles = Array.from(inputEl?.files || []);
            const restoredEntry = restoredUploads?.[config.inputId];
            let noticeMessage = '';

            if (currentFiles.length) {
                const fileLabel = currentFiles.length === 1 ? 'file is' : 'files are';
                noticeMessage = `${currentFiles.length} new portfolio ${fileLabel} selected in this browser session. "Save Progress" and browser refresh do not keep file uploads. Final submit/update them now or reattach them later.`;
                noticeEl.classList.remove('is-restored');
            } else if (restoredEntry && restoredEntry.count) {
                const fileNames = Array.isArray(restoredEntry.fileNames)
                    ? restoredEntry.fileNames.filter(Boolean).slice(0, 3)
                    : [];
                const extraCount = Math.max(0, restoredEntry.count - fileNames.length);
                const namesSuffix = fileNames.length
                    ? ` Previous selection: ${fileNames.join(', ')}${extraCount ? `, +${extraCount} more` : ''}.`
                    : '';
                noticeMessage = `Your previous session had ${restoredEntry.count} pending portfolio upload${restoredEntry.count === 1 ? '' : 's'}, but browsers cannot restore file inputs after refresh.${namesSuffix} Please reattach them before submitting.`;
                noticeEl.classList.add('is-restored');
            } else {
                noticeEl.classList.remove('is-restored');
            }

            noticeEl.textContent = noticeMessage;
            noticeEl.style.display = noticeMessage ? 'block' : 'none';
        });
    }

    function renderPortfolioSourceFiles(config, inputEl, previewContainer, files, options = {}) {
        const {
            errors = [],
            selectedUploadSignature = ''
        } = options;
        const isEditPage = typeof talentData !== 'undefined' && Number(talentData.isEditPage) === 1;

        if (isEditPage) {
            previewContainer
                .querySelectorAll(`.file-preview-item[data-portfolio-item-type="new"][data-portfolio-source="${config.inputId}"]`)
                .forEach((item) => item.remove());
        } else {
            previewContainer.innerHTML = '';
        }

        let mappedCoverChoice = '';

        files.forEach((file, fileIndex) => {
            const item = document.createElement('div');
            item.className = 'file-preview-item';
            item.dataset.portfolioItemType = 'new';
            item.dataset.portfolioSource = config.inputId;
            item.dataset.fileIndex = String(fileIndex);
            item.draggable = files.length > 1;

            const coverChoice = `upload:${config.inputId}:${fileIndex}`;
            const isSelected = selectedUploadSignature
                ? getPortfolioFileSignature(file) === selectedUploadSignature
                : (portfolioCoverChoiceInput && portfolioCoverChoiceInput.value === coverChoice);

            if (isSelected) {
                mappedCoverChoice = coverChoice;
            }

            const orderHint = files.length > 1
                ? '<span class="portfolio-order-hint">Drag to reorder</span>'
                : '';
            const actionControls = `
                <div class="portfolio-item-actions">
                    <button type="button" class="portfolio-action-btn danger" data-portfolio-action="remove-new" aria-label="Remove new portfolio item">&times;</button>
                </div>
            `;

            if (file.type.startsWith('image/')) {
                const imageUrl = URL.createObjectURL(file);
                item.innerHTML = `
                    <img src="${imageUrl}" alt="${escapePreviewText(file.name)}">
                    <span class="file-preview-name">${escapePreviewText(file.name)}</span>
                    <label class="portfolio-cover-option ${isSelected ? 'selected' : ''}">
                        <input type="radio" class="portfolio-cover-radio" name="portfolio_cover_visual" value="${coverChoice}" ${isSelected ? 'checked' : ''}>
                        <span>Use as cover</span>
                    </label>
                    ${orderHint}
                    ${actionControls}
                `;
                const image = item.querySelector('img');
                if (image) {
                    image.addEventListener('load', () => URL.revokeObjectURL(imageUrl), { once: true });
                }
                previewContainer.appendChild(item);
            } else if (file.type.startsWith('video/')) {
                item.innerHTML = `
                    <div class="video-preview-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="23 7 16 12 23 17 23 7"></polygon>
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                        </svg>
                    </div>
                    <span class="file-preview-name">${escapePreviewText(file.name)}</span>
                    ${orderHint}
                    ${actionControls}
                `;
                previewContainer.appendChild(item);
            } else {
                item.innerHTML = `
                    <span class="file-preview-name">${escapePreviewText(file.name)}</span>
                    ${orderHint}
                    ${actionControls}
                `;
                previewContainer.appendChild(item);
            }
        });

        if (mappedCoverChoice) {
            portfolioCoverChoiceInput.value = mappedCoverChoice;
        }

        updatePortfolioFeedback(previewContainer, errors);
        syncPortfolioCoverSelection();
        syncPortfolioDraftNotices();
    }

    function syncPortfolioCoverSelection() {
        if (!portfolioCoverChoiceInput) {
            return;
        }

        const radios = Array.from(form.querySelectorAll('.portfolio-cover-radio'));
        if (!radios.length) {
            portfolioCoverChoiceInput.value = '';
            return;
        }

        let selectedRadio = radios.find((radio) => radio.value === portfolioCoverChoiceInput.value);
        if (!selectedRadio) {
            selectedRadio = radios[0];
            selectedRadio.checked = true;
            portfolioCoverChoiceInput.value = selectedRadio.value;
        }

        form.querySelectorAll('.portfolio-cover-option').forEach((option) => {
            const radio = option.querySelector('.portfolio-cover-radio');
            option.classList.toggle('selected', radio === selectedRadio);
        });
    }

    window.syncPortfolioCoverSelection = syncPortfolioCoverSelection;

    document.addEventListener('change', function (event) {
        if (!event.target.classList.contains('portfolio-cover-radio')) {
            return;
        }

        if (portfolioCoverChoiceInput) {
            portfolioCoverChoiceInput.value = event.target.value;
        }

        syncPortfolioCoverSelection();
        saveStateToLocalStorage();
        queuePortfolioAutosave();
    });

    window.updatePortfolioFeedback = updatePortfolioFeedback;
    window.syncTalentPortfolioDraftState = syncPortfolioDraftNotices;
    window.queueTalentPortfolioAutosave = queuePortfolioAutosave;

    portfolioConfig.forEach(config => {
        const inputEl = document.getElementById(config.inputId);
        const previewContainer = document.getElementById(config.previewId);
        if (!inputEl || !previewContainer) {
            return;
        }

        let draggedPortfolioItem = null;

        previewContainer.addEventListener('click', function (event) {
            const removeButton = event.target.closest(`.portfolio-action-btn[data-portfolio-action="remove-new"]`);
            if (!removeButton) {
                return;
            }

            const item = removeButton.closest(`.file-preview-item[data-portfolio-item-type="new"][data-portfolio-source="${config.inputId}"]`);
            if (!item) {
                return;
            }

            const currentFiles = getTrackedPortfolioFiles(config, inputEl);
            const nextFiles = currentFiles.filter((_, index) => index !== Number.parseInt(item.dataset.fileIndex || '-1', 10));
            const selectedUploadSignature = getSelectedUploadFileSignature(config, currentFiles);

            setTrackedPortfolioFiles(config, inputEl, nextFiles);
            renderPortfolioSourceFiles(config, inputEl, previewContainer, nextFiles, {
                selectedUploadSignature
            });
            saveStateToLocalStorage();
            queuePortfolioAutosave();
        });

        previewContainer.addEventListener('dragstart', function (event) {
            const item = event.target.closest(`.file-preview-item[data-portfolio-item-type="new"][data-portfolio-source="${config.inputId}"]`);
            if (!item) {
                return;
            }

            draggedPortfolioItem = item;
            item.classList.add('is-dragging');
            if (event.dataTransfer) {
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', item.dataset.fileIndex || '');
            }
        });

        previewContainer.addEventListener('dragend', function () {
            if (draggedPortfolioItem) {
                draggedPortfolioItem.classList.remove('is-dragging');
                draggedPortfolioItem = null;
            }
        });

        previewContainer.addEventListener('dragover', function (event) {
            const targetItem = event.target.closest(`.file-preview-item[data-portfolio-item-type="new"][data-portfolio-source="${config.inputId}"]`);
            if (!draggedPortfolioItem || !targetItem || targetItem === draggedPortfolioItem) {
                return;
            }

            event.preventDefault();
            const targetRect = targetItem.getBoundingClientRect();
            const insertBeforeTarget = event.clientX < targetRect.left + (targetRect.width / 2);

            if (insertBeforeTarget) {
                previewContainer.insertBefore(draggedPortfolioItem, targetItem);
            } else {
                previewContainer.insertBefore(draggedPortfolioItem, targetItem.nextSibling);
            }
        });

        previewContainer.addEventListener('drop', function (event) {
            if (!draggedPortfolioItem) {
                return;
            }

            event.preventDefault();
            const currentFiles = getTrackedPortfolioFiles(config, inputEl);
            if (!currentFiles.length) {
                return;
            }

            const selectedUploadSignature = getSelectedUploadFileSignature(config, currentFiles);
            const orderedFiles = Array.from(
                previewContainer.querySelectorAll(`.file-preview-item[data-portfolio-item-type="new"][data-portfolio-source="${config.inputId}"]`)
            ).map((item) => currentFiles[Number.parseInt(item.dataset.fileIndex || '-1', 10)]).filter(Boolean);

            setTrackedPortfolioFiles(config, inputEl, orderedFiles);

            renderPortfolioSourceFiles(config, inputEl, previewContainer, orderedFiles, {
                selectedUploadSignature
            });
            saveStateToLocalStorage();
            queuePortfolioAutosave();
        });

        inputEl.addEventListener('change', function (e) {
            const previewContainer = document.getElementById(config.previewId);

            if (!previewContainer) return;

            const errors = [];
            const existingSelectedFiles = getTrackedPortfolioFiles(config, inputEl);
            const nextFiles = [...existingSelectedFiles];
            const metrics = getPortfolioPreviewMetrics(previewContainer, config.inputId);
            const selectedUploadSignature = getSelectedUploadFileSignature(config, existingSelectedFiles);
            let remainingSlots = Math.max(0, portfolioValidationRules.maxItems - metrics.total - existingSelectedFiles.length);

            Array.from(e.target.files).forEach((file) => {
                if (nextFiles.some((existingFile) => getPortfolioFileSignature(existingFile) === getPortfolioFileSignature(file))) {
                    errors.push(`${file.name} is already in your portfolio selection.`);
                    return;
                }

                if (!isAllowedPortfolioFile(file)) {
                    errors.push(`${file.name} is not an allowed file type. Use JPG, PNG, GIF, WebP, MP4, WebM, or MOV.`);
                    return;
                }

                if (file.size > portfolioValidationRules.maxFileSize) {
                    errors.push(`${file.name} exceeds the ${formatBytesToMb(portfolioValidationRules.maxFileSize)} limit.`);
                    return;
                }

                if (remainingSlots <= 0) {
                    errors.push(`Only ${portfolioValidationRules.maxItems} portfolio items are allowed. ${file.name} was not added.`);
                    return;
                }

                nextFiles.push(file);
                remainingSlots -= 1;
            });

            const dedupedFiles = dedupePortfolioFiles(nextFiles);
            setTrackedPortfolioFiles(config, inputEl, dedupedFiles);

            renderPortfolioSourceFiles(config, inputEl, previewContainer, dedupedFiles, {
                errors,
                selectedUploadSignature
            });
            saveStateToLocalStorage();
            queuePortfolioAutosave();
        });
    });

    // Custom Multi-Select Dropdown for Languages
    const selectBox = document.getElementById('languageSelectBox');
    if (selectBox) {
        const selectHeader = selectBox.querySelector('.select-box-header');
        const optionsContainer = selectBox.querySelector('.select-box-options');
        const placeholder = selectHeader.querySelector('.placeholder');

        // Toggle dropdown visibility
        selectHeader.addEventListener('click', function () {
            selectBox.classList.toggle('active');
        });

        // Handle checkbox changes
        const checkboxes = optionsContainer.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                updatePlaceholderText();
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!selectBox.contains(e.target)) {
                selectBox.classList.remove('active');
            }
        });

        // Update placeholder text based on selected options
        function updatePlaceholderText() {
            const selectedOptions = Array.from(checkboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.nextElementSibling.textContent);

            if (selectedOptions.length === 0) {
                placeholder.textContent = 'Select languages';
            } else if (selectedOptions.length === 1) {
                placeholder.textContent = selectedOptions[0];
            } else {
                placeholder.textContent = `${selectedOptions.length} languages selected`;
            }
        }
    }

    // Form submission handler
    form.addEventListener('submit', function (e) {
        isSubmittingForm = true;

        // Handle languages field - convert comma-separated string to array
        const languagesInput = document.getElementById('languages');
        if (languagesInput && languagesInput.value) {
            // Create a hidden input to store the array
            const languagesArray = languagesInput.value.split(',').map(lang => lang.trim()).filter(lang => lang.length > 0);

            // Create hidden inputs for each language
            languagesArray.forEach(function (lang, index) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'languages[]';
                hiddenInput.value = lang;
                form.appendChild(hiddenInput);
            });

            // Clear the original input to avoid duplication
            languagesInput.name = '';
        }

        // Clear localStorage on successful submission
        localStorage.removeItem(localStorageStepKey);
        localStorage.removeItem(localStorageDataKey);

        // Optional: Add a loading state to the submit button
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
        }

        // Test global.js functionality
        if (typeof showNotification === 'function') {
            console.log('Global JS is loaded and working');
        } else {
            console.log('Global JS is not loaded');
        }
    });

    // --- LOCALSTORAGE PERSISTENCE ---

    const saveStateToLocalStorage = () => {
        try {
            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                // Skip file inputs as they cannot be stored in localStorage as strings
                if (value instanceof File || value instanceof FileList) {
                    continue;
                }
                // Handle checkbox groups
                if (key.endsWith('[]')) {
                    key = key.slice(0, -2);
                    if (!data[key]) {
                        data[key] = [];
                    }
                    data[key].push(value);
                } else {
                    data[key] = value;
                }
            }
            
            // Store portfolio existing order if on edit page (to restore when coming back)
            const isEditPage = typeof talentData !== 'undefined' && Number(talentData.isEditPage) === 1;
            if (isEditPage) {
                const portfolioExistingOrderInput = form.querySelector('input[name="portfolio_existing_order"]');
                if (portfolioExistingOrderInput && portfolioExistingOrderInput.value) {
                    data['portfolio_existing_order'] = portfolioExistingOrderInput.value;
                }
                
                const portfolioCoverChoiceInput = form.querySelector('input[name="portfolio_cover_choice"]');
                if (portfolioCoverChoiceInput && portfolioCoverChoiceInput.value) {
                    data['portfolio_cover_choice'] = portfolioCoverChoiceInput.value;
                }
            }
            
            const pendingPortfolioUploads = getPendingPortfolioUploadsSnapshot();
            if (Object.keys(pendingPortfolioUploads).length) {
                data[portfolioPendingUploadsMetaKey] = pendingPortfolioUploads;
            }
            data._savedAt = Date.now();
            localStorage.setItem(localStorageDataKey, JSON.stringify(data));
            console.log('Form state saved to localStorage');
            syncPortfolioDraftNotices();
        } catch (error) {
            console.error('Error saving to localStorage:', error);
        }
    };

    const loadStateFromLocalStorage = () => {
        const savedData = localStorage.getItem(localStorageDataKey);
        if (!savedData) {
            syncPortfolioDraftNotices();
            return;
        }

        let data;
        try {
            data = JSON.parse(savedData);
        } catch (error) {
            console.error('Error parsing localStorage data:', error);
            syncPortfolioDraftNotices();
            return;
        }

        restoredPortfolioPendingUploads = data[portfolioPendingUploadsMetaKey] || {};

        if (typeof talentData !== 'undefined' && talentData.isEditPage) {
            // For edit pages, restore from localStorage if available (even if PHP values exist)
            // This ensures portfolio changes persist after saving progress and returning
            const portfolioExistingOrderInput = form.querySelector('input[name="portfolio_existing_order"]');
            const portfolioCoverChoiceInput = form.querySelector('input[name="portfolio_cover_choice"]');
            
            // Check if there's localStorage data to restore (user returned after saving progress)
            const hasLocalStoragePortfolioData = data['portfolio_existing_order'] || data['portfolio_cover_choice'];
            
            if (hasLocalStoragePortfolioData && portfolioExistingOrderInput) {
                // Restore portfolio order from localStorage
                portfolioExistingOrderInput.value = data['portfolio_existing_order'] || '';
                console.log('Restored portfolio existing order from localStorage:', data['portfolio_existing_order']);
                
                // Re-load existing portfolio images
                if (typeof loadExistingPortfolio === 'function') {
                    setTimeout(() => loadExistingPortfolio(), 100);
                }
            }
            
            if (data['portfolio_cover_choice'] && portfolioCoverChoiceInput) {
                portfolioCoverChoiceInput.value = data['portfolio_cover_choice'];
                console.log('Restored portfolio cover choice from localStorage:', data['portfolio_cover_choice']);
                if (typeof syncPortfolioCoverSelection === 'function') {
                    syncPortfolioCoverSelection();
                }
            }
            
            // Still process any pending uploads
            portfolioConfig.forEach((config) => {
                const previewContainer = document.getElementById(config.previewId);
                const inputEl = document.getElementById(config.inputId);
                if (previewContainer && inputEl) {
                    const currentFiles = Array.from(inputEl.files || []);
                    if (currentFiles.length > 0 || restoredPortfolioPendingUploads[config.inputId]) {
                        renderPortfolioSourceFiles(config, inputEl, previewContainer, currentFiles);
                    }
                }
            });
            syncPortfolioDraftNotices();
            
            // For edit page, restore profile photo preview from localStorage if a new one was uploaded
            // This ensures that if user uploaded a new photo and saved progress, they see it when they return
            const profilePhotoPreview = data._profilePhotoPreview;
            if (profilePhotoPreview) {
                const photoPreview = document.getElementById('photoPreview');
                if (photoPreview) {
                    photoPreview.innerHTML = `<img src="${profilePhotoPreview}" alt="Profile Preview" style="max-width: 100%; max-height: 100%;">`;
                    console.log('Restored profile photo preview from localStorage on edit page');
                }
            }
            
            // Don't run the rest of the form restoration logic that might interfere with server-side rendered data
            return;
        }

        isRestoringFormState = true;

        try {
            // Restore profile photo preview from localStorage for submission page
            const profilePhotoPreview = data._profilePhotoPreview;
            if (profilePhotoPreview) {
                const photoPreview = document.getElementById('photoPreview');
                if (photoPreview) {
                    photoPreview.innerHTML = `<img src="${profilePhotoPreview}" alt="Profile Preview" style="max-width: 100%; max-height: 100%;">`;
                    console.log('Restored profile photo preview from localStorage');
                }
            }

            // Handle Notable Works repeater first
            if (data.workTitle && Array.isArray(data.workTitle)) {
                const worksContainer = document.getElementById('notableWorksContainer');
                const firstWorkItem = worksContainer.querySelector('.notable-work-item');
                const presentValues = data.workPresent || [];

                // Clear any extra items, keeping the template
                while (worksContainer.children.length > 1) {
                    worksContainer.removeChild(worksContainer.lastChild);
                }

                data.workTitle.forEach((title, index) => {
                    let workItem;
                    if (index === 0) {
                        workItem = firstWorkItem;
                    } else {
                        workItem = firstWorkItem.cloneNode(true);
                        workItem.querySelector('.remove-work-btn').style.display = 'inline-block';
                        worksContainer.appendChild(workItem);
                    }

                    workItem.querySelector('input[name="workTitle[]"]').value = title || '';
                    workItem.querySelector('input[name="workRole[]"]').value = (data.workRole || [])[index] || '';
                    workItem.querySelector('input[name="workStartDate[]"]').value = (data.workStartDate || [])[index] || '';
                    workItem.querySelector('input[name="workEndDate[]"]').value = (data.workEndDate || [])[index] || '';
                    workItem.querySelector('textarea[name="workDescription[]"]').value = (data.workDescription || [])[index] || '';

                    const presentCheckbox = workItem.querySelector('.work-present-checkbox');
                    const isPresent = Array.isArray(presentValues) && presentValues.includes(String(index));
                    if (presentCheckbox) {
                        presentCheckbox.checked = isPresent;
                        const endDateInput = workItem.querySelector('.work-end-date');
                        if (endDateInput) {
                            endDateInput.disabled = isPresent;
                        }
                    }
                });

                if (typeof worksContainer.syncNotableWorkIndexes === 'function') {
                    worksContainer.syncNotableWorkIndexes();
                }
            }

            for (const key in data) {
                // Skip repeater fields as they are handled separately above
                if (['workTitle', 'workRole', 'workStartDate', 'workEndDate', 'workDescription', 'workPresent', portfolioPendingUploadsMetaKey, '_savedAt'].includes(key)) continue;

                const value = data[key];

                // Handle other fields normally
                const elements = form.querySelectorAll(`[name="${key}"], [name="${key}[]"]`);

                if (elements.length > 0) {
                    const el = elements[0];
                    if (el.type === 'radio') {
                        const radioToSelect = form.querySelector(`input[name="${key}"][value="${value}"]`);
                        if (radioToSelect) {
                            radioToSelect.checked = true;
                            // For radio cards, also update the parent label's class
                            const card = radioToSelect.closest('.radio-card');
                            if (card) {
                                // Remove selected from siblings
                                card.parentElement.querySelectorAll('.radio-card').forEach(c => c.classList.remove('selected'));
                                card.classList.add('selected');
                            }
                        }
                    } else if (el.type === 'checkbox') {
                        // Handle single checkbox
                        if (elements.length === 1) {
                            el.checked = value === 'on' || value === true;
                        } else { // Handle checkbox group
                            elements.forEach(checkbox => {
                                if (Array.isArray(value) && value.includes(checkbox.value)) {
                                    checkbox.checked = true;
                                }
                            });
                        }
                    } else if (el.type === 'file') {
                        // Skip file inputs as their values cannot be set programmatically for security reasons
                        continue;
                    } else {
                        el.value = value;
                    }

                    // Special handling for UI updates
                    if (key === 'domain') {
                        setSelectedDomain(value);
                    }

                    if (key === 'role') {
                        setSelectedRole(value);
                    }

                    // Trigger change event for fields with conditional logic
                    if (['willingToTravel', 'selectedDomain', 'selectedRole', 'brandCollabs'].includes(el.id)) {
                        el.dispatchEvent(new Event('change'));
                    }
                }
            }
        } finally {
            isRestoringFormState = false;
        }

        portfolioConfig.forEach((config) => {
            const previewContainer = document.getElementById(config.previewId);
            if (previewContainer) {
                updatePortfolioFeedback(previewContainer);
            }
        });
        syncPortfolioCoverSelection();
        syncPortfolioDraftNotices();
        console.log('Form state loaded from localStorage.');
    };

    // Clean up any problematic localStorage data on initialization
    const checkAndCleanLocalStorage = () => {
        const savedData = localStorage.getItem(localStorageDataKey);
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                // No cleanup needed currently
            } catch (e) {
                console.error('Error cleaning localStorage:', e);
            }
        }
    };

    checkAndCleanLocalStorage();

    const persistDraftState = (event) => {
        if (isSubmittingForm || isRestoringFormState) {
            return;
        }

        saveStateToLocalStorage();

        if (event?.target?.type !== 'file') {
            queuePortfolioAutosave(1200);
        }
    };

    // Save form state on any input/change and debounce draft sync
    form.addEventListener('input', persistDraftState);
    form.addEventListener('change', persistDraftState);
    window.saveTalentFormStateToLocalStorage = saveStateToLocalStorage;

    window.addEventListener('beforeunload', function (event) {
        if (isSubmittingForm || !getPendingPortfolioUploadCount()) {
            return;
        }

        event.preventDefault();
        event.returnValue = '';
    });

    // Test if global.js functions are available
    if (typeof showNotification === 'function') {
        console.log('Global JS is loaded and working');
        // Test the notification
        // showNotification('Talent submission page loaded successfully!', 'success');
    } else {
        console.log('Global JS is not loaded');
    }

    // --- POPULATE FORM FROM DRAFT ---
    if (hasServerDraft) {
        const draft = talentData.draft;
        isRestoringFormState = true;

        try {
            // If draft has current step information, use it to set the form to the correct step
            if (draft.currentStep) {
                const stepNumber = parseInt(draft.currentStep);
                if (!isNaN(stepNumber) && stepNumber >= 1 && stepNumber <= totalSteps) {
                    showStep(stepNumber);
                }
            }

            // Helper to set value safely
            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val || '';
            };

            // Basic Info
            if (draft.post_id) {
                let postIdInput = document.createElement('input');
                postIdInput.type = 'hidden';
                postIdInput.name = 'post_id';
                postIdInput.value = draft.post_id;
                form.appendChild(postIdInput);
            }

            setVal('fullName', draft.fullName);
            setVal('email', draft.email);
            setVal('phone', draft.phone);
            setVal('city', draft.city || draft.state);
            setVal('country', draft.country);

            const setRadio = (name, val) => {
                if (!val) return;
                const radio = form.querySelector(`input[name="${name}"][value="${val}"]`);
                if (radio) radio.checked = true;
            };

            setRadio('ageGroup', draft.ageGroup);
            setRadio('gender', draft.gender);
            setRadio('heightUnit', draft.heightUnit);
            setRadio('willingToTravel', draft.willingToTravel);

            const travelCheckbox = document.getElementById('willingToTravel');
            if (travelCheckbox && travelCheckbox.type === 'checkbox') {
                travelCheckbox.checked = draft.willingToTravel === 'yes' || draft.willingToTravel === '1';
                travelCheckbox.dispatchEvent(new Event('change'));
            }

            setVal('height', draft.height);
            setVal('measurements', draft.measurements);
            setVal('styleDescription', draft.styleDescription);
            setVal('yearsActive', draft.yearsActive);
            setVal('affiliation', draft.affiliation);
            setVal('education', draft.education);
            setVal('interestedProjects', draft.interestedProjects);
            setVal('preferredLocations', draft.preferredLocations);
            setVal('brandCollabs', draft.brandCollabs);
            setVal('instagram', draft.instagram);
            setVal('linkedin', draft.linkedin);
            setVal('tiktok', draft.tiktok);
            setVal('website', draft.website);

            if (draft.domain) {
                setSelectedDomain(draft.domain);
            }

            if (draft.role) {
                setSelectedRole(draft.role);
            }

            if (draft.languages && Array.isArray(draft.languages)) {
                const langInput = document.getElementById('languages');
                if (langInput) {
                    langInput.value = draft.languages.join(', ');
                }

                const checkboxes = document.querySelectorAll('#languageSelectBox input[type="checkbox"]');
                checkboxes.forEach(cb => {
                    if (draft.languages.includes(cb.value)) {
                        cb.checked = true;
                    }
                });
                const selectBox = document.getElementById('languageSelectBox');
                if (selectBox) {
                    const placeholder = selectBox.querySelector('.placeholder');
                    const selectedCount = draft.languages.length;
                    if (selectedCount === 0) placeholder.textContent = 'Select languages';
                    else if (selectedCount === 1) placeholder.textContent = draft.languages[0];
                    else placeholder.textContent = `${selectedCount} languages selected`;
                }
            }

            const setCheckboxes = (name, values) => {
                if (!values || !Array.isArray(values)) return;
                values.forEach(val => {
                    const cb = form.querySelector(`input[name="${name}[]"][value="${val}"]`);
                    if (cb) cb.checked = true;
                });
            };

            setCheckboxes('availableFor', draft.availableFor);
            setCheckboxes('designCategories', draft.designCategories);

            if (draft.notableWorks && Array.isArray(draft.notableWorks) && draft.notableWorks.length > 0) {
                const container = document.getElementById('notableWorksContainer');
                if (container) {
                    const firstItem = container.querySelector('.notable-work-item');

                    draft.notableWorks.forEach((work, index) => {
                        let item;
                        if (index === 0) {
                            item = firstItem;
                        } else {
                            item = firstItem.cloneNode(true);
                            container.appendChild(item);
                            item.querySelector('.remove-work-btn').style.display = 'inline-block';
                        }

                        if (item) {
                            item.querySelector('input[name="workTitle[]"]').value = work.title || '';
                            item.querySelector('input[name="workRole[]"]').value = work.role || '';
                            item.querySelector('input[name="workStartDate[]"]').value = work.startDate || '';
                            item.querySelector('input[name="workEndDate[]"]').value = work.endDate || '';
                            item.querySelector('textarea[name="workDescription[]"]').value = work.description || '';

                            const presentCb = item.querySelector('.work-present-checkbox');
                            if (presentCb) {
                                presentCb.checked = work.present === 'on';
                                const endDateInput = item.querySelector('.work-end-date');
                                if (endDateInput) {
                                    endDateInput.disabled = presentCb.checked;
                                }
                            }
                        }
                    });

                    if (typeof container.syncNotableWorkIndexes === 'function') {
                        container.syncNotableWorkIndexes();
                    }
                }
            }
        } finally {
            isRestoringFormState = false;
        }
    }

    // Load the last active step from localStorage, otherwise default to 1
    const savedStep = localStorage.getItem(localStorageStepKey);
    const initialStep = savedStep ? parseInt(savedStep, 10) : 1;

    // First, show the correct step
    showStep(initialStep);

    // Then, load the data from localStorage to populate the fields
    loadStateFromLocalStorage();
    syncSelectionStateFromInputs();
    
    // For edit pages, portfolio loading is handled by template-talent-edit.php inline script
    // Only load portfolio here for submission pages when on step 7
    const isEditPage = typeof talentData !== 'undefined' && Number(talentData.isEditPage) === 1;
    if (!isEditPage && initialStep === 7) {
        setTimeout(() => {
            if (typeof loadExistingPortfolio === 'function') {
                loadExistingPortfolio();
            }
        }, 250);
    }
});



// At the end of the file, initialize the dynamic loader
// Wait for the window to be fully loaded
window.addEventListener('load', initTalentForm);

function initTalentForm() {
    // Check if we're on the talent submission page
    const form = document.getElementById('velvetReelForm') || document.getElementById('velvetReelEditForm');
    if (!form) {
        return;
    }

    // Initialize video link fields
    // Add a small delay to ensure all elements are loaded
    setTimeout(function () {
        initVideoLinkFields();
        initVideoLinkValidation();
    }, 500);
}

// Function to initialize video link fields
function initVideoLinkFields() {
    // Attach event listeners to add buttons
    const addButtons = document.querySelectorAll('.add-video-link-btn-simple-inline');
    addButtons.forEach(function (button) {
        // Check if event listener is already attached
        if (!button.hasAttribute('data-listener-attached')) {
            button.setAttribute('data-listener-attached', 'true');
            button.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                addVideoLinkField(e.target);
            });
        }
    });

    // Attach event listeners to remove buttons
    const removeButtons = document.querySelectorAll('.remove-video-link-btn-simple');
    removeButtons.forEach(function (button) {
        // Check if event listener is already attached
        if (!button.hasAttribute('data-listener-attached')) {
            button.setAttribute('data-listener-attached', 'true');
            button.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                removeVideoLinkField(e.target);
            });
        }
    });
}

// Function to add a new video link field
function addVideoLinkField(button) {
    // Ensure the button is enabled
    if (button.disabled) {
        button.disabled = false;
    }

    const container = button.closest('.video-links-container');
    if (!container) return;

    const videoLinkItems = container.querySelectorAll('.video-link-item');
    if (videoLinkItems.length >= 10) {
        alert('You can only add up to 10 video links.');
        return;
    }

    // Create new video link item with input and remove button
    const newItem = document.createElement('div');
    newItem.className = 'video-link-item';
    newItem.innerHTML = `
        <input type="url" name="videoLinks[]" placeholder="https://youtube.com/watch?v=..." class="video-url-input">
        <button type="button" class="remove-video-link-btn-simple">×</button>
    `;

    // Insert before the button's parent item (the last video-link-item)
    const buttonParent = button.closest('.video-link-item');
    if (buttonParent) {
        container.insertBefore(newItem, buttonParent);
    } else {
        // Fallback: append to container if we can't find the parent
        container.appendChild(newItem);
    }

    // Add event listener to the new remove button
    const removeBtn = newItem.querySelector('.remove-video-link-btn-simple');
    if (removeBtn) {
        // Ensure the button is enabled
        removeBtn.disabled = false;
        // Check if event listener is already attached
        if (!removeBtn.hasAttribute('data-listener-attached')) {
            removeBtn.setAttribute('data-listener-attached', 'true');
            removeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                removeVideoLinkField(e.target);
            });
        }
    }

    // Add event listeners to the new inputs for validation
    const urlInput = newItem.querySelector('.video-url-input');

    if (urlInput) {
        urlInput.addEventListener('input', function (e) {
            e.stopPropagation();
            validateVideoUrl(e);
        });
    }
}



// Function to remove a video link field
function removeVideoLinkField(button) {
    // Verify the button exists
    if (!button) {
        return;
    }

    const item = button.closest('.video-link-item');
    if (item) {
        const container = item.closest('.video-links-container');
        if (container) {
            const items = container.querySelectorAll('.video-link-item');
            // Don't remove if there's exactly one item left (the first input)
            if (items.length <= 1) {
                return;
            }
        }

        // Remove the item completely
        item.remove();
    }
}

// Function to validate video URL
function validateVideoUrl(event) {
    const input = event.target;
    const url = input.value.trim();

    // If the field is empty, clear any error state
    if (url === '') {
        input.classList.remove('error');
        return;
    }

    // Check if it's a valid URL
    try {
        new URL(url);
        // Check if it's a YouTube or Vimeo URL
        if (url.includes('youtube.com') || url.includes('youtu.be') || url.includes('vimeo.com')) {
            input.classList.remove('error');
        } else {
            input.classList.add('error');
        }
    } catch (e) {
        input.classList.add('error');
    }
}

// Initialize video link validation for existing fields
function initVideoLinkValidation() {
    document.querySelectorAll('.video-url-input').forEach(input => {
        input.addEventListener('input', function (e) {
            e.stopPropagation();
            validateVideoUrl(e);
        });
    });
}

/**
 * Show a success popup using the existing success popup style
 * @param {string} message - The message to display
 * @param {string} type - The type of popup (success, error, info)
 */
function showSuccessPopup(message, type = 'success') {
    // Remove any existing popups
    const existingPopup = document.querySelector('.success-popup-overlay');
    if (existingPopup) {
        existingPopup.remove();
    }

    // Create popup overlay
    const overlay = document.createElement('div');
    overlay.className = 'success-popup-overlay';

    // Create container that matches the template structure
    const container = document.createElement('div');
    container.className = 'success-container';

    // Create card that matches the template structure
    const card = document.createElement('div');
    card.className = 'success-card';

    // Create popup content based on type
    let iconHtml = '';
    let titleText = '';
    let buttonHtml = '';
    let buttonLink = '/talent/';
    let buttonText = 'View Portfolios';

    switch (type) {
        case 'success':
            iconHtml = `
                <div class="success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
            `;
            titleText = 'Thank You!';
            buttonHtml = `<a href="${buttonLink}" class="btn btn-primary">${buttonText}</a>`;
            break;
        case 'error':
            iconHtml = `
                <div class="success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
            `;
            titleText = 'Error';
            buttonLink = '/talent/';
            buttonText = 'View My Profile';
            buttonHtml = `<a href="${buttonLink}" class="btn btn-primary">${buttonText}</a>`;
            break;
        case 'info':
            iconHtml = `
                <div class="success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
            `;
            titleText = 'Information';
            break;
    }

    card.innerHTML = `
        ${iconHtml}
        <h2 class="success-title">${titleText}</h2>
        <p class="success-message">${message}</p>
        ${buttonHtml}
    `;

    // Add close button inside the card
    const closeBtn = document.createElement('button');
    closeBtn.className = 'success-popup-close';
    closeBtn.innerHTML = '&times;';
    closeBtn.setAttribute('aria-label', 'Close');
    closeBtn.addEventListener('click', function () {
        overlay.remove();
    });
    card.appendChild(closeBtn);

    // Allow clicking outside to close
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            overlay.remove();
        }
    });

    // Assemble the structure to match the template
    container.appendChild(card);
    overlay.appendChild(container);
    document.body.appendChild(overlay);
}

// Make the function globally available
// Disable right-click and image dragging to prevent image downloads

document.addEventListener('DOMContentLoaded', function () {
    // Disable right-click on images
    document.addEventListener('contextmenu', function (e) {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
            return false;
        }
    });

    // Disable drag-and-drop for images
    document.addEventListener('dragstart', function (e) {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
            return false;
        }
    });

    // Disable image selection
    document.addEventListener('selectstart', function (e) {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
            return false;
        }
    });
});

window.showSuccessPopup = showSuccessPopup;

/**
 * Refresh the nonce before form submission to prevent stale nonce errors
 * This fetches a fresh nonce from the server and updates the form
 */
async function refreshFormNonce() {
    return new Promise((resolve, reject) => {
        // Determine which nonce action to use based on the form
        const form = document.getElementById('velvetReelForm') || document.getElementById('velvetReelEditForm');
        const isEditPage = form && form.id === 'velvetReelEditForm';
        const nonceAction = isEditPage ? 'talent_update' : 'talent_submission';
        const nonceFieldName = isEditPage ? 'talent_update_nonce' : 'talent_submission_nonce';

        // Get AJAX URL
        const ajaxUrl = typeof talentData !== 'undefined' && talentData.ajaxurl
            ? talentData.ajaxurl
            : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');

        const params = new URLSearchParams();
        params.append('action', 'get_refreshed_nonce');
        params.append('nonce_action', nonceAction);

        fetch(ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.nonce) {
                    // Update the form nonce field
                    const nonceField = form.querySelector(`[name="${nonceFieldName}"]`);
                    if (nonceField) {
                        nonceField.value = data.data.nonce;
                    }

                    // Also update talentData.nonce if it exists
                    if (typeof talentData !== 'undefined') {
                        talentData.nonce = data.data.nonce;
                    }

                    console.log('Nonce refreshed successfully');
                    resolve(data.data.nonce);
                } else {
                    console.warn('Failed to refresh nonce:', data);
                    resolve(null);
                }
            })
            .catch(error => {
                console.error('Error refreshing nonce:', error);
                resolve(null);
            });
    });
}

// Add event listener to refresh nonce before form submission
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('velvetReelForm') || document.getElementById('velvetReelEditForm');
    if (form) {
        form.addEventListener('submit', async function (e) {
            // Refresh nonce before submission - this helps prevent stale nonce issues
            await refreshFormNonce();
        });
    }
});
