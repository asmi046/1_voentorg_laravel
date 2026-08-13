<template>
    <Teleport to="body">
        <div
            v-if="modelValue"
            class="courier_delivery_modal_overlay"
            @click.self="close"
        >
            <div class="courier_delivery_modal" role="dialog" aria-modal="true">
                <div class="courier_delivery_modal__header">
                    <h3 class="courier_delivery_modal__title">
                        Доставка курьером
                    </h3>
                    <button
                        type="button"
                        class="courier_delivery_modal__close"
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

                <div class="courier_delivery_modal__body">
                    <div class="courier_delivery_modal__field">
                        <label class="courier_delivery_modal__label"
                            >Город</label
                        >
                        <SearchableCombobox
                            v-model="selectedCity"
                            :items="cities"
                            :loading="citiesLoading"
                            placeholder="Начните вводить город"
                            loading-placeholder="Загрузка городов..."
                            empty-text="Ничего не найдено"
                        />
                    </div>

                    <div class="courier_delivery_modal__field">
                        <label class="courier_delivery_modal__label"
                            >Адрес доставки</label
                        >
                        <input
                            v-model.trim="deliveryAddress"
                            type="text"
                            class="courier_delivery_modal__input"
                            placeholder="Улица, дом"
                        />
                    </div>

                    <div class="courier_delivery_modal__field">
                        <label class="courier_delivery_modal__label"
                            >Квартира</label
                        >
                        <input
                            v-model.trim="apartment"
                            type="text"
                            class="courier_delivery_modal__input"
                            placeholder="Например, 25"
                        />
                    </div>

                    <div class="courier_delivery_modal__price">
                        <span>Стоимость доставки:</span>
                        <strong>{{ formattedDeliveryPrice }}</strong>
                    </div>

                    <p
                        v-if="deliveryDateText"
                        class="courier_delivery_modal__date"
                    >
                        {{ deliveryDateText }}
                    </p>

                    <p v-if="tariffError" class="courier_delivery_modal__error">
                        {{ tariffError }}
                    </p>
                </div>

                <div class="courier_delivery_modal__footer">
                    <button
                        type="button"
                        class="button courier_delivery_modal__submit"
                        :disabled="!canSelect"
                        @click="submit"
                    >
                        {{ tariffLoading ? "Расчет..." : "Выбрать" }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import SearchableCombobox from "./SearchableCombobox.vue";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    parcelWeightGrams: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(["update:modelValue", "select"]);

const cities = ref([]);
const citiesLoading = ref(false);
const selectedCity = ref("");
const deliveryAddress = ref("");
const apartment = ref("");
const deliveryPrice = ref(0);
const selectedTariff = ref(null);
const tariffLoading = ref(false);
const tariffError = ref("");
let recalcTimer = null;
let requestId = 0;

const hasRequiredFields = computed(
    () => !!selectedCity.value && !!deliveryAddress.value.trim(),
);

const canSelect = computed(
    () =>
        hasRequiredFields.value &&
        !tariffLoading.value &&
        selectedTariff.value !== null,
);

const formattedDeliveryPrice = computed(() => {
    const numericPrice = Number(deliveryPrice.value);

    if (!Number.isFinite(numericPrice) || numericPrice <= 0) {
        return "0 ₽";
    }

    return `${numericPrice.toLocaleString("ru-RU")} ₽`;
});

const deliveryDateText = computed(() => {
    const range = selectedTariff.value?.delivery_date_range;

    if (!range?.min || !range?.max) {
        return "";
    }

    return `Срок доставки: ${range.min} - ${range.max}`;
});

const getSelectedCityObject = () => {
    return (
        cities.value.find((city) => city.code === selectedCity.value) || null
    );
};

const clearTariffResult = () => {
    selectedTariff.value = null;
    deliveryPrice.value = 0;
    tariffError.value = "";
};

const fetchCities = async () => {
    citiesLoading.value = true;

    try {
        const response = await axios.get("/delivery/cities", {
            params: { country_codes: "RU" },
        });

        const list = Array.isArray(response.data?.data)
            ? response.data.data
            : [];

        cities.value = list.map((city) => ({
            code: String(city.code ?? ""),
            name: city.city ?? "",
        }));

        const kursk = cities.value.find((city) => city.name === "Курск");
        selectedCity.value = kursk?.code || cities.value[0]?.code || "";
    } catch (error) {
        console.error(error);
    } finally {
        citiesLoading.value = false;
    }
};

const calculateCourierTariff = async () => {
    if (!hasRequiredFields.value) {
        clearTariffResult();
        return;
    }

    const localRequestId = ++requestId;
    tariffLoading.value = true;
    tariffError.value = "";

    try {
        const response = await axios.post("/delivery/courier-offers", {
            to_code: selectedCity.value,
            weight: props.parcelWeightGrams,
            delivery_mode: 3,
        });

        if (localRequestId !== requestId) {
            return;
        }

        const offer = response.data?.data?.best ?? null;
        selectedTariff.value = offer?.raw || null;
        const sum = Number(offer?.price ?? offer?.raw?.delivery_sum ?? 0);
        deliveryPrice.value = Number.isFinite(sum) ? sum : 0;
    } catch (error) {
        if (localRequestId !== requestId) {
            return;
        }

        clearTariffResult();
        tariffError.value = "Не удалось рассчитать стоимость доставки";
        console.error(error);
    } finally {
        if (localRequestId === requestId) {
            tariffLoading.value = false;
        }
    }
};

const scheduleRecalculation = () => {
    if (recalcTimer) {
        clearTimeout(recalcTimer);
    }

    recalcTimer = setTimeout(() => {
        calculateCourierTariff();
    }, 300);
};

const submit = () => {
    if (!canSelect.value) {
        return;
    }

    emit("select", {
        city: getSelectedCityObject(),
        address: deliveryAddress.value.trim(),
        apartment: apartment.value.trim(),
        deliveryPrice: deliveryPrice.value,
        tariff: selectedTariff.value,
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

watch(
    () => props.modelValue,
    async (visible) => {
        if (!visible) {
            return;
        }

        if (!cities.value.length) {
            await fetchCities();
        }

        scheduleRecalculation();
    },
);

watch(
    () => [selectedCity.value, deliveryAddress.value, props.parcelWeightGrams],
    () => {
        if (!props.modelValue) {
            return;
        }

        if (!hasRequiredFields.value) {
            clearTariffResult();
            return;
        }

        scheduleRecalculation();
    },
);

onMounted(() => {
    window.addEventListener("keydown", onKeydown);
});

onBeforeUnmount(() => {
    if (recalcTimer) {
        clearTimeout(recalcTimer);
    }

    window.removeEventListener("keydown", onKeydown);
});
</script>

<style></style>
