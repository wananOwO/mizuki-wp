# Mizuki WordPress 主题(阶段 0 + 阶段 1)实现计划

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** 搭好开发/本地 WordPress 环境并提取 Mizuki 还原基准,交付一个能在 WordPress 中激活、且**博客核心页面(首页列表 / 单篇 / 归档 / 搜索 / 404)与 Mizuki 原版视觉一致**的混合主题骨架。

**Architecture:** 混合经典主题。复用 Mizuki `pnpm build` 产物的真实 DOM 结构与编译后 CSS/JS/字体,PHP 模板(`header.php`/`footer.php`/`index.php`/`single.php` 等)只负责在相同 DOM 结构里用 The Loop 填入 WordPress 数据;`theme.json` 管全局色板与 `--hue` 变量。本地环境用 `@wordpress/env`(Docker)。

**Tech Stack:** WordPress(经典主题 + theme.json)、PHP 模板、`@wordpress/env`(wp-env / Docker)、WP-CLI(经 wp-env)、Theme Check 插件、Mizuki(Astro 6,作为只读还原基准)、Playwright(视觉对比截图,经 npx)。

**范围说明:** 本计划仅覆盖 spec 的**阶段 0、阶段 1**。spec 的阶段 2(主题化/侧栏/搜索深化)、阶段 3(特色页 CPT)、阶段 4(交互层打磨)、阶段 5(发布准备)将各自出独立计划。

**约定:**
- 项目根:`/root/mizuki`(已 `git init`)。
- 主题目录:`theme/mizuki-wp/`;文本域 `mizuki`;函数/句柄前缀 `mizuki_`。
- Mizuki 还原基准:`reference/mizuki/`(git 忽略)。
- 所有 `git commit` 用一致作者信息;提交信息行尾追加
  `Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>`。

---

## 文件结构(本计划产出)

```
/root/mizuki/
├── .gitignore                         # 忽略 reference/、node_modules、wp-env 数据
├── .wp-env.json                       # wp-env:把 theme/mizuki-wp 挂载为主题 + 装 Theme Check
├── package.json                       # 脚本封装:env 启停、build:assets、test:visual
├── reference/mizuki/                  # 克隆的 Mizuki(git 忽略),构建出 dist/ 作还原基准
├── docs/superpowers/
│   ├── specs/2026-06-18-mizuki-wordpress-theme-design.md   # 已存在
│   ├── plans/2026-06-18-mizuki-wp-theme-phase0-1.md        # 本文件
│   └── notes/2026-06-18-mizuki-build-discovery.md          # 阶段0 产出的还原基准笔记
├── tools/visual-compare.mjs           # Playwright 截图 + 逐页对比脚本
└── theme/mizuki-wp/                    # ★ 主题交付物
    ├── style.css                      # 主题头信息
    ├── theme.json                     # 色板 + --hue + 布局
    ├── functions.php                  # 仅 require inc/*
    ├── header.php / footer.php
    ├── index.php / single.php / archive.php / page.php / search.php / 404.php
    ├── inc/
    │   ├── setup.php                  # add_theme_support、菜单、widget 区
    │   ├── enqueue.php                # 入队 assets 下的编译 CSS/JS/字体
    │   └── template-tags.php          # mizuki_reading_time() 等模板函数
    └── assets/{css,js,fonts,icons}/   # 从 reference 构建产物拷入
```

---

## 阶段 0:环境与还原基准

### Task 0.1:项目骨架与 .gitignore

**Files:**
- Create: `/root/mizuki/.gitignore`
- Create: `/root/mizuki/package.json`

- [ ] **Step 1: 写 `.gitignore`**

```gitignore
# 还原基准(只读参考,不入库)
/reference/

# Node
node_modules/
/tools/node_modules/

# wp-env 本地数据
/.wp-env/
/wordpress/

# 系统
.DS_Store
*.log
```

- [ ] **Step 2: 写 `package.json`(脚本封装,稍后任务逐步用到)**

```json
{
  "name": "mizuki-wp",
  "private": true,
  "version": "0.0.0",
  "description": "Mizuki theme ported to WordPress",
  "scripts": {
    "env:start": "wp-env start",
    "env:stop": "wp-env stop",
    "env:clean": "wp-env clean all",
    "cli": "wp-env run cli wp",
    "test:visual": "node tools/visual-compare.mjs"
  },
  "devDependencies": {
    "@wordpress/env": "^10.0.0",
    "playwright": "^1.49.0"
  }
}
```

- [ ] **Step 3: 安装 devDependencies**

Run: `cd /root/mizuki && npm install`
Expected: 成功安装,生成 `node_modules/` 与 `package-lock.json`,无 error 退出码 0。

