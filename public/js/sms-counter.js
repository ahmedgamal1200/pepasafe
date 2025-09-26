document.addEventListener('DOMContentLoaded', function () {

    /**
     * A utility function to debounce calls to a function.
     * @param {function} func - The function to be debounced.
     * @param {number} delay - The delay in milliseconds.
     */
    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    }

    /**
     * A function that starts a character counter and updates a hidden field,
     * while also sending the character count to the backend via AJAX.
     * @param {string} textareaId - The ID of the textarea.
     * @param {string} counterId - The ID of the div displaying the counter.
     * @param {string} hiddenInputId - The ID of the hidden field that stores the character count.
     */
    function initializeCharCounter(textareaId, counterId, hiddenInputId) {
        const textarea = document.getElementById(textareaId);
        const charCounter = document.getElementById(counterId);
        const hiddenInput = document.getElementById(hiddenInputId);

        if (!textarea || !charCounter || !hiddenInput) {
            console.error(`One of the elements not found: ${textareaId}, ${counterId}, or ${hiddenInputId}`);
            return;
        }

        // A debounced version of the AJAX function
        const sendCharCount = debounce((count) => {
            // Check for character count
            if (count > 0) {
                // Using Fetch API for simplicity and modern JavaScript
                fetch('/calculate-document-price', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ character_count: count })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Success! Char Count calculated:', data);
                        // You can display the price here, for example:
                        // document.getElementById('price-display').textContent = `Price: ${data.price}`;
                    })
                    .catch(error => {
                        console.error('Error calculating Char Count:', error);
                    });
            }
        }, 500); // 500ms delay

        function updateCounter() {
            const currentLength = textarea.value.length;

            // 1. Update the visible counter
            charCounter.textContent = `${window.i18n.char_count}: ${currentLength}`;

            // 2. Update the hidden field value
            hiddenInput.value = currentLength;

            // 3. Trigger the AJAX call with the new count
            sendCharCount(currentLength);
        }

        // Connect the update function to the input event
        textarea.addEventListener('input', updateCounter);

        // Run the counter on page load for any old data
        updateCounter();
    }

    // ✨ Call the function with the hidden field ID as the third argument
    initializeCharCounter(
        'attendance-message-input',
        'attendance-char-counter',
        'attendance-char-count-hidden'
    );

    initializeCharCounter(
        'document-message-input',
        'document-char-counter',
        'document-char-count-hidden'
    );

});
