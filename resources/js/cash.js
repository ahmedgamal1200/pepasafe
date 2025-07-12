// عند الحاجة لجلب العدد من API:
// يتوقّع الرد بصيغة JSON: { count: 5 }

document.addEventListener("DOMContentLoaded", () => {
  const notifCountEl = document.getElementById("notif-count");

  // استبدل '/api/notifications/count' بالـ endpoint الخاص بكم
  fetch("/api/notifications/count")
    .then((res) => {
      if (!res.ok) throw new Error("فشل في جلب عدد الإشعارات");
      return res.json();
    })
    .then((data) => {
      notifCountEl.textContent = data.count;
      // إذا كان العدد صفر، يمكنك إخفاء العداد:
      // if (data.count === 0) notifCountEl.style.display = 'none';
    })
    .catch((err) => {
      console.error(err);
      // إبقاء العداد على القيمة الابتدائية أو إخفاؤه:
      // notifCountEl.style.display = 'none';
    });
});




  // // نسخ رقم الحساب
  //   document.getElementById('copy-account').addEventListener('click', function() {
  //     const acc = document.getElementById('account-number').textContent;
  //     navigator.clipboard.writeText(acc);
  //     alert('تم نسخ رقم الحساب');
  //   });
    // تفعيل زر تأكيد الدفع عند إرفاق ملف
    document.getElementById('file-attachment').addEventListener('change', function(e) {
      document.getElementById('confirm-payment').disabled = !e.target.files.length;
    });





