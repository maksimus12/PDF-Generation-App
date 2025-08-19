<?php
/**
 * Batch Processor Class
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Batch_Processor {
    const BATCH_SIZE = 20; // Обрабатывать по 20 записей за раз
    
    /**
     * Разделить данные на пакеты
     */
    public static function prepare_batches($data) {
        return array_chunk($data, self::BATCH_SIZE);
    }
    
    /**
     * Запустить пакетную обработку через AJAX
     */
    public static function start_batch_process($data, $template_id) {
        // Создаем уникальный ID для текущего процесса
        $process_id = 'pdf_gen_' . time() . '_' . wp_rand();
        
        // Разделяем данные на пакеты
        $batches = self::prepare_batches($data);
        
        // Сохраняем информацию о процессе в транзиент
        set_transient($process_id, [
            'total_batches' => count($batches),
            'processed_batches' => 0,
            'total_items' => count($data),
            'processed_items' => 0,
            'template_id' => $template_id,
            'pdf_files' => [],
            'batches' => $batches,
            'status' => 'processing'
        ], DAY_IN_SECONDS); // Хранить 24 часа
        
        return $process_id;
    }
    
    /**
     * Обработать следующий пакет
     */
    public static function process_next_batch($process_id) {
    // Логирование для отладки
    error_log('Starting batch process for ID: ' . $process_id);
    
    // Получаем текущее состояние процесса
    $process_data = get_transient($process_id);
    
    if (!$process_data) {
        error_log('Process data not found for ID: ' . $process_id);
        return ['success' => false, 'message' => 'Process data not found. The process might have expired.'];
    }
    
    if ($process_data['status'] !== 'processing') {
        error_log('Invalid process status: ' . $process_data['status']);
        return ['success' => false, 'message' => 'Invalid process status: ' . $process_data['status']];
    }
    
    // Получаем текущий пакет для обработки
    $current_batch_index = $process_data['processed_batches'];
    
    if ($current_batch_index >= $process_data['total_batches']) {
        // Все пакеты обработаны
        $process_data['status'] = 'completed';
        set_transient($process_id, $process_data, DAY_IN_SECONDS);
        
        error_log('All batches completed. Creating ZIP file...');
        
        try {
            // Создаем ZIP-архив со всеми PDF-файлами
            $pdf_generator = new PDF_Generator();
            $zip_url = $pdf_generator->create_zip($process_data['pdf_files']);
            
            error_log('ZIP file created: ' . $zip_url);
            
            return [
                'success' => true,
                'is_completed' => true,
                'download_url' => $zip_url,
                'progress' => 100,
                'processed' => $process_data['processed_items'],
                'total' => $process_data['total_items']
            ];
        } catch (Exception $e) {
            error_log('Error creating ZIP file: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error creating ZIP file: ' . $e->getMessage()];
        }
    }
    
    error_log('Processing batch ' . ($current_batch_index + 1) . ' of ' . $process_data['total_batches']);
    
    // Обрабатываем текущий пакет
    $batch_data = $process_data['batches'][$current_batch_index];
    $template_id = $process_data['template_id'];
    
    try {
        // Генерируем PDF для текущего пакета
        $pdf_generator = new PDF_Generator();
        $pdf_files = $pdf_generator->generate_pdfs($batch_data, $template_id);
        
        // Обновляем информацию о процессе
        $process_data['processed_batches']++;
        $process_data['processed_items'] += count($batch_data);
        $process_data['pdf_files'] = array_merge($process_data['pdf_files'], $pdf_files);
        
        // Обновляем транзиент
        $set_result = set_transient($process_id, $process_data, DAY_IN_SECONDS);
        
        if (!$set_result) {
            error_log('Failed to update transient data. Data might be too large.');
            // Попробуем сократить данные, оставив только необходимое
            $minimal_data = [
                'status' => $process_data['status'],
                'processed_batches' => $process_data['processed_batches'],
                'total_batches' => $process_data['total_batches'],
                'processed_items' => $process_data['processed_items'],
                'total_items' => $process_data['total_items'],
                'template_id' => $process_data['template_id'],
                'pdf_files' => $process_data['pdf_files'],
            ];
            set_transient($process_id, $minimal_data, DAY_IN_SECONDS);
        }
        
        $progress = round(($process_data['processed_items'] / $process_data['total_items']) * 100);
        
        error_log("Batch processed. Progress: $progress%. Items: {$process_data['processed_items']}/{$process_data['total_items']}");
        
        return [
            'success' => true,
            'is_completed' => false,
            'progress' => $progress,
            'processed' => $process_data['processed_items'],
            'total' => $process_data['total_items']
        ];
        
    } catch (Exception $e) {
        // В случае ошибки, помечаем процесс как неудачный
        error_log('Error processing batch: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        
        $process_data['status'] = 'failed';
        $process_data['error'] = $e->getMessage();
        set_transient($process_id, $process_data, DAY_IN_SECONDS);
        
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
}