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





















// ------------------------------------------------



const cardData = {};

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
        const fileInputName = sideInput.previousElementSibling.name;
        const sideValue = sideInput.value;
        return `${fileInputName}-${sideValue}`;
    }

    function getCardIdFromSpecificCanvas(canvasInstance) {
        for (const id in cardData) {
            if (cardData[id].fabricCanvas === canvasInstance) {
                return id;
            }
        }
        return null;
    }

    function saveITextObjectsFromSpecificCanvas(specificCanvas, cardIdentifier) {
        if (!specificCanvas || !(specificCanvas instanceof fabric.Canvas)) return;
        const iTextObjects = specificCanvas.getObjects().filter(obj => obj.type === 'i-text' && obj.opacity !== 0);
        cardData[cardIdentifier].iTextObjects = iTextObjects.map(obj => ({
            text: obj.text,
            left: obj.left,
            top: obj.top,
            fontFamily: obj.fontFamily,
            fontSize: obj.fontSize,
            fill: obj.fill,
            scaleX: obj.scaleX,
            scaleY: obj.scaleY,
            angle: obj.angle,
        }));
    }

    function restoreITextObjectsOnSpecificCanvas(specificCanvas, cardIdentifier) {
        if (!specificCanvas || !(specificCanvas instanceof fabric.Canvas)) return;
        if (!cardData[cardIdentifier] || !cardData[cardIdentifier].iTextObjects || cardData[cardIdentifier].iTextObjects.length === 0) return;

        specificCanvas.remove(...specificCanvas.getObjects().filter(obj => obj.type === 'i-text'));
        cardData[cardIdentifier].iTextObjects.forEach(data => {
            const textObject = new fabric.IText(data.text, {
                left: data.left,
                top: data.top,
                fontFamily: data.fontFamily,
                fontSize: data.fontSize,
                fill: data.fill,
                selectable: true,
                hasControls: true,
                textBaseline: 'alphabetic',
                scaleX: data.scaleX,
                scaleY: data.scaleY,
                angle: data.angle,
            });
            specificCanvas.add(textObject);
        });
        specificCanvas.renderAll();
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

    function initializeTemplateCanvas(canvasElement, imageUrl, cardIdentifier) {
        if (!canvasElement) {
            console.error('Canvas element not provided or found.');
            return;
        }

        if (cardData[cardIdentifier] && cardData[cardIdentifier].fabricCanvas) {
            cardData[cardIdentifier].fabricCanvas.dispose();
            cardData[cardIdentifier].fabricCanvas = null;
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
        cardData[cardIdentifier].fabricCanvas = currentCanvas;

        currentCanvas.on('mouse:down', function() {
            activeCanvas = currentCanvas;
            console.log(`Active canvas set to: ${cardIdentifier}`);
        });

        const imageUrlToLoad = cardData[cardIdentifier].imageUrl || imageUrl;

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

            restoreITextObjectsOnSpecificCanvas(currentCanvas, cardIdentifier);
            currentCanvas.renderAll();
        }, { crossOrigin: 'Anonymous' });

        let isDragging = false;
        let dragStartX = 0;
        let dragStartY = 0;

        currentCanvas.on('mouse:down', function(opt) {
            const evt = opt.e;
            const target = opt.target;

            if (target && target.type === 'i-text') {
                isDragging = true;
                currentlyDraggedFabricObject = target;
                startDragCanvas = currentCanvas;
                dragStartX = evt.clientX;
                dragStartY = evt.clientY;

                target.set({ opacity: 0, selectable: false, evented: false });
                currentCanvas.renderAll();

                draggingProxyElement = document.createElement('div');
                draggingProxyElement.textContent = target.text;
                draggingProxyElement.style.position = 'fixed';
                draggingProxyElement.style.zIndex = '99999';
                draggingProxyElement.style.pointerEvents = 'none';
                draggingProxyElement.style.backgroundColor = 'rgba(0, 0, 255, 0.6)';
                draggingProxyElement.style.color = 'white';
                draggingProxyElement.style.padding = '5px 10px';
                draggingProxyElement.style.borderRadius = '5px';
                draggingProxyElement.style.fontFamily = target.fontFamily;
                draggingProxyElement.style.fontSize = `${target.fontSize * target.scaleX}px`;
                document.body.appendChild(draggingProxyElement);

                if (deleteBtn) deleteBtn.disabled = true;
            } else if (!target || evt.ctrlKey || evt.metaKey) {
                isPanning = true;
                currentCanvas.discardActiveObject().renderAll();
                currentCanvas.selection = false;
                lastPosX = evt.clientX;
                lastPosY = evt.clientY;
            }
        });

        document.addEventListener('mousemove', (evt) => {
            if (isPanning) {
                const deltaX = evt.clientX - lastPosX;
                const deltaY = evt.clientY - lastPosY;
                const viewportTransform = currentCanvas.viewportTransform;
                viewportTransform[4] += deltaX;
                viewportTransform[5] += deltaY;
                currentCanvas.requestRenderAll();
                lastPosX = evt.clientX;
                lastPosY = evt.clientY;
                return;
            }

            if (isDragging && currentlyDraggedFabricObject && draggingProxyElement) {
                draggingProxyElement.style.left = `${evt.clientX}px`;
                draggingProxyElement.style.top = `${evt.clientY}px`;
            }
        });

        document.addEventListener('mouseup', (evt) => {
            if (isDragging && currentlyDraggedFabricObject) {
                let targetCanvas = null;
                let targetCardId = null;

                for (const id in cardData) {
                    const canvasInstance = cardData[id].fabricCanvas;
                    if (canvasInstance && canvasInstance.getElement() && canvasInstance.getElement().offsetWidth > 0 && canvasInstance.getElement().offsetHeight > 0) {
                        if (isMouseInsideCanvas(canvasInstance, evt)) {
                            targetCanvas = canvasInstance;
                            targetCardId = id;
                            break;
                        }
                    }
                }

                const pointer = targetCanvas ? targetCanvas.getPointer(evt, true) : null;
                if (targetCanvas && targetCanvas !== startDragCanvas && pointer) {
                    startDragCanvas.remove(currentlyDraggedFabricObject);
                    saveITextObjectsFromSpecificCanvas(startDragCanvas, getCardIdFromSpecificCanvas(startDragCanvas));

                    const newObject = new fabric.IText(currentlyDraggedFabricObject.text, {
                        left: pointer.x,
                        top: pointer.y,
                        fontFamily: currentlyDraggedFabricObject.fontFamily,
                        fontSize: currentlyDraggedFabricObject.fontSize,
                        fill: currentlyDraggedFabricObject.fill,
                        selectable: true,
                        hasControls: true,
                        textBaseline: 'alphabetic',
                        scaleX: currentlyDraggedFabricObject.scaleX,
                        scaleY: currentlyDraggedFabricObject.scaleY,
                        angle: currentlyDraggedFabricObject.angle
                    });

                    targetCanvas.add(newObject);
                    newObject.bringToFront();
                    targetCanvas.setActiveObject(newObject);
                    targetCanvas.renderAll();
                    saveITextObjectsFromSpecificCanvas(targetCanvas, targetCardId);
                    console.log(`Dropped object '${newObject.text}' on ${targetCardId} at (${pointer.x}, ${pointer.y})`);
                } else {
                    currentlyDraggedFabricObject.set({ opacity: 1, selectable: true, evented: true });
                    startDragCanvas.renderAll();
                    startDragCanvas.setActiveObject(currentlyDraggedFabricObject);
                    if (deleteBtn) deleteBtn.disabled = false;
                    console.log('Object returned to startCanvas.');
                }

                if (draggingProxyElement) {
                    draggingProxyElement.remove();
                    draggingProxyElement = null;
                }
                currentlyDraggedFabricObject = null;
                isDragging = false;
                startDragCanvas = null;
                if (deleteBtn) deleteBtn.disabled = false;
            } else if (isPanning) {
                isPanning = false;
                if (activeCanvas) activeCanvas.selection = true;
            }
        });

        currentCanvas.on('selection:cleared', () => {
            const editorPanel = document.getElementById('text-editor-panel');
            const textContentInput = document.getElementById('text-content');
            if (editorPanel) editorPanel.classList.add('hidden');
            if (textContentInput) textContentInput.value = '';
            if (fontColorInput) fontColorInput.value = '#000000';
            if (fontSizeInput) fontSizeInput.value = 20;
            if (fontFamilySelect) fontFamilySelect.value = 'Arial';
            if (deleteBtn) deleteBtn.disabled = true;
        });

        currentCanvas.on('selection:created', (e) => {
            const selectedObject = e.selected[0];
            if (selectedObject && selectedObject.type === 'i-text') {
                updateEditorControls(selectedObject);
                if (deleteBtn) deleteBtn.disabled = false;
            } else {
                if (deleteBtn) deleteBtn.disabled = true;
            }
        });

        currentCanvas.on('selection:updated', (e) => {
            const selectedObject = e.selected[0];
            if (selectedObject && selectedObject.type === 'i-text') {
                updateEditorControls(selectedObject);
                if (deleteBtn) deleteBtn.disabled = false;
            } else {
                if (deleteBtn) deleteBtn.disabled = true;
            }
        });

        currentCanvas.on('object:modified', (e) => {
            const modifiedObject = e.target;
            if (modifiedObject && modifiedObject.type === 'i-text') {
                saveITextObjectsFromSpecificCanvas(currentCanvas, cardIdentifier);
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

    function displayHeadersOnSpecificCanvas(specificCanvas, headers) {
        if (!specificCanvas || !(specificCanvas instanceof fabric.Canvas)) return;

        const startX = specificCanvas.width * 0.05;
        let currentY = specificCanvas.height * 0.05;
        const defaultFontSize = Math.max(16, specificCanvas.height * 0.02);

        const existingHeadersOnCanvas = specificCanvas.getObjects().filter(obj => obj.type === 'i-text').map(obj => obj.text);
        headers.forEach((headerText) => {
            const formattedHeaderText = `{{${headerText}}}`;
            if (!existingHeadersOnCanvas.includes(formattedHeaderText)) {
                // كشف اللغة تلقائيًا
                const isArabic = /[\u0600-\u06FF]/.test(headerText);
                const textAlign = 'center'; // افتراضي، يمكن يعدله اليوزر
                const direction = isArabic ? 'rtl' : 'ltr';

                const textObject = new fabric.IText(formattedHeaderText, {
                    left: startX,
                    top: currentY,
                    fontFamily: 'Arial',
                    fontSize: defaultFontSize,
                    fill: '#000000',
                    selectable: true,
                    hasControls: true,
                    textBaseline: 'alphabetic',
                    textAlign: textAlign,
                    direction: direction,
                    width: 200, // عرض أولي، يمكن يتغير
                    // تكيف الحجم تلقائيًا
                    scaleX: 1,
                    scaleY: 1,
                });

                // تعديل العرض بناءً على طول النص
                textObject.set({
                    width: textObject.getBoundingRect().width + 20 // إضافة هامش صغير
                });

                specificCanvas.add(textObject);
                currentY += defaultFontSize + 10;
            }
        });
        specificCanvas.renderAll();
        const formBlock = document.querySelector('.form-block');
        const currentActiveSide = (formBlock.querySelector('input.js-face[data-face="back"]') && formBlock.querySelector('input.js-face[data-face="back"]').checked) ? 'back' : 'front';
        const activeSideInput = formBlock.querySelector(`.side-input[value="${currentActiveSide}"]`);
        if (activeSideInput) {
            const cardIdOfActiveCard = getCardIdFromSideInput(activeSideInput);
            saveITextObjectsFromSpecificCanvas(specificCanvas, cardIdOfActiveCard);
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

    function setupFileCard(fileCardElement) {
        const fileInput = fileCardElement.querySelector('.file-input');
        const fabricCanvasContainer = fileCardElement.querySelector('.fabric-canvas-container');
        const initialUploadState = fileCardElement.querySelector('.initial-upload-state');
        const removePreviewBtn = fileCardElement.querySelector('.remove-preview-btn');
        const sideInput = fileCardElement.querySelector('.side-input');

        const cardIdentifier = getCardIdFromSideInput(sideInput);

        if (!cardData[cardIdentifier]) {
            cardData[cardIdentifier] = {
                fabricCanvas: null,
                iTextObjects: [],
                imageUrl: null
            };
        }

        let currentTemplateCanvasElement = null;

        if (!fileInput || !fabricCanvasContainer || !initialUploadState || !removePreviewBtn || !sideInput) {
            console.warn('One or more elements missing in fileCardElement for setupFileCard:', fileCardElement);
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

        updateCardDisplayState(fileInput.value || cardData[cardIdentifier].imageUrl);

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    updateCardDisplayState(true);
                    // تأكدي أن cardIdentifier بيجيب الـ ID الصحيح زي 'attendance_template_data_file_path-front'
                    console.log("Card Identifier being used:", cardIdentifier);
                    cardData[cardIdentifier].imageUrl = event.target.result;

                    // لو الـ input ده بتاع الشهادات
                    if (fileInput.name === 'document_template_file_path[]') {
                        // كود الشهادات الحالي
                        if (cardData[cardIdentifier].fabricCanvas) {
                            saveITextObjectsFromSpecificCanvas(cardData[cardIdentifier].fabricCanvas, cardIdentifier);
                            cardData[cardIdentifier].fabricCanvas.dispose();
                            cardData[cardIdentifier].fabricCanvas = null;
                        }

                        fabricCanvasContainer.querySelectorAll('canvas').forEach(canvas => canvas.remove());
                        if (!fabricCanvasContainer.contains(removePreviewBtn)) fabricCanvasContainer.appendChild(removePreviewBtn);

                        currentTemplateCanvasElement = document.createElement('canvas');
                        currentTemplateCanvasElement.setAttribute('data-card-id', cardIdentifier);
                        currentTemplateCanvasElement.setAttribute('id', `canvas-${cardIdentifier}`);
                        currentTemplateCanvasElement.style.width = '100%';
                        currentTemplateCanvasElement.style.height = '100%';
                        currentTemplateCanvasElement.style.display = 'block';
                        fabricCanvasContainer.prepend(currentTemplateCanvasElement);

                        requestAnimationFrame(() => {
                            setTimeout(() => {
                                if (currentTemplateCanvasElement && currentTemplateCanvasElement.offsetWidth > 0 && currentTemplateCanvasElement.offsetHeight > 0) {
                                    console.log(`Initializing canvas for ${cardIdentifier}`);
                                    initializeTemplateCanvas(currentTemplateCanvasElement, event.target.result, cardIdentifier);
                                    setTimeout(() => {
                                        if (cardData[cardIdentifier].fabricCanvas) {
                                            const isBackCard = cardIdentifier.includes('-back');
                                            let shouldApplyHeaders = !isBackCard && extractedHeaders && extractedHeaders.length > 0;
                                            if (isBackCard) {
                                                shouldApplyHeaders = false;
                                                cardData[cardIdentifier].iTextObjects = [];
                                            }

                                            if (cardData[cardIdentifier].iTextObjects && cardData[cardIdentifier].iTextObjects.length > 0) {
                                                console.log(`Restoring iTextObjects for ${cardIdentifier}:`, cardData[cardIdentifier].iTextObjects);
                                                restoreITextObjectsOnSpecificCanvas(cardData[cardIdentifier].fabricCanvas, cardIdentifier);
                                                cardData[cardIdentifier].fabricCanvas.renderAll();
                                            } else if (shouldApplyHeaders) {
                                                console.log(`Re-adding headers for ${cardIdentifier}:`, extractedHeaders);
                                                displayHeadersOnSpecificCanvas(cardData[cardIdentifier].fabricCanvas, extractedHeaders);
                                            } else {
                                                console.warn(`No iTextObjects or headers to restore for ${cardIdentifier}. Starting clean.`);
                                            }
                                        } else {
                                            console.error(`Canvas for ${cardIdentifier} is not initialized.`);
                                        }
                                    }, 200);
                                } else {
                                    setTimeout(() => {
                                        console.log(`Retrying canvas initialization for ${cardIdentifier}`);
                                        initializeTemplateCanvas(currentTemplateCanvasElement, event.target.result, cardIdentifier);
                                        setTimeout(() => {
                                            if (cardData[cardIdentifier].fabricCanvas) {
                                                const isBackCard = cardIdentifier.includes('-back');
                                                let shouldApplyHeaders = !isBackCard && extractedHeaders && extractedHeaders.length > 0;
                                                if (isBackCard) {
                                                    shouldApplyHeaders = false;
                                                    cardData[cardIdentifier].iTextObjects = [];
                                                }

                                                if (cardData[cardIdentifier].iTextObjects && cardData[cardIdentifier].iTextObjects.length > 0) {
                                                    console.log(`Restoring iTextObjects for ${cardIdentifier}:`, cardData[cardIdentifier].iTextObjects);
                                                    restoreITextObjectsOnSpecificCanvas(cardData[cardIdentifier].fabricCanvas, cardIdentifier);
                                                    cardData[cardIdentifier].fabricCanvas.renderAll();
                                                } else if (shouldApplyHeaders) {
                                                    console.log(`Re-adding headers for ${cardIdentifier}:`, extractedHeaders);
                                                    displayHeadersOnSpecificCanvas(cardData[cardIdentifier].fabricCanvas, extractedHeaders);
                                                } else {
                                                    console.warn(`No iTextObjects or headers to restore for ${cardIdentifier} on retry. Starting clean.`);
                                                }
                                            } else {
                                                console.error(`Canvas for ${cardIdentifier} is not initialized on retry.`);
                                            }
                                        }, 200);
                                    }, 500);
                                }
                            }, 50);
                        });
                    }
                        // هذا هو الجزء الذي يتعامل مع ملفات الحضور (بما أنها ليست شهادات)
                    // يجب أن نضمن هنا تهيئة Fabric Canvas أيضًا
                    else {
                        // هنا لازم يتم نفس منطق تهيئة الـ canvas اللي بيحصل للشهادات
                        // لكن مع الحفاظ على عرض PDF كـ iframe لو كان الملف PDF

                        // 1. أزيلي أي محتوى قديم (img/iframe)
                        fabricCanvasContainer.innerHTML = '';
                        if (!fabricCanvasContainer.contains(removePreviewBtn)) fabricCanvasContainer.appendChild(removePreviewBtn);

                        // 2. أنشئي canvas جديد للحضور
                        currentTemplateCanvasElement = document.createElement('canvas');
                        currentTemplateCanvasElement.setAttribute('data-card-id', cardIdentifier);
                        currentTemplateCanvasElement.setAttribute('id', `canvas-${cardIdentifier}`); // id فريد
                        currentTemplateCanvasElement.style.width = '100%';
                        currentTemplateCanvasElement.style.height = '100%';
                        currentTemplateCanvasElement.style.display = 'block';
                        fabricCanvasContainer.prepend(currentTemplateCanvasElement); // أضيفيه للـ container

                        // 3. هيئي الـ Fabric Canvas باستخدام الصورة المرفوعة
                        requestAnimationFrame(() => {
                            setTimeout(() => {
                                if (currentTemplateCanvasElement && currentTemplateCanvasElement.offsetWidth > 0 && currentTemplateCanvasElement.offsetHeight > 0) {
                                    console.log(`Initializing attendance canvas for ${cardIdentifier}`);
                                    initializeTemplateCanvas(currentTemplateCanvasElement, event.target.result, cardIdentifier);

                                    // لو الملف PDF، أضيفي iframe فوق الـ canvas
                                    if (file.type === 'application/pdf') {
                                        const iframe = document.createElement('iframe');
                                        iframe.src = event.target.result;
                                        iframe.className = 'w-full h-full absolute inset-0 pointer-events-none'; // مهم عشان تقدري تتفاعلي مع الـ canvas
                                        iframe.style.border = 'none';
                                        fabricCanvasContainer.appendChild(iframe);
                                    }

                                    // بعد تهيئة الـ canvas، لو فيه headers من Excel قبل كده، اعرضيها
                                    setTimeout(() => {
                                        if (cardData[cardIdentifier].fabricCanvas && extractedHeaders && extractedHeaders.length > 0) {
                                            console.log(`Applying extracted headers to attendance canvas for ${cardIdentifier}`);
                                            displayHeadersOnSpecificCanvas(cardData[cardIdentifier].fabricCanvas, extractedHeaders);
                                        }
                                    }, 200);

                                } else {
                                    // محاولة إعادة التهيئة لو الأبعاد مش صحيحة
                                    setTimeout(() => {
                                        console.log(`Retrying attendance canvas initialization for ${cardIdentifier}`);
                                        initializeTemplateCanvas(currentTemplateCanvasElement, event.target.result, cardIdentifier);
                                        if (file.type === 'application/pdf') {
                                            const iframe = document.createElement('iframe');
                                            iframe.src = event.target.result;
                                            iframe.className = 'w-full h-full absolute inset-0 pointer-events-none';
                                            iframe.style.border = 'none';
                                            fabricCanvasContainer.appendChild(iframe);
                                        }
                                        setTimeout(() => {
                                            if (cardData[cardIdentifier].fabricCanvas && extractedHeaders && extractedHeaders.length > 0) {
                                                console.log(`Applying extracted headers to attendance canvas for ${cardIdentifier} on retry`);
                                                displayHeadersOnSpecificCanvas(cardData[cardIdentifier].fabricCanvas, extractedHeaders);
                                            }
                                        }, 200);
                                    }, 500);
                                }
                            }, 50);
                        });
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        removePreviewBtn.addEventListener('click', () => {
            fileInput.value = '';
            updateCardDisplayState(false);

            if (cardData[cardIdentifier] && cardData[cardIdentifier].iTextObjects && cardData[cardIdentifier].iTextObjects.length > 0) {
                console.log(`Found iTextObjects on ${cardIdentifier}. Attempting to move them back to front card.`);
                const frontCardId = `document_template_file_path[]-front`;
                const frontCanvas = cardData[frontCardId]?.fabricCanvas;

                if (frontCanvas) {
                    console.log('Front canvas found. Transferring objects.');
                    cardData[cardIdentifier].iTextObjects.forEach(objData => {
                        const newObject = new fabric.IText(objData.text, {
                            left: objData.left,
                            top: objData.top,
                            fontFamily: objData.fontFamily,
                            fontSize: objData.fontSize,
                            fill: objData.fill,
                            selectable: true,
                            hasControls: true,
                            textBaseline: 'alphabetic',
                            scaleX: objData.scaleX,
                            scaleY: objData.scaleY,
                            angle: objData.angle,
                        });
                        frontCanvas.add(newObject);
                        newObject.bringToFront();
                    });
                    saveITextObjectsFromSpecificCanvas(frontCanvas, frontCardId);
                    frontCanvas.renderAll();
                    console.log('Objects moved to front canvas and saved.');
                } else {
                    console.warn('Front canvas not found. Objects will be removed with the card.');
                }
            }

            fabricCanvasContainer.innerHTML = '';
            if (fileInput.name === 'document_template_file_path[]') {
                if (cardData[cardIdentifier] && cardData[cardIdentifier].fabricCanvas) {
                    cardData[cardIdentifier].fabricCanvas.dispose();
                    cardData[cardIdentifier].fabricCanvas = null;
                }
                if (currentTemplateCanvasElement && currentTemplateCanvasElement.parentNode) {
                    currentTemplateCanvasElement.parentNode.removeChild(currentTemplateCanvasElement);
                    currentTemplateCanvasElement = null;
                }
            } else {
                fabricCanvasContainer.innerHTML = '';
            }
            if (!fabricCanvasContainer.contains(removePreviewBtn)) fabricCanvasContainer.appendChild(removePreviewBtn);

            if (cardData[cardIdentifier]) {
                cardData[cardIdentifier].iTextObjects = [];
                cardData[cardIdentifier].imageUrl = null;
            }
        });
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
          <input name="attendance_images_pdf_files[]" type="file" class="sr-only file-input" accept="application/pdf,image/*">
          <input type="hidden" name="attendance_sides[]" class="side-input" value="${isBack ? 'back' : 'front'}">
        </label>
      </div>
      <div class="fabric-canvas-container hidden w-full h-48 flex justify-center items-center absolute inset-0 relative">
        <button type="button" class="remove-preview-btn absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-lg hover:bg-red-600 transition z-10" title="إزالة الملف">
          ×
        </button>
      </div>
    `;
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
        allCardElements.forEach(cardElement => {
            const sideInput = cardElement.querySelector('.side-input');
            if (sideInput) {
                const cardIdentifier = getCardIdFromSideInput(sideInput);
                if (cardData[cardIdentifier] && cardData[cardIdentifier].fabricCanvas) {
                    saveITextObjectsFromSpecificCanvas(cardData[cardIdentifier].fabricCanvas, cardIdentifier);
                    cardData[cardIdentifier].fabricCanvas.dispose();
                    cardData[cardIdentifier].fabricCanvas = null;
                }
                if (sideInput.value === 'back' && frontRadio.checked) {
                    if (cardData[cardIdentifier] && cardData[cardIdentifier].iTextObjects && cardData[cardIdentifier].iTextObjects.length > 0) {
                        backITextObjects = [...cardData[cardIdentifier].iTextObjects];
                    }
                    console.log(`Clearing back card data for ${cardIdentifier}`);
                    delete cardData[cardIdentifier];
                }
            }
        });

        if (backITextObjects.length > 0) {
            const frontCardId = getCardIdFromSideInput(frontRadio.closest('.filebox-card').querySelector('.side-input'));
            if (cardData[frontCardId]) {
                cardData[frontCardId].iTextObjects = [...cardData[frontCardId].iTextObjects, ...backITextObjects];
            }
        }

        hub.innerHTML = '';

        let frontCardElement = null;
        let backCardElement = null;

        const f = document.importNode(fileTpl, true);
        f.querySelector('.card-title').textContent = 'تحميل مستندات – الوجه الأمامي';
        f.querySelector('.side-input').value = 'front';
        f.querySelector('.file-input').name = 'document_template_file_path[]';
        frontCardElement = f.querySelector('.filebox-card');
        hub.appendChild(f);
        setupFileCard(frontCardElement);

        if (backRadio && backRadio.checked) {
            const b = document.importNode(fileTpl, true);
            b.querySelector('.card-title').textContent = 'تحميل مستندات – الوجه الخلفي';
            b.querySelector('.side-input').value = 'back';
            b.querySelector('.file-input').name = 'document_template_file_path[]';
            backCardElement = b.querySelector('.filebox-card');
            hub.appendChild(b);
            setupFileCard(backCardElement);
            const backCardId = getCardIdFromSideInput(backCardElement.querySelector('.side-input'));
            if (!cardData[backCardId]) {
                cardData[backCardId] = { fabricCanvas: null, iTextObjects: [], imageUrl: null };
            }
        }

        requestAnimationFrame(() => {
            setTimeout(() => {
                if (frontCardElement) {
                    const frontCardId = getCardIdFromSideInput(frontCardElement.querySelector('.side-input'));
                    const frontCanvasContainer = frontCardElement.querySelector('.fabric-canvas-container');
                    if (cardData[frontCardId] && cardData[frontCardId].imageUrl && !cardData[frontCardId].fabricCanvas) {
                        const canvasEl = document.createElement('canvas');
                        canvasEl.setAttribute('data-card-id', frontCardId);
                        canvasEl.setAttribute('id', `canvas-${frontCardId}`);
                        canvasEl.style.width = '100%';
                        canvasEl.style.height = '100%';
                        canvasEl.style.display = 'block';
                        frontCanvasContainer.prepend(canvasEl);
                        frontCanvasContainer.classList.remove('hidden');
                        frontCardElement.querySelector('.initial-upload-state').classList.add('hidden');
                        frontCardElement.querySelector('.remove-preview-btn').style.display = 'flex';
                        initializeTemplateCanvas(canvasEl, cardData[frontCardId].imageUrl, frontCardId);
                        setTimeout(() => {
                            if (cardData[frontCardId].fabricCanvas) {
                                if (cardData[frontCardId].iTextObjects && cardData[frontCardId].iTextObjects.length > 0) {
                                    restoreITextObjectsOnSpecificCanvas(cardData[frontCardId].fabricCanvas, frontCardId);
                                    cardData[frontCardId].fabricCanvas.renderAll();
                                } else if (extractedHeaders && extractedHeaders.length > 0) {
                                    displayHeadersOnSpecificCanvas(cardData[frontCardId].fabricCanvas, extractedHeaders);
                                }
                            }
                        }, 200);
                    }
                }

                if (backCardElement && backRadio && backRadio.checked) {
                    const backCardId = getCardIdFromSideInput(backCardElement.querySelector('.side-input'));
                    const backCanvasContainer = backCardElement.querySelector('.fabric-canvas-container');
                    if (cardData[backCardId] && cardData[backCardId].imageUrl && !cardData[backCardId].fabricCanvas) {
                        const canvasEl = document.createElement('canvas');
                        canvasEl.setAttribute('data-card-id', backCardId);
                        canvasEl.setAttribute('id', `canvas-${backCardId}`);
                        canvasEl.style.width = '100%';
                        canvasEl.style.height = '100%';
                        canvasEl.style.display = 'block';
                        backCanvasContainer.prepend(canvasEl);
                        backCanvasContainer.classList.remove('hidden');
                        backCardElement.querySelector('.initial-upload-state').classList.add('hidden');
                        backCardElement.querySelector('.remove-preview-btn').style.display = 'flex';
                        initializeTemplateCanvas(canvasEl, cardData[backCardId].imageUrl, backCardId);
                        setTimeout(() => {
                            if (cardData[backCardId].fabricCanvas) {
                                if (cardData[backCardId].iTextObjects && cardData[backCardId].iTextObjects.length > 0) {
                                    restoreITextObjectsOnSpecificCanvas(cardData[backCardId].fabricCanvas, backCardId);
                                    cardData[backCardId].fabricCanvas.renderAll();
                                } else if (extractedHeaders && extractedHeaders.length > 0) {
                                    displayHeadersOnSpecificCanvas(cardData[backCardId].fabricCanvas, extractedHeaders);
                                }
                            }
                        }, 200);
                    } else {
                        backCanvasContainer.classList.add('hidden');
                        backCardElement.querySelector('.initial-upload-state').classList.remove('hidden');
                        backCardElement.querySelector('.remove-preview-btn').style.display = 'none';
                    }
                }
            }, 50);
        });
    }

    function renderAttendanceCards(block, initial = false) {
        const containers = block.querySelectorAll('.attachments-container');
        if (containers.length === 0) return;
        const container = containers[0];

        const one = block.querySelector('input[name="side"][value="1"]');
        const two = block.querySelector('input[name="side"][value="2"]');
        let count = initial ? 1 : (two && two.checked ? 2 : 1);

        container.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const newCardElement = createAttachmentCard(i === 1);
            container.appendChild(newCardElement);
            setupFileCard(newCardElement);
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
        const oldCanvas = document.getElementById('preview-canvas');
        const oldPreviewModal = document.querySelector('.preview-modal');
        const oldChoiceModal = document.querySelector('.choice-modal');

        if (oldPreviewModal) oldPreviewModal.remove();
        if (oldChoiceModal) oldChoiceModal.remove();

        if (oldCanvas) {
            if (typeof fabric !== 'undefined' && fabric.Canvas && fabric.Canvas.get(oldCanvas.id)) {
                const existingFabricInstance = fabric.Canvas.get(oldCanvas.id);
                if (existingFabricInstance) {
                    existingFabricInstance.dispose();
                    console.log('Fabric canvas instance disposed successfully for:', oldCanvas.id);
                }
            }
            oldCanvas.remove();
            console.log('Preview canvas element removed from DOM.');
        }

        if (typeof fabric !== 'undefined' && fabric.Canvas && fabric.Canvas._instances) {
            const instancesCopy = [...fabric.Canvas._instances];
            instancesCopy.forEach(instance => {
                if (instance && !instance.isDisposed) instance.dispose();
            });
        } else {
            console.warn('fabric.js is not available or _instances is undefined, skipping full canvas cleanup.');
        }
    }

    function showPreview(side, isAttendance = false) {
        cleanDOM();
        console.log(`Showing preview for side: ${side}, isAttendance: ${isAttendance}`);
        const cardId = isAttendance ? `attendance_template_data_file_path-${side}` : `document_template_file_path[]-${side}`;
        const currentCanvas = cardData[cardId]?.fabricCanvas;
        const imageUrl = cardData[cardId]?.imageUrl;

        if (!currentCanvas || !imageUrl) {
            console.error(`Error: Canvas or Image URL not found for ${cardId}. currentCanvas: ${!!currentCanvas}, imageUrl: ${imageUrl}`);
            alert(`خطأ: لم يتم العثور على قالب أو صورة للوجه ${side === 'front' ? 'الأمامي' : 'الخلفي'}.`);
            return;
        }

        readFirstDataRow(excelInfo => {
            if (!excelInfo) {
                console.error('No Excel data available');
                alert('خطأ: الرجاء رفع ملف Excel أولاً.');
                return;
            }

            const previewCanvasElement = document.createElement('canvas');
            previewCanvasElement.id = 'preview-canvas';
            previewCanvasElement.width = currentCanvas.width;
            previewCanvasElement.height = currentCanvas.height;
            previewCanvasElement.style.width = `${currentCanvas.width}px`;
            previewCanvasElement.style.height = `${currentCanvas.height}px`;
            previewCanvasElement.style.display = 'block';

            const modal = document.createElement('div');
            modal.className = 'preview-modal';
            modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            overflow: auto;
        `;
            const modalContent = document.createElement('div');
            modalContent.style.cssText = `
            background: white;
            padding: 0;
            border-radius: 0;
            width: ${currentCanvas.width}px;
            max-width: 90vw;
            max-height: 90vh;
            overflow: auto;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        `;

            modalContent.appendChild(previewCanvasElement);
            modal.appendChild(modalContent);
            document.body.appendChild(modal);

            const previewCanvas = new fabric.Canvas('preview-canvas', {
                width: currentCanvas.width,
                height: currentCanvas.height,
                selection: false,
                hoverCursor: 'default'
            });

            console.log("Attempting to load image from URL:", imageUrl);
            fabric.Image.fromURL(imageUrl, function(img) {
                if (img) {
                    const scaleX = currentCanvas.width / img.width;
                    const scaleY = currentCanvas.height / img.height;
                    const scale = Math.min(scaleX, scaleY);
                    img.scale(scale);
                    img.set({
                        originX: 'center',
                        originY: 'center',
                        top: currentCanvas.height / 2,
                        left: currentCanvas.width / 2,
                        selectable: false
                    });
                    previewCanvas.add(img);

                    currentCanvas.getObjects().forEach(obj => {
                        console.log('Object found:', obj.text);
                        if (obj && obj.type === 'i-text') {
                            try {
                                obj.clone(cloned => {
                                    let updatedText = obj.text;
                                    if (excelInfo && excelInfo.data && updatedText.match(/\{\{.*?\}\}/)) {
                                        updatedText = updatedText.replace(/\{\{(.*?)\}\}/g, (match, placeholder) => {
                                            const columnIndex = excelInfo.headers.indexOf(placeholder.trim());
                                            return columnIndex !== -1 && excelInfo.data[columnIndex] ? excelInfo.data[columnIndex] : match;
                                        });
                                    }
                                    cloned.set({
                                        text: updatedText,
                                        left: obj.left,
                                        top: obj.top,
                                        scaleX: obj.scaleX,
                                        scaleY: obj.scaleY,
                                        fontFamily: obj.fontFamily,
                                        fontSize: obj.fontSize,
                                        fill: obj.fill,
                                        textAlign: obj.textAlign || 'center',
                                        direction: obj.direction || (/[\u0600-\u06FF]/.test(obj.text) ? 'rtl' : 'ltr'),
                                        selectable: false,
                                        hasControls: false,
                                        textBaseline: 'alphabetic'
                                    });
                                    previewCanvas.add(cloned);
                                });
                            } catch (err) {
                                console.error('Clone error while adding text object:', err);
                            }
                        }
                    });
                    previewCanvas.renderAll();
                } else {
                    console.error(`Failed to load background image from URL: ${imageUrl}. Please check the URL and CORS settings. Using fallback.`);
                    const fallbackImg = new fabric.Rect({ width: currentCanvas.width, height: currentCanvas.height, fill: '#f0f0f0', top: 0, left: 0 });
                    previewCanvas.add(fallbackImg);
                    previewCanvas.renderAll();
                }
            }, { crossOrigin: 'anonymous' });

            if (side === 'front') {
                const additionalInfoCard = document.createElement('div');
                additionalInfoCard.className = 'additional-info-card';
                additionalInfoCard.style.cssText = `
                width: ${currentCanvas.width}px;
                background: #f8f8f8;
                padding: 15px;
                margin-top: 20px;
                display: flex;
                justify-content: space-around;
                align-items: center;
                border: 1px solid #eee;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                flex-shrink: 0;
                box-sizing: border-box;
            `;

                const logoPlaceholder = document.createElement('div');
                logoPlaceholder.className = 'logo-placeholder';
                logoPlaceholder.style.cssText = `
                width: 100px;
                height: 100px;
                background: #e0e0e0;
                display: flex;
                justify-content: center;
                align-items: center;
                border: 2px dashed #999;
                border-radius: 5px;
                font-size: 14px;
                color: #555;
                text-align: center;
            `;
                logoPlaceholder.textContent = 'شعار الموقع هنا';

                const qrCodePlaceholder = document.createElement('div');
                qrCodePlaceholder.className = 'qr-code-placeholder';
                qrCodePlaceholder.style.cssText = `
                width: 100px;
                height: 100px;
                background: #e0e0e0;
                display: flex;
                justify-content: center;
                align-items: center;
                border: 2px dashed #999;
                border-radius: 5px;
                font-size: 14px;
                color: #555;
                text-align: center;
            `;
                qrCodePlaceholder.textContent = 'كود QR هنا';

                additionalInfoCard.appendChild(logoPlaceholder);
                additionalInfoCard.appendChild(qrCodePlaceholder);
                modalContent.appendChild(additionalInfoCard);
            }

            const closeBtnX = document.createElement('div');
            closeBtnX.textContent = '×';
            closeBtnX.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            background: #ff4444;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
            closeBtnX.addEventListener('click', () => {
                cleanDOM();
                console.log('Closing preview modal');
            });
            modalContent.appendChild(closeBtnX);

            const title = document.createElement('h3');
            title.textContent = `معاينة الوجه ${side === 'front' ? 'الأمامي' : 'الخلفي'} ${isAttendance ? 'للحضور' : 'للشهادة'}`;
            title.style.cssText = 'margin-bottom: 10px; font-size: 18px;';
            modalContent.insertBefore(title, previewCanvasElement);

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    cleanDOM();
                    console.log('Preview modal closed by clicking outside');
                }
            });
        });
    }

    function initPreviewManager(finalizeBtn, attendanceFinalizeBtn) {
        if (finalizeBtn) {
            finalizeBtn.addEventListener('click', () => {
                console.log('Certificate finalize button clicked!');
                const formBlock = document.querySelector('.form-block');
                if (!formBlock) {
                    console.error('Error: .form-block not found in DOM');
                    alert('خطأ: لا يمكن العثور على عنصر النموذج. تأكد من تحميل الصفحة بشكل صحيح.');
                    return;
                }

                const fileHub = formBlock.querySelector('.js-filehub');
                if (!fileHub) {
                    console.error('Error: .js-filehub not found in DOM');
                    alert('خطأ: لا يمكن العثور على قسم رفع الملفات. تأكد من تحميل الصفحة بشكل صحيح.');
                    return;
                }

                const frontSideInput = formBlock.querySelector('.side-input[value="front"]');
                if (!frontSideInput) {
                    console.error('Error: Front side input not found');
                    alert('خطأ: يجب رفع قالب للوجه الأمامي أولاً.');
                    return;
                }

                const hasBackSide = cardData['document_template_file_path[]-back']?.imageUrl || false;
                console.log('Has back side for certificate:', hasBackSide);

                if (hasBackSide) {
                    console.log('Two sides detected for certificate, showing choice modal...');
                    cleanDOM();
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
                z-index: 10000;
            `;
                    const choiceContent = document.createElement('div');
                    choiceContent.style.cssText = `
                background: white;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                min-width: 300px;
            `;
                    const choiceTitle = document.createElement('h3');
                    choiceTitle.textContent = 'اختر الوجه لمعاينة الشهادة';
                    choiceTitle.style.cssText = 'margin-bottom: 20px; font-size: 18px;';

                    const frontBtn = document.createElement('button');
                    frontBtn.textContent = 'الوجه الأمامي';
                    frontBtn.style.cssText = `
                padding: 10px 20px;
                margin: 0 10px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            `;
                    frontBtn.addEventListener('click', () => {
                        choiceModal.remove();
                        showPreview('front'); // هنا
                    });

                    const backBtn = document.createElement('button');
                    backBtn.textContent = 'الوجه الخلفي';
                    backBtn.style.cssText = `
                padding: 10px 20px;
                margin: 0 10px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            `;
                    backBtn.addEventListener('click', () => {
                        choiceModal.remove();
                        showPreview('back');
                    });

                    const cancelBtn = document.createElement('button');
                    cancelBtn.textContent = 'إلغاء';
                    cancelBtn.style.cssText = `
                padding: 10px 20px;
                margin: 0 10px;
                background: #ff4444;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            `;
                    cancelBtn.addEventListener('click', () => {
                        choiceModal.remove();
                        cleanDOM();
                    });

                    choiceContent.appendChild(choiceTitle);
                    choiceContent.appendChild(frontBtn);
                    choiceContent.appendChild(backBtn);
                    choiceContent.appendChild(cancelBtn);
                    choiceModal.appendChild(choiceContent);
                    document.body.appendChild(choiceModal);

                    choiceModal.addEventListener('click', (e) => {
                        if (e.target === choiceModal) {
                            choiceModal.remove();
                            cleanDOM();
                            console.log('Choice modal closed by clicking outside');
                        }
                    });
                } else {
                    console.log('Single side detected for certificate, showing front preview directly...');
                    showPreview('front');
                }
            });
        } else {
            console.error('Error: finalizeBtn not found in DOM');
            alert('خطأ: زر معاينة الشهادة غير موجود. تأكد من تحميل الصفحة بشكل صحيح.');
        }

        if (attendanceFinalizeBtn) {
            attendanceFinalizeBtn.addEventListener('click', () => {
                console.log('Attendance finalize button clicked!');

                // تم التعديل هنا: البحث عن '.form-block' بدلاً من '.form-card'
                const formBlockForAttendance = document.querySelector('.form-block');
                if (!formBlockForAttendance) {
                    console.error('Error: .form-block (for attendance) not found in DOM');
                    alert('خطأ: لا يمكن العثور على قسم الحضور. تأكد من تحميل الصفحة بشكل صحيح.');
                    return;
                }

                // تم التعديل هنا: البحث عن '.js-filehub.attendance-filehub' داخل الـ formBlockForAttendance
                const attendanceFileHub = formBlockForAttendance.querySelector('.js-filehub.attendance-filehub');
                if (!attendanceFileHub) {
                    console.error('Error: .js-filehub.attendance-filehub (attachments container) not found in DOM for Attendance.');
                    alert('خطأ: لا يمكن العثور على قسم المرفقات (ملفات الحضور). تأكد من تحميل الصفحة بشكل صحيح.');
                    return;
                }

                // الآن البحث عن الـ frontSideInput سيكون داخل الـ attendanceFileHub الجديد
                const frontSideInput = attendanceFileHub.querySelector('.side-input[value="front"]');
                if (!frontSideInput) {
                    console.error('Error: Front side input not found for attendance within attendance file hub');
                    alert('خطأ: يجب رفع قالب للوجه الأمامي للحضور أولاً.');
                    return;
                }

                const hasBackSide = cardData['attendance_template_data_file_path-back']?.imageUrl || false;
                console.log('Has back side for attendance:', hasBackSide);

                if (hasBackSide) {
                    console.log('Two sides detected for attendance, showing choice modal...');
                    cleanDOM();
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
                z-index: 10000;
            `;
                    const choiceContent = document.createElement('div');
                    choiceContent.style.cssText = `
                background: white;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                min-width: 300px;
            `;
                    const choiceTitle = document.createElement('h3');
                    choiceTitle.textContent = 'اختر الوجه لمعاينة الحضور';
                    choiceTitle.style.cssText = 'margin-bottom: 20px; font-size: 18px;';

                    const frontBtn = document.createElement('button');
                    frontBtn.textContent = 'الوجه الأمامي';
                    frontBtn.style.cssText = `
                padding: 10px 20px;
                margin: 0 10px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            `;

                    frontBtn.addEventListener('click', () => {
                        choiceModal.remove();
                        // هنا التعديل:
                        setTimeout(() => {
                            showPreview('front', true);
                        }, 500); // تأخير 500 مللي ثانية
                    });

                    const backBtn = document.createElement('button');
                    backBtn.textContent = 'الوجه الخلفي';
                    backBtn.style.cssText = `
                padding: 10px 20px;
                margin: 0 10px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            `;
                    backBtn.addEventListener('click', () => {
                        choiceModal.remove();
                        // هنا التعديل:
                        setTimeout(() => {
                            showPreview('back', true);
                        }, 500); // تأخير 500 مللي ثانية
                    });

                    const cancelBtn = document.createElement('button');
                    cancelBtn.textContent = 'إلغاء';
                    cancelBtn.style.cssText = `
                padding: 10px 20px;
                margin: 0 10px;
                background: #ff4444;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            `;
                    cancelBtn.addEventListener('click', () => {
                        choiceModal.remove();
                        cleanDOM();
                    });

                    choiceContent.appendChild(choiceTitle);
                    choiceContent.appendChild(frontBtn);
                    choiceContent.appendChild(backBtn);
                    choiceContent.appendChild(cancelBtn);
                    choiceModal.appendChild(choiceContent);
                    document.body.appendChild(choiceModal);

                    choiceModal.addEventListener('click', (e) => {
                        if (e.target === choiceModal) {
                            choiceModal.remove();
                            cleanDOM();
                            console.log('Choice modal closed by clicking outside');
                        }
                    });
                } else {
                    console.log('Single side detected for attendance, showing front preview directly...');
                    // هنا التعديل:
                    setTimeout(() => {
                        showPreview('front', true);
                    }, 500); // تأخير 500 مللي ثانية
                }
            });
        } else {
            console.error('Error: attendanceFinalizeBtn not found in DOM');
            alert('خطأ: زر معاينة الحضور غير موجود. تأكد من تحميل الصفحة بشكل صحيح.');
        }
    }

// استبدل الكود القديم بتاع finalizeBtn بالسطر ده

    const attendanceFinalizeBtn = document.getElementById('attendance-fabric-popup');
    initPreviewManager(finalizeBtn, attendanceFinalizeBtn);



// دالة لقراءة أول صف بيانات من الاكسيل
    // دالة لقراءة رؤوس الأعمدة وأول صف بيانات من الاكسيل
    function readFirstDataRow(callback) {
        if (templateDataExcelInput && templateDataExcelInput.files.length > 0) {
            const file = templateDataExcelInput.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];

                // قراءة الرؤوس (الصف 0)
                const headers = [];
                const range = XLSX.utils.decode_range(worksheet['!ref']);
                for (let C = range.s.c; C <= range.e.c; ++C) {
                    const cellAddress = XLSX.utils.encode_cell({ r: 0, c: C }); // الصف الأول (رؤوس الأعمدة)
                    const cell = worksheet[cellAddress];
                    headers.push(cell ? cell.v : '');
                }

                // قراءة الصف الأول من البيانات الحقيقية (الصف 1)
                const firstActualDataRow = [];
                for (let C = range.s.c; C <= range.e.c; ++C) {
                    const cellAddress = XLSX.utils.encode_cell({ r: 1, c: C }); // الصف الثاني (أول صف بيانات حقيقي)
                    const cell = worksheet[cellAddress];
                    firstActualDataRow.push(cell ? cell.v : '');
                }

                // تمرير كائن يحتوي على الرؤوس والبيانات
                callback({
                    headers: headers, // رؤوس الأعمدة عشان تستخدم في المطابقة
                    data: firstActualDataRow // أول صف بيانات حقيقي
                });
            };
            reader.readAsArrayBuffer(file);
        } else {
            callback(null);
        }
    }




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


