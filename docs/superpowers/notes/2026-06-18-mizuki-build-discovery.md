# Mizuki 构建还原基准笔记（Build Discovery）

> Task 0.3 — 为 WordPress 主题移植建立保真基线。
> 目标：WP 模板必须复刻 Mizuki 编译后的 DOM 结构与 class 名，使已编译的 CSS 原样命中、像素级一致。
>
> 取证来源：
> - 编译产物：`reference/mizuki/dist/`（`index.html` / `posts/markdown-tutorial/index.html` / `archive/index.html`，以及 `dist/_astro/`）
> - 源模板（class 名可读，已与编译 HTML 交叉核对）：`reference/mizuki/src/layouts/`、`reference/mizuki/src/components/`、`reference/mizuki/src/config/siteConfig.ts`
>
> 说明：编译 HTML 经过压缩，且 Astro 的 scoped class 会附带 `data-astro-cid-xxxx` 属性（纯样式作用域，WP 复刻时**不需要**这些属性，已编译 CSS 同时也带通用 class 选择器）。结构骨架 class 名以 `.astro` 源为准并已核对。

---

## 1. 关键页面 DOM 结构

### 1.0 全站共享布局外壳（MainGridLayout，所有页面通用）

来源：`src/layouts/MainGridLayout.astro`（编译 HTML 中确认了 `id` 链：`top-row` → `main-grid` → `swup-container` → `content-wrapper`）。

```html
<!-- <html> 带 class="dark"（暗色时）与 data-theme="github-dark|github-light" -->
<body>
  <div id="top-row" class="...">
    <div id="navbar-wrapper">            <!-- 顶部导航容器 -->
      ...Navbar...
    </div>
  </div>

  <Banner .../>                          <!-- id="banner-wrapper"，见 1.1 -->

  <!-- 主网格：max-w-(--page-width) 居中 -->
  <div class="relative max-w-(--page-width) mx-auto pointer-events-auto">
    <div id="main-grid" class="grid grid-cols-[...] ...">
      <div id="banner-credit" ...>...</div>

      <div class="contents">
        <SideBar .../>                   <!-- 左侧栏（侧栏小组件） -->
      </div>

      <main id="swup-container">         <!-- Swup 过渡容器，页面切换时被替换 -->
        <div id="content-wrapper" class="onload-animation ...">
          <div id="page-overlay-data" class="hidden"
               data-title data-date data-category data-words
               data-is-post data-is-home data-wallpaper-mode></div>
          <slot />                       <!-- 各页面正文插入点 -->
        </div>
      </main>
    </div>

    <!-- 大屏右侧浮动 TOC（仅 2xl 显示） -->
    <div class="absolute w-full z-0 hidden 2xl:block">
      <div class="relative max-w-(--page-width) mx-auto">
        <div id="toc-wrapper" class="...">
          <div id="toc-inner-wrapper">
            <div id="toc-container">...</div>
          </div>
        </div>
      </div>
    </div>
    <div id="toc-container" class="hidden" />  <!-- 小屏占位 -->
  </div>
</body>
```

关键变量：`--page-width`（内联设为 `90rem`）控制最大宽度；`onload-animation` 是入场动画 class，几乎所有内容块都带它。

### 1.1 导航栏（Navbar）

来源：`src/components/organisms/navigation/Navbar.astro`

```html
<div id="navbar" class="z-50 onload-animation group"
     data-transparent-mode="semi" data-is-home="true|false">
  <div class="absolute h-8 left-0 right-0 -top-8 bg-[var(--card-bg)] transition"></div>
  <div class="!overflow-visible max-w-[var(--page-width)] h-[4.5rem] mx-auto flex items-center justify-between px-4">
    <a href="/" class="btn-plain scale-animation rounded-lg h-[2.5rem] md:h-[3.25rem] ...">
      <div class="flex flex-row items-center text-md">
        <Icon name="material-symbols:home-pin-outline" .../>
        <span class="dark:text-white text-black">{title}</span>
      </div>
    </a>
    <div id="navbar-links-container" class="hidden md:flex items-center space-x-1 ...">
      ...菜单链接...
    </div>
    <div class="flex items-center gap-1">
      <div id="search-container">...Search 岛...</div>
      <button id="display-settings-switch" class="btn-plain scale-animation rounded-lg h-11 w-11 ...">
        <Icon name="material-symbols:palette-outline"/>   <!-- 主题/色相设置入口 -->
      </button>
      <button id="nav-menu-switch" class="btn-plain ... md:!hidden">...</button>
    </div>
  </div>
</div>
```

