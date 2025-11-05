document.addEventListener('DOMContentLoaded', function() {
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

    // --- THEME SWITCHER ---
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;

    const setTheme = (theme) => {
        body.dataset.theme = theme;
        localStorage.setItem('theme', theme);
    };

    themeToggle.addEventListener('click', () => {
        const newTheme = body.dataset.theme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });

    // Apply saved theme on page load, default to dark
    const savedTheme = localStorage.getItem('theme') || 'dark';
    setTheme(savedTheme);

    // --- NAVIGATION ---
    const showStep = (stepNumber) => {
        // Hide all steps
        document.querySelectorAll('.form-step').forEach(step => step.classList.remove('active'));
        // Show the correct step
        const activeStepEl = document.querySelector(`.form-step[data-step="${stepNumber}"]`);
        if (activeStepEl) {
            activeStepEl.classList.add('active');
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
            if (currentStep < totalSteps) {
                if (currentStep === 5) { // After Domain selection
                    const selectedDomain = document.getElementById('selectedDomain').value;
                    filterRolesByDomain(selectedDomain);
                }
                if (currentStep === 6) { // After Role selection
                    const selectedRole = document.getElementById('selectedRole').value;
                    toggleRoleSpecificFields(selectedRole);
                }
                showStep(currentStep + 1);
            }
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    });

    // --- DYNAMIC FORM ELEMENTS ---

    // Profile Photo Preview
    document.getElementById('profilePhoto')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('photoPreview').innerHTML = `<img src="${event.target.result}" alt="Profile Preview">`;
            };
            reader.readAsDataURL(file);
        }
    });

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
    document.getElementById('willingToTravel')?.addEventListener('change', function() {
        const section = document.getElementById('preferredLocationsSection');
        if (section) {
            section.style.display = this.checked ? 'block' : 'none';
        }
    });

    // Conditional Fields (Physical Details for Models/Actors)
    const roleContainer = document.getElementById('roleContainer');
    const physicalDetailsSection = document.getElementById('physicalDetailsSection');

    if (roleContainer && physicalDetailsSection) {
        roleContainer.addEventListener('click', (e) => {
            const roleCard = e.target.closest('.role-card');
            if (roleCard) {
                const role = roleCard.dataset.role;
                if (['fashion-model', 'actor'].includes(role)) {
                    physicalDetailsSection.style.display = 'grid';
                } else if (document.getElementById('selectedRole').value !== 'fashion-model' && document.getElementById('selectedRole').value !== 'actor') {
                    physicalDetailsSection.style.display = 'none';
                }
            }
        });
    }

    // Domain & Role Selection
    document.querySelectorAll('.domain-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.domain-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('selectedDomain').value = this.dataset.domain || '';
        });
    });

    const filterRolesByDomain = (domain) => {
        document.querySelectorAll('.role-category').forEach(category => {
            category.style.display = (domain === 'both' || category.dataset.domain === domain) ? 'block' : 'none';
        });
    };

    document.querySelectorAll('.role-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('selectedRole').value = this.dataset.role || '';
        });
    });

    document.getElementById('roleSearch')?.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.role-card').forEach(card => {
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(searchTerm) ? 'block' : 'none';
        });
    });

    const toggleRoleSpecificFields = (role) => {
        document.querySelectorAll('.role-specific-fields').forEach(el => {
            el.style.display = 'none';
        });
        const roleSpecificEl = document.querySelector(`[data-role-specific="${role}"]`);
        if (roleSpecificEl) {
            roleSpecificEl.style.display = 'block';
        }
    };

    // Notable Works Repeater
    const worksContainer = document.getElementById('notableWorksContainer');
    if (worksContainer) {
        document.getElementById('addWorkBtn')?.addEventListener('click', () => {
            if (worksContainer.children.length >= 10) {
                alert('You can add a maximum of 10 works.');
                return;
            }
            const firstWorkItem = worksContainer.querySelector('.notable-work-item');
            const newWorkItem = firstWorkItem.cloneNode(true);
            newWorkItem.querySelectorAll('input, textarea').forEach(input => input.value = '');
            newWorkItem.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            newWorkItem.querySelector('.remove-work-btn').style.display = 'inline-block';
            worksContainer.appendChild(newWorkItem);
        });

        worksContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-work-btn')) {
                e.target.closest('.notable-work-item').remove();
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
    }

    // Portfolio Preview for all roles
    const portfolioConfig = [
        { inputId: 'portfolio', previewId: 'portfolioPreview' },
        { inputId: 'portfolio-textile', previewId: 'portfolioPreviewTextile' },
        { inputId: 'portfolio-accessory', previewId: 'portfolioPreviewAccessory' },
        { inputId: 'portfolio-illustrator', previewId: 'portfolioPreviewIllustrator' },
        { inputId: 'portfolio-model', previewId: 'portfolioPreviewModel' },
        { inputId: 'portfolio-choreographer', previewId: 'portfolioPreviewChoreographer' },
        { inputId: 'portfolio-stylist', previewId: 'portfolioPreviewStylist' },
        { inputId: 'portfolio-makeup', previewId: 'portfolioPreviewMakeup' },
        { inputId: 'portfolio-director', previewId: 'portfolioPreviewDirector' },
        { inputId: 'portfolio-storyboard', previewId: 'portfolioPreviewStoryboard' },
        { inputId: 'portfolio-actor', previewId: 'portfolioPreviewActor' },
        { inputId: 'portfolio-voice', previewId: 'portfolioPreviewVoice' },
        { inputId: 'portfolio-dancer', previewId: 'portfolioPreviewDancer' },
        { inputId: 'portfolio-dop', previewId: 'portfolioPreviewDop' },
        { inputId: 'portfolio-editor', previewId: 'portfolioPreviewEditor' }
    ];
    
    portfolioConfig.forEach(config => {
        document.getElementById(config.inputId)?.addEventListener('change', function(e) {
            const previewContainer = document.getElementById(config.previewId);
            
            if (!previewContainer) return;
            previewContainer.innerHTML = ''; // Clear existing previews

            Array.from(e.target.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const item = document.createElement('div');
                        item.className = 'file-preview-item';
                        item.innerHTML = `
                            <img src="${event.target.result}" alt="${file.name}">
                            <span class="file-preview-name">${file.name}</span>
                        `;
                        previewContainer.appendChild(item);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    });

    // Custom Multi-Select Dropdown for Languages
    const selectBox = document.getElementById('languageSelectBox');
    if (selectBox) {
        const selectHeader = selectBox.querySelector('.select-box-header');
        const optionsContainer = selectBox.querySelector('.select-box-options');
        const placeholder = selectHeader.querySelector('.placeholder');
        
        // Toggle dropdown visibility
        selectHeader.addEventListener('click', function() {
            selectBox.classList.toggle('active');
        });
        
        // Handle checkbox changes
        const checkboxes = optionsContainer.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updatePlaceholderText();
            });
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
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
    form.addEventListener('submit', function(e) {
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

    // --- INITIALIZATION ---
    // Test if global.js functions are available
    if (typeof showNotification === 'function') {
        console.log('Global JS is loaded and working');
        // Test the notification
        // showNotification('Talent submission page loaded successfully!', 'success');
    } else {
        console.log('Global JS is not loaded');
    }
    
    showStep(1);
});