import { Ajax } from "../ajax.js";

const ajax = new Ajax();

/**
 * @typedef {Object} products
 * @property {boolean} enabled
 * @property {string} sku
 * @property {string} category
 * @property {string} brand
 * @property {string} shortDescription
 * @property {number} price
 */

export class Products {

    async fetchData() {
        return await ajax.get('/admin/products-data');
    }

    /**
     * Function for deleting a certain product using the AJAX approach.
     *
     * @param sku of the product to be deleted
     * @param title of the product to be deleted
     *
     * @param btn
     * @returns {Promise<void>}
     */
    async deleteProduct(sku, title, btn) {
        if (!confirm(`Are you sure you want to delete ${title}?`)) {
            return;
        }

        try {
            //AJAX call:
            const result = await ajax.post('/admin/products/delete', { delete_sku: sku });

            if (result.status === 'success') {
                alert(result.message);

                // Removing product row without reloading the page
                const row = btn.closest('tr');
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

    async render() {
        const products = await this.fetchData();
        const html = `
            <h2>Products</h2>
        
            <div class="box-buttons">
                <div class="left-buttons">
                    <button type="button" onclick="window.location.href='/admin/products/create'">Add new product</button>
                    <button>Delete selected</button>
                    <button>Enable selected</button>
                </div>
                <div class="right-buttons">
                    <button>Filter</button>
                </div>
            </div>
        
            <table>
                <thead>
                <tr>
                    <th>Selected</th>
                    <th>Title</th>
                    <th>SKU</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Short description</th>
                    <th>Price</th>
                    <th>Enabled</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    ${products.map((product, index) => `
                        <tr>
                            <td><input type="checkbox" value="${index}"></td>
                            <td>${product.title}</td>
                            <td>${product.sku}</td>
                            <td>${product.brand}</td>
                            <td>${product.category}</td>
                            <td>${product.shortDescription}</td>
                            <td>$${product.price.toFixed(2)}</td>
                            <td class="checkbox-cell">
                                <input type="checkbox" name="enabled[]" value="${index}" ${product.enabled ? 'checked' : ''}>
                            </td>
                            <td class="button-cell">
                                <button onclick="window.location.href='/admin/products/${product.sku}'">Edit</button>
                            </td>
                            <td class="button-cell">
                                <button class="delete-btn" data-sku="${product.sku}" data-title="${product.title}">Delete</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
    `;

        // Insert HTML into container first
        const content = document.getElementById('content');

        content.addEventListener('click', async (e) => {
            const btn = e.target.closest('.delete-btn');
            if (!btn) return;

            const sku = btn.dataset.sku;
            const title = btn.dataset.title;
            await this.deleteProduct(sku, title, btn);
        });

        content.innerHTML = html;
    }
}
