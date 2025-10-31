
document.addEventListener('DOMContentLoaded', () => {
    const tree = document.querySelector('.categories-tree');

    if (!tree) return;

    // Event delegation: listen on tree container
    tree.addEventListener('click', (e) => {
        const li = e.target.closest('li');
        if (!li) return;

        const childUl = li.querySelector('ul');
        if (!childUl) return;

        e.stopPropagation();
        li.classList.toggle('expanded');
    });

    // Initially expand root categories
    tree.querySelectorAll(':scope > ul > li').forEach(li => {
        li.classList.add('expanded');
    });
});
