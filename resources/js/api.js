export async function apiFetch(url, options = {}) {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const res = await fetch(url, {
        ...options,
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...options.headers,
        },
    });
    if (res.status === 419) {
        window.dispatchEvent(new CustomEvent('session-expired'));
        throw new Error('CSRF token expired');
    }
    return res;
}
window.apiFetch = apiFetch;