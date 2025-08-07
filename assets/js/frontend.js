/**
 * Frontend JS for CSV to PDF Generator
 */
jQuery(document).ready(function($) {
    
    $('#csv-to-pdf-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = new FormData(form[0]);
        var messageDiv = form.find('.csv-to-pdf-message');
        var loader = form.find('.csv-to-pdf-loader');
        var downloadSection = form.find('.csv-to-pdf-download');
        var downloadButton = form.find('.download-zip-button');
        
        // Clear previous messages
        messageDiv.empty().removeClass('error success');
        
        // Validate file
        var fileInput = form.find('input[name="csv_file"]')[0];
        if (fileInput.files.length === 0) {
            messageDiv.addClass('error').text('Please select a CSV file');
            return false;
        }
        
        // Check file type
        var fileName = fileInput.files[0].name;
        var fileExt = fileName.split('.').pop().toLowerCase();
        
        if (fileExt !== 'csv') {
            messageDiv.addClass('error').text('Please upload a valid CSV file');
            return false;
        }
        
        // Show loader
        loader.show();
        downloadSection.hide();
        
        // Submit form via AJAX
        $.ajax({
            url: csv_to_pdf_vars.ajax_url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                loader.hide();
                
                if (response.success) {
                    messageDiv.addClass('success').text('PDFs generated successfully!');
                    
                    // Show download button and set link
                    downloadButton.attr('href', response.data.download_url);
                    downloadSection.show();
                } else {
                    messageDiv.addClass('error').text('Error: ' + response.data);
                }
            },
            error: function() {
                loader.hide();
                messageDiv.addClass('error').text('Server error. Please try again later.');
            }
        });
        
        return false;
    });
});