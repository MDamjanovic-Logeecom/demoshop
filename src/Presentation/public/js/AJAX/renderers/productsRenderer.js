/**
 * @typedef {Object} products
 * @property {boolean} enabled
 * @property {string} sku
 * @property {string} category
 * @property {string} brand
 * @property {string} shortDescription
 * @property {number} price
 */

class ProductsFragment {

    async fetchData() {
        return await ajax.get('/admin/products-data');
    }

    async render() {
        const products = await this.fetchData();
        return `
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
                                <button onclick="deleteProduct('${product.sku}', '${product.title}')">Delete</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
    `;
    }
}
