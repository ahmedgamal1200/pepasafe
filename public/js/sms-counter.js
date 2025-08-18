document.addEventListener('DOMContentLoaded', function () {

    /**
     * دالة تقوم بتشغيل عداد الحروف وتحديث حقل مخفي
     * @param {string} textareaId - الـ ID الخاص بـ textarea
     * @param {string} counterId - الـ ID الخاص بـ div الذي يعرض العداد
     * @param {string} hiddenInputId - الـ ID الخاص بالحقل المخفي الذي سيخزن عدد الحروف
     */
    function initializeCharCounter(textareaId, counterId, hiddenInputId) {
        const textarea = document.getElementById(textareaId);
        const charCounter = document.getElementById(counterId);
        const hiddenInput = document.getElementById(hiddenInputId);

        // التأكد من وجود جميع العناصر المطلوبة
        if (!textarea || !charCounter || !hiddenInput) {
            console.error(`لم يتم العثور على أحد العناصر: ${textareaId}, ${counterId}, أو ${hiddenInputId}`);
            return;
        }

        function updateCounter() {
            const currentLength = textarea.value.length;

            // 1. تحديث العداد الظاهر للمستخدم (بدون حد أقصى)
            charCounter.textContent = `عدد الحروف: ${currentLength}`;

            // 2. تحديث قيمة الحقل المخفي ليتم إرسالها مع الفورم
            hiddenInput.value = currentLength;
        }

        // ربط دالة التحديث مع حدث الإدخال
        textarea.addEventListener('input', updateCounter);

        // تشغيل العداد عند تحميل الصفحة للتعامل مع أي بيانات قديمة
        updateCounter();
    }

    // ✨ استدعاء الدالة مع إضافة الـ ID الخاص بالحقل المخفي كمعامل ثالث
    initializeCharCounter(
        'attendance-message-input',
        'attendance-char-counter',
        'attendance-char-count-hidden'
    );

    initializeCharCounter(
        'document-message-input',
        'document-char-counter',
        'document-char-count-hidden'
    );

});
