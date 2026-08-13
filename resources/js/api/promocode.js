import http from './http';

/**
 * API-слой для работы с промокодами.
 */

/**
 * Проверить и применить промокод.
 *
 * @param {{promocode: string, cart_sum: number}} data
 * @returns {Promise<Object>}
 */
export function verifyPromocode(data) {
    return http.post('/promocod/verify', data).then((response) => response.data);
}
