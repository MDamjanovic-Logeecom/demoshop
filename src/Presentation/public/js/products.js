async function deleteProduct(sku, title) {
    if (!confirm(`Are you sure you want to delete ${title}?`)) {
        return;
    }

    try {
        const response = await fetch('/admin/products/delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ delete_sku: sku })
        });

        const result = await response.json();

        if (result.status === 'success') {
            alert(result.message);
            location.reload(); // simple first step
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Delete failed:', error);
        alert('An error occurred while deleting the product.');
    }
}