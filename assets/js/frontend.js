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
        var submitButton = form.find('.submit-button');
        
        // Clear previous messages
        messageDiv.empty().removeClass('error success');
        downloadSection.hide();
        
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
        submitButton.hide(); 
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
                    
                    // Clear previous link before setting new one
                    downloadButton.attr('href', '#');
                    
                    // Set new link with slight delay to ensure proper initialization
                    setTimeout(function() {
                        downloadButton.attr('href', response.data.download_url);
                        downloadSection.show();
                    }, 100);
                } else {
                    messageDiv.addClass('error').text('Error: ' + response.data);
                }
            },
            error: function() {
                loader.hide();
                submitButton.show();
                messageDiv.addClass('error').text('Server error. Please try again later.');
            }
        });
        
        return false;
    });
    
    $(document).on('click', '.download-zip-button', function(e) {
        var href = $(this).attr('href');
        if (href === '#' || href === '') {
            e.preventDefault();
            alert('Please wait, download link is being prepared...');
        }
    });
        
     // Add click handler for download button to reload page after download starts
    $('.download-zip-button').on('click', function() {
        // Set a short timeout to allow the download to start before reloading
        setTimeout(function() {
            window.location.reload();
        }, 1000); // 1 second delay
    });
});