- [ ] **Step 4: 提交**

```bash
cd /root/mizuki
git add .gitignore package.json package-lock.json
git commit -m "chore: project scaffold (gitignore, npm scripts, wp-env + playwright)

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 0.2:克隆并构建 Mizuki(产生还原基准)

**Files:**
- Create(git 忽略): `/root/mizuki/reference/mizuki/`

- [ ] **Step 1: 克隆 Mizuki 到 reference/(浅克隆)**

Run:
```bash
cd /root/mizuki
git clone --depth 1 https://github.com/LyraVoid/Mizuki.git reference/mizuki
```
Expected: `reference/mizuki/` 出现,含 `package.json`、`src/`、`astro.config.mjs`。

- [ ] **Step 2: 安装 Mizuki 依赖**

Run: `cd /root/mizuki/reference/mizuki && pnpm install`
Expected: 退出码 0(首次较慢)。若报 Node 版本要求 ≥22,本机为 v24,满足。

- [ ] **Step 3: 初始化示例内容并构建**

Run:
```bash
cd /root/mizuki/reference/mizuki
pnpm sync-content || true
pnpm build
```
Expected: 生成 `dist/` 目录,含 `index.html`、`posts/...`、`_astro/*.css`、`_astro/*.js`、字体等。
注意:若 `build` 因 Pagefind/远程拉取(update-anime 等)失败,只需保证生成了 `dist/` 的 HTML 与 `_astro/` 资源即可;这些可选脚本的失败可忽略。

- [ ] **Step 4: 本地预览构建结果(供阶段0 笔记与后续视觉对比用)**

Run(后台): `cd /root/mizuki/reference/mizuki && pnpm preview --port 4321 &`
然后访问 `http://localhost:4321/` 确认博客首页可渲染。记下进程,后续 Task 1.9 会用到该基准站点;用完 `kill` 掉。
Expected: 浏览器/`curl -s localhost:4321 | head` 能看到完整 HTML。

- [ ] **Step 5:** 本任务不提交(reference/ 已 git 忽略)。

---

### Task 0.3:还原基准笔记 + Svelte 交互岛排查

> spec 阶段 0 首要任务:逐个排查 Svelte 交互岛,判定"可编译复用"还是"需改写为原生 JS"。本任务把发现固化成文档,作为阶段 1/4 的依据。

**Files:**
- Create: `/root/mizuki/docs/superpowers/notes/2026-06-18-mizuki-build-discovery.md`

- [ ] **Step 1: 采集关键页面 DOM 结构**

Run:
```bash
cd /root/mizuki/reference/mizuki/dist
ls -1 _astro/ | sed 's/\.[a-z0-9]\{8,\}\./.HASH./' | sort -u    # 资源清单(去 hash)
```
把首页 `index.html`、某篇文章 `posts/.../index.html`、归档 `archive/index.html` 的关键容器结构(`<head>` 引用、顶栏、Banner、文章卡片 `<article>`、单篇正文/TOC、页脚)摘录进笔记。

- [ ] **Step 2: 列出编译后资源清单**

在笔记中记录:全局 CSS 文件名、各页 JS chunk、字体文件(Roboto / JetBrains Mono)、图标 sprite、KaTeX/Expressive-Code 相关样式。标注哪些是"全站必加载",哪些是"按页加载"。

- [ ] **Step 3: 记录主题/配色机制**

Run: `grep -rno -- '--hue' src/ styles/ 2>/dev/null | head; grep -rno 'localStorage' src/ 2>/dev/null | head`
在笔记中记录:Mizuki 用哪个 CSS 变量控制主色(如 `--hue`)、亮暗切换如何打 class/属性、持久化键名。

- [ ] **Step 4: Svelte 交互岛清单与处置**

Run: `grep -rl '\.svelte' src/ | head -50; ls src/components/**/*.svelte 2>/dev/null`
在笔记中为每个交互岛(主题/换色开关、搜索框、Banner 轮播、TOC、返回顶部、画廊、Live2D 等)填一行表格:`组件 | 功能 | 依赖 | 处置(编译复用 / 改写原生 JS / 用 WP 原生替代) | 影响的阶段`。

- [ ] **Step 5: 提交笔记**

```bash
cd /root/mizuki
git add docs/superpowers/notes/2026-06-18-mizuki-build-discovery.md
git commit -m "docs: Mizuki build discovery notes (DOM, assets, theming, island triage)

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 0.4:wp-env 本地 WordPress 环境

**Files:**
- Create: `/root/mizuki/.wp-env.json`

- [ ] **Step 1: 写 `.wp-env.json`(把主题挂载进 WP,并预装 Theme Check)**

