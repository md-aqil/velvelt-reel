<?php
/*
Template Name: Test Scripts
*/

get_header();
?>

<div class="test-scripts-container">
    <h1>Test Scripts Page</h1>
    <p>This page is used to test if global.js and other scripts are loading correctly.</p>
    
    <div class="test-section">
        <h2>Console Tests</h2>
        <p>Open the browser console to see test results.</p>
        <button id="test-notification">Test Notification</button>
        <button id="test-ajax">Test AJAX Function</button>
    </div>
    
    <div class="test-section">
        <h2>File Upload Test</h2>
        <div class="file-upload-area">
            <p>Drag & drop files here or click to select</p>
            <input type="file" multiple>
            <div class="file-upload-preview"></div>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Form Test</h2>
        <form data-confirm="Are you sure you want to submit this test form?">
            <button type="submit">Test Form Confirmation</button>
        </form>
    </div>
    
    <div class="test-section">
        <h2>Modal Test</h2>
        <button data-modal-trigger="test-modal">Open Test Modal</button>
        
        <div class="modal" id="test-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Test Modal</h3>
                    <button class="modal-close" data-modal-close>&times;</button>
                </div>
                <div class="modal-body">
                    <p>This is a test modal to verify the global modal functionality.</p>
                </div>
                <div class="modal-footer">
                    <button data-modal-close>Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.test-scripts-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: Arial, sans-serif;
}

.test-section {
    margin: 30px 0;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f9f9f9;
}

button {
    padding: 10px 15px;
    margin: 5px;
    background: #0073aa;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background: #005a87;
}

.file-upload-area {
    border: 2px dashed #ccc;
    padding: 20px;
    text-align: center;
    background: white;
    border-radius: 4px;
}

.file-upload-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.file-preview-item {
    width: 100px;
    text-align: center;
}

.file-preview-item img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 4px;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
}

.modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    text-align: right;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Test scripts page loaded');
    
    // Test notification function
    document.getElementById('test-notification').addEventListener('click', function() {
        if (typeof showNotification === 'function') {
            showNotification('Global JS is working correctly!', 'success');
        } else {
            console.log('showNotification function not found');
        }
    });
    
    // Test AJAX function
    document.getElementById('test-ajax').addEventListener('click', function() {
        if (typeof ajaxRequest === 'function') {
            console.log('ajaxRequest function is available');
        } else {
            console.log('ajaxRequest function not found');
        }
    });
});
</script>

<?php get_footer(); ?>