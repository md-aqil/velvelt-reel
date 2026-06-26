/**
 * Talent Form Application Module
 * 
 * A modular, organized JavaScript application for the talent submission form.
 * This replaces the monolithic talent--submission.js with a cleaner architecture.
 * 
 * @package HelloElementorChild
 */

// ============================================================================
// CONFIGURATION
// ============================================================================

const TalentFormConfig = {
    // Form selectors
    selectors: {
        form: '#velvetReelForm, #velvetReelEditForm',
        nextBtn: '#nextBtn',
        prevBtn: '#prevBtn',
        submitBtn: '#submitBtn',
        saveProgressBtn: '#saveProgressBtn',
        selectedDomain: '#selectedDomain',
        selectedRole: '#selectedRole',
        progressSidebar: '.progress-sidebar .step',
        formSteps: '.form-step',
        progressFill: '.progress-fill',
        progressPercentage: '.progress-percentage',
        // Special sections
        roleContainer: '#roleContainer',
        physicalDetailsSection: '#physicalDetailsSection',
        notableWorksContainer: '#notableWorksContainer',
        roleFieldsContainer: '#roleFieldsContainer'
    },
    
    // LocalStorage keys
    storageKeys: {
        currentStep: 'talentForm_currentStep',
        formData: 'talentForm_data'
    },
    
    // Roles that require physical details
    rolesNeedingPhysicalDetails: [
        'fashion-model', 
        'actor', 
        'dancer', 
        'ramp-choreographer', 
        'model-development-coach', 
        'runway-coach'
    ],
    
    // Role display names
    roleNames: {
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
    },
    
    // Maximum items
    maxNotableWorks: 10,
    maxVideoLinks: 10
};

// ============================================================================
// STATE MANAGEMENT
// ============================================================================

class TalentFormState {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 0;
        this.form = null;
        this.elements = {};
        this.isEditPage = false;
    }
    
    initialize() {
        this.form = document.querySelector(TalentFormConfig.selectors.form);
        if (!this.form) return false;
        
        // Cache DOM elements
        this.elements = {
            nextBtn: document.querySelector(TalentFormConfig.selectors.nextBtn),
            prevBtn: document.querySelector(TalentFormConfig.selectors.prevBtn),
            submitBtn: document.querySelector(TalentFormConfig.selectors.submitBtn),
            selectedDomain: document.querySelector(TalentFormConfig.selectors.selectedDomain),
            selectedRole: document.querySelector(TalentFormConfig.selectors.selectedRole),
            roleContainer: document.querySelector(TalentFormConfig.selectors.roleContainer),
            physicalDetailsSection: document.querySelector(TalentFormConfig.selectors.physicalDetailsSection),
            notableWorksContainer: document.querySelector(TalentFormConfig.selectors.notableWorksContainer),
            roleFieldsContainer: document.querySelector(TalentFormConfig.selectors.roleFieldsContainer)
        };
        
        // Calculate total steps
        const steps = document.querySelectorAll(TalentFormConfig.selectors.formSteps);
        this.totalSteps = steps.length;
        
        // Check if edit page
        this.isEditPage = this.form.id === 'velvetReelEditForm';
        
        return true;
    }
    
    get(key) {
        return this.elements[key];
    }
}

const talentFormState = new TalentFormState();

// ============================================================================
// NAVIGATION MODULE
// ============================================================================

