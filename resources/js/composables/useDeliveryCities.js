import { ref } from 'vue';
import { getCities } from '@/api/delivery';

/**
 * Composable для загрузки списка городов доставки.
 * Используется в DeliveryPointModal и CourierDeliveryModal —
 * ранее логика загрузки дублировалась в обоих компонентах.
 *
 * @param {{defaultCity?: string}} [options]
 */
export function useDeliveryCities(options = {}) {
    const cities = ref([]);
    const citiesLoading = ref(false);
    const selectedCity = ref('');

    /**
     * Загрузить список городов и выбрать город по умолчанию.
     * Если указан defaultCity — выбирается он, иначе — Курск.
     */
    async function fetchCities() {
        citiesLoading.value = true;

        try {
            const list = await getCities({ country_codes: 'RU' });

            cities.value = list.map((city) => ({
                code: String(city.code ?? ''),
                name: city.city ?? '',
                lat: city.latitude != null ? Number(city.latitude) : null,
                lon: city.longitude != null ? Number(city.longitude) : null,
            }));

            const preferred = options.defaultCity ?? 'Курск';
            const found = cities.value.find((city) => city.name === preferred);
            selectedCity.value = found?.code || cities.value[0]?.code || '';
        } catch (error) {
            console.error(error);
            cities.value = [];
        } finally {
            citiesLoading.value = false;
        }
    }

    function getSelectedCityObject() {
        return cities.value.find((city) => city.code === selectedCity.value) || null;
    }

    return {
        cities,
        citiesLoading,
        selectedCity,
        fetchCities,
        getSelectedCityObject,
    };
}
