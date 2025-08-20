<?php
/**
 * Template Name: Signed Document
 * Template ID: signed-document
 * Description: Template for documents with signature
 * Required Fields: full_name, document_date, document_title, document_content, signature
 * Default Font: cambria
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Получаем данные
$full_name = isset($row['full_name']) ? htmlspecialchars($row['full_name']) : '';
$document_date = isset($row['document_date']) ? htmlspecialchars($row['document_date']) : '';
$document_title = isset($row['document_title']) ? htmlspecialchars($row['document_title']) : '';
$document_content = isset($row['document_content']) ? htmlspecialchars($row['document_content']) : '';
$signature = isset($row['signature']) ? $row['signature'] : '';

// CSS стили
$css = "
body {
    font-family: Cambria, serif;
    font-size: 14px;
    line-height: 1.5;
    color: #000;
    margin: 0;
    padding: 20px;
}
h1 {
    font-size: 22px;
    text-align: center;
    margin-bottom: 20px;
}
.content {
    margin-bottom: 40px;
}
.signature-block {
    margin-top: 60px;
    border-top: 1px solid #ddd;
    padding-top: 20px;
}
.signature-line {
    margin-bottom: 10px;
}
.signature-image {
    max-height: 100px;
    max-width: 300px;
}
.signature-name {
    font-weight: bold;
}
.signature-date {
    margin-top: 10px;
    font-style: italic;
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
    <h1><?php echo $document_title; ?></h1>
    
    <div class="content">
        <?php echo nl2br($document_content); ?>
    </div>
    
    <div class="signature-block">
        <div class="signature-line">
            <?php if (!empty($signature)): ?>
                <img src="<?php echo $signature; ?>" class="signature-image" alt="Signature">
            <?php endif; ?>
        </div>
        <div class="signature-name"><?php echo $full_name; ?></div>
        <div class="signature-date">Дата: <?php echo date('d.m.Y', strtotime($document_date)); ?></div>
    </div>
</body>
</html>