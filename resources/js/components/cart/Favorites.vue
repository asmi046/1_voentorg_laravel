<template>

<div class="product_card_wrapper c_4">

    <tovar-card v-for="element in elements" :key="element.id" :tovar="element.tovar_data" ></tovar-card>

    <div v-if="is_empty == 0" class="empty_favorites">Жмите на ♡ на странице товара и добавляйте товар в избранное</div>
</div>

</template>

<script>
import { computed, ref } from 'vue'
import { useStore } from 'vuex'
import TovarCard from './TovarCard.vue'
export default {
    components: { TovarCard },
    setup() {

        const store = useStore()

        return {
            is_empty: computed(() => store.state.favorites_count),
            elements: computed(() => store.state.favorites_tovars),

        }
    }

}
</script>

<style>

</style>
