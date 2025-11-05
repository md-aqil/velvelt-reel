<?php
// Test script to check if global.js is being loaded
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Script Loading</title>
    <?php wp_head(); ?>
</head>
<body>
    <h1>Test Script Loading</h1>
    <p>Check the browser developer tools to see if global.js is loaded.</p>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            
            // Check if our global functions exist
            if (typeof initializeGlobalScripts === 'function') {
                console.log('✓ initializeGlobalScripts function exists');
            } else {
                console.log('✗ initializeGlobalScripts function NOT found');
            }
            
            if (typeof showNotification === 'function') {
                console.log('✓ showNotification function exists');
                // Test the notification
                showNotification('Global JS is working!', 'success');
            } else {
                console.log('✗ showNotification function NOT found');
            }
        });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>