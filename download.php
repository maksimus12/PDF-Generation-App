<?php
/**
 * Download handler
 */

// Load WordPress
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

// Security check
if (!isset($_GET['file']) || !isset($_GET['nonce'])) {
    wp_die('Invalid request');
}

// Verify nonce
if (!wp_verify_nonce($_GET['nonce'], 'download_pdf_zip')) {
    wp_die('Security check failed');
}

// Get file path
$uploads = wp_upload_dir();
$file_dir = $uploads['basedir'] . '/csv-to-pdf-generator/';
$file_name = sanitize_file_name($_GET['file']);
$file_path = $file_dir . $file_name;

// Security: Make sure the file is within our uploads directory
if (strpos(realpath($file_path), realpath($file_dir)) !== 0) {
    wp_die('Invalid file path');
}

// Check if file exists
if (!file_exists($file_path)) {
    wp_die('File not found');
}

// Set headers and serve the file
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
