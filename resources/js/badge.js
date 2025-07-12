(function() {
      const container = document.getElementById('forms-container');
      const addBtn = document.getElementById('add-card-btn');
      const template = container.querySelector('.form-card');

      function updateNumbers() {
        container.querySelectorAll('.form-card').forEach((card, idx) => {
          const title = card.querySelector('h3');
          if (title) title.textContent = `نموذج ${idx + 1}`;
        });
      }

      updateNumbers();

      addBtn.addEventListener('click', () => {
        const clone = template.cloneNode(true);
        clone.querySelectorAll('input').forEach(i => {
          if (i.type === 'text') i.value = '';
          if (i.type === 'checkbox') i.checked = false;
        });
        clone.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
        clone.querySelectorAll('[id]').forEach(e => e.removeAttribute('id'));
        const header = clone.querySelector('.flex.justify-between.items-center.mb-4');
        const delBtn = document.createElement('button');
        delBtn.type = 'button';
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

      container.addEventListener('change', e => {
        if (e.target.classList.contains('toggle-presence')) {
          const card = e.target.closest('.form-card');
          const pres = card.querySelector('.presence-card');
          pres.classList.toggle('hidden', !e.target.checked);
        }
      });
    })();



    // داخل الـ IIFE أو بعد تحميل الـ DOM
(() => {
  // استمع لتغيير أي قائمة صلاحية داخل نماذجك
  document.getElementById('forms-container')
    .addEventListener('change', e => {
      if (e.target.id === 'validity') {
        // العنصر الأب (البطاقة) الخاص بالنموذج الجاري
        const card = e.target.closest('.form-card');
        // العنصر الذي يحتوي حقلي "من" و "إلى"
        const tempDates = card.querySelector('#temp-dates');
        if (e.target.value === 'temporary') {
          tempDates.classList.remove('hidden');
        } else {
          tempDates.classList.add('hidden');
        }
      }
    });
})();


 (function() {
    // ... الكود الحالي لإضافة/حذف النماذج وتفعيل الحضور ...

    // كود إظهار/إخفاء تواريخ الصلاحية المؤقتة
    document.getElementById('forms-container')
      .addEventListener('change', e => {
        if (e.target.id === 'validity') {
          const card = e.target.closest('.form-card');
          const tempDates = card.querySelector('#temp-dates');
          if (e.target.value === 'temporary') {
            tempDates.classList.remove('hidden');
          } else {
            tempDates.classList.add('hidden');
          }
        }
      });
  })();





  (function() {
    // ... الكود الحالي لإضافة/حذف النماذج وتفعيل الحضور ...

    // كود إظهار/إخفاء تواريخ الصلاحية المؤقتة
    document.getElementById('forms-container')
      .addEventListener('change', e => {
        if (e.target.id === 'validity') {
          const card = e.target.closest('.form-card');
          const tempDates = card.querySelector('#temp-dates');
          if (e.target.value === 'temporary') {
            tempDates.classList.remove('hidden');
          } else {
            tempDates.classList.add('hidden');
          }
        }
      });
  })();


  








