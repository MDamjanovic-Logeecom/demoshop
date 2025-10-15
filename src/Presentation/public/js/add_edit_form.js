// Variable to store the selected image file
let uploadedFile = null;

// When file is selected in add/edit forms
function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Store file in a variable
    uploadedFile = file;

    // Update preview
    const preview = document.getElementById('preview');
    preview.src = URL.createObjectURL(file);
}

// Trigger hidden file input
function triggerFileInput(fileInputId) {
    const input = document.getElementById(fileInputId);
    if (input) input.click();
}