### 1.1b Banner（首页大图轮播容器）

来源：`src/components/layout/Banner.astro`

```html
<div id="banner-wrapper" ...>
  <div id="banner-carousel">
    <div class="banner-image-slot-mobile absolute inset-0 block md:hidden">...</div>
    <template class="banner-tpl-mobile">...</template>
    <div class="banner-image-slot-desktop absolute inset-0 hidden md:block">
      <div id="banner">...</div>
    </div>
    <template class="banner-tpl-desktop">...</template>
  </div>
  <div id="banner-single-container" class="relative h-full w-full">...</div>
  <!-- 标题 -->
  <h1 class="banner-title text-6xl lg:text-8xl text-white drop-shadow-lg ...">...</h1>
  <h2 class="banner-subtitle text-xl lg:text-3xl text-white/90 ...">...</h2>
  <div id="banner-page-overlay" class="banner-page-overlay hidden lg:flex absolute inset-0 z-20 ...">...</div>
  <div id="banner-credit" class="...">...</div>
</div>
```

### 1.2 首页 / 列表的文章卡片（PostCard）

来源：`src/components/features/posts/PostCard.astro`。卡片外层 class 为 **`card-base`**（全站卡片基类）。结构骨架：

```html
<!-- 卡片外壳 -->
<div class="card-base flex flex-col-reverse md:flex-col w-full rounded-[var(--radius-large)] overflow-hidden relative">

  <!-- 文字区（有封面时宽度 = 100% - var(--coverWidth) - 0.75rem） -->
  <div class="pl-6 md:pl-9 pr-6 md:pr-2 pt-6 md:pt-7 pb-6 relative w-full md:w-[calc(100%_-_var(--coverWidth)_-_0.75rem)]">

    <!-- 标题（链接到文章） -->
    <a href="{url}" class="transition group w-full block font-bold mb-3 text-3xl text-90
       hover:text-[var(--primary)] before:w-1 before:h-5 before:rounded-md
       before:bg-[var(--primary)] before:absolute ...">
      {title}
      <!-- 右侧 chevron 图标（hover 滑入） -->
    </a>

    <!-- 元信息：发布日期 / 字数 / 阅读时间 / 标签 -->
    <PostMetadata className="mb-4" showOnlyBasicMeta showWordCount words={...} />
    <!-- 编译为：见 1.5 PostMeta 结构（meta-icon + text-50 text-sm） -->

    <!-- 摘要 -->
    <div class="transition text-75 mb-3.5 pr-4 [line-clamp-2 md:line-clamp-1]">
      {homeContent}      <!-- 摘要/正文片段 -->
    </div>

    <!-- 标签行 -->
    <div class="flex flex-wrap gap-2 mt-2">
      <a href="{tagUrl}" class="link-lg transition text-50 text-xs font-medium px-2 py-1 rounded-lg ...">
        <span class="transition-transform group-hover/tag:translate-x-0.5"># {tag}</span>
      </a>
      <!-- 旧样式备选：class="btn-regular h-6 text-xs px-2 rounded-lg" -->
    </div>
  </div>

  <!-- 封面（有 cover 时；--coverWidth 默认 28%） -->
  <a href="{url}" aria-label="{title}"
     class="group max-h-[20vh] md:max-h-none mx-4 mt-4 -mb-2 md:mb-0 md:mx-0 md:mt-0
            md:w-[var(--coverWidth)] relative md:absolute md:top-3 md:bottom-3 md:right-3 rounded-xl overflow-hidden active:scale-95">
    <div class="absolute pointer-events-none z-10 w-full h-full group-hover:bg-black/30 ..."></div>
    <div class="absolute pointer-events-none z-20 w-full h-full flex items-center justify-center">...chevron...</div>
    <img class="w-full h-full" .../>     <!-- Astro <Image>，widths=[200,400,800] -->
  </a>

  <!-- 无封面时：右侧“进入”按钮 -->
  <a href="{url}" class="!hidden md:!flex btn-regular w-[3.25rem] absolute right-3 top-3 bottom-3 rounded-xl
       bg-[var(--enter-btn-bg)] hover:bg-[var(--enter-btn-bg-hover)] ...">...chevron...</a>
</div>

<!-- 卡片间移动端分隔虚线 -->
<div class="transition border-t-[1px] border-dashed mx-6 border-black/10 dark:border-white/[0.15] last:border-t-0 md:hidden"></div>
```

