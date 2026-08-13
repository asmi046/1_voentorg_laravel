<template>
    <Teleport to="body">
        <div
            v-if="modelValue"
            class="delivery_point_modal_overlay"
            @click.self="close"
        >
            <div class="delivery_point_modal" role="dialog" aria-modal="true">
                <div class="delivery_point_modal__header">
                    <h3 class="delivery_point_modal__title">
                        Выбор пункта выдачи
                    </h3>
                    <button
                        type="button"
                        class="delivery_point_modal__close"
                        aria-label="Закрыть"
                        @click="close"
                    >
                        <svg viewBox="0 0 24 24">
                            <path
                                d="M6 6l12 12M18 6 6 18"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </button>
                </div>

                <div class="delivery_point_modal__city">
                    <SearchableCombobox
                        v-model="selectedCity"
                        :items="cities"
                        :loading="citiesLoading"
                        placeholder="Начните вводить город"
                        loading-placeholder="Загрузка городов..."
                        empty-text="Ничего не найдено"
                        @change="onCityChange"
                    />
                </div>

                <div class="delivery_point_modal__body">
                    <div class="delivery_map">
                        <div
                            ref="mapContainer"
                            class="delivery_map__container"
                        ></div>

                        <div
                            v-if="mapState !== 'ready'"
                            class="delivery_map__placeholder"
                        >
                            <span
                                v-if="mapState === 'loading'"
                                class="delivery_map__spinner"
                            ></span>
                            <svg
                                v-else
                                class="delivery_map__icon"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11Z"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                />
                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2.5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                />
                            </svg>
                            <span>{{
                                mapState === "error"
                                    ? "Не удалось загрузить карту"
                                    : "Загружаем карту..."
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";
import SearchableCombobox from "../shared/SearchableCombobox.vue";
import * as deliveryApi from "@/api/delivery";
import { useDeliveryCities } from "@/composables/useDeliveryCities";
import { useYandexMap } from "@/composables/useYandexMap";
import { getDeliveryPinOptions } from "@/composables/useDeliveryPins";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "select"]);

const {
    cities,
    citiesLoading,
    selectedCity,
    fetchCities: fetchCitiesList,
    getSelectedCityObject,
} = useDeliveryCities();

const { loadYandexMaps } = useYandexMap();

const pickupPoints = ref([]);
const selectedPoint = ref(null);
const mapContainer = ref(null);
const mapState = ref("loading");

let mapInstance = null;

const DELIVERY_POINT_TYPES = "PVZ,POSTAMAT";

const FALLBACK_CENTER = [51.7303, 36.1926]; // Курск

const getCityCenter = () => {
    const city = cities.value.find((item) => item.code === selectedCity.value);

    if (city && city.lat != null && city.lon != null) {
        return [city.lat, city.lon];
    }

    return FALLBACK_CENTER;
};

const fetchCities = async () => {
    await fetchCitiesList();

    if (selectedCity.value) {
        await fetchPickupPoints(selectedCity.value);
    }
};

const extractPoints = (payload) => {
    if (Array.isArray(payload)) {
        return payload;
    }

    if (payload && Array.isArray(payload.points)) {
        return payload.points;
    }

    if (payload && Array.isArray(payload.delivery_points)) {
        return payload.delivery_points;
    }

    return [];
};

const mapPoint = (point) => {
    const raw = point?.raw ?? point ?? {};
    const phones = Array.isArray(raw.phones)
        ? raw.phones
              .map((phone) => phone?.number)
              .filter((value) => value !== undefined && value !== "")
        : [];

    const lat = raw.location?.latitude ?? raw.location?.lat ?? null;
    const lon = raw.location?.longitude ?? raw.location?.lon ?? null;

    return {
        code: String(point?.id ?? raw.code ?? ""),
        name: raw.name ?? point?.label ?? "",
        address:
            raw.location?.address_full ||
            raw.full_address ||
            raw.location?.address ||
            raw.address ||
            point?.label ||
            "",
        addressComment: raw.address_comment ?? "",
        workTime: raw.work_time ?? "",
        phones,
        provider: point?.provider ?? raw.provider ?? "unknown",
        lat: lat != null ? Number(lat) : null,
        lon: lon != null ? Number(lon) : null,
    };
};

