let yandexApiPromise = null;

/**
 * Composable для загрузки Яндекс.Карт.
 * Скрипт загружается один раз и кэшируется в yandexApiPromise,
 * поэтому несколько компонентов могут одновременно использовать карту.
 *
 * @returns {{loadYandexMaps: () => Promise<void>}}
 */
export function useYandexMap() {
    function loadYandexMaps() {
        if (window.ymaps) {
            return Promise.resolve();
        }

        if (yandexApiPromise) {
            return yandexApiPromise;
        }

        const scriptSrc = '//api-maps.yandex.ru/2.1/?lang=ru_RU';

        yandexApiPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = scriptSrc;
            script.async = true;
            script.onload = () => window.ymaps.ready(() => resolve());
            script.onerror = () => {
                yandexApiPromise = null;
                reject(new Error('Не удалось загрузить Яндекс.Карты'));
            };
            document.head.appendChild(script);
        });

        return yandexApiPromise;
    }

    return { loadYandexMaps };
}
