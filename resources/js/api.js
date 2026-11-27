const csrfMeta = () => document.querySelector('meta[name="csrf-token"]');

async function refreshCsrfToken() {
    const response = await fetch('/csrf-token', {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
    });

    if (!response.ok) throw new Error('Không thể làm mới CSRF token.');

    const data = await response.json();
    const meta = csrfMeta();
    if (meta && data.token) meta.content = data.token;
    return data.token;
}

export async function apiFetch(url, options = {}, retry = true) {
    const meta = csrfMeta();
    const token = meta?.content ?? '';

    const headers = {
        Accept: 'application/json',
        ...(options.body && !(options.body instanceof FormData)
            ? { 'Content-Type': 'application/json' }
            : {}),
        ...options.headers,
        'X-CSRF-TOKEN': token,
    };

    let response = await fetch(url, {
        credentials: 'same-origin',
        ...options,
        headers,
    });

    if (response.status === 419 && retry) {
        try {
            await refreshCsrfToken();
            return apiFetch(url, options, false);
        } catch (_) {
            window.dispatchEvent(new CustomEvent('session-expired'));
            throw new Error('Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.');
        }
    }

    if (response.status === 419) {
        window.dispatchEvent(new CustomEvent('session-expired'));
        throw new Error('Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.');
    }

    return response;
}

window.apiFetch = apiFetch;
