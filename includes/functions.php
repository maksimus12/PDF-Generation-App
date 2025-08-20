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
 * Создает необходимую структуру папок для языковых шаблонов
 */
function create_language_template_folders() {
    // Базовая директория для шаблонов
    $templates_dir = CSV_TO_PDF_PATH . 'templates/pdf-templates/';
    
    // Директория для языковых шаблонов
    $languages_dir = $templates_dir . 'languages/';
    
    // Создаем базовую директорию если не существует
    if (!file_exists($templates_dir)) {
        wp_mkdir_p($templates_dir);
    }
    
    // Создаем директорию для языковых шаблонов
    if (!file_exists($languages_dir)) {
        wp_mkdir_p($languages_dir);
    }
    
    // Список языков для создания папок
    $languages = array('ru', 'en'); // Можно расширить по необходимости
    
    // Создаем папки для каждого языка
    foreach ($languages as $lang) {
        $lang_dir = $languages_dir . $lang . '/';
        if (!file_exists($lang_dir)) {
            wp_mkdir_p($lang_dir);
        }
    }
}

// Вызываем функцию при активации плагина
add_action('admin_init', 'create_language_template_folders');

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

