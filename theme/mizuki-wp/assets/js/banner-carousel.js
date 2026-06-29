/**
 * Mizuki Banner Carousel — 淡入淡出轮播 + Ken Burns 缩放动画。
 * 读取 #banner-carousel 的 data-* 属性驱动。
 */
(function() {
  'use strict';

  document.addEventListener('DOMContentLoaded', function() {
    var carousel = document.getElementById('banner-carousel');
    if (!carousel) return;

    var enabled = carousel.dataset.carouselEnabled === '1';
    if (!enabled) return;

    var interval = parseInt(carousel.dataset.carouselInterval) || 3000;
    var fadeMs = parseInt(carousel.dataset.fadeMs) || 1200;

    // 检测设备类型
    var isMobile = window.innerWidth < 768;
    var slotSelector = isMobile ? '.banner-image-slot-mobile' : '.banner-image-slot-desktop';
    var tplSelector = isMobile ? '.banner-tpl-mobile' : '.banner-tpl-desktop';

    var slot = carousel.querySelector(slotSelector);
    var templates = carousel.querySelectorAll(tplSelector);

    if (!slot || templates.length === 0) return; // 单图不轮播

    var currentIndex = 0;

    function makeSlide(index) {
      var content = templates[index].content.cloneNode(true);
      var slide = content.firstElementChild;
      return slide;
    }

    function applyKenBurns(slide) {
      var inner = slide.querySelector('.object-cover');
      if (inner) {
        inner.classList.add('banner-kb');
      }
    }

    // 初始化:从 templates[0] 克隆首图填入空 slot,使其立即可见。
    var firstSlide = makeSlide(0);
    firstSlide.style.opacity = '1';
    firstSlide.style.position = 'absolute';
    firstSlide.style.inset = '0';
    applyKenBurns(firstSlide);
    slot.appendChild(firstSlide);
    currentIndex = 1; // 下一次切换到 image #2

    function nextSlide() {
      if (templates.length < 2) return; // 单图不轮播

      var oldSlide = slot.firstElementChild;
      if (!oldSlide) {
        currentIndex = 0;
        return;
      }

      // 克隆下一张图片。
      var newSlide = makeSlide(currentIndex);

      // 初始化:透明 + Ken Burns 动画。
      newSlide.style.opacity = '0';
      newSlide.style.position = 'absolute';
      newSlide.style.inset = '0';
      applyKenBurns(newSlide);

      slot.appendChild(newSlide);

      // 淡入新图,淡出旧图。
      setTimeout(function() {
        newSlide.style.transition = 'opacity ' + fadeMs + 'ms ease-in-out';
        newSlide.style.opacity = '1';
        oldSlide.style.transition = 'opacity ' + fadeMs + 'ms ease-in-out';
        oldSlide.style.opacity = '0';
      }, 50);

      // 移除旧图。
      setTimeout(function() {
        if (oldSlide.parentNode === slot) {
          slot.removeChild(oldSlide);
        }
      }, fadeMs + 100);

      currentIndex = (currentIndex + 1) % templates.length;
    }

    // 启动轮播
    setInterval(nextSlide, interval);

    // 响应式:窗口尺寸变化时重新初始化
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
