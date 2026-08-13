import http from './http';

/**
 * API-слой для работы со справочниками доставки (города, ПВЗ, тарифы).
 * Обёртки над эндпоинтами /delivery/*.
 */

/**
 * Получить список городов.
 *
 * @param {{country_codes?: string, city?: string}} [params]
 * @returns {Promise<Array>}
 */
export function getCities(params = {}) {
    return http
        .get('/delivery/cities', { params })
        .then((response) => response.data.data);
}

/**
 * Получить пункты выдачи для города.
 *
 * @param {{city_code: string, type_code?: string}} data
 * @returns {Promise<Array>}
 */
export function getPickupPoints(data) {
    return http
        .post('/delivery/pickup-points', data)
        .then((response) => response.data.data.points);
}

/**
 * Получить курьерские предложения (рассчёт стоимости).
 *
 * @param {{to_code: string, weight: number, delivery_mode?: number}} data
 * @returns {Promise<{best: Object|null, alternatives: Array}>}
 */
export function getCourierOffers(data) {
    return http
        .post('/delivery/courier-offers', data)
        .then((response) => response.data.data);
}
