const routes = { // server urls to fetch for the fragments
    dashboard: '/admin/dashboard',
    products: '/admin/products',
    categories: '/admin/categories'
};

/**
 * Changes the active button's color
 *
 * @param name of the section to show (dashboard, products, categories)
 */
function setActive(name) {
    document.querySelectorAll('.side-btn').forEach(btn =>
        btn.classList.toggle('active', btn.dataset.target === name)
    );
}

/**
 * Function for switching fragments in the admin_layout shell using the AJAX approach.
 *
 * @param name of the section to show (dashboard, products, categories)
 * @param push whether the browser's address bar is to be updated
 *
 * @returns {Promise<void>}
 */
async function navigate(name, push = true) {
    const url = routes[name]; // gets the  correct url from routes const
    const container = document.getElementById('app-content'); //gets html element where to place fragment
    container.innerHTML = '<p>Loading...</p>';

    try {//fetch doesn't reload page - marked as async (+same_origin -> use cookies from browser)
        const res = await fetch(url, {credentials: 'same-origin'}); // req. from server for html for the fragment
        if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
        const html = await res.text();
        container.innerHTML = html; //replaces content in the designated spot with fetched fragment html
        setActive(name);
        if (push) history.pushState({name}, '', '/admin#' + name);
    } catch (err) {
        container.innerHTML = `<p style="color:red;">Error loading ${name}: ${err.message}</p>`;
    }
}

/**
 * Listens to any clicks on the whole page, runs the navigate function if
 * element clicked is one of the menu buttons (.side-btn)
 */
document.addEventListener('click', e => {
    const btn = e.target.closest('.side-btn');
    if (!btn) return;
    e.preventDefault();
    navigate(btn.dataset.target);
});

/**
 * When clicking on browser's "back" and "forward buttons", if earlier state
 * found, go back to the previous/next fragment - it not, load dashboard.
 */
window.addEventListener('popstate', e => {
    const name = e.state?.name || location.hash.replace('#', '') || 'dashboard';
    navigate(name, false);
});

/**
 * Initial page loading - by default shows dashboard fragment first.
 */
window.addEventListener('DOMContentLoaded', () => {
    const initial = location.hash.replace('#', '') || 'dashboard';
    navigate(initial, false);
});
