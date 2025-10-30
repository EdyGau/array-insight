document.addEventListener('DOMContentLoaded', () => {
    const checkbox = document.querySelector('input[name="array_analyzer_form[manualInput]"]');
    const numbersField = document.getElementById('numbersField');
    const sizeField = document.getElementById('sizeField');

    if (!checkbox || !numbersField || !sizeField) return;

    function toggleFields() {
        numbersField.classList.toggle('show', checkbox.checked);
        numbersField.classList.toggle('hide', !checkbox.checked);
        sizeField.classList.toggle('show', !checkbox.checked);
        sizeField.classList.toggle('hide', checkbox.checked);
    }

    toggleFields();

    checkbox.addEventListener('change', toggleFields);
});
