// الجزء الخاص ب اظهار اسم الملف الخاص ب الوثيقة
document.addEventListener('DOMContentLoaded', function () {
    const fileInput1 = document.getElementById('excel-input-model-1');
    const fileNameDisplay1 = document.getElementById('file-name-display');

    const fileInput2 = document.getElementById('excel-input-model-2');
    const fileNameDisplay2 = document.getElementById('file-name-display-2');

    function setupFileInput(fileInput, fileNameDisplay) {
        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
                fileNameDisplay.classList.remove('hidden');
            } else {
                fileNameDisplay.classList.add('hidden');
            }
        });
    }

    // تهيئة الملفين
    setupFileInput(fileInput1, fileNameDisplay1);
    setupFileInput(fileInput2, fileNameDisplay2);
});


// الجزء الخاص باظهار اسم الملف في جزء الحضور
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('badge-excel-input-2');
    const fileNameDisplay = document.getElementById('badge-file-name-display');

    fileInput.addEventListener('change', function () {
        if (this.files.length > 0) {
            // إظهار اسم الملف الأول فقط
            fileNameDisplay.textContent = this.files[0].name;
            fileNameDisplay.classList.remove('hidden');
        } else {
            // إخفاء العنصر إذا لم يتم اختيار أي ملف
            fileNameDisplay.classList.add('hidden');
        }
    });
});





// الجزء الخاص ب ان الداتا تظهر بشكل لحظي جنب كلمة اعدادات

document.addEventListener('DOMContentLoaded', () => {
    const input = document.querySelector('input[name="document_title"]');
    const output = document.getElementById('model-title');

    if (input && output) {
        input.addEventListener('input', () => {
            output.textContent = input.value.trim() || ''; // لو فاضي ميظهرش حاجة
        });
    }
});





(function() {
    const container = document.getElementById('forms-container');
    const addBtn    = document.getElementById('add-card-btn');
    const template  = container.querySelector('.form-card');

    // عدِّل أرقام النماذج
    function updateNumbers() {
        container.querySelectorAll('.form-card').forEach((card, idx) => {
            const title = card.querySelector('h3');
            if (title) title.textContent = `${window.i18n.form_count} ${idx + 1}`;
        });
    }
    updateNumbers();

    // استنساخ بطاقة جديدة
    addBtn.addEventListener('click', () => {
        const clone = template.cloneNode(true);
        // ارجع الحقول الافتراضية
        clone.querySelectorAll('input').forEach(i => {
            if (i.type === 'text')     i.value   = '';
            if (i.type === 'checkbox') i.checked = false;
        });
        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        // شيل الـ id عشان تتجنّب التعارض
        clone.querySelectorAll('[id]').forEach(e => e.removeAttribute('id'));

        // زرّ الحذف
        const header = clone.querySelector('.flex.justify-between.items-center.mb-4');
        const delBtn = document.createElement('button');
        delBtn.type      = 'button';
        delBtn.className = 'text-red-600 hover:opacity-75 ml-2';
        delBtn.innerHTML = '<i class="fas fa-trash"></i>';
        delBtn.addEventListener('click', () => {
            clone.remove();
            updateNumbers();
        });
        header.appendChild(delBtn);

        container.appendChild(clone);
        updateNumbers();
    });





})();





// دالة تهيئة كارد واحد
function initValidity(card) {
    const select = card.querySelector('.certificate-validity');
    const datesBox = card.querySelector('.certificate-dates');
    // الحالة الابتدائية
    datesBox.classList.toggle('hidden', select.value !== 'temporary');
    // مستمع على التغيير
    select.addEventListener('change', () => {
        datesBox.classList.toggle('hidden', select.value !== 'temporary');
    });
}

// 1- شغّل التهيئة على كل الكروت الموجودة عند تحميل الصفحة
document.querySelectorAll('.form-card').forEach(initValidity);

// 2- بعد ما تضيف كارد جديد (داخل اللوجيك اللي بتستخدمه لإضافة الكارد)
// مجرد مثال لو الزر بيتحكّم في إضافة الكارد:
document.getElementById('add-card-btn').addEventListener('click', () => {
    // هنا شف الكارد المضاف حديثاً—مثلاً آخِر واحد في القائمة
    const all = document.querySelectorAll('.form-card');
    const newCard = all[all.length - 1];
    initValidity(newCard);
});













document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('forms-container');
    const addBtn = document.getElementById('add-card-btn');

    // دالة تهيئة كارد (إخفاء البطاقة وضبط الـ toggle)
    function initPresenceCard(card) {
        const presenceCard = card.querySelector('.presence-card');
        const toggle = card.querySelector('.toggle-presence');
        if (presenceCard && toggle) {
            presenceCard.classList.add('hidden');
            toggle.checked = false;
        }
    }

    // 1. هيّئ كل الكروت الموجودة عند التحميل
    container.querySelectorAll('.form-card').forEach(initPresenceCard);

    // 2. تفويض (delegation) حدث التغيير لإظهار/إخفاء البطاقة
    container.addEventListener('change', e => {
        if (!e.target.classList.contains('toggle-presence')) return;
        const card = e.target.closest('.form-card');
        const presenceCard = card.querySelector('.presence-card');
        if (e.target.checked) presenceCard.classList.remove('hidden');
        else presenceCard.classList.add('hidden');
    });

    // 3. بعد إضافة كارد جديد، مرّر عليه تهيئة أوليّة
    addBtn.addEventListener('click', () => {
        // تأجل قليلًا حتى ينضاف الكارد فعليًّا إلى DOM
        setTimeout(() => {
            const cards = container.querySelectorAll('.form-card');
            const newCard = cards[cards.length - 1];
            initPresenceCard(newCard);
        }, 0);
    });
});






// دالة تخفي/تظهر التواريخ بناءً على اختيار كل select
function toggleDatesForSelect(select) {
    const dates = select.closest('.form-card').querySelector('.cert-dates-new');
    if (!dates) return;
    // لو القيمة مؤقتة نبين، وإلا نخفي
    if (select.value === 'temporary') {
        dates.classList.remove('hidden');
    } else {
        dates.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // عند التحميل: هيّئ كل select موجود
    document.querySelectorAll('.cert-validity-new').forEach(toggleDatesForSelect);
});

// استمع لأي تغيير في أي select من النوع ده
document.addEventListener('change', e => {
    if (e.target.classList.contains('cert-validity-new')) {
        toggleDatesForSelect(e.target);
    }
});

// لو عندك زر "إضافة نموذج جديد" وبينفذ استنساخ للكارد:
document.getElementById('add-card-btn')?.addEventListener('click', () => {
    // ... الكود اللي بيعمل clone للكارد ويدخله في DOM ...
    // بعد الإضافة، هيّئ الـ select الجديد
    document.querySelectorAll('.form-card:last-child .cert-validity-new')
        .forEach(toggleDatesForSelect);
});






// دالة لتحديث أي toggle للحالة الصحيحة (مفعّل/غير مفعّل)
function updateUI(input) {
    const wrapper = input.closest('.presence-wrapper');
    const label   = wrapper.querySelector('.presence-label');
    const track   = wrapper.querySelector('.toggle-track');

    if (input.checked) {
        label.textContent = window.i18n.off_attendance;
        label.classList.replace('text-blue-600', 'text-blue-600');
        wrapper.classList.replace('border-blue-600', 'border-blue-600');
        wrapper.classList.replace('bg-blue-100', 'bg-blue-100');
        track.classList.replace('peer-checked:bg-blue-600', 'peer-checked:bg-blue-600');
    } else {
        label.textContent = window.i18n.on_attendance;
        label.classList.replace('text-blue-600', 'text-blue-600');
        wrapper.classList.replace('border-blue-600', 'border-blue-600');
        wrapper.classList.replace('bg-blue-100', 'bg-blue-100');
        track.classList.replace('peer-checked:bg-blue-600', 'peer-checked:bg-blue-600');
    }
}

// 1) event delegation لتغييرات الـ toggle
document.addEventListener('change', e => {
    if (e.target.matches('.toggle-presence')) {
        updateUI(e.target);
    }
});

// 2) عند الـ DOMContentLoaded نهيّئ الموجودين
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toggle-presence').forEach(updateUI);

    // 3) نحط observer على الحاوية اللي بتضيف فيها الكاردز
    const container = document.getElementById('forms-container');
    const obs = new MutationObserver(muts => {
        muts.forEach(m => {
            m.addedNodes.forEach(node => {
                if (node.nodeType === 1) {
                    // لو الكارد الجديد فيه toggle-presence، مهّئه
                    node.querySelectorAll('.toggle-presence').forEach(updateUI);
                }
            });
        });
    });
    obs.observe(container, { childList: true, subtree: true });
});





















// ------------------------------------------------



const cardData = {};
const certificateCardData = {};
const attendanceCardData = {};
let attendanceExcelData = { headers: [], data: [] };
let certificateExcelData = { headers: [], data: [] };

let currentlyDraggedFabricObject = null;
let startDragCanvas = null;
let draggingProxyElement = null;
let activeCanvas = null;
let isDragging = false;
let isPanning = false;
let lastPosX = 0;
let lastPosY = 0;

const STANDARD_SIZES = {
    'A4': { width: 794, height: 1123 },      // 210mm x 297mm @ 96 DPI
    'Letter': { width: 816, height: 1056 },  // 8.5in x 11in @ 96 DPI
    'Card': { width: 324, height: 204 },     // CR80 - 85.6mm x 54mm @ 96 DPI
    'A5': { width: 560, height: 794 },       // 148mm x 210mm @ 96 DPI
    'B5': { width: 665, height: 945 },        // 176mm x 250mm @ 96 DPI


    '10x15': { width: 378, height: 567 },
    '16:9': { width: 386, height: 684 },
    '16K': { width: 737, height: 1020 },
    '5x7': { width: 480, height: 673 },
    '5x8': { width: 480, height: 767 },
    '8x10': { width: 767, height: 960 },
    '8.5x13': { width: 816, height: 1247 },
    '9x13': { width: 336, height: 480 },
    'A0': { width: 3179, height: 4494 },
    'A1': { width: 2245, height: 3179 },
    'A2': { width: 1587, height: 2245 },
    'A3': { width: 1123, height: 1587 },
    'A3+': { width: 1243, height: 1826 },
    'A6': { width: 397, height: 559 },
    'A7': { width: 280, height: 397 },
    'A8': { width: 197, height: 280 },
    'A9': { width: 140, height: 197 },
    'A10': { width: 98, height: 140 },
    'B0': { width: 3779, height: 5346 },
    'B1': { width: 2676, height: 3779 },
    'B2': { width: 1890, height: 2676 },
    'B3': { width: 1361, height: 1890 },
    'B4': { width: 945, height: 1361 },
    'B6': { width: 472, height: 668 },
    'B7': { width: 332, height: 472 },
    'B8': { width: 235, height: 332 },
    'B9': { width: 166, height: 235 },
    'B10': { width: 117, height: 166 },
    'BusinessCard': { width: 322, height: 208 },
    'CR100': { width: 378, height: 265 },
    'Envelope#10': { width: 397, height: 911 },
    'EnvelopeC6': { width: 431, height: 612 },
    'EnvelopeDL': { width: 416, height: 831 },
    'F4': { width: 794, height: 1247 },
    'GovernmentLetter': { width: 767, height: 1009 },
    'HalfLetter': { width: 529, height: 816 },
    'ID-2': { width: 397, height: 280 },
    'IndianLegal': { width: 813, height: 1304 },
    'JISB4': { width: 971, height: 1376 },
    'JISB5': { width: 688, height: 971 },
    'JISB6': { width: 484, height: 688 },
    'Legal': { width: 816, height: 1346 },
    'MexicanLegal': { width: 813, height: 1285 },
    'PostCard': { width: 378, height: 559 },
    'Tabloid': { width: 1054, height: 1633 }
};


function getCardDataType(fileHub) {
    if (fileHub.classList.contains('attendance-filehub')) {
        console.log('Detected attendance file hub, using attendanceCardData');
        return attendanceCardData;
    }
    console.log('Detected certificate file hub, using certificateCardData');
    return certificateCardData;
}


(function() {
    const container = document.getElementById('forms-container');
    const addBtn    = document.getElementById('add-card-btn');
    const template  = container.querySelector('.form-card');

    // عدِّل أرقام النماذج
    function updateNumbers() {
        container.querySelectorAll('.form-card').forEach((card, idx) => {
            const title = card.querySelector('h3');
            if (title) title.textContent = `${window.i18n.form_count} ${idx + 1}`;

        });
    }
    updateNumbers();

    // استنساخ بطاقة جديدة
    addBtn.addEventListener('click', () => {
        const clone = template.cloneNode(true);
        // ارجع الحقول الافتراضية
        clone.querySelectorAll('input').forEach(i => {
            if (i.type === 'text')     i.value   = '';
            if (i.type === 'checkbox') i.checked = false;
        });
        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        // شيل الـ id عشان تتجنّب التعارض
        clone.querySelectorAll('[id]').forEach(e => e.removeAttribute('id'));

        // زرّ الحذف
        const header = clone.querySelector('.flex.justify-between.items-center.mb-4');
        const delBtn = document.createElement('button');
        delBtn.type      = 'button';
        delBtn.className = 'text-red-600 hover:opacity-75 ml-2';
        delBtn.innerHTML = '<i class="fas fa-trash"></i>';
        delBtn.addEventListener('click', () => {
            clone.remove();
            updateNumbers();
        });
        header.appendChild(delBtn);

        container.appendChild(clone);
        updateNumbers();
    });
})();

// دالة تهيئة كارد واحد
function initValidity(card) {
    const select = card.querySelector('.certificate-validity');
    const datesBox = card.querySelector('.certificate-dates');
    // الحالة الابتدائية
    datesBox.classList.toggle('hidden', select.value !== 'temporary');
    // مستمع على التغيير
    select.addEventListener('change', () => {
        datesBox.classList.toggle('hidden', select.value !== 'temporary');
    });
}

// 1- شغّل التهيئة على كل الكروت الموجودة عند تحميل الصفحة
document.querySelectorAll('.form-card').forEach(initValidity);

// 2- بعد ما تضيف كارد جديد (داخل اللوجيك اللي بتستخدمه لإضافة الكارد)
// مجرد مثال لو الزر بيتحكّم في إضافة الكارد:
document.getElementById('add-card-btn').addEventListener('click', () => {
    // هنا شف الكارد المضاف حديثاً—مثلاً آخِر واحد في القائمة
    const all = document.querySelectorAll('.form-card');
    const newCard = all[all.length - 1];
    initValidity(newCard);
});

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('forms-container');
    const addBtn = document.getElementById('add-card-btn');

    // دالة تهيئة كارد (إخفاء البطاقة وضبط الـ toggle)
    function initPresenceCard(card) {
        const presenceCard = card.querySelector('.presence-card');
        const toggle = card.querySelector('.toggle-presence');
        if (presenceCard && toggle) {
            presenceCard.classList.add('hidden');
            toggle.checked = false;
        }
    }

    // 1. هيّئ كل الكروت الموجودة عند التحميل
    container.querySelectorAll('.form-card').forEach(initPresenceCard);

    // 2. تفويض (delegation) حدث التغيير لإظهار/إخفاء البطاقة
    container.addEventListener('change', e => {
        if (!e.target.classList.contains('toggle-presence')) return;
        const card = e.target.closest('.form-card');
        const presenceCard = card.querySelector('.presence-card');
        if (e.target.checked) presenceCard.classList.remove('hidden');
        else presenceCard.classList.add('hidden');
    });

    // 3. بعد إضافة كارد جديد، مرّر عليه تهيئة أوليّة
    addBtn.addEventListener('click', () => {
        // تأجل قليلًا حتى ينضاف الكارد فعليًّا إلى DOM
        setTimeout(() => {
            const cards = container.querySelectorAll('.form-card');
            const newCard = cards[cards.length - 1];
            initPresenceCard(newCard);
        }, 0);
    });
});

