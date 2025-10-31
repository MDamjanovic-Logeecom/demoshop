/**
 * Function for deleting a certain product using the AJAX approach.
 *
 * @param sku of the product to be deleted
 * @param title of the product to be deleted
 *
 * @returns {Promise<void>}
 */
async function deleteProduct(sku, title) {
    if (!confirm(`Are you sure you want to delete ${title}?`)) {
        return;
    }

    try {
        //AJAX call:
        const result = await ajax.post('/admin/products/delete', { delete_sku: sku });

        if (result.status === 'success') {
            alert(result.message);

            // Removing product row without reloading the page
            const row = document.querySelector(`button[onclick*="'${sku}'"]`)?.closest('tr');
            if (row) {
                row.remove();
            }

        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Delete failed:', error);
        alert('An error occurred while deleting the product.');
    }
}