const TalentFormNavigation = {
    /**
     * Show a specific step
     */
    showStep: function(stepNumber) {
        const { form, elements } = talentFormState;
        if (!form) return;
        
        // Hide all steps
        document.querySelectorAll(TalentFormConfig.selectors.formSteps).forEach(
            step => step.classList.remove('active')
        );
        
        // Show target step
        const activeStepEl = document.querySelector(
            `${TalentFormConfig.selectors.formSteps}[data-step="${stepNumber}"]`
        );
        if (activeStepEl) {
            activeStepEl.classList.add('active');
        }
        
        // Update sidebar
        document.querySelectorAll(TalentFormConfig.selectors.progressSidebar).forEach((stepEl, index) => {
            stepEl.classList.remove('active', 'completed');
            if ((index + 1) < stepNumber) {
                stepEl.classList.add('completed');
            } else if ((index + 1) === stepNumber) {
                stepEl.classList.add('active');
            }
        });
        
        // Update navigation buttons
        const { nextBtn, prevBtn, submitBtn } = elements;
        if (prevBtn) prevBtn.style.display = stepNumber === 1 ? 'none' : 'inline-block';
        if (nextBtn) nextBtn.style.display = stepNumber === talentFormState.totalSteps ? 'none' : 'inline-block';
        if (submitBtn) submitBtn.style.display = stepNumber === talentFormState.totalSteps ? 'inline-block' : 'none';
        
        // Update state
        talentFormState.currentStep = stepNumber;
        
        // Update progress bar
        this.updateProgressBar();
        
        // Save to localStorage
        localStorage.setItem(TalentFormConfig.storageKeys.currentStep, stepNumber);
        
        // Handle step 7 special logic
        if (stepNumber === 7) {
            this.handleStep7Navigation();
        }
    },
    
    /**
     * Update the progress bar
     */
    updateProgressBar: function() {
        const { currentStep, totalSteps } = talentFormState;
        const progress = Math.min(((currentStep - 1) / (totalSteps - 1)) * 100, 100);
        
        const progressFill = document.querySelector(TalentFormConfig.selectors.progressFill);
        const progressPercentage = document.querySelector(TalentFormConfig.selectors.progressPercentage);
        
        if (progressFill) progressFill.style.width = `${progress}%`;
        if (progressPercentage) progressPercentage.textContent = `${Math.round(progress)}% Completed`;
    },
    
    /**
     * Navigate to next step
     */
    nextStep: function() {
        const { currentStep, totalSteps } = talentFormState;
        if (currentStep < totalSteps) {
            if (currentStep === 5) {
                const selectedDomain = talentFormState.elements.selectedDomain?.value;
                TalentFormRoles.filterRolesByDomain(selectedDomain);
            }
            if (currentStep === 6) {
                const selectedRole = talentFormState.elements.selectedRole?.value;
                if (selectedRole) {
                    TalentFormRoles.toggleRoleSpecificFields(selectedRole);
                }
            }
            setTimeout(() => {
                this.showStep(currentStep + 1);
            }, 50);
        }
    },
    
    /**
     * Navigate to previous step
     */
    prevStep: function() {
        const { currentStep } = talentFormState;
        if (currentStep > 1) {
            this.showStep(currentStep - 1);
        }
    },
    
    /**
     * Handle navigation to step 7 (role-specific fields)
     */
    handleStep7Navigation: function() {
        const selectedRole = talentFormState.elements.selectedRole?.value;
        if (!selectedRole) return;
        
        // Ensure step 7 is visible
        const step7Element = document.querySelector('.form-step[data-step="7"]');
        if (step7Element) {
            step7Element.classList.add('active');
            
            // Show role-specific fields after delay
            setTimeout(() => {
                TalentFormRoles.toggleRoleSpecificFields(selectedRole);
            }, 100);
        }
    }
};

// ============================================================================
// VALIDATION MODULE
// ============================================================================

