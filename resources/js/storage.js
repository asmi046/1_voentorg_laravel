import { createStore } from 'vuex'
import * as cartApi from '@/api/cart'

export const store = new createStore({
    state: {
      cart_count: 0,
      cart_tovars: [],

      favorites_count: 0,
      favorites_tovars: []
    },

    mutations: {
        setCount (state, value) {
            state.cart_count = value
        },

        setTovars (state, value) {
            state.cart_tovars = Array.isArray(value) ? value : []
        },


        setFavoritesCount (state, value) {
            state.favorites_count = value
        },

        setFavorites (state, value) {
            state.favorites_tovars = Array.isArray(value) ? value : []
        },
    },

    getters: {
        cartCount: state => {
          return state.cart_count
        },

        favoritesCount: state => {
          return state.favorites_count
        },

        favoritesList: state => {
            return state.favorites_tovars
        }
    },

    actions: {

        initialBascet(context, value) {
                return cartApi.getCart()
                .then((data) => {
                    context.commit('setCount', data.count ?? 0)
                    context.commit('setTovars', data.position ?? [])
                })
                .catch(error => console.log(error));
        },

        initialFavorites(context, value) {
            axios.get('/favorites/get')
            .then((response) => {
                console.log(response.data.count)
                context.commit('setFavoritesCount', response.data.count)
                context.commit('setFavorites', response.data.position)
            })
            .catch(error => console.log(error));
        }
      }
  })
