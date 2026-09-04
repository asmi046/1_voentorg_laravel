<template>
    <p class="sku_in_page">
        Артикул: <span>{{ sku }}</span>
    </p>
    <div class="to_card_widget">
        <div class="price">
            <price-selector
                :prices="prices"
                :sku="sku"
                @select="onSelect"
            ></price-selector>

            <span class="price__main"> {{ price }} руб. </span>

            <span v-show="oldprice != 0" class="price__old">
                {{ oldprice }} руб.
            </span>
        </div>

        <div class="lb_wrapper">
            <div class="sale_btn">
                <to-bascet-btn-page
                    :sku="sku"
                    :skuid="id_sku"
                    :bascet="'/bascet'"
                ></to-bascet-btn-page>
            </div>

            <div class="like">
                <to-favorites-btn :sku="sku"></to-favorites-btn>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from "vue";
import ToFavoritesBtn from "./ToFavoritesBtn.vue";
import ToBascetBtnPage from "./ToBascetBtnPage.vue";
import PriceSelector from "./PriceSelector.vue";

export default {
    components: { ToFavoritesBtn, ToBascetBtnPage, PriceSelector },
    props: {
        prices: Array,
        sku: String,
    },

    setup(props) {
        let sku = ref(props.sku);
        let price = ref(props.prices[0].price);
        let oldprice = ref(props.prices[0].old_price);
        let id_sku = ref(props.prices[0].id);

        const onSelect = (index) => {
            price.value = props.prices[index].price;
            oldprice.value = props.prices[index].old_price;
            id_sku.value = props.prices[index].id;
            sku.value = props.prices[index].sku;
        };

        return {
            prices: props.prices,
            price,
            oldprice,
            sku,
            id_sku,
            onSelect,
        };
    },
};
</script>

<style></style>
