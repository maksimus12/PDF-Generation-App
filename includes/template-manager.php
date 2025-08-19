<?php
/**
 * Template Manager Class
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Template_Manager {
    /**
     * Получает список всех доступных шаблонов
     */
    public function get_templates() {
        $templates = [];
        $templates_dir = CSV_TO_PDF_PATH . 'templates/pdf-templates/';
        
        if (!is_dir($templates_dir)) {
            return $templates;
        }
        
        $files = glob($templates_dir . '*.php');
        
        foreach ($files as $file) {
            $template_data = $this->get_template_meta(basename($file, '.php'));
            if ($template_data) {
                $templates[basename($file, '.php')] = $template_data;
            }
        }
        
        return $templates;
    }
    
    /**
     * Получает метаданные шаблона из комментариев в файле
     */
    public function get_template_meta($template_id) {
        $file_path = CSV_TO_PDF_PATH . 'templates/pdf-templates/' . $template_id . '.php';
        
        if (!file_exists($file_path)) {
            return false;
        }
        
        // Читаем первые 4KB файла для получения заголовка
        $content = file_get_contents($file_path, false, null, 0, 4096);
        
        // Извлекаем метаданные из комментариев
        preg_match('/\* Template Name: (.*?)\n/', $content, $name);
        preg_match('/\* Template ID: (.*?)\n/', $content, $id);
        preg_match('/\* Description: (.*?)\n/', $content, $description);
        preg_match('/\* Required Fields: (.*?)\n/', $content, $fields);
        preg_match('/\* Default Font: (.*?)\n/', $content, $font);
        
        $required_fields = [];
        if (!empty($fields[1])) {
            $field_list = explode(',', $fields[1]);
            foreach ($field_list as $field) {
                $field = trim($field);
                $required_fields[$field] = ucfirst(str_replace('_', ' ', $field));
            }
        }
        
        return [
            'id' => !empty($id[1]) ? trim($id[1]) : basename($file_path, '.php'),
            'name' => !empty($name[1]) ? trim($name[1]) : basename($file_path, '.php'),
            'description' => !empty($description[1]) ? trim($description[1]) : '',
            'required_fields' => $required_fields,
            'default_font' => !empty($font[1]) ? trim($font[1]) : 'dejavusans'
        ];
    }
    
    // Добавьте метод для получения пути к CSV-шаблону
    public function get_csv_template_path($template_id) {
        $csv_template_path = CSV_TO_PDF_PATH . 'templates/csv-templates/' . $template_id . '.csv';
        if (file_exists($csv_template_path)) {
            return $csv_template_path;
        }
        return false;
    }
    
    // Добавьте метод для получения URL CSV-шаблона
    public function get_csv_template_url($template_id) {
        $csv_template_path = $this->get_csv_template_path($template_id);
        if ($csv_template_path) {
            return CSV_TO_PDF_URL . 'templates/csv-templates/' . $template_id . '.csv';
        }
        return false;
    }
        
}