// دالة تخفي/تظهر التواريخ بناءً على اختيار كل select
function toggleDatesForSelect(select) {
    const dates = select.closest('.form-card').querySelector('.cert-dates-new');
    if (!dates) return;
    // لو القيمة مؤقتة نبين، وإلا نخفي
    if (select.value === 'temporary') {
        dates.classList.remove('hidden');
    } else {
        dates.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // عند التحميل: هيّئ كل select موجود
    document.querySelectorAll('.cert-validity-new').forEach(toggleDatesForSelect);
});

// استمع لأي تغيير في أي select من النوع ده
document.addEventListener('change', e => {
    if (e.target.classList.contains('cert-validity-new')) {
        toggleDatesForSelect(e.target);
    }
});

// لو عندك زر "إضافة نموذج جديد" وبينفذ استنساخ للكارد:
document.getElementById('add-card-btn')?.addEventListener('click', () => {
    // ... الكود اللي بيعمل clone للكارد ويدخله في DOM ...
    // بعد الإضافة، هيّئ الـ select الجديد
    document.querySelectorAll('.form-card:last-child .cert-validity-new')
        .forEach(toggleDatesForSelect);
});

// دالة لتحديث أي toggle للحالة الصحيحة (مفعّل/غير مفعّل)
function updateUI(input) {
    const wrapper = input.closest('.presence-wrapper');
    const label   = wrapper.querySelector('.presence-label');
    const track   = wrapper.querySelector('.toggle-track');

    if (input.checked) {
        label.textContent = window.i18n.off_attendance;
        label.classList.replace('text-blue-600', 'text-blue-600');
        wrapper.classList.replace('border-blue-600', 'border-blue-600');
        wrapper.classList.replace('bg-blue-100', 'bg-blue-100');
        track.classList.replace('peer-checked:bg-blue-600', 'peer-checked:bg-blue-600');
    } else {
        label.textContent = window.i18n.on_attendance;
        label.classList.replace('text-blue-600', 'text-blue-600');
        wrapper.classList.replace('border-blue-600', 'border-blue-600');
        wrapper.classList.replace('bg-blue-100', 'bg-blue-100');
        track.classList.replace('peer-checked:bg-blue-600', 'peer-checked:bg-blue-600');
    }
}

// 1) event delegation لتغييرات الـ toggle
document.addEventListener('change', e => {
    if (e.target.matches('.toggle-presence')) {
        updateUI(e.target);
    }
});

// 2) عند الـ DOMContentLoaded نهيّئ الموجودين
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toggle-presence').forEach(updateUI);

    // 3) نحط observer على الحاوية اللي بتضيف فيها الكاردز
    const container = document.getElementById('forms-container');
    const obs = new MutationObserver(muts => {
        muts.forEach(m => {
            m.addedNodes.forEach(node => {
                if (node.nodeType === 1) {
                    // لو الكارد الجديد فيه toggle-presence، مهّئه
                    node.querySelectorAll('.toggle-presence').forEach(updateUI);
                }
            });
        });
    });
    obs.observe(container, { childList: true, subtree: true });
});

