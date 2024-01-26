<template>
    <div class="price">
        <select v-model="selected" name="prices" >
            <option  v-for="(item, index) in prices" :key="item.id" :value="index">{{item.volume}} {{item.ed_izm}}</option>
        </select>
        <span class="price__main">
            {{price}} руб.
        </span>



        <span v-show="oldprice != 0" class="price__old">
            {{oldprice}}  руб.
        </span>
    </div>

    <div class="lb_wrapper">
        <div class="like">
                <to-favorites-btn :sku="sku"></to-favorites-btn>
        </div>

        <div class="sale_btn">
            <to-bascet-btn-page :sku="sku" :skuid="id_sku" :bascet="'/bascet'"></to-bascet-btn-page>
        </div>
    </div>


</template>

<script>
import { ref, watch } from 'vue'
import ToFavoritesBtn from './ToFavoritesBtn.vue'
import ToBascetBtnPage from './ToBascetBtnPage.vue'

export default {
    components: { ToFavoritesBtn, ToBascetBtnPage },
    props:{
        prices:Array,
        sku:String,
    },

    setup(props){
        let selected = ref(0)
        let price = ref(props.prices[0].price)
        let oldprice = ref(props.prices[0].old_price)
        let id_sku = ref(props.prices[0].id)

        console.log(props.sku)

        watch(() => selected.value, function()  {
                price.value = props.prices[selected.value].price
                oldprice.value = props.prices[selected.value].old_price
                id_sku.value = props.prices[selected.value].id
            }
        )

        console.log(oldprice.value)

        return {
            prices:props.prices,
            selected,
            price,
            oldprice,
            sku:props.sku,
            id_sku
        }
    }
}
</script>

<style>

</style>
