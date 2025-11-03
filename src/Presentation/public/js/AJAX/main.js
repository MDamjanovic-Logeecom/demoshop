import { Router } from './spa-router.js';
import { Dashboard } from './renderers/dashboardRenderer.js';
import { Products } from './renderers/productsRenderer.js';
import { Categories } from './renderers/categoriesRenderer.js';

/**
 * Used as a central place to register routes in the router
 * and to handel UI events.
 */

const router = new Router();

/**
 * Route for reaching data for dashboard page
 */
router.addRoute('dashboard', () => new Dashboard().render());

/**
 * Route for reaching data for product page
 */
router.addRoute('products', () => new Products().render());

/**
 * Route for reaching data for category page
 */
router.addRoute('categories', () => new Categories().render());

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

// Router globally available
window.router = router;