document.addEventListener('DOMContentLoaded', () => {
    const fontColorInput = document.getElementById('font-color');
    const fontSizeInput = document.getElementById('font-size');
    const fontFamilySelect = document.getElementById('font-family');
    const addHeaderTextBtn = document.getElementById('add-header-text-btn');
    const deleteBtn = document.getElementById('delete-text-btn');
    const templateDataExcelInput = document.getElementById('excel-input-model-2');
    const badgeExcelInput = document.getElementById('badge-excel-input-2');
    const finalizeBtn = document.getElementById('fabric-popup');


    let extractedHeaders = [];
    let currentlyDraggedFabricObject = null;
    let draggingProxyElement = null;
    let startDragCanvas = null;
    let activeCanvas = null;
    let isPanning = false;
    let lastPosX = 0;
    let lastPosY = 0;

    function getCardIdFromSideInput(sideInput) {
        const fileInputName = sideInput.previousElementSibling.name;
        const sideValue = sideInput.value;
        return `${fileInputName}-${sideValue}`;
    }

    function getCardIdFromSpecificCanvas(canvas) {
        for (const id in cardData) {
            if (cardData[id].fabricCanvas === canvas) return id;
        }
        return null;
    }

    // function saveITextObjectsFromSpecificCanvas(canvas, cardId, allCardData) {
    //     if (!canvas || !canvas.getObjects) {
    //         console.warn(`Cannot save objects: Invalid canvas for cardId ${cardId}`);
    //         return;
    //     }
    //     if (!allCardData || typeof allCardData !== 'object') {
    //         console.error(`Cannot save objects: Invalid allCardData provided for cardId ${cardId}`);
    //         return;
    //     }
    //     if (!allCardData[cardId]) {
    //         allCardData[cardId] = { objects: [], fabricCanvas: canvas };
    //     }
    //
    //     const objects = canvas.getObjects().filter(obj => obj.selectable && (obj.type === 'i-text' || obj.type === 'qr-code')).map(obj => {
    //         const baseProps = {
    //             type: obj.type,
    //             left: obj.left,
    //             top: obj.top,
    //             scaleX: obj.scaleX || 1,
    //             scaleY: obj.scaleY || 1,
    //             angle: obj.angle || 0
    //         };
    //         if (obj.type === 'i-text') {
    //             return {
    //                 ...baseProps,
    //                 text: obj.text,
    //                 fontFamily: obj.fontFamily || 'Arial',
    //                 fontSize: obj.fontSize || 20,
    //                 fill: obj.fill || '#000000',
    //                 textBaseline: obj.textBaseline && ['top', 'middle', 'bottom'].includes(obj.textBaseline) ? obj.textBaseline : 'top',
    //                 textAlign: obj.textAlign || 'left',
    //                 fontWeight: obj.fontWeight || 'normal',
    //                 zIndex: obj.zIndex || 1
    //             };
    //         } else if (obj.type === 'qr-code') {
    //             return {
    //                 ...baseProps,
    //                 subtype: obj.subtype,
    //                 width: obj.width * (obj.scaleX || 1),
    //                 height: obj.height * (obj.scaleY || 1)
    //             };
    //         }
    //     });
    //
    //     allCardData[cardId].objects = objects;
    //     allCardData[cardId].canvasWidth = canvas.width;
    //     allCardData[cardId].canvasHeight = canvas.height;
    //
    //     console.log(`Saved ${objects.length} objects for ${cardId}:`, objects);
    //
    //     let inputFieldName;
    //     let expectedPrefix;
    //     if (cardId.includes('attendance_template_data_file_path')) {
    //         inputFieldName = 'attendance_text_data';
    //         expectedPrefix = 'attendance_template_data_file_path';
    //     } else if (cardId.includes('document_template_file_path')) {
    //         inputFieldName = 'certificate_text_data';
    //         expectedPrefix = 'document_template_file_path';
    //     } else {
    //         console.error(`Unknown cardId type: ${cardId}. Cannot determine input field name.`);
    //         return;
    //     }
    //
    //     const inputField = document.querySelector(`input[name="${inputFieldName}"]`);
    //     if (!inputField) {
    //         console.error(`Input field ${inputFieldName} not found`);
    //         return;
    //     }
    //
    //     let currentInputData = {};
    //     try {
    //         if (inputField.value) {
    //             currentInputData = JSON.parse(inputField.value);
    //         }
    //     } catch (e) {
    //         console.error(`Error parsing existing ${inputFieldName} JSON for saving:`, e);
    //     }
    //
    //     currentInputData = {
    //         [cardId]: {
    //             canvasWidth: allCardData[cardId].canvasWidth,
    //             canvasHeight: allCardData[cardId].canvasHeight,
    //             objects: allCardData[cardId].objects
    //         }
    //     };
    //
    //     inputField.value = JSON.stringify(currentInputData);
    //     console.log(`Value set to ${inputFieldName} input (after saveITextObjects):`, inputField.value);
    // }


    function saveITextObjectsFromSpecificCanvas(canvas, cardId, allCardData) {
        if (!canvas || !canvas.getObjects) {
            console.warn(`Cannot save objects: Invalid canvas for cardId ${cardId}`);
            return;
        }
        if (!allCardData || typeof allCardData !== 'object') {
            console.error(`Cannot save objects: Invalid allCardData provided for cardId ${cardId}`);
            return;
        }
        if (!allCardData[cardId]) {
            allCardData[cardId] = { objects: [], fabricCanvas: canvas };
        }

        const texts = canvas.getObjects()
            .filter(obj => obj.selectable && (obj.type === 'i-text')) // نركز على i-text للفالديشن
            .map(obj => ({
                id: obj.id,
                text: obj.text,
                left: obj.left,
                top: obj.top,
                fontFamily: obj.fontFamily || 'Arial',
                fontSize: obj.fontSize || 20,
                fill: obj.fill || '#000000',
                angle: obj.angle || 0,
                textBaseline: obj.textBaseline && ['top', 'middle', 'bottom'].includes(obj.textBaseline) ? obj.textBaseline : 'top'
            }));

        const qrCodes = canvas.getObjects()
            .filter(obj => obj.selectable && obj.type === 'qr-code')
            .map(obj => ({
                id: obj.id,
                type: obj.type,
                left: obj.left,
                top: obj.top,
                scaleX: obj.scaleX || 1,
                scaleY: obj.scaleY || 1,
                angle: obj.angle || 0,
                subtype: obj.subtype,
                width: obj.width * (obj.scaleX || 1),
                height: obj.height * (obj.scaleY || 1)
            }));

        allCardData[cardId].objects = [...texts, ...qrCodes]; // نحافظ على التخزين القديم كـ objects لو بتحتاجه داخليًا
        allCardData[cardId].canvasWidth = canvas.width;
        allCardData[cardId].canvasHeight = canvas.height;

        console.log(`Saved ${texts.length} texts and ${qrCodes.length} QR codes for ${cardId}`);

        let inputFieldName;
        let expectedPrefix;
        if (cardId.includes('attendance_template_data_file_path')) {
            inputFieldName = 'attendance_text_data';
            expectedPrefix = 'attendance_template_data_file_path';
        } else if (cardId.includes('document_template_file_path')) {
            inputFieldName = 'certificate_text_data';
            expectedPrefix = 'document_template_file_path';
        } else {
            console.error(`Unknown cardId type: ${cardId}. Cannot determine input field name.`);
            return;
        }

        const inputField = document.querySelector(`input[name="${inputFieldName}"]`);
        if (!inputField) {
            console.error(`Input field ${inputFieldName} not found`);
            return;
        }

        let currentInputData = {};
        try {
            if (inputField.value) {
                currentInputData = JSON.parse(inputField.value);
            }
        } catch (e) {
            console.error(`Error parsing existing ${inputFieldName} JSON for saving:`, e);
        }

        // هنا البنية اللي الباك إند متوقعها
        currentInputData[cardId] = {
            type: cardId.includes('front') ? 'certificate-front' : 'certificate-back',
            canvasWidth: allCardData[cardId].canvasWidth,
            canvasHeight: allCardData[cardId].canvasHeight,
            texts: texts,
            qrCodes: qrCodes,
        };

        inputField.value = JSON.stringify(currentInputData);
        console.log(`Value set to ${inputFieldName} input (after saveITextObjects):`, inputField.value);
    }




    $(document).ready(function() {
        $('#documentGenerationForm').on('submit', function(e) {
            console.log('Submitting with current certificate_text_data:', $('#certificate_text_data').val());
        });
    });

    function restoreITextObjectsOnSpecificCanvas(specificCanvas, cardIdentifier, allCardData) {
        if (!specificCanvas || !(specificCanvas instanceof fabric.Canvas)) {
            console.error(`Invalid canvas for ${cardIdentifier}`);
            return;
        }

        let inputFieldName;
        if (cardIdentifier.includes('attendance_template_data_file_path')) {
            inputFieldName = 'attendance_text_data';
        } else if (cardIdentifier.includes('document_template_file_path')) {
            inputFieldName = 'certificate_text_data';
        } else {
            console.error(`Unknown cardIdentifier type: ${cardIdentifier}. Cannot determine input field name for restoration.`);
            return;
        }

        const inputField = document.querySelector(`input[name="${inputFieldName}"]`);
        if (!inputField) {
            console.warn(`Input field ${inputFieldName} not found for restoration.`);
            if (allCardData && allCardData[cardIdentifier] && allCardData[cardIdentifier].objects && allCardData[cardIdentifier].objects.length > 0) {
                console.log(`Restoring from allCardData as input field not found for ${cardIdentifier}`);
                restoreObjectsFromData(specificCanvas, allCardData[cardIdentifier].objects);
            }
            return;
        }

        let storedData = {};
        try {
            if (inputField.value) {
                storedData = JSON.parse(inputField.value);
            }
        } catch (e) {
            console.error(`Error parsing existing ${inputFieldName} JSON for restoration:`, e);
            storedData = {};
        }

        const objectsToRestore = storedData[cardIdentifier] ? storedData[cardIdentifier].objects : [];

        if (objectsToRestore.length === 0) {
            console.log(`No objects to restore for ${cardIdentifier} from ${inputFieldName}.`);
            return;
        }

        console.log(`Restoring ${objectsToRestore.length} objects for ${cardIdentifier} from ${inputFieldName}`);

        specificCanvas.remove(...specificCanvas.getObjects().filter(obj => obj.selectable));

        function restoreObjectsFromData(canvas, objectsData) {
            objectsData.forEach(data => {
                if (data.type === 'i-text') {
                    const textObject = new fabric.IText(data.text, {
                        id: data.id,
                        left: data.left,
                        top: data.top,
                        fontFamily: data.fontFamily,
                        fontSize: data.fontSize,
                        fill: data.fill,
                        selectable: true,
                        hasControls: true,
                        textBaseline: data.textBaseline && ['top', 'middle', 'bottom'].includes(data.textBaseline) ? data.textBaseline : 'top',
                        textAlign: data.textAlign || 'left',
                        fontWeight: data.fontWeight || 'normal',
                        zIndex: data.zIndex || 1,
                        scaleX: data.scaleX,
                        scaleY: data.scaleY,
                        angle: data.angle
                    });
                    canvas.add(textObject);
                } else if (data.type === 'qr-code') {
                    const qrImageUrl = '/assets/qr-code.jpg'; // استبدل برابط QR الفعلي من الخلفية
                    fabric.Image.fromURL(qrImageUrl, (img) => {
                        img.set({
                            left: data.left,
                            top: data.top,
                            scaleX: data.scaleX || 0.3, // تصغير الحجم
                            scaleY: data.scaleY || 0.3, // تصغير الحجم
                            angle: data.angle,
                            selectable: true,
                            hasControls: true,
                            type: 'qr-code',
                            subtype: data.subtype,
                            width: data.width || 100,
                            height: data.height || 100
                        });
                        canvas.add(img);
                        canvas.renderAll();
                    }, { crossOrigin: 'Anonymous' });
                }
            });
            canvas.renderAll();
        }

        restoreObjectsFromData(specificCanvas, objectsToRestore);
    }

    function isMouseInsideCanvas(canvasInstance, evt) {
        if (!canvasInstance || !canvasInstance.getElement()) return false;
        const rect = canvasInstance.getElement().getBoundingClientRect();
        return (
            evt.clientX >= rect.left &&
            evt.clientX <= rect.right &&
            evt.clientY >= rect.top &&
            evt.clientY <= rect.bottom
        );
    }


// تم إضافة explicitWidth و explicitHeight إلى توقيع الدالة مع قيم افتراضية 0
    function initializeTemplateCanvas(canvasElement, imageUrl, cardIdentifier, explicitWidth = 0, explicitHeight = 0) {
        if (!canvasElement) {
            console.error('Canvas element not provided or found.');
            return;
        }

        // ❌ تم حذف الكود هنا الذي كان يستدعي:
        // cardData[cardIdentifier].fabricCanvas.dispose();
        // لأن عملية التنظيف (Dispose) يجب أن تتم بالكامل وحصرياً في معالج حدث الحذف (removePreviewBtn.addEventListener)
        // لتجنب خطأ TypeError: Cannot read properties of undefined (reading 'removeChild')
        // عند محاولة التخلص من Canvas تم تدميره بالفعل أو إزالة عنصر الـ DOM الخاص به.


        // 🌟 التعديل هنا: إعطاء الأولوية للأبعاد الصريحة التي تم تمريرها
        let finalCanvasWidth = explicitWidth;
        let finalCanvasHeight = explicitHeight;

        if (finalCanvasWidth === 0 || finalCanvasHeight === 0) {
            // إذا لم يتم تمرير أبعاد صريحة (كالحالة عند أول رفع للصورة)، نقرأ من DOM
            const rect = canvasElement.getBoundingClientRect();
            finalCanvasWidth = rect.width;
            finalCanvasHeight = rect.height;
            // console.log(`[INIT] - ${cardIdentifier} أبعاد الـ Canvas HTML: ${finalCanvasWidth}x${finalCanvasHeight}`);
        }

        if (finalCanvasWidth === 0 || finalCanvasHeight === 0) {
            const parentContainer = canvasElement.parentElement;
            if (parentContainer && parentContainer.offsetWidth > 0 && parentContainer.offsetHeight > 0) {
                finalCanvasWidth = parentContainer.offsetWidth;
                finalCanvasHeight = parentContainer.offsetHeight;
            } else {
                // ملاحظة: تم الاحتفاظ بأبعاد افتراضية لتجنب القيم الصفرية
                finalCanvasWidth = 900;
                finalCanvasHeight = 600;
                console.warn(`Canvas dimensions are zero for ${cardIdentifier}. Using default width: ${finalCanvasWidth}, height: ${finalCanvasHeight}`);
            }
        }

        // لضمان أن الـ Canvas لا يقل عن حجم أدنى
        if (finalCanvasWidth < 400) finalCanvasWidth = 400;
        if (finalCanvasHeight < 300) finalCanvasHeight = 300;
        // console.log(`[INIT] - ${cardIdentifier} الأبعاد النهائية للـ Fabric Canvas: ${finalCanvasWidth}x${finalCanvasHeight}`);

        canvasElement.style.width = `${finalCanvasWidth}px`;
        canvasElement.style.height = `${finalCanvasHeight}px`;
        canvasElement.width = finalCanvasWidth;
        canvasElement.height = finalCanvasHeight;
        canvasElement.style.display = 'block';

        const currentCanvas = new fabric.Canvas(canvasElement, {
            selection: true,
            width: finalCanvasWidth,
            height: finalCanvasHeight,
            preserveObjectStacking: true
        });
        currentCanvas.cardIdentifier = cardIdentifier;

        cardData[cardIdentifier] = cardData[cardIdentifier] || {};
        cardData[cardIdentifier].fabricCanvas = currentCanvas;

        // تعيين الكانفاس النشط
        currentCanvas.on('mouse:down', function() {
            activeCanvas = this;
            // console.log(`Mouse down, activeCanvas set to: ${this.cardIdentifier}`);
        });

        // إعداد محرر النصوص
        const isAttendance = cardIdentifier.includes('attendance_template_data_file_path');
        const editorPanelId = isAttendance ? 'attendance-text-editor-panel' : 'text-editor-panel';
        const textEditorPanel = document.getElementById(editorPanelId);
        const textContent = document.getElementById(isAttendance ? 'attendance-text-content' : 'text-content');
        const fontSize = document.getElementById(isAttendance ? 'attendance-font-size' : 'font-size');
        const fontColor = document.getElementById(isAttendance ? 'attendance-font-color' : 'font-color');
        const fontFamily = document.getElementById(isAttendance ? 'attendance-font-family' : 'font-family');

        // دالة لتحديث النص المحدد
        function updateSelectedText() {
            if (!activeCanvas) return;
            const activeObject = activeCanvas.getActiveObject();
            if (activeObject && activeObject.type === 'i-text') {
                if (textContent.value) activeObject.set('text', textContent.value);
                activeObject.set({
                    fontSize: parseInt(fontSize.value) || 20,
                    fill: fontColor.value,
                    fontFamily: fontFamily.value
                });
                activeCanvas.renderAll();
                // يجب تمرير cardData هنا بدلاً من المتغير العام
                // saveITextObjectsFromSpecificCanvas(activeCanvas, activeCanvas.cardIdentifier, cardData);
            }
        }

        // ربط مستمعات الأحداث للمحرر
        if (textContent) textContent.addEventListener('input', updateSelectedText);
        if (fontSize) fontSize.addEventListener('change', updateSelectedText);
        if (fontColor) fontColor.addEventListener('input', updateSelectedText);
        if (fontFamily) fontFamily.addEventListener('change', updateSelectedText);

        // مستمعات الكانفاس لتحديث المحرر
        currentCanvas.on('selection:created', () => {
            const activeObject = currentCanvas.getActiveObject();
            if (activeObject && activeObject.type === 'i-text' && textEditorPanel) {
                textContent.value = activeObject.text || '';
                fontSize.value = activeObject.fontSize || 20;
                fontColor.value = activeObject.fill || '#000000';
                fontFamily.value = activeObject.fontFamily || 'Arial';
                textEditorPanel.classList.remove('hidden');
            }
        });

        currentCanvas.on('selection:updated', () => {
            const activeObject = currentCanvas.getActiveObject();
            if (activeObject && activeObject.type === 'i-text' && textEditorPanel) {
                textContent.value = activeObject.text || '';
                fontSize.value = activeObject.fontSize || 20;
                fontColor.value = activeObject.fill || '#000000';
                fontFamily.value = activeObject.fontFamily || 'Arial';
                textEditorPanel.classList.remove('hidden');
            }
        });

        currentCanvas.on('selection:cleared', () => {
            if (textEditorPanel) {
                textEditorPanel.classList.add('hidden');
                textContent.value = '';
                fontSize.value = 20;
                fontColor.value = '#000000';
                fontFamily.value = 'Arial';
            }
        });

        const imageUrlToLoad = cardData[cardIdentifier].imageUrl || imageUrl;
        let finalImageUrl;

        // 🌟🌟 التحقق من نوع الـ URL وتطبيق Cache Busting 🌟🌟
        if (imageUrlToLoad && imageUrlToLoad.startsWith('data:')) {
            // إذا كان Base64 URI، نستخدمه كما هو
            finalImageUrl = imageUrlToLoad;
        } else if (imageUrlToLoad) {
            // إذا كان URL عادي، نضيف Timestamp لكسر الـ Cache وإجبار التحميل بالحجم الكامل
            finalImageUrl = `${imageUrlToLoad}?t=${new Date().getTime()}`;
        } else {
            console.warn(`No image URL provided for canvas initialization: ${cardIdentifier}`);
            // لا نحتاج لتهيئة الخلفية، لكن قد نحتاج لإعادة العناصر النصية
            restoreITextObjectsOnSpecificCanvas(currentCanvas, cardIdentifier, cardData);
            currentCanvas.renderAll();
            return currentCanvas;
        }


        fabric.Image.fromURL(finalImageUrl, function(img) { // استخدام finalImageUrl
            if (!img) {
                console.error('Failed to load image for canvas initialization.');
                return;
            }

            const scaleX = finalCanvasWidth / img.width;
            const scaleY = finalCanvasHeight / img.height;

            // التحجيم الصحيح لضمان عدم القص أو الفراغات
            const scale = Math.min(scaleX, scaleY);
            // console.log(`[INIT] - ${cardIdentifier} تحجيم صورة الخلفية: Scale=${scale.toFixed(3)}, Original=${img.width}x${img.height}`);

            currentCanvas.setBackgroundImage(img, currentCanvas.renderAll.bind(currentCanvas), {
                scaleX: scale,
                scaleY: scale,
                originX: 'center',
                originY: 'center',
                top: finalCanvasHeight / 2,
                left: finalCanvasWidth / 2,
                absolutePositioned: true
            });

            // استعادة عناصر النص بعد تحميل الخلفية
            restoreITextObjectsOnSpecificCanvas(currentCanvas, cardIdentifier, cardData);
            currentCanvas.renderAll();
        }, { crossOrigin: 'Anonymous' });

        // --------------------------------------------------------------------------------
        // ... باقي منطق السحب والإفلات (كما هو) ...
        // --------------------------------------------------------------------------------

        currentCanvas.on('mouse:down', function(opt) {
            const evt = opt.e;
            const target = opt.target;
            // التعديل 1: دعم QR Code في mouse:down
            if (target && (target.type === 'i-text' || target.type === 'qr-code')) {
                isDragging = true;
                currentlyDraggedFabricObject = target;
                startDragCanvas = this;

                target.set({ opacity: 0, selectable: false, evented: false });
                startDragCanvas.renderAll();

                draggingProxyElement = document.createElement('div');
                draggingProxyElement.style.position = 'fixed';
                draggingProxyElement.style.zIndex = '99999';
                draggingProxyElement.style.pointerEvents = 'none';

                if (target.type === 'i-text') {
                    draggingProxyElement.textContent = target.text;
                    draggingProxyElement.style.backgroundColor = 'rgba(0, 0, 255, 0.6)';
                    draggingProxyElement.style.color = 'white';
                    draggingProxyElement.style.padding = '5px 10px';
                    draggingProxyElement.style.borderRadius = '5px';
                    draggingProxyElement.style.fontFamily = target.fontFamily;
                    draggingProxyElement.style.fontSize = `${target.fontSize * target.scaleX}px`;
                } else if (target.type === 'qr-code') {
                    draggingProxyElement.textContent = 'QR Code';
                    draggingProxyElement.style.backgroundColor = 'rgba(255, 165, 0, 0.8)';
                    draggingProxyElement.style.color = 'black';
                    draggingProxyElement.style.padding = '5px 10px';
                    draggingProxyElement.style.borderRadius = '5px';
                }

                document.body.appendChild(draggingProxyElement);
            }
        });

        document.addEventListener('mousemove', (evt) => {
            if (isDragging && currentlyDraggedFabricObject && draggingProxyElement) {
                draggingProxyElement.style.left = `${evt.clientX}px`;
                draggingProxyElement.style.top = `${evt.clientY}px`;
            }
        });

        document.addEventListener('mouseup', (evt) => {
            // تصحيح خطأ targetCanvas is not defined (تحديد النطاق)
            let targetCanvas = null;
            let targetCardId = null;

            if (isDragging && currentlyDraggedFabricObject) {
                // تحديد الكانفاس المستهدف
                for (const id in cardData) {
                    const canvasInstance = cardData[id].fabricCanvas;
                    if (canvasInstance && canvasInstance.getElement() &&
                        canvasInstance.getElement().offsetWidth > 0 &&
                        canvasInstance.getElement().offsetHeight > 0) {
                        if (isMouseInsideCanvas(canvasInstance, evt)) {
                            targetCanvas = canvasInstance;
                            targetCardId = id;
                            break;
                        }
                    }
                }

                if (targetCanvas && targetCanvas !== startDragCanvas) {
                    // منطق الإفلات الناجح على كانفاس آخر
                    const pointer = targetCanvas.getPointer(evt, true);

                    startDragCanvas.remove(currentlyDraggedFabricObject);
                    // حفظ التغييرات على الكانفاس الأصلي (الحذف)
                    if (cardData[startDragCanvas.cardIdentifier]) {
                        saveITextObjectsFromSpecificCanvas(startDragCanvas, startDragCanvas.cardIdentifier, cardData);
                    }

                    if (currentlyDraggedFabricObject.type === 'qr-code') {

                        // 💥 التعديل الأهم لحل مشكلة تكرار QR Code (حذف القديم قبل إضافة الجديد)
                        targetCanvas.getObjects().filter(obj => obj.type === 'qr-code').forEach(qr => {
                            targetCanvas.remove(qr);
                        });

                        const qrImageUrl = '/assets/qr-code.jpg';
                        const originalObject = currentlyDraggedFabricObject; // حفظ مرجع
                        const targetCard = targetCardId;

                        // إنشاء كائن QR Code جديد بشكل غير متزامن
                        fabric.Image.fromURL(qrImageUrl, (img) => {
                            // التحقق من نجاح التحميل أولاً (يحل مشكلة الاختفاء عند فشل التحميل)
                            if (!img) {
                                console.error('Failed to load QR code image for drag and drop. Reverting drag.');
                                return;
                            }

                            img.set({
                                left: pointer.x,
                                top: pointer.y,
                                scaleX: originalObject.scaleX,
                                scaleY: originalObject.scaleY,
                                angle: originalObject.angle,
                                selectable: true,
                                hasControls: true,
                                type: 'qr-code',
                                subtype: originalObject.subtype,
                                width: originalObject.width,
                                height: originalObject.height,
                                id: `qr_${Math.random().toString(36).substr(2, 9)}` // ID فريد
                            });

                            targetCanvas.add(img);
                            img.bringToFront();
                            targetCanvas.setActiveObject(img);
                            targetCanvas.renderAll();

                            // حفظ التغييرات على الكانفاس الهدف (الإضافة)
                            if (cardData[targetCard]) {
                                saveITextObjectsFromSpecificCanvas(targetCanvas, targetCard, cardData);
                            }
                            // console.log(`تم إفلات QR Code على ${targetCard} في (${pointer.x}, ${pointer.y})`);
                        }, { crossOrigin: 'Anonymous' }, (err) => {
                            // معالج خطأ مخصص للتحميل
                            console.error('Error loading QR code during drag-and-drop:', err);
                        });

                    } else if (currentlyDraggedFabricObject.type === 'i-text') {
                        // منطق I-Text
                        const newObject = new fabric.IText(currentlyDraggedFabricObject.text, {
                            left: pointer.x,
                            top: pointer.y,
                            fontFamily: currentlyDraggedFabricObject.fontFamily,
                            fontSize: currentlyDraggedFabricObject.fontSize,
                            fill: currentlyDraggedFabricObject.fill,
                            selectable: true,
                            hasControls: true,
                            textBaseline: 'top',
                            scaleX: currentlyDraggedFabricObject.scaleX,
                            scaleY: currentlyDraggedFabricObject.scaleY,
                            angle: currentlyDraggedFabricObject.angle,
                            id: `text_${Math.random().toString(36).substr(2, 9)}` // ID فريد
                        });

                        targetCanvas.add(newObject);
                        newObject.bringToFront();
                        targetCanvas.setActiveObject(newObject);
                        targetCanvas.renderAll();

                        // حفظ التغييرات على الكانفاس الهدف (الإضافة)
                        if (cardData[targetCardId]) {
                            saveITextObjectsFromSpecificCanvas(targetCanvas, targetCardId, cardData);
                        }
                        // console.log(`تم إفلات النص '${newObject.text}' على ${targetCardId} في (${pointer.x}, ${pointer.y})`);
                    }

                } else {
                    // منطق الإفلات الفاشل أو الإفلات على نفس الكانفاس (إرجاع العنصر)
                    currentlyDraggedFabricObject.set({ opacity: 1, selectable: true, evented: true });
                    startDragCanvas.renderAll();
                    startDragCanvas.setActiveObject(currentlyDraggedFabricObject);
                    // console.log('تم إرجاع العنصر للكانفاس الأصلي.');
                }

                // خطوة حاسمة: مسح المتغيرات المؤقتة والـ Proxy لإنهاء عملية السحب
                if (draggingProxyElement) {
                    draggingProxyElement.remove();
                    draggingProxyElement = null;
                }
                currentlyDraggedFabricObject = null;
                isDragging = false;
                startDragCanvas = null;
            }
        });

        return currentCanvas;
    }
    // ملاحظة: افترض أن STANDARD_SIZES و saveITextObjectsFromSpecificCanvas
// و initializeTemplateCanvas دوال أو متغيرات عامة ومعدلة لتقبل cardDataRef.

    /**
     * تطبيق حجم القالب على مجموعة محددة من العناصر باستخدام بياناتها الخاصة.
     * * @param {string} selectedSizeKey - مفتاح الحجم المختار (مثل 'A4', 'Card').
     * @param selectedSizeKey
     * @param {string} containerSelector - محدد CSS للحاوية الرئيسية (مثل '.certificate-filehub' أو '.attendance-filehub').
     * @param {object} cardDataRef - مرجع لكائن البيانات (certificateCardData أو attendanceCardData).
     */
    /**
     * تطبيق حجم القالب على مجموعة محددة من العناصر باستخدام بياناتها الخاصة.
     * @param {string} selectedSizeKey - مفتاح الحجم المختار (مثل 'A4', 'Card').
     * @param {string} containerSelector - محدد CSS للحاوية الرئيسية.
     * @param {object} cardDataRef - مرجع لكائن البيانات.
     * @param {string} currentOrientation - الاتجاه الحالي ('portrait' أو 'landscape'). 👈 مُعامل إضافي
     */
    function applyTemplateSize(selectedSizeKey, containerSelector, cardDataRef, currentOrientation = 'portrait') {
        const targetFileHub = document.querySelector(containerSelector);

        if (!targetFileHub) {
            console.warn(`Target file hub not found for selector: ${containerSelector}`);
            return;
        }

        // البحث عن الكروت داخل الحاوية المستهدفة فقط
        const allCardElements = targetFileHub.querySelectorAll('.filebox-card');
        const baseDimensions = STANDARD_SIZES[selectedSizeKey] || STANDARD_SIZES['A4'];

        let newDimensions = { ...baseDimensions }; // نسخ الأبعاد الأساسية

        // 💥 تطبيق الاتجاه (Rotation) 💥
        if (currentOrientation === 'landscape') {
            // إذا كان الاتجاه أفقيًا، يتم عكس العرض والارتفاع
            newDimensions.width = baseDimensions.height;
            newDimensions.height = baseDimensions.width;
        }
        // ------------------------------------

        if (allCardElements.length === 0) {
            console.warn(`No .filebox-card elements found in ${containerSelector}.`);
            return;
        }

        console.log(`[APPLY] - بدء تغيير الحجم إلى: ${selectedSizeKey} (${currentOrientation}) لـ ${containerSelector}`);

        allCardElements.forEach(cardElement => {
            const canvasEl = cardElement.querySelector('canvas');
            const cardId = canvasEl ? canvasEl.getAttribute('data-card-id') : null;
            const cardData = cardDataRef;

            // 🛑 حفظ الاتجاه والمقاس في كائن البيانات
            if (cardData[cardId]) {
                cardData[cardId].sizeKey = selectedSizeKey;
                cardData[cardId].orientation = currentOrientation;
            }

            if (!canvasEl || !cardId || !cardData[cardId] || !cardData[cardId].imageUrl) {
                // تطبيق الأبعاد الجديدة
                cardElement.style.width = `${newDimensions.width}px`;
                cardElement.style.height = `${newDimensions.height}px`;
                return;
            }

            const currentCanvas = cardData[cardId].fabricCanvas;
            let scaleFactor = 1;

            if (currentCanvas) {
                const oldWidth = currentCanvas.width;
                const oldHeight = currentCanvas.height;

                saveITextObjectsFromSpecificCanvas(currentCanvas, cardId, cardData);

                // حساب معامل التحجيم
                if (oldWidth > 0 && oldHeight > 0) {
                    const widthScale = newDimensions.width / oldWidth;
                    const heightScale = newDimensions.height / oldHeight;
                    scaleFactor = Math.min(widthScale, heightScale);
                }

                // 1. تدمير Canvas القديم
                currentCanvas.dispose();
                cardData[cardId].fabricCanvas = null;
            }

            // 2. تطبيق أبعاد الحاوية وعنصر الـ Canvas HTML الجديدة
            cardElement.style.width = `${newDimensions.width}px`;
            cardElement.style.height = `${newDimensions.height}px`;

            canvasEl.style.width = `${newDimensions.width}px`;
            canvasEl.style.height = `${newDimensions.height}px`;
            canvasEl.width = newDimensions.width;
            canvasEl.height = newDimensions.height;

            // 3. تطبيق التحجيم على العناصر المحفوظة
            if (scaleFactor !== 1 && cardData[cardId].iTextObjects.length > 0) {
                cardData[cardId].iTextObjects.forEach(obj => {
                    obj.scaleX *= scaleFactor;
                    obj.scaleY *= scaleFactor;
                    obj.left *= scaleFactor;
                    obj.top *= scaleFactor;
                });
            }

            // 4. إعادة التهيئة
            requestAnimationFrame(() => {
                setTimeout(() => {
                    if (cardData[cardId] && cardData[cardId].imageUrl) {
                        initializeTemplateCanvas(
                            canvasEl,
                            cardData[cardId].imageUrl,
                            cardId,
                            cardData // تمرير مرجع البيانات
                        );

                        if (cardData[cardId].iTextObjects.length > 0 && cardData[cardId].fabricCanvas) {
                            restoreITextObjectsOnSpecificCanvas(cardData[cardId].fabricCanvas, cardId, cardData);
                            cardData[cardId].fabricCanvas.renderAll();
                        }
                    }
                }, 50);
            });
        });

        console.log(`[APPLY] - انتهى تغيير الحجم لـ ${containerSelector}.`);
    }
// ----------------------------------------------------------------------------------

    const certSelect = document.getElementById('template-size-select');

    $(document).ready(function() {
        // 1. تهيئة Select2 لجميع العناصر التي تحمل الكلاس 'searchable'
        // (هذا يجب أن يكون موجوداً لتشغيل خاصية البحث)
        $('.searchable').select2({
            placeholder: "{{ trans_db('search_for_template_size') }}",
            allowClear: true,
        });

        // ------------------------------------------------------------------
        // 2. الاستماع لحدث التغيير الخاص بـ "الشهادات" (الكود الموجود لديك)
        // ------------------------------------------------------------------
        $('#template-size-select').on('change', function (event) {
            const selectedValue = $(this).val();

            applyTemplateSize(
                selectedValue,
                '.js-filehub:not(.attendance-filehub)', // مُحدد حاوية الشهادات
                certificateCardData                     // كائن بيانات الشهادات
            );
        });

        // ------------------------------------------------------------------
        // 3. إضافة الاستماع لحدث التغيير الخاص بـ "الحضور" (الكود المفقود)
        // ------------------------------------------------------------------
        $('#attendance-size-select').on('change', function (event) {
            const selectedValue = $(this).val();

            applyTemplateSize(
                selectedValue,
                // المُحدد الصحيح لحاوية الحضور فقط (نستخدم الكلاس الذي قمت باستثنائه في الشهادات)
                '.attendance-filehub',
                // كائن بيانات بطاقات الحضور (يجب أن يكون معرّفاً ومتاحاً)
                attendanceCardData
            );
        });

    });


    // ---------------------------------------------------------------------------------------






    function updateEditorControls(obj) {
        const editorPanel = document.getElementById('text-editor-panel');
        const textContentInput = document.getElementById('text-content');
        if (!editorPanel || !textContentInput) return;

        if (obj && obj.type === 'i-text') {
            editorPanel.classList.remove('hidden');
            textContentInput.value = obj.text || '';
            fontColorInput.value = obj.fill || '#000000';
            fontSizeInput.value = obj.fontSize || 20;
            fontFamilySelect.value = obj.fontFamily || 'Arial';
        }
    }



    function displayHeadersOnSpecificCanvas(canvas, headers, cardId, cardData, objectPositions = {}) {
        if (!canvas || !headers || !Array.isArray(headers)) return;

        const isAttendance = cardId.includes('attendance_template_data_file_path');
        const dataSource = isAttendance ? attendanceCardData : certificateCardData;

        // دالة مساعدة لتحديد النوع والجانب بناءً على cardId
        function getTypeAndSide(cardId) {
            if (cardId.includes('attendance_template_data_file_path')) {
                return { type: 'attendance', side: cardId.includes('-back') ? 'back' : 'front' };
            } else if (cardId.includes('document_template_file_path')) {
                return { type: 'certificate', side: cardId.includes('-back') ? 'back' : 'front' };
            }
            return { type: null, side: null };
        }

        const { type, side } = getTypeAndSide(cardId);

        headers.forEach((header, index) => {
            const position = objectPositions[header] || { left: 50, top: 50 + index * 30 };
            const existingObject = canvas.getObjects().find(obj => obj.text === header && obj.type === 'i-text');

            if (existingObject) {
                existingObject.set({
                    left: position.left,
                    top: position.top,
                    selectable: true,
                    hasControls: true
                });
                console.log(`Updated header "${header}" to canvas at (${position.left}, ${position.top})`);
            } else {
                const text = new fabric.IText(header, {
                    left: position.left,
                    top: position.top,
                    fontFamily: 'Arial',
                    fontSize: 20,
                    fill: '#000000',
                    selectable: true,
                    hasControls: true,
                    textBaseline: 'top', // تصحيح textBaseline
                    id: `text_${Math.random().toString(36).substr(2, 9)}`
                });
                canvas.add(text);
                console.log(`Added header "${header}" to canvas at (${position.left}, ${position.top})`);
            }
        });

        // إضافة رمز QR واحد فقط إذا لم يكن موجودًا
        if (type && side) {
            const qrSubType = `${type}-${side}`;
            const existingQR = canvas.getObjects().find(obj => obj.type === 'qr-code' && obj.subtype === qrSubType);
            if (!existingQR) {
                const qrImageUrl = '/assets/qr-code.jpg'; // استبدل بالرابط الفعلي من الخلفية
                fabric.Image.fromURL(qrImageUrl, (img) => {
                    if (!img) {
                        console.error(`Failed to load QR code image from ${qrImageUrl}`);
                        return;
                    }
                    img.set({
                        left: 100,
                        top: 100,
                        scaleX: 0.3, // تصغير الحجم
                        scaleY: 0.3, // تصغير الحجم
                        selectable: true,
                        hasControls: true,
                        type: 'qr-code',
                        subtype: qrSubType,
                        width: img.width || 100,
                        height: img.height || 100
                    });
                    canvas.add(img);
                    canvas.renderAll();
                    console.log(`Added QR code for ${qrSubType} at (100, 100)`);
                    saveITextObjectsFromSpecificCanvas(canvas, cardId, dataSource);
                }, { crossOrigin: 'Anonymous' }, (err) => {
                    console.error(`Error loading QR code from ${qrImageUrl}:`, err);
                });
            }
        }

        canvas.renderAll();
        if (typeof saveITextObjectsFromSpecificCanvas === 'function') {
            saveITextObjectsFromSpecificCanvas(canvas, cardId, dataSource);
        }
    }


    // إضافة تحكم يدوي (مثال بسيط)
    function addTextAlignmentControls(canvas) {
        const alignLeftBtn = document.createElement('button');
        alignLeftBtn.textContent = 'محاذاة شمال';
        alignLeftBtn.addEventListener('click', () => {
            const activeObject = canvas.getActiveObject();
            if (activeObject && activeObject.type === 'i-text') {
                activeObject.set('textAlign', 'left');
                canvas.renderAll();
            }
        });

        const alignCenterBtn = document.createElement('button');
        alignCenterBtn.textContent = 'محاذاة وسط';
        alignCenterBtn.addEventListener('click', () => {
            const activeObject = canvas.getActiveObject();
            if (activeObject && activeObject.type === 'i-text') {
                activeObject.set('textAlign', 'center');
                canvas.renderAll();
            }
        });

        const alignRightBtn = document.createElement('button');
        alignRightBtn.textContent = 'محاذاة يمين';
        alignRightBtn.addEventListener('click', () => {
            const activeObject = canvas.getActiveObject();
            if (activeObject && activeObject.type === 'i-text') {
                activeObject.set('textAlign', 'right');
                canvas.renderAll();
            }
        });

        document.body.appendChild(alignLeftBtn);
        document.body.appendChild(alignCenterBtn);
        document.body.appendChild(alignRightBtn);
    }

    // استدعاء التحكم بعد تهيئة الكانفاس
    initializeTemplateCanvas.prototype.addTextAlignmentControls = addTextAlignmentControls;




    function setupFileCard(fileCardElement, imageUrl = null) {
        const fileInput = fileCardElement.querySelector('.file-input');
        const fabricCanvasContainer = fileCardElement.querySelector('.fabric-canvas-container');
        const initialUploadState = fileCardElement.querySelector('.initial-upload-state');
        const removePreviewBtn = fileCardElement.querySelector('.remove-preview-btn');
        const sideInput = fileCardElement.querySelector('.side-input');
        const fileHub = fileCardElement.closest('.js-filehub.attendance-filehub') || fileCardElement.closest('.js-filehub');

        // تسجيل قيم fileHub وisAttendance للتحقق
        const isAttendance = fileHub?.classList.contains('attendance-filehub');
        console.log('Setting up file card for:', fileCardElement, 'FileHub:', fileHub?.className, 'isAttendance:', isAttendance);
        console.log('File input name before correction:', fileInput?.name);

        if (!fileHub) {
            console.error('No fileHub found for fileCardElement:', fileCardElement);
            return;
        }

        // تصحيح اسم الإدخال إذا كان غير صحيح لبطاقات الحضور
        if (isAttendance && fileInput && fileInput.name !== 'attendance_template_file_path[]') {
            console.warn(`Incorrect file input name detected for attendance card: ${fileInput.name}. Correcting to attendance_template_file_path[]`);
            fileInput.name = 'attendance_template_file_path[]';
        }

        // تصحيح اسم sideInput بناءً على isAttendance
        if (sideInput) {
            if (isAttendance && sideInput.name !== 'attendance_template_sides[]') {
                console.warn(`Incorrect side input name detected for attendance: ${sideInput.name}. Correcting to attendance_template_sides[]`);
                sideInput.name = 'attendance_template_sides[]';
            } else if (!isAttendance && sideInput.name !== 'certificate_template_sides[]') {
                console.warn(`Incorrect side input name detected for certificate: ${sideInput.name}. Correcting to certificate_template_sides[]`);
                sideInput.name = 'certificate_template_sides[]';
            }
            if (!sideInput.value) {
                sideInput.value = 'front';
                console.warn('sideInput.value was empty, defaulting to "front"');
            }
        } else {
            console.error('sideInput not found in fileCardElement:', fileCardElement);
        }

        console.log('File input name after correction:', fileInput?.name);

        const cardData = isAttendance ? attendanceCardData : certificateCardData;
        if (!cardData) {
            console.warn(`Invalid card data type for fileHub: ${fileHub.className}`);
            return;
        }

        const cardIdentifier = isAttendance
            ? `attendance_template_data_file_path-${sideInput?.value || 'front'}`
            : `document_template_file_path[]-${sideInput?.value || 'front'}`;
        console.log('Setting up file card with cardIdentifier:', cardIdentifier);

        if (!cardData[cardIdentifier]) {
            cardData[cardIdentifier] = {
                fabricCanvas: null,
                iTextObjects: [],
                imageUrl: null,
                objectPositions: {}
            };
        }

        // استخدام imageUrl الممرر أو القيمة الموجودة
        if (imageUrl) {
            cardData[cardIdentifier].imageUrl = imageUrl;
        } else if (!cardData[cardIdentifier].imageUrl && fileInput?.value) {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    cardData[cardIdentifier].imageUrl = event.target.result;
                    updateCardDisplayState(true);
                    initializeCanvas();
                };
                reader.readAsDataURL(file);
            }
        }

        let currentTemplateCanvasElement = null;

        if (!fileInput || !fabricCanvasContainer || !initialUploadState || !removePreviewBtn || !sideInput) {
            console.warn('One or more elements missing in fileCardElement for setupFileCard:', fileCardElement);
            return;
        }

        const excelInput = isAttendance
            ? document.getElementById('badge-excel-input-2')
            : document.getElementById('excel-input-model-2');

        if (!excelInput) {
            console.error(`Excel input not found for ${isAttendance ? 'attendance' : 'certificate'}:`, isAttendance ? 'badge-excel-input-2' : 'excel-input-model-2');
            return;
        }

        const updateCardDisplayState = (hasImage) => {
            if (hasImage) {
                initialUploadState.classList.add('hidden');
                fabricCanvasContainer.classList.remove('hidden');
                removePreviewBtn.style.display = 'flex';
            } else {
                initialUploadState.classList.remove('hidden');
                fabricCanvasContainer.classList.add('hidden');
                removePreviewBtn.style.display = 'none';
            }
        };

        const initializeCanvas = () => {
            if (cardData[cardIdentifier].imageUrl) {
                fabricCanvasContainer.querySelectorAll('canvas, iframe').forEach(el => el.remove());
                currentTemplateCanvasElement = document.createElement('canvas');
                currentTemplateCanvasElement.setAttribute('data-card-id', cardIdentifier);
                currentTemplateCanvasElement.setAttribute('id', `canvas-${cardIdentifier}`);
                currentTemplateCanvasElement.style.width = '100%';
                currentTemplateCanvasElement.style.height = '100%';
                currentTemplateCanvasElement.style.display = 'block';
                fabricCanvasContainer.prepend(currentTemplateCanvasElement);
                const canvas = initializeTemplateCanvas(currentTemplateCanvasElement, cardData[cardIdentifier].imageUrl, cardIdentifier, cardData);
                if (canvas) {
                    cardData[cardIdentifier].fabricCanvas = canvas;
                    activeCanvas = canvas;
                    console.log(`Active canvas set to: ${cardIdentifier}`);
                    setupDragDrop(cardIdentifier);
                }
            } else {
                fabricCanvasContainer.innerHTML = '';
                if (!fabricCanvasContainer.contains(fileInput)) {
                    const fileInputWrapper = fileInput.parentElement || fileInput;
                    fabricCanvasContainer.appendChild(fileInputWrapper);
                    updateCardDisplayState(false);
                }
            }
            restoreITextObjects(cardIdentifier);
        };

        function restoreITextObjects(cardIdentifier) {
            const cardDataItem = cardData[cardIdentifier];
            if (cardDataItem.fabricCanvas && cardDataItem.iTextObjects.length) {
                cardDataItem.iTextObjects.forEach(objData => {
                    const newObj = new fabric.IText(objData.text, {
                        left: objData.left,
                        top: objData.top,
                        fontFamily: objData.fontFamily || 'Arial',
                        fontSize: objData.fontSize || 20,
                        fill: objData.fill || '#000000',
                        selectable: true,
                        hasControls: true,
                    });
                    cardDataItem.fabricCanvas.add(newObj);
                });
                cardDataItem.fabricCanvas.renderAll();
                console.log(`Restored ${cardDataItem.iTextObjects.length} iTextObjects for ${cardIdentifier}`);
            } else {
                console.log(`No iTextObjects to restore for ${cardIdentifier}`);
            }
        }

        function setupDragDrop(cardIdentifier) {
            const canvas = cardData[cardIdentifier].fabricCanvas;
            if (!canvas) return;

            canvas.on('object:moving', (e) => {
                const obj = e.target;
                if (obj.type === 'i-text' || obj.type === 'qr-code') {
                    const pointer = canvas.getPointer(e.e);
                    const currentCanvasId = canvas.cardIdentifier || cardIdentifier;
                    const targetCanvasId = findTargetCanvas(e.e.clientX, e.e.clientY);

                    if (targetCanvasId && targetCanvasId !== currentCanvasId) {
                        moveObjectToCanvas(obj, currentCanvasId, targetCanvasId);
                    } else {
                        obj.set({
                            left: Math.max(0, Math.min(canvas.width - obj.width * obj.scaleX, pointer.x)),
                            top: Math.max(0, Math.min(canvas.height - obj.height * obj.scaleY, pointer.y))
                        });
                        canvas.renderAll();
                        activeCanvas = canvas;
                        updateObjectPosition(currentCanvasId, canvas, obj);
                    }
                }
            });

            canvas.on('object:modified', function(e) {
                const obj = e.target;
                if (obj.type === 'i-text' || obj.type === 'qr-code') {
                    activeCanvas = this;
                    updateObjectPosition(this.cardIdentifier, canvas, obj);
                }
            });

            canvas.on('mouse:down', function(e) {
                activeCanvas = this;
                console.log('Mouse down, activeCanvas set to:', this.cardIdentifier);
            });
        }

        function findTargetCanvas(x, y) {
            const canvases = document.querySelectorAll('canvas[data-card-id]');
            for (let canvasEl of canvases) {
                const rect = canvasEl.getBoundingClientRect();
                if (x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom && canvasEl.getAttribute('data-card-id') !== cardIdentifier) {
                    return canvasEl.getAttribute('data-card-id');
                }
            }
            return null;
        }

        function moveObjectToCanvas(obj, sourceId, targetId) {
            const sourceData = cardData[sourceId];
            const targetData = cardData[targetId];
            if (!sourceData || !targetData || !targetData.fabricCanvas) return;

            const sourceCanvasRect = sourceData.fabricCanvas.getElement().getBoundingClientRect();
            const targetCanvasRect = targetData.fabricCanvas.getElement().getBoundingClientRect();
            sourceData.fabricCanvas.remove(obj);
            const newObj = new fabric.IText(obj.text, {
                left: obj.left + (targetCanvasRect.left - sourceCanvasRect.left),
                top: obj.top + (targetCanvasRect.top - sourceCanvasRect.top),
                fontFamily: obj.fontFamily,
                fontSize: obj.fontSize,
                fill: obj.fill,
                selectable: true,
                hasControls: true,
            });
            targetData.fabricCanvas.add(newObj);
            sourceData.iTextObjects = sourceData.iTextObjects.filter(o => o.text !== obj.text);
            targetData.iTextObjects.push({
                text: newObj.text,
                left: newObj.left,
                top: newObj.top,
                fontFamily: newObj.fontFamily,
                fontSize: newObj.fontSize,
                fill: newObj.fill,
            });
            targetData.fabricCanvas.renderAll();
            console.log(`Moved ${obj.text} from ${sourceId} to ${targetId}`);
        }

        function updateObjectPosition(cardIdentifier, canvas, object) {
            // if (!canvas || !object || !object.id) { // التحقق من وجود الـ ID
            //     console.error('Invalid canvas or object ID in updateObjectPosition');
            //     return;
            // }

            let inputFieldName;
            if (cardIdentifier.includes('attendance_template_data_file_path')) {
                inputFieldName = 'attendance_text_data';
            } else if (cardIdentifier.includes('document_template_file_path')) {
                inputFieldName = 'certificate_text_data';
            } else {
                console.error(`Unknown cardIdentifier type: ${cardIdentifier}`);
                return;
            }

            const inputField = document.querySelector(`input[name="${inputFieldName}"]`);
            if (!inputField) {
                console.error(`Input field ${inputFieldName} not found`);
                return;
            }

            let currentInputData = {};
            try {
                if (inputField.value) {
                    currentInputData = JSON.parse(inputField.value);
                }
            } catch (e) {
                console.error(`Error parsing ${inputFieldName} JSON:`, e);
            }

            if (!currentInputData[cardIdentifier]) {
                currentInputData[cardIdentifier] = {
                    canvasWidth: canvas.width,
                    canvasHeight: canvas.height,
                    texts: [], // استخدم 'texts' بدلاً من 'objects' لتوضيح المحتوى
                    qrCodes: []
                };
            }

            const texts = currentInputData[cardIdentifier].texts || [];
            const qrCodes = currentInputData[cardIdentifier].qrCodes || [];

            // الحل هنا: ابحث عن العنصر بالـ ID
            const objectIndex = texts.findIndex(obj => obj.id === object.id);
            const qrCodeIndex = qrCodes.findIndex(obj => obj.id === object.id);

            const objectData = {
                id: object.id,
                type: object.type,
                left: object.left,
                top: object.top,
                scaleX: object.scaleX || 1,
                scaleY: object.scaleY || 1,
                angle: object.angle || 0
            };

            if (object.type === 'i-text') {
                // ... (باقي البيانات كما هي)
                objectData.text = object.text;
                objectData.fontFamily = object.fontFamily || 'Arial';
                objectData.fontSize = object.fontSize || 20;
                objectData.fill = object.fill || '#000000';
                objectData.textBaseline = object.textBaseline && ['top', 'middle', 'bottom'].includes(object.textBaseline) ? object.textBaseline : 'top';
                objectData.textAlign = object.textAlign || 'left';
                objectData.fontWeight = object.fontWeight || 'normal';
                objectData.zIndex = object.zIndex || 1;

                if (objectIndex !== -1) {
                    texts[objectIndex] = objectData;
                } else {
                    texts.push(objectData);
                }
            } else if (object.type === 'qr-code') {
                // ... (باقي البيانات كما هي)
                objectData.subtype = object.subtype;
                objectData.width = object.width * (object.scaleX || 1);
                objectData.height = object.height * (object.scaleY || 1);

                if (qrCodeIndex !== -1) {
                    qrCodes[qrCodeIndex] = objectData;
                } else {
                    qrCodes.push(objectData);
                }
            }

            currentInputData[cardIdentifier].texts = texts;
            currentInputData[cardIdentifier].qrCodes = qrCodes;
            currentInputData[cardIdentifier].canvasWidth = canvas.width;
            currentInputData[cardIdentifier].canvasHeight = canvas.height;

            inputField.value = JSON.stringify(currentInputData);
            console.log(`Value set to ${inputFieldName} input:`, inputField.value);
            console.log(`Updated position for ${object.type === 'i-text' ? object.text : object.subtype} in ${cardIdentifier}`, objectData);
        }

        function saveITextObjectsFromSpecificCanvas(cardId, canvas) {
            if (!canvas || !cardId) {
                console.warn('Cannot save iTextObjects: Invalid canvas or cardId', cardId);
                return;
            }
            const cardDataItem = cardData[cardId];
            cardDataItem.iTextObjects = canvas.getObjects().filter(obj => obj.type === 'i-text').map(obj => ({
                text: obj.text,
                left: obj.left,
                top: obj.top,
                fontFamily: obj.fontFamily,
                fontSize: obj.fontSize,
                fill: obj.fill,
            }));
            console.log(`Saved iTextObjects for ${cardId}:`, cardDataItem.iTextObjects.length);
        }

        updateCardDisplayState(cardData[cardIdentifier].imageUrl);

        excelInput.addEventListener('change', () => {
            console.log(`Excel input changed for ${isAttendance ? 'attendance' : 'certificate'}`);
            readFirstDataRow(isAttendance, (excelInfo) => {
                if (excelInfo && excelInfo.headers && excelInfo.data) {
                    if (isAttendance) attendanceExcelData = excelInfo;
                    else certificateExcelData = excelInfo;
                    if (cardData[cardIdentifier].fabricCanvas && !cardIdentifier.includes('-back')) {
                        displayHeadersOnSpecificCanvas(cardData[cardIdentifier].fabricCanvas, excelInfo.headers, cardIdentifier, cardData);
                        cardData[cardIdentifier].fabricCanvas.renderAll();
                    }
                }
            });
        });

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    cardData[cardIdentifier].imageUrl = event.target.result;
                    updateCardDisplayState(true);
                    initializeCanvas();
                };
                reader.readAsDataURL(file);
            } else {
                updateCardDisplayState(false);
            }
        });

        removePreviewBtn.addEventListener('click', () => {
            // 1. مسح قيمة حقل الملفات
            fileInput.value = '';

            // 2. التخلص من كائن Fabric Canvas (مهم لتحرير الموارد)
            if (cardData[cardIdentifier].fabricCanvas) {
                cardData[cardIdentifier].fabricCanvas.dispose();
                cardData[cardIdentifier].fabricCanvas = null;
            }

            // 3. **الخطوة الحاسمة لإزالة عنصر <canvas> الذي تم إنشاؤه ديناميكياً:**
            // نستخدم querySelectorAll لإزالة عنصر <canvas> تحديداً.
            fabricCanvasContainer.querySelectorAll('canvas, iframe').forEach(el => el.remove());

            // 4. إفراغ ما تبقى في الحاوية (قد يكون عناصر DOM أخرى)
            // **ملاحظة:** بما أن الزر الأحمر (removePreviewBtn) في كودك الأصلي موجود داخل fabric-canvas-container،
            // فإن إفراغ innerHTML قد يحذفه. سنقوم بالتنظيف ثم إعادة إضافته لضمان وجوده في مكانه الثابت.
            fabricCanvasContainer.innerHTML = '';
            fabricCanvasContainer.appendChild(removePreviewBtn);

            // 5. مسح المراجع
            cardData[cardIdentifier].imageUrl = null;
            currentTemplateCanvasElement = null;

            // 6. تحديث حالة العرض (إظهار زر الرفع الأولي وإخفاء الحاوية الحالية)
            updateCardDisplayState(false);
        });

        // إضافة حدث للتحقق من أسماء الحقول عند إرسال النموذج
        const form = document.querySelector('#documentGenerationForm');
        if (form && !form.dataset.submitListenerAdded) {
            form.addEventListener('submit', (e) => {
                // e.preventDefault(); // منع الإرسال للاختبار
                const fileInputs = form.querySelectorAll('input[type="file"]');
                fileInputs.forEach(input => {
                    console.log('Input name before submit:', input.name);
                    const parentFileHub = input.closest('.js-filehub.attendance-filehub');
                    if (parentFileHub && input.name !== 'attendance_template_file_path[]') {
                        console.warn(`Correcting input name before submit from ${input.name} to attendance_template_file_path[]`);
                        input.name = 'attendance_template_file_path[]';
                    }
                });
                const sideInputs = form.querySelectorAll('input.side-input');
                sideInputs.forEach(input => {
                    console.log('Side input name before submit:', input.name);
                    const parentFileHub = input.closest('.js-filehub.attendance-filehub');
                    if (parentFileHub && input.name !== 'attendance_template_sides[]') {
                        console.warn(`Correcting side input name before submit from ${input.name} to attendance_template_sides[]`);
                        input.name = 'attendance_template_sides[]';
                    } else if (!parentFileHub && input.name !== 'template_sides[]') {
                        console.warn(`Correcting side input name before submit from ${input.name} to template_sides[]`);
                        input.name = 'template_sides[]';
                    }
                });
                console.log('Attendance sides:', Array.from(form.querySelectorAll('input[name="attendance_template_sides[]"]')).map(input => input.value));
                console.log('Certificate sides:', Array.from(form.querySelectorAll('input[name="template_sides[]"]')).map(input => input.value));
                console.log('Document sides (should be empty):', Array.from(form.querySelectorAll('input[name="document_template_sides[]"]')).map(input => input.value));
                console.log('Attendance files:', Array.from(form.querySelectorAll('input[name="attendance_template_file_path[]"]')).map(input => input.value));
                console.log('Document files:', Array.from(form.querySelectorAll('input[name="document_template_file_path[]"]')).map(input => input.value));
            });
            form.dataset.submitListenerAdded = 'true';
        }

        if (cardData[cardIdentifier].imageUrl) initializeCanvas();
    }



    function toggleViewMode(isTwoSided) {
        const frontCardIdentifier = 'document_template_file_path[]-front';
        const backCardIdentifier = 'document_template_file_path[]-back';
        const attendanceFrontIdentifier = 'attendance_template_data_file_path-front';
        const attendanceBackIdentifier = 'attendance_template_data_file_path-back';

        const frontImageUrl = certificateCardData[frontCardIdentifier]?.imageUrl || attendanceCardData[attendanceFrontIdentifier]?.imageUrl;
        const backImageUrl = certificateCardData[backCardIdentifier]?.imageUrl || attendanceCardData[attendanceBackIdentifier]?.imageUrl;

        if (cardElements[frontCardIdentifier] || cardElements[attendanceFrontIdentifier]) {
            const frontElement = cardElements[frontCardIdentifier] || cardElements[attendanceFrontIdentifier];
            frontElement.innerHTML = '';
            setupFileCard(frontElement, frontImageUrl);
        }

        if (isTwoSided && (cardElements[backCardIdentifier] || cardElements[attendanceBackIdentifier])) {
            const backElement = cardElements[backCardIdentifier] || cardElements[attendanceBackIdentifier];
            backElement.innerHTML = '';
            setupFileCard(backElement, backImageUrl);
        }

        updateCanvasDisplay(isTwoSided);
    }



    function createAttachmentCard(isBack) {
        const div = document.createElement('div');
        div.className = 'attachment-card filebox-card border-2 border-dashed border-gray-400 rounded-lg p-6 flex flex-col items-center gap-4 mb-4 hover:border-blue-600 transition-colors duration-300 relative min-h-[200px]';
        div.innerHTML = `
        <div class="initial-upload-state flex flex-col items-center gap-4">
            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 file-icon"></i>
            <h4 class="text-lg font-semibold">${isBack ? 'الوجه الخلفي للمرفق' : 'الوجه الأمامي للمرفق'}</h4>
            <p class="text-center text-gray-600">قم برفع ملفات PDF أو صور فقط.</p>
            <label class="inline-flex items-center gap-3 px-6 py-3 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-700 transition">
                <i class="fas fa-upload"></i>
                أرفاق PDF وصور
                <input name="attendance_template_file_path[]" type="file" class="sr-only file-input" accept="application/pdf,image/*">
                <input type="hidden" name="attendance_template_sides[]" class="side-input" value="${isBack ? 'back' : 'front'}">
            </label>
        </div>
        <div class="fabric-canvas-container hidden w-full h-48 flex justify-center items-center absolute inset-0 relative">
            <button type="button" class="remove-preview-btn absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg hover:bg-red-600 transition z-10" title="إزالة الملف">
                ×
            </button>
        </div>
    `;
        const fileInput = div.querySelector('.file-input');
        const sideInput = div.querySelector('.side-input');
        console.log('File input name in createAttachmentCard:', fileInput?.name);
        console.log('Side input name in createAttachmentCard:', sideInput?.name);
        $('#attachment-cards-container').append(div); // إضافة البطاقة إلى الحاوية
        setupFileCard(div, null, true); // تمرير isAttendance كـ true
        return div;
    }


    const fileTpl = document.getElementById('file-template').content;
    let docIdx = 0;




    function renderDocumentCards(block) {
        const frontRadio = block.querySelector('input.js-face[data-face="front"]');
        const backRadio = block.querySelector('input.js-face[data-face="back"]');
        const hub = block.querySelector('.js-filehub');

        if (!frontRadio || !hub) {
            console.warn('frontRadio or hub not found in renderDocumentCards.');
            return;
        }

        const allCardElements = hub.querySelectorAll('.filebox-card');
        let backITextObjects = [];

        // Save existing canvas data before clearing
        allCardElements.forEach(cardElement => {
            const sideInput = cardElement.querySelector('.side-input');
            if (sideInput) {
                const cardIdentifier = getCardIdFromSideInput(sideInput);
                if (cardData[cardIdentifier] && cardData[cardIdentifier].fabricCanvas) {
                    saveITextObjectsFromSpecificCanvas(cardData[cardIdentifier].fabricCanvas, cardIdentifier, cardData);
                    if (sideInput.value === 'back' && frontRadio.checked) {
                        backITextObjects = [...(cardData[cardIdentifier].iTextObjects || [])];
                        console.log(`Saving back iTextObjects for ${cardIdentifier}:`, backITextObjects);
                    }
                }
            }
        });

        hub.innerHTML = '';

        const f = document.importNode(fileTpl, true);
        f.querySelector('.card-title').textContent = window.i18n.documents_front_side;
        f.querySelector('.side-input').name = 'template_sides[]'; // تعديل اسم الإدخال
        f.querySelector('.side-input').value = 'front';
        f.querySelector('.file-input').name = 'document_template_file_path[]';
        const frontCardElement = f.querySelector('.filebox-card');
        hub.appendChild(f);
        setupFileCard(frontCardElement, null, false); // تمرير isAttendance كـ false

        const frontCardId = getCardIdFromSideInput(frontCardElement.querySelector('.side-input'));
        if (!cardData[frontCardId]) {
            cardData[frontCardId] = { fabricCanvas: null, iTextObjects: [], imageUrl: null };
        }

        // Restore front side canvas if it exists
        if (cardData[frontCardId].imageUrl) {
            const frontCanvasContainer = frontCardElement.querySelector('.fabric-canvas-container');
            frontCanvasContainer.classList.remove('hidden');
            frontCardElement.querySelector('.initial-upload-state').classList.add('hidden');
            frontCardElement.querySelector('.remove-preview-btn').style.display = 'flex';

            const canvasEl = document.createElement('canvas');
            canvasEl.setAttribute('data-card-id', frontCardId);
            canvasEl.setAttribute('id', `canvas-${frontCardId.replace(/[\[\]]/g, '')}`);
            canvasEl.style.width = '100%';
            canvasEl.style.height = '100%';
            canvasEl.style.display = 'block';
            frontCanvasContainer.prepend(canvasEl);

            requestAnimationFrame(() => {
                setTimeout(() => {
                    initializeTemplateCanvas(canvasEl, cardData[frontCardId].imageUrl, frontCardId, cardData);
                    if (cardData[frontCardId].iTextObjects.length > 0) {
                        restoreITextObjectsOnSpecificCanvas(cardData[frontCardId].fabricCanvas, frontCardId, cardData);
                    }
                    cardData[frontCardId].fabricCanvas.renderAll();
                }, 50);
            });
        }

        if (backRadio && backRadio.checked) {
            const b = document.importNode(fileTpl, true);
            b.querySelector('.card-title').textContent = window.i18n.documents_back_side;
            b.querySelector('.side-input').name = 'certificate_template_sides[]'; // تعديل اسم الإدخال
            b.querySelector('.side-input').value = 'back';
            b.querySelector('.file-input').name = 'document_template_file_path[]';
            const backCardElement = b.querySelector('.filebox-card');
            hub.appendChild(b);
            setupFileCard(backCardElement, null, false); // تمرير isAttendance كـ false

            const backCardId = getCardIdFromSideInput(backCardElement.querySelector('.side-input'));
            if (!cardData[backCardId]) {
                cardData[backCardId] = { fabricCanvas: null, iTextObjects: [], imageUrl: null };
            }

            // Restore back side canvas if it exists
            if (cardData[backCardId].imageUrl) {
                const backCanvasContainer = backCardElement.querySelector('.fabric-canvas-container');
                backCanvasContainer.classList.remove('hidden');
                backCardElement.querySelector('.initial-upload-state').classList.add('hidden');
                backCardElement.querySelector('.remove-preview-btn').style.display = 'flex';

                const canvasEl = document.createElement('canvas');
                canvasEl.setAttribute('data-card-id', backCardId);
                canvasEl.setAttribute('id', `canvas-${backCardId.replace(/[\[\]]/g, '')}`);
                canvasEl.style.width = '100%';
                canvasEl.style.height = '100%';
                canvasEl.style.display = 'block';
                backCanvasContainer.prepend(canvasEl);

                requestAnimationFrame(() => {
                    setTimeout(() => {
                        initializeTemplateCanvas(canvasEl, cardData[backCardId].imageUrl, backCardId, cardData);
                        if (cardData[backCardId].iTextObjects.length > 0) {
                            restoreITextObjectsOnSpecificCanvas(cardData[backCardId].fabricCanvas, backCardId, cardData);
                        }
                        cardData[backCardId].fabricCanvas.renderAll();
                    }, 50);
                });
            }

            // Restore back iTextObjects to front if switching to one side
            if (backITextObjects.length > 0 && cardData[frontCardId].fabricCanvas) {
                cardData[frontCardId].iTextObjects = [...cardData[frontCardId].iTextObjects, ...backITextObjects];
                restoreITextObjectsOnSpecificCanvas(cardData[frontCardId].fabricCanvas, frontCardId, cardData);
                cardData[frontCardId].fabricCanvas.renderAll();
            }
        } else {
            // Clear back card data only if switching to single side
            const backCardId = 'document_template_file_path[]-back';
            if (cardData[backCardId]) {
                if (cardData[backCardId].fabricCanvas) {
                    cardData[backCardId].fabricCanvas.dispose();
                }
                delete cardData[backCardId];
                console.log(`Cleared back card data for ${backCardId}`);
            }
        }

        // 🌟🌟 الإضافة الجديدة لتطبيق الأبعاد القياسية بعد إنشاء الكارد 🌟🌟
        const sizeSelect = document.getElementById('template-size-select');
        if (sizeSelect) {
            // نستخدم requestAnimationFrame و setTimeout لضمان استقرار العناصر في الـ DOM قبل محاولة قياس أبعادها
            requestAnimationFrame(() => {
                setTimeout(() => {
                    applyTemplateSize(sizeSelect.value || 'A4');
                }, 50); // تأخير بسيط
            });
        }
    }

    function renderAttendanceCards(block, initial = false) {
        const containers = block.querySelectorAll('.attachments-container');
        if (containers.length === 0) return;
        const container = containers[0];

        // إضافة الكلاسات لضمان التعرف على الحاوية كجزء من الحضور
        container.classList.add('js-filehub', 'attendance-filehub');
        console.log('Container classes:', container.className);

        const one = block.querySelector('input[name="side"][value="1"]');
        const two = block.querySelector('input[name="side"][value="2"]');
        let count = initial ? 1 : (two && two.checked ? 2 : 1);

        container.innerHTML = '';

        for (let i = 0; i < count; i++) {
            const newCardElement = createAttachmentCard(i === 1);
            // التحقق من اسم الإدخال وتصحيحه إذا لزم الأمر
            const fileInput = newCardElement.querySelector('.file-input');
            if (fileInput) {
                if (fileInput.name !== 'attendance_template_file_path[]') {
                    console.warn(`Incorrect file input name detected: ${fileInput.name}. Correcting to attendance_template_file_path[]`);
                    fileInput.name = 'attendance_template_file_path[]';
                }
                console.log('File input name for attendance card:', fileInput.name);
            } else {
                console.error('File input not found in attendance card:', newCardElement);
            }
            container.appendChild(newCardElement);
            setupFileCard(newCardElement);

            const sideInput = newCardElement.querySelector('.side-input');
            const cardId = getCardIdFromSideInput(sideInput);
            if (!attendanceCardData[cardId]) {
                attendanceCardData[cardId] = { fabricCanvas: null, iTextObjects: [], imageUrl: null, objectPositions: {} };
            }

            if (attendanceCardData[cardId].imageUrl) {
                const canvasContainer = newCardElement.querySelector('.fabric-canvas-container');
                canvasContainer.classList.remove('hidden');
                newCardElement.querySelector('.initial-upload-state').classList.add('hidden');
                newCardElement.querySelector('.remove-preview-btn').style.display = 'flex';

                const canvasEl = document.createElement('canvas');
                canvasEl.setAttribute('data-card-id', cardId);
                canvasEl.setAttribute('id', `canvas-${cardId.replace(/[\[\]]/g, '')}`);
                canvasEl.style.width = '100%';
                canvasEl.style.height = '100%';
                canvasEl.style.display = 'block';
                canvasContainer.prepend(canvasEl);

                requestAnimationFrame(() => {
                    setTimeout(() => {
                        initializeTemplateCanvas(canvasEl, attendanceCardData[cardId].imageUrl, cardId, attendanceCardData);
                        if (attendanceCardData[cardId].iTextObjects.length > 0) {
                            restoreITextObjectsOnSpecificCanvas(attendanceCardData[cardId].fabricCanvas, cardId, attendanceCardData);
                        }
                        attendanceCardData[cardId].fabricCanvas.renderAll();
                    }, 50);
                });
            }
        }

        if (count === 1) {
            const backCardId = 'attendance_template_data_file_path-back';
            if (attendanceCardData[backCardId]) {
                if (attendanceCardData[backCardId].fabricCanvas) {
                    attendanceCardData[backCardId].fabricCanvas.dispose();
                }
                delete attendanceCardData[backCardId];
                console.log(`Cleared back card data for ${backCardId}`);
            }
        }

        if (one) one.checked = (count === 1);
        if (two) two.checked = (count === 2);
    }

    if (templateDataExcelInput) {
        templateDataExcelInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const range = XLSX.utils.decode_range(worksheet['!ref']);
                    range.s.r = 0; range.e.r = 0;
                    const headers = [];
                    for (let C = range.s.c; C <= range.e.c; ++C) {
                        const cellAddress = XLSX.utils.encode_cell({ r: 0, c: C });
                        const cell = worksheet[cellAddress];
                        if (cell && cell.v !== undefined) headers.push(cell.v);
                    }
                    extractedHeaders = headers;

                    const formBlock = document.querySelector('.form-block');
                    let currentActiveSide = (formBlock.querySelector('input.js-face[data-face="back"]') && formBlock.querySelector('input.js-face[data-face="back"]').checked) ? 'back' : 'front';
                    const activeSideInput = formBlock.querySelector(`.side-input[value="${currentActiveSide}"]`);
                    if (activeSideInput) {
                        const cardIdOfActiveCard = getCardIdFromSideInput(activeSideInput);
                        if (cardData[cardIdOfActiveCard] && cardData[cardIdOfActiveCard].fabricCanvas) {
                            displayHeadersOnSpecificCanvas(cardData[cardIdOfActiveCard].fabricCanvas, extractedHeaders);
                        }
                    }
                    if (addHeaderTextBtn) addHeaderTextBtn.disabled = false;
                };
                reader.onerror = (error) => {
                    console.error('حدث خطأ أثناء قراءة ملف Excel للشهادة:', error);
                    alert('عذرًا، حدث خطأ أثناء قراءة ملف Excel للشهادة. يرجى المحاولة مرة أخرى.');
                };
                reader.readAsArrayBuffer(file);
            }
        });
    }

    if (badgeExcelInput) {
        badgeExcelInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const range = XLSX.utils.decode_range(worksheet['!ref']);
                    range.s.r = 0; range.e.r = 0;

                    const badgeHeaders = [];
                    for (let C = range.s.c; C <= range.e.c; ++C) {
                        const cellAddress = XLSX.utils.encode_cell({ r: 0, c: C });
                        const cell = worksheet[cellAddress];
                        if (cell && cell.v !== undefined) badgeHeaders.push(cell.v);
                    }

                    // ✅ عرض المعاينة بعد قراءة رؤوس الأعمدة
                    const previewCanvasEl = document.getElementById('attendance-preview-canvas');
                    if (previewCanvasEl) {
                        const cardId = 'attendance_template_data_file_path-front';
                        cardData[cardId] = { fabricCanvas: null, iTextObjects: [], imageUrl: null };

                        const defaultTemplateUrl = '/path/to/default/template.jpg'; // 👈 بدّله بمسار صورة القالب الفعلي لو عندك

                        initializeTemplateCanvas(previewCanvasEl, defaultTemplateUrl, cardId);

                        setTimeout(() => {
                            if (cardData[cardId]?.fabricCanvas) {
                                displayHeadersOnSpecificCanvas(cardData[cardId].fabricCanvas, badgeHeaders);
                            }
                        }, 200);
                    }
                };

                reader.onerror = (error) => {
                    console.error('حدث خطأ أثناء قراءة ملف Excel للبادج:', error);
                    alert('عذرًا، حدث خطأ أثناء قراءة ملف Excel للبادج. يرجى المحاولة مرة أخرى.');
                };

                reader.readAsArrayBuffer(file);
            }
        });
    }


    function initDocumentBlock(block) {
        if (block.dataset.inited) return;
        block.dataset.inited = '1';
        const name = `doc-face-${docIdx++}`;
        const front = block.querySelector('input.js-face[data-face="front"]');
        const back = block.querySelector('input.js-face[data-face="back"]');
        if (front && back) {
            front.name = back.name = name;
            front.checked = true;
            back.checked = false;
        } else if (front) {
            front.name = name;
            front.checked = true;
        }
        renderDocumentCards(block);
    }

    function initAttendanceBlock(block) {
        if (block.dataset.inited) return;
        block.dataset.inited = '1';
        const one = block.querySelector('input[name="side"][value="1"]');
        const two = block.querySelector('input[name="side"][value="2"]');
        if (one) one.checked = true;
        renderAttendanceCards(block, true);
    }

    document.body.addEventListener('change', (event) => {
        if (event.target.matches('input.js-face')) {
            const block = event.target.closest('.form-block');
            if (block) renderDocumentCards(block);
        } else if (event.target.matches('input[name="side"]')) {
            const card = event.target.closest('.form-card');
            if (card) renderAttendanceCards(card, false);
        } else if (event.target.matches('.toggle-presence')) {
            const toggle = event.target;
            const presenceCard = toggle.closest('.form-card').querySelector('.presence-card');
            const presenceWrapper = toggle.closest('.presence-wrapper');
            const presenceLabel = presenceWrapper.querySelector('.presence-label');
            if (presenceCard) {
                if (toggle.checked) {
                    presenceCard.classList.remove('hidden');
                    presenceLabel.textContent = 'نعم';
                } else {
                    presenceCard.classList.add('hidden');
                    presenceLabel.textContent = 'لا';
                }
            }
        }
    });

    if (addHeaderTextBtn) {
        addHeaderTextBtn.addEventListener('click', () => {
            if (extractedHeaders.length === 0) {
                alert('الرجاء رفع ملف Excel أولاً لاستخراج رؤوس الأعمدة.');
                return;
            }
            const formBlock = document.querySelector('.form-block');
            let currentActiveSide = (formBlock.querySelector('input.js-face[data-face="back"]') && formBlock.querySelector('input.js-face[data-face="back"]').checked) ? 'back' : 'front';
            const activeSideInput = formBlock.querySelector(`.side-input[value="${currentActiveSide}"]`);
            if (activeSideInput) {
                const cardIdOfActiveCard = getCardIdFromSideInput(activeSideInput);
                if (cardData[cardIdOfActiveCard] && cardData[cardIdOfActiveCard].fabricCanvas) {
                    displayHeadersOnSpecificCanvas(cardData[cardIdOfActiveCard].fabricCanvas, extractedHeaders);
                } else {
                    alert('الرجاء رفع صورة القالب أولاً على الكارد النشط.');
                }
            }
        });
        addHeaderTextBtn.disabled = true;
    }

    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            const formBlock = document.querySelector('.form-block');
            let currentActiveSide = (formBlock.querySelector('input.js-face[data-face="back"]') && formBlock.querySelector('input.js-face[data-face="back"]').checked) ? 'back' : 'front';
            const activeSideInput = formBlock.querySelector(`.side-input[value="${currentActiveSide}"]`);
            if (activeSideInput) {
                const cardIdOfActiveCard = getCardIdFromSideInput(activeSideInput);
                const currentCanvas = cardData[cardIdOfActiveCard]?.fabricCanvas;
                if (currentCanvas && currentCanvas.getActiveObject() && currentCanvas.getActiveObject().type === 'i-text') {
                    currentCanvas.remove(currentCanvas.getActiveObject());
                    currentCanvas.discardActiveObject().renderAll();
                    saveITextObjectsFromSpecificCanvas(currentCanvas, cardIdOfActiveCard);
                    if (deleteBtn) deleteBtn.disabled = true;
                } else {
                    alert('الرجاء تحديد نص لحذفه أولاً.');
                }
            }
        });
        deleteBtn.disabled = true;
    }

    const cutBtn = document.getElementById('cut-text-btn');
    const pasteBtn = document.getElementById('paste-text-btn');
    if (cutBtn) cutBtn.style.display = 'none';
    if (pasteBtn) pasteBtn.style.display = 'none';

    document.querySelectorAll('.form-block').forEach(initDocumentBlock);
    document.querySelectorAll('.form-card').forEach(initAttendanceBlock);












    function cleanDOM() {
        if (typeof fabric === 'undefined' || !fabric.Canvas._instances) {
            console.warn('fabric.js غير متاح أو _instances غير معرف، يتم تخطي تنظيف الكانفاس بالكامل');
            return;
        }
        fabric.Canvas._instances.forEach(canvas => canvas.dispose());
        const previewContainers = document.querySelectorAll('.preview-container');
        previewContainers.forEach(container => container.remove());
    }


    function showPreview(side, isAttendance, cardData, excelFirstRowData = null) {
        let previewCanvas;
        console.log(`عرض المعاينة للوجه: ${side}, هل هو حضور؟: ${isAttendance}`);
        // تأكد من وجود دالة cleanDOM() في نطاق هذا الملف أو ملف آخر تم تحميله
        cleanDOM();

        if (typeof side !== 'string') {
            console.error('Side is not a string:', side);
            return;
        }

        const cardId = isAttendance
            ? `attendance_template_data_file_path-${side}`
            : `document_template_file_path[]-${side}`;

        const cardDataSource = isAttendance ? attendanceCardData : certificateCardData;
        const currentCanvasData = cardDataSource && cardDataSource.hasOwnProperty(cardId) ? cardDataSource[cardId] : null;

        if (!currentCanvasData) {
            console.error(`No data found for cardId: ${cardId}`);
            const canvasElement = document.createElement('canvas');
            document.body.appendChild(canvasElement);
            // تأكد من وجود دالة createEmptyPreviewCanvas()
            previewCanvas = createEmptyPreviewCanvas(canvasElement, 'لا توجد بيانات كانفاس');
            return;
        }

        const previewContainer = document.createElement('div');
        previewContainer.className = 'preview-container';
        previewContainer.style.cssText = `
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.7); display: flex; justify-content: center;
    align-items: center; z-index: 1000; overflow: auto;
`;

        // 🌟🌟 الإضافة المطلوبة هنا 🌟🌟
        previewContainer.onclick = (event) => {
            // نتحقق مما إذا كان النقر حدث على العنصر الحاوية نفسه وليس على أي من أبنائه
            if (event.target === previewContainer) {
                previewContainer.remove();
                if (previewCanvas) previewCanvas.dispose();
            }
        };
        // 🌟🌟 نهاية الإضافة المطلوبة 🌟🌟

        const previewWrapper = document.createElement('div');
        previewWrapper.style.cssText = `
    position: relative; background: white; border-radius: 8px; padding: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; align-items: center;
`;

        const closeButton = document.createElement('button');
        closeButton.innerHTML = 'X';
        closeButton.className = 'absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors';
        closeButton.style.cssText = `
    position: absolute; top: 10px; right: 10px; z-index: 1001;
`;

        closeButton.onclick = () => {
            previewContainer.remove();
            if (previewCanvas) previewCanvas.dispose();
        };

        const canvasElement = document.createElement('canvas');
        canvasElement.id = `preview-canvas-${cardId}`;
        // canvasElement.style.cssText = `border: 2px solid #ccc; margin-bottom: 10px;`;

        previewWrapper.appendChild(closeButton);
        previewWrapper.appendChild(canvasElement);

        // ⭐⭐ بداية التعديلات الجديدة ⭐⭐

        // تحديد ما إذا كان هناك وجه واحد أو وجهين
        const frontCardId = isAttendance ? `attendance_template_data_file_path-front` : `document_template_file_path[]-front`;
        const backCardId = isAttendance ? `attendance_template_data_file_path-back` : `document_template_file_path[]-back`;
        const hasBothSides = cardDataSource.hasOwnProperty(frontCardId) && cardDataSource.hasOwnProperty(backCardId);

        // تحديد ما إذا كان يجب إظهار الكارد السفلي
        const shouldShowBottomCard = (hasBothSides && side === 'back') || (!hasBothSides && side === 'front');

        if (shouldShowBottomCard) {
            // Container for the logo and QR code
            const bottomCard = document.createElement('div');
            // bottomCard.style.cssText =`
            //     background-color: white; border: 1px solid #ccc; border-radius: 4px;
            // padding: 3px 8px; display: flex; justify-content: space-between;
            // align-items: center; width: 449px; box-sizing: border-box;
            // margin-top: -5px;
            // `;

//             bottomCard.style.cssText =`
// background-color: white; border: 1px solid #ccc; border-radius: 4px;
// padding: 3px 8px; display: flex; flex-direction: row-reverse; gap: 8px;
// align-items: center; width: 64%; box-sizing: border-box;
// margin-top: -5px`;



            //     const logoImg = document.createElement('img');
            //     logoImg.src = '/assets/logo.jpg'; // 👈 **تأكد من هذا المسار**
            //     logoImg.alt = 'شعار الموقع';
            //     logoImg.style.height = '40px';
            //
            //     // ⭐⭐ هنا التعديل: استبدال الـ QR code بالنص ⭐⭐
            //     const verifiedText = document.createElement('span');
            //     verifiedText.textContent = 'Verified by Pepasafe';
            //     verifiedText.style.cssText = `
            // font-weight: bold;
            // font-size: 14px;
            // color: #4a5568;
            // `;

            // bottomCard.appendChild(logoImg);
            // bottomCard.appendChild(verifiedText); // إضافة النص بدلاً من الصورة
            previewWrapper.appendChild(bottomCard);
        }
        // ⭐⭐ نهاية التعديلات الجديدة ⭐⭐

        previewContainer.appendChild(previewWrapper);
        document.body.appendChild(previewContainer);

        let originalCanvas = currentCanvasData.fabricCanvas;

        if (originalCanvas) {
            const originalWidth = originalCanvas.width;
            const originalHeight = originalCanvas.height;

            // const maxWidth = window.innerWidth * 0.8;
            // const maxHeight = window.innerHeight * 0.6;

            let scale = 1;
            // if (originalWidth > maxWidth || originalHeight > maxHeight) {
            //     const scaleX = maxWidth / originalWidth;
            //     const scaleY = maxHeight / originalHeight;
            //     scale = Math.min(scaleX, scaleY);
            // }

            const previewWidth = originalWidth * scale;
            const previewHeight = originalHeight * scale;

            canvasElement.width = previewWidth;
            canvasElement.height = previewHeight;
            previewWrapper.style.width = `${previewWidth}px`;
            previewWrapper.style.alignItems = 'center';

            previewCanvas = new fabric.Canvas(canvasElement, {
                width: previewWidth,
                height: previewHeight
            });

            const originalObjects = originalCanvas.getObjects();
            originalObjects.forEach(obj => {
                const clonedObj = fabric.util.object.clone(obj);
                if (clonedObj.textBaseline === 'alphabetical') {
                    clonedObj.textBaseline = 'alphabetic';
                }
                clonedObj.set({
                    left: obj.left * scale,
                    top: obj.top * scale,
                    scaleX: obj.scaleX * scale,
                    scaleY: obj.scaleY * scale,
                });

                // ⭐ التعديل لإضافة بيانات Excel ⭐
                if (obj.type === 'i-text' && excelFirstRowData && obj.text) {
                    const headerText = obj.text.trim();
                    if (excelFirstRowData.hasOwnProperty(headerText)) {
                        // هذا هو الجزء الذي يقوم بالاستبدال
                        clonedObj.set('text', String(excelFirstRowData[headerText]));
                    }
                }
                // ⭐ نهاية التعديل ⭐

                previewCanvas.add(clonedObj);
            });

            if (originalCanvas.backgroundImage) {
                const backgroundImage = originalCanvas.backgroundImage;
                const clonedBackground = new fabric.Image(backgroundImage.getElement(), {
                    left: backgroundImage.left * scale,
                    top: backgroundImage.top * scale,
                    scaleX: backgroundImage.scaleX * scale,
                    scaleY: backgroundImage.scaleY * scale,
                    originX: backgroundImage.originX,
                    originY: backgroundImage.originY,
                    selectable: false
                });
                previewCanvas.setBackgroundImage(clonedBackground, previewCanvas.renderAll.bind(previewCanvas));
            }

            previewCanvas.renderAll();
        } else if (currentCanvasData.imageUrl) {
            const img = new Image();
            img.src = currentCanvasData.imageUrl;
            img.crossOrigin = 'Anonymous';
            img.onload = () => {
                const originalWidth = img.width;
                const originalHeight = img.height;

                const maxWidth = window.innerWidth * 0.8;
                const maxHeight = window.innerHeight * 0.6;

                let scale = 1;
                if (originalWidth > maxWidth || originalHeight > maxHeight) {
                    const scaleX = maxWidth / originalWidth;
                    const scaleY = maxHeight / originalHeight;
                    scale = Math.min(scaleX, scaleY);
                }

                const previewWidth = originalWidth * scale;
                const previewHeight = originalHeight * scale;

                canvasElement.width = previewWidth;
                canvasElement.height = previewHeight;
                previewWrapper.style.width = `${previewWidth}px`;
                previewWrapper.style.alignItems = 'center';

                previewCanvas = new fabric.Canvas(canvasElement, {
                    width: previewWidth,
                    height: previewHeight
                });

                const fabricImage = new fabric.Image(img, {
                    left: 0,
                    top: 0,
                    scaleX: scale,
                    scaleY: scale,
                    selectable: false
                });
                previewCanvas.add(fabricImage);
                previewCanvas.sendToBack(fabricImage);

                if (currentCanvasData.iTextObjects && currentCanvasData.iTextObjects.length > 0) {
                    currentCanvasData.iTextObjects.forEach((iTextObj) => {
                        const textObject = new fabric.IText(iTextObj.text, {
                            left: iTextObj.left * scale,
                            top: iTextObj.top * scale,
                            fontFamily: iTextObj.fontFamily || 'Arial',
                            fontSize: (iTextObj.fontSize || 20) * scale,
                            fill: iTextObj.fill || '#000000',
                            selectable: false,
                            evented: false,
                            hasControls: false,
                            textBaseline: 'alphabetic'
                        });

                        // ⭐ التعديل لإضافة بيانات Excel هنا أيضاً ⭐
                        if (excelFirstRowData && iTextObj.text) {
                            const headerText = iTextObj.text.trim();
                            if (excelFirstRowData.hasOwnProperty(headerText)) {
                                textObject.set('text', String(excelFirstRowData[headerText]));
                            }
                        }
                        // ⭐ نهاية التعديل ⭐

                        previewCanvas.add(textObject);
                    });
                }

                previewCanvas.renderAll();
            };
            img.onerror = () => {
                console.error(`Failed to load image for ${cardId}`);
                previewCanvas = createEmptyPreviewCanvas(canvasElement, 'فشل تحميل الصورة');
            };
        } else {
            console.error(`No canvas or image found for cardId: ${cardId}`);
            previewCanvas = createEmptyPreviewCanvas(canvasElement, 'لا توجد بيانات كانفاس أو صورة');
        }
    }

    function createEmptyPreviewCanvas(canvasElement, message) {
        const width = 300;
        const height = 150;
        canvasElement.width = width;
        canvasElement.height = height;
        const canvas = new fabric.Canvas(canvasElement, {
            width: width,
            height: height
        });
        const text = new fabric.Text(message, {
            left: width / 2,
            top: height / 2,
            originX: 'center',
            originY: 'center',
            fontSize: 20,
            fill: '#000000',
            textBaseline: 'alphabetic'
        });
        canvas.add(text);
        canvas.renderAll();
        return canvas;
    }



    function initPreviewManager() {
        const certificateBtn = document.getElementById('fabric-popup');
        const attendanceBtn = document.getElementById('attendance-fabric-popup');
        const certificateExcelInput = document.getElementById('excel-input-model-2');
        const attendanceExcelInput = document.getElementById('badge-excel-input-2');

        if (certificateBtn) {
            certificateBtn.addEventListener('click', () => {
                console.log('Certificate finalize button clicked!');
                const hasBackSide = !!certificateCardData['document_template_file_path[]-back']?.imageUrl;
                console.log('Has back side for certificate:', hasBackSide);

                if (!hasBackSide) {
                    console.log('Single side detected for certificate, showing front preview directly...');
                    showPreview('front', false, certificateCardData);
                    return;
                }

                const choiceModal = document.createElement('div');
                choiceModal.className = 'choice-modal';
                choiceModal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            `;

                const modalContent = document.createElement('div');
                modalContent.style.cssText = `
                background: white;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
            `;

                const frontBtn = document.createElement('button');
                frontBtn.textContent = 'معاينة الوجه الأمامي';
                frontBtn.style.cssText = `
                margin: 10px;
                padding: 10px 20px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            `;
                frontBtn.addEventListener('click', () => {
                    showPreview('front', false, certificateCardData);
                    choiceModal.remove();
                });

                const backBtn = document.createElement('button');
                backBtn.textContent = 'معاينة الوجه الخلفي';
                backBtn.style.cssText = `
                margin: 10px;
                padding: 10px 20px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            `;
                backBtn.addEventListener('click', () => {
                    showPreview('back', false, certificateCardData);
                    choiceModal.remove();
                });

                modalContent.append(frontBtn, backBtn);
                choiceModal.appendChild(modalContent);
                document.body.appendChild(choiceModal);
            });
        } else {
            console.warn('Certificate preview button (fabric-popup) not found');
        }

        if (attendanceBtn) {
            attendanceBtn.addEventListener('click', () => {
                console.log('Attendance finalize button clicked!');
                const hasBackSide = !!attendanceCardData['attendance_template_data_file_path-back']?.imageUrl;
                console.log('Has back side for attendance:', hasBackSide);

                if (!hasBackSide) {
                    console.log('Single side detected for attendance, showing front preview directly...');
                    showPreview('front', true, attendanceCardData);
                    return;
                }

                const choiceModal = document.createElement('div');
                choiceModal.className = 'choice-modal';
                choiceModal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            `;

                const modalContent = document.createElement('div');
                modalContent.style.cssText = `
                background: white;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
            `;

                const frontBtn = document.createElement('button');
                frontBtn.textContent = 'معاينة الوجه الأمامي';
                frontBtn.style.cssText = `
                margin: 10px;
                padding: 10px 20px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            `;
                frontBtn.addEventListener('click', () => {
                    showPreview('front', true, attendanceCardData);
                    choiceModal.remove();
                });

                const backBtn = document.createElement('button');
                backBtn.textContent = 'معاينة الوجه الخلفي';
                backBtn.style.cssText = `
                margin: 10px;
                padding: 10px 20px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            `;
                backBtn.addEventListener('click', () => {
                    showPreview('back', true, attendanceCardData);
                    choiceModal.remove();
                });

                modalContent.append(frontBtn, backBtn);
                choiceModal.appendChild(modalContent);
                document.body.appendChild(choiceModal);
            });
        } else {
            console.warn('Attendance preview button (attendance-fabric-popup) not found');
        }

        if (certificateExcelInput) {
            certificateExcelInput.addEventListener('change', () => {
                console.log('Certificate Excel input changed');
                readFirstDataRow(false, (excelInfo) => {
                    if (excelInfo && excelInfo.headers) {
                        console.log('Certificate Excel headers:', excelInfo.headers);
                        certificateExcelData = excelInfo;
                        const frontCanvas = certificateCardData['document_template_file_path[]-front']?.fabricCanvas;
                        if (frontCanvas) {
                            console.log('Displaying headers on certificate front canvas');
                            // displayHeadersOnSpecificCanvas(frontCanvas, excelInfo.headers, 'document_template_file_path[]-front', certificateCardData);
                            frontCanvas.renderAll();
                        }
                    } else {
                        console.warn('No headers found in certificate Excel file');
                        certificateExcelData = { headers: [], data: [] };
                    }
                });
            });
        } else {
            console.warn('Certificate Excel input (excel-input-model-2) not found');
        }

        if (attendanceExcelInput) {
            attendanceExcelInput.addEventListener('change', () => {
                console.log('Attendance Excel input changed');
                readFirstDataRow(true, (excelInfo) => {
                    if (excelInfo && excelInfo.headers) {
                        console.log('Attendance Excel headers:', excelInfo.headers);
                        attendanceExcelData = excelInfo;
                        const frontCanvas = attendanceCardData['attendance_template_data_file_path-front']?.fabricCanvas;
                        if (frontCanvas) {
                            console.log('Displaying headers on attendance front canvas');
                            // displayHeadersOnSpecificCanvas(frontCanvas, excelInfo.headers, 'attendance_template_data_file_path-front', attendanceCardData);
                            frontCanvas.renderAll();
                        }
                    } else {
                        console.warn('No headers found in attendance Excel file');
                        attendanceExcelData = { headers: [], data: [] };
                    }
                });
            });
        } else {
            console.warn('Attendance Excel input (badge-excel-input-2) not found');
        }
    }

