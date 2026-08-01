<template>
    <div
        class="delivery_option"
        :class="{ active: selected }"
        @click="$emit('select')"
    >
        <span class="delivery_option__icon">
            <svg class="cart_icon">
                <use :xlink:href="`#${option.icon}`"></use>
            </svg>
        </span>

        <span class="delivery_option__body">
            <span class="delivery_option__title">{{ option.title }}</span>
            <span v-if="option.description" class="delivery_option__desc">
                {{ option.description }}
            </span>
        </span>

        <span class="delivery_option__price">{{ priceText }}</span>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    option: {
        type: Object,
        required: true,
    },
    selected: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["select"]);

const priceText = computed(() => {
    const price = props.option.price;

    if (price === null || price === undefined || price === "") {
        return "—";
    }

    const numericPrice = Number(price);

    if (numericPrice === 0) {
        return "Бесплатно";
    }

    return `${numericPrice.toLocaleString("ru-RU")} ₽`;
});
</script>

<style></style>
