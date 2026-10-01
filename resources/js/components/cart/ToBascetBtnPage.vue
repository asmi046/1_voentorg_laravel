<template>
    <span
        v-if="disabled"
        class="button fill_btn button--disabled"
        aria-disabled="true"
        >Нет в наличии</span
    >
    <a
        v-else-if="!inBascet"
        href="#"
        @click.prevent="addToBascet"
        class="button fill_btn"
        >Добавить в корзину</a
    >
    <a v-else :href="bascet" class="button fill_btn"
        >Оформить {{ inBascetCount }} шт.</a
    >
</template>

<script>
import { ref } from "vue";
import { useStore } from "vuex";
import * as cartApi from "@/api/cart";

export default {
    props: {
        sku: String,
        skuid: Number,
        bascet: String,
        disabled: {
            type: Boolean,
            default: false,
        },
    },

    setup(props) {
        const store = useStore();

        let inBascet = ref(false);
        let inBascetCount = ref(0);

        const refreshFromStore = () => {
            const inBascetElem = (store.state.cart_tovars || []).find(
                (elem) => elem.product_sku === props.sku,
            );
            inBascet.value = inBascetElem != null;
            inBascetCount.value =
                inBascetElem != null ? Number(inBascetElem.quantity) || 0 : 0;
        };

        const addToBascet = () => {
            if (props.disabled) {
                return;
            }
            cartApi
                .addToCart({
                    product_sku: props.sku,
                    quantity: 1,
                })
                .then(() => {
                    store.dispatch("initialBascet").then(refreshFromStore);
                })
                .catch((error) => console.log(error));
        };

        return {
            inBascet,
            inBascetCount,
            addToBascet,
            bascet: props.bascet,
        };
    },
};
</script>

<style></style>