# Mizuki WordPress 主题 — Phase 2 实现计划

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 为 Mizuki WordPress 主题添加主题化功能:Customizer 设置面板(Banner 图、主色 hue 滑块、个人资料卡、社交链接、Live2D 开关)、侧栏 widget 充实、搜索/评论样式深化,以及原生 JS 亮暗切换和 TOC 生成。

**Architecture:** 经典 PHP 模板 + Customizer API。亮暗切换和 hue 调色器用原生 JS(读写 localStorage + CSS 变量)。TOC 从 `.markdown-content` 的 h2/h3 生成,滚动高亮用 IntersectionObserver。

**Tech Stack:** PHP (Customizer API, widget API), vanilla JS, CSS variables.

**约定:**
- 工作目录:`/root/mizuki`,分支 `feat/wp-theme-phase0-1`
- 主题目录:`theme/mizuki-wp/`,同步:`bash tools/sync-theme.sh`,WP-CLI:`bash tools/wp-cli.sh <cmd>`
- 提交信息追加 `Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>`

---

## 文件结构

```
theme/mizuki-wp/
├── inc/customizer.php        # Customizer 设置面板(Banner/hue/资料/社交/Live2D)
├── inc/template-tags.php     # 追加: mizuki_social_links() 等
├── assets/js/mizuki-theme.js # 原生 JS: 亮暗切换 + hue 调色器 + TOC 生成
└── (修改现有)header.php, footer.php, sidebar.php
```

---

### Task 2.1: Customizer 设置面板

**Files:**
- Create: `theme/mizuki-wp/inc/customizer.php`
- Modify: `theme/mizuki-wp/functions.php`(require customizer.php)

- [ ] **Step 1: 写 `inc/customizer.php`**

```php
<?php
/**
 * Customizer 设置面板:Banner、主色 hue、个人资料、社交链接、Live2D。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 注册 Customizer 设置与控件。
 */
function mizuki_customize_register( $wp_customize ) {

	// === Banner 设置 ===
	$wp_customize->add_section( 'mizuki_banner', array(
		'title'    => __( 'Banner 设置', 'mizuki' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'mizuki_banner_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mizuki_banner_image', array(
		'label'   => __( 'Banner 图片', 'mizuki' ),
		'section' => 'mizuki_banner',
	) ) );

	$wp_customize->add_setting( 'mizuki_banner_height', array(
		'default'           => '60vh',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'mizuki_banner_height', array(
		'label'   => __( 'Banner 高度', 'mizuki' ),
		'section' => 'mizuki_banner',
		'type'    => 'text',
	) );

	// === 主题色设置 ===
	$wp_customize->add_section( 'mizuki_color', array(
		'title'    => __( '主题色', 'mizuki' ),
		'priority' => 35,
	) );

	$wp_customize->add_setting( 'mizuki_hue', array(
		'default'           => 240,
		'sanitize_callback' => 'absint',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'mizuki_hue', array(
		'label'       => __( '主题色相 (Hue)', 'mizuki' ),
		'description' => __( '0-360,默认 240(蓝色)。', 'mizuki' ),
		'section'     => 'mizuki_color',
		'type'        => 'range',
		'input_attrs' => array( 'min' => 0, 'max' => 360, 'step' => 1 ),
	) );

	$wp_customize->add_setting( 'mizuki_hue_fixed', array(
		'default'           => false,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	_wp_customize->add_control( 'mizuki_hue_fixed', array(
		'label'   => __( '锁定主题色(隐藏访客调色器)', 'mizuki' ),
		'section' => 'mizuki_color',
		'type'    => 'checkbox',
	) );

	// === 个人资料 ===
	$wp_customize->add_section( 'mizuki_profile', array(
		'title'    => __( '个人资料', 'mizuki' ),
		'priority' => 40,
	) );

	$profile_fields = array(
		'mizuki_avatar'  => array( __( '头像 URL', 'mizuki' ), 'url_raw', 'url' ),
		'mizuki_nickname'=> array( __( '昵称', 'mizuki' ), 'sanitize_text_field', 'text' ),
		'mizuki_bio'     => array( __( '简介', 'mizuki' ), 'sanitize_text_field', 'textarea' ),
	);
	foreach ( $profile_fields as $id => $cfg ) {
		$wp_customize->add_setting( $id, array(
			'default'           => '',
			'sanitize_callback' => $cfg[1],
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $cfg[0],
			'section' => 'mizuki_profile',
			'type'    => $cfg[2],
		) );
	}

	// === 社交链接 ===
	$wp_customize->add_section( 'mizuki_social', array(
		'title'    => __( '社交链接', 'mizuki' ),
		'priority' => 45,
	) );

	$social_platforms = array( 'github', 'twitter', 'email', 'rss' );
	foreach ( $social_platforms as $platform ) {
		$wp_customize->add_setting( "mizuki_social_{$platform}", array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( "mizuki_social_{$platform}", array(
			'label'   => ucfirst( $platform ) . ' URL',
			'section' => 'mizuki_social',
			'type'    => 'url',
		) );
	}

	// === Live2D ===
	$wp_customize->add_section( 'mizuki_live2d', array(
		'title'    => __( 'Live2D 看板娘', 'mizuki' ),
		'priority' => 50,
	) );

	$wp_customize->add_setting( 'mizuki_live2d_enabled', array(
		'default'           => false,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'mizuki_live2d_enabled', array(
		'label'   => __( '启用 Live2D 看板娘', 'mizuki' ),
		'section' => 'mizuki_live2d',
		'type'    => 'checkbox',
	) );
}
add_action( 'customize_register', 'mizuki_customize_register' );
```

