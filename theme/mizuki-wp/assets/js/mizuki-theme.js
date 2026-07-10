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
  function setHue(hue, persist) {
    document.documentElement.style.setProperty('--hue', String(hue));
    if (persist !== false) localStorage.setItem(HUE_KEY, String(hue));
  }
  var storedHue = getStoredHue();
  if (storedHue !== null) { setHue(storedHue); }

  // === Banner 显示模式 ===
  var WALLPAPER_MODE_KEY = 'wallpaperMode';
  var POST_LIST_LAYOUT_KEY = 'postListLayout';
  var OVERLAY_OPACITY_KEY = 'overlayOpacity';
  var OVERLAY_BLUR_KEY = 'overlayBlur';
  var OVERLAY_CARD_OPACITY_KEY = 'overlayCardOpacity';
  var LEGACY_BANNER_MODE_KEY = 'bannerMode';
  var LEGACY_LAYOUT_MODE_KEY = 'layoutMode';
  var desktopMediaQuery = window.matchMedia('(min-width: 1280px)');

  function normalizeWallpaperMode(mode) {
    if (mode === 'off') return 'none';
    return mode === 'banner' || mode === 'fullscreen' || mode === 'overlay' || mode === 'none' ? mode : null;
  }

  function normalizeLayoutMode(mode) {
    return mode === 'list' || mode === 'grid' ? mode : null;
  }

  function getSectionDefault(resetId, fallback, normalize) {
    var reset = document.getElementById(resetId);
    var section = reset && reset.closest('.desktop-setting-section');
    return normalize(section ? section.dataset.default : null) || fallback;
  }

  function migratePreference(key, legacyKey, normalize) {
    var current = normalize(localStorage.getItem(key));
    if (!current) {
      var legacy = normalize(localStorage.getItem(legacyKey));
      if (legacy) localStorage.setItem(key, legacy);
    } else if (localStorage.getItem(key) !== current) {
      localStorage.setItem(key, current);
    }
    localStorage.removeItem(legacyKey);
  }

  function migrateDisplayPreferences() {
    migratePreference(WALLPAPER_MODE_KEY, LEGACY_BANNER_MODE_KEY, normalizeWallpaperMode);
    migratePreference(POST_LIST_LAYOUT_KEY, LEGACY_LAYOUT_MODE_KEY, normalizeLayoutMode);
  }

  function getStoredWallpaperMode() {
    return normalizeWallpaperMode(localStorage.getItem(WALLPAPER_MODE_KEY)) ||
      getSectionDefault('reset-wallpaper-mode', 'banner', normalizeWallpaperMode);
  }

  function getStoredLayoutMode() {
    return normalizeLayoutMode(localStorage.getItem(POST_LIST_LAYOUT_KEY)) ||
      getSectionDefault('reset-layout-mode', 'list', normalizeLayoutMode);
  }

  function updateResetState(resetId, isDefault) {
    var reset = document.getElementById(resetId);
    if (!reset) return;
    reset.classList.toggle('is-default', isDefault);
    reset.disabled = isDefault;
    reset.setAttribute('aria-hidden', String(isDefault));
  }

  function updateOptionState(selector, mode) {
    var buttons = document.querySelectorAll(selector);
    for (var i = 0; i < buttons.length; i++) {
      var selected = buttons[i].dataset.mode === mode;
      buttons[i].classList.toggle('is-selected', selected);
      buttons[i].setAttribute('data-selected', String(selected));
      buttons[i].setAttribute('aria-pressed', String(selected));
    }
  }

  function getOverlayDefault(inputId, fallback) {
    var input = document.getElementById(inputId);
    var value = Number(input && input.dataset ? input.dataset.default : fallback);
    return Number.isFinite(value) ? value : fallback;
  }

  function getStoredNumber(key, fallback, min, max) {
    var raw = localStorage.getItem(key);
    var value = raw === null ? fallback : Number(raw);
    if (!Number.isFinite(value)) value = fallback;
    return Math.min(max, Math.max(min, value));
  }

  function getOverlaySettings() {
    var opacityDefault = getOverlayDefault('overlay-opacity-slider', 0.8);
    var blurDefault = getOverlayDefault('overlay-blur-slider', 1.5);
    var cardOpacityDefault = getOverlayDefault('overlay-card-opacity-slider', 0.8);
    return {
      opacity: getStoredNumber(OVERLAY_OPACITY_KEY, opacityDefault, 0.2, 1),
      blur: getStoredNumber(OVERLAY_BLUR_KEY, blurDefault, 0, 12),
      cardOpacity: getStoredNumber(OVERLAY_CARD_OPACITY_KEY, cardOpacityDefault, 0.2, 1)
    };
  }

  function persistOverlaySettings(settings) {
    localStorage.setItem(OVERLAY_OPACITY_KEY, String(settings.opacity));
    localStorage.setItem(OVERLAY_BLUR_KEY, String(settings.blur));
    localStorage.setItem(OVERLAY_CARD_OPACITY_KEY, String(settings.cardOpacity));
  }

  function updateRangeProgress(input) {
    if (!input) return;
    var min = Number(input.min || 0);
    var max = Number(input.max || 100);
    var value = Number(input.value || 0);
    var progress = ((value - min) * 100) / (max - min || 1);
    input.style.setProperty('--range-progress', Math.min(100, Math.max(0, progress)) + '%');
  }

  function updateOverlayControls(settings) {
    var opacity = document.getElementById('overlay-opacity-slider');
    var blur = document.getElementById('overlay-blur-slider');
    var cardOpacity = document.getElementById('overlay-card-opacity-slider');
    var opacityValue = document.getElementById('overlay-opacity-value');
    var blurValue = document.getElementById('overlay-blur-value');
    var cardOpacityValue = document.getElementById('overlay-card-opacity-value');

    if (opacity) opacity.value = String(Math.round(settings.opacity * 100));
    if (blur) blur.value = String(settings.blur);
    if (cardOpacity) cardOpacity.value = String(Math.round(settings.cardOpacity * 100));
    if (opacityValue) opacityValue.textContent = Math.round(settings.opacity * 100) + '%';
    if (blurValue) blurValue.textContent = settings.blur.toFixed(1) + 'px';
    if (cardOpacityValue) cardOpacityValue.textContent = Math.round(settings.cardOpacity * 100) + '%';
    updateRangeProgress(opacity);
    updateRangeProgress(blur);
    updateRangeProgress(cardOpacity);

    updateResetState(
      'reset-overlay-settings',
      settings.opacity === getOverlayDefault('overlay-opacity-slider', 0.8) &&
      settings.blur === getOverlayDefault('overlay-blur-slider', 1.5) &&
      settings.cardOpacity === getOverlayDefault('overlay-card-opacity-slider', 0.8)
    );
  }

  function applyOverlayVisualSettings(mode) {
    var wallpaper = document.querySelector('[data-fullscreen-wallpaper]');
    var root = document.documentElement;
    var settings = getOverlaySettings();
    updateOverlayControls(settings);
    if (!wallpaper) return;

    if (mode === 'overlay') {
      wallpaper.style.setProperty('--wallpaper-opacity', String(settings.opacity));
      wallpaper.style.setProperty('--wallpaper-blur', String(settings.blur) + 'px');
      root.style.setProperty('--card-transparent-opacity', String(settings.cardOpacity));
    } else {
      wallpaper.style.removeProperty('--wallpaper-opacity');
      wallpaper.style.removeProperty('--wallpaper-blur');
      root.style.removeProperty('--card-transparent-opacity');
    }
  }

  function updateOverlaySection(mode) {
    var section = document.getElementById('overlay-settings-section');
    if (section) section.hidden = mode !== 'overlay' || !desktopMediaQuery.matches;
  }

  function applyWallpaperMode(mode) {
    var body = document.body;
    if (!body) return;
    var banner = document.getElementById('banner-wrapper');
    var wallpaper = document.querySelector('[data-fullscreen-wallpaper]');
    body.classList.remove(
      'enable-banner',
      'fullscreen-banner',
      'overlay-wallpaper',
      'wallpaper-transparent',
      'no-banner-mode'
    );
    if (mode === 'fullscreen') {
      body.classList.add('enable-banner', 'fullscreen-banner');
      if (banner) banner.style.display = 'block';
      if (wallpaper) wallpaper.style.display = 'none';
    } else if (mode === 'banner') {
      body.classList.add('enable-banner');
      if (banner) banner.style.display = 'block';
      if (wallpaper) wallpaper.style.display = 'none';
    } else if (mode === 'overlay') {
      body.classList.add('overlay-wallpaper', 'wallpaper-transparent', 'no-banner-mode');
      if (banner) banner.style.display = 'none';
      if (wallpaper) wallpaper.style.display = 'block';
    } else if (mode === 'none') {
      body.classList.add('no-banner-mode');
      if (banner) banner.style.display = 'none';
      if (wallpaper) wallpaper.style.display = 'none';
    }
    applyOverlayVisualSettings(mode);
    updateOverlaySection(mode);
  }

  function setWallpaperMode(mode, persist) {
    mode = normalizeWallpaperMode(mode) || 'banner';
    if (persist !== false && !desktopMediaQuery.matches) return;
    if (persist !== false) localStorage.setItem(WALLPAPER_MODE_KEY, mode);
    applyWallpaperMode(mode);
    updateOptionState('.wallpaper-mode-btn', mode);
    updateResetState(
      'reset-wallpaper-mode',
      mode === getSectionDefault('reset-wallpaper-mode', 'banner', normalizeWallpaperMode)
    );
  }

  function setLayoutMode(mode, persist) {
    mode = normalizeLayoutMode(mode) || 'list';
    if (persist !== false && !desktopMediaQuery.matches) return;
    var mainGrid = document.getElementById('main-grid');
    if (mainGrid) mainGrid.setAttribute('data-layout-mode', mode);
    var postList = document.getElementById('post-list-container');
    if (postList) {
      postList.classList.remove('list-mode', 'grid-mode');
      postList.classList.add(mode === 'grid' ? 'grid-mode' : 'list-mode');
    }
    var rightSidebar = document.querySelector('.right-sidebar-container');
    if (rightSidebar) {
      rightSidebar.classList.toggle('hidden-in-grid-mode', mode === 'grid');
    }
    if (persist !== false) localStorage.setItem(POST_LIST_LAYOUT_KEY, mode);
    updateOptionState('.layout-mode-btn', mode);
    updateResetState(
      'reset-layout-mode',
      mode === getSectionDefault('reset-layout-mode', 'list', normalizeLayoutMode)
    );
  }

  function applyResponsiveDisplayState() {
    if (desktopMediaQuery.matches) {
      setWallpaperMode(getStoredWallpaperMode(), false);
      setLayoutMode(getStoredLayoutMode(), false);
    } else {
      setWallpaperMode('banner', false);
      setLayoutMode('list', false);
    }
  }

  migrateDisplayPreferences();
  applyResponsiveDisplayState();
  if (typeof desktopMediaQuery.addEventListener === 'function') {
    desktopMediaQuery.addEventListener('change', applyResponsiveDisplayState);
  } else if (typeof desktopMediaQuery.addListener === 'function') {
    desktopMediaQuery.addListener(applyResponsiveDisplayState);
  }

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
    var panelHueValue = document.getElementById('panel-hue-value');
    var resetHue = document.getElementById('reset-hue');
    if (panelSlider) {
      var defaultHue = panelSlider.dataset.default || '240';
      var updateHueControl = function (hue) {
        panelSlider.value = String(hue);
        if (panelHueValue) panelHueValue.value = String(hue) + '\u00b0';
        updateResetState('reset-hue', String(hue) === String(defaultHue));
      };
      updateHueControl(getStoredHue() || defaultHue);
      panelSlider.addEventListener('input', function () {
        setHue(this.value);
        updateHueControl(this.value);
      });
      if (resetHue) {
        resetHue.addEventListener('click', function () {
          localStorage.removeItem(HUE_KEY);
          setHue(defaultHue, false);
          updateHueControl(defaultHue);
        });
      }
    }

    // === Banner 显示模式切换按钮 ===
    var wallpaperModeBtns = document.querySelectorAll('.wallpaper-mode-btn');
    for (var wi = 0; wi < wallpaperModeBtns.length; wi++) {
      wallpaperModeBtns[wi].addEventListener('click', function () {
        setWallpaperMode(this.dataset.mode);
      });
    }

    var overlayOpacity = document.getElementById('overlay-opacity-slider');
    var overlayBlur = document.getElementById('overlay-blur-slider');
    var overlayCardOpacity = document.getElementById('overlay-card-opacity-slider');
    var updateOverlayFromInputs = function () {
      if (!desktopMediaQuery.matches) return;
      var settings = {
        opacity: overlayOpacity ? Number(overlayOpacity.value) / 100 : 0.8,
        blur: overlayBlur ? Number(overlayBlur.value) : 1.5,
        cardOpacity: overlayCardOpacity ? Number(overlayCardOpacity.value) / 100 : 0.8
      };
      persistOverlaySettings(settings);
      updateOverlayControls(settings);
      if (getStoredWallpaperMode() === 'overlay') applyOverlayVisualSettings('overlay');
    };
    if (overlayOpacity) overlayOpacity.addEventListener('input', updateOverlayFromInputs);
    if (overlayBlur) overlayBlur.addEventListener('input', updateOverlayFromInputs);
    if (overlayCardOpacity) overlayCardOpacity.addEventListener('input', updateOverlayFromInputs);

    var resetOverlay = document.getElementById('reset-overlay-settings');
    if (resetOverlay) {
      resetOverlay.addEventListener('click', function () {
        if (!desktopMediaQuery.matches) return;
        var defaults = {
          opacity: getOverlayDefault('overlay-opacity-slider', 0.8),
          blur: getOverlayDefault('overlay-blur-slider', 1.5),
          cardOpacity: getOverlayDefault('overlay-card-opacity-slider', 0.8)
        };
        persistOverlaySettings(defaults);
        updateOverlayControls(defaults);
        if (getStoredWallpaperMode() === 'overlay') applyOverlayVisualSettings('overlay');
      });
    }

    // === 布局模式切换按钮 ===
    var layoutModeBtns = document.querySelectorAll('.layout-mode-btn');
    for (var li = 0; li < layoutModeBtns.length; li++) {
      layoutModeBtns[li].addEventListener('click', function () {
        setLayoutMode(this.dataset.mode);
      });
    }

    var resetWallpaper = document.getElementById('reset-wallpaper-mode');
    if (resetWallpaper) {
      resetWallpaper.addEventListener('click', function () {
        if (!desktopMediaQuery.matches) return;
        localStorage.removeItem(WALLPAPER_MODE_KEY);
        setWallpaperMode(
          getSectionDefault('reset-wallpaper-mode', 'banner', normalizeWallpaperMode),
          false
        );
      });
    }

    var resetLayout = document.getElementById('reset-layout-mode');
    if (resetLayout) {
      resetLayout.addEventListener('click', function () {
        if (!desktopMediaQuery.matches) return;
        localStorage.removeItem(POST_LIST_LAYOUT_KEY);
        setLayoutMode(
          getSectionDefault('reset-layout-mode', 'list', normalizeLayoutMode),
          false
        );
      });
    }

    applyResponsiveDisplayState();

    // === 通用面板切换函数 ===
    var settingBtn = document.getElementById('display-settings-switch');
    var settingPanel = document.getElementById('display-setting');
    function setSettingsPanelOpen(open) {
      if (!settingBtn || !settingPanel) return;
      settingPanel.classList.toggle('float-panel-closed', !open);
      settingBtn.setAttribute('aria-expanded', String(open));
    }

    function closeAllPanels(exceptId) {
      var panels = ['display-setting', 'nav-menu-panel', 'search-panel'];
      for (var i = 0; i < panels.length; i++) {
        if (panels[i] === exceptId) continue;
        var el = document.getElementById(panels[i]);
        if (el && !el.classList.contains('float-panel-closed')) {
          if (panels[i] === 'display-setting') {
            setSettingsPanelOpen(false);
          } else {
            el.classList.add('float-panel-closed');
          }
        }
      }
    }

    // === 显示设置面板切换 ===
    if (settingBtn && settingPanel) {
      setSettingsPanelOpen(!settingPanel.classList.contains('float-panel-closed'));
      settingBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = !settingPanel.classList.contains('float-panel-closed');
        if (isOpen) {
          setSettingsPanelOpen(false);
        } else {
          closeAllPanels('display-setting');
          setSettingsPanelOpen(true);
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

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closeAllPanels();
    });

    // === 导航下拉菜单(点击展开/收起,点击子项跳转,点击外部关闭) ===
    var navDropdowns = document.querySelectorAll('#navbar-links-container [data-dropdown]');
    var dropdownForceClosedAttribute = 'data-dropdown-force-closed';
    function releaseDropdownForcedClose(dropdown) {
      dropdown.removeAttribute(dropdownForceClosedAttribute);
    }
    function openDropdown(dropdown, trigger) {
      releaseDropdownForcedClose(dropdown);
      trigger.setAttribute('aria-expanded', 'true');
    }
    function closeDropdown(dropdown, trigger) {
      trigger.setAttribute('aria-expanded', 'false');
      dropdown.setAttribute(dropdownForceClosedAttribute, '');
      var active = document.activeElement;
      if (active && dropdown.contains(active) && typeof active.blur === 'function') active.blur();
    }
    function closeAllDropdowns(except) {
      for (var d = 0; d < navDropdowns.length; d++) {
        var t = navDropdowns[d].querySelector('[data-dropdown-trigger]');
        if (t && t !== except) closeDropdown(navDropdowns[d], t);
      }
    }
    for (var di = 0; di < navDropdowns.length; di++) {
      (function (dd) {
        var trigger = dd.querySelector('[data-dropdown-trigger]');
        if (!trigger) return;
        dd.addEventListener('mouseenter', function () {
          releaseDropdownForcedClose(dd);
        });
        trigger.addEventListener('focus', function () {
          releaseDropdownForcedClose(dd);
        });
        trigger.addEventListener('click', function (e) {
          e.stopPropagation();
          var open = trigger.getAttribute('aria-expanded') === 'true';
          if (open) {
            closeDropdown(dd, trigger);
          } else {
            closeAllDropdowns(trigger);
            openDropdown(dd, trigger);
          }
        });
        trigger.addEventListener('keydown', function (e) {
          if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); trigger.click(); }
        });
        dd.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') {
            e.preventDefault();
            closeDropdown(dd, trigger);
          }
        });
      })(navDropdowns[di]);
    }
    if (navDropdowns.length) {
      document.addEventListener('click', function (e) {
        for (var d = 0; d < navDropdowns.length; d++) {
          if (!navDropdowns[d].contains(e.target)) {
            var t = navDropdowns[d].querySelector('[data-dropdown-trigger]');
            if (t) closeDropdown(navDropdowns[d], t);
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
