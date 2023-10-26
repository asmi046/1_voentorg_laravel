import './bootstrap';

import { register } from 'swiper/element/bundle';
register();

const swiperEl = document.querySelector('swiper-container');
if (swiperEl) {
    const params = {

        injectStyles: [
          `
          .swiper-button-next svg,
          .swiper-button-prev svg {
              display: none;
          }
          `,
        ],
      };

      Object.assign(swiperEl, params);

      swiperEl.initialize();
}




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
