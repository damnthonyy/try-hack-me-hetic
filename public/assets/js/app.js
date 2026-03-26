(() => {
    'use strict';

    const AUTH_ENDPOINTS = new Set(['/login', '/register']);

    /**
     * @param {HTMLElement} section
     * @param {string} message
     */
    function showFetchError(section, message) {
        let el = section.querySelector('.js-auth-fetch-error');
        if (!el) {
            el = document.createElement('div');
            el.className = 'auth__alert auth__alert--error js-auth-fetch-error';
            el.setAttribute('role', 'alert');
            const form = section.querySelector('.auth__form');
            if (form && form.parentNode) {
                form.parentNode.insertBefore(el, form);
            } else {
                section.appendChild(el);
            }
        }
        el.textContent = message;
        el.hidden = false;
    }

    /**
     * @param {HTMLElement} section
     */
    function hideFetchError(section) {
        const el = section.querySelector('.js-auth-fetch-error');
        if (el) {
            el.hidden = true;
            el.textContent = '';
        }
    }

    /**
     * @param {HTMLFormElement} form
     */
    async function submitAuthForm(form) {
        const action = form.getAttribute('action') || '';
        const section = form.closest('.auth');
        if (!section) {
            return;
        }

        const isRegister = action === '/register';
        const emailInput = form.querySelector('[name="email"]');
        const passwordInput = form.querySelector('[name="password"]');
        const email = (emailInput && 'value' in emailInput ? String(emailInput.value) : '').trim();
        const password = passwordInput && 'value' in passwordInput ? String(passwordInput.value) : '';

        let confirmation = '';
        if (isRegister) {
            const c = form.querySelector('[name="password_confirmation"]');
            confirmation = c && 'value' in c ? String(c.value) : '';
        }

        if (!email || !password) {
            showFetchError(section, 'Email et mot de passe obligatoires.');
            return;
        }

        if (isRegister && password !== confirmation) {
            showFetchError(section, 'Les mots de passe ne correspondent pas.');
            return;
        }

        /** @type {Record<string, string>} */
        const payload = { email, password };
        if (isRegister) {
            payload.password_confirmation = confirmation;
        }

        hideFetchError(section);

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
        }

        try {
            const res = await fetch(action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                const msg =
                    data && data.error && typeof data.error.message === 'string'
                        ? data.error.message
                        : 'Une erreur est survenue.';
                showFetchError(section, msg);
                return;
            }

            window.location.assign('/dashboard');
        } catch {
            showFetchError(section, 'Impossible de contacter le serveur.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form.auth__form').forEach((form) => {
            const action = form.getAttribute('action') || '';
            if (!AUTH_ENDPOINTS.has(action)) {
                return;
            }
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                void submitAuthForm(form);
            });
        });
    });
})();
