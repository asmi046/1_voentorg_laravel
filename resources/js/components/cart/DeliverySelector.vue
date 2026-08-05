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
    </div>
</template>

<script setup>
import { ref } from "vue";
import DeliveryOption from "./DeliveryOption.vue";
import DeliveryPointModal from "./DeliveryPointModal.vue";

defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

const pointModalVisible = ref(false);
const selectedPoint = ref(null);

const options = [
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

const selectOption = (option) => {
    emit("update:modelValue", option.type);

    if (option.type === "pickup_point") {
        pointModalVisible.value = true;
        return;
    }

    emit("change", { ...option, selected: true });
};

const onPointSelected = (point) => {
    selectedPoint.value = point;
    const option = options.find((item) => item.type === "pickup_point");

    emit("change", {
        ...option,
        selected: true,
        point,
    });
};
</script>

<style></style>
