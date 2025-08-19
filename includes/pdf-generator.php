<?php
/**
 * PDF Generator Class
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'mpdf-config.php';

class PDF_Generator {
    private $mpdf;
    private $uploads_dir;
    
    public function __construct() {
        // Setup uploads directory
        $uploads = wp_upload_dir();
        $this->uploads_dir = $uploads['basedir'] . '/csv-to-pdf-generator/';
        
        // Create directory if it doesn't exist
        if (!file_exists($this->uploads_dir)) {
            wp_mkdir_p($this->uploads_dir);
            
            // Create .htaccess file to protect this directory
            file_put_contents($this->uploads_dir . '.htaccess', 'deny from all');
            
            // Create index.php to prevent directory listing
            file_put_contents($this->uploads_dir . 'index.php', '<?php // Silence is golden');
        }
    }
    
    /**
     * Оптимизированный метод для генерации PDF
     */
    public function generate_pdfs($data, $template_id) {
        // Путь к шаблону
        $template_path = CSV_TO_PDF_PATH . 'templates/pdf-templates/' . $template_id . '.php';
        
        if (!file_exists($template_path)) {
            throw new Exception('Template file not found');
        }
        
        // Получаем метаданные шаблона
        $template_manager = new Template_Manager();
        $template_meta = $template_manager->get_template_meta($template_id);
        
        $pdf_files = array();
        
        foreach ($data as $index => $row) {
            // Создаем уникальное имя файла на основе первого поля или индекса
            $first_field = reset($row);
            $filename = $this->uploads_dir . 'document_' . ($index + 1) . '_' . sanitize_title($first_field) . '.pdf';
            
            // Инициализируем mPDF для каждого документа отдельно, чтобы избежать утечек памяти
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
            
            // Генерируем PDF из шаблона
            ob_start();
            include $template_path;
            $html = ob_get_clean();
            
            if (isset($footer_html)) {
                $mpdf->SetHTMLFooter($footer_html);
            }
            
            $mpdf->WriteHTML($html);
            $mpdf->Output($filename, 'F');
            
            // Явно освобождаем память
            $mpdf = null;
            unset($mpdf);
            
            $pdf_files[] = $filename;
        }
        
        return $pdf_files;
    }
    
    /**
     * Создает ZIP-архив с PDF файлами и возвращает URL для скачивания
     * 
     * @param array $pdf_files Массив путей к PDF файлам
     * @return string URL для скачивания ZIP-архива
     */
    public function create_zip($pdf_files) {
        if (empty($pdf_files)) {
            throw new Exception('No PDF files to archive');
        }
        
        // Создаем уникальное имя для ZIP файла
        $zip_filename = 'documents_' . time() . '_' . wp_rand() . '.zip';
        $zip_path = $this->uploads_dir . $zip_filename;
        
        // Создаем ZIP архив
        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
            throw new Exception('Cannot create ZIP file');
        }
        
        // Добавляем PDF файлы в архив
        foreach ($pdf_files as $pdf_file) {
            if (file_exists($pdf_file)) {
                $zip->addFile($pdf_file, basename($pdf_file));
            }
        }
        
        $zip->close();
        
        // Создаем URL для скачивания с защитой через nonce
        $download_url = plugin_dir_url(dirname(__FILE__)) . 'download.php?file=' . $zip_filename . '&nonce=' . wp_create_nonce('download_pdf_zip');
        
        return $download_url;
    }
}