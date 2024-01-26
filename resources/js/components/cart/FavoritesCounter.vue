<template>
    <div class="fb_counter">{{count}}</div>
</template>

<script>

import { useStore } from 'vuex'
import { computed } from 'vue'

export default {

    setup() {
        const store = useStore()

        return {
            count:computed(() => store.getters.favoritesCount)
        }
    }
}
</script>

<style>

</style>
