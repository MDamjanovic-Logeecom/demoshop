/**
 * Helper class for AJAX methods
 */
export class Ajax {
    /**
     * @param url for GET method
     * @returns {Promise<string>}
     */
    async get(url) { //fetch doesn't reload page - marked as async (+same_origin -> use cookies from browser)
        const res = await fetch(url, {credentials: 'same-origin'});
        if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
        return res.json();
    }

    /**
     * @param url for POST method
     * @param data to be sent to server
     * @returns {Promise<any>}
     */
    async post(url, data) {
        const res = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
        return res.json();
    }

    /**
     * @param url for DELETE method
     * @returns {Promise<any>}
     */
    async delete(url) {
        const res = await fetch(url, {
            method: 'DELETE',
            credentials: 'same-origin'
        });
        if (!res.ok) throw new Error(`${res.status} ${res.statusText}`);
        return res.json();
    }
}
