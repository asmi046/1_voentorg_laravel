import {createApp} from 'vue/dist/vue.esm-bundler';

import { store } from "./storage"
import { useStore } from 'vuex'

import { VMaskDirective } from 'v-slim-mask'

import axios from 'axios'
import VueAxios from 'vue-axios'

import Cart from "./components/cart/Cart.vue"
import PageToCart from './components/cart/PageToCart.vue'

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