关键 class：`card-base`、`--radius-large`、`--coverWidth`(28%)、`--primary`、`text-90/text-75/text-50`(文字层级灰度)、`link-lg`、`btn-regular`、`onload-animation`。

### 1.3 单篇文章（post / slug）

来源：`src/pages/posts/[...slug].astro`（关键 id 在编译 HTML 中确认：`post-container`、`post-cover`、正文 `markdown-content` + `prose`）。

```html
<div class="flex w-full rounded-[var(--radius-large)] overflow-hidden relative mb-4">
  <div id="post-container" class="card-base z-10 px-6 md:px-9 pt-6 ...">

    <!-- 字数 + 阅读时间 -->
    <div class="flex flex-row text-black/30 dark:text-white/30 gap-5 mb-3 transition onload-animation">
      <div class="flex flex-row items-center"><WordCount .../></div>
      <div class="flex flex-row items-center"><ReadingTime .../></div>
    </div>

    <!-- 标题 -->
    <div class="relative onload-animation">
      <div data-pagefind-meta="title" class="transition w-full block font-bold mb-3 text-3xl md:text-4xl ...">
        {entry.data.title}
      </div>
    </div>

    <!-- 文章元信息（发布/更新日期、标签、分类） -->
    <div class="onload-animation">
      <PostMetadata published updated tags category .../>   <!-- 见 1.5 -->
      <div class="mt-4 border-[var(--line-divider)] border-dashed border-b-[1px] mb-5"></div>
    </div>

    <!-- 封面（如有） -->
    <img id="post-cover" class="mb-8 rounded-xl banner-container onload-animation" .../>

    <!-- 正文主体：prose + markdown-content -->
    <div data-pagefind-body
         class="prose dark:prose-invert prose-base !max-w-none custom-md mb-6 markdown-content onload-animation">
      ...Markdown 渲染后的 HTML（h1/h2/p/pre.expressive-code/katex 等）...
    </div>

    <!-- 版权组件 -->
    <div id="license-component" class="mb-6 rounded-xl license-container onload-animation">...</div>

    <!-- 上一篇/下一篇 等 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">...PostNavigation...</div>
  </div>
</div>
```

正文 wrapper 的精确 class（编译确认，逐字）：
`prose dark:prose-invert prose-base !max-w-none custom-md mb-6 markdown-content onload-animation`，并带 `data-pagefind-body`。

**TOC 结构**（右侧栏 SidebarTOC，自定义元素 `<table-of-contents>`，列表项由 JS 注入）：
来源 `src/components/features/toc/SidebarTOC.astro`
```html
<table-of-contents id="toc" class="group ...">
  <!-- JS 注入的每一项： -->
  <a href="#{id}" class="px-2 flex gap-2 relative transition w-full min-h-9 rounded-xl
       hover:bg-[var(--toc-btn-hover)] active:bg-[var(--toc-btn-active)] py-2">
    <div class="... rounded-lg ... bg-[var(--toc-badge-bg)] text-[var(--btn-content)]">{序号}</div>
    <div class="transition text-sm text-50|text-30">{标题文本}</div>
  </a>
  <div id="active-indicator" class="-z-10 absolute bg-[var(--toc-btn-hover)] ... rounded-xl ..."></div>
</table-of-contents>
```
另有：移动端浮动 TOC `src/components/features/toc/FloatingTOC.astro`（`#floating-toc-btn` / `#floating-toc-panel` / `.floating-toc-item`），以及卡片式 `card-toc`（`#card-toc` → `.toc-scroll-container` → `.toc-content[data-card-toc-content]`，由 `CardTOC` 脚本注入）。WP 移植可任选其一实现。

