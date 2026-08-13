import http from './http';

/**
 * API-слой для работы с корзиной.
 *
 * Использует новые эндпоинты /shop/cart/*.
 * Все ответы бэкенда оборачиваются в { success, data }, поэтому
 * здесь делается ответная анпакинг: возвращается только payload из data.
 */

/**
 * Получить содержимое корзины.
 *
 * @returns {Promise<{count: number, position: Array}>}
 */
export function getCart() {
    return http.get('/shop/cart').then((response) => response.data.data);
}

/**
 * Добавить товар в корзину.
 *
 * @param {{product_id: string|number, product_sku: string, addcount: number}} data
 * @returns {Promise<Object>}
 */
export function addToCart(data) {
    return http.post('/shop/cart/add', data).then((response) => response.data.data);
}

/**
 * Обновить количество позиции.
 *
 * @param {{product_id: string|number, count: number}} data
 * @returns {Promise<Object>}
 */
export function updateCartItem(data) {
    return http.post('/shop/cart/update', data).then((response) => response.data.data);
}

/**
 * Удалить позицию из корзины.
 *
 * @param {{product_id: string|number}} data
 * @returns {Promise<Object>}
 */
export function deleteCartItem(data) {
    return http.delete('/shop/cart/delete', { data }).then((response) => response.data.data);
}

/**
 * Очистить корзину.
 *
 * @param {Object} [data] — дополнительные поля (например _token)
 * @returns {Promise<Object>}
 */
export function clearCart(data = {}) {
    return http.delete('/shop/cart/clear', { data }).then((response) => response.data.data);
}

/**
 * Оформить заказ (создание заказа + регистрация платежа YooKassa).
 *
 * @param {Object} payload
 * @returns {Promise<Object>}
 */
export function checkout(payload) {
    return http.post('/shop/cart/checkout', payload).then((response) => response.data.data);
}
