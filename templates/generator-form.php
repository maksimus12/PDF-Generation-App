<?php
/**
 * Frontend form template with Tailwind CSS styling
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Подключение Tailwind CSS через CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-white shadow-lg rounded-lg p-6 max-w-2xl mx-auto my-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?php echo esc_html($atts['title']); ?></h2>
    
    <div class="csv-to-pdf-form">
        <form id="csv-to-pdf-form" enctype="multipart/form-data">
            <div class="mb-6">
                <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                    <?php _e('Upload CSV File', 'csv-to-pdf-generator'); ?>
                </label>
                
                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed border-gray-300 rounded-md hover:border-blue-400 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="csv_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                <span><?php _e('Select a CSV file', 'csv-to-pdf-generator'); ?></span>
                                <input id="csv_file" name="csv_file" type="file" accept=".csv" class="sr-only" required>
                            </label>
                            <p class="pl-1"><?php _e('or drag and drop', 'csv-to-pdf-generator'); ?></p>
                        </div>
                        <p class="text-xs text-gray-500">
                            <?php _e('Please upload a CSV file with your data', 'csv-to-pdf-generator'); ?>
                        </p>
                        <p id="file-name" class="text-sm font-medium text-blue-600 mt-2 hidden"></p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between mt-8">
                <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    <?php _e('Generate PDFs', 'csv-to-pdf-generator'); ?>
                </button>
            </div>
            
            <!-- Сообщения -->
            <div class="csv-to-pdf-message mt-4 px-4 py-3 rounded-md hidden"></div>
            
            <!-- Загрузка -->
            <div class="csv-to-pdf-loader hidden mt-6 flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-gray-700"><?php _e('Generating PDFs, please wait...', 'csv-to-pdf-generator'); ?></span>
            </div>
            
            <!-- Кнопка скачивания -->
            <div class="csv-to-pdf-download hidden mt-6">
                <a href="#" class="download-zip-button flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors w-full sm:w-auto" target="_blank">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <?php _e('Download PDF Package', 'csv-to-pdf-generator'); ?>
                </a>
            </div>
            
            <input type="hidden" name="action" value="process_csv_to_pdf">
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('csv_to_pdf_nonce'); ?>">
        </form>
    </div>
</div>

<script>
// Скрипт для отображения имени выбранного файла
document.getElementById('csv_file').addEventListener('change', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : '';
    var fileNameElement = document.getElementById('file-name');
    
    if (fileName) {
        fileNameElement.textContent = 'Selected file: ' + fileName;
        fileNameElement.classList.remove('hidden');
    } else {
        fileNameElement.classList.add('hidden');
    }
});

// Обновление скриптов для работы с новыми классами
jQuery(document).ready(function($) {
    $('#csv-to-pdf-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = new FormData(form[0]);
        var messageDiv = form.find('.csv-to-pdf-message');
        var loader = form.find('.csv-to-pdf-loader');
        var downloadSection = form.find('.csv-to-pdf-download');
        var downloadButton = form.find('.download-zip-button');
        
        // Очистка предыдущих сообщений
        messageDiv.empty().addClass('hidden').removeClass('bg-red-100 text-red-800 bg-green-100 text-green-800');
        
        // Валидация файла
        var fileInput = form.find('input[name="csv_file"]')[0];
        if (fileInput.files.length === 0) {
            messageDiv.addClass('bg-red-100 text-red-800').removeClass('hidden').text('Please select a CSV file');
            return false;
        }
        
        // Проверка типа файла
        var fileName = fileInput.files[0].name;
        var fileExt = fileName.split('.').pop().toLowerCase();
        
        if (fileExt !== 'csv') {
            messageDiv.addClass('bg-red-100 text-red-800').removeClass('hidden').text('Please upload a valid CSV file');
            return false;
        }
        
        // Показать загрузку
        loader.removeClass('hidden');
        downloadSection.addClass('hidden');
        
        // Отправка формы через AJAX
        $.ajax({
            url: csv_to_pdf_vars.ajax_url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                loader.addClass('hidden');
                
                if (response.success) {
                    messageDiv.addClass('bg-green-100 text-green-800').removeClass('hidden').text('PDFs generated successfully!');
                    
                    // Показать кнопку скачивания и установить ссылку
                    downloadButton.attr('href', response.data.download_url);
                    downloadSection.removeClass('hidden');
                } else {
                    messageDiv.addClass('bg-red-100 text-red-800').removeClass('hidden').text('Error: ' + response.data);
                }
            },
            error: function() {
                loader.addClass('hidden');
                messageDiv.addClass('bg-red-100 text-red-800').removeClass('hidden').text('Server error. Please try again later.');
            }
        });
        
        return false;
    });
});
</script>