const TalentFormValidation = {
    /**
     * Validate a specific step
     */
    validateStep: function(stepNumber) {
        const currentStepEl = document.querySelector(
            `.form-step[data-step="${stepNumber}"]`
        );
        if (!currentStepEl) return true;
        
        const requiredFields = currentStepEl.querySelectorAll('[required]');
        let isValid = true;
        let firstInvalidField = null;
        
        for (const field of requiredFields) {
            field.classList.remove('error');
            let fieldValid = true;
            
            if (field.type === 'checkbox' || field.type === 'radio') {
                if (field.required) {
                    const name = field.name;
                    const checkedElement = currentStepEl.querySelector(`[name="${name}"]:checked`);
                    if (!checkedElement) {
                        fieldValid = false;
                        // Mark the entire group as error
                        const group = field.closest('.form-group, .radio-cards, .checkbox-grid, .radio-group');
                        if (group && !group.classList.contains('error')) {
                            group.classList.add('error');
                        }
                    }
                }
            } else if (!field.value.trim()) {
                fieldValid = false;
            }
            
            if (!fieldValid) {
                isValid = false;
                if (field.type !== 'checkbox' && field.type !== 'radio') {
                    field.classList.add('error');
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                }
            }
        }
        
        // Special validation for Step 5 (Domain selection)
        if (stepNumber === 5) {
            const selectedDomain = document.getElementById('selectedDomain')?.value;
            if (!selectedDomain) {
                isValid = false;
                const domainError = document.querySelector('.domain-error-message');
                if (domainError) {
                    domainError.style.display = 'block';
                }
                if (!firstInvalidField) {
                    firstInvalidField = document.querySelector('.domain-cards');
                }
            } else {
                const domainError = document.querySelector('.domain-error-message');
                if (domainError) {
                    domainError.style.display = 'none';
                }
            }
        }
        
        // Special validation for Step 6 (Role selection)
        if (stepNumber === 6) {
            const selectedRole = document.getElementById('selectedRole')?.value;
            if (!selectedRole) {
                isValid = false;
                const roleError = document.querySelector('.role-error-message');
                if (roleError) {
                    roleError.style.display = 'block';
                }
                if (!firstInvalidField) {
                    firstInvalidField = document.querySelector('.role-cards');
                }
            } else {
                const roleError = document.querySelector('.role-error-message');
                if (roleError) {
                    roleError.style.display = 'none';
                }
            }
        }
        
        if (!isValid) {
            // Scroll to first invalid field
            if (firstInvalidField) {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            alert('Please fill in all required fields before proceeding.');
        }
        
        return isValid;
    },
    
    /**
     * Check if a step is completed (has all required fields filled)
     */
    isStepCompleted: function(stepNumber) {
        const currentStepEl = document.querySelector(
            `.form-step[data-step="${stepNumber}"]`
        );
        if (!currentStepEl) return false;
        
        const requiredFields = currentStepEl.querySelectorAll('[required]');
        
        for (const field of requiredFields) {
            if (field.type === 'checkbox' || field.type === 'radio') {
                if (field.required) {
                    const name = field.name;
                    if (!currentStepEl.querySelector(`[name="${name}"]:checked`)) {
                        return false;
                    }
                }
            } else if (!field.value.trim()) {
                return false;
            }
        }
        
        // Special checks for domain and role steps
        if (stepNumber === 5) {
            const selectedDomain = document.getElementById('selectedDomain')?.value;
            return !!selectedDomain;
        }
        
        if (stepNumber === 6) {
            const selectedRole = document.getElementById('selectedRole')?.value;
            return !!selectedRole;
        }
        
        return true;
    }
};

// ============================================================================
// ROLE MANAGEMENT MODULE
// ============================================================================

const TalentFormRoles = {
    /**
     * Filter roles by selected domain
     */
    filterRolesByDomain: function(domain) {
        document.querySelectorAll('.role-category').forEach(category => {
            category.style.display = (domain === 'both' || category.dataset.domain === domain) 
                ? 'block' 
                : 'none';
        });
    },
    
    /**
     * Toggle role-specific fields visibility
     */
    toggleRoleSpecificFields: function(role) {
        if (!role) {
            // Hide all if no role provided
            document.querySelectorAll('.role-specific-fields').forEach(field => {
                field.style.setProperty('display', 'none', 'important');
                field.style.setProperty('opacity', '0', 'important');
                field.style.setProperty('visibility', 'hidden', 'important');
            });
            return;
        }
        
        // Hide all first
        document.querySelectorAll('.role-specific-fields').forEach(field => {
            field.style.setProperty('display', 'none', 'important');
            field.style.setProperty('opacity', '0', 'important');
            field.style.setProperty('visibility', 'hidden', 'important');
        });
        
        // Show selected role's fields
        const roleField = document.querySelector(`.role-specific-fields[data-role-specific="${role}"]`);
        if (roleField) {
            roleField.style.setProperty('display', 'block', 'important');
            roleField.style.setProperty('opacity', '1', 'important');
            roleField.style.setProperty('visibility', 'visible', 'important');
            
            // Update title
            const titleEl = document.getElementById('roleSpecificTitle');
            if (titleEl) {
                const roleName = TalentFormConfig.roleNames[role] || 
                    role.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                titleEl.textContent = `${roleName} Details`;
            }
            
            window.currentSelectedRole = role;
        }
    },
    
    /**
     * Update physical details visibility based on selected role
     */
    updatePhysicalDetailsVisibility: function() {
        const { physicalDetailsSection, selectedRole } = talentFormState.elements;
        if (!physicalDetailsSection || !selectedRole) return;
        
        const heading = physicalDetailsSection.previousElementSibling;
        const isHeading = heading && heading.tagName === 'H3';
        const selectedRoleValue = selectedRole.value;
        
        if (TalentFormConfig.rolesNeedingPhysicalDetails.includes(selectedRoleValue)) {
            physicalDetailsSection.style.display = 'grid';
            if (isHeading) heading.style.display = '';
        } else {
            physicalDetailsSection.style.display = 'none';
            if (isHeading) heading.style.display = 'none';
        }
    },
    
    /**
     * Initialize domain and role selection
     */
    initializeDomainRoleSelection: function() {
        // Domain selection
        document.querySelectorAll('.domain-card').forEach(card => {
            card.addEventListener('click', function() {
                const domain = this.dataset.domain;
                document.querySelectorAll('.domain-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                
                if (talentFormState.elements.selectedDomain) {
                    talentFormState.elements.selectedDomain.value = domain;
                }
                this.filterRolesByDomain(domain);
            });
        });
        
        // Role selection
        document.querySelectorAll('.role-card').forEach(card => {
            card.addEventListener('click', function() {
                const role = this.dataset.roleSpecific;
                document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                
                if (talentFormState.elements.selectedRole) {
                    talentFormState.elements.selectedRole.value = role;
                }
                
                // Update role-specific fields
                this.toggleRoleSpecificFields(role);
                this.updatePhysicalDetailsVisibility();
            });
        });
    }
};

// ============================================================================
// STORAGE MODULE
// ============================================================================

const TalentFormStorage = {
    /**
     * Save form state to localStorage
     */
    saveToLocalStorage: function() {
        const { form } = talentFormState;
        if (!form) return;
        
        try {
            const formData = new FormData(form);
            const data = {};
            
            for (let [key, value] of formData.entries()) {
                // Skip file inputs
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
            
            localStorage.setItem(TalentFormConfig.storageKeys.formData, JSON.stringify(data));
            console.log('Form state saved to localStorage');
        } catch (error) {
            console.error('Error saving to localStorage:', error);
        }
    },
    
    /**
     * Load form state from localStorage
     */
    loadFromLocalStorage: function() {
        const { form, elements } = talentFormState;
        if (!form) return;
        
        // Don't load if server-side draft exists
        if (typeof talentData !== 'undefined' && talentData.draft) {
            return;
        }
        
        const savedData = localStorage.getItem(TalentFormConfig.storageKeys.formData);
        if (!savedData) return;
        
        const data = JSON.parse(savedData);
        
        // Restore form fields
        for (const key in data) {
            // Skip repeater fields
            if (['workTitle', 'workRole', 'workStartDate', 'workEndDate', 'workDescription', 'workPresent'].includes(key)) {
                continue;
            }
            
            const value = data[key];
            const elements = form.querySelectorAll(`[name="${key}"], [name="${key}[]"]`);
            
            if (elements.length > 0) {
                const el = elements[0];
                
                if (el.type === 'radio') {
                    const radioToSelect = form.querySelector(`input[name="${key}"][value="${value}"]`);
                    if (radioToSelect) {
                        radioToSelect.checked = true;
                        const card = radioToSelect.closest('.radio-card');
                        if (card) {
                            card.parentElement.querySelectorAll('.radio-card').forEach(c => c.classList.remove('selected'));
                            card.classList.add('selected');
                        }
                    }
                } else if (el.type === 'checkbox') {
                    if (elements.length === 1) {
                        el.checked = value === 'on' || value === true;
                    } else {
                        elements.forEach(checkbox => {
                            if (Array.isArray(value) && value.includes(checkbox.value)) {
                                checkbox.checked = true;
                            }
                        });
                    }
                } else if (el.type !== 'file') {
                    el.value = value;
                }
                
                // Special handling for UI
                if (key === 'domain') {
                    document.querySelectorAll('.domain-card').forEach(c => c.classList.remove('selected'));
                    const selectedCard = document.querySelector(`.domain-card[data-domain="${value}"]`);
                    if (selectedCard) selectedCard.classList.add('selected');
                    TalentFormRoles.filterRolesByDomain(value);
                }
                
                if (key === 'role') {
                    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
                    const selectedCard = document.querySelector(`.role-card[data-role-specific="${value}"]`);
                    if (selectedCard) selectedCard.classList.add('selected');
                    TalentFormRoles.toggleRoleSpecificFields(value);
                }
                
                // Trigger change events
                if (['willingToTravel', 'selectedDomain', 'selectedRole', 'brandCollabs'].includes(el.id)) {
                    el.dispatchEvent(new Event('change'));
                }
            }
        }
        
        console.log('Form state loaded from localStorage');
    },
    
    /**
     * Clear localStorage
     */
    clearLocalStorage: function() {
        localStorage.removeItem(TalentFormConfig.storageKeys.currentStep);
        localStorage.removeItem(TalentFormConfig.storageKeys.formData);
    }
};

// ============================================================================
// SUBMISSION MODULE
// ============================================================================

const TalentFormSubmission = {
    /**
     * Save progress to server
     */
    saveProgress: function() {
        const { form } = talentFormState;
        if (!form || typeof talentData === 'undefined') return;
        
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        params.append('action', 'save_talent_progress');
        params.append('nonce', talentData.nonce);
        params.append('current_step', talentFormState.currentStep);
        
        const postIdInput = form.querySelector('input[name="post_id"]');
        if (postIdInput && postIdInput.value) {
            params.append('post_id', postIdInput.value);
        }
        
        const formDataString = new URLSearchParams(formData).toString();
        params.append('form_data', formDataString);
        
        // Show saving indicator
        if (typeof showSuccessPopup === 'function') {
            showSuccessPopup('Saving progress...', 'info');
        }
        
        fetch(talentData.ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.post_id) {
                let postIdInput = form.querySelector('input[name="post_id"]');
                if (!postIdInput) {
                    postIdInput = document.createElement('input');
                    postIdInput.type = 'hidden';
                    postIdInput.name = 'post_id';
                    form.appendChild(postIdInput);
                }
                postIdInput.value = data.data.post_id;
                
                if (typeof showSuccessPopup === 'function') {
                    showSuccessPopup('Progress saved successfully!');
                }
            } else {
                if (typeof showSuccessPopup === 'function') {
                    showSuccessPopup('Error saving progress. Please try again.', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error saving progress:', error);
            if (typeof showSuccessPopup === 'function') {
                showSuccessPopup('Error saving progress. Please try again.', 'error');
            }
        });
    },
    
    /**
     * Prepare form for submission
     */
    prepareFormSubmission: function() {
        const { form } = talentFormState;
        if (!form) return;
        
        // Handle languages field
        const languagesInput = document.getElementById('languages');
        if (languagesInput && languagesInput.value) {
            const languagesArray = languagesInput.value.split(',').map(lang => lang.trim()).filter(lang => lang.length > 0);
            
            languagesArray.forEach(function(lang, index) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'languages[]';
                hiddenInput.value = lang;
                form.appendChild(hiddenInput);
            });
            
            languagesInput.name = '';
        }
        
        // Set profile photo from portfolio if not set
        this.setProfilePhotoFromPortfolio();
        
        // Clear localStorage
        TalentFormStorage.clearLocalStorage();
        
        // Disable submit button
        const { submitBtn } = talentFormState.elements;
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
        }
    },
    
    /**
     * Set profile photo from first portfolio image if not set
     */
    setProfilePhotoFromPortfolio: function() {
        const profilePhotoInput = document.getElementById('profilePhoto');
        if (profilePhotoInput && profilePhotoInput.files && profilePhotoInput.files.length > 0) {
            return; // Profile photo already selected
        }
        
        const portfolioConfigs = [
            { inputId: 'portfolio' },
            { inputId: 'portfolio-textile-designer' },
            { inputId: 'portfolio-accessory-designer' },
            { inputId: 'portfolio-fashion-illustrator' },
            { inputId: 'portfolio-fashion-model' },
            { inputId: 'portfolio-ramp-choreographer' },
            { inputId: 'portfolio-fashion-stylist' },
            { inputId: 'portfolio-makeup-artist' },
            { inputId: 'portfolio-director' },
            { inputId: 'portfolio-assistant-director' },
            { inputId: 'portfolio-screenwriter' },
            { inputId: 'portfolio-storyboard-artist' },
            { inputId: 'portfolio-actor' },
            { inputId: 'portfolio-voice-actor' },
            { inputId: 'portfolio-dancer' },
            { inputId: 'portfolio-dop' },
            { inputId: 'portfolio-editor' },
            { inputId: 'portfolio-fashion-designer' }
        ];
        
        for (const config of portfolioConfigs) {
            const portfolioInput = document.getElementById(config.inputId);
            if (portfolioInput && portfolioInput.files && portfolioInput.files.length > 0) {
                const firstPortfolioFile = portfolioInput.files[0];
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(firstPortfolioFile);
                profilePhotoInput.files = dataTransfer.files;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photoPreview').innerHTML = 
                        `<img src="${e.target.result}" alt="Profile Preview" style="max-width: 100%; max-height: 100%;">`;
                };
                reader.readAsDataURL(firstPortfolioFile);
                
                console.log('Profile photo set from first portfolio image');
                break;
            }
        }
    }
};

// ============================================================================
// INITIALIZATION
// ============================================================================

const TalentFormApp = {
    /**
     * Initialize the form application
     */
    init: function() {
        // Check if we're on the talent submission page
        if (!talentFormState.initialize()) {
            console.log('Not on talent submission page');
            return;
        }
        
        console.log('Initializing Talent Form App...');
        
        // Initialize modules
        this.initNavigation();
        this.initEventListeners();
        this.initFormFeatures();
        this.initDraftLoading();
        
        // Load saved step
        const savedStep = localStorage.getItem(TalentFormConfig.storageKeys.currentStep);
        const initialStep = savedStep ? parseInt(savedStep, 10) : 1;
        
        TalentFormNavigation.showStep(initialStep);
        TalentFormStorage.loadFromLocalStorage();
        
        console.log('Talent Form App initialized');
    },
    
    /**
     * Initialize navigation
     */
    initNavigation: function() {
        const { elements } = talentFormState;
        
        // Next button
        elements.nextBtn?.addEventListener('click', () => {
            if (TalentFormValidation.validateStep(talentFormState.currentStep)) {
                TalentFormNavigation.nextStep();
            }
        });
        
        // Previous button
        elements.prevBtn?.addEventListener('click', () => {
            TalentFormNavigation.prevStep();
        });
        
        // Clickable sidebar steps with validation
        document.querySelectorAll(TalentFormConfig.selectors.progressSidebar).forEach(stepEl => {
            stepEl.addEventListener('click', function() {
                const targetStep = parseInt(this.dataset.step, 10);
                const currentStep = talentFormState.currentStep;
                
                // Allow clicking on current or previous completed steps
                if (targetStep <= currentStep) {
                    TalentFormNavigation.showStep(targetStep);
                    return;
                }
                
                // For forward navigation, validate all intermediate steps
                let canNavigate = true;
                for (let step = currentStep; step < targetStep; step++) {
                    if (!TalentFormValidation.isStepCompleted(step)) {
                        canNavigate = false;
                        // Show notification about incomplete step
                        alert(`Please complete Step ${step} before skipping to Step ${targetStep}.`);
                        break;
                    }
                }
                
                // If all intermediate steps are complete, validate current step and navigate
                if (canNavigate && TalentFormValidation.validateStep(currentStep)) {
                    TalentFormNavigation.showStep(targetStep);
                }
            });
        });
        
        // Add pointer cursor style and completed step styling
        const style = document.createElement('style');
        style.innerHTML = `
            .progress-sidebar .step { cursor: pointer; }
            .progress-sidebar .step.completed .step-number {
                background: linear-gradient(135deg, #b2122d 0%, #df1d3d 100%);
                color: #fff;
            }
        `;
        document.head.appendChild(style);
    },
    
    /**
     * Initialize event listeners
     */
    initEventListeners: function() {
        const { form, elements } = talentFormState;
        
        // Save progress button
        elements.saveProgressBtn?.addEventListener('click', function(e) {
            e.preventDefault();
            TalentFormStorage.saveToLocalStorage();
            TalentFormSubmission.saveProgress();
        });
        
        // Form submission
        form?.addEventListener('submit', function(e) {
            TalentFormSubmission.prepareFormSubmission();
        });
        
        // Auto-save on input
        form?.addEventListener('input', () => {
            TalentFormStorage.saveToLocalStorage();
        });
        
        // Conditional fields - travel
        document.getElementById('willingToTravel')?.addEventListener('change', function() {
            const section = document.getElementById('preferredLocationsSection');
            if (section) {
                section.style.display = this.checked ? 'block' : 'none';
            }
        });
        
        // Initialize domain/role selection
        TalentFormRoles.initializeDomainRoleSelection();
    },
    
    /**
     * Initialize form features
     */
    initFormFeatures: function() {
        // Profile photo preview
        document.getElementById('profilePhoto')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('photoPreview').innerHTML = 
                        `<img src="${event.target.result}" alt="Profile Preview" style="max-width: 100%; max-height: 100%;">`;
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Character counters
        document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
            const counterId = textarea.id + 'Count';
            const counter = document.getElementById(counterId);
            if (counter) {
                const updateCount = () => counter.textContent = textarea.value.length;
                textarea.addEventListener('input', updateCount);
                updateCount();
            }
        });
        
        // Notable works repeater
        this.initNotableWorksRepeater();
        
        // Language multi-select
        this.initLanguageMultiSelect();
        
        // Image protection
        this.initImageProtection();
    },
    
    /**
     * Initialize notable works repeater
     */
    initNotableWorksRepeater: function() {
        const container = document.querySelector(TalentFormConfig.selectors.notableWorksContainer);
        if (!container) return;
        
        document.getElementById('addWorkBtn')?.addEventListener('click', () => {
            if (container.children.length >= TalentFormConfig.maxNotableWorks) {
                alert(`You can add a maximum of ${TalentFormConfig.maxNotableWorks} works.`);
                return;
            }
            const firstWorkItem = container.querySelector('.notable-work-item');
            const newWorkItem = firstWorkItem.cloneNode(true);
            newWorkItem.querySelectorAll('input, textarea').forEach(input => input.value = '');
            newWorkItem.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            newWorkItem.querySelector('.remove-work-btn').style.display = 'inline-block';
            container.appendChild(newWorkItem);
        });
        
        container.addEventListener('click', function(e) {
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
    },
    
    /**
     * Initialize language multi-select
     */
    initLanguageMultiSelect: function() {
        const selectBox = document.getElementById('languageSelectBox');
        if (!selectBox) return;
        
        const selectHeader = selectBox.querySelector('.select-box-header');
        const optionsContainer = selectBox.querySelector('.select-box-options');
        const placeholder = selectHeader.querySelector('.placeholder');
        
        selectHeader.addEventListener('click', function() {
            selectBox.classList.toggle('active');
        });
        
        const checkboxes = optionsContainer.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const selectedOptions = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.nextElementSibling.textContent);
                
                if (selectedOptions.length === 0) {
                    placeholder.textContent = 'Select languages';
                } else if (selectedOptions.length === 1) {
                    placeholder.textContent = selectedOptions[0];
                } else {
                    placeholder.textContent = `${selectedOptions.length} languages selected`;
                }
            });
        });
        
        document.addEventListener('click', function(e) {
            if (!selectBox.contains(e.target)) {
                selectBox.classList.remove('active');
            }
        });
    },
    
    /**
     * Initialize image protection
     */
    initImageProtection: function() {
        document.addEventListener('contextmenu', function(e) {
            if (e.target.tagName === 'IMG') {
                e.preventDefault();
            }
        });
        
        document.addEventListener('dragstart', function(e) {
            if (e.target.tagName === 'IMG') {
                e.preventDefault();
            }
        });
        
        document.addEventListener('selectstart', function(e) {
            if (e.target.tagName === 'IMG') {
                e.preventDefault();
            }
        });
    },
    
    /**
     * Initialize draft loading
     */
    initDraftLoading: function() {
        if (typeof talentData !== 'undefined' && talentData.draft) {
            const draft = talentData.draft;
            
            if (draft.currentStep) {
                const stepNumber = parseInt(draft.currentStep);
                if (!isNaN(stepNumber) && stepNumber >= 1 && stepNumber <= talentFormState.totalSteps) {
                    TalentFormNavigation.showStep(stepNumber);
                }
            }
            
            // Populate form fields from draft...
            // (This would mirror the existing code in talent--submission.js)
        }
    }
};

// ============================================================================
// START APPLICATION
// ============================================================================

document.addEventListener('DOMContentLoaded', function() {
    TalentFormApp.init();
});

// Make functions globally available for backward compatibility
window.toggleRoleSpecificFields = function(role) {
    TalentFormRoles.toggleRoleSpecificFields(role);
};

window.showSuccessPopup = window.showSuccessPopup || function(message, type) {
    console.log(`Popup: ${type} - ${message}`);
};
