<?php
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

$file_content = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
file_put_contents('/tmp/test-pixel.png', $file_content);

$_FILES['test_upload'] = array(
    'name' => 'test-pixel.png',
    'type' => 'image/png',
    'tmp_name' => '/tmp/test-pixel.png',
    'error' => 0,
    'size' => filesize('/tmp/test-pixel.png')
);

$id = media_handle_upload('test_upload', 0);
if (is_wp_error($id)) {
    echo "ERROR: " . $id->get_error_message() . "\n";
} else {
    echo "SUCCESS: " . $id . "\n";
}
