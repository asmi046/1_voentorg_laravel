<template>
    <a :href="'/product/'+tovar.slug" class="prod_card d-flex">

        <div class="img_wrapper">
            <img v-if="tovar.img != ''" loading="lazy" :src="tovar.img" :alt="tovar.title">
            <img v-else loading="lazy" :src="'img/no_photo.jpg'" :alt="tovar.title">
        </div>

        <div class="text_wrapper">
            <h4>{{tovar.title}}</h4>
            <span class="spacer__vendor">Артикул: {{tovar.sku}}</span>
            <div class="prod-card__price d-flex">
                <p v-if="tovar.product_prices[0].old_price != 0" class='prod-card__price-old'> {{ tovar.product_prices[0].old_price }} руб.</p>
                <p class="prod-card__price-new">{{tovar.product_prices[0].price}} руб.</p>
            </div>
        </div>

        <div class="prod-card__button d-flex">
            <span class="prod-card__order d-flex">Подробнее</span>
        </div>
    </a>
</template>

<script>

export default {
    components: {

    },

    props:{
        tovar:Object
    },

    setup(props) {
        console.log(props.tovar)
        return {
            tovar:props.tovar
        }
    }
}
</script>

<style>

</style>