const fetchPickupPoints = async (city) => {
    mapState.value = "loading";

    try {
        const points = await deliveryApi.getPickupPoints({
            city_code: city,
            type_code: DELIVERY_POINT_TYPES,
        });

        pickupPoints.value = extractPoints(points).map(mapPoint);
    } catch (error) {
        console.error(error);
        pickupPoints.value = [];
    }

    await initMap();
};

const buildBalloonContent = (point) => {
    const parts = [
        `<div class="delivery_balloon__address">${point.address}</div>`,
    ];

    if (point.addressComment) {
        parts.push(
            `<div class="delivery_balloon__comment">${point.addressComment}</div>`,
        );
    }

    if (point.workTime) {
        parts.push(
            `<div class="delivery_balloon__row"><span class="delivery_balloon__label">Время работы:</span> ${point.workTime}</div>`,
        );
    }

    if (point.phones.length) {
        parts.push(
            `<div class="delivery_balloon__row"><span class="delivery_balloon__label">Телефон:</span> ${point.phones.join(", ")}</div>`,
        );
    }

    parts.push(
        `<button type="button" class="delivery_balloon__btn" data-point-code="${point.code}">Выбрать</button>`,
    );

    return `<div class="delivery_balloon">${parts.join("")}</div>`;
};

const bindBalloonSelect = () => {
    if (!mapInstance) {
        return;
    }

    mapInstance.balloon.events.add("open", () => {
        requestAnimationFrame(() => {
            const button = document.querySelector(".delivery_balloon__btn");

            if (!button) {
                return;
            }

            const code = button.getAttribute("data-point-code");
            const point = pickupPoints.value.find((item) => item.code === code);

            if (!point) {
                return;
            }

            button.onclick = () => selectPoint(point);
        });
    });
};

const addPlacemarks = () => {
    if (!mapInstance) {
        return;
    }

    mapInstance.geoObjects.removeAll();

    for (const point of pickupPoints.value) {
        if (point.lat == null || point.lon == null) {
            continue;
        }

        const pinOptions = getDeliveryPinOptions(point.provider);
        console.log(pinOptions);
        const placemark = new window.ymaps.Placemark(
            [point.lat, point.lon],
            {
                hintContent: point.name || point.address,
                balloonContent: buildBalloonContent(point),
            },
            pinOptions,
        );

        mapInstance.geoObjects.add(placemark);
    }
};

const initMap = async () => {
    mapState.value = "loading";

    try {
        await loadYandexMaps();

        if (!mapContainer.value) {
            return;
        }

        if (!mapInstance) {
            mapInstance = new window.ymaps.Map(mapContainer.value, {
                center: getCityCenter(),
                zoom: 12,
                controls: ["zoomControl"],
            });
            bindBalloonSelect();
        } else {
            mapInstance.setCenter(getCityCenter());
        }

        addPlacemarks();

        if (mapInstance.container?.fitToContainer) {
            mapInstance.container.fitToContainer();
        }

        mapState.value = "ready";
    } catch (error) {
        console.error(error);
        mapState.value = "error";
    }
};

const onCityChange = () => {
    if (!selectedCity.value) {
        return;
    }

    selectedPoint.value = null;
    fetchPickupPoints(selectedCity.value);
};

const selectPoint = (point) => {
    selectedPoint.value = point;
    emit("select", {
        point,
        city: getSelectedCityObject(),
    });
    close();
};

const close = () => {
    emit("update:modelValue", false);
};

const onKeydown = (event) => {
    if (event.key === "Escape") {
        close();
    }
};

const destroyMap = () => {
    if (mapInstance) {
        mapInstance.destroy();
        mapInstance = null;
    }

    mapState.value = "loading";
};

onMounted(() => {
    window.addEventListener("keydown", onKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener("keydown", onKeydown);
    destroyMap();
});

watch(
    () => props.modelValue,
    async (visible) => {
        if (!visible) {
            destroyMap();
            return;
        }

        await nextTick();

        if (cities.value.length) {
            fetchPickupPoints(selectedCity.value);
        } else {
            fetchCities();
        }
    },
);
</script>
