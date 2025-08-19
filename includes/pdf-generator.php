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
     * Generate multiple PDFs from data array
     */
    public function generate_pdfs($data) {
        $pdf_files = array();
        
        foreach ($data as $index => $row) {
            // Create a unique filename
            $filename = $this->uploads_dir . 'document_' . ($index + 1) . '_' . $row['fullNameEn'] . '.pdf';
            
            // Initialize mPDF
             $mpdf = new \Mpdf\Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'margin_left' => 10,
                        'margin_right' => 10,
                        'margin_top' => 5,
                        'margin_bottom' => 0,
                        'margin_header' => 0,
                        'margin_footer' => 0,
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
                        'default_font' => 'cambria',
                        'tempDir' => $this->uploads_dir . 'tmp/'
                    ]);
                    
            
            // Apply font configuration
            $mpdf = csv_to_pdf_configure_fonts($mpdf);
            
            // Set document metadata
            $mpdf->SetTitle(isset($row['title']) ? $row['title'] : 'Document ' . ($index + 1));
            $mpdf->SetAuthor('CSV to PDF Generator');
            
            // Generate PDF content from template
            ob_start();
            include CSV_TO_PDF_PATH . 'templates/pdf-template.php';
            $html = ob_get_clean();
            
            // Write to PDF
            $mpdf->WriteHTML($html);
            
            // Save PDF
            $mpdf->Output($filename, 'F');
            
            // Add to files array
            $pdf_files[] = $filename;
        }
        
        return $pdf_files;
    }
    
    /**
     * Create ZIP archive with all PDFs
     */
    public function create_zip($pdf_files) {
        // Create a unique filename for ZIP
        $zip_filename = $this->uploads_dir . 'pdf_package_' . time() . '.zip';
        
        $zip = new ZipArchive();
        if ($zip->open($zip_filename, ZipArchive::CREATE) !== TRUE) {
            throw new Exception("Cannot create ZIP archive");
        }
        
        // Add files to ZIP
        foreach ($pdf_files as $pdf_file) {
            $zip->addFile($pdf_file, basename($pdf_file));
        }
        
        $zip->close();
        
        foreach ($pdf_files as $pdf_file) {
            if (file_exists($pdf_file)) {
                @unlink($pdf_file);
            }
        }
        
        // Create secure download URL through our download handler
        $nonce = wp_create_nonce('download_pdf_zip');
        $download_url = plugins_url('download.php', dirname(__FILE__)) . 
                        '?file=' . basename($zip_filename) . 
                        '&nonce=' . $nonce;
        
        return $download_url;
    }
}
