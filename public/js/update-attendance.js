
    // ==========================================================
    // الخطوة 1: تعريف المتغيرات والدالة بشكل عام (Global)
    // ==========================================================
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    const toastIcon = document.getElementById('toast-icon');

    /**
    * دالة عامة لإظهار رسالة منبثقة (Toast)
    * @param {string} message - نص الرسالة
    * @param {string} type - 'success' أو 'error'
    */
    function showToast(message, type = 'success') {
    if (!toast || !toastMessage || !toastIcon) {
    console.error("Toast elements not found!");
    return;
}

    toast.classList.remove('hidden', 'opacity-0', 'translate-x-5', 'border-green-200', 'border-red-200');
    toast.classList.add('opacity-100', 'translate-x-0');

    toastMessage.textContent = message;

    if (type === 'success') {
    toast.classList.add('border-green-200');
    toastIcon.className = 'fas fa-check-circle text-green-600';
} else {
    toast.classList.add('border-red-200');
    toastIcon.className = 'fas fa-times-circle text-red-600';
}

    // إخفاء التوست بعد 3 ثواني
    setTimeout(() => {
    toast.classList.remove('opacity-100', 'translate-x-0');
    toast.classList.add('opacity-0', 'translate-x-5');
    setTimeout(() => toast.classList.add('hidden'), 300);
}, 3000);
}

    // ==========================================================
    // الخطوة 2: تشغيل الأكواد التي تعتمد على تحميل الصفحة
    // ==========================================================
    document.addEventListener('DOMContentLoaded', function () {

    // الكود الخاص بـ "تفعيل حضور الكل يدوياً"
    const switches = document.querySelectorAll('input[type="checkbox"][data-template-id]');
    switches.forEach(switchInput => {
    switchInput.addEventListener('change', function () {
    const templateId = this.dataset.templateId;
    const isChecked = this.checked ? 1 : 0;

    fetch("{{ route('toggleAttendance') }}", {
    method: "POST",
    headers: {
    "Content-Type": "application/json",
    "X-CSRF-TOKEN": "{{ csrf_token() }}"
},
    body: JSON.stringify({
    template_id: templateId,
    status: isChecked
})
})
    .then(response => response.json())
    .then(data => {
    if (data.success) {
    showToast('تم التحديث بنجاح'); // <-- هذا سيستدعي الدالة العامة بنجاح

    const statusDiv = document.getElementById(`attendance-status-${templateId}`);
    if (statusDiv) {
    statusDiv.innerHTML = isChecked
    ? `<i class="fas fa-check-circle text-base sm:text-lg text-green-600"></i>
                                   <span class="text-sm sm:text-base font-medium text-green-700">تسجيل الحضور: مفعل</span>`
    : `<i class="fas fa-times-circle text-base sm:text-lg text-red-600"></i>
                                   <span class="text-sm sm:text-base font-medium text-red-700">تسجيل الحضور: غير مفعل</span>`;
}
} else {
    showToast('لم يتم التحديث: ' + data.message, 'error');
}
})
    .catch(error => {
    showToast('حصل خطأ في الاتصال بالسيرفر', 'error');
    console.error(error);
});
});
});

    // الكود الخاص بزر تفعيل حضور المستخدم الواحد (الذي أضفناه)
    $('.attendance-toggle').change(function() {
    var isChecked = $(this).is(':checked') ? 1 : 0;
    var userId = $(this).closest('[data-user-id]').data('user-id');

    $.ajax({
    url: '/update-attendance',
    type: 'POST',
    data: { user_id: userId, is_attendance: isChecked, _token: csrfToken },
    success: function(response) {
    if (response.status === 'success') {
    showToast('تم تحديث حالة الحضور بنجاح!', 'success'); // <-- وهذا أيضاً سيستدعيها بنجاح
} else {
    showToast('حدث خطأ: ' + response.message, 'error');
}
},
    error: function(xhr, status, error) {
    showToast("حدث خطأ أثناء الاتصال بالسيرفر.", 'error');
}
});
});
});
