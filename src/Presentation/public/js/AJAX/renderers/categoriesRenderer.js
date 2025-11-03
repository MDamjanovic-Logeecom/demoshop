import { Ajax } from "../ajax.js";

const ajax = new Ajax();

export class Categories {
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

    toggleEditMode(enable) {

        const viewButtons = document.querySelector('.view-buttons');
        const editButtons = document.querySelector('.edit-buttons');
        const detailInputs = document.querySelectorAll('.category-details input, .category-details select, .category-details textarea');

        if (enable) {
            viewButtons.style.display = 'none';
            editButtons.style.display = 'block';
            detailInputs.forEach(input => input.disabled = false);
        } else {
            viewButtons.style.display = 'flex';
            editButtons.style.display = 'none';
            detailInputs.forEach(input => input.disabled = true);
        }
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

                    // Enable the Edit and Delete buttons in view mode
                    detailPanel.querySelector('.edit-btn').disabled = false;
                    detailPanel.querySelector('.view-buttons .delete-btn').disabled = false;
                }
            });
        });

        // When clicking Edit in view mode
        detailPanel.querySelector('.edit-btn').addEventListener('click', () => {
            this.toggleEditMode(true);
        });

        // When clicking Cancel in edit mode
        detailPanel.querySelector('.cancel-btn').addEventListener('click', () => {
            this.toggleEditMode(false);
            // optionally reset inputs to original values
        });

        // OK button would submit/save changes
        detailPanel.querySelector('.ok-btn').addEventListener('click', async () => {
            // gather input values and send ajax POST/PUT request
            const updatedCategory = {
                id: parseInt(detailPanel.querySelector('.selected-category')?.closest('li')?.dataset.id, 10),
                title: detailPanel.querySelector('input[name="title"]').value,
                parent_id: parseInt(detailPanel.querySelector('select[name="parent"]').value) || null,
                code: detailPanel.querySelector('input[name="code"]').value,
                description: detailPanel.querySelector('textarea[name="description"]').value,
            };

            try {
                const result = await ajax.post('/admin/categories/update', updatedCategory);
                if (result.status === 'success') {
                    alert('Category updated!');
                    this.toggleEditMode(false);
                    // optionally re-render tree
                } else {
                    alert(result.message);
                }
            } catch (err) {
                console.error(err);
                alert('Update failed');
            }
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
                        <!-- view mode -->
                        <div class="view-buttons">
                            <button type="button" class="delete-btn" disabled>Delete</button>
                            <button type="button" class="edit-btn" disabled>Edit</button>
                        </div>
                    
                        <!-- edit mode -->
                        <div class="edit-buttons" style="display: none;">
                            <button type="button" class="delete-btn">Delete</button>
                            <button type="button" class="cancel-btn">Cancel</button>
                            <button type="button" class="ok-btn">OK</button>
                        </div>
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

        const content = document.getElementById('content');
        content.innerHTML = html;
    }
}
