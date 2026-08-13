import axios from 'axios';

/**
 * Единый экземпляр axios для всего фронтенда.
 * CSRF-токен берётся из мета-тега и отправляется заголовком X-CSRF-TOKEN,
 * что обеспечивает совместимость со всеми POST/DELETE-эндпоинтами Laravel.
 */
const instance = axios.create({
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
    },
});

instance.interceptors.request.use((config) => {
    const token = document.querySelector('meta[name="_token"]')?.content;

    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }

    return config;
});

export default instance;