// استبدل الكود القديم بتاع finalizeBtn بالسطر ده

    const attendanceFinalizeBtn = document.getElementById('attendance-fabric-popup');
    initPreviewManager(finalizeBtn, attendanceFinalizeBtn);



// دالة لقراءة أول صف بيانات من الاكسيل
    // دالة لقراءة رؤوس الأعمدة وأول صف بيانات من الاكسيل
    function readFirstDataRow(isAttendance, callback) {
        const templateDataExcelInput = isAttendance
            ? document.getElementById('badge-excel-input-2')
            : document.getElementById('excel-input-model-2');
        if (templateDataExcelInput && templateDataExcelInput.files.length > 0) {
            const file = templateDataExcelInput.files[0];
            console.log('Reading Excel file:', file.name);
            const reader = new FileReader();
            reader.onload = (e) => {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheetName = workbook.SheetNames[0];
                    if (!firstSheetName) {
                        console.error('No sheets found in Excel file');
                        if (callback) callback(null);
                        return;
                    }

                    const worksheet = workbook.Sheets[firstSheetName];
                    const range = XLSX.utils.decode_range(worksheet['!ref']);
                    if (range.e.r < 1) { // 0-indexed, so 0 is row 1, 1 is row 2
                        console.error('Excel file has insufficient rows (needs at least 2 rows: headers and data)');
                        if (callback) callback(null);
                        return;
                    }

                    const headers = [];
                    for (let C = range.s.c; C <= range.e.c; ++C) {
                        const cellAddress = XLSX.utils.encode_cell({ r: 0, c: C });
                        const cell = worksheet[cellAddress];
                        // ⭐ تم التعديل هنا: إضافة String() و .trim() ⭐
                        headers.push(cell ? String(cell.v).trim() : '');
                    }
                    console.log('Excel headers:', headers);

                    const firstActualDataRow = [];
                    for (let C = range.s.c; C <= range.e.c; ++C) {
                        const cellAddress = XLSX.utils.encode_cell({ r: 1, c: C });
                        const cell = worksheet[cellAddress];
                        // ⭐ تم التعديل هنا: إضافة String() ⭐
                        firstActualDataRow.push(cell ? String(cell.v) : '');
                    }
                    console.log('First data row:', firstActualDataRow);

                    const excelInfo = {
                        headers: headers,
                        data: firstActualDataRow
                    };
                    if (isAttendance) {
                        // تأكد أن attendanceExcelData و certificateExcelData معرفتان كمتغيرات عامة أو يمكن الوصول إليها
                        attendanceExcelData = excelInfo;
                    } else {
                        certificateExcelData = excelInfo;
                    }
                    if (callback) callback(excelInfo);
                } catch (err) {
                    console.error('Error reading Excel file:', err);
                    if (callback) callback(null);
                }
            };
            reader.onerror = (err) => {
                console.error('FileReader error:', err);
                if (callback) callback(null);
            };
            reader.readAsArrayBuffer(file);
        } else {
            console.warn('No Excel file selected for:', isAttendance ? 'attendance' : 'certificate');
            if (isAttendance) {
                attendanceExcelData = { headers: [], data: [] };
            } else {
                certificateExcelData = { headers: [], data: [] };
            }
            if (callback) callback(null);
        }
    }



    document.getElementById('viewAttendancePreviewButton').addEventListener('click', () => {
        const isAttendanceType = true; // أو false للشهادات

        readFirstDataRow(isAttendanceType, (excelInfo) => {
            if (excelInfo && excelInfo.headers.length > 0 && excelInfo.data.length > 0) {
                const headers = excelInfo.headers;
                const firstDataRow = excelInfo.data;

                // بناء الكائن الذي تحتاجه showPreview
                const mappedFirstRowData = {};
                headers.forEach((header, index) => {
                    if (firstDataRow[index] !== undefined) {
                        mappedFirstRowData[header] = String(firstDataRow[index]);
                    }
                });

                console.log("Mapped data to display:", mappedFirstRowData);

                // ⭐ استدعاء دالة showPreview هنا ⭐
                // تحتاج إلى توفير قيم لـ 'side' و 'cardData' التي تناسب سياقك الحالي.
                // مثلاً:
                const currentSide = 'front'; // افترض أن هذا هو جانب البطاقة الذي تريد معاينته
                const currentCardData = attendanceCardData; // أو certificateCardData بناءً على isAttendanceType

                showPreview(currentSide, isAttendanceType, currentCardData, mappedFirstRowData);

            } else {
                console.warn("لم يتم الحصول على بيانات Excel صالحة للعرض.");
                // يمكنك اختيار عرض المعاينة بدون بيانات Excel إذا لم يتم العثور عليها
                // showPreview(currentSide, isAttendanceType, currentCardData, null);
            }
        });
    });