```json
{
  "core": "WordPress/WordPress#master",
  "phpVersion": "8.2",
  "themes": [ "./theme/mizuki-wp" ],
  "plugins": [ "https://downloads.wordpress.org/plugin/theme-check.zip" ],
  "config": {
    "WP_DEBUG": true,
    "WP_DEBUG_LOG": true,
    "WP_DEBUG_DISPLAY": false
  },
  "mappings": {}
}
```

- [ ] **Step 2: 创建空主题占位目录**(让 wp-env 挂载不报错;真正内容在 Task 1.1 起)

Run: `mkdir -p /root/mizuki/theme/mizuki-wp/assets/{css,js,fonts,icons}`

- [ ] **Step 3: 启动 wp-env**

Run: `cd /root/mizuki && npm run env:start`
Expected: 首次拉取 Docker 镜像后输出 `WordPress development site started at http://localhost:8888`。

- [ ] **Step 4: 验证 WordPress 运行**

Run: `curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8888`
Expected: `200`(或 `302` 跳转到安装/首页)。
Run: `cd /root/mizuki && npm run cli -- option get blogname`
Expected: 输出默认站点名(确认 WP-CLI 经 wp-env 可用)。

- [ ] **Step 5: 提交**

```bash
cd /root/mizuki
git add .wp-env.json
git commit -m "chore: wp-env local WordPress with Theme Check plugin

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## 阶段 1:核心博客主题骨架(像素级一致)

> 验证手段(本阶段通用):① wp-env 的 `WP_DEBUG_LOG` 无新增 PHP notice/warning(`npm run cli -- eval` 或读 `wp-content/debug.log`);② 页面 HTTP 200 且含预期 DOM 标记;③ Task 1.9 的视觉逐页对比。

### Task 1.1:主题元数据,使其可激活

**Files:**
- Create: `theme/mizuki-wp/style.css`
- Create: `theme/mizuki-wp/theme.json`
- Create: `theme/mizuki-wp/index.php`
- Create: `theme/mizuki-wp/functions.php`

- [ ] **Step 1: 写 `style.css` 主题头**

```css
/*
Theme Name: Mizuki
Theme URI: https://github.com/LyraVoid/Mizuki
Author: Mizuki WP port (based on Mizuki by Matsuzaka Yuki)
Author URI: https://github.com/LyraVoid
Description: 由 Astro 主题 Mizuki 移植而来的 WordPress 混合主题,Material Design 3 风格、亮暗切换、可配主色。
Version: 0.1.0
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 8.0
License: GNU General Public License v3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: mizuki
Tags: blog, custom-colors, custom-menu, featured-images, translation-ready, full-width-template
*/
```

- [ ] **Step 2: 写最小 `theme.json`**(阶段1 先占位,Task 1.8 充实)

```json
{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 3,
  "settings": {
    "appearanceTools": true,
    "layout": { "contentSize": "75rem", "wideSize": "90rem" }
  }
}
```

- [ ] **Step 3: 写最小 `index.php`**(Task 1.4 替换为真实卡片循环)

```php
<?php
/**
 * 临时兜底模板,Task 1.4 用真实 Mizuki 卡片结构替换。
 *
 * @package Mizuki
 */
get_header();
?>
<main id="main" class="site-main">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_title( '<h2><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
		}
	} else {
		esc_html_e( '暂无内容。', 'mizuki' );
	}
	?>
</main>
<?php
get_footer();
```

- [ ] **Step 4: 写 `functions.php`(只做 require,保持瘦)**

```php
<?php
/**
 * Mizuki 主题引导文件。
 *
 * @package Mizuki
 */

defined( 'ABSPATH' ) || exit;

define( 'MIZUKI_VERSION', '0.1.0' );
define( 'MIZUKI_DIR', get_template_directory() );
define( 'MIZUKI_URI', get_template_directory_uri() );

