// To parse URL parameters - if message exists -> show the message
document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get("message");
    const status = urlParams.get("status");

    if (message) {
        alert(message);
        // Removes ?status=...&message=... without reloading page so it displays msg and has normal url
        const url = new URL(window.location);
        url.searchParams.delete("status");
        url.searchParams.delete("message");
        window.history.replaceState({}, document.title, url.pathname);
    }
});

document.addEventListener('click', async function (e) {
    if (e.target.closest('.delete-form button')) {
        e.preventDefault();

        const form = e.target.closest('.delete-form');
        const sku = form.dataset.sku;

        if (!confirm(`Are you sure you want to delete: ${sku}?`)) return;

        const formData = new FormData();
        formData.append('delete_sku', sku);

        try {
            const response = await fetch('/admin/products/delete', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            // Display confirmation message
            alert(data.message);

            if (data.status === 'success') {
                // Option 1: remove the row visually
                form.closest('tr').remove();

                // Option 2: reload product list dynamically
                // loadPage('products');
            }

        } catch (error) {
            console.error('Delete failed:', error);
            alert('An error occurred while deleting the product.');
        }
    }
});
