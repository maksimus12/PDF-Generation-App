<?php
/**
 * Plugin Name: CSV to PDF Generator
 * Description: Generates multiple PDF documents from CSV file
 * Version: 1.1
 * Author: Maxim Diacenko
 * Text Domain: csv-to-pdf-generator
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


// Define plugin constants
@ini_set('memory_limit', '512M');
@ini_set('max_execution_time', '600'); // 10 минут
@ini_set('upload_max_filesize', '50M');
@ini_set('post_max_size', '50M');

define('CSV_TO_PDF_PATH', plugin_dir_path(__FILE__));
define('CSV_TO_PDF_URL', plugin_dir_url(__FILE__));
define('CSV_TO_PDF_VERSION', '1.0.0');


// Include the autoloader from composer
if (file_exists(CSV_TO_PDF_PATH . 'vendor/autoload.php')) {
    require_once CSV_TO_PDF_PATH . 'vendor/autoload.php';
}

// Main plugin class
class CSV_To_PDF_Generator {
    
    // Singleton instance
    private static $instance = null;
    
    // Get singleton instance
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Constructor
    public function __construct() {
        // Initialize hooks
        add_action('init', array($this, 'init'));
        add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'register_frontend_scripts'));
        
        // Add shortcode
        add_shortcode('csv_to_pdf_generator', array($this, 'generator_shortcode'));
        
        // AJAX handlers
        add_action('wp_ajax_process_csv_to_pdf', array($this, 'process_csv_to_pdf'));
        add_action('wp_ajax_nopriv_process_csv_to_pdf', array($this, 'process_csv_to_pdf'));
        add_action('wp_ajax_get_template_info', array($this, 'get_template_info'));
        add_action('wp_ajax_nopriv_get_template_info', array($this, 'get_template_info'));
        
        // Добавляем обработчик для пакетной обработки
        add_action('wp_ajax_process_batch', array($this, 'process_batch'));
        add_action('wp_ajax_nopriv_process_batch', array($this, 'process_batch'));
    }
    
    // Initialize plugin
    public function init() {
        // Load text domain
        load_plugin_textdomain('csv-to-pdf-generator', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        
        // Include required files
        require_once CSV_TO_PDF_PATH . 'includes/functions.php';
        require_once CSV_TO_PDF_PATH . 'includes/csv-processor.php';
        require_once CSV_TO_PDF_PATH . 'includes/pdf-generator.php';
        require_once CSV_TO_PDF_PATH . 'includes/template-manager.php';
        
        // Create templates directory if not exists
        $templates_dir = CSV_TO_PDF_PATH . 'templates/pdf-templates/';
        if (!file_exists($templates_dir)) {
            wp_mkdir_p($templates_dir);
        }
        
        // Create CSV templates directory if not exists
        $csv_templates_dir = CSV_TO_PDF_PATH . 'templates/csv-templates/';
        if (!file_exists($csv_templates_dir)) {
            wp_mkdir_p($csv_templates_dir);
        }
    }
    
    
    
    
    
    
    // Register admin scripts and styles
    public function register_admin_scripts() {
        wp_enqueue_style('csv-to-pdf-admin', CSV_TO_PDF_URL . 'assets/css/admin.css', array(), CSV_TO_PDF_VERSION);
        wp_enqueue_script('csv-to-pdf-admin', CSV_TO_PDF_URL . 'assets/js/admin.js', array('jquery'), CSV_TO_PDF_VERSION, true);
    }
    
    // Register frontend scripts and styles
    public function register_frontend_scripts() {
        wp_enqueue_style('csv-to-pdf-frontend', CSV_TO_PDF_URL . 'assets/css/frontend.css', array(), CSV_TO_PDF_VERSION);
        wp_enqueue_script('csv-to-pdf-frontend', CSV_TO_PDF_URL . 'assets/js/frontend.js', array('jquery'), CSV_TO_PDF_VERSION, true);
        
        wp_localize_script('csv-to-pdf-frontend', 'csv_to_pdf_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('csv_to_pdf_nonce')
        ));
    }
    
    
    // Shortcode function
    public function generator_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => 'CSV to PDF Generator',
        ), $atts, 'csv_to_pdf_generator');
        
        ob_start();
        include CSV_TO_PDF_PATH . 'templates/generator-form.php';
        return ob_get_clean();
    }
    
    // Модифицируем AJAX-обработчик для получения информации о шаблоне
    public function get_template_info() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'csv_to_pdf_nonce')) {
            wp_send_json_error('Security check failed');
        }
        
        if (!isset($_POST['template_id'])) {
            wp_send_json_error('Template ID is missing');
        }
        
        $template_id = sanitize_text_field($_POST['template_id']);
        
        // Get template info
        $template_manager = new Template_Manager();
        $template_meta = $template_manager->get_template_meta($template_id);
        
        if ($template_meta) {
            // Добавляем URL для скачивания CSV-шаблона
            $template_meta['csv_template_url'] = $template_manager->get_csv_template_url($template_id);
            
            wp_send_json_success($template_meta);
        } else {
            wp_send_json_error('Template not found');
        }
        
        wp_die();
    }
   // AJAX handler for processing CSV to PDF
    public function process_csv_to_pdf() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'csv_to_pdf_nonce')) {
            wp_send_json_error('Security check failed');
        }
        
        try {
            // Check if file is uploaded
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Please upload a valid CSV file');
            }
            
            // Get template ID
            $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
            if (empty($template_id)) {
                throw new Exception('No template selected');
            }
            
            // Check if template exists
            $template_manager = new Template_Manager();
            $template_meta = $template_manager->get_template_meta($template_id);
            if (!$template_meta) {
                throw new Exception('Selected template not found');
            }
            
            // Process CSV file
            $csv_processor = new CSV_Processor($_FILES['csv_file']['tmp_name']);
            
            // Validate CSV against template requirements
            if (!empty($template_meta['required_fields'])) {
                try {
                    $csv_processor->validate(array_keys($template_meta['required_fields']));
                } catch (Exception $e) {
                    throw new Exception('CSV validation error: ' . $e->getMessage());
                }
            }
            
            $data = $csv_processor->process();
            
            // Если файл содержит более 20 строк, используем пакетную обработку
            if (count($data) > 20) {
                require_once CSV_TO_PDF_PATH . 'includes/batch-processor.php';
                $process_id = Batch_Processor::start_batch_process($data, $template_id);
                
                wp_send_json_success(array(
                    'process_id' => $process_id,
                    'total_items' => count($data)
                ));
            } else {
                // Для небольших файлов используем существующий код
                $pdf_generator = new PDF_Generator();
                $pdf_files = $pdf_generator->generate_pdfs($data, $template_id);
                
                // Create ZIP archive with all PDFs
                $zip_file = $pdf_generator->create_zip($pdf_files);
                
                // Send download URL
                wp_send_json_success(array(
                    'download_url' => $zip_file
                ));
            }
            
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
        
        wp_die();
    }
    
    // AJAX handler for batch processing
    public function process_batch() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'csv_to_pdf_nonce')) {
            wp_send_json_error(['message' => 'Security check failed']);
            return;
        }
        
        if (!isset($_POST['process_id'])) {
            wp_send_json_error(['message' => 'Process ID is missing']);
            return;
        }
        
        $process_id = sanitize_text_field($_POST['process_id']);
        
        // Для отладки
        error_log('Received batch processing request for ID: ' . $process_id);
        
        require_once CSV_TO_PDF_PATH . 'includes/batch-processor.php';
        $result = Batch_Processor::process_next_batch($process_id);
        
        // Логируем результат
        error_log('Batch processing result: ' . json_encode($result));
        
        if (isset($result['success']) && $result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error([
                'message' => isset($result['message']) ? $result['message'] : 'Unknown error during batch processing'
            ]);
        }
        
        wp_die();
    }
}



// Initialize plugin
function csv_to_pdf_generator_init() {
    return CSV_To_PDF_Generator::get_instance();
}

// Start the plugin
csv_to_pdf_generator_init();

// Initialize Form to PDF functionality
require_once CSV_TO_PDF_PATH . 'includes/form-to-pdf/class-form-to-pdf.php';

// Register directories for form templates
add_action('init', function() {
    // Create form templates directory if not exists
    $form_templates_dir = CSV_TO_PDF_PATH . 'templates/form-templates/';
    if (!file_exists($form_templates_dir)) {
        wp_mkdir_p($form_templates_dir);
    }
    
    // Create tmp directory for mPDF
    $uploads = wp_upload_dir();
    $tmp_dir = $uploads['basedir'] . '/csv-to-pdf-generator/tmp/';
    if (!file_exists($tmp_dir)) {
        wp_mkdir_p($tmp_dir);
    }
    
    // Add default initialization data that can be used across the plugin
    global $pdf_form_defaults;
    $pdf_form_defaults = array(
        'datetime' => '2025-08-19 11:51:56', // Текущая дата/время
        'user' => 'maksimus12', // Текущий пользователь
        'version' => '1.1.0', // Версия модуля форм
    );
});

/**
 * Get current user and date information for PDF generation
 * This can be used in form processors and PDF templates
 */
function get_pdf_form_user_data() {
    // Get current user info if logged in
    $current_user = wp_get_current_user();
    $username = $current_user->ID ? $current_user->user_login : 'maksimus12';
    
    return array(
        'username' => $username,
        'datetime' => '2025-08-19 11:51:56', // Используем фиксированную дату/время
        'timestamp' => time(),
        'default_date' => '2025-08-19',
        'default_user' => 'maksimus12'
    );
}

// Initialize Form to PDF
function form_to_pdf_init() {
    return Form_To_PDF::get_instance();
}

// Start the Form to PDF module
add_action('init', 'form_to_pdf_init');

