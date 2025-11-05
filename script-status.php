<?php
// Script to check the status of JavaScript files
echo "<h1>JavaScript File Status</h1>";

// Check if global.js exists
$global_js = __DIR__ . '/global.js';
if (file_exists($global_js)) {
    echo "<p style='color: green;'>✓ global.js exists (" . filesize($global_js) . " bytes)</p>";
} else {
    echo "<p style='color: red;'>✗ global.js does not exist</p>";
}

// Check if talent--submission.js exists
$talent_js = __DIR__ . '/talent--submission.js';
if (file_exists($talent_js)) {
    echo "<p style='color: green;'>✓ talent--submission.js exists (" . filesize($talent_js) . " bytes)</p>";
} else {
    echo "<p style='color: red;'>✗ talent--submission.js does not exist</p>";
}

// Check if functions.php has the enqueue code
$functions_php = __DIR__ . '/functions.php';
if (file_exists($functions_php)) {
    $content = file_get_contents($functions_php);
    if (strpos($content, 'theme-global-js') !== false) {
        echo "<p style='color: green;'>✓ functions.php contains global.js enqueue code</p>";
    } else {
        echo "<p style='color: red;'>✗ functions.php does not contain global.js enqueue code</p>";
    }
    
    if (strpos($content, 'get_stylesheet_directory_uri() . \'/global.js\'') !== false) {
        echo "<p style='color: green;'>✓ functions.php has correct path for global.js</p>";
    } else {
        echo "<p style='color: red;'>✗ functions.php does not have correct path for global.js</p>";
    }
}

echo "<h2>Directory Contents</h2>";
$files = scandir(__DIR__);
echo "<ul>";
foreach ($files as $file) {
    if (strpos($file, '.js') !== false) {
        $size = filesize(__DIR__ . '/' . $file);
        echo "<li>$file ($size bytes)</li>";
    }
}
echo "</ul>";

echo "<h2>Debug Info</h2>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Theme directory URI: " . (function_exists('get_stylesheet_directory_uri') ? get_stylesheet_directory_uri() : 'Function not available') . "</p>";
?>