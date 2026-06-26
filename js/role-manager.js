/**
 * Role Manager - Single source of truth for role selection
 * This replaces the multiple conflicting role handling systems
 */

class RoleManager {
    constructor() {
        this.selectedRole = null;
        this.roleData = {};
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadRoleData();
    }

    setupEventListeners() {
        // Handle role card selection
        document.addEventListener('click', (e) => {
            const roleCard = e.target.closest('.role-card');
            if (roleCard) {
                e.preventDefault();
                this.selectRole(roleCard);
            }
        });

        // Handle domain filtering
        document.addEventListener('click', (e) => {
            const domainCard = e.target.closest('.domain-card');
            if (domainCard) {
                this.filterByDomain(domainCard.dataset.domain);
            }
        });

        // Handle search
        const searchInput = document.getElementById('roleSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchRoles(e.target.value);
            });
        }
    }

    loadRoleData() {
        // In a real implementation, this would fetch from server
        // For now, we'll use the existing role mapping
        this.roleData = {
            'fashion-designer': {
                name: 'Fashion Designer',
                categories: ['Menswear', 'Womenswear', 'Kidswear', 'Unisex']
            },
            'textile-designer': {
                name: 'Textile Designer', 
                categories: ['Print Design', 'Surface Design', 'Digital Printing', 'Handloom']
            },
            'accessory-designer': {
                name: 'Accessory Designer',
                categories: ['Bags', 'Shoes', 'Jewelry', 'Belts']
            },
            'fashion-illustrator': {
                name: 'Fashion Illustrator',
                categories: ['Sketching', 'Digital Illustration', 'Concept Art', 'Technical Drawings']
            },
            'fashion-model': {
                name: 'Fashion Model',
                categories: ['Runway', 'Editorial', 'Commercial', 'Catalog']
            },
            'ramp-choreographer': {
                name: 'Ramp Choreographer',
                categories: ['Runway Choreography', 'Show Direction', 'Staging', 'Performance']
            },
            'fashion-stylist': {
                name: 'Fashion Stylist',
                categories: ['Editorial Styling', 'Personal Styling', 'Commercial Styling', 'Wardrobe']
            },
            'makeup-artist': {
                name: 'Makeup Artist',
                categories: ['Bridal Makeup', 'Film Makeup', 'Editorial Makeup', 'Special Effects']
            },
            'director': {
                name: 'Director',
                categories: ['Feature Films', 'Short Films', 'Commercials', 'Music Videos']
            },
            'assistant-director': {
                name: 'Assistant Director',
                categories: ['1st AD', '2nd AD', 'Production Coordination', 'Script Supervision']
            },
            'screenwriter': {
                name: 'Screenwriter',
                categories: ['Feature Scripts', 'TV Scripts', 'Short Scripts', 'Documentary']
            },
            'storyboard-artist': {
                name: 'Storyboard Artist',
                categories: ['Visual Scripting', 'Scene Planning', 'Shot Composition', 'Pre-visualization']
            },
            'actor': {
                name: 'Actor/Actress',
                categories: ['Film', 'Television', 'Theatre', 'Web Series']
            },
            'voice-actor': {
                name: 'Voice Actor',
                categories: ['Dubbing', 'Narration', 'Animation', 'Audiobooks']
            },
            'dancer': {
                name: 'Dancer/Choreographer',
                categories: ['Bollywood', 'Contemporary', 'Classical', 'Freestyle']
            },
            'dop': {
                name: 'DOP/Camera Crew',
                categories: ['Cinematography', 'Camera Operation', 'Lighting', 'Drone Operation']
            },
            'editor': {
                name: 'Editor/VFX Artist',
                categories: ['Video Editing', 'Color Grading', 'Visual Effects', 'Motion Graphics']
            },
            'runway-coach': {
                name: 'Runway Coach',
                categories: ['Walk Training', 'Posture Correction', 'Confidence Building', 'Stage Presence']
            },
            'model-development-coach': {
                name: 'Model Development Coach',
                categories: ['Portfolio Development', 'Industry Guidance', 'Career Planning', 'Professional Skills']
            },
            'acting-coach': {
                name: 'Acting Coach',
                categories: ['Method Acting', 'Character Development', 'Voice Training', 'Scene Study']
            },
            'voice-diction-coach': {
                name: 'Voice and Diction Coach',
                categories: ['Voice Modulation', 'Diction Training', 'Accent Reduction', 'Public Speaking']
            },
            'singer': {
                name: 'Singer',
                categories: ['Playback Singing', 'Live Performance', 'Classical Singing', 'Jingle Singing']
            },
            'music-director': {
                name: 'Music Director',
                categories: ['Orchestration', 'Sound Design', 'Jingle Creation', 'Background Score']
            }
        };
    }

    selectRole(roleCard) {
        const role = roleCard.dataset.roleSpecific;
        
        // Update UI state
        document.querySelectorAll('.role-card').forEach(card => {
            card.classList.remove('selected');
        });
        roleCard.classList.add('selected');
        
        // Update hidden input
        const selectedRoleInput = document.getElementById('selectedRole');
        if (selectedRoleInput) {
            selectedRoleInput.value = role;
        }
        
        // Store selected role
        this.selectedRole = role;
        
        // Show role-specific fields
        this.showRoleSpecificFields(role);
        
        // Update title
        this.updateTitle(role);
        
        console.log('Role selected:', role);
    }

    showRoleSpecificFields(role) {
        // Hide all role-specific containers
        document.querySelectorAll('.role-specific-fields').forEach(container => {
            container.style.display = 'none';
        });
        
        // Show the specific role container
        const roleContainer = document.querySelector(`.role-specific-fields[data-role-specific="${role}"]`);
        if (roleContainer) {
            roleContainer.style.display = 'block';
            console.log('Showing role-specific fields for:', role);
        } else {
            console.warn('No role-specific fields found for:', role);
            // Create dynamic fields if container doesn't exist
            this.createDynamicFields(role);
        }
    }

    createDynamicFields(role) {
        const roleInfo = this.roleData[role];
        if (!roleInfo) return;

        const container = document.getElementById('roleSpecificQuestions');
        if (!container) return;

        // Clear existing content
        container.innerHTML = '';

        // Create title
        const title = document.createElement('h3');
        title.textContent = `${roleInfo.name} Specialization`;
        title.style.marginBottom = '20px';
        container.appendChild(title);

        // Create categories section
        if (roleInfo.categories && roleInfo.categories.length > 0) {
            const categoriesSection = document.createElement('div');
            categoriesSection.className = 'form-group full-width';
            
            const label = document.createElement('label');
            label.textContent = `${roleInfo.name} Categories`;
            categoriesSection.appendChild(label);

            const grid = document.createElement('div');
            grid.className = 'checkbox-grid';

            roleInfo.categories.forEach(category => {
                const labelElement = document.createElement('label');
                labelElement.className = 'checkbox-label';
                
                const input = document.createElement('input');
                input.type = 'checkbox';
                input.name = 'designCategories[]';
                input.value = category;
                
                const indicator = document.createElement('span');
                indicator.className = 'control-indicator';
                
                const text = document.createElement('span');
                text.textContent = category;
                
                labelElement.appendChild(input);
                labelElement.appendChild(indicator);
                labelElement.appendChild(text);
                grid.appendChild(labelElement);
            });

            categoriesSection.appendChild(grid);
            container.appendChild(categoriesSection);
        }

        // Add portfolio gallery
        this.addPortfolioGallery(container, role, roleInfo.name);
        
        console.log('Dynamic fields created for:', role);
    }

    addPortfolioGallery(container, role, roleName) {
        const gallerySection = document.createElement('div');
        gallerySection.className = 'form-group full-width';
        
        const label = document.createElement('label');
        label.textContent = `${roleName} Portfolio`;
        gallerySection.appendChild(label);

        const instructions = document.createElement('div');
        instructions.className = 'file-upload-instructions';
        instructions.innerHTML = `
            <p>Upload images of your work (max 10 images, 5MB each)</p>
            <p>Supported formats: JPG, PNG, GIF</p>
        `;
        gallerySection.appendChild(instructions);

        const uploadArea = document.createElement('div');
        uploadArea.className = 'file-upload-area';
        uploadArea.innerHTML = `
            <input type="file" id="portfolio_${role}" name="portfolio[]" accept="image/*,video/*" multiple>
            <div class="file-upload-placeholder">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Click or drag files to upload (images or videos)</p>
            </div>
        `;
        gallerySection.appendChild(uploadArea);

        container.appendChild(gallerySection);

        // Add event listener for file upload
        this.setupFileUpload(uploadArea);
    }

    setupFileUpload(uploadArea) {
        const fileInput = uploadArea.querySelector('input[type="file"]');
        const placeholder = uploadArea.querySelector('.file-upload-placeholder');
        
        if (fileInput && placeholder) {
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    placeholder.style.display = 'none';
                } else {
                    placeholder.style.display = 'block';
                }
            });
        }
    }

    filterByDomain(domain) {
        document.querySelectorAll('.role-category').forEach(category => {
            const categoryDomain = category.dataset.domain;
            if (domain === 'both' || categoryDomain === domain) {
                category.style.display = 'block';
            } else {
                category.style.display = 'none';
            }
        });
        
        // Clear selection when domain changes
        this.clearSelection();
    }

    searchRoles(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        
        document.querySelectorAll('.role-card').forEach(card => {
            const text = card.textContent.toLowerCase();
            if (term === '' || text.includes(term)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    updateTitle(role) {
        const titleElement = document.getElementById('roleSpecificTitle');
        if (titleElement && this.roleData[role]) {
            titleElement.textContent = `${this.roleData[role].name} Details`;
        }
    }

    clearSelection() {
        document.querySelectorAll('.role-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        const selectedRoleInput = document.getElementById('selectedRole');
        if (selectedRoleInput) {
            selectedRoleInput.value = '';
        }
        
        this.selectedRole = null;
        
        // Hide all role-specific fields
        document.querySelectorAll('.role-specific-fields').forEach(container => {
            container.style.display = 'none';
        });
        
        // Clear dynamic fields
        const container = document.getElementById('roleSpecificQuestions');
        if (container) {
            container.innerHTML = '';
        }
    }

    // Public methods for external access
    getSelectedRole() {
        return this.selectedRole;
    }

    getRoleData(role) {
        return this.roleData[role] || null;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.roleManager = new RoleManager();
    console.log('Role Manager initialized');
});

// Export for global access
if (typeof window !== 'undefined') {
    window.RoleManager = RoleManager;
}