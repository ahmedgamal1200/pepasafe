document.addEventListener('DOMContentLoaded', function() {

    const excelInput = document.getElementById('excel-input-model-1');
    const warningCard = document.getElementById('warning-card');
    const warningCardMessage = document.getElementById('warning-card-message');
    const warningCardIcon = document.getElementById('warning-card-icon');

    if (excelInput) {
        excelInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (!file) {
                warningCard.classList.add('hidden');
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, {
                        type: 'array'
                    });
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const range = XLSX.utils.decode_range(worksheet['!ref']);
                    const rowCount = range.e.r; // عدد الصفوف بعد صف العناوين

                    if (rowCount > 0) {
                        fetchDocumentPrice(rowCount);
                    } else {
                        updateWarningCard({ status: 'info', message: 'ملف الإكسل فارغ.' });
                    }
                } catch (error) {
                    console.error("خطأ في قراءة ملف الإكسل:", error);
                    updateWarningCard({ status: 'error', message: 'حدث خطأ أثناء محاولة قراءة ملف الإكسل.' });
                }
            };
            reader.readAsArrayBuffer(file);
        });
    }

    /**
     * إرسال عدد الصفوف إلى الخادم عبر AJAX واستقبال تفاصيل السعر
     * @param {int} count عدد الصفوف
     */
    function fetchDocumentPrice(count) {
        updateWarningCard({ status: 'loading', message: `تم العثور على ${count} صف. جاري حساب التكلفة...` });

        fetch('/calculate-document-price', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                count: count
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('فشل الاتصال بالخادم');
                }
                return response.json();
            })
            .then(data => {
                // استدعاء الدالة الجديدة لعرض الرسالة بناءً على البيانات المستلمة
                updateWarningCard(data);
            })
            .catch(error => {
                console.error('خطأ في طلب AJAX:', error);
                updateWarningCard({ status: 'error', message: 'حدث خطأ أثناء حساب السعر. الرجاء المحاولة مرة أخرى.' });
            });
    }

    /**
     * عرض الرسائل الديناميكية في بطاقة التحذير بناءً على البيانات من الخادم
     * @param {object} data الكائن المستلم من الخادم
     */
    // استبدل دالة updateWarningCard بالكامل بهذا الكود

    function updateWarningCard(data) {
        let messageHtml = '';
        let cardType = 'info'; // 'success', 'warning', 'error', 'info'

        switch (data.status) {
            case 'in_plan':
                // رسالة جديدة للسيناريو الأول بناءً على طلبك
                messageHtml = `
                            ${window.i18n.issue_docs_message
                            .replace(':docs_count', `<strong>${data.docs_count}</strong>`)
                            .replace(':total_cost', `<strong>${data.total_cost}</strong>`)
                            .replace(':plan_balance_after', `<strong>${data.plan_balance_after}</strong>`)}
                            `;


                cardType = 'success';
                break;

            case 'partial_plan':
                // تحديث الرسالة لتطابق أسماء الحقول الجديدة
                messageHtml = `
    ${window.i18n.issue_docs_wallet_message
                    .replace(':docs_count', `<strong>${data.docs_count}</strong>`)
                    .replace(':covered_by_plan_count', `<strong>${data.covered_by_plan_count}</strong>`)
                    .replace(':extra_docs_count', `<strong>${data.extra_docs_count}</strong>`)
                    .replace(':extra_cost', `<strong>${data.extra_cost}</strong>`)
                    .replace(':current_wallet_balance', `<strong>${data.current_wallet_balance}</strong>`)
                    .replace(':wallet_balance_after', `<strong>${data.wallet_balance_after}</strong>`)}
`;

                cardType = 'warning';
                break;

            case 'insufficient_funds':
            case 'error':
                messageHtml = data.message;
                cardType = 'error';
                break;

            case 'loading':
                messageHtml = data.message;
                cardType = 'info';
                break;

            default:
                messageHtml = data.message || 'حدث خطأ غير متوقع.';
                cardType = 'error';
                break;
        }

        // --- هذا الجزء هو الذي تم تعديله ---
        warningCardMessage.innerHTML = messageHtml;
        // أضفنا الكلاس 'mb-4' هنا لإضافة مسافة سفلية
        warningCard.className = 'mt-6 mb-4 max-w-5xl mx-auto rounded-lg p-6 flex items-start gap-4';
        warningCardIcon.className = 'text-3xl mt-1';
        switch (cardType) {
            case 'success':
                warningCard.classList.add('bg-green-100', 'border', 'border-green-400', 'text-green-800');
                warningCardIcon.classList.add('fas', 'fa-check-circle');
                break;
            case 'warning':
                warningCard.classList.add('bg-yellow-100', 'border', 'border-yellow-400', 'text-yellow-800');
                warningCardIcon.classList.add('fas', 'fa-exclamation-triangle');
                break;
            case 'error':
                warningCard.classList.add('bg-red-100', 'border', 'border-red-400', 'text-red-800');
                warningCardIcon.classList.add('fas', 'fa-times-circle');
                break;
            default:
                warningCard.classList.add('bg-blue-100', 'border', 'border-blue-400', 'text-blue-800');
                warningCardIcon.classList.add('fas', 'fa-info-circle');
                if (data.status === 'loading') warningCardIcon.classList.add('fa-spin');
                break;
        }
        warningCard.classList.remove('hidden');
    }
});
