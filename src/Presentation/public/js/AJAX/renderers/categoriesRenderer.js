
class CategoriesFragment {
    async fetchData() {
        return await ajax.get('/admin/categories-data');
    }

    buildTree(categories, parentId = null) {
        const filtered = categories.filter(c => c.parentId === parentId);
        if (!filtered.length) return '';

        return `
            <ul>
                ${filtered.map(c => `
                    <li data-id="${c.id}">
                        ${c.title}
                        ${this.buildTree(categories, c.id)}
                    </li>
                `).join('')}
            </ul>
        `;
    }

    async render() {
        const categories = await this.fetchData();

        return `
            <div class="categories-layout">
                <div class="categories-tree">
                    ${this.buildTree(categories)}
                    <div class="tree-buttons">
                        <button type="button" class="tree-btn add-root-btn">Add root category</button>
                        <button type="button" class="tree-btn add-sub-btn">Add subcategory</button>
                    </div>
                </div>

                <div class="category-details">
                    <div class="category-banner">Selected category</div>
                    <label>Title: <input type="text" name="title" disabled></label>
                    <label>Parent category:
                        <select name="parent" disabled>
                            <option value="">Select parent</option>
                        </select>
                    </label>
                    <label>Code: <input type="text" name="code" disabled></label>
                    <label>Description: <textarea name="description" disabled></textarea></label>
                    <div class="details-buttons">
                        <button type="button" class="delete-btn" disabled>Delete</button>
                        <button type="button" class="cancel-btn" disabled>Cancel</button>
                        <button type="button" class="ok-btn" disabled>OK</button>
                    </div>
                </div>
            </div>
    `;
    }
}
