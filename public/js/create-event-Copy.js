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


// استنساخ بطاقة جديدة
let formCardCounter = 0; // **أضف هذا السطر في أعلى ملف JavaScript، خارج أي دالة.**


(function() {
    const container = document.getElementById('forms-container');
    const addBtn    = document.getElementById('add-card-btn');
    const template  = container.querySelector('.form-card');

    // عدِّل أرقام النماذج
    function updateNumbers() {
        container.querySelectorAll('.form-card').forEach((card, idx) => {
            const title = card.querySelector('h3');
            if (title) title.textContent = `نموذج ${idx + 1}`;
        });
    }
    updateNumbers();





    addBtn.addEventListener('click', () => {
        formCardCounter++; // زيادة العداد لكل كارت جديد

        const clone = template.cloneNode(true);

        // 1. إزالة الـ IDs لتجنب التعارض (كما هو موجود لديك)
        clone.querySelectorAll('[id]').forEach(e => e.removeAttribute('id'));

        // 2. تحديث سمات 'name' و 'id' لتكون فريدة لكل كارت
        clone.querySelectorAll('[name]').forEach(e => {
            const originalName = e.getAttribute('name');
            // هنا نقوم بتعديل اسم الحقل ليحتوي على الفهرس الجديد.
            // مثلاً: document_title  تصبح  document_title[1] أو document_title_1
            // اختر النمط الذي يناسب الباك إند الخاص بك.
            // أنا أقترح استخدام نمط الأقواس `[]` إذا كان الباك إند يتوقع مصفوفة،
            // أو استخدام `_` إذا كان يتوقع أسماء حقول منفصلة.
            // للتبسيط ولأنك تستخدم `[]` في بعض الأسماء الأخرى، سأستخدم `_${formCardCounter}` كجزء من الاسم المؤقت الآن.
            // سنتأكد لاحقًا من أن هذا يتوافق مع ما يتوقعه الباك إند في الحقول المخفية.

            // For simple inputs like event_title, issuer, document_title
            if (['event_title', 'issuer', 'document_title', 'attendance_send_at', 'attendance_message', 'attendance_message_char_count', 'attendance_validity', 'attendance_valid_from', 'attendance_valid_until', 'document_send_at', 'document_message', 'document_message_char_count', 'document_validity', 'valid_from', 'valid_until', 'recipient_file_path', 'template_data_file_path'].includes(originalName)) {
                e.setAttribute('name', `${originalName}_${formCardCounter}`);
                if (e.id) e.setAttribute('id', `${e.id}_${formCardCounter}`); // Update ID if it exists
            }
            // For array names like attendance_send_via[], document_send_via[]
            else if (originalName.endsWith('[]')) {
                // No change for array names, Laravel handles them well with arrays
                // However, the *parent* structure needs to be unique.
                // We'll manage this through the hidden input fields for canvas data.
            }
            // For file inputs like document_template_file_path[] and attendance_template_file_path[]
            // and their corresponding side inputs.
            // These will be handled specifically by renderDocumentCards/renderAttendanceCards and setupFileCard
            // We'll give them a temporary unique ID for now.
            if (e.classList.contains('file-input')) {
                e.setAttribute('name', `file_input_${formCardCounter}`); // Temporary unique name
                if (e.id) e.setAttribute('id', `${e.id}_${formCardCounter}`); // Update ID if it exists
            } else if (e.classList.contains('side-input')) {
                e.setAttribute('name', `side_input_${formCardCounter}`); // Temporary unique name
                if (e.id) e.setAttribute('id', `${e.id}_${formCardCounter}`); // Update ID if it exists
            }
        });

        // 3. تحديث الـ IDs في لوحات المحرر لـ 'attendance' و 'certificate'
        // هذه اللوحات تحتاج إلى أن تكون فريدة لكل كارت
        const attendanceEditorPanel = clone.querySelector('#attendance-text-editor-panel');
        if (attendanceEditorPanel) {
            attendanceEditorPanel.setAttribute('id', `attendance-text-editor-panel_${formCardCounter}`);
            attendanceEditorPanel.querySelector('#attendance-text-content').setAttribute('id', `attendance-text-content_${formCardCounter}`);
            attendanceEditorPanel.querySelector('#attendance-font-size').setAttribute('id', `attendance-font-size_${formCardCounter}`);
            attendanceEditorPanel.querySelector('#attendance-font-color').setAttribute('id', `attendance-font-color_${formCardCounter}`);
            attendanceEditorPanel.querySelector('#attendance-font-family').setAttribute('id', `attendance-font-family_${formCardCounter}`);
        }

        const certificateEditorPanel = clone.querySelector('#text-editor-panel'); // Note: This is '#text-editor-panel' in your HTML
        if (certificateEditorPanel) {
            certificateEditorPanel.setAttribute('id', `text-editor-panel_${formCardCounter}`);
            certificateEditorPanel.querySelector('#text-content').setAttribute('id', `text-content_${formCardCounter}`);
            certificateEditorPanel.querySelector('#font-size').setAttribute('id', `font-size_${formCardCounter}`);
            certificateEditorPanel.querySelector('#font-color').setAttribute('id', `font-color_${formCardCounter}`);
            certificateEditorPanel.querySelector('#font-family').setAttribute('id', `font-family_${formCardCounter}`);
        }


        // ارجع الحقول الافتراضية (كما هي)
        clone.querySelectorAll('input').forEach(i => {
            if (i.type === 'text') i.value = '';
            if (i.type === 'checkbox') i.checked = false;
        });
        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);

        // زرّ الحذف (كما هو)
        const header = clone.querySelector('.flex.justify-between.items-center.mb-4');
        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'text-red-600 hover:opacity-75 ml-2';
        delBtn.innerHTML = '<i class="fas fa-trash"></i>';
        delBtn.addEventListener('click', () => {
            // عند حذف الكارد، يجب أن ننظف بيانات الكانفاس المرتبطة به
            const formCardIdentifier = `form_card_${formCardCounter}`; // أو أي معرف فريد آخر للكارد الأب
            // ... (هنا نحتاج لمنطق لتحديد المعرف الصحيح وتنظيف cardData)
            clone.remove();
            updateNumbers();
        });
        header.appendChild(delBtn);

        container.appendChild(clone);
        updateNumbers(); // تحديث أرقام النماذج (نموذج 1، نموذج 2، إلخ)

        // 4. استدعاء دوال التهيئة للكارت الجديد
        initValidity(clone); // تهيئة صلاحية الشهادة/البادج
        initPresenceCard(clone); // تهيئة بطاقة الحضور
        toggleDatesForSelect(clone.querySelector('.cert-validity-new')); // تهيئة عرض التواريخ
        updateUI(clone.querySelector('.toggle-presence')); // تهيئة زر التفعيل/التعطيل للحضور

        // 5. تهيئة كتل المستندات والحضور داخل الكارت الجديد
        // تحتاج هذه الدوال الآن لقبول الفهرس الجديد.
        // يجب أن نقوم بتعديل تعريف هذه الدوال لتمرير formCardCounter
        const docBlock = clone.querySelector('.form-block'); // كتلة الشهادة
        const attendanceBlock = clone.querySelector('.presence-card .form-block'); // كتلة الحضور (داخل presence-card)

        // التأكد من تهيئة بلوك الشهادات
        if (docBlock) {
            initDocumentBlock(docBlock, formCardCounter);
        }

        // التأكد من تهيئة بلوك الحضور
        if (attendanceBlock) {
            initAttendanceBlock(attendanceBlock, formCardCounter);
        }


        // تهيئة محرر النصوص للكانفاس الجديد (لا ينبغي أن يعتمد على activeCanvas)
        // هذا الجزء من الكود يجب أن يتم استدعاؤه بعد تهيئة الكانفاس الفعلي في setupFileCard
        // وسنعدله لاحقاً ليشمل formCardCounter
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
        label.textContent = 'إلغاء الحضور';
        label.classList.replace('text-blue-600', 'text-red-600');
        wrapper.classList.replace('border-blue-600', 'border-red-600');
        wrapper.classList.replace('bg-blue-100', 'bg-red-100');
        track.classList.replace('peer-checked:bg-blue-600', 'peer-checked:bg-red-600');
    } else {
        label.textContent = 'تفعيل الحضور';
        label.classList.replace('text-red-600', 'text-blue-600');
        wrapper.classList.replace('border-red-600', 'border-blue-600');
        wrapper.classList.replace('bg-red-100', 'bg-blue-100');
        track.classList.replace('peer-checked:bg-red-600', 'peer-checked:bg-blue-600');
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
            if (title) title.textContent = `نموذج ${idx + 1}`;
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
        label.textContent = 'إلغاء الحضور';
        label.classList.replace('text-blue-600', 'text-red-600');
        wrapper.classList.replace('border-blue-600', 'border-red-600');
        wrapper.classList.replace('bg-blue-100', 'bg-red-100');
        track.classList.replace('peer-checked:bg-blue-600', 'peer-checked:bg-red-600');
    } else {
        label.textContent = 'تفعيل الحضور';
        label.classList.replace('text-red-600', 'text-blue-600');
        wrapper.classList.replace('border-red-600', 'border-blue-600');
        wrapper.classList.replace('bg-red-100', 'bg-blue-100');
        track.classList.replace('peer-checked:bg-red-600', 'peer-checked:bg-blue-600');
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
        const fileInput = sideInput.previousElementSibling; // هذا هو الـ <input type="file">
        if (!fileInput) {
            console.error('File input (previousSibling) not found for side input:', sideInput);
            return null;
        }

        const fileInputName = fileInput.name; // مثل attendance_template_file_path_1_front
        const sideValue = sideInput.value;   // مثل front أو back

        // استخدام Regular Expression لتحليل اسم fileInputName
        // نتوقع صيغة مثل: [نوع_القالب]_template_file_path_[رقم_الكارد]_[الجانب]
        const match = fileInputName.match(/^(attendance|document)_template_file_path_(\d+)_(front|back)$/);

        if (!match) {
            console.error('Could not parse fileInputName to get card index and side:', fileInputName);
            return null;
        }

        const fileType = match[1]; // 'attendance' أو 'document'
        const formCardIndex = match[2]; // رقم الكارت، مثل '1'
        // const parsedSide = match[3]; // الجانب من اسم الملف، مثل 'front'

        // بناء cardIdentifier بالصيغة الجديدة
        // لاحظ أن `_template_file_path` تصبح `_template_data_file_path` في المعرف
        // وأن `-` يفصل بين الفهرس والجانب
        const basePrefix = `${fileType}_template_data_file_path`;
        return `${basePrefix}_${formCardIndex}-${sideValue}`;
    }

    function getCardIdFromSpecificCanvas(canvas) {
        // التحقق أولاً في certificateCardData
        for (const formCardIndex in certificateCardData) {
            if (certificateCardData.hasOwnProperty(formCardIndex)) {
                for (const side in certificateCardData[formCardIndex]) {
                    if (certificateCardData[formCardIndex].hasOwnProperty(side)) {
                        if (certificateCardData[formCardIndex][side].fabricCanvas === canvas) {
                            // بناء cardIdentifier بالصيغة الصحيحة
                            return `document_template_file_path_${formCardIndex}-${side}`;
                        }
                    }
                }
            }
        }

        // إذا لم يتم العثور عليه، تحقق في attendanceCardData
        for (const formCardIndex in attendanceCardData) {
            if (attendanceCardData.hasOwnProperty(formCardIndex)) {
                for (const side in attendanceCardData[formCardIndex]) {
                    if (attendanceCardData[formCardIndex].hasOwnProperty(side)) {
                        if (attendanceCardData[formCardIndex][side].fabricCanvas === canvas) {
                            // بناء cardIdentifier بالصيغة الصحيحة
                            return `attendance_template_data_file_path_${formCardIndex}-${side}`;
                        }
                    }
                }
            }
        }

        return null;
    }



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

    // تم تحديث توقيع الدالة ليعكس أننا لا نمرر allCardData مباشرة بنفس الطريقة
// بل نستخدمها كمرجع للبيانات العامة إذا لزم الأمر
    function restoreITextObjectsOnSpecificCanvas(specificCanvas, cardIdentifier, allCardDataParent) {
        if (!specificCanvas || !(specificCanvas instanceof fabric.Canvas)) {
            console.error(`Invalid canvas for ${cardIdentifier}`);
            return;
        }

        // استخراج formCardIndex والجانب من cardIdentifier
        const parts = cardIdentifier.split(/_(.+)/);
        const prefix = parts[0];
        const indexAndSide = parts[1];

        const formCardIndexMatch = indexAndSide.match(/(\d+)-(.+)/);
        if (!formCardIndexMatch) {
            console.error('Invalid cardIdentifier format for restoration:', cardIdentifier);
            return;
        }
        const currentFormCardIndex = parseInt(formCardIndexMatch[1]);
        const currentSide = formCardIndexMatch[2];


        let inputFieldName;
        const isAttendance = cardIdentifier.includes('attendance_template_data_file_path');
        if (isAttendance) {
            inputFieldName = 'attendance_text_data';
        } else {
            inputFieldName = 'certificate_text_data';
        }

        const inputField = document.querySelector(`input[name="${inputFieldName}"]`);
        if (!inputField) {
            console.warn(`Input field ${inputFieldName} not found for restoration for card ${currentFormCardIndex}, side ${currentSide}.`);
            // إذا لم يتم العثور على حقل الإدخال، ولم تكن هناك بيانات في الهيكل المتداخل
            // قد نحتاج للتعامل مع الحالة التي لا توجد فيها بيانات محفوظة
            return;
        }

        let storedData = {};
        try {
            if (inputField.value) {
                storedData = JSON.parse(inputField.value);
            }
        } catch (e) {
            console.error(`Error parsing existing ${inputFieldName} JSON for restoration for card ${currentFormCardIndex}, side ${currentSide}:`, e);
            storedData = {};
        }

        // ⭐⭐ هنا التعديل الجوهري: الوصول إلى البيانات المخزنة بالهيكل المتداخل الجديد ⭐⭐
        const cardDataForRestore = storedData[currentFormCardIndex] ? storedData[currentFormCardIndex][currentSide] : null;

        if (!cardDataForRestore) {
            console.log(`No stored data found for card ${currentFormCardIndex}, side ${currentSide} from ${inputFieldName}.`);
            return;
        }

        const textsToRestore = cardDataForRestore.texts || [];
        const qrCodesToRestore = cardDataForRestore.qrCodes || [];
        const objectsToRestore = [...textsToRestore, ...qrCodesToRestore];


        if (objectsToRestore.length === 0) {
            console.log(`No objects (texts or QR codes) to restore for card ${currentFormCardIndex}, side ${currentSide} from ${inputFieldName}.`);
            return;
        }

        console.log(`Restoring ${objectsToRestore.length} objects for card ${currentFormCardIndex}, side ${currentSide} from ${inputFieldName}`);

        // إزالة الكائنات الحالية القابلة للتحديد قبل الاستعادة
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
                    const qrImageUrl = '/assets/qr-code.jpg'; // استخدم هذا المسار
                    fabric.Image.fromURL(qrImageUrl, (img) => {
                        if (!img) {
                            console.error(`Failed to load QR code image for object ID: ${data.id}`);
                            return;
                        }
                        img.set({
                            id: data.id, // تأكد من استعادة الـ ID
                            left: data.left,
                            top: data.top,
                            scaleX: data.scaleX || 0.3,
                            scaleY: data.scaleY || 0.3,
                            angle: data.angle,
                            selectable: true,
                            hasControls: true,
                            type: 'qr-code',
                            subtype: data.subtype,
                            width: data.width || 100,
                            height: data.height || 100
                        });
                        canvas.add(img);
                        canvas.renderAll(); // أعد الرسم بعد إضافة كل QR code
                    }, { crossOrigin: 'Anonymous' });
                }
            });
            canvas.renderAll(); // أعد الرسم مرة أخيرة بعد إضافة جميع الكائنات
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


    // قم بتحديث تعريف الدالة لتقبل formCardIndex و currentSide و cardDataSource
    function initializeTemplateCanvas(canvasElement, imageUrl, cardIdentifier, cardDataSource) {
        if (!canvasElement) {
            console.error('Canvas element not provided or found for cardIdentifier:', cardIdentifier);
            return;
        }

        // استخراج formCardIndex والجانب من cardIdentifier
        const parts = cardIdentifier.split(/_(.+)/);
        const prefix = parts[0];
        const indexAndSide = parts[1];

        const formCardIndexMatch = indexAndSide.match(/(\d+)-(.+)/);
        if (!formCardIndexMatch) {
            console.error('Invalid cardIdentifier format:', cardIdentifier);
            return;
        }
        const currentFormCardIndex = parseInt(formCardIndexMatch[1]);
        const currentSide = formCardIndexMatch[2];


        // الوصول إلى الكائن الصحيح في الهيكل المتداخل
        // هذا الكائن يحتوي على fabricCanvas، objects، imageUrl
        const cardDataEntry = cardDataSource[currentFormCardIndex][currentSide];


        if (cardDataEntry && cardDataEntry.fabricCanvas) {
            cardDataEntry.fabricCanvas.dispose();
            cardDataEntry.fabricCanvas = null;
        }

        const rect = canvasElement.getBoundingClientRect();
        let finalCanvasWidth = rect.width;
        let finalCanvasHeight = rect.height;

        if (finalCanvasWidth === 0 || finalCanvasHeight === 0) {
            const parentContainer = canvasElement.parentElement;
            if (parentContainer && parentContainer.offsetWidth > 0 && parentContainer.offsetHeight > 0) {
                finalCanvasWidth = parentContainer.offsetWidth;
                finalCanvasHeight = parentContainer.offsetHeight;
            } else {
                finalCanvasWidth = 900;
                finalCanvasHeight = 600;
                console.warn(`Canvas dimensions are zero for ${cardIdentifier}. Using default width: ${finalCanvasWidth}, height: ${finalCanvasHeight}`);
            }
        }
        if (finalCanvasWidth < 400) finalCanvasWidth = 400;
        if (finalCanvasHeight < 300) finalCanvasHeight = 300;

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
        currentCanvas.cardIdentifier = cardIdentifier; // الاحتفاظ به كخاصية لتسهيل التتبع

        // ربط الكانفاس الجديد بالكائن الصحيح في الهيكل المتداخل
        cardDataEntry.fabricCanvas = currentCanvas;

        // تعيين الكانفاس النشط عند التفاعل معه
        currentCanvas.on('mouse:down', function() {
            activeCanvas = this; // تحديث activeCanvas
            console.log(`Mouse down, activeCanvas set to: ${this.cardIdentifier}`);
            // تحديث محرر النصوص للكارت النشط
            updateTextEditorForActiveCanvas(currentFormCardIndex, currentSide);
        });

        // ----------------------------------------------------------------------
        // إعداد محرر النصوص (تعديل selectors للبحث عن IDs الفريدة)
        // ----------------------------------------------------------------------
        const isAttendance = cardIdentifier.includes('attendance_template_data_file_path');
        const editorPanelPrefix = isAttendance ? 'attendance-text-editor-panel' : 'text-editor-panel';
        const textContentPrefix = isAttendance ? 'attendance-text-content' : 'text-content';
        const fontSizePrefix = isAttendance ? 'attendance-font-size' : 'font-size';
        const fontColorPrefix = isAttendance ? 'attendance-font-color' : 'font-color';
        const fontFamilyPrefix = isAttendance ? 'attendance-font-family' : 'font-family';

        // البحث عن العناصر باستخدام IDs الفريدة
        const textEditorPanel = document.getElementById(`${editorPanelPrefix}_${currentFormCardIndex}`);
        const textContent = document.getElementById(`${textContentPrefix}_${currentFormCardIndex}`);
        const fontSize = document.getElementById(`${fontSizePrefix}_${currentFormCardIndex}`);
        const fontColor = document.getElementById(`${fontColorPrefix}_${currentFormCardIndex}`);
        const fontFamily = document.getElementById(`${fontFamilyPrefix}_${currentFormCardIndex}`);


        // دالة لتحديث محرر النصوص بناءً على الكانفاس النشط والكائن المحدد
        function updateTextEditorControls() {
            if (!activeCanvas || activeCanvas.cardIdentifier !== cardIdentifier) return; // تأكد أن المحرر يخص الكانفاس النشط
            const activeObject = activeCanvas.getActiveObject();
            if (activeObject && activeObject.type === 'i-text' && textEditorPanel) {
                textContent.value = activeObject.text || '';
                fontSize.value = activeObject.fontSize || 20;
                fontColor.value = activeObject.fill || '#000000';
                fontFamily.value = activeObject.fontFamily || 'Arial';
                textEditorPanel.classList.remove('hidden');
            } else if (textEditorPanel) {
                textEditorPanel.classList.add('hidden');
                // مسح قيم المحرر عند عدم تحديد أي نص
                textContent.value = '';
                fontSize.value = 20;
                fontColor.value = '#000000';
                fontFamily.value = 'Arial';
            }
        }


        // ربط مستمعات الأحداث للمحرر (للكانفاس المحدد)
        if (textContent) textContent.addEventListener('input', updateTextEditorControls);
        if (fontSize) fontSize.addEventListener('change', updateTextEditorControls);
        if (fontColor) fontColor.addEventListener('input', updateTextEditorControls);
        if (fontFamily) fontFamily.addEventListener('change', updateTextEditorControls);

        // مستمعات الكانفاس لتحديث المحرر
        currentCanvas.on('selection:created', updateTextEditorControls);
        currentCanvas.on('selection:updated', updateTextEditorControls);
        currentCanvas.on('selection:cleared', updateTextEditorControls);
        currentCanvas.on('object:modified', updateTextEditorControls); // تحديث المحرر عند تعديل الكائن

        // ----------------------------------------------------------------------
        // تحميل الصورة الخلفية واستعادة الكائنات
        // ----------------------------------------------------------------------
        const imageUrlToLoad = cardDataEntry.imageUrl || imageUrl;

        fabric.Image.fromURL(imageUrlToLoad, function(img) {
            const scaleX = finalCanvasWidth / img.width;
            const scaleY = finalCanvasHeight / img.height;
            const scale = Math.min(scaleX * 0.97, scaleY * 0.97);

            img.scale(scale);

            currentCanvas.setBackgroundImage(img, currentCanvas.renderAll.bind(currentCanvas), {
                scaleX: scale,
                scaleY: scale,
                originX: 'center',
                originY: 'center',
                top: finalCanvasHeight / 2,
                left: finalCanvasWidth / 2,
                absolutePositioned: true
            });

            // استعادة الكائنات من البيانات المحفوظة
            restoreITextObjectsOnSpecificCanvas(currentCanvas, cardIdentifier, cardDataSource);
            currentCanvas.renderAll();
        }, { crossOrigin: 'Anonymous' });

        // --------------------------------------------------------------------------------
        // منطق السحب والإفلات المعدّل (ليدعم I-Text و QR Code) - يحتاج إلى تحديثات بسيطة
        // --------------------------------------------------------------------------------

        currentCanvas.on('mouse:down', function(opt) {
            const evt = opt.e;
            const target = opt.target;
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
            let targetCanvas = null;
            let targetCardId = null; // This will now be cardIdentifier for the target
            let targetFormCardIndex = null;
            let targetSide = null;

            if (isDragging && currentlyDraggedFabricObject) {
                // تحديد الكانفاس المستهدف
                // هنا نحتاج إلى تكرار على `certificateCardData` و `attendanceCardData`
                // للبحث عن الكانفاس الصحيح
                for (const fIdx in certificateCardData) {
                    for (const sideKey in certificateCardData[fIdx]) {
                        const canvasInstance = certificateCardData[fIdx][sideKey].fabricCanvas;
                        if (canvasInstance && isMouseInsideCanvas(canvasInstance, evt)) {
                            targetCanvas = canvasInstance;
                            targetCardId = canvasInstance.cardIdentifier;
                            targetFormCardIndex = parseInt(fIdx);
                            targetSide = sideKey;
                            break;
                        }
                    }
                    if (targetCanvas) break;
                }

                if (!targetCanvas) { // إذا لم يتم العثور في الشهادات، ابحث في الحضور
                    for (const fIdx in attendanceCardData) {
                        for (const sideKey in attendanceCardData[fIdx]) {
                            const canvasInstance = attendanceCardData[fIdx][sideKey].fabricCanvas;
                            if (canvasInstance && isMouseInsideCanvas(canvasInstance, evt)) {
                                targetCanvas = canvasInstance;
                                targetCardId = canvasInstance.cardIdentifier;
                                targetFormCardIndex = parseInt(fIdx);
                                targetSide = sideKey;
                                break;
                            }
                        }
                        if (targetCanvas) break;
                    }
                }


                if (targetCanvas && targetCanvas !== startDragCanvas) {
                    // منطق الإفلات الناجح على كانفاس آخر
                    const pointer = targetCanvas.getPointer(evt, true);

                    startDragCanvas.remove(currentlyDraggedFabricObject);
                    // حفظ التغييرات على الكانفاس الأصلي (الحذف)
                    // يجب أن نستخدم saveITextObjectsFromSpecificCanvas المعدلة
                    // وأن نمرر `cardDataSource` الصحيح
                    const startCanvasIsAttendance = startDragCanvas.cardIdentifier.includes('attendance_template_data_file_path');
                    const startCanvasDataSource = startCanvasIsAttendance ? attendanceCardData : certificateCardData;
                    saveITextObjectsFromSpecificCanvas(startDragCanvas, startDragCanvas.cardIdentifier, startCanvasDataSource);


                    if (currentlyDraggedFabricObject.type === 'qr-code') {
                        // 💥 التعديل الأهم لحل مشكلة تكرار QR Code (حذف القديم قبل إضافة الجديد)
                        targetCanvas.getObjects().filter(obj => obj.type === 'qr-code').forEach(qr => {
                            targetCanvas.remove(qr);
                        });

                        const qrImageUrl = '/assets/qr-code.jpg';
                        const originalObject = currentlyDraggedFabricObject;
                        const targetCardIdForSave = targetCardId; // المعرف الكامل للهدف
                        const targetDataSource = targetCardId.includes('attendance') ? attendanceCardData : certificateCardData;

                        fabric.Image.fromURL(qrImageUrl, (img) => {
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
                                id: `qr_${targetFormCardIndex}_${targetSide}_${Math.random().toString(36).substr(2, 5)}` // ID فريد ومميز للهدف
                            });

                            targetCanvas.add(img);
                            img.bringToFront();
                            targetCanvas.setActiveObject(img);
                            targetCanvas.renderAll();

                            // حفظ التغييرات على الكانفاس الهدف (الإضافة)
                            saveITextObjectsFromSpecificCanvas(targetCanvas, targetCardIdForSave, targetDataSource);
                            console.log(`تم إفلات QR Code على ${targetCardIdForSave} في (${pointer.x}, ${pointer.y})`);
                        }, { crossOrigin: 'Anonymous' }, (err) => {
                            console.error('Error loading QR code during drag-and-drop:', err);
                        });

                    } else if (currentlyDraggedFabricObject.type === 'i-text') {
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
                            id: `text_${targetFormCardIndex}_${targetSide}_${Math.random().toString(36).substr(2, 5)}` // ID فريد ومميز للهدف
                        });

                        targetCanvas.add(newObject);
                        newObject.bringToFront();
                        targetCanvas.setActiveObject(newObject);
                        targetCanvas.renderAll();

                        const targetCardIdForSave = targetCardId; // المعرف الكامل للهدف
                        const targetDataSource = targetCardId.includes('attendance') ? attendanceCardData : certificateCardData;
                        saveITextObjectsFromSpecificCanvas(targetCanvas, targetCardIdForSave, targetDataSource);
                        console.log(`تم إفلات النص '${newObject.text}' على ${targetCardIdForSave} في (${pointer.x}, ${pointer.y})`);
                    }

                } else {
                    // منطق الإفلات الفاشل أو الإفلات على نفس الكانفاس (إرجاع العنصر)
                    currentlyDraggedFabricObject.set({ opacity: 1, selectable: true, evented: true });
                    startDragCanvas.renderAll();
                    startDragCanvas.setActiveObject(currentlyDraggedFabricObject);
                    console.log('تم إرجاع العنصر للكانفاس الأصلي.');
                }

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



    // قم بتحديث تعريف الدالة لتقبل formCardIndex و currentSide
    function displayHeadersOnSpecificCanvas(canvas, headers, cardIdentifier, cardDataSource, formCardIndex, currentSide, objectPositions = {}) {
        if (!canvas || !headers || !Array.isArray(headers)) {
            console.warn('Invalid arguments for displayHeadersOnSpecificCanvas. Canvas, headers, or headers array is missing.');
            return;
        }

        // تحديد ما إذا كان هذا الكارت يخص الحضور أم الشهادة
        const isAttendance = cardIdentifier.includes('attendance_template_data_file_path');
        // dataSource هو الآن الكائن الأعلى (attendanceCardData أو certificateCardData)
        // وليس الكائن الفرعي للكارد، لأنه يتم تمريرها إلى saveITextObjectsFromSpecificCanvas
        // والتي تقوم بمعالجة الهيكل المتداخل.


        // دالة مساعدة لتحديد النوع والجانب بناءً على cardIdentifier
        // يمكننا تبسيطها الآن حيث لدينا formCardIndex و currentSide بشكل صريح
        // ولكن سنتركها لمساعدتنا في QR Subtype
        function getTypeAndSideForQR(id, index, side) {
            if (id.includes('attendance_template_data_file_path')) {
                return { type: 'attendance', side: side };
            } else if (id.includes('document_template_file_path')) {
                return { type: 'certificate', side: side };
            }
            return { type: null, side: null };
        }

        const { type, side } = getTypeAndSideForQR(cardIdentifier, formCardIndex, currentSide);


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
                console.log(`Updated header "${header}" for card ${formCardIndex}, side ${currentSide} at (${position.left}, ${position.top})`);
            } else {
                const text = new fabric.IText(header, {
                    left: position.left,
                    top: position.top,
                    fontFamily: 'Arial',
                    fontSize: 20,
                    fill: '#000000',
                    selectable: true,
                    hasControls: true,
                    textBaseline: 'top',
                    id: `text_${formCardIndex}_${currentSide}_${Math.random().toString(36).substr(2, 5)}` // ID فريد ومميز
                });
                canvas.add(text);
                console.log(`Added header "${header}" for card ${formCardIndex}, side ${currentSide} at (${position.left}, ${position.top})`);
            }
        });

        // إضافة رمز QR واحد فقط إذا لم يكن موجودًا
        if (type && side) {
            const qrSubType = `${type}-${side}`;
            // البحث عن QR code الموجود بناءً على type و subtype
            const existingQR = canvas.getObjects().find(obj => obj.type === 'qr-code' && obj.subtype === qrSubType);
            if (!existingQR) {
                const qrImageUrl = '/assets/qr-code.jpg';
                fabric.Image.fromURL(qrImageUrl, (img) => {
                    if (!img) {
                        console.error(`Failed to load QR code image from ${qrImageUrl} for card ${formCardIndex}, side ${currentSide}`);
                        return;
                    }
                    img.set({
                        left: 100,
                        top: 100,
                        scaleX: 0.3,
                        scaleY: 0.3,
                        selectable: true,
                        hasControls: true,
                        type: 'qr-code',
                        subtype: qrSubType,
                        width: img.width || 100,
                        height: img.height || 100,
                        id: `qr_${formCardIndex}_${currentSide}_${Math.random().toString(36).substr(2, 5)}` // ID فريد ومميز
                    });
                    canvas.add(img);
                    canvas.renderAll();
                    console.log(`Added QR code for ${qrSubType} on card ${formCardIndex}, side ${currentSide} at (100, 100)`);
                    // حفظ التغييرات بعد إضافة QR code
                    saveITextObjectsFromSpecificCanvas(canvas, cardIdentifier, cardDataSource);
                }, { crossOrigin: 'Anonymous' }, (err) => {
                    console.error(`Error loading QR code from ${qrImageUrl} for card ${formCardIndex}, side ${currentSide}:`, err);
                });
            }
        }

        canvas.renderAll();
        // حفظ التغييرات على الكانفاس بعد إضافة الرؤوس (وليس QR code فقط)
        // تأكد أن `saveITextObjectsFromSpecificCanvas` تتلقى `cardDataSource` الصحيح
        // وهي الآن تفهم البنية المتداخلة بفضل التعديل السابق.
        if (typeof saveITextObjectsFromSpecificCanvas === 'function') {
            saveITextObjectsFromSpecificCanvas(canvas, cardIdentifier, cardDataSource);
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




    // قم بتحديث توقيع الدالة لتقبل formCardIndex
    function setupFileCard(fileCardElement, imageUrl = null, isAttendanceExplicit = false, formCardIndex) {
        const fileInput = fileCardElement.querySelector('.file-input');
        const fabricCanvasContainer = fileCardElement.querySelector('.fabric-canvas-container');
        const initialUploadState = fileCardElement.querySelector('.initial-upload-state');
        const removePreviewBtn = fileCardElement.querySelector('.remove-preview-btn');
        const sideInput = fileCardElement.querySelector('.side-input');
        // fileHub لم يعد ضروريًا بنفس الطريقة لتحديد isAttendance بفضل isAttendanceExplicit و formCardIndex
        // ومع ذلك، قد يكون مفيدًا لاحقًا للعثور على العناصر الأبوية
        const fileHub = fileCardElement.closest('.js-filehub'); // أزالنا .attendance-filehub هنا

        // تحديد ما إذا كان هذا الكارت يخص الحضور أم الشهادة بناءً على isAttendanceExplicit
        const isAttendance = isAttendanceExplicit;

        console.log(`Setting up file card for formCardIndex: ${formCardIndex}, isAttendance: ${isAttendance}`);
        console.log('File input name BEFORE adjustment:', fileInput?.name);
        console.log('Side input name BEFORE adjustment:', sideInput?.name);

        if (!fileHub) {
            console.error('No fileHub found for fileCardElement:', fileCardElement);
            return;
        }
        if (!sideInput) {
            console.error('sideInput not found in fileCardElement:', fileCardElement);
            return;
        }
        const currentSide = sideInput.value || 'front';
        if (!sideInput.value) {
            sideInput.value = 'front'; // تأكد أن له قيمة افتراضية
            console.warn('sideInput.value was empty, defaulting to "front"');
        }


        // 1. تحديث أسماء المدخلات (fileInput و sideInput) لتكون فريدة لكل كارت وكل جانب
        if (isAttendance) {
            // Attendance names: attendance_template_file_path_1_front, attendance_template_sides_1_front
            fileInput.name = `attendance_template_file_path_${formCardIndex}_${currentSide}`;
            sideInput.name = `attendance_template_sides_${formCardIndex}`; // الاسم لمجموعة الراديو
        } else {
            // Certificate names: document_template_file_path_1_front, certificate_template_sides_1_front
            fileInput.name = `document_template_file_path_${formCardIndex}_${currentSide}`;
            sideInput.name = `certificate_template_sides_${formCardIndex}`; // الاسم لمجموعة الراديو
        }
        console.log('File input name AFTER adjustment:', fileInput.name);
        console.log('Side input name AFTER adjustment:', sideInput.name);


        // 2. بناء cardIdentifier الفريد باستخدام formCardIndex والجانب
        const cardIdentifier = isAttendance
            ? `attendance_template_data_file_path_${formCardIndex}-${currentSide}`
            : `document_template_file_path_${formCardIndex}-${currentSide}`;
        console.log('Final cardIdentifier:', cardIdentifier);


        // 3. تحديد كائن البيانات الصحيح (attendanceCardData أو certificateCardData)
        // والوصول إلى بيانات الكارد الصحيحة باستخدام formCardIndex والجانب
        const cardDataSource = isAttendance ? attendanceCardData : certificateCardData;
        // التأكد من وجود الكائن الفرعي للكارد والفهرس والجانب
        cardDataSource[formCardIndex] = cardDataSource[formCardIndex] || {};
        cardDataSource[formCardIndex][currentSide] = cardDataSource[formCardIndex][currentSide] || {
            fabricCanvas: null,
            objects: [], // سنستخدم 'objects' كاسم عام للتخزين هنا، ثم نقسمها إلى texts و qrCodes عند الحفظ
            imageUrl: null,
            objectPositions: {},
            canvasWidth: 0,
            canvasHeight: 0
        };

        // هذا هو الكائن الذي سنعمل عليه مباشرة (لتبسيط الكود)
        const currentCardData = cardDataSource[formCardIndex][currentSide];


        // 4. استخدام imageUrl الممرر أو القيمة الموجودة في currentCardData
        if (imageUrl) {
            currentCardData.imageUrl = imageUrl;
        } else if (!currentCardData.imageUrl && fileInput?.files.length > 0) { // تم تغيير fileInput?.value إلى fileInput?.files.length > 0
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    currentCardData.imageUrl = event.target.result;
                    updateCardDisplayState(true);
                    initializeCanvas();
                };
                reader.readAsDataURL(file);
            }
        }


        if (!fileInput || !fabricCanvasContainer || !initialUploadState || !removePreviewBtn) {
            console.warn('One or more essential elements missing in fileCardElement for setupFileCard:', fileCardElement);
            return;
        }

        const excelInput = document.getElementById(isAttendance ? 'badge-excel-input-2' : 'excel-input-model-2');
        if (!excelInput) {
            console.error(`Excel input not found for ${isAttendance ? 'attendance' : 'certificate'}:`, isAttendance ? 'badge-excel-input-2' : 'excel-input-model-2');
            // قد تحتاج إلى اتخاذ إجراءات إضافية هنا، مثل تعطيل بعض الميزات
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
            if (currentCardData.imageUrl) {
                // إزالة أي كانفاسات موجودة لتجنب تراكمها
                fabricCanvasContainer.querySelectorAll('canvas, iframe').forEach(el => el.remove());

                currentTemplateCanvasElement = document.createElement('canvas');
                currentTemplateCanvasElement.setAttribute('data-card-id', cardIdentifier);
                // تأكد من أن الـ ID صالح للاستخدام في HTML (لا يحتوي على [ أو ])
                currentTemplateCanvasElement.setAttribute('id', `canvas-${cardIdentifier.replace(/[^a-zA-Z0-9-]/g, '')}`);
                currentTemplateCanvasElement.style.width = '100%';
                currentTemplateCanvasElement.style.height = '100%';
                currentTemplateCanvasElement.style.display = 'block';
                fabricCanvasContainer.prepend(currentTemplateCanvasElement);

                // تمرير cardIdentifier إلى initializeTemplateCanvas
                const canvas = initializeTemplateCanvas(currentTemplateCanvasElement, currentCardData.imageUrl, cardIdentifier);
                if (canvas) {
                    currentCardData.fabricCanvas = canvas;
                    // يجب أن نكون حذرين بشأن activeCanvas هنا
                    // activeCanvas يجب أن يتم تعيينه فقط عندما يتفاعل المستخدم مع كانفاس معين
                    // أو نستخدم آلية لتحديد الكانفاس النشط للكارت المحدد
                    // activeCanvas = canvas; // تم إزالة هذا السطر مؤقتًا هنا
                    console.log(`Canvas initialized for: ${cardIdentifier}`);
                    setupDragDrop(cardIdentifier, cardDataSource, formCardIndex, currentSide); // تمرير cardDataSource
                }
            } else {
                fabricCanvasContainer.innerHTML = '';
                // لا تضف fileInput مباشرة هنا، اتركه في مكانه الأصلي في DOM
                updateCardDisplayState(false);
            }
            restoreITextObjects(cardIdentifier, cardDataSource); // تمرير cardDataSource
        };


        // دالة restoreITextObjects تحتاج إلى الوصول إلى cardDataSource
        function restoreITextObjects(cardIdentifier, dataSource) {
            const cardDataItem = dataSource[formCardIndex][currentSide];
            if (cardDataItem.fabricCanvas) {
                // الآن، نستدعي restoreITextObjectsOnSpecificCanvas مباشرة،
                // والتي تقوم بقراءة البيانات من الحقول المخفية.
                restoreITextObjectsOnSpecificCanvas(cardDataItem.fabricCanvas, cardIdentifier, dataSource);
                cardDataItem.fabricCanvas.renderAll();
                console.log(`Restored objects for ${cardIdentifier}`);
            } else {
                console.log(`No canvas to restore objects for ${cardIdentifier}`);
            }
        }


        // يجب تحديث setupDragDrop و updateObjectPosition و saveITextObjectsFromSpecificCanvas
        // لقبول cardDataSource و formCardIndex لتحديد الكائن الصحيح في الهيكل المتداخل

        function setupDragDrop(cardIdentifier, dataSource, currentFormCardIndex, currentSide) {
            const canvas = dataSource[currentFormCardIndex][currentSide].fabricCanvas;
            if (!canvas) return;

            canvas.on('object:moving', (e) => {
                const obj = e.target;
                if (obj.type === 'i-text' || obj.type === 'qr-code') {
                    // ... منطق تحريك الكائن ...
                    // يجب أن نستخدم updateObjectPosition مع المعرفات الجديدة
                    updateObjectPosition(cardIdentifier, canvas, obj, dataSource, currentFormCardIndex, currentSide);
                }
            });

            canvas.on('object:modified', function(e) {
                const obj = e.target;
                if (obj.type === 'i-text' || obj.type === 'qr-code') {
                    activeCanvas = this;
                    updateObjectPosition(this.cardIdentifier, canvas, obj, dataSource, currentFormCardIndex, currentSide);
                }
            });

            canvas.on('mouse:down', function(e) {
                activeCanvas = this;
                console.log('Mouse down, activeCanvas set to:', this.cardIdentifier);
            });
        }


        // دالة updateObjectPosition تحتاج إلى تعديل كبير للوصول إلى البيانات بشكل صحيح
        function updateObjectPosition(cardIdentifier, canvas, object, dataSource, currentFormCardIndex, currentSide) {
            if (!canvas || !object || !object.id) {
                console.error('Invalid canvas or object ID in updateObjectPosition');
                return;
            }

            let inputFieldName;
            if (isAttendance) { // استخدام isAttendance بدلاً من التحقق من cardIdentifier
                inputFieldName = 'attendance_text_data';
            } else {
                inputFieldName = 'certificate_text_data';
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

            // هنا التعديل الجوهري: يجب أن يتم الوصول إلى البيانات باستخدام formCardIndex و currentSide
            if (!currentInputData[currentFormCardIndex]) {
                currentInputData[currentFormCardIndex] = {};
            }
            if (!currentInputData[currentFormCardIndex][currentSide]) {
                currentInputData[currentFormCardIndex][currentSide] = {
                    type: currentSide.includes('front') ? 'certificate-front' : 'certificate-back', // أو attendance-front/back
                    canvasWidth: canvas.width,
                    canvasHeight: canvas.height,
                    texts: [],
                    qrCodes: []
                };
            }

            const sideData = currentInputData[currentFormCardIndex][currentSide];
            const texts = sideData.texts || [];
            const qrCodes = sideData.qrCodes || [];

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
                objectData.subtype = object.subtype;
                objectData.width = object.width * (object.scaleX || 1);
                objectData.height = object.height * (object.scaleY || 1);

                if (qrCodeIndex !== -1) {
                    qrCodes[qrCodeIndex] = objectData;
                } else {
                    qrCodes.push(objectData);
                }
            }

            sideData.texts = texts;
            sideData.qrCodes = qrCodes;
            sideData.canvasWidth = canvas.width;
            sideData.canvasHeight = canvas.height;

            inputField.value = JSON.stringify(currentInputData);
            console.log(`Value set to ${inputFieldName} input for card ${currentFormCardIndex}, side ${currentSide}:`, inputField.value);
            console.log(`Updated position for ${object.type === 'i-text' ? object.text : object.subtype} in ${cardIdentifier}`, objectData);
        }

        // saveITextObjectsFromSpecificCanvas تحتاج أيضًا إلى تعديل لكي تتوافق مع البنية الجديدة
        // يمكننا تبسيطها لكي تعتمد على updateObjectPosition أو تمرير المزيد من السياق.
        // حاليًا، اسمها يشير إلى أنها تحفظ iTextObjects فقط، لكن الكود يبدو أنه يحفظ كل شيء.
        // سأعدلها لتعمل مع البنية الجديدة وتكون أكثر وضوحًا.
        function saveITextObjectsFromSpecificCanvas(canvas, cardIdentifier, allCardDataParent) {
            if (!canvas || !cardIdentifier || !allCardDataParent) {
                console.warn('Cannot save objects: Invalid canvas, cardIdentifier, or allCardDataParent.');
                return;
            }

            // استخراج formCardIndex والجانب من cardIdentifier
            const parts = cardIdentifier.split(/_(.+)/); // تقسيم عند أول _
            const prefix = parts[0]; // attendance_template_data_file_path أو document_template_file_path
            const indexAndSide = parts[1]; // 1-front أو 1-back

            const formCardIndexMatch = indexAndSide.match(/(\d+)-(.+)/);
            if (!formCardIndexMatch) {
                console.error('Invalid cardIdentifier format for saving:', cardIdentifier);
                return;
            }
            const currentFormCardIndex = parseInt(formCardIndexMatch[1]);
            const currentSide = formCardIndexMatch[2];

            // تحديد ما إذا كانت البيانات للحضور أو الشهادة
            const isAttendanceSave = cardIdentifier.includes('attendance_template_data_file_path');
            const inputFieldName = isAttendanceSave ? 'attendance_text_data' : 'certificate_text_data';
            const inputField = document.querySelector(`input[name="${inputFieldName}"]`);

            if (!inputField) {
                console.error(`Input field ${inputFieldName} not found for saving.`);
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

            // التأكد من وجود الهيكل الصحيح
            if (!currentInputData[currentFormCardIndex]) {
                currentInputData[currentFormCardIndex] = {};
            }
            if (!currentInputData[currentFormCardIndex][currentSide]) {
                currentInputData[currentFormCardIndex][currentSide] = {
                    type: isAttendanceSave ? `attendance-${currentSide}` : `certificate-${currentSide}`,
                    canvasWidth: canvas.width,
                    canvasHeight: canvas.height,
                    texts: [],
                    qrCodes: []
                };
            }

            const texts = canvas.getObjects()
                .filter(obj => obj.selectable && (obj.type === 'i-text'))
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

            currentInputData[currentFormCardIndex][currentSide].texts = texts;
            currentInputData[currentFormCardIndex][currentSide].qrCodes = qrCodes;
            currentInputData[currentFormCardIndex][currentSide].canvasWidth = canvas.width;
            currentInputData[currentFormCardIndex][currentSide].canvasHeight = canvas.height;

            inputField.value = JSON.stringify(currentInputData);
            console.log(`Saved canvas state for form card ${currentFormCardIndex}, side ${currentSide}:`, currentInputData[currentFormCardIndex][currentSide]);
        }


        updateCardDisplayState(currentCardData.imageUrl); // استخدم currentCardData

        // استمع إلى excelInput الذي يتوافق مع formCardIndex هذا
        const specificExcelInputId = isAttendance ? `badge-excel-input-2` : `excel-input-model-2`;
        const specificExcelInput = fileCardElement.closest('.form-card').querySelector(`#${specificExcelInputId}`);

        if (specificExcelInput) {
            specificExcelInput.addEventListener('change', () => {
                console.log(`Excel input changed for form card ${formCardIndex}, isAttendance: ${isAttendance}`);
                readFirstDataRow(isAttendance, (excelInfo) => {
                    if (excelInfo && excelInfo.headers && excelInfo.data) {
                        // تحديث البيانات العامة لـ Excel
                        if (isAttendance) attendanceExcelData = excelInfo;
                        else certificateExcelData = excelInfo;

                        // عرض الرؤوس على الكانفاس المحدد لهذا الكارت
                        if (currentCardData.fabricCanvas && currentSide === 'front') { // فقط على الوجه الأمامي
                            displayHeadersOnSpecificCanvas(currentCardData.fabricCanvas, excelInfo.headers, cardIdentifier, cardDataSource, formCardIndex, currentSide);
                            currentCardData.fabricCanvas.renderAll();
                        }
                    }
                }, formCardIndex); // تمرير formCardIndex إلى readFirstDataRow
            });
        } else {
            console.warn(`Specific Excel input for card ${formCardIndex}, type ${isAttendance ? 'attendance' : 'certificate'} not found.`);
        }


        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    currentCardData.imageUrl = event.target.result;
                    updateCardDisplayState(true);
                    initializeCanvas();
                };
                reader.readAsDataURL(file);
            } else {
                updateCardDisplayState(false);
                if (currentCardData.fabricCanvas) {
                    currentCardData.fabricCanvas.dispose();
                    currentCardData.fabricCanvas = null;
                }
                fabricCanvasContainer.innerHTML = '';
                // لا تضف removePreviewBtn هنا
                currentCardData.imageUrl = null;
                currentTemplateCanvasElement = null;
            }
        });

        removePreviewBtn.addEventListener('click', () => {
            fileInput.value = '';
            updateCardDisplayState(false);
            if (currentCardData.fabricCanvas) {
                currentCardData.fabricCanvas.dispose();
                currentCardData.fabricCanvas = null;
            }
            fabricCanvasContainer.innerHTML = '';
            currentCardData.imageUrl = null;
            currentTemplateCanvasElement = null;

            // مسح البيانات من الهيكل المتداخل
            if (cardDataSource[formCardIndex] && cardDataSource[formCardIndex][currentSide]) {
                delete cardDataSource[formCardIndex][currentSide];
                console.log(`Cleared data for card ${formCardIndex}, side ${currentSide} after removal.`);
            }
        });


        // إضافة حدث للتحقق من أسماء الحقول عند إرسال النموذج - هذا الجزء يحتاج إلى تعديل شامل
        // لأنه يفترض بنية أسماء حقول موحدة (مثل attendance_template_file_path[])
        // ولكننا الآن نستخدم أسماء فريدة لكل كارت (attendance_template_file_path_1_front)
        const form = document.querySelector('#documentGenerationForm');
        if (form && !form.dataset.submitListenerAdded) {
            form.addEventListener('submit', (e) => {
                // لا تمنع الإرسال هنا إذا كنت تريد إرسال النموذج
                // e.preventDefault();
                console.log('Form submit initiated...');

                // هنا يجب أن يتم جمع البيانات من جميع الكروت الديناميكية
                // وهذا يتطلب منطقًا معقدًا يتجاوز نطاق هذا التعديل الفوري
                // حاليًا، أسماء المدخلات ستكون:
                // attendance_template_file_path_1_front
                // attendance_template_sides_1
                // attendance_template_file_path_2_front
                // attendance_template_sides_2
                // وهكذا...
                // يجب أن يكون الباك إند قادرًا على معالجة هذه الأسماء المتغيرة.

                // للتحقق، يمكنك تسجيل جميع حقول الملفات والجانب
                form.querySelectorAll('input[type="file"], input.side-input').forEach(input => {
                    console.log(`Final input name on submit: ${input.name}, value: ${input.value}`);
                });

                // التأكد من حفظ آخر التغييرات على جميع الكانفاسات قبل الإرسال
                Object.values(certificateCardData).forEach(cardObj => {
                    Object.values(cardObj).forEach(sideObj => {
                        if (sideObj.fabricCanvas) {
                            saveITextObjectsFromSpecificCanvas(sideObj.fabricCanvas, sideObj.fabricCanvas.cardIdentifier, certificateCardData);
                        }
                    });
                });
                Object.values(attendanceCardData).forEach(cardObj => {
                    Object.values(cardObj).forEach(sideObj => {
                        if (sideObj.fabricCanvas) {
                            saveITextObjectsFromSpecificCanvas(sideObj.fabricCanvas, sideObj.fabricCanvas.cardIdentifier, attendanceCardData);
                        }
                    });
                });


            });
            form.dataset.submitListenerAdded = 'true';
        }


        if (currentCardData.imageUrl) initializeCanvas();
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



    // قم بتحديث تعريف الدالة لتقبل formCardIndex
    function createAttachmentCard(isBack, formCardIndex) {
        const div = document.createElement('div');
        div.className = 'attachment-card filebox-card border-2 border-dashed border-gray-400 rounded-lg p-6 flex flex-col items-center gap-4 mb-4 hover:border-blue-600 transition-colors duration-300 relative min-h-[200px]';

        // قم بإنشاء أسماء حقول فريدة هنا باستخدام formCardIndex
        const fileInputName = `attendance_template_file_path_${formCardIndex}`;
        const sideInputName = `attendance_template_sides_${formCardIndex}`;
        const sideValue = isBack ? 'back' : 'front';

        div.innerHTML = `
        <div class="initial-upload-state flex flex-col items-center gap-4">
            <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 file-icon"></i>
            <h4 class="text-lg font-semibold">${isBack ? 'الوجه الخلفي للمرفق' : 'الوجه الأمامي للمرفق'}</h4>
            <p class="text-center text-gray-600">قم برفع ملفات PDF أو صور فقط.</p>
            <label class="inline-flex items-center gap-3 px-6 py-3 bg-blue-600 text-white rounded-md cursor-pointer hover:bg-blue-700 transition">
                <i class="fas fa-upload"></i>
                أرفاق PDF وصور
                <input name="${fileInputName}" type="file" class="sr-only file-input" accept="application/pdf,image/*">
                <input type="hidden" name="${sideInputName}" class="side-input" value="${sideValue}">
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

        // هذه الخطوة `$('#attachment-cards-container').append(div);`
        // هي التي يجب أن تحدث في `renderAttendanceCards` وليس هنا،
        // لأن `renderAttendanceCards` هي التي تحدد أين يتم إضافة الكارت.
        // لذا، سنقوم بإزالة هذا السطر من هنا.

        // تمرير formCardIndex إلى setupFileCard
        // Note: the third argument (isAttendance) is true here
        setupFileCard(div, null, true, formCardIndex);
        return div;
    }


    const fileTpl = document.getElementById('file-template').content;
    let docIdx = 0;




    // قم بتحديث تعريف الدالة لتقبل formCardIndex
    function renderDocumentCards(block, formCardIndex) {
        const frontRadio = block.querySelector('input.js-face[data-face="front"]');
        const backRadio = block.querySelector('input.js-face[data-face="back"]');
        const hub = block.querySelector('.js-filehub');

        if (!frontRadio || !hub) {
            console.warn('frontRadio or hub not found in renderDocumentCards.');
            return;
        }

        // هنا سنعدل كيفية الوصول إلى البيانات
        // سنستخدم certificateCardData[formCardIndex] ككائن فرعي لكل كارت
        certificateCardData[formCardIndex] = certificateCardData[formCardIndex] || {};

        const allCardElements = hub.querySelectorAll('.filebox-card');
        let backITextObjects = [];

        // Save existing canvas data before clearing
        allCardElements.forEach(cardElement => {
            const sideInput = cardElement.querySelector('.side-input');
            if (sideInput) {
                // المعرف يجب أن يكون خاصًا بهذا الكارت (formCardIndex)
                const currentSide = sideInput.value; // 'front' or 'back'
                const cardIdentifier = `document_template_file_path_${formCardIndex}-${currentSide}`; // بناء معرف فريد

                // استخدم certificateCardData[formCardIndex][currentSide] للوصول إلى بيانات الكانفاس
                const canvasDataForSide = certificateCardData[formCardIndex][currentSide];

                if (canvasDataForSide && canvasDataForSide.fabricCanvas) {
                    // حفظ البيانات قبل مسحها
                    saveITextObjectsFromSpecificCanvas(canvasDataForSide.fabricCanvas, cardIdentifier, certificateCardData[formCardIndex]);
                    if (currentSide === 'back' && frontRadio.checked) {
                        // إذا كنا ننتقل من وجهين إلى وجه واحد، قم بحفظ كائنات الوجه الخلفي
                        backITextObjects = [...(canvasDataForSide.iTextObjects || [])];
                        console.log(`Saving back iTextObjects for ${cardIdentifier}:`, backITextObjects);
                    }
                }
            }
        });

        hub.innerHTML = ''; // مسح المحتوى الحالي للـ hub

        // ----------------------------------------------------
        // إنشاء كارت الوجه الأمامي
        // ----------------------------------------------------
        const f = document.importNode(fileTpl, true);
        f.querySelector('.card-title').textContent = 'تحميل مستندات – الوجه الأمامي';
        // تحديث أسماء المدخلات لتكون فريدة باستخدام formCardIndex
        f.querySelector('.side-input').name = `certificate_template_sides_${formCardIndex}`;
        f.querySelector('.side-input').value = 'front';
        f.querySelector('.file-input').name = `document_template_file_path_${formCardIndex}`; // اسم ملف فريد
        const frontCardElement = f.querySelector('.filebox-card');
        hub.appendChild(f);

        // معرف الكارد الأمامي لـ setupFileCard
        const frontCardIdForSetup = `document_template_file_path_${formCardIndex}-front`;
        setupFileCard(frontCardElement, certificateCardData[formCardIndex]?.front?.imageUrl, false, formCardIndex);

        // تحديث بنية التخزين
        if (!certificateCardData[formCardIndex].front) {
            certificateCardData[formCardIndex].front = { fabricCanvas: null, objects: [], imageUrl: null, canvasWidth: 0, canvasHeight: 0 };
        }

        // استعادة كانفاس الوجه الأمامي إذا كانت الصورة موجودة
        if (certificateCardData[formCardIndex].front.imageUrl) {
            const frontCanvasContainer = frontCardElement.querySelector('.fabric-canvas-container');
            frontCanvasContainer.classList.remove('hidden');
            frontCardElement.querySelector('.initial-upload-state').classList.add('hidden');
            frontCardElement.querySelector('.remove-preview-btn').style.display = 'flex';

            const canvasEl = document.createElement('canvas');
            canvasEl.setAttribute('data-card-id', frontCardIdForSetup); // نفس المعرف المستخدم في setupFileCard
            canvasEl.setAttribute('id', `canvas-${frontCardIdForSetup.replace(/[^a-zA-Z0-9-]/g, '')}`); // ID صالح
            canvasEl.style.width = '100%';
            canvasEl.style.height = '100%';
            canvasEl.style.display = 'block';
            frontCanvasContainer.prepend(canvasEl);

            requestAnimationFrame(() => {
                setTimeout(() => {
                    const canvasInstance = initializeTemplateCanvas(canvasEl, certificateCardData[formCardIndex].front.imageUrl, frontCardIdForSetup);
                    certificateCardData[formCardIndex].front.fabricCanvas = canvasInstance; // تخزين المثيل
                    restoreITextObjectsOnSpecificCanvas(canvasInstance, frontCardIdForSetup); // استعادة الكائنات من البيانات المحفوظة

                    // إذا كان هناك objects iTextObjects من الوجه الخلفي عند التحويل إلى وجه واحد
                    if (backITextObjects.length > 0) {
                        backITextObjects.forEach(objData => {
                            const newObj = new fabric.IText(objData.text, {
                                left: objData.left,
                                top: objData.top,
                                fontFamily: objData.fontFamily || 'Arial',
                                fontSize: objData.fontSize || 20,
                                fill: objData.fill || '#000000',
                                selectable: true,
                                hasControls: true,
                            });
                            canvasInstance.add(newObj);
                        });
                        // حفظ هذه الكائنات الجديدة أيضًا
                        saveITextObjectsFromSpecificCanvas(canvasInstance, frontCardIdForSetup);
                    }
                    canvasInstance.renderAll();
                }, 50);
            });
        }

        // ----------------------------------------------------
        // إنشاء كارت الوجه الخلفي (إذا تم تحديده)
        // ----------------------------------------------------
        if (backRadio && backRadio.checked) {
            const b = document.importNode(fileTpl, true);
            b.querySelector('.card-title').textContent = 'تحميل مستندات – الوجه الخلفي';
            // تحديث أسماء المدخلات لتكون فريدة باستخدام formCardIndex
            b.querySelector('.side-input').name = `certificate_template_sides_${formCardIndex}`;
            b.querySelector('.side-input').value = 'back';
            b.querySelector('.file-input').name = `document_template_file_path_${formCardIndex}`; // اسم ملف فريد
            const backCardElement = b.querySelector('.filebox-card');
            hub.appendChild(b);

            const backCardIdForSetup = `document_template_file_path_${formCardIndex}-back`;
            setupFileCard(backCardElement, certificateCardData[formCardIndex]?.back?.imageUrl, false, formCardIndex);

            if (!certificateCardData[formCardIndex].back) {
                certificateCardData[formCardIndex].back = { fabricCanvas: null, objects: [], imageUrl: null, canvasWidth: 0, canvasHeight: 0 };
            }

            // استعادة كانفاس الوجه الخلفي إذا كانت الصورة موجودة
            if (certificateCardData[formCardIndex].back.imageUrl) {
                const backCanvasContainer = backCardElement.querySelector('.fabric-canvas-container');
                backCanvasContainer.classList.remove('hidden');
                backCardElement.querySelector('.initial-upload-state').classList.add('hidden');
                backCardElement.querySelector('.remove-preview-btn').style.display = 'flex';

                const canvasEl = document.createElement('canvas');
                canvasEl.setAttribute('data-card-id', backCardIdForSetup);
                canvasEl.setAttribute('id', `canvas-${backCardIdForSetup.replace(/[^a-zA-Z0-9-]/g, '')}`);
                canvasEl.style.width = '100%';
                canvasEl.style.height = '100%';
                canvasEl.style.display = 'block';
                backCanvasContainer.prepend(canvasEl);

                requestAnimationFrame(() => {
                    setTimeout(() => {
                        const canvasInstance = initializeTemplateCanvas(canvasEl, certificateCardData[formCardIndex].back.imageUrl, backCardIdForSetup);
                        certificateCardData[formCardIndex].back.fabricCanvas = canvasInstance;
                        restoreITextObjectsOnSpecificCanvas(canvasInstance, backCardIdForSetup);
                        canvasInstance.renderAll();
                    }, 50);
                });
            }
        } else {
            // إذا لم يكن الوجه الخلفي محددًا، قم بمسح بياناته
            const backCardIdForClear = `document_template_file_path_${formCardIndex}-back`;
            if (certificateCardData[formCardIndex].back) {
                if (certificateCardData[formCardIndex].back.fabricCanvas) {
                    certificateCardData[formCardIndex].back.fabricCanvas.dispose();
                }
                delete certificateCardData[formCardIndex].back;
                console.log(`Cleared back card data for ${backCardIdForClear}`);
            }
        }
    }


    // قم بتحديث تعريف الدالة لتقبل formCardIndex
    function renderAttendanceCards(block, initial = false, formCardIndex) {
        const containers = block.querySelectorAll('.attachments-container');
        if (containers.length === 0) {
            console.warn('No .attachments-container found in attendance block.');
            return;
        }
        const container = containers[0];

        // إضافة الكلاسات لضمان التعرف على الحاوية كجزء من الحضور
        // هذه الكلاسات يجب أن تكون موجودة على الـ container بالفعل في قالب Blade لكي تعمل selectors بشكل صحيح
        // container.classList.add('js-filehub', 'attendance-filehub'); // قد لا تحتاج لإضافتها هنا إذا كانت موجودة في القالب
        console.log('Container classes:', container.className);

        const one = block.querySelector(`input[name="attendance_template_sides_${formCardIndex}"][value="front"]`); // تحديث الاسم
        const two = block.querySelector(`input[name="attendance_template_sides_${formCardIndex}"][value="back"]`);   // تحديث الاسم

        let count = initial ? 1 : (two && two.checked ? 2 : 1);

        container.innerHTML = ''; // مسح المحتوى الحالي للحاوية

        // هنا سنعدل كيفية الوصول إلى البيانات
        // سنستخدم attendanceCardData[formCardIndex] ككائن فرعي لكل كارت
        attendanceCardData[formCardIndex] = attendanceCardData[formCardIndex] || {};

        for (let i = 0; i < count; i++) {
            // تمرير formCardIndex إلى createAttachmentCard
            const newCardElement = createAttachmentCard(i === 1, formCardIndex);
            container.appendChild(newCardElement);

            const currentSide = (i === 0) ? 'front' : 'back';
            const cardIdForSetup = `attendance_template_data_file_path_${formCardIndex}-${currentSide}`;

            // تمرير formCardIndex و isAttendance (true) إلى setupFileCard
            setupFileCard(newCardElement, attendanceCardData[formCardIndex]?.[currentSide]?.imageUrl, true, formCardIndex);

            // تحديث بنية التخزين
            if (!attendanceCardData[formCardIndex][currentSide]) {
                attendanceCardData[formCardIndex][currentSide] = { fabricCanvas: null, objects: [], imageUrl: null, objectPositions: {}, canvasWidth: 0, canvasHeight: 0 };
            }

            // استعادة الكانفاس إذا كانت الصورة موجودة
            if (attendanceCardData[formCardIndex][currentSide].imageUrl) {
                const canvasContainer = newCardElement.querySelector('.fabric-canvas-container');
                canvasContainer.classList.remove('hidden');
                newCardElement.querySelector('.initial-upload-state').classList.add('hidden');
                newCardElement.querySelector('.remove-preview-btn').style.display = 'flex';

                const canvasEl = document.createElement('canvas');
                canvasEl.setAttribute('data-card-id', cardIdForSetup);
                canvasEl.setAttribute('id', `canvas-${cardIdForSetup.replace(/[^a-zA-Z0-9-]/g, '')}`);
                canvasEl.style.width = '100%';
                canvasEl.style.height = '100%';
                canvasEl.style.display = 'block';
                canvasContainer.prepend(canvasEl);

                requestAnimationFrame(() => {
                    setTimeout(() => {
                        const canvasInstance = initializeTemplateCanvas(canvasEl, attendanceCardData[formCardIndex][currentSide].imageUrl, cardIdForSetup);
                        attendanceCardData[formCardIndex][currentSide].fabricCanvas = canvasInstance; // تخزين المثيل
                        restoreITextObjectsOnSpecificCanvas(canvasInstance, cardIdForSetup);
                        canvasInstance.renderAll();
                    }, 50);
                });
            }
        }

        // منطق مسح بيانات الوجه الخلفي إذا تم التحويل إلى وجه واحد
        if (count === 1) {
            const backCardKey = 'back';
            if (attendanceCardData[formCardIndex][backCardKey]) {
                if (attendanceCardData[formCardIndex][backCardKey].fabricCanvas) {
                    attendanceCardData[formCardIndex][backCardKey].fabricCanvas.dispose();
                }
                delete attendanceCardData[formCardIndex][backCardKey];
                console.log(`Cleared back card data for attendance card ${formCardIndex}`);
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


    // قم بتحديث تعريف الدالة لتقبل formCardIndex
    function initDocumentBlock(block, formCardIndex) {
        // استخدم formCardIndex لتحديد ما إذا كان البلوك قد تمت تهيئته بالفعل لهذا الكارت
        if (block.dataset.inited === formCardIndex.toString()) return; // التحقق من الفهرس المحدد
        block.dataset.inited = formCardIndex.toString(); // تخزين الفهرس كعلامة للتهيئة

        // استخدم formCardIndex لإنشاء اسم فريد لمجموعات الراديو
        const uniqueNameForRadio = `doc-face-${formCardIndex}`;

        const front = block.querySelector('input.js-face[data-face="front"]');
        const back = block.querySelector('input.js-face[data-face="back"]');

        if (front && back) {
            front.name = uniqueNameForRadio; // تعيين اسم فريد لكل كارت
            back.name = uniqueNameForRadio;   // تعيين نفس الاسم لمجموعة الراديو
            front.checked = true;
            back.checked = false;
        } else if (front) {
            front.name = uniqueNameForRadio; // تعيين اسم فريد
            front.checked = true;
        }
        // ملاحظة: تم إزالة docIdx++ من هنا لأننا نستخدم formCardCounter العام.

        // تمرير formCardIndex إلى renderDocumentCards
        renderDocumentCards(block, formCardIndex);
    }

    // قم بتحديث تعريف الدالة لتقبل formCardIndex
    function initAttendanceBlock(block, formCardIndex) {
        // استخدم formCardIndex لتحديد ما إذا كان البلوك قد تمت تهيئته بالفعل لهذا الكارت
        if (block.dataset.inited === formCardIndex.toString()) return; // التحقق من الفهرس المحدد
        block.dataset.inited = formCardIndex.toString(); // تخزين الفهرس كعلامة للتهيئة

        const one = block.querySelector('input[name="side"][value="1"]');
        const two = block.querySelector('input[name="side"][value="2"]');

        // تحديث أسماء حقول الراديو لتكون فريدة لكل كارت
        if (one) {
            one.name = `attendance_template_sides_${formCardIndex}`; // اسم فريد
            if (one.value === "1") one.checked = true; // التأكد من تحديد القيمة الافتراضية
        }
        if (two) {
            two.name = `attendance_template_sides_${formCardIndex}`; // اسم فريد
        }


        // تمرير formCardIndex إلى renderAttendanceCards
        renderAttendanceCards(block, true, formCardIndex);
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


    // قم بتحديث تعريف الدالة لتقبل formCardIndex و side بشكل صريح
// و cardDataSource سيكون الكائن الرئيسي (certificateCardData أو attendanceCardData)
    function showPreview(side, isAttendance, formCardIndex, excelFirstRowData = null) {
        let previewCanvas;
        console.log(`عرض المعاينة للكارت رقم: ${formCardIndex}, الوجه: ${side}, هل هو حضور؟: ${isAttendance}`);

        cleanDOM(); // تأكد من وجود دالة cleanDOM() في النطاق

        if (typeof side !== 'string' || typeof formCardIndex !== 'string' && typeof formCardIndex !== 'number') {
            console.error('Invalid side or formCardIndex provided for showPreview.');
            return;
        }

        const cardDataSource = isAttendance ? attendanceCardData : certificateCardData;
        // ⭐⭐ هنا التعديل الجوهري: الوصول إلى بيانات الكانفاس بالهيكل المتداخل ⭐⭐
        const currentCardDataEntry = cardDataSource[formCardIndex] ? cardDataSource[formCardIndex][side] : null;

        if (!currentCardDataEntry || !currentCardDataEntry.fabricCanvas) {
            console.error(`No fabricCanvas data found for card ${formCardIndex}, side ${side}.`);
            const canvasElement = document.createElement('canvas');
            document.body.appendChild(canvasElement);
            previewCanvas = createEmptyPreviewCanvas(canvasElement, `لا توجد بيانات كانفاس للكارت ${formCardIndex}, الوجه ${side}`);
            return;
        }

        const previewContainer = document.createElement('div');
        previewContainer.className = 'preview-container';
        previewContainer.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.7); display: flex; justify-content: center;
        align-items: center; z-index: 1000; overflow: auto;
    `;

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
        canvasElement.id = `preview-canvas-${formCardIndex}-${side}`; // ID فريد للكانفاس المعاينة
        previewWrapper.appendChild(closeButton);
        previewWrapper.appendChild(canvasElement);

        // ⭐⭐ منطق البطاقة السفلية (Verified by Pepasafe) ⭐⭐
        // يجب أن يعتمد على وجود الوجه الخلفي في نفس الكارت المحدد
        const hasBackSideForThisCard = !!(cardDataSource[formCardIndex]?.back?.imageUrl);
        const shouldShowBottomCard = (hasBackSideForThisCard && side === 'back') || (!hasBackSideForThisCard && side === 'front');

        if (shouldShowBottomCard) {
            const bottomCard = document.createElement('div');
            bottomCard.style.cssText = `
            background-color: white; border: 1px solid #ccc; border-radius: 4px;
            padding: 3px 8px; display: flex; flex-direction: row-reverse; gap: 8px;
            align-items: center; width: 64%; box-sizing: border-box;
            margin-top: -5px;
        `;

            const logoImg = document.createElement('img');
            logoImg.src = '/assets/logo.jpg';
            logoImg.alt = 'شعار الموقع';
            logoImg.style.height = '40px';

            const verifiedText = document.createElement('span');
            verifiedText.textContent = 'Verified by Pepasafe';
            verifiedText.style.cssText = `
            font-weight: bold;
            font-size: 14px;
            color: #4a5568;
        `;

            bottomCard.appendChild(logoImg);
            bottomCard.appendChild(verifiedText);
            previewWrapper.appendChild(bottomCard);
        }
        // ⭐⭐ نهاية منطق البطاقة السفلية ⭐⭐

        previewContainer.appendChild(previewWrapper);
        document.body.appendChild(previewContainer);

        // ⭐⭐ الوصول إلى الكانفاس الأصلي من currentCardDataEntry ⭐⭐
        const originalCanvas = currentCardDataEntry.fabricCanvas;

        if (originalCanvas) {
            const originalWidth = originalCanvas.width;
            const originalHeight = originalCanvas.height;

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
                height: previewHeight,
                selection: false, // لا نريد تحديد الكائنات في المعاينة
                evented: false    // لا نريد أحداث الكائنات في المعاينة
            });

            // ----------------------------------------------------
            // استعادة كائنات النص و QR من البيانات المحفوظة
            // ----------------------------------------------------
            // يجب أن نقرأ من الحقل المخفي نفسه للحصول على أحدث حالة محفوظة
            let inputFieldName;
            if (isAttendance) {
                inputFieldName = 'attendance_text_data';
            } else {
                inputFieldName = 'certificate_text_data';
            }
            const inputField = document.querySelector(`input[name="${inputFieldName}"]`);
            let storedData = {};
            if (inputField && inputField.value) {
                try {
                    storedData = JSON.parse(inputField.value);
                } catch (e) {
                    console.error('Error parsing stored text data for preview:', e);
                }
            }

            const cardSpecificStoredData = storedData[formCardIndex] ? storedData[formCardIndex][side] : null;

            const textsToRender = cardSpecificStoredData?.texts || [];
            const qrCodesToRender = cardSpecificStoredData?.qrCodes || [];
            const objectsToRender = [...textsToRender, ...qrCodesToRender];


            // ----------------------------------------------------
            // استنساخ وتعديل الكائنات
            // ----------------------------------------------------
            objectsToRender.forEach(objData => {
                if (objData.type === 'i-text') {
                    const textObject = new fabric.IText(objData.text, {
                        left: objData.left * scale,
                        top: objData.top * scale,
                        fontFamily: objData.fontFamily,
                        fontSize: objData.fontSize * scale, // scale font size too
                        fill: objData.fill,
                        angle: objData.angle,
                        textBaseline: objData.textBaseline || 'top',
                        textAlign: objData.textAlign || 'left',
                        fontWeight: objData.fontWeight || 'normal',
                        selectable: false, // لا يمكن تحديدها في المعاينة
                        evented: false,    // لا تستجيب للأحداث
                        hasControls: false
                    });

                    // ⭐ التعديل لإضافة بيانات Excel ⭐
                    if (excelFirstRowData && objData.text) {
                        const headerText = objData.text.trim();
                        if (excelFirstRowData.hasOwnProperty(headerText)) {
                            textObject.set('text', String(excelFirstRowData[headerText]));
                        }
                    }
                    previewCanvas.add(textObject);
                } else if (objData.type === 'qr-code') {
                    const qrImageUrl = '/assets/qr-code.jpg';
                    fabric.Image.fromURL(qrImageUrl, (img) => {
                        if (!img) {
                            console.error('Failed to load QR code image for preview:', objData.id);
                            return;
                        }
                        img.set({
                            left: objData.left * scale,
                            top: objData.top * scale,
                            scaleX: objData.scaleX * scale,
                            scaleY: objData.scaleY * scale,
                            angle: objData.angle,
                            width: objData.width, // العرض الأساسي
                            height: objData.height, // الارتفاع الأساسي
                            selectable: false,
                            evented: false,
                            hasControls: false,
                            type: 'qr-code',
                            subtype: objData.subtype
                        });
                        previewCanvas.add(img);
                        previewCanvas.renderAll();
                    }, { crossOrigin: 'Anonymous' });
                }
            });

            // ----------------------------------------------------
            // استنساخ الخلفية
            // ----------------------------------------------------
            if (originalCanvas.backgroundImage) {
                const backgroundImage = originalCanvas.backgroundImage;
                const clonedBackground = new fabric.Image(backgroundImage.getElement(), {
                    left: backgroundImage.left * scale,
                    top: backgroundImage.top * scale,
                    scaleX: backgroundImage.scaleX * scale,
                    scaleY: backgroundImage.scaleY * scale,
                    originX: backgroundImage.originX,
                    originY: backgroundImage.originY,
                    selectable: false,
                    evented: false
                });
                previewCanvas.setBackgroundImage(clonedBackground, previewCanvas.renderAll.bind(previewCanvas));
            }

            previewCanvas.renderAll(); // تأكد من إعادة الرسم بعد إضافة جميع الكائنات
        } else if (currentCardDataEntry.imageUrl) {
            // حالة عدم وجود كانفاس Fabric.js ولكن توجد صورة خلفية مباشرة
            // هذا الجزء قد لا يكون ضروريًا إذا كان initializeTemplateCanvas يتم استدعاؤه دائمًا لإنشاء الكانفاس
            console.warn(`No Fabric.js canvas found for card ${formCardIndex}, side ${side}, but imageUrl exists. Showing image directly.`);
            const img = new Image();
            img.src = currentCardDataEntry.imageUrl;
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
                    height: previewHeight,
                    selection: false,
                    evented: false
                });

                const fabricImage = new fabric.Image(img, {
                    left: 0,
                    top: 0,
                    scaleX: scale,
                    scaleY: scale,
                    selectable: false,
                    evented: false
                });
                previewCanvas.add(fabricImage);
                previewCanvas.sendToBack(fabricImage);

                // استعادة النصوص و QR Codes من البيانات المخزنة إذا وجدت
                let inputFieldName;
                if (isAttendance) {
                    inputFieldName = 'attendance_text_data';
                } else {
                    inputFieldName = 'certificate_text_data';
                }
                const inputField = document.querySelector(`input[name="${inputFieldName}"]`);
                let storedData = {};
                if (inputField && inputField.value) {
                    try {
                        storedData = JSON.parse(inputField.value);
                    } catch (e) {
                        console.error('Error parsing stored text data for preview (image only):', e);
                    }
                }
                const cardSpecificStoredData = storedData[formCardIndex] ? storedData[formCardIndex][side] : null;
                const textsToRender = cardSpecificStoredData?.texts || [];
                const qrCodesToRender = cardSpecificStoredData?.qrCodes || [];
                const objectsToRender = [...textsToRender, ...qrCodesToRender];

                objectsToRender.forEach((objData) => {
                    if (objData.type === 'i-text') {
                        const textObject = new fabric.IText(objData.text, {
                            left: objData.left * scale,
                            top: objData.top * scale,
                            fontFamily: objData.fontFamily || 'Arial',
                            fontSize: (objData.fontSize || 20) * scale,
                            fill: objData.fill || '#000000',
                            selectable: false,
                            evented: false,
                            hasControls: false,
                            textBaseline: 'alphabetic'
                        });
                        if (excelFirstRowData && objData.text) {
                            const headerText = objData.text.trim();
                            if (excelFirstRowData.hasOwnProperty(headerText)) {
                                textObject.set('text', String(excelFirstRowData[headerText]));
                            }
                        }
                        previewCanvas.add(textObject);
                    } else if (objData.type === 'qr-code') {
                        const qrImageUrl = '/assets/qr-code.jpg';
                        fabric.Image.fromURL(qrImageUrl, (img) => {
                            if (!img) {
                                console.error('Failed to load QR code image for preview (image only):', objData.id);
                                return;
                            }
                            img.set({
                                left: objData.left * scale,
                                top: objData.top * scale,
                                scaleX: objData.scaleX * scale,
                                scaleY: objData.scaleY * scale,
                                angle: objData.angle,
                                width: objData.width,
                                height: objData.height,
                                selectable: false,
                                evented: false,
                                hasControls: false,
                                type: 'qr-code',
                                subtype: objData.subtype
                            });
                            previewCanvas.add(img);
                            previewCanvas.renderAll();
                        }, { crossOrigin: 'Anonymous' });
                    }
                });

                previewCanvas.renderAll();
            };
            img.onerror = () => {
                console.error(`Failed to load image for card ${formCardIndex}, side ${side}`);
                previewCanvas = createEmptyPreviewCanvas(canvasElement, `فشل تحميل الصورة للكارت ${formCardIndex}, الوجه ${side}`);
            };
        } else {
            console.error(`No canvas or image found for card ${formCardIndex}, side ${side}.`);
            previewCanvas = createEmptyPreviewCanvas(canvasElement, `لا توجد بيانات كانفاس أو صورة للكارت ${formCardIndex}, الوجه ${side}`);
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



// دالة مساعدة لإنشاء زر في المودال
    function createModalButton(text, onClickHandler, className = '') {
        const button = document.createElement('button');
        button.textContent = text;
        button.className = `px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition ${className}`;
        button.addEventListener('click', onClickHandler);
        return button;
    }

// دالة مساعدة لإنشاء مودال عام
    function createGenericModal(title, contentElement) {
        const modalOverlay = document.createElement('div');
        modalOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50';
        modalOverlay.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.7); display: flex; justify-content: center;
        align-items: center; z-index: 1000; overflow: auto;
    `;

        const modalContentWrapper = document.createElement('div');
        modalContentWrapper.className = 'bg-white rounded-lg p-6 shadow-xl relative max-w-lg w-full text-center';

        const modalTitle = document.createElement('h3');
        modalTitle.className = 'text-xl font-semibold mb-4';
        modalTitle.textContent = title;

        const closeButton = createModalButton('X', () => modalOverlay.remove(), 'absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors');

        modalContentWrapper.append(closeButton, modalTitle, contentElement);
        modalOverlay.appendChild(modalContentWrapper);
        document.body.appendChild(modalOverlay);

        return modalOverlay;
    }


    function initPreviewManager() {
        const certificatePreviewBtn = document.getElementById('fabric-popup');
        const attendancePreviewBtn = document.getElementById('attendance-fabric-popup');
        const certificateExcelInput = document.getElementById('excel-input-model-2');
        const attendanceExcelInput = document.getElementById('badge-excel-input-2');

        // --------------------------------------------------------------------------------
        // معالج حدث لزر معاينة الشهادة (Fabric Popup)
        // --------------------------------------------------------------------------------
        if (certificatePreviewBtn) {
            certificatePreviewBtn.addEventListener('click', () => {
                console.log('Certificate preview button clicked!');
                handlePreviewButtonClick(false); // false لـ isAttendance
            });
        } else {
            console.warn('Certificate preview button (fabric-popup) not found');
        }

        // --------------------------------------------------------------------------------
        // معالج حدث لزر معاينة الحضور (Attendance Fabric Popup)
        // --------------------------------------------------------------------------------
        if (attendancePreviewBtn) {
            attendancePreviewBtn.addEventListener('click', () => {
                console.log('Attendance preview button clicked!');
                handlePreviewButtonClick(true); // true لـ isAttendance
            });
        } else {
            console.warn('Attendance preview button (attendance-fabric-popup) not found');
        }

        // --------------------------------------------------------------------------------
        // دالة عامة لمعالجة نقرة زر المعاينة
        // --------------------------------------------------------------------------------
        function handlePreviewButtonClick(isAttendance) {
            const availableCardsData = isAttendance ? attendanceCardData : certificateCardData;
            const cardTypeLabel = isAttendance ? 'الحضور' : 'الشهادة';

            const formCardIndices = Object.keys(availableCardsData).filter(key => Object.keys(availableCardsData[key]).length > 0);

            if (formCardIndices.length === 0) {
                alert(`لا توجد كروت ${cardTypeLabel} متاحة للمعاينة. الرجاء إضافة كارت ورفع صورة القالب أولاً.`);
                return;
            }

            const cardsList = document.createElement('div');
            cardsList.className = 'flex flex-col gap-3 mt-4';

            // إنشاء زر لكل كارت موجود
            formCardIndices.forEach(formCardIndex => {
                const cardButton = createModalButton(
                    `معاينة كارت ${cardTypeLabel} رقم ${parseInt(formCardIndex) + 1}`, // افتراض أن الفهرس يبدأ من 0، إذا كان formCardCounter يبدأ من 1 فاجعلها formCardIndex فقط
                    () => {
                        // بعد اختيار الكارت، نحتاج لتحديد الجانب (وجه واحد/وجهين)
                        const cardSides = availableCardsData[formCardIndex];
                        const hasFront = !!cardSides.front?.imageUrl;
                        const hasBack = !!cardSides.back?.imageUrl;

                        document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50.flex.justify-center.items-center.z-50')?.remove(); // إغلاق المودال الحالي

                        if (hasFront && hasBack) {
                            // إظهار مودال اختيار الجانب
                            showSideSelectionModal(isAttendance, formCardIndex);
                        } else if (hasFront) {
                            // كارت ذو وجه واحد، عرض الوجه الأمامي مباشرة
                            console.log(`Showing preview for card ${formCardIndex}, side front, type ${cardTypeLabel}`);
                            readFirstDataRow(isAttendance, (excelInfo) => {
                                const mappedExcelData = mapExcelData(excelInfo);
                                showPreview('front', isAttendance, formCardIndex, mappedExcelData);
                            });
                        } else {
                            alert('لا يوجد قالب صور تم رفعه لهذا الكارت.');
                        }
                    },
                    'w-full py-3 bg-green-500 hover:bg-green-600'
                );
                cardsList.appendChild(cardButton);
            });

            createGenericModal(`اختر كارت ${cardTypeLabel} للمعاينة`, cardsList);
        }

        // --------------------------------------------------------------------------------
        // دالة لإظهار مودال اختيار الجانب
        // --------------------------------------------------------------------------------
        function showSideSelectionModal(isAttendance, formCardIndex) {
            const cardTypeLabel = isAttendance ? 'الحضور' : 'الشهادة';

            const sideSelectionContainer = document.createElement('div');
            sideSelectionContainer.className = 'flex flex-col gap-3 mt-4';

            const frontSideButton = createModalButton(
                'معاينة الوجه الأمامي',
                () => {
                    document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50.flex.justify-center.items-center.z-50')?.remove(); // إغلاق المودال
                    console.log(`Showing preview for card ${formCardIndex}, side front, type ${cardTypeLabel}`);
                    readFirstDataRow(isAttendance, (excelInfo) => {
                        const mappedExcelData = mapExcelData(excelInfo);
                        showPreview('front', isAttendance, formCardIndex, mappedExcelData);
                    });
                },
                'w-full py-3 bg-green-500 hover:bg-green-600'
            );
            sideSelectionContainer.appendChild(frontSideButton);

            const backSideButton = createModalButton(
                'معاينة الوجه الخلفي',
                () => {
                    document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50.flex.justify-center.items-center.z-50')?.remove(); // إغلاق المودال
                    console.log(`Showing preview for card ${formCardIndex}, side back, type ${cardTypeLabel}`);
                    readFirstDataRow(isAttendance, (excelInfo) => {
                        const mappedExcelData = mapExcelData(excelInfo);
                        showPreview('back', isAttendance, formCardIndex, mappedExcelData);
                    });
                },
                'w-full py-3 bg-green-500 hover:bg-green-600'
            );
            sideSelectionContainer.appendChild(backSideButton);

            createGenericModal(`اختر وجه الكارت رقم ${parseInt(formCardIndex) + 1} لـ ${cardTypeLabel}`, sideSelectionContainer);
        }


        // دالة مساعدة لتحويل بيانات Excel إلى كائن
        function mapExcelData(excelInfo) {
            if (!excelInfo || !excelInfo.headers || !excelInfo.data) return null;
            const mappedData = {};
            excelInfo.headers.forEach((header, index) => {
                if (excelInfo.data[index] !== undefined) {
                    mappedData[header] = String(excelInfo.data[index]);
                }
            });
            return mappedData;
        }


        // --------------------------------------------------------------------------------
        // ملاحظة: معالجات حدث Excel Input هنا تحتاج إلى مراجعة
        // لأنها تستخدم IDs ثابتة، بينما قد يكون هناك كروت ديناميكية متعددة.
        // إذا كنت تريد أن يؤثر ملف Excel واحد على جميع الكروت من نفس النوع، فهذا جيد.
        // ولكن إذا كنت تريد أن يكون لكل كارت ملف Excel خاص به، فهذا يتطلب إعادة هيكلة.
        // بافتراض أن Excel واحد هو للكل في الوقت الحالي.
        // --------------------------------------------------------------------------------

        if (certificateExcelInput) {
            certificateExcelInput.addEventListener('change', () => {
                console.log('Certificate Excel input changed');
                readFirstDataRow(false, (excelInfo) => {
                    if (excelInfo && excelInfo.headers) {
                        certificateExcelData = excelInfo;
                        // هنا لا داعي لاستدعاء displayHeadersOnSpecificCanvas مباشرة هنا
                        // لأنها يتم استدعاؤها بالفعل عند تهيئة الكانفاس أو عند تغيير صورة القالب
                        // ولكن يمكننا تحديث جميع الكانفاسات الموجودة إذا لزم الأمر.
                    } else {
                        console.warn('No headers found in certificate Excel file');
                        certificateExcelData = { headers: [], data: [] };
                    }
                });
            });
        }

        if (attendanceExcelInput) {
            attendanceExcelInput.addEventListener('change', () => {
                console.log('Attendance Excel input changed');
                readFirstDataRow(true, (excelInfo) => {
                    if (excelInfo && excelInfo.headers) {
                        attendanceExcelData = excelInfo;
                    } else {
                        console.warn('No headers found in attendance Excel file');
                        attendanceExcelData = { headers: [], data: [] };
                    }
                });
            });
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
