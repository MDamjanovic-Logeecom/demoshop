class CategoriesFragment {
    async fetchData() {
        return await ajax.get('/admin/categories-data');
    }

    buildTree(categories, parentId = null) {
        const filtered = categories.filter(c => c.parent_id === parentId);
        if (!filtered.length) return '';

        return `
        <ul>
            ${filtered.map(c => `
                <li data-id="${c.id}" class="collapsed">
                    <span class="caret category-item">${c.title}</span>
                    ${this.buildTree(categories, c.id)}
                </li>
            `).join('')}
        </ul>
    `;
    }

    addExpandCollapseLogic(container) {
        // Handle expand/collapse behavior
        container.querySelectorAll('.caret').forEach(caret => {
            caret.addEventListener('click', () => {
                const li = caret.parentElement;
                li.classList.toggle('expanded');
            });
        });
    }

    addCategorySelectLogic(container, categories) {
        const detailPanel = document.querySelector('.category-details');
        const titleInput = detailPanel.querySelector('input[name="title"]');
        const parentSelect = detailPanel.querySelector('select[name="parent"]');
        const codeInput = detailPanel.querySelector('input[name="code"]');
        const descriptionArea = detailPanel.querySelector('textarea[name="description"]');
        const banner = detailPanel.querySelector('.category-banner');

        // Populate parent <select> options once
        parentSelect.innerHTML = `
            <option value="">Select parent</option>
            ${categories.map(c => `<option value="${c.id}">${c.title}</option>`).join('')}
        `;

        container.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', e => {
                e.stopPropagation(); // avoid collapsing when selecting

                // Remove previous highlight
                container.querySelectorAll('.selected-category').forEach(el => el.classList.remove('selected-category'));
                item.classList.add('selected-category');

                const li = item.closest('li');
                const id = parseInt(li.dataset.id, 10);
                const category = categories.find(c => c.id === id);

                if (category) {
                    titleInput.value = category.title;
                    parentSelect.value = category.parent_id ?? '';
                    codeInput.value = category.code;
                    descriptionArea.value = category.description;
                    banner.textContent = `Selected: ${category.title}`;
                }
            });
        });
    }

    async render() {
        const categories = await this.fetchData();

        const html = `
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

        // Return as a fragment first
        setTimeout(() => {
            const container = document.querySelector('.categories-tree');
            if (container) this.addExpandCollapseLogic(container);
            this.addCategorySelectLogic(container, categories);
        });

        return html;
    }
}
