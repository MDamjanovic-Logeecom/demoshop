const routes = {
    dashboard: '/admin/dashboard',
    products: '/admin/products',
    categories: '/admin/categories'
};

function setActive(name) {
    document.querySelectorAll('.side-btn').forEach(btn =>
        btn.classList.toggle('active', btn.dataset.target === name)
    );
}

async function navigate(name, push = true) {
    const url = routes[name];
    const container = document.getElementById('app-content');
    container.innerHTML = '<p>Loading...</p>';

    try {
        const res = await fetch(url, {credentials: 'same-origin'});
        if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
        const html = await res.text();
        container.innerHTML = html;
        setActive(name);
        if (push) history.pushState({name}, '', '/admin#' + name);
    } catch (err) {
        container.innerHTML = `<p style="color:red;">Error loading ${name}: ${err.message}</p>`;
    }
}

document.addEventListener('click', e => {
    const btn = e.target.closest('.side-btn');
    if (!btn) return;
    e.preventDefault();
    navigate(btn.dataset.target);
});

window.addEventListener('popstate', e => {
    const name = e.state?.name || location.hash.replace('#', '') || 'dashboard';
    navigate(name, false);
});

// initial load
window.addEventListener('DOMContentLoaded', () => {
    const initial = location.hash.replace('#', '') || 'dashboard';
    navigate(initial, false);
});
