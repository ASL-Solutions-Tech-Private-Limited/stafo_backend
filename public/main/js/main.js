// slider
var swiper = new Swiper('.banner-slider', {
  slidesPerView: 2,
  spaceBetween: 30,
  centeredSlides: true,
  loop: true,
  pagination: {
    el: '.swiper-pagination',
    clickable: true,
  },
  autoplay: {
    speeds: 2000,
    delay: 4000,
  },
  speed: 1000,
  breakpoints: {
    991: {
      slidesPerView: 2,
    },
    767: {
      slidesPerView: 2,
    },
    575: {
      slidesPerView: 2,
    },
  }
});



// to get the latest year for footer section
document.getElementById("year").textContent = new Date().getFullYear();