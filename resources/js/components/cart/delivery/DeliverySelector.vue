<template>
    <div class="delivery_selector">
        <h3 class="delivery_selector__title">Способ доставки</h3>

        <div class="delivery_selector__list">
            <DeliveryOption
                v-for="option in options"
                :key="option.type"
                :option="option"
                :selected="modelValue === option.type"
                @select="selectOption(option)"
            />
        </div>

        <DeliveryPointModal
            v-model="pointModalVisible"
            @select="onPointSelected"
        />

        <CourierDeliveryModal
            v-model="courierModalVisible"
            :parcel-weight-grams="parcelWeightGrams"
            @select="onCourierSelected"
        />
    </div>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import DeliveryOption from "./DeliveryOption.vue";
import CourierDeliveryModal from "./CourierDeliveryModal.vue";
import DeliveryPointModal from "./DeliveryPointModal.vue";
import * as deliveryApi from "@/api/delivery";
import { toNumber, formatDeliveryDateRange } from "@/composables/useDeliveryFormatters";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    parcelWeightGrams: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

const pointModalVisible = ref(false);
const courierModalVisible = ref(false);
const selectedPoint = ref(null);
const selectedCity = ref(null);
const selectedCourier = ref(null);
const pickupPointTariff = ref(null);
const pickupPointTariffRequestId = ref(0);

const BASE_OPTIONS = [
    {
        type: "pickup",
        title: "Самовывоз",
        description: "Из нашего магазина",
        icon: "delivery_pickup",
        price: 0,
    },
    {
        type: "pickup_point",
        title: "Пункт выдачи",
        description: "Доставка в пункт выдачи СДЭК",
        icon: "delivery_pickup_point",
        price: null,
    },
    {
        type: "courier",
        title: "Курьер",
        description: "Доставка курьером до двери",
        icon: "delivery_courier",
        price: null,
    },
];

const getNumericPrice = toNumber;

const resolvePickupPointPrice = (point) => {
    const rawTariff = pickupPointTariff.value?.raw || pickupPointTariff.value;

    if (rawTariff?.delivery_sum !== undefined) {
        const tariffPrice = getNumericPrice(rawTariff.delivery_sum);

        if (tariffPrice !== null) {
            return tariffPrice;
        }
    }

    if (!point) {
        return null;
    }

    const candidates = [
        point.delivery_price,
        point.deliveryPrice,
        point.price,
        point.tariff_price,
        point.tariffPrice,
        point.tariff?.delivery_sum,
        point.tariff?.total_sum,
    ];

    for (const candidate of candidates) {
        const price = getNumericPrice(candidate);

        if (price !== null) {
            return price;
        }
    }

    return null;
};

const calculateDeliveryPriceStub = async (deliveryType, city = null) => {
    if (deliveryType === "pickup") {
        return 0;
    }

    if (deliveryType === "pickup_point") {
        if (!city?.code) {
            pickupPointTariff.value = null;
            return null;
        }

        const requestId = ++pickupPointTariffRequestId.value;

        try {
            const data = await deliveryApi.getCourierOffers({
                to_code: city.code,
                weight: props.parcelWeightGrams,
                delivery_mode: 4,
            });

            if (requestId !== pickupPointTariffRequestId.value) {
                return null;
            }

            pickupPointTariff.value = data?.best?.raw || null;

            return resolvePickupPointPrice(null);
        } catch (error) {
            if (requestId === pickupPointTariffRequestId.value) {
                pickupPointTariff.value = null;
            }

            console.error(error);

            return null;
        }
    }

    return null;
};

const pickupPointDescription = computed(() => {
    if (!selectedPoint.value || !selectedCity.value) {
        return "Доставка в пункт выдачи СДЭК";
    }

    const cityName = selectedCity.value.name || "";
    const pointText =
        selectedPoint.value.address ||
        selectedPoint.value.name ||
        selectedPoint.value.code ||
        "";

    if (!cityName || !pointText) {
        return "Доставка в пункт выдачи СДЭК";
    }

    const lines = [`Доставка в пункт выдачи СДЭК - ${cityName}, ${pointText}`];
    const deliveryDateText = formatDeliveryDateRange(
        pickupPointTariff.value?.delivery_date_range ||
            pickupPointTariff.value?.raw?.delivery_date_range ||
            null,
    );

    if (deliveryDateText) {
        lines.push(deliveryDateText);
    }

    return lines.join("\n");
});

const courierDescription = computed(() => {
    if (!selectedCourier.value?.city || !selectedCourier.value?.address) {
        return "Доставка курьером до двери";
    }

    const lines = [
        `Доставка курьером СДЭК - ${selectedCourier.value.city.name}, ${selectedCourier.value.address}`,
    ];

    if (selectedCourier.value.apartment) {
        lines[0] += `, кв. ${selectedCourier.value.apartment}`;
    }

    const deliveryDateText = formatDeliveryDateRange(
        selectedCourier.value.tariff?.delivery_date_range ||
            selectedCourier.value.tariff?.raw?.delivery_date_range ||
            null,
    );

    if (deliveryDateText) {
        lines.push(deliveryDateText);
    }

    return lines.join("\n");
});

