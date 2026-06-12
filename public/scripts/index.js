document.addEventListener('DOMContentLoaded', () => {
    // Field Modal Elements
    const fieldModal = document.getElementById('field-modal');
    const fieldModalClose = document.getElementById('field-modal-close');
    const fieldModalCancel = document.getElementById('field-modal-cancel');
    const fieldModalTitle = document.getElementById('field-modal-title');
    const fieldModalSubtitle = document.getElementById('field-modal-subtitle');
    const fieldIdInput = document.getElementById('field-id-input');
    const fieldTaskCountInput = document.getElementById('field-task-count-input');
    const fieldQuickCounts = document.getElementById('field-quick-counts');
    const fieldTaskCountForm = document.getElementById('field-task-count-form');
    
    // Random Modal Elements
    const randomModal = document.getElementById('random-modal');
    const randomModalClose = document.getElementById('random-modal-close');
    const randomModalCancel = document.getElementById('random-modal-cancel');
    const randomTaskCountInput = document.getElementById('random-task-count-input');
    const randomQuickCounts = document.getElementById('random-quick-counts');
    const randomTaskCountForm = document.getElementById('random-task-count-form');
    const randomBtn = document.getElementById('random-btn');
    
    const fieldCards = document.querySelectorAll('.field-card');
    const quickCountValues = [5, 10, 15, 20, 25, 30];

    // Funkcje do zamykania modali
    function closeFieldModal() {
        fieldModal.classList.remove('active');
    }

    function closeRandomModal() {
        randomModal.classList.remove('active');
    }

    // Event listenery dla zamykania modali
    fieldModalClose.addEventListener('click', closeFieldModal);
    fieldModalCancel.addEventListener('click', closeFieldModal);
    randomModalClose.addEventListener('click', closeRandomModal);
    randomModalCancel.addEventListener('click', closeRandomModal);

    // Zamykanie modali po kliknięciu poza oknem modalnym
    window.addEventListener('click', function(event) {
        if (event.target === fieldModal) {
            closeFieldModal();
        }
        if (event.target === randomModal) {
            closeRandomModal();
        }
    });

    // Obsługa kart pól (działów)
    fieldCards.forEach((card) => {
        const maxCount = parseInt(card.dataset.maxCount, 10);
        const countLabel = card.querySelector('.field-count');

        if (countLabel) {
            countLabel.innerHTML = `<i class="fa-solid fa-layer-group"></i> Dostępne zadania: ${maxCount}`;
        }

        card.addEventListener('click', () => {
            const fieldId = card.dataset.fieldId;
            const fieldName = card.dataset.fieldName;
            const maxCount = parseInt(card.dataset.maxCount, 10);
            const initialCount = Math.min(5, maxCount);

            fieldIdInput.value = fieldId;
            fieldTaskCountInput.max = maxCount;
            fieldTaskCountInput.value = initialCount;
            fieldModalTitle.innerText = `Dział: ${fieldName}`;
            fieldModalSubtitle.innerText = `Maksymalna liczba zadań: ${maxCount}`;

            // Generowanie szybkich przycisków wyboru liczby zadań
            fieldQuickCounts.innerHTML = '';
            quickCountValues
                .filter((count) => count <= maxCount)
                .forEach((count) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'quick-count';
                    button.innerText = count;
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        fieldTaskCountInput.value = count;
                    });
                    fieldQuickCounts.appendChild(button);
                });

            if (maxCount > 0 && !quickCountValues.includes(maxCount)) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'quick-count';
                button.innerText = maxCount;
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    fieldTaskCountInput.value = maxCount;
                });
                fieldQuickCounts.appendChild(button);
            }

            if (!fieldQuickCounts.children.length) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'quick-count';
                button.innerText = maxCount;
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    fieldTaskCountInput.value = maxCount;
                });
                fieldQuickCounts.appendChild(button);
            }

            fieldModal.classList.add('active');
            fieldTaskCountInput.focus();
        });
    });

    // Obsługa formularza wyboru zadań z konkretnego działu
    fieldTaskCountForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const fieldId = fieldIdInput.value;
        const maxCount = parseInt(fieldTaskCountInput.max, 10);
        let selectedCount = parseInt(fieldTaskCountInput.value, 10);

        if (!fieldId || Number.isNaN(selectedCount)) {
            return;
        }

        selectedCount = Math.max(1, Math.min(selectedCount, maxCount));
        window.location.href = `/exercises/field/${fieldId}?limit=${selectedCount}`;
    });

    // Obsługa przycisku losowych zadań
    randomBtn.addEventListener('click', () => {
        randomTaskCountInput.value = 10;

        // Generowanie szybkich przycisków dla losowych zadań
        randomQuickCounts.innerHTML = '';
        [5, 10, 15, 20, 25, 30, 50].forEach((count) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'quick-count';
            button.innerText = count;
            button.addEventListener('click', (e) => {
                e.preventDefault();
                randomTaskCountInput.value = count;
            });
            randomQuickCounts.appendChild(button);
        });

        randomModal.classList.add('active');
        randomTaskCountInput.focus();
    });

    // Obsługa formularza losowych zadań
    randomTaskCountForm.addEventListener('submit', (event) => {
        event.preventDefault();

        let selectedCount = parseInt(randomTaskCountInput.value, 10);
        if (Number.isNaN(selectedCount) || selectedCount < 1) {
            selectedCount = 10;
        }

        selectedCount = Math.min(selectedCount, 50);
        window.location.href = `/exercises/random?limit=${selectedCount}`;
    });
});