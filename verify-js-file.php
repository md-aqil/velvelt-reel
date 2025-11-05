<?php
// Verify if the global.js file exists and is readable
$js_file_path = __DIR__ . '/global.js';

if (file_exists($js_file_path) && is_readable($js_file_path)) {
    $file_size = filesize($js_file_path);
    $first_line = fgets(fopen($js_file_path, 'r'));
    
    echo "File exists and is readable\n";
    echo "File size: " . $file_size . " bytes\n";
    echo "First line: " . $first_line . "\n";
} else {
    echo "File does not exist or is not readable\n";
    echo "Expected path: " . $js_file_path . "\n";
    
    // List files in directory to see what's there
    $files = scandir(__DIR__);
    echo "Files in directory:\n";
    foreach ($files as $file) {
        if (strpos($file, '.js') !== false) {
            echo "- " . $file . "\n";
        }
    }
}
?>