**评论**：编译后 post 页面同时引用了 `twikoo` 与 `giscus`（按 `siteConfig` 开关二选一）。`twikoo.*.css` 全站加载。

### 1.4 归档（archive）

来源：`src/pages/archive.astro` → `src/components/features/archive/ArchivePanel.svelte`（Svelte 岛；编译后顶部还有 `category-bar`）。

```html
<!-- 顶部分类筛选条（编译确认，所有列表/归档页共用） -->
<div id="category-bar" class="card-base category-bar p-3 onload-animation"
     data-home-path="/" data-archive-path="/archive/">
  <div class="category-bar-inner flex gap-2">
    <a href="/" class="category-pill text-sm px-2 py-1 rounded-lg ..." data-category-name="">...home icon...</a>
    <a href="/archive/" class="category-pill ..." data-category-name="__archive__">Archive <span class="text-xs opacity-60 ml-1">...</span></a>
    ...各分类 pill...
  </div>
</div>

<!-- 时间线面板 -->
<div class="card-base px-8 py-6">
  <!-- 年份分组行 -->
  <div class="flex flex-row w-full items-center h-[3.75rem]">
    <div class="w-[15%] md:w-[10%] transition text-2xl font-bold text-right text-75">{year}</div>
    <div class="w-[15%] md:w-[10%]">
      <div class="h-3 w-3 bg-none rounded-full outline outline-[var(--primary)] mx-auto ..."></div>
    </div>
    <div class="w-[70%] md:w-[80%] transition text-left text-50">{该年文章数}</div>
  </div>

  <!-- 每篇文章一行 -->
  <a href="{postUrl}" class="group btn-plain !block h-10 w-full rounded-lg hover:text-[initial] ...">
    <div class="flex flex-row justify-start items-center h-full">
      <div class="w-[15%] md:w-[10%] transition text-sm text-right text-50">{日期 MM-DD}</div>
      <div class="w-[15%] md:w-[10%] relative dash-line h-full flex items-center">
        <div class="transition-all mx-auto w-1 h-1 rounded group-hover:h-5 ..."></div>   <!-- 时间线点 -->
      </div>
      <div class="w-[70%] md:max-w-[65%] md:w-[65%] text-left font-bold ...">{title}</div>
      <div class="hidden md:block md:w-[15%] text-left text-sm transition ...">{tags/分类}</div>
    </div>
  </a>
</div>
```

时间线核心 class：`card-base`、年份行三段 `w-[15%]/w-[15%]/w-[70%]`、`dash-line`（竖直虚线）、`btn-plain`、`outline-[var(--primary)]`。

---

## 2. 编译后资源清单

### 2.1 CSS（共 15 个；经 grep `dist/index.html` / post / `dist/archive/index.html` 的 `<link>`）

**结论：Astro 把几乎所有 CSS 都打进每个页面的 `<head>`，只有 Expressive Code（`ec.*.css`）是按页加载。**

