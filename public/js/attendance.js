let attendanceTemplateImageUrl = null;

document.addEventListener('DOMContentLoaded', () => {
    const badgeImageInput = document.getElementById('badge-image-input');
    if (badgeImageInput) {
        badgeImageInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    attendanceTemplateImageUrl = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    const badgeExcelInput = document.getElementById('badge-excel-input-2');
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

                    const previewCanvasEl = document.getElementById('attendance-preview-canvas');
                    if (previewCanvasEl) {
                        const cardId = 'attendance_template_data_file_path-front';
                        cardData[cardId] = { fabricCanvas: null, iTextObjects: [], imageUrl: attendanceTemplateImageUrl || '/path/to/default/template.jpg' };
                        initializeTemplateCanvas(previewCanvasEl, cardData[cardId].imageUrl, cardId);
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
});
