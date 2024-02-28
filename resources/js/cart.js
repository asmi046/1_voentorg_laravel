import {createApp} from 'vue/dist/vue.esm-bundler';

import { store } from "./storage"
import { useStore } from 'vuex'

import { VMaskDirective } from 'v-slim-mask'

import axios from 'axios'
import VueAxios from 'vue-axios'

import Cart from "./components/cart/Cart.vue"
import PageToCart from './components/cart/PageToCart.vue'
import CartCounter from './components/cart/CartCounter.vue'
import FavoritesCounter from './components/cart/FavoritesCounter.vue'
import Favorites from './components/cart/Favorites.vue'

const cart_app = createApp({
    components:{
        Cart,
        PageToCart
    },

    setup() {

        const store = useStore()

        store.dispatch('initialBascet');
        store.dispatch('initialFavorites');

        return {
        };
    },
})


cart_app.use(VueAxios, axios)
cart_app.use(store)
cart_app.directive('mask', VMaskDirective)
cart_app.mount("#cart_app")

const counter_app = createApp({
    components:{
        CartCounter
    },

    setup() {

        const store = useStore()
        store.dispatch('initialBascet');
        store.dispatch('initialFavorites');

    },
})

counter_app.use(store)
counter_app.mount("#counter_app")

const panel_app = createApp({
    components:{
        FavoritesCounter
    },

    setup() {

        const store = useStore()
        store.dispatch('initialBascet');
        store.dispatch('initialFavorites');

    },
})

panel_app.use(store)
panel_app.mount("#panel_app")

const favorites_app = createApp({
    components:{
        Favorites
    },

    setup() {

        const store = useStore()
        store.dispatch('initialBascet');
        store.dispatch('initialFavorites');
    },
})

favorites_app.use(store)
favorites_app.mount("#favorites_app")
