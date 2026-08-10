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
import SearchableCombobox from "./SearchableCombobox.vue";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "select"]);

const cities = ref([]);
const citiesLoading = ref(false);
const selectedCity = ref("");
const pickupPoints = ref([]);
const selectedPoint = ref(null);
const mapContainer = ref(null);
const mapState = ref("loading");

let mapInstance = null;

const YANDEX_MAP_API_KEY = ""; // TODO: вставить API-ключ Яндекс.Карт

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
    citiesLoading.value = true;

    try {
        const response = await axios.get("/cdek/cities", {
            params: { country_codes: "RU" },
        });

        const list = Array.isArray(response.data?.data)
            ? response.data.data
            : [];

        cities.value = list.map((city) => ({
            code: String(city.code ?? ""),
            name: city.city ?? "",
            lat: city.latitude != null ? Number(city.latitude) : null,
            lon: city.longitude != null ? Number(city.longitude) : null,
        }));

        const kursk = cities.value.find((city) => city.name === "Курск");
        selectedCity.value = kursk?.code || cities.value[0]?.code || "";
    } catch (error) {
        console.error(error);
    } finally {
        citiesLoading.value = false;
    }

    if (selectedCity.value) {
        await fetchPickupPoints(selectedCity.value);
    }
};

const extractPoints = (payload) => {
    if (Array.isArray(payload)) {
        return payload;
    }

    if (payload && Array.isArray(payload.delivery_points)) {
        return payload.delivery_points;
    }

    return [];
};

const mapPoint = (point) => {
    const phones = Array.isArray(point.phones)
        ? point.phones
              .map((phone) => phone?.number)
              .filter((value) => value !== undefined && value !== "")
        : [];

    const lat = point.location?.latitude ?? point.location?.lat ?? null;
    const lon = point.location?.longitude ?? point.location?.lon ?? null;

    return {
        code: String(point.code ?? ""),
        name: point.name ?? "",
        address:
            point.location?.address_full ||
            point.full_address ||
            point.location?.address ||
            point.address ||
            "",
        addressComment: point.address_comment ?? "",
        workTime: point.work_time ?? "",
        phones,
        lat: lat != null ? Number(lat) : null,
        lon: lon != null ? Number(lon) : null,
    };
};

const fetchPickupPoints = async (city) => {
    mapState.value = "loading";

    try {
        const response = await axios.get("/cdek/delivery-points", {
            params: { city_code: city, type_code: DELIVERY_POINT_TYPES },
        });

        pickupPoints.value = extractPoints(response.data?.data).map(mapPoint);
    } catch (error) {
        console.error(error);
        pickupPoints.value = [];
    }

    await initMap();
};

let yandexApiPromise = null;

const loadYandexMaps = () => {
    if (window.ymaps) {
        return Promise.resolve();
    }

    if (yandexApiPromise) {
        return yandexApiPromise;
    }

    // const scriptSrc = `https://api-maps.yandex.ru/2.1/?lang=ru_RU${
    //     YANDEX_MAP_API_KEY ? `&apikey=${YANDEX_MAP_API_KEY}` : ""
    // }`;

    const scriptSrc = "//api-maps.yandex.ru/2.1/?lang=ru_RU";

    yandexApiPromise = new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.src = scriptSrc;
        script.async = true;
        script.onload = () => window.ymaps.ready(() => resolve());
        script.onerror = () => {
            yandexApiPromise = null;
            reject(new Error("Не удалось загрузить Яндекс.Карты"));
        };
        document.head.appendChild(script);
    });

    return yandexApiPromise;
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

        const placemark = new window.ymaps.Placemark(
            [point.lat, point.lon],
            {
                hintContent: point.name || point.address,
                balloonContent: buildBalloonContent(point),
            },
            { preset: "islands#greenIcon" },
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

const getSelectedCity = () => {
    return (
        cities.value.find((city) => city.code === selectedCity.value) || null
    );
};

const selectPoint = (point) => {
    selectedPoint.value = point;
    emit("select", {
        point,
        city: getSelectedCity(),
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
