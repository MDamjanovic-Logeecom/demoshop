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
    /**
     * Sets the values for query fetch parameters
     */
    constructor() {
        this.filters = {
            searchTerm: '',
            enabledOnly: false,
            titleAsc: null,  // null = no sort, true = ascending, false = descending
            priceAsc: null,
            page: 1
        };
    }

    /**
     * Fetch the data from the server while applying filters
     *
     * @returns {Promise<string>}
     */
    async fetchData() {
        const params = new URLSearchParams();

        if (this.filters.searchTerm)
            params.append('search', this.filters.searchTerm);

        if (this.filters.enabledOnly)
            params.append('enabledOnly', 'true');

        if (this.filters.titleAsc !== null)
            params.append('titleAsc', this.filters.titleAsc ? 'true' : 'false');

        if (this.filters.priceAsc !== null)
            params.append('priceAsc', this.filters.priceAsc ? 'true' : 'false');

        if (this.filters.page !== null)
            params.append('page', this.filters.page);

        const query = params.toString() ? `?${params.toString()}` : '';
        const response = await ajax.get(`/admin/products-data${query}`);

        // save pagination info for renderPagination
        this.pagination = {
            totalPages: response.totalPages ?? 1,
            currentPage: response.currentPage ?? 1
        };

        return response.products ?? [];
    }

    /**
     * Search for products matching search term from input
     *
     * @param searchTerm
     *
     * @returns {Promise<void>}
     */
    async search(searchTerm){
        if (!searchTerm) return;

        this.filters.searchTerm = searchTerm.toLowerCase().trim();
        this.filters.page = 1;

        this.showSpinner();
        const products = await this.fetchData();
        this.hideSpinner();
        this.renderTable(products);

    }

    /**
     * Handles sorting toggles and updates filters
     *
     * @param column
     *
     * @returns {Promise<void>}
     */
    async handleSort(column) {
        if (column === 'title') {
            // toggle: null > true > false > null
            this.filters.titleAsc = this.filters.titleAsc === null
                ? true
                : this.filters.titleAsc === true
                    ? false
                    : null;
        } else if (column === 'price') {
            this.filters.priceAsc = this.filters.priceAsc === null
                ? true
                : this.filters.priceAsc === true
                    ? false
                    : null;
        }

        this.filters.page = 1;

        this.showSpinner();
        const products = await this.fetchData();
        this.hideSpinner();
        this.renderTable(products);


        // Visual indicator arrows in header
        this.updateSortIndicators();
    }

    /**
     * Visual feedback in table headers
     */
    updateSortIndicators() {
        const titleTh = document.querySelector('th[data-column="title"]');
        const priceTh = document.querySelector('th[data-column="price"]');

        // Clearing text from columns first
        [titleTh, priceTh].forEach(th => th.textContent = th.dataset.column.charAt(0).toUpperCase() + th.dataset.column.slice(1));

        if (this.filters.titleAsc !== null)
            titleTh.textContent += this.filters.titleAsc ? ' ▲' : ' ▼';
        if (this.filters.priceAsc !== null)
            priceTh.textContent += this.filters.priceAsc ? ' ▲' : ' ▼';
    }

    /**
     * Handles pagination controls (Next / Prev / direct page number)
     *
     * @param newPage
     *
     * @returns {Promise<void>}
     */
    async changePage(newPage) {
        if (newPage < 1) return;
        this.filters.page = newPage;

        this.showSpinner();
        const products = await this.fetchData();
        this.hideSpinner();
        this.renderTable(products);

        this.renderPagination(); // Refresh pagination UI
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

    /**
     * Hide spinner and display spinner while table loading
     */
    showSpinner() {
        const spinner = document.getElementById('loading-spinner');
        const table = document.getElementById('products-table');
        if (spinner && table) {
            spinner.classList.remove('hidden');
            table.classList.add('hidden');
        }
    }

    /**
     * Hide spinner and show table when table loaded
     */
    hideSpinner() {
        const spinner = document.getElementById('loading-spinner');
        const table = document.getElementById('products-table');
        if (spinner && table) {
            spinner.classList.add('hidden');
            table.classList.remove('hidden');
        }
    }

    /**
     * Render the products page
     *
     * @returns {Promise<void>}
     */
    async render() {
        // this.showSpinner();
        // const products = await this.fetchData();
        // this.hideSpinner();

        const html = `
            <h2>Products</h2>
        
            <div class="box-buttons">
                <div class="left-buttons">
                    <button type="button" onclick="window.location.href='/admin/products/create'">Add new product</button>
                    <button>Delete selected</button>
                    <button>Enable selected</button>
                </div>
                <div class="right-buttons">
                    <input type="text" id="product-search" placeholder="Search products..." />
                    <button id="filter-btn">Filter</button>
                </div>
            </div>
        
            <table id="products-table">
                <thead>
                <tr>
                    <th>Selected</th>
                    <th data-column="title" style="cursor:pointer">Title</th>
                    <th>SKU</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Short description</th>
                    <th data-column="price" style="cursor:pointer">Price</th>
                    <th>Enabled</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
            
            <div id="loading-spinner" class="spinner hidden">
                <div class="spinner-circle"></div>
                <p>Loading products...</p>
            </div>
            
            <div id="pagination"></div>
            
            <div id="filter-modal" class="modal hidden">
                <div class="modal-content">
                    <h3>Filter Products</h3>
                    <label>
                        <input type="checkbox" id="filter-enabled" /> Show only enabled products
                    </label>
                    <div class="modal-actions">
                        <button id="apply-filters">Apply</button>
                        <button id="close-filters">Cancel</button>
                    </div>
                </div>
            </div>
    `;

        // Insert HTML into container first
        const content = document.getElementById('content');
        content.innerHTML = html;

        this.showSpinner();

        const products = await this.fetchData();

        this.hideSpinner();
        this.renderTable(products);
        this.renderPagination();

        /**
         * Handle product deletion on delete button click -> triggers deleteProduct(sku, title, pressed button)
         */
        content.addEventListener('click', async (e) => {
            const btn = e.target.closest('.delete-btn');
            if (!btn) return;

            const sku = btn.dataset.sku;
            const title = btn.dataset.title;
            await this.deleteProduct(sku, title, btn);
        });

        /**
         * Handle search input -> triggers search(input)
         */
        const searchInput = document.getElementById('product-search');
        searchInput.addEventListener('input', async () => {
            await this.search(searchInput.value);
        });

        const filterBtn = document.getElementById('filter-btn');
        const modal = document.getElementById('filter-modal');
        const closeFilters = document.getElementById('close-filters');
        const applyFilters = document.getElementById('apply-filters');
        const enabledCheckbox = document.getElementById('filter-enabled');

        /**
         * Handle filter button click -> opens modal that displays filter options
         */
        filterBtn.addEventListener('click', () => {
            enabledCheckbox.checked = this.filters.enabledOnly;
            modal.classList.remove('hidden');
        });

        /**
         * Handle cancel button click' -> closes modal
         */
        closeFilters.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        /**
         * Handle apply button click -> applies filters and fetches filtered data
         */
        applyFilters.addEventListener('click', async () => {
            this.filters.enabledOnly = enabledCheckbox.checked;
            this.filters.page = 1;
            modal.classList.add('hidden');

            this.showSpinner();
            const products = await this.fetchData();
            this.hideSpinner();
            this.renderTable(products);
        });

        /**
         * Handle sorting by column on column header click
         */
        content.querySelectorAll('th[data-column]').forEach(th => {
            th.addEventListener('click', async () => {
                const column = th.dataset.column;
                await this.handleSort(column);
            });
        });
    }

    /**
     * Re-renders the table (<tbody>) with a fresh products array
     *
     * @param products
     */
    renderTable(products) {
        const tbody = document.querySelector('table tbody');
        if (!tbody) return;

        tbody.innerHTML = products.map((product, index) => `
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
        `).join('');

        // Reattaching delete listeners
        const content = document.getElementById('content');
        content.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const sku = btn.dataset.sku;
                const title = btn.dataset.title;
                await this.deleteProduct(sku, title, btn);
            });
        });
    }

    /**
     * Renders Pagination buttons under table
     */
    renderPagination() {
        const paginationContainer = document.getElementById('pagination');
        if (!paginationContainer || !this.pagination) return;

        const { totalPages, currentPage } = this.pagination;
        let html = '';

        html += `<button ${currentPage <= 1 ? 'disabled' : ''} data-page="${currentPage - 1}">Prev</button>`;
        html += `<span> Page ${currentPage} of ${totalPages} </span>`;
        html += `<button ${currentPage >= totalPages ? 'disabled' : ''} data-page="${currentPage + 1}">Next</button>`;

        paginationContainer.innerHTML = html;

        paginationContainer.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', async () => {
                const newPage = parseInt(btn.dataset.page, 10);
                await this.changePage(newPage);
            });
        });
    }

}
