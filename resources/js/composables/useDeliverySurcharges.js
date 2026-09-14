/**
 * Надбавки к стоимости доставки СДЭК, которые мы показываем покупателю.
 *
 * - delivery_sum (тариф СДЭК) приходит без НДС.
 * - Страховой сбор СДЭК: 0.75% от объявленной стоимости (cost по нашим товарам).
 * - Надбавки облагаются НДС 5% (ставка по договору).
 */

export const VAT_RATE = 0.05;
export const INSURANCE_RATE = 0.0075;

function roundCurrency(value) {
    if (!Number.isFinite(value)) {
        return 0;
    }
    return Math.round(value * 100) / 100;
}

/**
 * Разбивка итоговой стоимости доставки.
 *
 * @param {number|null|undefined} baseDelivery — тариф СДЭК (delivery_sum) без НДС
 * @param {number|null|undefined} cartSubtotal — сумма корзины (cost по товарам)
 * @returns {{base: number, vat: number, insurance: number, insuranceVat: number, total: number}}
 */
export function calculateDeliveryCost(baseDelivery, cartSubtotal = 0) {
    const base = roundCurrency(Number(baseDelivery) || 0);
    const vat = roundCurrency(base * VAT_RATE);

    const insurance = roundCurrency(
        (Number(cartSubtotal) || 0) * INSURANCE_RATE,
    );
    const insuranceVat = roundCurrency(insurance * VAT_RATE);

    const total = roundCurrency(base + vat + insurance + insuranceVat);

    return { base, vat, insurance, insuranceVat, total };
}

/**
 * Текстовое представление разбивки для отображения покупателю.
 *
 * @param {ReturnType<typeof calculateDeliveryCost>} breakdown
 * @returns {string}
 */
export function formatDeliveryBreakdown(breakdown) {
    if (!breakdown || breakdown.base <= 0) {
        return '';
    }

    const lines = [
        `Тариф СДЭК: ${breakdown.base.toLocaleString('ru-RU')} ₽`,
    ];

    if (breakdown.insurance > 0) {
        lines.push(
            `Страховка (0.75%): ${breakdown.insurance.toLocaleString(
                'ru-RU',
            )} ₽`,
        );
    }

    if (breakdown.vat > 0) {
        lines.push(
            `в т.ч. НДС 5%: ${(breakdown.vat + breakdown.insuranceVat)
                .toLocaleString('ru-RU')} ₽`,
        );
    }

    return lines.join('\n');
}