| 文件（`dist/_astro/`） | 用途 | 首页 | 文章 | 归档 | 分类 |
|---|---|:--:|:--:|:--:|:--:|
| `variables.DodHd1RT.css` | OKLCH 色相→色板派生、`--primary` 等所有主题变量 | ✓ | ✓ | ✓ | **全站必加载** |
| `MainGridLayout.B8XpZmO3.css` | 网格布局、navbar、卡片、按钮基类 | ✓ | ✓ | ✓ | **全站必加载** |
| `markdown.BwSq-11t.css` | Markdown/prose 基础排版 | ✓ | ✓ | ✓ | **全站必加载** |
| `Markdown.CRaqE6DM.css` | Markdown 组件作用域样式（含 @font-face） | ✓ | ✓ | ✓ | **全站必加载** |
| `markdown-extend.DO1wsYky.css` | Markdown 扩展（admonition 等） | ✓ | ✓ | ✓ | **全站必加载** |
| `banner.B27mKpCM.css` | Banner 大图/轮播 | ✓ | ✓ | ✓ | **全站必加载** |
| `katex.CLz4Jbk5.css` | KaTeX 数学公式（含 KaTeX @font-face） | ✓ | ✓ | ✓ | **全站必加载** |
| `fancybox.y3Ble-Pa.css` | 图片灯箱 | ✓ | ✓ | ✓ | **全站必加载** |
| `encrypted-content.CJdg-m7N.css` | 加密文章 | ✓ | ✓ | ✓ | **全站必加载** |
| `mobile-post-list-fix.BME5tngA.css` | 移动端列表修正 | ✓ | ✓ | ✓ | **全站必加载** |
| `transition.CXKxdX36.css` | Swup 页面过渡 | ✓ | ✓ | ✓ | **全站必加载** |
| `twikoo.DkeNYhTP.css` | Twikoo 评论 | ✓ | ✓ | ✓ | **全站必加载** |
| `SidebarTrackInfo.CCdDbRun.css` | 侧栏音乐播放信息 | ✓ | ✓ | ✓ | **全站必加载** |
| `widget-responsive.DZ1QRtFM.css` | 侧栏小组件响应式 | ✓ | ✓ | ✓ | **全站必加载** |
| `ec.5npgr.css` | **Expressive Code 代码块** | ✗ | ✓ | ✗ | **按页加载（仅含代码块的文章）** |

> WP 入队建议（Task 1.2）：14 个标“全站必加载”的 CSS 在 `functions.php` 里无条件 `wp_enqueue_style`；`ec.*.css` 仅在 `is_single()` 且文章含代码块时入队（或简单地在所有单篇文章入队）。

### 2.2 JS（type="module"，经 grep 三类页面的 `<script src>`）

| 文件（`dist/_astro/`） | 用途 | 首页 | 文章 | 归档 |
|---|---|:--:|:--:|:--:|
| `page.B4XSkO9y.js` | **Swup** 页面过渡入口（依赖 Swup* 插件 chunk） | ✓ | ✓ | ✓ |
| `Layout.astro_..._index_0_lang.CdGC7Aho.js` | 布局主脚本：主题/特效/音乐/导航初始化（依赖 effectsConfig/musicConfig/navBarConfig） | ✓ | ✓ | ✓ |
| `Layout.astro_..._index_1_lang.DGJQrGZe.js` | 布局脚本 2 | ✓ | ✓ | ✓ |
| `Layout.astro_..._index_2_lang._R4ib5TO.js` | 布局脚本 3 | ✓ | ✓ | ✓ |
| `CardTOC.astro_..._index_0_lang.Cg5TZbb8.js` | 卡片式 TOC 注入 | ✓ | ✓ | ✓ |
| `FloatingTOC.astro_..._index_0_lang.B-TDumfD.js` | 浮动 TOC | ✓ | ✓ | ✓ |
| `SidebarTOC.astro_..._index_0_lang.FfS9vcvU.js` | 侧栏 TOC | ✓ | ✓ | ✓ |
| `_...slug_.astro_..._index_0_lang.DzjEfclF.js` | 文章页脚本（仅 post） | ✗ | ✓ | ✗ |
| `archive.astro_..._index_0_lang.DzjEfclF.js` | 归档页脚本（仅 archive） | ✗ | ✗ | ✓ |
| `ec.g1fg5.js` | **Expressive Code** 代码块交互（复制/折叠/行号），仅 post | ✗ | ✓ | ✗ |

> 多数 JS 假设由 Astro 的客户端运行时/Swup 调度。WP 移植时这些不能照搬（依赖 Astro hydration 与 Swup）——属第 4 节“岛”范畴，多数需改写原生 JS 或弃用。安全可复用的：`ec.*.js`（代码块复制/折叠，框架无关，建议保留）。

### 2.3 字体

