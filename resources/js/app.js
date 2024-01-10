import './bootstrap';
import {createApp} from 'vue/dist/vue.esm-bundler';

import FsLightbox from "fslightbox-vue/v3";

import { register } from 'swiper/element/bundle';
register();




document.addEventListener("DOMContentLoaded", () => {
    let catalog_up_btn = document.querySelectorAll(".cat_navigation ul li a")
      for (let elem of catalog_up_btn)
        elem.addEventListener("click", function (e) {
          // console.log(elem.nextElementSibling);
          if (elem.nextElementSibling != null)
          {
            e.preventDefault();
            elem.classList.toggle("active")
          }
        })
})


const global_app = createApp({
    components:{
        // Swiper,
        // SwiperSlide,
        // YandexMap,
        // ToBascetBtn,
        // ToBascetBtnPage,
        // BascetCounter,
        // FavoritesCounter,
        // Bascet,
        // Favorites,
        // ToFavoritesBtn,
        // PageToBascet,
        // Forma,
        FsLightbox
    },

    setup() {

        // const swiperEl = document.querySelector('swiper-container');
        // if (swiperEl) {
        //     const params = {

        //         injectStyles: [
        //         `
        //         .swiper-button-next svg,
        //         .swiper-button-prev svg {
        //             display: none;
        //         }
        //         `,
        //         ],
        //     };

        //     Object.assign(swiperEl, params);

        //     swiperEl.initialize();
        // }

    //     const store = useStore()

    //     store.dispatch('initialBascet');
    //     store.dispatch('initialFavorites');

        return {
              noarrow:[
                `
                .swiper-button-next svg, .swiper-button-prev svg {display: none;}
                `,
            ]
            //   modules: [EffectCoverflow, Pagination],
        };
    },
})

global_app.mount("#global_app")



