

<?php
$role_key = 'singer';
$role_name = 'Singer';
?>

<div class="role-specific-fields" data-role-specific="singer" hidden>
    <div class="form-group full-width">
        <label for="singerExperience">Years of Experience as Singer</label>
        <select id="singerExperience" name="singerExperience">
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
        <label for="vocalRange">Vocal Range</label>
        <select id="vocalRange" name="vocalRange">
            <option value="">Select Vocal Range</option>
            <option value="Soprano">Soprano</option>
            <option value="Mezzo-soprano">Mezzo-soprano</option>
            <option value="Alto">Alto</option>
            <option value="Tenor">Tenor</option>
            <option value="Baritone">Baritone</option>
            <option value="Bass">Bass</option>
            <option value="Other">Other</option>
        </select>
    </div>

    <div class="form-group full-width">
        <label for="singerGenres">Genres Specialized In</label>
        <input type="text" id="singerGenres" name="singerGenres" placeholder="Pop, Classical, Rock, Folk, etc." value="">
    </div>

    <div class="form-group full-width">
        <label for="singerNotableSongs">Notable Songs/Recordings</label>
        <textarea id="singerNotableSongs" name="singerNotableSongs" rows="3" maxlength="500" placeholder="List notable songs or recordings you've performed"></textarea>
    </div>

    <div class="form-group full-width">
        <label for="singerAwardsRecognition">Awards & Recognition</label>
        <input type="text" id="singerAwardsRecognition" name="singerAwardsRecognition" placeholder="Awards, nominations, recognitions" value="">
    </div>

    <div class="form-group full-width">
        <label>Singing Types</label>
        <div class="checkbox-grid">
            <label class="checkbox-label">
                <input type="checkbox" name="singerTypes[]" value="Film/Folk"><span class="control-indicator"></span>
                <span>Film/Folk</span>
            </label>
            <label class="checkbox-label">
                <input type="checkbox" name="singerTypes[]" value="Carnatic/Hindustani"><span class="control-indicator"></span>
                <span>Carnatic/Hindustani</span>
            </label>
            <label class="checkbox-label">
                <input type="checkbox" name="singerTypes[]" value="Light Music/Devotional"><span class="control-indicator"></span>
                <span>Light Music/Devotional</span>
            </label>
            <label class="checkbox-label">
                <input type="checkbox" name="singerTypes[]" value="Western/Pop / Indie/Rap/Fusion"><span class="control-indicator"></span>
                <span>Western/Pop / Indie/Rap/Fusion</span>
            </label>
            
        </div>
    </div>
</div>
