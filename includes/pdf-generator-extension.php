<?php
/**
 * PDF Generator Extension for Form to PDF
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add method to PDF_Generator class
class_exists('PDF_Generator') or require_once CSV_TO_PDF_PATH . 'includes/pdf-generator.php';

// Add method to PDF_Generator using class_exists check to avoid errors if method already exists
if (!method_exists('PDF_Generator', 'generate_pdf_from_form')) {
    /**
     * Add generate_pdf_from_form method to PDF_Generator
     */
    PDF_Generator::add_method('generate_pdf_from_form', function($form_data, $template_id) {
        // Create unique filename
        $filename = $this->uploads_dir . 'form_' . time() . '_' . wp_rand() . '.pdf';
        
        // Get template path
        $template_path = CSV_TO_PDF_PATH . 'templates/pdf-templates/' . $template_id . '.php';
        
        if (!file_exists($template_path)) {
            throw new Exception('Template file not found');
        }
        
        // Get template metadata
        $template_manager = new Template_Manager();
        $template_meta = $template_manager->get_template_meta($template_id);
        
        // Initialize mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 5,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 10,
            'default_font_size' => 12,
            'fontDir' => [
                plugin_dir_path(dirname(__FILE__)) . 'fonts/',
                plugin_dir_path(dirname(__FILE__)) . 'fonts/dejavu/',
            ],
            'fontdata' => [
                'cambria' => [
                    'R' => 'Cambria.ttf',
                    'B' => 'Cambria-Bold.ttf',
                    'I' => 'Cambria-Italic.ttf',
                    'BI' => 'Cambria-BoldItalic.ttf',
                ],
                'dejavuserifcondensed' => [
                    'R' => 'dejavu/DejaVuSerifCondensed.ttf',
                    'B' => 'dejavu/DejaVuSerifCondensed-Bold.ttf',
                    'I' => 'dejavu/DejaVuSerifCondensed-Italic.ttf',
                    'BI' => 'dejavu/DejaVuSerifCondensed-BoldItalic.ttf',
                ]
            ],
            'default_font' => $template_meta['default_font'] ?? 'dejavuserifcondensed',
            'tempDir' => $this->uploads_dir . 'tmp/'
        ]);
        
        // Generate PDF from template
        ob_start();
        $row = $form_data; // Use variable $row for compatibility with existing templates
        include $template_path;
        $html = ob_get_clean();
        
        if (isset($footer_html)) {
            $mpdf->SetHTMLFooter($footer_html);
        }
        
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'F');
        
        return $filename;
    });
}

/**
 * Helper function to add methods to existing classes
 */
if (!function_exists('add_method')) {
    function add_method($class_name, $method_name, $method_implementation) {
        // Check if class exists
        if (!class_exists($class_name)) {
            return false;
        }
        
        // Check if method already exists
        if (method_exists($class_name, $method_name)) {
            return false;
        }
        
        // Add method using Reflection
        $reflection = new ReflectionClass($class_name);
        $method = $reflection->getMethod($method_name);
        
        if (!$method) {
            $class = $reflection->newInstanceWithoutConstructor();
            $class->$method_name = $method_implementation;
            return true;
        }
        
        return false;
    }
}