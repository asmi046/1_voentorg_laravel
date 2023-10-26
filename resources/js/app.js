import './bootstrap';

import { register } from 'swiper/element/bundle';
register();

const swiperEl = document.querySelector('swiper-container');

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
