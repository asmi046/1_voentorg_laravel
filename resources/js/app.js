import './bootstrap';


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