| 字体 | 位置 | 说明 |
|---|---|---|
| **KaTeX**（KaTeX_Main/Math/Size*/AMS/Caligraphic/Fraktur/SansSerif/Script/Typewriter，woff2+woff+ttf） | `dist/_astro/KaTeX_*.{woff2,woff,ttf}` | 由 `katex.*.css` 的 @font-face 引用 |
| **JetBrains Mono**（latin/cyrillic/greek/vietnamese/…，含 italic，woff2） | `dist/_astro/jetbrains-mono-*.woff2` | 代码字体，由 `ec.*.css`/markdown 引用 |
| **ZenMaruGothic-Medium**（正文主字体） | `dist/assets/font/ZenMaruGothic-Medium.ttf` | `src/styles/main.css` @font-face；内联 style 中 `font-family` 首选 |
| **萝莉体 第二版 / loli** | `dist/assets/font/loli.ttf` | 装饰字体 |
| **Roboto** | **未打包** | `--font-sans` 把 `"Roboto"` 列为首选，但 dist 内**无** Roboto 字体文件——依赖系统/外部 web font 回退。WP 移植可忽略或自行通过 Google Fonts 引入。 |

> 路径注意：`_astro/` 下字体走指纹文件名；`assets/font/` 下是原名 TTF。WP 移植时连同 `_astro/`、`assets/font/` 整体拷入主题并保持相对路径（CSS 内引用为绝对路径 `/_astro/...` 与 `/assets/font/...`，入队时需保证主题资源映射到这些路径或改写 CSS 中的 url）。

---

## 3. 主题 / 配色机制

### 3.1 主题色相变量：`--hue`（运行时）与 `--configHue`（SSR 种子），二者都存在

- **`--configHue`**：服务端渲染时写进内联 style（`dist/index.html` 实测：`style="--configHue: 240;--page-width: 90rem; ..."`，同时也注入 `<html>` 上）。它只是“初始种子”。
- **`--hue`**：真正驱动配色的运行时变量。`variables.*.css` 里**所有**颜色都写成 `oklch(L C var(--hue))` 形式（grep 命中大量 `var(--hue))`）。
- 启动流程（`src/layouts/partials/HeadTags.astro` 内联脚本，逐字）：
  ```js
  const hue = themeColorFixed ? configHue : (localStorage.getItem("hue") || configHue);
  document.documentElement.style.setProperty("--hue", hue);
  ```
  即：固定模式用 `configHue`，否则优先读 localStorage 的 `hue`，回退到 `configHue`。
- 用户调色后写入：`src/utils/setting-utils.ts` → `r.style.setProperty("--hue", String(hue))` + `localStorage.setItem("hue", ...)`。

> **WP 结论（Task 1.8）**：在 `<html>`（或 `:root`）写入内联 `--hue`（来自主题选项/`theme.json` 自定义属性，默认 240），让 `variables.*.css` 的 OKLCH 派生自动生效。`--configHue` 可一并写为初始种子，但驱动色板的是 `--hue`。

### 3.2 明暗切换：`.dark` class + `data-theme` 属性，均加在 `<html>`

来源：`src/utils/setting-utils.ts`
```js
document.documentElement.classList.add("dark");      // 暗色
document.documentElement.classList.remove("dark");   // 亮色
document.documentElement.setAttribute("data-theme", "github-dark" | "github-light"); // 供 Expressive Code 代码块换肤
```
- **localStorage 键：`theme`**（值为 `"light"` | `"dark"`；`auto` 时按系统）。读取：`localStorage.getItem("theme") || DEFAULT_THEME`。
- 常量：`src/constants/constants.ts` → `LIGHT_MODE="light"`, `DARK_MODE="dark"`, `DEFAULT_THEME=LIGHT_MODE`。
- 暗色样式在 CSS 里普遍以 `.dark ...` / `dark:` 前缀命中（Tailwind class 策略）。
- 切换还会临时加 `is-theme-transitioning` class 配合 View Transition 动画。

> **WP 结论**：默认主题=亮色；切换时在 `<html>` 加/去 `dark` class，并同步 `data-theme=github-dark|github-light`（仅影响代码块配色），localStorage 键沿用 `theme`。

### 3.3 默认色相与配置位置

- 默认 hue = **240**（蓝色系）。来源 `src/config/siteConfig.ts`：
  ```ts
  themeColor: {
    hue: 240,    // 0–360
    fixed: false // true 时隐藏访客调色器，强制用 configHue
  }
  ```
