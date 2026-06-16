<template>
    <div class="control">
        <a
            @click.prevent="$emit('clear-bascet')"
            class="clear_bascet_btn"
            href="#"
        >
            <svg class="cart_icon cart_icon_loader">
                <use xlink:href="#cart_clear"></use>
            </svg>

            <span>Очистить корзину</span>
        </a>
    </div>

    <div class="tovar_list">
        <CartItemRow
            v-for="(item, index) in bascetList"
            :key="item.product_id"
            :item="item"
            :index="index"
            :no-photo-url="noPhotoUrl"
            @change-item-quantity="
                (rowItem, delta) =>
                    $emit('change-item-quantity', rowItem, delta)
            "
            @delete-element="
                (rowItem, rowIndex) =>
                    $emit('delete-element', rowItem, rowIndex)
            "
        />
    </div>
</template>

<script setup>
import CartItemRow from "./CartItemRow.vue";

defineProps({
    bascetList: {
        type: Array,
        required: true,
    },
    noPhotoUrl: {
        type: String,
        required: true,
    },
});

defineEmits(["clear-bascet", "change-item-quantity", "delete-element"]);
</script>

<style></style>