// مثال لزر "عرض معاينة الشهادة"
    document.getElementById('viewCertificatePreviewButton').addEventListener('click', () => {
        const isAttendanceType = false; // للشهادات

        readFirstDataRow(isAttendanceType, (excelInfo) => {
            if (excelInfo && excelInfo.headers.length > 0 && excelInfo.data.length > 0) {
                const headers = excelInfo.headers;
                const firstDataRow = excelInfo.data;

                const mappedFirstRowData = {};
                headers.forEach((header, index) => {
                    if (firstDataRow[index] !== undefined) {
                        mappedFirstRowData[header] = String(firstDataRow[index]);
                    }
                });

                console.log("Mapped data to display:", mappedFirstRowData);

                const currentSide = 'front';
                const currentCardData = certificateCardData;

                showPreview(currentSide, isAttendanceType, currentCardData, mappedFirstRowData);

            } else {
                console.warn("لم يتم الحصول على بيانات Excel صالحة للعرض.");
            }
        });
    });




    // إضافة تحكم المحرر للنصوص
    const textEditorPanel = document.getElementById('text-editor-panel');
    const textContent = document.getElementById('text-content');
    const fontSize = document.getElementById('font-size');
    const fontColor = document.getElementById('font-color');
    const fontFamily = document.getElementById('font-family');

    // دالة لتحديث النص المختار
    function updateSelectedText() {
        if (!activeCanvas) return;
        const activeObject = activeCanvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
            if (textContent.value) activeObject.set('text', textContent.value);
            activeObject.set({
                fontSize: parseInt(fontSize.value) || 20,
                fill: fontColor.value,
                fontFamily: fontFamily.value
            });
            activeCanvas.renderAll();
            saveITextObjectsFromSpecificCanvas(activeCanvas, getCardIdFromSpecificCanvas(activeCanvas));
        }
    }

    // ربط الأحداث للمحرر
    textContent.addEventListener('input', updateSelectedText);
    fontSize.addEventListener('change', updateSelectedText);
    fontColor.addEventListener('input', updateSelectedText);
    fontFamily.addEventListener('change', updateSelectedText);

    // تحديث المحرر لما يتم اختيار نص
    activeCanvas.on('selection:created', () => {
        const activeObject = activeCanvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
            textContent.value = activeObject.text || '';
            fontSize.value = activeObject.fontSize || 20;
            fontColor.value = activeObject.fill || '#000000';
            fontFamily.value = activeObject.fontFamily || 'Arial';
            textEditorPanel.classList.remove('hidden');
        }
    });

    activeCanvas.on('selection:updated', () => {
        const activeObject = activeCanvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
            textContent.value = activeObject.text || '';
            fontSize.value = activeObject.fontSize || 20;
            fontColor.value = activeObject.fill || '#000000';
            fontFamily.value = activeObject.fontFamily || 'Arial';
            textEditorPanel.classList.remove('hidden');
        }
    });

    activeCanvas.on('selection:cleared', () => {
        textEditorPanel.classList.add('hidden');
        textContent.value = '';
        fontSize.value = 20;
        fontColor.value = '#000000';
        fontFamily.value = 'Arial';
    });



});






// --------------------------------------------------------------------------------