- 编译 `index.html` 内联 `--configHue: 240` 与之吻合。
- WP 等价物：把 `240` 作为 `theme.json`/主题选项默认值；`fixed` 对应“是否提供前端调色器”。

---

## 4. Svelte 交互岛清单与处置

源码共 50+ 个 `.svelte`，但绝大多数是音乐播放器 / 日历 / 设置面板的原子子组件。下表聚焦真正构成“交互岛”的功能单元（含其挂载/编译产物），并给出 WP 移植处置建议。

| 组件 | 功能 | 依赖 | 处置（编译复用 / 改写原生 JS / WP 原生替代） | 影响阶段 |
|---|---|---|---|---|
| `control/ThemeSwitch.svelte` | 明暗切换按钮 | svelte、`utils/setting-utils`（`setTheme`/`getStoredTheme`）、`@iconify/svelte` | **改写原生 JS**：读/写 localStorage `theme`，切 `<html>.dark` + `data-theme`（逻辑见 §3.2） | 1.3 / 1.8 |
| 色相调色器（`features/settings/DisplaySettings.svelte` + `SettingSlider/SettingsPanel`，入口 `#display-settings-switch`） | 主题色相（hue）滑块 | svelte、`setting-utils`（写 `--hue` + localStorage `hue`） | **改写原生 JS**：滑块改 `--hue` 内联变量并存 localStorage（逻辑见 §3.1）。若隐藏调色器则可省略 | 1.8 |
| `control/LayoutSwitch.svelte` | 列表布局切换（卡片/紧凑） | svelte、localStorage | 改写原生 JS（可选；切 `#post-list-container` 的 class） | 1.4（可选） |
| `organisms/navigation/Search.svelte` | 站内搜索框 | svelte、**Pagefind**（`window.pagefind.search`，PROD 才有索引） | **WP 原生替代**：Pagefind 索引依赖 Astro 构建产物，WP 不可用 → 用 WP 搜索（`get_search_form()` / WP_Query `s=`），复刻搜索 UI class | 1.6 |
| `layout/Banner.astro` 轮播（非 .svelte，但有客户端脚本） | 首页大图轮播 | 原生脚本 + banner.css | **编译复用**（CSS）+ 轮播逻辑可保留/改写原生 JS（框架无关） | 1.3 / 1.4 |
| TOC：`features/toc/SidebarTOC.astro` / `FloatingTOC.astro` / `MobileTOC.svelte` / `card-toc/CardTOC.astro` | 目录生成 + 滚动高亮 | svelte(MobileTOC) / 自定义元素 `<table-of-contents>` + 注入脚本 | **改写原生 JS**：从 `.markdown-content` 的 h1–h6 生成 TOC，scrollspy 高亮（复用其 class：`floating-toc-item` / `#toc` 项样式） | 1.5 |
| 返回顶部（`control/FloatingControls.astro` 内的 back-to-top） | 回到顶部按钮 | 原生脚本 | **改写原生 JS**（几行 scrollTo），复用按钮 class | 1.3 |
| 图片灯箱（`scripts/handlers/fancybox-handler.ts` + fancybox.css） | 文章图片画廊/灯箱 | **Fancybox**（第三方库，框架无关） | **编译复用**：直接引入 fancybox JS/CSS，对 `.markdown-content img` 绑定（与 Astro 无耦合） | 1.5 |
| 音乐播放器（`widgets/music-player/*` + `MusicFabButton.svelte` + `FabMusicPanel.svelte`） | 浮动音乐播放器 | svelte 大量子组件、musicConfig | **改写原生 JS 或弃用**：组件树重，建议 v1 弃用或后续用原生 `<audio>`+少量 JS 重做 | Phase 4（可选） |
| 侧栏音乐（`widgets/music-sidebar/SidebarMusicClient.svelte` + `SidebarTrackInfo.svelte`，CSS=`SidebarTrackInfo.css`） | 侧栏正在播放信息 | svelte、musicConfig | 改写原生 JS 或弃用（同上） | Phase 4（可选） |
| 日历（`widgets/calendar/Calendar.svelte` + 子组件） | 侧栏日历/按日期看文 | svelte | **WP 原生替代或弃用**：可用 WP Calendar widget；否则弃用 | Phase 4（可选） |
| 设置面板（`features/settings/SettingsPanel.svelte` 等） | 显示设置（含调色、特效开关） | svelte | 部分改写原生 JS（调色见上），其余特效开关可弃用 | 1.8 / Phase 4 |
| 归档面板（`features/archive/ArchivePanel.svelte`） | 归档时间线 + 分类筛选交互 | svelte | **WP 原生替代**：用 PHP 直接输出时间线（结构见 §1.4），筛选用普通链接或少量原生 JS | 1.6 |
| 密码保护（`features/auth/PasswordModal.svelte` + `PasswordProtection.astro` + encrypted-content.css） | 加密文章解锁弹窗 | svelte、加密逻辑 | 改写原生 JS 或 **WP 原生替代**（WP 自带 post password） | 1.6 / Phase 4（可选） |
| Live2D 看板娘（`dist/pio/l2d-widget.min.js` + `pio/models`，非 .svelte） | 看板娘 | 独立 `l2d-widget.min.js`（框架无关） | **编译复用**：直接引脚本 + 拷 `pio/` 资源（与框架无关，最易复用） | Phase 4（可选） |
| 评论 Twikoo（`twikoo.css`；post 页含 twikoo + giscus 二选一） | 评论区 | Twikoo SDK（外部）/ Giscus | **保留 Twikoo 或 WP 原生评论替代**：v1 推荐 WP 原生 `comments_template()`；如需沿用社区，挂 Twikoo/Giscus 嵌入脚本即可（与 Astro 无耦合） | 1.5 |
| 分享海报（`misc/SharePoster.svelte`） | 生成分享图 | svelte、canvas | 弃用或后续原生 canvas 重做 | Phase 4（可选） |
| 标签/Chip/Badge（`atoms/*`） | 纯展示原子组件 | svelte | **WP 用 PHP/HTML 复刻 class 即可**（无交互） | 1.4 / 1.6 |

