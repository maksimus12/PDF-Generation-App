<?php
/**
 * mPDF font configuration for Cambria
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configure mPDF to use Cambria font
 */
function csv_to_pdf_configure_fonts($mpdf) {
    // Определяем данные шрифта напрямую, без поиска в директории
    $mpdf->fontdata = [
        'cambria' => [
            'R' => 'Cambria.ttf',
            'B' => 'Cambria-Bold.ttf',
            'I' => 'Cambria-Italic.ttf',
            'BI' => 'Cambria-BoldItalic.ttf',
        ]
    ];
    
    // Отключаем автоматическую подстановку шрифтов
    $mpdf->useSubstitutions = false;
    
    // Устанавливаем шрифт по умолчанию
    $mpdf->default_font = 'cambria';
    
    return $mpdf;
}