<?php
/**
 * Template Name: Application Letter
 * Template ID: application-letter
 * Description: Template for applications addressed to the Rector of DIVITIA GRATIAE University
 * Required Fields: full_name, document_title, application_text, document_date, signature
 * Default Font: cambria
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Получаем данные
$full_name = isset($row['full_name']) ? htmlspecialchars($row['full_name']) : '';
$document_date = isset($row['document_date']) ? htmlspecialchars($row['document_date']) : '';
$document_title = isset($row['document_title']) ? htmlspecialchars($row['document_title']) : 'Cerere';
$application_text = isset($row['application_text']) ? htmlspecialchars($row['application_text']) : '';
$signature = isset($row['signature']) ? $row['signature'] : '';

// Получаем пути к изображениям
$plugin_dir = plugin_dir_path(dirname(__FILE__, 2));
$headerImage = $plugin_dir . 'assets/images/udg_doc_header.png';

// Проверяем наличие файла
$headerImage = file_exists($headerImage) ? $headerImage : '';

// Форматирование даты в более читаемый формат
$formatted_date = !empty($document_date) ? date('d.m.Y', strtotime($document_date)) : date('d.m.Y');

// CSS стили
$css = "
body {
    font-family: Cambria, DejaVu Serif, serif;
    font-size: 14px;
    line-height: 1.5;
    color: #000;
    margin: 0;
    padding: 20px;
}
.header {
    margin-bottom: 60px;
}

.page{
       padding: 20px !important;
}
.recipient {
    text-align: right;
    margin-bottom: 10px;
    font-size: 16px;
    line-height: 1.6;
}
.title {
    font-size: 18px;
    text-align: center;
    margin: 40px 0 30px 0;
    font-weight: bold;
}
.content {
    margin-bottom: 60px;
    min-height: 200px;
    text-align: justify;
    
}

/* --- signature block for page 6 (mpdf-friendly) --- */
.signature-block{
    margin-top: 36px;
    width: 100%;
    box-sizing: border-box;
}

.sig-left {
    width: 300px;
    float: left; 
    box-sizing: border-box; 
    text-align: left;
    font-family: Cambria, DejaVu Serif, serif;
}

.sig-right{
    width: 300px;
    float: right;
    box-sizing: border-box; 
    text-align: right;
    font-family: Cambria, DejaVu Serif, serif;
}

.sig-right img{
    max-width: 160px;
    margin-bottom: 5px;
}

.sig-left p{
    text-align: left;
}

";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $document_title; ?></title>
    <style><?php echo $css; ?></style>
</head>
<body>
    <?php if ($headerImage): ?>
        <img src="<?php echo $headerImage; ?>" alt="University Header">
    <?php endif; ?>
    <div class="page">
    
    <div class="recipient">
        <p><strong>Rectorului Universității „DIVITIA GRATIAE"</strong><br>
        Iurie Malancea<br>
        De la <?php echo $full_name; ?></p>
    </div>
    
    <div class="title">
        <?php echo $document_title; ?>
    </div>
    
    <div class="content">
        <?php echo nl2br($application_text); ?>
    </div>
    
    <div class="signature-block">
                <div class="sig-left">
                    
                    <p>Data: <?php echo $formatted_date; ?></p>
                    
                </div>
                <div class="sig-right">
                    <?php if (!empty($signature)): ?>
                    <img src="<?php echo $signature; ?>" class="signature-image" alt="Signature">
                    <?php endif; ?>
                </div>
            </div>
            
            </div>
    
</body>
</html>