- [ ] **Step 2: 在 `functions.php` 末尾追加 require**

```php
require_once MIZUKI_DIR . '/inc/customizer.php';
```

- [ ] **Step 3: 修改 `inc/setup.php` 的 `mizuki_output_hue()` 使用 Customizer 值**

把 `mizuki_output_hue()` 中的硬编码 `240` 改为 `get_theme_mod( 'mizuki_hue', 240 )`。

- [ ] **Step 4: 验证**

```bash
bash tools/sync-theme.sh
bash tools/wp-cli.sh eval 'echo get_theme_mod("mizuki_hue", 240);'  # expect 240
curl -s http://localhost:8888 | grep -o -- '--hue:[0-9]*'           # expect --hue:240
```

- [ ] **Step 5: 提交**

```bash
git add theme/mizuki-wp/inc/customizer.php theme/mizuki-wp/functions.php theme/mizuki-wp/inc/setup.php
git commit -m "feat(theme): Customizer settings (banner, hue, profile, social, Live2D)

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 2.2: 原生 JS 亮暗切换 + hue 调色器 + TOC 生成

**Files:**
- Create: `theme/mizuki-wp/assets/js/mizuki-theme.js`
- Modify: `theme/mizuki-wp/inc/enqueue.php`(入队 JS)
- Modify: `theme/mizuki-wp/header.php`(添加亮暗切换按钮 + hue 滑块 HTML)

- [ ] **Step 1: 写 `assets/js/mizuki-theme.js`**

```js
/**
 * Mizuki 主题交互:亮暗切换 + hue 调色器 + TOC 生成。
 * 原生 JS,无框架依赖。
 */
(function () {
  'use strict';

  // === 亮暗切换 ===
  const THEME_KEY = 'theme';
  const LIGHT = 'light';
  const DARK = 'dark';

  function getStoredTheme() {
    return localStorage.getItem(THEME_KEY) || LIGHT;
  }

  function setTheme(theme) {
    const root = document.documentElement;
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

  // 绑定切换按钮
  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('theme-toggle');
    if (btn) {
      btn.addEventListener('click', function () {
        setTheme(getStoredTheme() === DARK ? LIGHT : DARK);
      });
    }
  });

  // === Hue 调色器 ===
  const HUE_KEY = 'hue';

  function getStoredHue() {
    return localStorage.getItem(HUE_KEY) || null;
  }

  function setHue(hue) {
    document.documentElement.style.setProperty('--hue', String(hue));
    localStorage.setItem(HUE_KEY, String(hue));
  }

  // 初始化 hue
  const storedHue = getStoredHue();
  if (storedHue !== null) {
    setHue(storedHue);
  }

  document.addEventListener('DOMContentLoaded', function () {
    const slider = document.getElementById('hue-slider');
    if (slider) {
      slider.value = getStoredHue() || slider.dataset.default || 240;
      slider.addEventListener('input', function () {
        setHue(this.value);
      });
    }
  });

  // === TOC 生成(从 .markdown-content 的 h2/h3) ===
  document.addEventListener('DOMContentLoaded', function () {
    const content = document.querySelector('.markdown-content');
    const tocContainer = document.getElementById('toc-container');
    if (!content || !tocContainer) return;

    const headings = content.querySelectorAll('h2, h3');
    if (headings.length < 2) return;

    const nav = document.createElement('nav');
    nav.className = 'toc-nav';

    headings.forEach(function (h, i) {
      if (!h.id) h.id = 'heading-' + i;
      const a = document.createElement('a');
      a.href = '#' + h.id;
      a.className = 'px-2 flex gap-2 relative transition w-full min-h-9 rounded-xl hover:bg-[var(--toc-btn-hover)] py-2 ' + (h.tagName === 'H3' ? 'pl-6' : '');
      a.innerHTML = '<div class="text-sm text-50">' + h.textContent + '</div>';
      nav.appendChild(a);
    });

    tocContainer.innerHTML = '';
    tocContainer.appendChild(nav);

    // 滚动高亮
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        const link = nav.querySelector('a[href="#' + entry.target.id + '"]');
        if (link) {
          if (entry.isIntersecting) {
            link.classList.add('bg-[var(--toc-btn-hover)]', 'text-90');
          } else {
            link.classList.remove('bg-[var(--toc-btn-hover)]', 'text-90');
          }
        }
      });
    }, { rootMargin: '-20% 0px -60% 0px' });

    headings.forEach(function (h) { observer.observe(h); });
  });

})();
```

- [ ] **Step 2: 在 `inc/enqueue.php` 追加 JS 入队**

在 `mizuki_enqueue_global_styles()` 末尾追加:

```php
$js = MIZUKI_DIR . '/assets/js/mizuki-theme.js';
if ( file_exists( $js ) ) {
	wp_enqueue_script( 'mizuki-theme', MIZUKI_URI . '/assets/js/mizuki-theme.js', array(), $ver, true );
}
```

- [ ] **Step 3: 在 `header.php` 的 `#display-settings-switch` 按钮旁添加亮暗切换按钮**

