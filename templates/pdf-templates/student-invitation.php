<?php
/**
 * Template Name: Student Invitation
 * Template ID: student-invitation
 * Description: Template for university invitations in English and Russian
 * Required Fields: number, date, fullNameEn, fullNameRu, salutation
 * Default Font: cambria
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Получаем пути к изображениям
$plugin_dir = plugin_dir_path(dirname(__FILE__, 2));
$uploads_dir = wp_upload_dir();

$headerImage = $plugin_dir . 'assets/images/udg_doc_header.png';
$signImage = $plugin_dir . 'assets/images/Sign.png';
$stampImage = $plugin_dir . 'assets/images/stamp.png';
$qrImage = $plugin_dir . 'assets/images/qr-code.png';

// Проверяем наличие файлов
$headerImage = file_exists($headerImage) ? $headerImage : '';
$signImage = file_exists($signImage) ? $signImage : '';
$stampImage = file_exists($stampImage) ? $stampImage : '';
$qrImage = file_exists($qrImage) ? $qrImage : '';

// Получаем данные из CSV
$student_number = isset($row['number']) ? htmlspecialchars($row['number']) : '';
$student_date = isset($row['date']) ? htmlspecialchars($row['date']) : date('d.m.Y');
$student_name_en = isset($row['fullNameEn']) ? htmlspecialchars($row['fullNameEn']) : '';
$student_name_ru = isset($row['fullNameRu']) ? htmlspecialchars($row['fullNameRu']) : '';
$student_salutation = isset($row['salutation']) ? htmlspecialchars($row['salutation']) : 'Уважаемый(ая)';

// Если нет имени на русском, используем английский вариант
if (empty($student_name_ru) && !empty($student_name_en)) {
    $student_name_ru = $student_name_en;
}

// CSS стили
$css = "
body {
    font-family: Cambria, DejaVu Serif, serif;
    font-size: 16px;
    line-height: 1.2;
    color: #000;
    margin: 0;
    padding: 0;
}
h1 {
    text-align: center;
    color: #000;
    margin-bottom: 10px;
    font-size: 20px;
}
h2 {
    text-align: center;
    color: #000;
    margin-bottom: 10px;
    font-size: 16px;
}
p {
    margin: 10px 0;
    text-align: justify;
    font-size: 18px;
}
strong {
    color: #000;
}
img {
    width: 100%;
    max-width: 100%;
}
.page {
    position: relative;
    padding: 20px;
    height: 100%;
    min-height: 95vh;
    box-sizing: border-box;
}
.page:first-child {
    page-break-after: always;
}
.footer {
    position: relative;
    width: 100%;
    text-align: center;
    padding-top: 10px;
    margin-top: 20px;
}

.qr-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    width: 100px;
}

.qr-container img {
    width: 100px !important;
    height: auto;
    display: block;
    margin-bottom: 5px;
}

.qr-container a {
    display: block;
    text-align: center;
    font-size: 14px;
}

.sign_stamp {
    position: relative;
    height: 80px;
    margin-bottom: 10px;
}
._sign {
    width: 250px !important;
    position: absolute;
    top: -55px;
    left: -25px;
    z-index: 20;
}
._stamp {
    position: absolute;
    left: 190px;
    top: 0;
    width: 130px !important;
}
.content {
    padding: 20px 30px;
}
.page-break {
    page-break-before: always;
}";

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Приглашение на учебу - <?php echo $student_name_en; ?></title>
    <style><?php echo $css; ?></style>
</head>
<body>
    <!-- English Page -->
    <div class="page">
        <?php if ($headerImage): ?>
            <img src="<?php echo $headerImage; ?>" alt="University Header">
        <?php endif; ?>
        <div class="content">
            <p><em><strong># <?php echo $student_number; ?> Date <?php echo $student_date; ?></strong></em></p>
            <br>
            <h1 style="text-align: center">STUDY INVITATION</h1>
            <br>
            <p>Dear <strong><?php echo $student_name_en; ?></strong>,</p>
            <p>We are pleased to inform you that you have been admitted to DIVITIA GRATIAE University as a student for the upcoming academic year beginning September 1, 2025. On behalf of the Admissions Office, I congratulate you on your successful admission and invite you to join our institution.</p>
            <p>DIVITIA GRATIAE University will provide you with full support. Our staff will carry out all the necessary legal procedures related to your studies at the University.</p>
            <p>We wish you a safe arrival and a blessed start of the academic year.</p>
            <br>
            <p>With respect,</p>
            <p>Provost of DIVITIA GRATIAE University</p>
            <div class="sign_stamp">
                <?php if ($signImage): ?>
                    <img class="_sign" src="<?php echo $signImage; ?>" alt="Signature">
                <?php endif; ?>
                <?php if ($stampImage): ?>
                    <img class="_stamp" src="<?php echo $stampImage; ?>" alt="Stamp">
                <?php endif; ?>
            </div>
            <h1 style="text-align: left">Dr. Iurie MALANCEA</h1>
        </div>
        <div class="footer">
            <?php if ($qrImage): ?>
                <div class="qr-container">
                    <img src="<?php echo $qrImage; ?>" alt="QR Code">
                    <a href="https://www.uni-dg.md">www.uni-dg.md</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Russian Page -->
    <div class="page page-break">
        <?php if ($headerImage): ?>
            <img src="<?php echo $headerImage; ?>" alt="University Header">
        <?php endif; ?>
        <div class="content">
            <p><em><strong>№ <?php echo $student_number; ?> от <?php echo $student_date; ?></strong></em></p>
            <br>
            <h1 style="text-align: center">ПРИГЛАШЕНИЕ НА УЧЕБУ</h1>
            <br>
            <p><?php echo $student_salutation; ?> <strong><?php echo $student_name_ru; ?></strong>,</p>
            <p>Мы рады сообщить Вам, что Вы были приняты в Университет DIVITIA GRATIAE в качестве студента на предстоящий учебный год, начинающийся 1 сентября 2025 года. От имени приемной комиссии я поздравляю Вас с успешным поступлением и приглашаю присоединиться к нашему учебному заведению.</p>
            <p>Университет DIVITIA GRATIAE окажет Вам всестороннюю поддержку. Наши сотрудники выполнят все необходимые юридические процедуры, связанные с вашим обучением в Университете.</p>
            <p>Желаем вам благополучного прибытия и благословенного начала учебного года.</p>
            <br>
            <p>С уважением,</p>
            <p>Ректор Университетa DIVITIA GRATIAE</p>
            <div class="sign_stamp">
                <?php if ($signImage): ?>
                    <img class="_sign" src="<?php echo $signImage; ?>" alt="Signature">
                <?php endif; ?>
                <?php if ($stampImage): ?>
                    <img class="_stamp" src="<?php echo $stampImage; ?>" alt="Stamp">
                <?php endif; ?>
            </div>
            <h1 style="text-align: left">Д-р Юрий МАЛАНЧА</h1>
        </div>
        <div class="footer">
            <?php if ($qrImage): ?>
                <div class="qr-container">
                    <img src="<?php echo $qrImage; ?>" alt="QR Code">
                    <a href="https://www.uni-dg.md">www.uni-dg.md</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>