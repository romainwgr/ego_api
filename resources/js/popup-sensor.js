function toggleInput(index) {
    console.log("toggleInput", index);

    const input = document.getElementById('input_' + index);
    const label = document.getElementById('label_' + index);
    const checkbox = document.getElementById('chk_' + index);

    if (!input || !label || !checkbox) {
        console.warn('Un des éléments est introuvable pour l’index', index);
        return;
    }

    if (checkbox.checked) {
        input.classList.remove('d-none');
        label.classList.add('d-none');
    } else {
        input.classList.add('d-none');
        label.classList.remove('d-none');
    }
};

window.toggleInput = toggleInput;