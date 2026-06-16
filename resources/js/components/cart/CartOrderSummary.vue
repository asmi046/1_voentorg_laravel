<template>
    <div class="itogo">
        <div class="itogo_price_count">
            <div class="itogo_row">
                <span class="text"
                    >Товары (<span>{{ count }}</span
                    >)</span
                >
                <span class="razd"></span>
                <span class="p_price rub price_formator"
                    >{{ Number(subtotal).toLocaleString("ru-RU") }}
                    <span class="rub_symbol">₽</span>
                </span>
            </div>

            <div v-if="deliveryPrice != 0" class="itogo_row">
                <span class="text">Доставка</span>
                <span class="razd"></span>
                <span class="p_price rub price_formator"
                    >{{ Number(deliveryPrice).toLocaleString("ru-RU") }}₽
                    <span class="rub_symbol">₽</span>
                </span>
            </div>

            <div v-if="promoApplied" class="itogo_row">
                <span class="text">Скидка по промокоду</span>
                <span class="razd"></span>
                <span class="p_price rub price_formator"
                    >-{{ Number(promoDiscount).toLocaleString("ru-RU") }}
                    <span class="rub_symbol">₽</span>
                </span>
            </div>

            <div class="itogo_row itogo_row_final">
                <span class="text">Итого</span>
                <span class="razd"></span>
                <span class="p_price rub price_formator"
                    >{{ Number(finalTotal).toLocaleString("ru-RU") }}
                    <span class="rub_symbol">₽</span>
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    count: {
        type: Number,
        required: true,
    },
    subtotal: {
        type: Number,
        required: true,
    },
    deliveryPrice: {
        type: Number,
        required: true,
    },
    promoApplied: {
        type: Boolean,
        required: true,
    },
    promoDiscount: {
        type: Number,
        required: true,
    },
    finalTotal: {
        type: Number,
        required: true,
    },
});
</script>

<style></style>
