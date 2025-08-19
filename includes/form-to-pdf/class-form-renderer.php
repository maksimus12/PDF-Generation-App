<?php
/**
 * Form Renderer Class
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Form_Renderer {
    /**
     * Render a form based on template ID
     */
    public function render_form($form_id, $pdf_template, $atts) {
        $form_template = $this->get_form_template_path($form_id);
        
        if (!file_exists($form_template)) {
            return '<div class="pdf-form-error">Error: Form template not found.</div>';
        }
        
        // Check if PDF template exists
        $template_manager = new Template_Manager();
        $template_meta = $template_manager->get_template_meta($pdf_template);
        
        if (!$template_meta) {
            return '<div class="pdf-form-error">Error: PDF template not found.</div>';
        }
        
        // Output buffer to return form HTML
        ob_start();
        
        // Include the form template with access to these variables
        $template_title = $atts['title'];
        $pdf_template_id = $pdf_template;
        $required_fields = !empty($template_meta['required_fields']) ? $template_meta['required_fields'] : array();
        
        include $form_template;
        
        // Return the rendered form
        return ob_get_clean();
    }
    
    /**
     * Get the path to a form template
     */
    private function get_form_template_path($form_id) {
        $form_id = sanitize_file_name($form_id);
        return CSV_TO_PDF_PATH . 'templates/form-templates/' . $form_id . '.php';
    }
}