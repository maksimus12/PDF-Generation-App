<?php
/**
 * Helper functions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clean temporary files
 */
function csv_to_pdf_clean_temp_files() {
    $uploads = wp_upload_dir();
    $directory = $uploads['basedir'] . '/csv-to-pdf-generator/';
    
    if (file_exists($directory) && is_dir($directory)) {
        $files = glob($directory . '*.{pdf,zip}', GLOB_BRACE);
        
        // Delete files older than 24 hours
        foreach ($files as $file) {
            if (filemtime($file) < time() - 86400) {
                @unlink($file);
            }
        }
    }
}

// Schedule cleanup
if (!wp_next_scheduled('csv_to_pdf_cleanup_event')) {
    wp_schedule_event(time(), 'daily', 'csv_to_pdf_cleanup_event');
}
add_action('csv_to_pdf_cleanup_event', 'csv_to_pdf_clean_temp_files');

/**
 * Plugin activation
 */
function csv_to_pdf_plugin_activate() {
    // Create necessary directories
    $uploads = wp_upload_dir();
    $directory = $uploads['basedir'] . '/csv-to-pdf-generator/';
    
    if (!file_exists($directory)) {
        wp_mkdir_p($directory);
        file_put_contents($directory . '.htaccess', 'deny from all');
        file_put_contents($directory . 'index.php', '<?php // Silence is golden');
    }
    // Создаем директорию для CSV-шаблонов
    $csv_templates_dir = plugin_dir_path(dirname(__FILE__)) . 'templates/csv-templates/';
    if (!file_exists($csv_templates_dir)) {
        wp_mkdir_p($csv_templates_dir);
    }
}
register_activation_hook(__FILE__, 'csv_to_pdf_plugin_activate');

/**
 * Plugin deactivation
 */
function csv_to_pdf_plugin_deactivate() {
    // Clear scheduled events
    wp_clear_scheduled_hook('csv_to_pdf_cleanup_event');
}
register_deactivation_hook(__FILE__, 'csv_to_pdf_plugin_deactivate');