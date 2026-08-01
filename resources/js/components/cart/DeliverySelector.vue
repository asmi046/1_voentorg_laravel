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
    </div>
</template>

<script setup>
import DeliveryOption from "./DeliveryOption.vue";

defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

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
    emit("change", { ...option, selected: true });
};
</script>

<style></style>
