/**
 * Mizuki Banner Carousel — 淡入淡出轮播 + Ken Burns 缩放动画。
 * 读取 [data-mizuki-carousel] 的 data-* 属性驱动。
 */
(function() {
  'use strict';

  document.addEventListener('DOMContentLoaded', function() {
    var isMobile = window.innerWidth < 768;
    var carousels = document.querySelectorAll('[data-mizuki-carousel]');

    function initCarousel(carousel) {
      var enabled = carousel.dataset.carouselEnabled === '1';
      var interval = parseInt(carousel.dataset.carouselInterval, 10) || 3000;
      var fadeMs = parseInt(carousel.dataset.fadeMs, 10) || 1200;
      var slotSelector = isMobile ? '.banner-image-slot-mobile' : '.banner-image-slot-desktop';
      var tplSelector = isMobile ? '.banner-tpl-mobile' : '.banner-tpl-desktop';
      var slot = carousel.querySelector(slotSelector);
      var templates = carousel.querySelectorAll(tplSelector);

      if (!slot || templates.length === 0) return;

      var currentIndex = 0;

      function makeSlide(index) {
        var content = templates[index].content.cloneNode(true);
        return content.firstElementChild;
      }

      function applyKenBurns(slide) {
        var inner = slide.querySelector('.object-cover');
        if (inner) inner.classList.add('banner-kb');
      }

      var firstSlide = makeSlide(0);
      firstSlide.style.opacity = '1';
      firstSlide.style.position = 'absolute';
      firstSlide.style.inset = '0';
      applyKenBurns(firstSlide);
      slot.textContent = '';
      slot.appendChild(firstSlide);
      currentIndex = 1;

      function nextSlide() {
        if (!enabled || templates.length < 2) return;

        var oldSlide = slot.firstElementChild;
        if (!oldSlide) {
          currentIndex = 0;
          return;
        }

        var newSlide = makeSlide(currentIndex);
        newSlide.style.opacity = '0';
        newSlide.style.position = 'absolute';
        newSlide.style.inset = '0';
        applyKenBurns(newSlide);
        slot.appendChild(newSlide);

        setTimeout(function() {
          newSlide.style.transition = 'opacity ' + fadeMs + 'ms ease-in-out';
          newSlide.style.opacity = '1';
          oldSlide.style.transition = 'opacity ' + fadeMs + 'ms ease-in-out';
          oldSlide.style.opacity = '0';
        }, 50);

        setTimeout(function() {
          if (oldSlide.parentNode === slot) slot.removeChild(oldSlide);
        }, fadeMs + 100);

        currentIndex = (currentIndex + 1) % templates.length;
      }

      if (enabled && templates.length > 1) setInterval(nextSlide, interval);
    }

    for (var i = 0; i < carousels.length; i++) initCarousel(carousels[i]);

    var resizeTimer;
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function() {
        var nowMobile = window.innerWidth < 768;
        if (nowMobile !== isMobile) {
          location.reload(); // 简单重载以切换 mobile/desktop 轮播
        }
      }, 300);
    });
  });
})();
