
const router = {
    routes: {},

    addRoute(fragmentName, componentFunction) {
        this.routes[fragmentName] = componentFunction;
    },

    /**
     * Function for switching fragments in the admin_layout shell using the AJAX approach.
     *
     * @param fragmentName
     * @param push whether the browser's address bar is to be updated
     *
     * @returns {Promise<void>}
     */
    async navigate(fragmentName, push = true) {
        const content = document.getElementById('content');
        content.innerHTML = '<p>Loading...</p>';

        try {
            if (!this.routes[fragmentName]) throw new Error('Route not found');
            // Call the component function and inject HTML
            const html = await this.routes[fragmentName]();
            content.innerHTML = html; //replaces content in the designated spot with fetched fragment html

            // Update active menu button
            setActive(fragmentName);

            // Updating browser history
            if (push) history.pushState({name: fragmentName}, '', '/admin#' + fragmentName);
        } catch (err) {
            content.innerHTML = `<p style="color:red;">Error loading ${fragmentName}: ${err.message}</p>`;
        }
    }
};

/**
 * Route for reaching data for dashboard page
 */
router.addRoute('dashboard', () => new DashboardFragment().render());

/**
 * Route for reaching data for product page
 */
router.addRoute('products', () => new ProductsFragment().render());

/**
 * Route for reaching data for category page
 */
router.addRoute('categories', () => new CategoriesFragment().render());


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
// async function navigate(name, push = true) {
//     const url = routes[name]; // gets the  correct url from routes const
//     const container = document.getElementById('content');
//     container.innerHTML = '<p>Loading...</p>';
//
//     try {
//         const html = await ajax.get(url);
//         container.innerHTML = html; //replaces content in the designated spot with fetched fragment html
//         setActive(name);
//         if (push) history.pushState({name}, '', '/admin#' + name);
//     } catch (err) {
//         container.innerHTML = `<p style="color:red;">Error loading ${name}: ${err.message}</p>`;
//     }
// }

/**
 * Listens to any clicks on the whole page, runs the navigate function if
 * element clicked is one of the menu buttons (.side-btn)
 */
document.addEventListener('click', e => {
    const btn = e.target.closest('.side-btn');
    if (!btn) return;
    e.preventDefault();
    router.navigate(btn.dataset.target);
});

/**
 * When clicking on browser's "back" and "forward buttons", if earlier state
 * found, go back to the previous/next fragment - it not, load dashboard.
 */
window.addEventListener('popstate', e => {
    const name = e.state?.name || location.hash.replace('#', '') || 'dashboard';
    router.navigate(name, false);
});

/**
 * Initial page loading - by default shows dashboard fragment first.
 */
window.addEventListener('DOMContentLoaded', () => {
    const initial = location.hash.replace('#', '') || 'dashboard';
    router.navigate(initial, false);
});