**框架无关、可直接编译复用的资产**（无需 Astro/Svelte 运行时）：
- Expressive Code 代码块：`ec.*.css` + `ec.*.js`（复制/折叠/行号，HTML 已在构建期生成）→ **复用**。
- KaTeX：公式 HTML 与字体在构建期生成，仅需 `katex.*.css` + 字体 → **复用**（WP 端用 `[katex]` 或在 markdown 渲染时预生成）。
- Fancybox、Live2D pio：第三方独立库 → **复用**。

---

## 关键结论速查

- **主题色相变量**：运行时 `--hue`（驱动 OKLCH 色板），SSR 种子 `--configHue`；默认 **240**，配置在 `src/config/siteConfig.ts` 的 `themeColor.hue`。
- **明暗机制**：`<html>` 上的 `.dark` class（+ `data-theme=github-dark|github-light` 供代码块），localStorage 键 **`theme`**（`light`/`dark`，默认 `light`）；色相 localStorage 键 **`hue`**。
- **全站 vs 按页 CSS**：15 个里 **14 个全站必加载**，唯一按页的是 `ec.*.css`（Expressive Code，仅含代码块的文章）。JS 中 `ec.*.js`、`[...slug]` 脚本仅文章页，`archive` 脚本仅归档页。
- **文章卡片**：外壳 `card-base flex flex-col-reverse md:flex-col w-full rounded-[var(--radius-large)] overflow-hidden relative`；标题/摘要/标签/封面结构见 §1.2。
- **布局外壳**：`#top-row` → `Banner(#banner-wrapper)` → `.max-w-(--page-width) > #main-grid` →（SideBar + `<main#swup-container> > #content-wrapper`）+ 右侧 `#toc-wrapper`。
- **正文容器**：`prose dark:prose-invert prose-base !max-w-none custom-md mb-6 markdown-content onload-animation`（带 `data-pagefind-body`）。
- **岛处置三类**：搜索→WP 原生（Pagefind 不可用）；评论→WP 原生或保留 Twikoo/Giscus；主题/色相/TOC/返回顶部→改写原生 JS；Expressive Code/KaTeX/Fancybox/Live2D→编译复用；音乐/日历/海报→可弃用。
