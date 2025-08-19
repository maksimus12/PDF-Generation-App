<?php
/**
 * Template for Student Contract Form
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="pdf-form-container">
    <h2 class="pdf-form-title"><?php echo esc_html($template_title); ?></h2>
    
    <form class="pdf-form" method="post">
        <input type="hidden" name="form_id" value="application-form">
        <input type="hidden" name="pdf_template" value="<?php echo esc_attr($pdf_template_id); ?>">
        
        <div class="pdf-form-group">
            <label for="number"><?php _e('Contract Number', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="number" name="number" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="date"><?php _e('Date', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="nameRo"><?php _e('Full Name (Romanian)', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="nameRo" name="nameRo" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="birthDate"><?php _e('Birth Date', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="date" id="birthDate" name="birthDate" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="country"><?php _e('Country', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="country" name="country" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="city"><?php _e('City', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="city" name="city" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="documentType"><?php _e('Document Type', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <select id="documentType" name="documentType" required>
                <option value="">-- <?php _e('Select', 'csv-to-pdf-generator'); ?> --</option>
                <option value="Passport"><?php _e('Passport', 'csv-to-pdf-generator'); ?></option>
                <option value="ID Card"><?php _e('ID Card', 'csv-to-pdf-generator'); ?></option>
            </select>
        </div>
        
        <div class="pdf-form-group">
            <label for="seria"><?php _e('Series', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="seria" name="seria" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="documentNumber"><?php _e('Document Number', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="documentNumber" name="documentNumber" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="documentId"><?php _e('Document ID', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="documentId" name="documentId" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="phone"><?php _e('Phone', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="tel" id="phone" name="phone" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="email"><?php _e('Email', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="faculty"><?php _e('Faculty', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="faculty" name="faculty" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="program"><?php _e('Program', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="program" name="program" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="yearsNumber"><?php _e('Years Number', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="yearsNumber" name="yearsNumber" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="yearsLetters"><?php _e('Years in Letters', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="yearsLetters" name="yearsLetters" required>
        </div>
        
        <div class="pdf-form-group">
            <label for="price"><?php _e('Price', 'csv-to-pdf-generator'); ?> <span class="pdf-form-required">*</span></label>
            <input type="text" id="price" name="price" required>
        </div>
        
        <button type="submit" class="pdf-form-submit">
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