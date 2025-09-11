document.querySelectorAll('#share-btn').forEach(button => {
    button.addEventListener('click', () => {
        const url = button.dataset.url;

        const shareData = {
            title: 'شهادتي',
            text: 'تحقق من الشهادة من خلال هذا الرابط',
            url: url,
        };

        if (navigator.share) {
            navigator.share(shareData)
                .then(() => console.log('تمت المشاركة'))
                .catch(err => console.error('خطأ في المشاركة:', err));
        } else {
            navigator.clipboard.writeText(url).then(() => {
                alert('تم نسخ الرابط للحافظة');
            });
        }
    });
});







