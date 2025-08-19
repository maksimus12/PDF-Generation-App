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
                    submitButton.removeClass('hidden');
                }
            },
            error: function() {
                loader.addClass('hidden');
                submitButton.removeClass('hidden');
                messageDiv.addClass('bg-red-100 text-red-800').removeClass('hidden').text('Server error. Please try again later.');
            }
        });
        
        return false;
    });
    
     // Add click handler for download button to reload page after download starts
    $('.download-zip-button').on('click', function() {
        // Set a short timeout to allow the download to start before reloading
        setTimeout(function() {
            window.location.reload();
        }, 1000); // 1 second delay
    });
});