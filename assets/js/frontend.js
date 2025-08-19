/**
 * Frontend JS for CSV to PDF Generator
 */
jQuery(document).ready(function($) {
    // Функция обновления информации о шаблоне
    function updateTemplateInfo() {
        var templateId = $('#template_id').val();
        
        if (!templateId) return;
        
        $.ajax({
            url: csv_to_pdf_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'get_template_info',
                nonce: csv_to_pdf_vars.nonce,
                template_id: templateId
            },
            success: function(response) {
                if (response.success) {
                    var requiredFieldsDiv = $('#required-fields');
                    var fieldsHTML = '';
                    
                    if (response.data.csv_template_url) {
                        // Показываем ссылку на скачивание CSV-шаблона
                        fieldsHTML = '<div class="mb-4">' +
                            '<p class="mb-2">Download the CSV file template:</p>' +
                            '<a href="' + response.data.csv_template_url + '" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors" download>' +
                            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />' +
                            '</svg>Download Template</a>' +
                            '</div>';
                    } else {
                        fieldsHTML = '<p>No required fields specified for this template.</p>';
                    }
                    
                    requiredFieldsDiv.html(fieldsHTML);
                }
            }
        });
    }
    
    // Вызываем обновление информации при загрузке страницы
    updateTemplateInfo();
    
    // Обновляем информацию при выборе шаблона
    $('#template_id').on('change', updateTemplateInfo);
    
    $('#csv-to-pdf-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = new FormData(form[0]);
        var messageDiv = form.find('.csv-to-pdf-message');
        var loader = form.find('.csv-to-pdf-loader');
        var downloadSection = form.find('.csv-to-pdf-download');
        var downloadButton = form.find('.download-zip-button');
        var submitButton = form.find('.submit-button');
        var progressBar = form.find('.progress-bar');
        var progressSection = form.find('.csv-to-pdf-progress');
        var progressText = form.find('.progress-text');
        
        // Очистка предыдущих сообщений
        messageDiv.empty().addClass('hidden').removeClass('bg-red-100 text-red-800 bg-green-100 text-green-800');
        
        // Проверка выбора шаблона
        var templateId = form.find('#template_id').val();
        if (!templateId) {
            messageDiv.addClass('bg-red-100 text-red-800').removeClass('hidden').text('Please select a template');
            return false;
        }
        
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
        submitButton.addClass('hidden');
        downloadSection.addClass('hidden');
        progressSection.removeClass('hidden');
        progressBar.css('width', '0%').attr('aria-valuenow', 0);
        progressText.text('Preparing data...');
        
        // Начинаем процесс обработки
        $.ajax({
            url: csv_to_pdf_vars.ajax_url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    // Если большой CSV-файл, используем пакетную обработку
                    if (response.data.process_id) {
                        processBatch(response.data.process_id);
                    } else {
                        // Для небольших файлов - сразу показываем результат
                        loader.addClass('hidden');
                        progressSection.addClass('hidden');
                        messageDiv.addClass('bg-green-100 text-green-800').removeClass('hidden').text('PDFs generated successfully!');
                        downloadButton.attr('href', response.data.download_url);
                        downloadSection.removeClass('hidden');
                    }
                } else {
                    loader.addClass('hidden');
                    progressSection.addClass('hidden');
                    submitButton.removeClass('hidden');
                    messageDiv.addClass('bg-red-100 text-red-800').removeClass('hidden').text('Error: ' + response.data);
                }
            },
            error: function() {
                loader.addClass('hidden');
                progressSection.addClass('hidden');
                submitButton.removeClass('hidden');
                messageDiv.addClass('bg-red-100 text-red-800').removeClass('hidden').text('Server error. Please try again later.');
            }
        });
        
        return false;
    });
    
    // Функция для обработки одного пакета
    function processBatch(processId) {
        $.ajax({
            url: csv_to_pdf_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'process_batch',
                nonce: csv_to_pdf_vars.nonce,
                process_id: processId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Обновляем прогресс
                    var progressBar = $('.progress-bar');
                    var progressText = $('.progress-text');
                    
                    progressBar.css('width', response.progress + '%').attr('aria-valuenow', response.progress);
                    
                    if (!response.is_completed) {
                        // Еще есть пакеты для обработки
                        progressText.text('Processing: ' + response.processed + ' of ' + response.total + ' documents (' + response.progress + '%)');
                        
                        // Обрабатываем следующий пакет
                        setTimeout(function() {
                            processBatch(processId);
                        }, 1000);
                    } else {
                        // Все пакеты обработаны
                        progressText.text('Completed! All documents processed successfully.');
                        
                        // Показываем кнопку скачивания
                        $('.csv-to-pdf-loader').addClass('hidden');
                        $('.csv-to-pdf-message').addClass('bg-green-100 text-green-800').removeClass('hidden').text('PDFs generated successfully!');
                        
                        $('.download-zip-button').attr('href', response.download_url);
                        $('.csv-to-pdf-download').removeClass('hidden');
                    }
                } else {
                    // В случае ошибки
                    $('.csv-to-pdf-loader').addClass('hidden');
                    $('.csv-to-pdf-progress').addClass('hidden');
                    $('.submit-button').removeClass('hidden');
                    $('.csv-to-pdf-message').addClass('bg-red-100 text-red-800').removeClass('hidden').text('Error: ' + response.message);
                }
            },
            error: function() {
                $('.csv-to-pdf-loader').addClass('hidden');
                $('.csv-to-pdf-progress').addClass('hidden');
                $('.submit-button').removeClass('hidden');
                $('.csv-to-pdf-message').addClass('bg-red-100 text-red-800').removeClass('hidden').text('Server error. Please try again later.');
            }
        });
    }
     
    // Add click handler for download button to reload page after download starts
    $('.download-zip-button').on('click', function() {
        // Set a short timeout to allow the download to start before reloading
        setTimeout(function() {
            window.location.reload();
        }, 1000); // 1 second delay
    });
});