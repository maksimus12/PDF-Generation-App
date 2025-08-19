<?php
/**
 * Batch Processor Class
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Batch_Processor {
    const BATCH_SIZE = 10; // Обрабатывать по 10 записей за раз
    
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
        
        error_log("Total batches created: " . count($batches) . " with batch size " . self::BATCH_SIZE);
        
        // Сохраняем информацию о процессе в отдельном опциональном поле
        // Это поле будет содержать главные данные процесса
        $process_info = [
            'id' => $process_id,
            'total_batches' => count($batches),
            'processed_batches' => 0,
            'total_items' => count($data),
            'processed_items' => 0,
            'template_id' => $template_id,
            'pdf_files' => [],
            'pdf_files_transients' => [],
            'status' => 'processing',
            'created_at' => time()
        ];
        
        update_option('pdf_batch_' . $process_id . '_info', $process_info);
        
        // Сохраняем первую партию в транзиент
        set_transient($process_id . '_batch_0', $batches[0], DAY_IN_SECONDS);
        
        // Сохраняем остальные пакеты отдельно, по одному
        for ($i = 1; $i < count($batches); $i++) {
            set_transient($process_id . '_batch_' . $i, $batches[$i], DAY_IN_SECONDS);
            error_log("Saved batch $i with " . count($batches[$i]) . " items");
        }
        
        return $process_id;
    }
    
    /**
     * Обработать следующий пакет
     */
    public static function process_next_batch($process_id) {
        // Логирование для отладки
        error_log('Starting batch process for ID: ' . $process_id);
        
        // Получаем информацию о процессе из опций
        $process_info = get_option('pdf_batch_' . $process_id . '_info');
        
        if (!$process_info) {
            error_log('Process info not found for ID: ' . $process_id);
            return ['success' => false, 'message' => 'Process info not found. The process might have expired.'];
        }
        
        if ($process_info['status'] !== 'processing') {
            error_log('Invalid process status: ' . $process_info['status']);
            return ['success' => false, 'message' => 'Invalid process status: ' . $process_info['status']];
        }
        
        // Получаем текущий пакет для обработки
        $current_batch_index = $process_info['processed_batches'];
        $total_batches = $process_info['total_batches'];
        
        error_log("Processing batch $current_batch_index of $total_batches");
        
        if ($current_batch_index >= $total_batches) {
            // Все пакеты обработаны
            $process_info['status'] = 'completed';
            update_option('pdf_batch_' . $process_id . '_info', $process_info);
            
            error_log('All batches completed. Creating ZIP file...');
            
            try {
                // Собираем все PDF-файлы
                $pdf_files = $process_info['pdf_files'];
                error_log("Base array contains " . count($pdf_files) . " PDF files");
                
                // Проверяем, есть ли дополнительные файлы в других транзиентах
                if (!empty($process_info['pdf_files_transients'])) {
                    error_log("Found " . count($process_info['pdf_files_transients']) . " additional transients with files");
                    
                    foreach ($process_info['pdf_files_transients'] as $transient_id) {
                        $additional_files = get_transient($transient_id);
                        if ($additional_files && is_array($additional_files)) {
                            error_log("Adding " . count($additional_files) . " files from transient $transient_id");
                            $pdf_files = array_merge($pdf_files, $additional_files);
                        } else {
                            error_log("Warning: Could not retrieve files from transient $transient_id");
                        }
                    }
                    
                    error_log("Total files after merging all transients: " . count($pdf_files));
                }
                
                // Создаем ZIP-архив со всеми PDF-файлами
                $pdf_generator = new PDF_Generator();
                $zip_url = $pdf_generator->create_zip($pdf_files);
                
                error_log('ZIP file created: ' . $zip_url . ' with ' . count($pdf_files) . ' files');
                
                return [
                    'success' => true,
                    'is_completed' => true,
                    'download_url' => $zip_url,
                    'progress' => 100,
                    'processed' => $process_info['processed_items'],
                    'total' => $process_info['total_items'],
                    'files_count' => count($pdf_files)
                ];
            } catch (Exception $e) {
                error_log('Error creating ZIP file: ' . $e->getMessage());
                return ['success' => false, 'message' => 'Error creating ZIP file: ' . $e->getMessage()];
            }
        }
        
        // Получаем данные текущей партии
        $batch_data = get_transient($process_id . '_batch_' . $current_batch_index);
        
        if (!$batch_data) {
            error_log("Batch data not found for index $current_batch_index in transient {$process_id}_batch_{$current_batch_index}");
            return ['success' => false, 'message' => 'Batch data not found for index ' . $current_batch_index];
        }
        
        error_log("Found batch data for index $current_batch_index with " . count($batch_data) . " items");
        $template_id = $process_info['template_id'];
        
        try {
            // Рассчитываем начальный индекс для текущего пакета
            $start_index = $current_batch_index * self::BATCH_SIZE;
            
            // Генерируем PDF для текущего пакета, передавая начальный индекс
            $pdf_generator = new PDF_Generator();
            $pdf_files = $pdf_generator->generate_pdfs($batch_data, $template_id, $start_index);
            
            error_log("Generated " . count($pdf_files) . " PDF files for batch $current_batch_index (starting from index $start_index)");
            
            // Обновляем информацию о процессе
            $process_info['processed_batches']++;
            $process_info['processed_items'] += count($batch_data);
            
            // Проверяем, не стал ли массив файлов слишком большим
            if (count($process_info['pdf_files']) > 100) {
                // Сохраняем текущие файлы в отдельный транзиент
                $transient_id = $process_id . '_files_' . uniqid();
                set_transient($transient_id, $process_info['pdf_files'], DAY_IN_SECONDS);
                $process_info['pdf_files_transients'][] = $transient_id;
                error_log("Saved " . count($process_info['pdf_files']) . " files to transient $transient_id");
                
                // Начинаем новый массив с текущих файлов
                $process_info['pdf_files'] = $pdf_files; 
            } else {
                // Просто добавляем новые файлы к существующим
                $process_info['pdf_files'] = array_merge($process_info['pdf_files'], $pdf_files);
            }
            
            // Сохраняем обновленную информацию
            update_option('pdf_batch_' . $process_id . '_info', $process_info);
            
            // Можно удалить транзиент с обработанной партией для экономии памяти
            delete_transient($process_id . '_batch_' . $current_batch_index);
            
            $progress = round(($process_info['processed_items'] / $process_info['total_items']) * 100);
            
            error_log("Batch processed. Progress: $progress%. Items: {$process_info['processed_items']}/{$process_info['total_items']}");
            
            return [
                'success' => true,
                'is_completed' => false,
                'progress' => $progress,
                'processed' => $process_info['processed_items'],
                'total' => $process_info['total_items']
            ];
            
        } catch (Exception $e) {
            // В случае ошибки, помечаем процесс как неудачный
            error_log('Error processing batch: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $process_info['status'] = 'failed';
            $process_info['error'] = $e->getMessage();
            update_option('pdf_batch_' . $process_id . '_info', $process_info);
            
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}