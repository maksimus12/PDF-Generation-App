/**
 * JavaScript for Form to PDF Generator
 */
jQuery(document).ready(function($) {
    // Handle form submission
    $('.pdf-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = new FormData(form[0]);
        var messageDiv = form.find('.pdf-form-message');
        var loader = form.find('.pdf-form-loader');
        var downloadSection = form.find('.pdf-form-download');
        var submitButton = form.find('.pdf-form-submit');
        
        // Clear previous messages
        messageDiv.empty().addClass('hidden').removeClass('error success');
        
        // Add action
        formData.append('action', 'process_pdf_form');
        formData.append('nonce', pdf_form_vars.nonce);
        
        // Show loader
        loader.removeClass('hidden');
        submitButton.prop('disabled', true);
        downloadSection.addClass('hidden');
        
        $.ajax({
            url: pdf_form_vars.ajax_url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                loader.addClass('hidden');
                
                if (response.success) {
                    // Show success message
                    messageDiv.removeClass('hidden error').addClass('success')
                        .text(response.data.message || 'PDF generated successfully!');
                    
                    // Show download link
                    downloadSection.removeClass('hidden')
                        .find('a').attr('href', response.data.download_url);
                    
                    // Optionally reset form
                    // form[0].reset();
                } else {
                    // Show error message
                    messageDiv.removeClass('hidden success').addClass('error')
                        .text('Error: ' + (response.data || 'Unknown error'));
                    submitButton.prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                loader.addClass('hidden');
                messageDiv.removeClass('hidden success').addClass('error')
                    .text('Server error: ' + error);
                submitButton.prop('disabled', false);
                console.error(xhr.responseText);
            }
        });
        
        return false;
    });
});