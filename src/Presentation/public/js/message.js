// To parse URL parameters - if message exists -> show the message
document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get("message");
    const status = urlParams.get("status");

    if (message) {
        alert(message);
        // Removes ?status=...&message=... without reloading page so it displays msg and has normal url
        const url = new URL(window.location);
        url.searchParams.delete("status");
        url.searchParams.delete("message");
        window.history.replaceState({}, document.title, url.pathname);
    }
});