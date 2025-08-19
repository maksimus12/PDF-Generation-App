<?php
/**
 * Plugin Name: CSV to PDF Generator
 * Description: Generates multiple PDF documents from CSV file
 * Version: 1.0
 * Author: Maxim Diacenko
 * Text Domain: csv-to-pdf-generator
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants

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
    }
    
    // Initialize plugin
    public function init() {
        // Load text domain
        load_plugin_textdomain('csv-to-pdf-generator', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        
        // Include required files
        require_once CSV_TO_PDF_PATH . 'includes/functions.php';
        require_once CSV_TO_PDF_PATH . 'includes/csv-processor.php';
        require_once CSV_TO_PDF_PATH . 'includes/pdf-generator.php';
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
            'title' => 'CSV to PDF Invitation',
        ), $atts, 'csv_to_pdf_generator');
        
        ob_start();
        include CSV_TO_PDF_PATH . 'templates/generator-form.php';
        return ob_get_clean();
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
            
            // Process CSV file
            $csv_processor = new CSV_Processor($_FILES['csv_file']['tmp_name']);
            $data = $csv_processor->process();
            
            // Generate PDFs
            $pdf_generator = new PDF_Generator();
            $pdf_files = $pdf_generator->generate_pdfs($data);
            
            // Create ZIP archive with all PDFs
            $zip_file = $pdf_generator->create_zip($pdf_files);
            
            // Send download URL
            wp_send_json_success(array(
                'download_url' => $zip_file
            ));
            
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
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
