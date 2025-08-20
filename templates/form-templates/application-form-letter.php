<?php
/**
 * Template for Application Letter Form with Tailwind CSS
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<!-- Подключение Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<div class="bg-white rounded-lg shadow-xl max-w-2xl mx-auto p-8 my-8 border border-gray-100">
    <h2 class="text-2xl font-bold text-gray-800 mb-6"><?php echo esc_html($template_title); ?></h2>
    
    <form class="pdf-form space-y-6" method="post">
        <input type="hidden" name="form_id" value="application-form-letter">
        <input type="hidden" name="pdf_template" value="<?php echo esc_attr($pdf_template_id); ?>">
        
        <!-- Полное имя -->
        <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                <?php _e('Full Name', 'csv-to-pdf-generator'); ?> <span class="text-red-500">*</span>
            </label>
            <input type="text" id="full_name" name="full_name" 
                class="w-full px-4 py-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
        </div>
        
        <!-- Заголовок документа -->
        <div>
            <input type="hidden" id="document_title" name="document_title" value="Cerere" 
                class="w-full px-4 py-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
        </div>
        
        <!-- Текст заявления -->
        <div>
            <label for="application_text" class="block text-sm font-medium text-gray-700 mb-1">
                <?php _e('Application Text', 'csv-to-pdf-generator'); ?> <span class="text-red-500">*</span>
            </label>
            <textarea id="application_text" name="application_text" rows="6" 
                class="w-full px-4 py-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required></textarea>
        </div>
        
        <!-- Дата -->
        <div>
            <label for="document_date" class="block text-sm font-medium text-gray-700 mb-1">
                <?php _e('Date', 'csv-to-pdf-generator'); ?> <span class="text-red-500">*</span>
            </label>
            <input type="date" id="document_date" name="document_date" value="<?php echo date('Y-m-d'); ?>" 
                class="w-full px-4 py-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" required>
        </div>
        
        <!-- Подпись -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <?php _e('Signature', 'csv-to-pdf-generator'); ?> <span class="text-red-500">*</span>
            </label>
            
            <div class="signature-container border-2 border-dashed border-gray-300 hover:border-blue-500 transition-colors rounded-lg overflow-hidden ">
                <canvas id="signature-pad" class="signature-pad w-full h-52 cursor-pointer touch-none"></canvas>
                <input type="hidden" name="signature" id="signature-data">
                
                <div class="flex justify-end items-center p-3 bg-white border-t border-gray-200">
                    <button type="button" id="clear-signature" class="flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 focus:ring-2 focus:ring-gray-400 text-gray-700 rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <?php _e('Clear', 'csv-to-pdf-generator'); ?>
                    </button>
                </div>
            </div>
            <p class="mt-1 text-sm text-gray-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?php _e('Please sign above using your mouse or touch screen', 'csv-to-pdf-generator'); ?>
            </p>
        </div>
        
        <div class="flex items-center justify-between pt-4">
            <!-- Кнопка отправки формы -->
            <button type="submit" id="submit-form" class="group relative inline-flex items-center justify-center overflow-hidden rounded-lg bg-gradient-to-br from-blue-600 to-blue-500 p-0.5 font-medium text-gray-900 hover:text-white focus:outline-none focus:ring-4 focus:ring-blue-300 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <span class="relative flex items-center rounded-md bg-white px-5 py-2.5 transition-all duration-75 ease-in group-hover:bg-opacity-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="group-hover:text-white"><?php _e('Generate PDF', 'csv-to-pdf-generator'); ?></span>
                </span>
            </button>
            

        </div>
        
        <!-- Сообщения и статусы -->
        <div class="pdf-form-message hidden mt-4 px-4 py-3 rounded-md"></div>
        
        <!-- Индикатор загрузки -->
        <div class="pdf-form-loader hidden">
            <div class="flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-gray-700"><?php _e('Generating PDF, please wait...', 'csv-to-pdf-generator'); ?></span>
            </div>
        </div>
        
        <!-- Секция скачивания -->
        <div class="pdf-form-download hidden">
            <a href="#" class="pdf-download-button flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <?php _e('Download PDF', 'csv-to-pdf-generator'); ?>
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация Signature Pad с улучшенными опциями для плавности
    var canvas = document.getElementById('signature-pad');
    var signaturePad = new SignaturePad(canvas, {
        backgroundColor: '#fff',
        penColor: 'rgb(0, 0, 139)',
        minWidth: 1,
        maxWidth: 2.5,
        throttle: 16, // для плавности линий
        velocityFilterWeight: 0.7
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
    
    // Добавим визуальный эффект при подписи
    canvas.addEventListener('mousedown', function() {
        document.querySelector('.signature-container').classList.add('ring-2', 'ring-blue-400');
    });
    
    document.addEventListener('mouseup', function() {
        document.querySelector('.signature-container').classList.remove('ring-2', 'ring-blue-400');
    });
    
    // Кнопка очистки подписи с анимацией
    document.getElementById('clear-signature').addEventListener('click', function() {
        signaturePad.clear();
        document.getElementById('signature-data').value = '';
        document.getElementById('submit-form').disabled = true;
        
        // Добавим небольшую анимацию при очистке
        var container = document.querySelector('.signature-container');
        container.classList.add('bg-red-50');
        setTimeout(function() {
            container.classList.remove('bg-red-50');
        }, 300);
    });
    
    // Проверяем наличие подписи при попытке отправить форму
    var form = document.querySelector('.pdf-form');
    var submitButton = document.getElementById('submit-form');
    
    signaturePad.addEventListener("endStroke", function() {
        if (!signaturePad.isEmpty()) {
            var signatureData = signaturePad.toDataURL();
            document.getElementById('signature-data').value = signatureData;
            submitButton.disabled = false;
            
            // Визуальный отклик при успешной подписи
            var container = document.querySelector('.signature-container');
            container.classList.add('bg-green-50');
            setTimeout(function() {
                container.classList.remove('bg-green-50');
            }, 300);
        } else {
            document.getElementById('signature-data').value = '';
            submitButton.disabled = true;
        }
    });
    
    // Проверка перед отправкой
    form.addEventListener('submit', function(e) {
        if (signaturePad.isEmpty()) {
            e.preventDefault();
            
            // Более стильное предупреждение
            var container = document.querySelector('.signature-container');
            container.classList.add('border-red-500', 'ring-2', 'ring-red-300');
            
            setTimeout(function() {
                container.classList.remove('border-red-500', 'ring-2', 'ring-red-300');
            }, 2000);
            
            // Показываем ошибку под полем подписи
            var message = document.querySelector('.pdf-form-message');
            message.textContent = 'Пожалуйста, поставьте подпись';
            message.classList.remove('hidden');
            message.classList.add('bg-red-100', 'text-red-800');
            
            setTimeout(function() {
                message.classList.add('hidden');
            }, 3000);
            
            return false;
        }
    });
    
    // Предзаполним поле даты текущим значением
    document.getElementById('document_date').value = '2025-08-19';
});
</script>

<style>
/* Дополнительные стили для подписи */
.signature-pad {
    touch-action: none;
    cursor: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAMAAAC6V+0/AAAAVFBMVEUAAAD///////////////////////////////////////////////////////////////////////////////////////////////////////////8wXzyWAAAAG3RSTlMAAwYOERUYIzA1PkRRWWJveYWImKq1yNHc6/to7QQSAAAAfklEQVQYGX3BSw6DIBBA0VdABIHir7bn/vfZaCSTGl5WJ5PMklM5/pw4xBs5hnDmUJXCGVUmG0hzlDmUQrYwfSbTYvOoChOpcjlLNGlrpPKgN7Lz5GZUd0p9EIqHqHKo9jN0/aVm0e7bNUCzVR9tGb9oVm15CHfS9jGnEL9yPFdG1ASF/AAAAABJRU5ErkJggg=='), pointer;
}

/* Плавная анимация для всех переходов */
* {
    transition: all 0.2s ease;
}
</style>