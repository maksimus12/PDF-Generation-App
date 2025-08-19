<?php
/**
 * Template for Signature Form
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="pdf-form-container">
    <h2 class="pdf-form-title"><?php echo esc_html($template_title); ?></h2>
    
    <form class="pdf-form" method="post">
        <input type="hidden" name="form_id" value="signature-form">
        <input type="hidden" name="pdf_template" value="<?php echo esc_attr($pdf_template_id); ?>">
        
        <div class="pdf-form-group">
            <label for="full_name"><?php _e('Full Name', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="full_name" name="full_name" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="document_date"><?php _e('Date', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="date" id="document_date" name="document_date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="document_title"><?php _e('Document Title', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="document_title" name="document_title" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="document_content"><?php _e('Document Content', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <textarea id="document_content" name="document_content" rows="4" required></textarea>
        </div>
        
        <!-- Signature Field -->
        <div class="pdf-form-group">
            <label><?php _e('Signature', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <div class="signature-container">
                <canvas id="signature-pad" class="signature-pad" width="400" height="200"></canvas>
                <input type="hidden" name="signature" id="signature-data">
                <div class="signature-actions">
                    <button type="button" id="clear-signature" class="pdf-form-button pdf-form-button-secondary"><?php _e('Clear', 'csv-to-pdf-generator'); ?></button>
                </div>
            </div>
            <div class="pdf-form-description">
                <?php _e('Please sign above using your mouse or touch screen.', 'csv-to-pdf-generator'); ?>
            </div>
        </div>
        
        <button type="submit" id="submit-form" class="pdf-form-submit" disabled>
            <?php _e('Generate PDF', 'csv-to-pdf-generator'); ?>
        </button>
        
        <div class="pdf-form-message hidden"></div>
        
        <div class="pdf-form-loader hidden">
            <div class="spinner"></div>
            <span><?php _e('Generating PDF, please wait...', 'csv-to-pdf-generator'); ?></span>
        </div>
        
        <div class="pdf-form-download hidden">
            <a href="#" class="pdf-download-button" target="_blank">
                <?php _e('Download PDF', 'csv-to-pdf-generator'); ?>
            </a>
        </div>
    </form>
</div>

<style>
.signature-container {
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 10px;
    position: relative;
}

.signature-pad {
    width: 100%;
    height: 200px;
    background-color: #fff;
    border-radius: 4px;
}

.signature-actions {
    margin-top: 10px;
    text-align: right;
}

.pdf-form-button {
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}

.pdf-form-button-secondary {
    background-color: #f1f1f1;
    color: #333;
}

.pdf-form-button-secondary:hover {
    background-color: #e1e1e1;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация Signature Pad
    var canvas = document.getElementById('signature-pad');
    var signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });
    
    // Сделать canvas отзывчивым
    function resizeCanvas() {
        var ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); // иначе подпись масштабируется
    }
    
    // Вызываем сразу при загрузке и при изменении размера окна
    window.onresize = resizeCanvas;
    resizeCanvas();
    
    // Кнопка очистки подписи
    document.getElementById('clear-signature').addEventListener('click', function() {
        signaturePad.clear();
        document.getElementById('signature-data').value = '';
        document.getElementById('submit-form').disabled = true;
    });
    
    // Проверяем наличие подписи при попытке отправить форму
    var form = document.querySelector('.pdf-form');
    var submitButton = document.getElementById('submit-form');
    
    signaturePad.addEventListener("endStroke", function() {
        if (!signaturePad.isEmpty()) {
            var signatureData = signaturePad.toDataURL();
            document.getElementById('signature-data').value = signatureData;
            submitButton.disabled = false;
        } else {
            document.getElementById('signature-data').value = '';
            submitButton.disabled = true;
        }
    });
    
    // Проверка перед отправкой
    form.addEventListener('submit', function(e) {
        if (signaturePad.isEmpty()) {
            e.preventDefault();
            alert('Пожалуйста, поставьте подпись');
            return false;
        }
    });
});
</script>