const options = computed(() => {
    return BASE_OPTIONS.map((option) => {
        const isPickupPoint = option.type === "pickup_point";
        const isCourier = option.type === "courier";

        return {
            ...option,
            description: isPickupPoint
                ? pickupPointDescription.value
                : isCourier
                  ? courierDescription.value
                  : option.description,
            price:
                option.type === "pickup"
                    ? 0
                    : isPickupPoint
                      ? resolvePickupPointPrice(selectedPoint.value)
                      : getNumericPrice(selectedCourier.value?.deliveryPrice),
        };
    });
});

const buildDeliveryPayload = (option, overrides = {}) => {
    const point = overrides.selectedPoint ?? selectedPoint.value;
    const city = overrides.selectedCity ?? selectedCity.value;
    const courier = overrides.selectedCourier ?? selectedCourier.value;
    const optionPrice =
        overrides.deliveryPrice !== undefined
            ? overrides.deliveryPrice
            : option.price;

    const transportCompany = option.type === "pickup" ? null : "СДЭК";

    return {
        deliveryMethod: option.title,
        transportCompany,
        deliveryType: option.type,
        selectedPickupPoint: option.type === "pickup_point" ? point : null,
        selectedCity:
            option.type === "pickup_point"
                ? city
                : option.type === "courier"
                  ? courier?.city || null
                  : null,
        deliveryAddress:
            option.type === "courier" ? courier?.address || "" : "",
        apartment: option.type === "courier" ? courier?.apartment || "" : "",
        deliveryPrice: getNumericPrice(optionPrice),
        deliveryDateRange:
            option.type === "pickup_point"
                ? pickupPointTariff.value?.delivery_date_range ||
                  pickupPointTariff.value?.raw?.delivery_date_range ||
                  null
                : option.type === "courier"
                  ? selectedCourier.value?.tariff?.delivery_date_range ||
                    selectedCourier.value?.tariff?.raw?.delivery_date_range ||
                    null
                  : null,
        tariff:
            option.type === "courier"
                ? selectedCourier.value?.tariff || null
                : null,
    };
};

const selectOption = (option) => {
    emit("update:modelValue", option.type);

    if (option.type === "pickup_point") {
        selectedCourier.value = null;
        emit(
            "change",
            buildDeliveryPayload(option, {
                selectedPoint: selectedPoint.value,
                selectedCity: selectedCity.value,
                selectedCourier: null,
            }),
        );
        pointModalVisible.value = true;
        return;
    }

    if (option.type === "courier") {
        selectedPoint.value = null;
        selectedCity.value = null;
        pickupPointTariff.value = null;

        emit(
            "change",
            buildDeliveryPayload(option, {
                selectedPoint: null,
                selectedCity: null,
                selectedCourier: selectedCourier.value,
                deliveryPrice: selectedCourier.value?.deliveryPrice,
            }),
        );

        courierModalVisible.value = true;
        return;
    }

    selectedPoint.value = null;
    selectedCity.value = null;
    selectedCourier.value = null;
    pickupPointTariff.value = null;

    emit(
        "change",
        buildDeliveryPayload(option, {
            selectedPoint: null,
            selectedCity: null,
            selectedCourier: null,
        }),
    );
};

const onPointSelected = (payload) => {
    selectedPoint.value = payload?.point || payload || null;
    selectedCity.value = payload?.city || null;

    const option = options.value.find((item) => item.type === "pickup_point");

    if (!option) {
        return;
    }

    emit("update:modelValue", "pickup_point");

    emit(
        "change",
        buildDeliveryPayload(option, {
            selectedPoint: selectedPoint.value,
            selectedCity: selectedCity.value,
            selectedCourier: null,
        }),
    );
};

const onCourierSelected = (payload) => {
    selectedCourier.value = {
        city: payload?.city || null,
        address: payload?.address || "",
        apartment: payload?.apartment || "",
        deliveryPrice: payload?.deliveryPrice ?? null,
        tariff: payload?.tariff || null,
    };

    const option = options.value.find((item) => item.type === "courier");

    if (!option) {
        return;
    }

    emit("update:modelValue", "courier");

    emit(
        "change",
        buildDeliveryPayload(option, {
            selectedPoint: null,
            selectedCity: null,
            selectedCourier: selectedCourier.value,
            deliveryPrice: selectedCourier.value.deliveryPrice,
        }),
    );
};

const emitSelectedOptionChange = () => {
    const option = options.value.find((item) => item.type === props.modelValue);

    if (!option) {
        return;
    }

    emit(
        "change",
        buildDeliveryPayload(option, {
            selectedPoint:
                option.type === "pickup_point" ? selectedPoint.value : null,
            selectedCity:
                option.type === "pickup_point" ? selectedCity.value : null,
            selectedCourier:
                option.type === "courier" ? selectedCourier.value : null,
            deliveryPrice:
                option.type === "courier"
                    ? selectedCourier.value?.deliveryPrice
                    : undefined,
        }),
    );
};

watch(
    () => [selectedCity.value?.code || "", props.parcelWeightGrams],
    async ([cityCode]) => {
        if (!cityCode) {
            pickupPointTariff.value = null;
            if (props.modelValue === "pickup_point") {
                emitSelectedOptionChange();
            }
            return;
        }

        await calculateDeliveryPriceStub("pickup_point", selectedCity.value);

        if (props.modelValue === "pickup_point") {
            emitSelectedOptionChange();
        }
    },
    { immediate: true },
);

watch(
    () => props.parcelWeightGrams,
    () => {
        if (props.modelValue === "courier") {
            emitSelectedOptionChange();
        }
    },
);
</script>

<style></style>