require_once MIZUKI_DIR . '/inc/setup.php';
require_once MIZUKI_DIR . '/inc/enqueue.php';
require_once MIZUKI_DIR . '/inc/template-tags.php';
```

- [ ] **Step 5: 建占位 inc 文件,避免 require 致命错误**

Create `theme/mizuki-wp/inc/setup.php`、`inc/enqueue.php`、`inc/template-tags.php`,每个内容:
```php
<?php
/**
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
```
(下游任务逐个填充。)

- [ ] **Step 6: 激活主题并验证**

Run:
```bash
cd /root/mizuki
npm run cli -- theme activate mizuki
npm run cli -- theme list
```
Expected: `theme list` 中 `mizuki` 状态为 `active`。

- [ ] **Step 7: 验证无 PHP 致命错误**

Run: `curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8888`
Expected: `200`。
Run: `npm run cli -- eval 'echo "ok";'`
Expected: 输出 `ok`(无 PHP fatal)。

- [ ] **Step 8: 提交**

```bash
cd /root/mizuki
git add theme/mizuki-wp
git commit -m "feat(theme): activatable theme skeleton (style.css, theme.json, functions, stubs)

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 1.2:拷入编译资源并入队

**Files:**
- Modify: `theme/mizuki-wp/inc/enqueue.php`
- Create: `theme/mizuki-wp/assets/css/*`、`assets/js/*`、`assets/fonts/*`、`assets/icons/*`(从 reference 拷入)

- [ ] **Step 1: 拷贝 Mizuki 编译产物到主题 assets**

Run(按 Task 0.3 笔记里的真实文件名调整):
```bash
cd /root/mizuki
cp reference/mizuki/dist/_astro/*.css  theme/mizuki-wp/assets/css/  2>/dev/null || true
cp reference/mizuki/dist/_astro/*.js   theme/mizuki-wp/assets/js/   2>/dev/null || true
# 字体与图标按 dist 实际目录拷贝(可能在 dist/_astro 或 dist/fonts)
find reference/mizuki/dist -name '*.woff2' -exec cp {} theme/mizuki-wp/assets/fonts/ \;
```
为稳定句柄,按笔记把全局主样式重命名为可预测名(如 `theme/mizuki-wp/assets/css/mizuki-main.css`)。
Expected: `assets/css`、`assets/js`、`assets/fonts` 内出现文件。

- [ ] **Step 2: 写 `inc/enqueue.php`**

```php
<?php
/**
 * 前端资源入队。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 入队 Mizuki 编译后的样式与脚本。
 */