在 `#navbar` 的按钮区域添加:

```html
<button id="theme-toggle" class="btn-plain scale-animation rounded-lg h-11 w-11" aria-label="<?php esc_attr_e( '切换明暗', 'mizuki' ); ?>">
  <!-- 月亮/太阳图标(SVG inline) -->
</button>
```

- [ ] **Step 4: 在 `footer.php` 或侧栏添加 hue 滑块(可选,按 Customizer `mizuki_hue_fixed` 控制)**

```php
<?php if ( ! get_theme_mod( 'mizuki_hue_fixed', false ) ) : ?>
<div class="hue-slider-wrapper px-4 py-2">
  <input type="range" id="hue-slider" min="0" max="360" step="1"
         data-default="<?php echo esc_attr( get_theme_mod( 'mizuki_hue', 240 ) ); ?>"
         class="w-full" />
</div>
<?php endif; ?>
```

- [ ] **Step 5: 验证**

```bash
bash tools/sync-theme.sh
curl -s http://localhost:8888 | grep -c 'mizuki-theme.js'    # expect 1
curl -s http://localhost:8888 | grep -c 'theme-toggle'       # expect 1
curl -s http://localhost:8888 | grep -c 'hue-slider'         # expect 1
```

- [ ] **Step 6: 提交**

```bash
git add theme/mizuki-wp/assets/js/mizuki-theme.js theme/mizuki-wp/inc/enqueue.php theme/mizuki-wp/header.php theme/mizuki-wp/footer.php
git commit -m "feat(theme): vanilla JS light/dark toggle + hue slider + TOC generation

Reads/writes localStorage 'theme' and 'hue' keys. TOC generated from
.markdown-content h2/h3 with IntersectionObserver scroll highlighting.

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 2.3: Banner 背景图 + 侧栏 widget 充实

**Files:**
- Modify: `theme/mizuki-wp/header.php`(Banner 背景图)
- Create: `theme/mizuki-wp/sidebar.php`(侧栏模板)

- [ ] **Step 1: 在 `header.php` 的 `#banner-wrapper` 添加背景图**

用 `get_theme_mod( 'mizuki_banner_image' )` 获取 Banner 图 URL,设为背景:

```php
<?php
$banner_img = get_theme_mod( 'mizuki_banner_image', '' );
$banner_style = $banner_img ? 'style="background-image: url(' . esc_url( $banner_img ) . '); background-size: cover; background-position: center;"' : '';
?>
<div id="banner-wrapper" <?php echo $banner_style; ?>>
```

- [ ] **Step 2: 写 `sidebar.php`**

```php
<?php
/**
 * 侧栏模板。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
?>
<aside id="sidebar" class="sidebar">
	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php else : ?>
		<div class="widget card-base p-4 mb-4">
			<h3 class="widget__title font-bold mb-2"><?php esc_html_e( '侧栏', 'mizuki' ); ?></h3>
			<p class="text-50 text-sm"><?php esc_html_e( '请在 外观 > 小工具 中添加内容。', 'mizuki' ); ?></p>
		</div>
	<?php endif; ?>
</aside>
```

- [ ] **Step 3: 验证并提交**

```bash
bash tools/sync-theme.sh
curl -s http://localhost:8888 | grep -c 'banner-wrapper'  # expect 1
curl -s http://localhost:8888 | grep -c 'sidebar'          # expect >=1
git add theme/mizuki-wp/header.php theme/mizuki-wp/sidebar.php
git commit -m "feat(theme): Banner background image from Customizer + sidebar template

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Phase 2 完成判据

- [ ] Customizer 中可见:Banner 设置、主题色 hue 滑块、个人资料、社交链接、Live2D 开关
- [ ] 亮暗切换按钮工作(切 `<html>.dark` + localStorage)
- [ ] hue 调色器工作(改 `--hue` + localStorage,与 Customizer 联动)
- [ ] TOC 在文章页自动生成(从 h2/h3)
- [ ] Banner 背景图从 Customizer 设置加载
- [ ] 侧栏有占位提示(无 widget 时)或渲染 widget
- [ ] 无 PHP 警告
