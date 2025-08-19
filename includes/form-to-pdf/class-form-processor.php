<?php
/**
 * Form Processor Class
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Form_Processor {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Ensure tmp directory exists
        $uploads = wp_upload_dir();
        $tmp_dir = $uploads['basedir'] . '/csv-to-pdf-generator/tmp/';
        if (!file_exists($tmp_dir)) {
            wp_mkdir_p($tmp_dir);
        }
    }
    
    /**
     * Process form data and generate PDF
     */
    public function process_form($post_data, $pdf_template) {
        // Clean and prepare form data
        $form_data = $this->prepare_form_data($post_data);
        
        // Verify that PDF template exists
        $template_manager = new Template_Manager();
        $template_meta = $template_manager->get_template_meta($pdf_template);
        
        if (!$template_meta) {
            throw new Exception('PDF template not found');
        }
        
        // Check if all required fields are present
        if (!empty($template_meta['required_fields'])) {
            $this->validate_required_fields($form_data, $template_meta['required_fields']);
        }
        
        // Add user and datetime information
        $user_data = get_pdf_form_user_data();
        $form_data['generated_by'] = $user_data['username'];
        $form_data['generated_date'] = $user_data['datetime'];
        
        // Generate PDF (используем наш собственный метод вместо вызова метода из PDF_Generator)
        $pdf_file = $this->generate_pdf_from_form($form_data, $pdf_template);
        
        // Create download URL
        $download_url = $this->create_download_url($pdf_file);
        
        return array(
            'pdf_file' => $pdf_file,
            'download_url' => $download_url
        );
    }
    
    /**
     * Prepare and sanitize form data
     */
    private function prepare_form_data($post_data) {
        $form_data = array();
        
        foreach ($post_data as $key => $value) {
            // Skip non-form data
            if (in_array($key, array('action', 'nonce', 'form_id', 'pdf_template'))) {
                continue;
            }
            
            // Sanitize data
            if (is_array($value)) {
                $form_data[$key] = array_map('sanitize_text_field', $value);
            } else {
                $form_data[$key] = sanitize_text_field($value);
            }
        }
        
        return $form_data;
    }
    
    /**
     * Validate that all required fields are present
     */
    private function validate_required_fields($form_data, $required_fields) {
        $missing_fields = array();
        
        foreach (array_keys($required_fields) as $field) {
            if (!isset($form_data[$field]) || empty($form_data[$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            throw new Exception('Missing required fields: ' . implode(', ', $missing_fields));
        }
        
        return true;
    }
    
    /**
     * Generate PDF from form data
     */
    private function generate_pdf_from_form($form_data, $template_id) {
        $uploads = wp_upload_dir();
        $uploads_dir = $uploads['basedir'] . '/csv-to-pdf-generator/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploads_dir)) {
            wp_mkdir_p($uploads_dir);
        }
        
        $template_path = CSV_TO_PDF_PATH . 'templates/pdf-templates/' . $template_id . '.php';
        
        if (!file_exists($template_path)) {
            throw new Exception('Template file not found');
        }
        
        // Create unique filename
        $filename = $uploads_dir . 'form_' . time() . '_' . wp_rand() . '.pdf';
        
        // Get template metadata for settings
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
                plugin_dir_path(dirname(dirname(__FILE__))) . 'fonts/',
                plugin_dir_path(dirname(dirname(__FILE__))) . 'fonts/dejavu/',
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
            'tempDir' => $uploads_dir . 'tmp/'
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
    }
    
    /**
     * Create download URL for PDF file
     */
    private function create_download_url($pdf_file) {
        $filename = basename($pdf_file);
        
        return plugin_dir_url(dirname(dirname(__FILE__))) . 'download.php?file=' . $filename . 
               '&nonce=' . wp_create_nonce('download_pdf_zip');
    }
}