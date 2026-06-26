<?php
/**
 * Progress Sidebar Template Part
 * 
 * Displays progress steps and progress bar
 * 
 * @package HelloElementorChild
 */
?>

<div class="progress-sidebar">
    <?php
    // Include header section
    get_template_part('templates/talent-submission/header-section');
    ?>
    
    <div class="progress-steps">
        <div class="step active" data-step="1">
            <div class="step-number">1</div>
            <div class="step-info">
                <div class="step-title">Profile Basics</div>
                <div class="step-subtitle">Personal information</div>
            </div>
        </div>
        <div class="step" data-step="2">
            <div class="step-number">2</div>
            <div class="step-info">
                <div class="step-title">Experience</div>
                <div class="step-subtitle">Your background</div>
            </div>
        </div>
        <div class="step" data-step="3">
            <div class="step-number">3</div>
            <div class="step-info">
                <div class="step-title">Portfolio</div>
                <div class="step-subtitle">Showcase work</div>
            </div>
        </div>
        <div class="step" data-step="4">
            <div class="step-number">4</div>
            <div class="step-info">
                <div class="step-title">Availability</div>
                <div class="step-subtitle">Preferences</div>
            </div>
        </div>
        <div class="step" data-step="5">
            <div class="step-number">5</div>
            <div class="step-info">
                <div class="step-title">Domain</div>
                <div class="step-subtitle">Your field</div>
            </div>
        </div>
        <div class="step" data-step="6">
            <div class="step-number">6</div>
            <div class="step-info">
                <div class="step-title">Role</div>
                <div class="step-subtitle">Specialization</div>
            </div>
        </div>
        <div class="step" data-step="7">
            <div class="step-number">7</div>
            <div class="step-info">
                <div class="step-title">Details</div>
                <div class="step-subtitle">Role-specific</div>
            </div>
        </div>
        <div class="step" data-step="8">
            <div class="step-number">8</div>
            <div class="step-info">
                <div class="step-title">Preview</div>
                <div class="step-subtitle">Review & publish</div>
            </div>
        </div>
    </div>
    <div class="progress-bar-container">
        <div class="progress-percentage">0% Completed</div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: 0%"></div>
        </div>
    </div>
</div>