function mizuki_enqueue_assets() {
	$ver = MIZUKI_VERSION;

	// 主样式(由 Mizuki 构建产物拷入并改名)。
	wp_enqueue_style(
		'mizuki-main',
		MIZUKI_URI . '/assets/css/mizuki-main.css',
		array(),
		$ver
	);

	// 主交互脚本(按 Task 0.3 笔记确定的全站脚本)。
	$main_js = MIZUKI_DIR . '/assets/js/mizuki-main.js';
	if ( file_exists( $main_js ) ) {
		wp_enqueue_script(
			'mizuki-main',
			MIZUKI_URI . '/assets/js/mizuki-main.js',
			array(),
			$ver,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'mizuki_enqueue_assets' );
```
(若笔记显示存在多份必加载 CSS/JS,在此追加对应 `wp_enqueue_*`,句柄统一 `mizuki-` 前缀。)

- [ ] **Step 2b:** 若主样式用相对 `url()` 引用字体且路径与 dist 不同,在 `assets/css/mizuki-main.css` 中把字体引用改为相对 `../fonts/` 实际位置(grep `url(` 核对)。

- [ ] **Step 3: 验证资源加载无 404**

Run:
```bash
cd /root/mizuki
curl -s http://localhost:8888 | grep -oE 'assets/(css|js)/[^"]+' | sort -u
for u in $(curl -s http://localhost:8888 | grep -oE 'http://localhost:8888/wp-content/themes/mizuki/assets/[^"]+'); do
  echo "$(curl -s -o /dev/null -w '%{http_code}' "$u") $u"; done
```
Expected: 每个 assets URL 返回 `200`(无 404)。

- [ ] **Step 4: 提交**

```bash
cd /root/mizuki
git add theme/mizuki-wp/assets theme/mizuki-wp/inc/enqueue.php
git commit -m "feat(theme): bundle and enqueue Mizuki compiled CSS/JS/fonts

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 1.3:header.php / footer.php(外壳、顶栏、Banner)

**Files:**
- Create: `theme/mizuki-wp/header.php`
- Create: `theme/mizuki-wp/footer.php`

- [ ] **Step 1: 写 `header.php`**

按 Task 0.3 笔记把 Mizuki 的 `<head>`、顶栏、Banner 外壳结构搬入,WordPress 钩子替换静态部分:

```php
<?php
/**
 * 页头:文档头 + 顶栏 + Banner 外壳。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
	<!-- 以下顶栏/Banner 容器的 class 与结构需与 Mizuki dist 完全一致(见 Task 0.3 笔记)。 -->
	<header class="site-header"><!-- 顶栏:站点标题 + 导航 -->
		<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => 'nav',
				'fallback_cb'    => false,
				'menu_class'     => 'site-nav',
			)
		);
		?>
	</header>
	<div id="page" class="site-content">
```
> 实操要点:把上面 `site-header`/`site-title`/`site-nav` 替换为 Mizuki dist 中真实的容器 class 与层级(包含 Banner 轮播容器节点);Banner 图片来源在阶段 2 接主题设置,本阶段可先用 Mizuki 默认 Banner 资源占位,确保视觉一致。

- [ ] **Step 2: 写 `footer.php`**

```php
<?php
/**
 * 页脚:闭合容器 + 页脚区 + wp_footer。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
?>
	</div><!-- #page -->
	<footer class="site-footer"><!-- class 对齐 Mizuki dist -->
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
	</footer>
<?php wp_footer(); ?>
</body>
</html>
```

- [ ] **Step 3: 验证页头页脚渲染、无 PHP 警告**

Run:
```bash
cd /root/mizuki
curl -s http://localhost:8888 | grep -c 'site-header'      # 期望 >=1
npm run cli -- eval 'error_reporting(E_ALL); echo "ok";'   # 期望 ok
docker exec $(docker ps -qf name=wordpress) sh -c 'tail -n 20 /var/www/html/wp-content/debug.log' 2>/dev/null || true
```
Expected: 含 `site-header`;`debug.log` 无新增 `PHP Notice/Warning`。

- [ ] **Step 4: 提交**

```bash
cd /root/mizuki
git add theme/mizuki-wp/header.php theme/mizuki-wp/footer.php
git commit -m "feat(theme): header/footer shell with nav + banner container (Mizuki DOM)

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 1.4:首页 / 博客列表(文章卡片)

**Files:**
- Modify: `theme/mizuki-wp/index.php`
- Create: `theme/mizuki-wp/inc/template-tags.php`(填充阅读时间)

- [ ] **Step 1: 写 `mizuki_reading_time()` 到 `inc/template-tags.php`**

```php
<?php
/**
 * 模板辅助函数。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 估算当前文章阅读时间(分钟,按 ~300 中文字/分钟)。
 *
 * @param int|null $post_id 文章 ID,默认当前文章。
 * @return int 阅读分钟数(至少 1)。
 */
function mizuki_reading_time( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id ?: get_the_ID() );
	$text    = wp_strip_all_tags( $content );
	$count   = mb_strlen( preg_replace( '/\s+/u', '', $text ), 'UTF-8' );
	return max( 1, (int) ceil( $count / 300 ) );
}
```

- [ ] **Step 2: 用真实卡片结构重写 `index.php`**

按 Task 0.3 笔记中 Mizuki 文章卡片 `<article>` 的真实 class 与层级,逐字段映射:

```php
<?php
/**
 * 博客首页 / 文章列表。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="post-list"><!-- 容器 class 对齐 Mizuki dist -->
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'post-card' ); ?>><!-- class 对齐 Mizuki 卡片 -->
				<?php if ( has_post_thumbnail() ) : ?>
					<a class="post-card__cover" href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'large' ); ?>
					</a>
				<?php endif; ?>
				<div class="post-card__body">
					<h2 class="post-card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h2>
					<div class="post-card__meta">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<span class="post-card__rt"><?php echo esc_html( sprintf( /* translators: %d 分钟 */ __( '%d 分钟', 'mizuki' ), mizuki_reading_time() ) ); ?></span>
					</div>
					<div class="post-card__excerpt"><?php echo wp_kses_post( get_the_excerpt() ); ?></div>
				</div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
	<?php else : ?>
		<p class="post-list__empty"><?php esc_html_e( '暂无内容。', 'mizuki' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
```

- [ ] **Step 3: 灌入官方测试数据以便有内容渲染**

Run:
```bash
cd /root/mizuki
curl -sL https://raw.githubusercontent.com/WPTT/theme-test-data/master/themeunittestdata.wordpress.xml -o /tmp/wptt.xml
npm run cli -- plugin install wordpress-importer --activate
docker cp /tmp/wptt.xml $(docker ps -qf name=wordpress):/tmp/wptt.xml
npm run cli -- import /tmp/wptt.xml --authors=create
```
Expected: 导入大量示例文章/页面/分类。

- [ ] **Step 4: 验证列表页渲染**

Run:
```bash
cd /root/mizuki
curl -s http://localhost:8888 | grep -c 'post-card'   # 期望 >=1
```
Expected: 出现多张 `post-card`。

- [ ] **Step 5: 提交**

```bash
cd /root/mizuki
git add theme/mizuki-wp/index.php theme/mizuki-wp/inc/template-tags.php
git commit -m "feat(theme): post list with Mizuki card markup + reading time

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 1.5:单篇文章(正文 / TOC / 阅读时间 / 评论)

**Files:**
- Create: `theme/mizuki-wp/single.php`
- Create: `theme/mizuki-wp/comments.php`

- [ ] **Step 1: 写 `single.php`**

按笔记的单篇结构(正文容器、TOC、代码块/KaTeX 类需与 dist 一致,以命中已入队 CSS):

```php
<?php
/**
 * 单篇文章。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="post-single"><!-- class 对齐 Mizuki dist -->
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'post' ); ?>>
			<header class="post__header">
				<h1 class="post__title"><?php the_title(); ?></h1>
				<div class="post__meta">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<span class="post__rt"><?php echo esc_html( sprintf( __( '%d 分钟', 'mizuki' ), mizuki_reading_time() ) ); ?></span>
				</div>
			</header>
			<div class="post__content prose"><!-- prose/正文 class 对齐 dist -->
				<?php the_content(); ?>
			</div>
			<footer class="post__footer">
				<?php the_tags( '<div class="post__tags">', '', '</div>' ); ?>
			</footer>
		</article>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	<?php endwhile; ?>
</main>
<?php
get_footer();
```

- [ ] **Step 2: 写 `comments.php`(原生评论,套基础 class)**

```php
<?php
/**
 * 原生评论模板。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php echo esc_html( sprintf( _n( '%s 条评论', '%s 条评论', get_comments_number(), 'mizuki' ), number_format_i18n( get_comments_number() ) ) ); ?>
		</h2>
		<ol class="comment-list">
			<?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true ) ); ?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>
	<?php comment_form(); ?>
</section>
```

- [ ] **Step 3: 验证单篇渲染**

Run:
```bash
cd /root/mizuki
pid=$(npm run --silent cli -- post list --post_type=post --posts_per_page=1 --field=ID | tr -d '\r' | head -1)
echo "post id=$pid"
curl -s "http://localhost:8888/?p=${pid}" | grep -c 'post__content'   # 期望 1
```
Expected: 单篇页含 `post__content`,HTTP 200。(用 `?p=ID` 避免依赖固定链接设置。)

- [ ] **Step 4: 提交**

```bash
cd /root/mizuki
git add theme/mizuki-wp/single.php theme/mizuki-wp/comments.php
git commit -m "feat(theme): single post template + native comments

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 1.6:归档 / 搜索 / 页面 / 404

**Files:**
- Create: `theme/mizuki-wp/archive.php`、`search.php`、`page.php`、`404.php`

- [ ] **Step 1: 写 `archive.php`**(复用卡片列表 + 归档标题)

```php
<?php
/** 归档(分类/标签/日期)。 @package Mizuki */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="archive">
	<header class="archive__header">
		<h1 class="archive__title"><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="archive__desc">', '</div>' ); ?>
	</header>
	<div class="post-list">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'post-card' ); ?>>
				<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="post-card__excerpt"><?php echo wp_kses_post( get_the_excerpt() ); ?></div>
			</article>
		<?php endwhile; the_posts_pagination( array( 'mid_size' => 1 ) ); else : ?>
			<p class="post-list__empty"><?php esc_html_e( '暂无内容。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer();
```

- [ ] **Step 2: 写 `search.php`**

```php
<?php
/** 搜索结果(WordPress 原生搜索)。 @package Mizuki */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="search-results">
	<header class="archive__header">
		<h1 class="archive__title">
			<?php echo esc_html( sprintf( __( '搜索:%s', 'mizuki' ), get_search_query() ) ); ?>
		</h1>
	</header>
	<div class="post-list">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'post-card' ); ?>>
				<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="post-card__excerpt"><?php echo wp_kses_post( get_the_excerpt() ); ?></div>
			</article>
		<?php endwhile; the_posts_pagination( array( 'mid_size' => 1 ) ); else : ?>
			<p class="post-list__empty"><?php esc_html_e( '没有匹配的结果。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer();
```

- [ ] **Step 3: 写 `page.php`**

```php
<?php
/** 单个页面。 @package Mizuki */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="page-single">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'page' ); ?>>
			<h1 class="page__title"><?php the_title(); ?></h1>
			<div class="page__content prose"><?php the_content(); ?></div>
		</article>
		<?php if ( comments_open() || get_comments_number() ) { comments_template(); } ?>
	<?php endwhile; ?>
</main>
<?php get_footer();
```

- [ ] **Step 4: 写 `404.php`**

```php
<?php
/** 404。 @package Mizuki */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="error-404">
	<h1 class="error-404__title"><?php esc_html_e( '页面未找到', 'mizuki' ); ?></h1>
	<p class="error-404__text"><?php esc_html_e( '你访问的页面不存在或已移动。', 'mizuki' ); ?></p>
	<?php get_search_form(); ?>
</main>
<?php get_footer();
```

- [ ] **Step 5: 验证四类页面**

Run:
```bash
cd /root/mizuki
echo -n "archive "; curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8888/?cat=1
echo -n "search  "; curl -s -o /dev/null -w '%{http_code}\n' "http://localhost:8888/?s=lorem"
echo -n "404     "; curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8888/this-does-not-exist-xyz
```
Expected:archive/search 返回 200;404 返回 404 且页面含 `error-404`。

- [ ] **Step 6: 提交**

```bash
cd /root/mizuki
git add theme/mizuki-wp/archive.php theme/mizuki-wp/search.php theme/mizuki-wp/page.php theme/mizuki-wp/404.php
git commit -m "feat(theme): archive, search, page, 404 templates

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 1.7:主题支持、菜单、widget 区(setup.php)

**Files:**
- Modify: `theme/mizuki-wp/inc/setup.php`

- [ ] **Step 1: 写 `inc/setup.php`**

```php
<?php
/**
 * 主题初始化:supports、菜单、widget 区。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

/**
 * 注册主题功能支持与导航菜单。
 */
function mizuki_setup() {
	load_theme_textdomain( 'mizuki', MIZUKI_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array( 'primary' => __( '主导航', 'mizuki' ) )
	);
}
add_action( 'after_setup_theme', 'mizuki_setup' );

/**
 * 注册侧栏 widget 区(资料卡、分类、标签、公告)。
 */
function mizuki_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( '侧栏', 'mizuki' ),
			'id'            => 'sidebar-1',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget__title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'mizuki_widgets_init' );
```

- [ ] **Step 2: 验证 supports 与菜单已注册**

Run:
```bash
cd /root/mizuki
npm run cli -- eval 'var_dump( current_theme_supports("post-thumbnails"), has_nav_menu("primary") !== null );'
npm run cli -- eval 'var_dump( (bool) get_theme_support("html5") );'
```
Expected: 输出 `bool(true)` 等,无 PHP fatal。
Run: `npm run cli -- menu create "Main" ; npm run cli -- menu location assign Main primary` → 期望成功。

- [ ] **Step 3: 提交**

```bash
cd /root/mizuki
git add theme/mizuki-wp/inc/setup.php
git commit -m "feat(theme): theme supports, nav menu, sidebar widget area

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 1.8:theme.json 色板 + `--hue` 配色单一真相源

> spec §6:`theme.json` 是主色唯一权威来源,写入 `--hue`;Mizuki JS 只读取该变量做亮暗切换,不另存主色。

**Files:**
- Modify: `theme/mizuki-wp/theme.json`
- Modify: `theme/mizuki-wp/inc/setup.php`(输出 `--hue` 到 `<head>`)

- [ ] **Step 1: 充实 `theme.json`**(色板对齐 Mizuki,数值依 Task 0.3 笔记)

```json
{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 3,
  "settings": {
    "appearanceTools": true,
    "layout": { "contentSize": "75rem", "wideSize": "90rem" },
    "color": {
      "custom": true,
      "palette": [
        { "slug": "primary", "color": "oklch(0.7 0.14 var(--hue))", "name": "主色" },
        { "slug": "page-bg", "color": "var(--page-bg)", "name": "页面背景" },
        { "slug": "text", "color": "var(--text)", "name": "正文" }
      ]
    },
    "typography": {
      "fontFamilies": [
        { "slug": "sans", "name": "Sans", "fontFamily": "Roboto, system-ui, sans-serif" },
        { "slug": "mono", "name": "Mono", "fontFamily": "'JetBrains Mono', monospace" }
      ]
    }
  }
}
```
(若 Mizuki 不用 oklch,改成笔记中的真实色彩函数;关键是 `--hue` 作为唯一变量贯穿。)

- [ ] **Step 2: 在 `inc/setup.php` 末尾追加把主色 hue 注入 `<head>`**

```php
/**
 * 把主题主色 hue 作为 CSS 变量注入文档头(配色单一真相源)。
 * 默认值取自 Mizuki(见还原基准笔记);后续阶段 2 接 Customizer 设置项。
 */
function mizuki_output_hue() {
	$hue = (int) apply_filters( 'mizuki_theme_hue', 250 ); // 250 为占位默认,依笔记调整。
	printf( "<style id=\"mizuki-hue\">:root{--hue:%d;}</style>\n", absint( $hue ) );
}
add_action( 'wp_head', 'mizuki_output_hue', 1 );
```

- [ ] **Step 3: 验证 hue 注入且 theme.json 合法**

Run:
```bash
cd /root/mizuki
curl -s http://localhost:8888 | grep -o 'mizuki-hue.*--hue:[0-9]*'   # 期望含 --hue:NNN
python3 -c "import json;json.load(open('theme/mizuki-wp/theme.json'));print('theme.json OK')"
```
Expected: 输出含 `--hue:` 行;`theme.json OK`。

- [ ] **Step 4: 提交**

```bash
cd /root/mizuki
git add theme/mizuki-wp/theme.json theme/mizuki-wp/inc/setup.php
git commit -m "feat(theme): theme.json palette + single-source --hue injection

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 1.9:视觉逐页对比(像素级验收)+ Theme Check

**Files:**
- Create: `/root/mizuki/tools/visual-compare.mjs`

- [ ] **Step 1: 写 `tools/visual-compare.mjs`**(Playwright 截图原版与 WP 版并比对)

```js
// 逐页截图:Mizuki 基准(:4321)与 WP 主题(:8888),输出对比图与像素差比例。
import { chromium } from 'playwright';
import fs from 'node:fs';

const PAGES = [
  { name: 'home', ref: '/', wp: '/' },
  { name: 'archive', ref: '/archive/', wp: '/?post_type=post' },
];
const OUT = 'tools/visual-out';
fs.mkdirSync(OUT, { recursive: true });

const shot = async (ctx, url, file) => {
  const page = await ctx.newPage();
  await page.setViewportSize({ width: 1440, height: 900 });
  await page.goto(url, { waitUntil: 'networkidle' }).catch(() => {});
  await page.screenshot({ path: file, fullPage: true });
  await page.close();
};

const browser = await chromium.launch();
const ctx = await browser.newContext();
for (const p of PAGES) {
  await shot(ctx, `http://localhost:4321${p.ref}`, `${OUT}/${p.name}-ref.png`);
  await shot(ctx, `http://localhost:8888${p.wp}`, `${OUT}/${p.name}-wp.png`);
  console.log(`captured ${p.name}: ${OUT}/${p.name}-ref.png vs ${OUT}/${p.name}-wp.png`);
}
await browser.close();
console.log('对比图已生成,逐页人工核对视觉一致性。');
```

- [ ] **Step 2: 安装 Playwright 浏览器并运行对比**

Run:
```bash
cd /root/mizuki
npx playwright install chromium
# 确保 Mizuki 基准站点在 4321(Task 0.2 Step4),WP 在 8888
node tools/visual-compare.mjs
```
Expected: `tools/visual-out/` 生成 `*-ref.png` 与 `*-wp.png`。

- [ ] **Step 3: 逐页人工核对**

打开 `tools/visual-out/home-ref.png` 与 `home-wp.png` 并排对比:顶栏、Banner、卡片排版、配色、字体一致即通过。记录差异点,回到对应 Task(1.2 资源 / 1.3 外壳 / 1.4 卡片 / 1.8 配色)修正,直到视觉一致。

- [ ] **Step 4: 跑 Theme Check**

Theme Check 的编程式接口跨版本不稳定,改用稳定的后台流程:

Run:
```bash
cd /root/mizuki
npm run cli -- plugin activate theme-check
echo "打开 http://localhost:8888/wp-admin/ (admin/password),进入 外观 > Theme Check,选 mizuki,点 Check it!"
```
Expected: 后台 Theme Check 结果页 **REQUIRED 项为 0**;WARNING/RECOMMENDED 逐条评估,与发布相关的(转义、文本域、预留功能)在本阶段或阶段 5 处理。把结果摘要记入提交信息。

- [ ] **Step 5: 提交**

```bash
cd /root/mizuki
git add tools/visual-compare.mjs package.json package-lock.json
git commit -m "test: visual regression harness + Theme Check verification

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## 阶段 1 完成判据(Definition of Done)

- [ ] 主题在 wp-env 中可激活,首页/单篇/归档/搜索/页面/404 全部 HTTP 正常且无 PHP notice/warning。
- [ ] 所有 `assets/*` 资源加载 200,无 404。
- [ ] 首页与归档经 Task 1.9 逐页对比,与 Mizuki 原版**视觉一致**。
- [ ] 配色由 `--hue` 单一变量驱动(§6),`theme.json` 合法。
- [ ] Theme Check 无 REQUIRED 级问题。

## 后续(本计划之外,各出独立计划)

- 阶段 2:Customizer/设置面板(Banner、hue、资料卡)、侧栏 widget 充实、搜索/评论样式深化。
- 阶段 3:特色页 CPT(anime/friend/diary/album/project/skill/timeline)+ 页面模板。
- 阶段 4:交互层(Swup 过渡、Banner 轮播、画廊、KaTeX、代码块、Live2D 开关)按 Task 0.3 岛屿排查结论落地。
- 阶段 5:发布准备(i18n .pot、readme.txt、许可处理、最新 WP/PHP 兼容、跨浏览器与可访问性)。
