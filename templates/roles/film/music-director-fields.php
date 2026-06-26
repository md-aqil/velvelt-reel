

<?php
// Set variables for portfolio gallery component
$role_key = 'music-director';
$role_name = 'Music Director';
?>

<div class="role-specific-fields" data-role-specific="music-director" hidden>
    <div class="form-group full-width">
        <label for="musicDirectorExperience">Years of Experience as Music Director</label>
        <select id="musicDirectorExperience" name="musicDirectorExperience">
            <option value="">Select Years of Experience</option>
            <option value="Less than 1 year">Less than 1 year</option>
            <option value="1-3 years">1-3 years</option>
            <option value="3-5 years">3-5 years</option>
            <option value="5-10 years">5-10 years</option>
            <option value="10-15 years">10-15 years</option>
            <option value="15+ years">15+ years</option>
        </select>
    </div>

    <div class="form-group full-width">
        <label for="musicInstruments">Primary Instruments</label>
        <input type="text" id="musicInstruments" name="musicInstruments" placeholder="Piano, Guitar, Violin, etc." value="">
    </div>

    <div class="form-group full-width">
        <label for="musicGenres">Music Genres Specialized In</label>
        <input type="text" id="musicGenres" name="musicGenres" placeholder="Classical, Jazz, Pop, etc." value="">
    </div>

    <div class="form-group full-width">
        <label for="musicNotableProjects">Notable Music Projects</label>
        <textarea id="musicNotableProjects" name="musicNotableProjects" rows="3" maxlength="500" placeholder="List notable music projects you've worked on"></textarea>
    </div>

    <div class="form-group full-width">
        <label for="musicAwardsRecognition">Awards & Recognition</label>
        <input type="text" id="musicAwardsRecognition" name="musicAwardsRecognition" placeholder="Awards, nominations, recognitions" value="">
    </div>

    <div class="form-group full-width">
        <label>Specializations</label>
        <div class="checkbox-grid">
            <label class="checkbox-label">
                <input type="checkbox" name="musicSpecializations[]" value="Orchestration"><span class="control-indicator"></span>
                <span>Orchestration</span>
            </label>
            <label class="checkbox-label">
                <input type="checkbox" name="musicSpecializations[]" value="Sound Design"><span class="control-indicator"></span>
                <span>Sound Design</span>
            </label>
            <label class="checkbox-label">
                <input type="checkbox" name="musicSpecializations[]" value="Jingle Creation"><span class="control-indicator"></span>
                <span>Jingle Creation</span>
            </label>
            <label class="checkbox-label">
                <input type="checkbox" name="musicSpecializations[]" value="Background Score"><span class="control-indicator"></span>
                <span>Background Score</span>
            </label>
        </div>
    </div>
</div>
