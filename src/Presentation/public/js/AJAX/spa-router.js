
export class Router {

    constructor(contentSelector = '#content') {
        this.routes = {};
        this.content = document.querySelector(contentSelector);
    }

    addRoute(fragmentName, componentFunction) {
        this.routes[fragmentName] = componentFunction;
    }

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
            const route = this.routes[fragmentName];
            if (!route) throw new Error('Route not found');

            // Calling component function; it handles updating the DOM itself
            await route();

            // Update active menu button
            this.setActive(fragmentName);

            // Updating browser history
            if (push) history.pushState({name: fragmentName}, '', '/admin#' + fragmentName);
        } catch (err) {
            content.innerHTML = `<p style="color:red;">Error loading ${fragmentName}: ${err.message}</p>`;
        }
    }

    /**
     * Changes the active button's color
     *
     * @param name of the section to show (dashboard, products, categories)
     */
    setActive(name) {
        document.querySelectorAll('.side-btn').forEach(btn =>
            btn.classList.toggle('active', btn.dataset.target === name)
        );
    }
}
