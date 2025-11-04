import {Ajax} from "../ajax.js";

const ajax = new Ajax();

export class Categories {
    /**
     * Get all categories
     *
     * @returns {Promise<string>}
     */
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
        const detailPanel = document.querySelector('.category-details');
        const viewButtons = detailPanel.querySelector('.view-buttons');
        const editButtons = detailPanel.querySelector('.edit-buttons');
        const createButtons = detailPanel.querySelector('.create-buttons');
        const detailInputs = detailPanel.querySelectorAll('input, select, textarea');
        const titleInput = detailPanel.querySelector('input[name="title"]');
        const codeInput = detailPanel.querySelector('input[name="code"]');
        const parentSelect = detailPanel.querySelector('select[name="parent"]');
        const descriptionArea = detailPanel.querySelector('textarea[name="description"]');
        const banner = detailPanel.querySelector('.category-banner');

        // resetting all buttons
        viewButtons.style.display = 'none';
        editButtons.style.display = 'none';
        createButtons.style.display = 'none';

        detailInputs.forEach(input => (input.disabled = true));

        switch (enable) {
            case 'edit' :
                editButtons.style.display = 'block';
                detailInputs.forEach(input => (input.disabled = false));
                codeInput.disabled = true;
                break;

            case 'create-sub':
            case 'create-root':
                createButtons.style.display = 'block';
                detailInputs.forEach(input => (input.disabled = false));

                titleInput.value = '';
                codeInput.value = '';
                descriptionArea.value = '';
                parentSelect.value = '';

                // banner
                banner.textContent =
                    enable === 'create-sub'
                        ? 'Adding new subcategory'
                        : 'Adding new root category';

                codeInput.disabled = false;
                parentSelect.disabled = enable === 'create-root'; // disable only for root
                break;

            case 'clear' :
                titleInput.value = '';
                codeInput.value = '';
                descriptionArea.value = '';
                parentSelect.value = '';

            case 'view' :
            default : {
                viewButtons.style.display = 'flex';
                break;
            }
        }
    }

    categoryEventsLogic(container, categories) {
        const detailPanel = document.querySelector('.category-details');
        const titleInput = detailPanel.querySelector('input[name="title"]');
        const parentSelect = detailPanel.querySelector('select[name="parent"]');
        const codeInput = detailPanel.querySelector('input[name="code"]');
        const descriptionArea = detailPanel.querySelector('textarea[name="description"]');
        const banner = detailPanel.querySelector('.category-banner');

        const addRootBtn = container.querySelector('.add-root-btn');
        const addSubBtn = container.querySelector('.add-sub-btn');
        const viewDeleteBtn = detailPanel.querySelector('.view-buttons .delete-btn');
        const editDeleteBtn = detailPanel.querySelector('.edit-buttons .delete-btn');

        // Populate parent <select> options once
        parentSelect.innerHTML = `
            <option value="">Select parent</option>
            ${categories.map(c => `<option value="${c.id}">${c.title}</option>`).join('')}
        `;

        /**
         * Tree selection
         */
        container.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', e => {
                e.stopPropagation(); // avoid collapsing when selecting
                this.toggleEditMode('view');
                const li = item.closest('li');
                if (!li) return;

                // Remove previous highlight
                container.querySelectorAll('.selected-category').forEach(el => el.classList.remove('selected-category'));
                li.classList.add('selected-category');

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

        /**
         * Edit mode activation
         */
        detailPanel.querySelector('.edit-btn').addEventListener('click', () => {
            this.toggleEditMode('edit');
        });

        /**
         * Edit mode deactivation
         */
        detailPanel.querySelector('.cancel-btn').addEventListener('click', () => {
            this.toggleEditMode('view');
        });

        /**
         * Create mode deactivation
         */
        detailPanel.querySelector('.cancel-create-btn').addEventListener('click', () => {
            this.toggleEditMode('view');
        });

        /**
         * Create root mode activation
         */
        addRootBtn.addEventListener('click', () => {
            this.currentMode = 'create-root';
            this.toggleEditMode('create-root');
        });

        /**
         * Create subcategory mode activation
         */
        addSubBtn.addEventListener('click', () => {
            this.currentMode = 'create-sub';
            this.toggleEditMode('create-sub');
        });

        /**
         * Create a new category
         */
        const okCreateBtn = document.querySelector('.ok-create-btn');

        // Replaces the button with a fresh clone to remove previous listeners
        okCreateBtn.replaceWith(okCreateBtn.cloneNode(true));

        const freshOkCreateBtn = document.querySelector('.ok-create-btn');
        freshOkCreateBtn.addEventListener('click', async () => {
            const isSub = this.currentMode === 'create-sub';
            await this.createCategory(isSub, container);
        });

        /**
         * Save edit on "OK" button press
         */
        detailPanel.querySelector('.ok-btn').addEventListener('click', async () => {
            const selectedSpan = container.querySelector('.selected-category');
            if (!selectedSpan) return alert('No category selected');

            // gather input values and send ajax POST/PUT request
            const updatedCategory = {
                id: parseInt(selectedSpan.dataset.id, 10),
                title: titleInput.value,
                parent_id: parseInt(parentSelect.value) || null,
                code: codeInput.value,
                description: descriptionArea.value
            };

            try {
                const result = await ajax.post('/admin/categories/update', updatedCategory);
                if (result.status === 'success') {
                    alert('Category updated!');
                    this.toggleEditMode('view');

                    // Re-fetch categories and re-render only the <ul> tree
                    const categories = await this.fetchData();
                    const treeList = container.querySelector('.tree-list'); // only the tree
                    treeList.innerHTML = this.buildTree(categories);

                    this.addExpandCollapseLogic(treeList);
                    this.categoryEventsLogic(container, categories);

                } else {
                    alert(result.message);
                }
            } catch (err) {
                console.error(err);
                alert('Update failed');
            }
        });

        /**
         * Deletes selected category
         * (attaches listener for set delete action to delete buttons)
         *///TODO: CHECK IF PRODUCT IS CONNECTED TO IT - MAKE THAT CHECK HERE OR AT BACKEND! (when products-category connection implemented)
        const attachDeleteListener = (btn) => {
            const freshBtn = btn.cloneNode(true);
            btn.replaceWith(freshBtn);

            freshBtn.addEventListener('click', async () => {
                const selectedSpan = container.querySelector('.selected-category');
                if (!selectedSpan) return alert('No category selected');

                const code = codeInput.value;
                if (!code) return alert('Category code not found.');

                if (!confirm(`Are you sure you want to delete category "${titleInput.value}"?`)) return;

                try {
                    const result = await ajax.post('/admin/categories/delete', {code});

                    if (result.status === 'success') {
                        alert('Category deleted successfully.');
                        this.toggleEditMode('clear');

                        const categories = await this.fetchData();
                        const treeList = container.querySelector('.tree-list');
                        treeList.innerHTML = this.buildTree(categories);

                        this.addExpandCollapseLogic(treeList);
                        this.categoryEventsLogic(container, categories);
                    } else {
                        alert(result.message);
                    }
                } catch (err) {
                    console.error(err);
                    alert('Failed to delete category.');
                }
            });
        };

        attachDeleteListener(viewDeleteBtn);
        attachDeleteListener(editDeleteBtn);
    }

    /**
     * Category creation
     *
     * @param isSub - defines whether the new item is a subcategory or root category
     * @param container
     * @returns {Promise<void>}
     */
    async createCategory(isSub = false, container) {
        const detailPanel = document.querySelector('.category-details');
        const titleInput = detailPanel.querySelector('input[name="title"]');
        const codeInput = detailPanel.querySelector('input[name="code"]');
        const parentSelect = detailPanel.querySelector('select[name="parent"]');
        const descriptionArea = detailPanel.querySelector('textarea[name="description"]');

        if (!titleInput.value.trim() || !codeInput.value.trim()) {
            alert('Title and code are required.');
            return;
        }

        const parentId = isSub ? parseInt(parentSelect.value) || null : null;

        const newCategory = {
            title: titleInput.value,
            code: codeInput.value,
            description: descriptionArea.value,
            parent_id: parentId
        };

        try {
            const result = await ajax.post('/admin/categories/create', newCategory);
            if (result.status === 'success') {
                alert('Category created!');
                this.toggleEditMode('view');

                // Re-fetch categories and re-render only the <ul> tree
                const categories = await this.fetchData();
                const treeList = container.querySelector('.tree-list'); // only the tree
                treeList.innerHTML = this.buildTree(categories);

                this.addExpandCollapseLogic(treeList);
                this.categoryEventsLogic(container, categories);
            } else {
                alert(result.message);
            }
        } catch (err) {
            console.error(err);
            alert('Category creation failed');
        }
    }

    async render() {
        const categories = await this.fetchData();

        const html = `
            <div class="categories-layout">
                <div class="categories-tree">
                    <div class="tree-list">
                        ${this.buildTree(categories)}
                    </div>
                
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
                        
                        <!-- create mode -->
                        <div class="create-buttons" style="display: none;">
                            <button type="button" class="cancel-create-btn">Cancel</button>
                            <button type="button" class="ok-create-btn">OK</button>
                        </div>
                    </div>
                </div>
            </div>
    `;

        // Return as a fragment first
        setTimeout(() => {
            const container = document.querySelector('.categories-tree');
            if (container) this.addExpandCollapseLogic(container);
            this.categoryEventsLogic(container, categories);
        });

        const content = document.getElementById('content');
        content.innerHTML = html;
    }
}
