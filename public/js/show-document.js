document.addEventListener('DOMContentLoaded', () => {
    // Generate QR code
    const qrContainer = document.getElementById('qr-container');
    const rawUrl = qrContainer.getAttribute('data-qr-url') || '';
    const size = '150x150';
    const qrImg = document.createElement('img');
    qrImg.alt = 'QR Code';
    qrImg.className = 'w-full h-full object-contain';
    qrImg.src = `https://chart.googleapis.com/chart?chs=${size}&cht=qr&chl=${encodeURIComponent(rawUrl)}`;
    qrContainer.appendChild(qrImg);

    // Modal functionality
    const certImage = document.getElementById('cert-image');
    const modal = document.getElementById('image-modal');
    const modalImage = document.getElementById('modal-image');
    const modalClose = document.getElementById('modal-close');

    certImage.addEventListener('click', () => {
        modalImage.src = certImage.src;
        modal.classList.remove('hidden');
    });

    modalClose.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Close when clicking outside image
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});


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

