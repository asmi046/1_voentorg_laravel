/**
 * Registry delivery-provider pin configurations for Yandex Maps.
 *
 * Each provider can define:
 * - `logoUrl` — image/SVG for the pin body
 * - `fallbackColor` — solid color used when no logo is configured
 * - `width` / `height` — icon size in px
 * - `offsetX` / `offsetY` — anchor offset in px (default centers the bottom-center)
 *
 * To add a new provider, just append a new entry here and drop its logo
 * into `public/images/icons/delivery-logos/`.
 */

const PIN_CONFIGS = {
    cdek: {
        logoUrl: '/images/icons/delivery-logos/cdek.svg',
        width: 32,
        height: 52,
        offsetX: -16,
        offsetY: -52,
    },
    boxberry: {
        logoUrl: '/images/icons/delivery-logos/boxberry.svg',
        width: 32,
        height: 52,
        offsetX: -16,
        offsetY: -52,
    },
    dpd: {
        logoUrl: '/images/icons/delivery-logos/dpd.svg',
        width: 32,
        height: 52,
        offsetX: -16,
        offsetY: -52,
    },
};

const DEFAULT_PIN = {
    fallbackColor: '#00B33C',
    width: 28,
    height: 44,
    offsetX: -14,
    offsetY: -44,
};

/**
 * Return Yandex Maps Placemark icon options for the given provider code.
 *
 * @param {string} provider
 * @returns {Object}
 */
export function getDeliveryPinOptions(provider = 'unknown') {
    const config = PIN_CONFIGS[provider] || DEFAULT_PIN;

    if (config.logoUrl) {
        return {
            iconLayout: 'default#image',
            iconImageHref: config.logoUrl,
            iconImageSize: [config.width, config.height],
            iconImageOffset: [config.offsetX, config.offsetY],
        };
    }

    return {
        preset: `islands#${config.fallbackColor.replace('#', '')}Icon`,
    };
}

/**
 * Register or override pin config for a delivery provider.
 *
 * @param {string} provider
 * @param {Object} config
 */
export function registerDeliveryPin(provider, config) {
    PIN_CONFIGS[provider] = {
        ...DEFAULT_PIN,
        ...PIN_CONFIGS[provider],
        ...config,
    };
}
