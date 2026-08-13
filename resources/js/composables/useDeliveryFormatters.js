/**
 * Вспомогательные функции форматирования для компонентов доставки.
 * Ранее дублировались между DeliverySelector, DeliveryPointModal и CourierDeliveryModal.
 */

/**
 * Безопасно преобразовать значение в число или null.
 *
 * @param {*} value
 * @returns {number|null}
 */
export function toNumber(value) {
    if (value === null || value === undefined || value === '') {
        return null;
    }

    const numeric = Number(value);
    return Number.isFinite(numeric) ? numeric : null;
}

/**
 * Отформатировать цену в формате «1 234 ₽».
 *
 * @param {number|string|null} value
 * @returns {string}
 */
export function formatPrice(value) {
    const numeric = toNumber(value);
    if (numeric === null || numeric <= 0) {
        return '0 ₽';
    }
    return `${numeric.toLocaleString('ru-RU')} ₽`;
}

/**
 * Отформатировать одиночную дату доставки.
 *
 * @param {string|null} value
 * @returns {string}
 */
export function formatDeliveryDate(value) {
    if (!value) {
        return '';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return String(value);
    }

    return date.toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

/**
 * Отформатировать диапазон дат доставки.
 *
 * @param {Object|null} range — {min, max} или {period_min, Period_max}
 * @returns {string}
 */
export function formatDeliveryDateRange(range) {
    if (!range) {
        return '';
    }

    const min = range.min ?? range.period_min ?? range.calendar_min ?? null;
    const max = range.max ?? range.period_max ?? range.calendar_max ?? null;

    if (!min && !max) {
        return '';
    }

    if (min && max) {
        return `Срок доставки: ${formatDeliveryDate(min)} - ${formatDeliveryDate(max)}`;
    }

    return `Срок доставки: ${formatDeliveryDate(min ?? max)}`;
}
