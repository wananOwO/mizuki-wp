/**
 * Mizuki 主题交互:亮暗切换 + hue 调色器 + TOC 生成。
 * 原生 JS,无框架依赖。
 */
(function () {
  'use strict';

  // === 亮暗切换 ===
  var THEME_KEY = 'theme';
  var LIGHT = 'light';
  var DARK = 'dark';

  function getStoredTheme() {
    return localStorage.getItem(THEME_KEY) || LIGHT;
  }
  function setTheme(theme) {
    var root = document.documentElement;
    if (theme === DARK) {
      root.classList.add('dark');
      root.setAttribute('data-theme', 'github-dark');
    } else {
      root.classList.remove('dark');
      root.setAttribute('data-theme', 'github-light');
    }
    localStorage.setItem(THEME_KEY, theme);
  }
  // 初始化(避免 FOUC)
  setTheme(getStoredTheme());

  // === Hue 调色器 ===
  var HUE_KEY = 'hue';
  function getStoredHue() { return localStorage.getItem(HUE_KEY); }
  function setHue(hue) {
    document.documentElement.style.setProperty('--hue', String(hue));
    localStorage.setItem(HUE_KEY, String(hue));
  }
  var storedHue = getStoredHue();
  if (storedHue !== null) { setHue(storedHue); }

  // === DOMContentLoaded ===
  document.addEventListener('DOMContentLoaded', function () {
    // 亮暗切换按钮
    var themeBtn = document.getElementById('theme-toggle');
    if (themeBtn) {
      themeBtn.addEventListener('click', function () {
        setTheme(getStoredTheme() === DARK ? LIGHT : DARK);
      });
    }

    // Hue 滑块
    var slider = document.getElementById('hue-slider');
    if (slider) {
      slider.value = getStoredHue() || slider.dataset.default || '240';
      slider.addEventListener('input', function () { setHue(this.value); });
    }

    // TOC 生成
    var content = document.querySelector('.markdown-content');
    var tocContainer = document.getElementById('toc-container');
    if (!content || !tocContainer) return;
    var headings = content.querySelectorAll('h2, h3');
    if (headings.length < 2) return;
    var nav = document.createElement('nav');
    nav.className = 'toc-nav';
    for (var i = 0; i < headings.length; i++) {
      var h = headings[i];
      if (!h.id) h.id = 'heading-' + i;
      var a = document.createElement('a');
      a.href = '#' + h.id;
      a.className = 'px-2 flex gap-2 relative transition w-full min-h-9 rounded-xl hover:bg-[var(--toc-btn-hover)] py-2';
      if (h.tagName === 'H3') a.className += ' pl-6';
      a.innerHTML = '<div class="text-sm text-50">' + h.textContent + '</div>';
      nav.appendChild(a);
    }
    tocContainer.innerHTML = '';
    tocContainer.appendChild(nav);

    // 滚动高亮
    var observer = new IntersectionObserver(function (entries) {
      for (var j = 0; j < entries.length; j++) {
        var link = nav.querySelector('a[href="#' + entries[j].target.id + '"]');
        if (link) {
          if (entries[j].isIntersecting) {
            link.classList.add('bg-[var(--toc-btn-hover)]', 'text-90');
          } else {
            link.classList.remove('bg-[var(--toc-btn-hover)]', 'text-90');
          }
        }
      }
    }, { rootMargin: '-20% 0px -60% 0px' });
    for (var k = 0; k < headings.length; k++) { observer.observe(headings[k]); }
  });

  // === Fancybox 画廊(文章图片灯箱) ===
  var postImages = document.querySelectorAll('.markdown-content img');
  for (var fi = 0; fi < postImages.length; fi++) {
    var pimg = postImages[fi];
    if (!pimg.closest('a')) {
      var fa = document.createElement('a');
      fa.href = pimg.src;
      fa.dataset.fancybox = 'gallery';
      fa.dataset.caption = pimg.alt || '';
      pimg.parentNode.insertBefore(fa, pimg);
      fa.appendChild(pimg);
    }
  }
})();
