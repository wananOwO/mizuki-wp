/**
 * Mizuki 主题交互:亮暗切换 + hue 调色器 + TOC 生成 + 面板控制。
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
    // 导航栏滚动透明切换(semifull:顶部透明,滚动后磨砂卡片背景)。
    var navbar = document.getElementById('navbar');
    var backToTop = document.getElementById('back-to-top-btn');
    if (navbar || backToTop) {
      var applyScrolled = function () {
        var y = window.scrollY;
        if (navbar) { navbar.classList.toggle('scrolled', y > 55); }
        if (backToTop) { backToTop.classList.toggle('hide', y < 300); }
      };
      applyScrolled();
      window.addEventListener('scroll', applyScrolled, { passive: true });
    }
    if (backToTop) {
      backToTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }

    // 移动端文章列表默认 opacity:0(等待 JS 初始化),补上原项目缺失的 js-initialized 类,
    // 否则移动端文章卡片不可见(但仍占位、可点击)。
    var postListContainer = document.getElementById('post-list-container');
    if (postListContainer) {
      postListContainer.classList.add('js-initialized');
    }

    // 亮暗切换按钮
    var themeBtn = document.getElementById('theme-toggle');
    if (themeBtn) {
      themeBtn.addEventListener('click', function () {
        setTheme(getStoredTheme() === DARK ? LIGHT : DARK);
      });
    }

    // === Hue 滑块(顶部"显示设置"面板,唯一调色入口) ===
    var panelSlider = document.getElementById('panel-hue-slider');
    if (panelSlider) {
      panelSlider.value = getStoredHue() || panelSlider.dataset.default || '240';
      panelSlider.addEventListener('input', function () { setHue(this.value); });
    }

    // === 通用面板切换函数 ===
    function closeAllPanels(exceptId) {
      var panels = ['display-setting', 'nav-menu-panel', 'search-panel'];
      for (var i = 0; i < panels.length; i++) {
        if (panels[i] === exceptId) continue;
        var el = document.getElementById(panels[i]);
        if (el && !el.classList.contains('float-panel-closed')) {
          el.classList.add('float-panel-closed');
        }
      }
    }

    // === 显示设置面板切换 ===
    var settingBtn = document.getElementById('display-settings-switch');
    var settingPanel = document.getElementById('display-setting');
    if (settingBtn && settingPanel) {
      settingBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = !settingPanel.classList.contains('float-panel-closed');
        if (isOpen) {
          // 如果已打开,直接关闭
          settingPanel.classList.add('float-panel-closed');
        } else {
          // 如果已关闭,先关闭其他面板再打开
          closeAllPanels('display-setting');
          settingPanel.classList.remove('float-panel-closed');
        }
      });
    }

    // === 移动端导航菜单切换 ===
    var menuBtn = document.getElementById('nav-menu-switch');
    var menuPanel = document.getElementById('nav-menu-panel');
    if (menuBtn && menuPanel) {
      menuBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        e.preventDefault(); // 防止触发其他默认行为
        var isOpen = !menuPanel.classList.contains('float-panel-closed');
        if (isOpen) {
          // 如果已打开,直接关闭(不调用 closeAllPanels,避免 toggle 后再打开)
          menuPanel.classList.add('float-panel-closed');
        } else {
          // 如果已关闭,先关闭其他面板再打开
          closeAllPanels('nav-menu-panel');
          menuPanel.classList.remove('float-panel-closed');
        }
      });
    }

    // === 移动端搜索面板切换 ===
    var searchBtn = document.getElementById('search-switch');
    var searchPanel = document.getElementById('search-panel');
    if (searchBtn && searchPanel) {
      searchBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        e.preventDefault();
        var isOpen = !searchPanel.classList.contains('float-panel-closed');
        if (isOpen) {
          searchPanel.classList.add('float-panel-closed');
        } else {
          closeAllPanels('search-panel');
          searchPanel.classList.remove('float-panel-closed');
          var input = searchPanel.querySelector('input');
          if (input) input.focus();
        }
      });
    }

    // 点击面板外关闭所有面板
    document.addEventListener('click', function (event) {
      var isInsidePanel = false;
      var panels = [settingPanel, menuPanel, searchPanel];
      var buttons = [settingBtn, menuBtn, searchBtn];
      for (var i = 0; i < panels.length; i++) {
        if (panels[i] && panels[i].contains(event.target)) isInsidePanel = true;
        if (buttons[i] && buttons[i].contains(event.target)) isInsidePanel = true;
      }
      if (!isInsidePanel) {
        closeAllPanels();
      }
    });

    // === 导航下拉菜单(点击展开/收起,点击子项跳转,点击外部关闭) ===
    var navDropdowns = document.querySelectorAll('#navbar-links-container [data-dropdown]');
    function closeAllDropdowns(except) {
      for (var d = 0; d < navDropdowns.length; d++) {
        var t = navDropdowns[d].querySelector('[data-dropdown-trigger]');
        if (t && t !== except) t.setAttribute('aria-expanded', 'false');
      }
    }
    for (var di = 0; di < navDropdowns.length; di++) {
      (function (dd) {
        var trigger = dd.querySelector('[data-dropdown-trigger]');
        if (!trigger) return;
        trigger.addEventListener('click', function (e) {
          e.stopPropagation();
          var open = trigger.getAttribute('aria-expanded') === 'true';
          closeAllDropdowns(trigger);
          trigger.setAttribute('aria-expanded', String(!open));
        });
        trigger.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); trigger.click(); }
          else if (e.key === 'Escape') { trigger.setAttribute('aria-expanded', 'false'); }
        });
      })(navDropdowns[di]);
    }
    if (navDropdowns.length) {
      document.addEventListener('click', function (e) {
        for (var d = 0; d < navDropdowns.length; d++) {
          if (!navDropdowns[d].contains(e.target)) {
            var t = navDropdowns[d].querySelector('[data-dropdown-trigger]');
            if (t) t.setAttribute('aria-expanded', 'false');
          }
        }
      });
    }

    // === TOC 生成 ===
    var content = document.querySelector('.markdown-content');
    var tocContainer = document.getElementById('toc-container');
    if (content && tocContainer) {
      var headings = content.querySelectorAll('h2, h3');
      if (headings.length >= 2) {
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
      }
    }

    // === Fancybox 画廊(文章图片灯箱) ===
    // 将正文中未被链接包裹的图片包进 data-fancybox 锚点。
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

    // 初始化 Fancybox 灯箱(库由 enqueue.php 通过 CDN 加载)。
    if (window.Fancybox && typeof window.Fancybox.bind === 'function') {
      window.Fancybox.bind('[data-fancybox]', {});
    }
  });
})();
