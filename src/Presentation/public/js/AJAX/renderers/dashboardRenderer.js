/**
 * @typedef {Object} DashboardData
 * @property {number} productCount
 * @property {number} categoriesCount
 * @property {number} homePageViews
 * @property {string} mostViewedProduct
 * @property {number} productViews
 */

class DashboardFragment {

    async fetchData() {
        return await ajax.get('/admin/dashboard-data');
    }

    async render() {
        const data = await this.fetchData();
        return `
            <h2>Dashboard</h2>
            <div class="dashboard-layout">
                <div class="dashboard-left">
                    <div class="dashboard-row">
                        <span class="label">Products count:</span>
                        <span class="value">${data.productCount}</span>
                    </div>
                    <div class="dashboard-row">
                        <span class="label">Categories count:</span>
                        <span class="value">${data.categoriesCount}</span>
                    </div>
                </div>

                <div class="dashboard-right">
                    <div class="dashboard-row">
                        <span class="label">Home page opening count:</span>
                        <span class="value">${data.homePageViews}</span>
                    </div>
                    <div class="dashboard-row">
                        <span class="label">The most often viewed product:</span>
                        <span class="value">${data.mostViewedProduct}</span>
                    </div>
                    <div class="dashboard-row">
                        <span class="label">Number of product views:</span>
                        <span class="value">${data.productViews}</span>
                    </div>
                </div>
            </div>
    `;
    }
}