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
                    
                    if (response.data.required_fields && Object.keys(response.data.required_fields).length > 0) {
                        var fieldsList = '';
                        $.each(response.data.required_fields, function(field, label) {
                            fieldsList += '<div class="mb-1"><code class="bg-gray-200 px-1 py-0.5 rounded">' + field + '</code> - ' + label + '</div>';
                        });
                        fieldsHTML = fieldsList;
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