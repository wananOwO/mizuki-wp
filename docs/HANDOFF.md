# Mizuki WordPress 主题 — 交接文档

## 一、项目结构

```
theme/mizuki-wp/
├── functions.php              # 入口，加载 inc/ 下所有模块
├── header.php                 # 页头：导航栏 + Banner + 主网格开启
├── footer.php                 # 页脚：关闭主网格 + TOC 容器 + Hue 滑块
├── index.php                  # 博客首页/文章列表
├── single.php                 # 单篇文章
├── page.php                   # 单个页面
├── archive.php                # 分类/标签/日期归档
├── search.php                 # 搜索结果
├── 404.php                    # 404 页面
├── comments.php               # 评论模板
├── sidebar.php                # 侧边栏模板（备用，主侧栏在 header.php 中渲染）
├── style.css                  # 仅主题元数据，无 CSS 规则
├── theme.json                 # Gutenberg 编辑器配置
├── inc/
│   ├── setup.php              # 主题支持、菜单、侧边栏、评论回调、hue 输出、body_class 过滤器
│   ├── enqueue.php            # CSS/JS 入队 + 内联导航/分页/搜索样式
│   ├── template-tags.php      # 阅读时间、字数统计辅助函数
│   ├── customizer.php         # Customizer 设置面板 + 前端输出（侧栏个人资料、社交链接、Live2D）
│   └── cpt.php                # 6 个 CPT + 6 个 meta box（含 album/project/skill）
├── templates/
│   ├── template-anime.php     # 追番页面模板
│   ├── template-diary.php     # 说说页面模板
│   ├── template-friends.php   # 友链页面模板
│   └── template-timeline.php  # 时间线页面模板
├── assets/
│   ├── css/                   # 16 个 CSS 文件（从原 Astro 项目编译）
│   ├── js/
│   │   ├── mizuki-theme.js    # 主题交互脚本（亮暗切换、hue、TOC、面板控制）
│   │   ├── customizer-preview.js  # Customizer 实时预览
│   │   └── ...                # 原 Astro 项目的 JS（Swup、TOC 等，大部分未被使用）
│   ├── fonts/                 # ZenMaruGothic + loli 字体
│   └── _astro/                # KaTeX + JetBrains Mono 字体文件
└── languages/
    └── mizuki.pot             # i18n 翻译模板
```

---

## 二、参考项目（原 Mizuki Astro 主题）

路径：`/root/mizuki/reference/mizuki/`

原项目是一个 Astro + Svelte 静态站点生成器主题，主要特性：
- 基于 `--hue` CSS 变量的动态配色系统
- 亮暗模式切换
- 三栏响应式布局（左侧栏 | 主内容 | 右侧 TOC）
- 文章卡片（PostCard）带封面图、标签、元信息
- 追番（AnimeCard）、友链（FriendCard）、说说（MomentCard）、时间线（TimelineCard）等特色页面
- 浮动控制按钮组（FloatingControls）
- 侧边栏小工具系统（Profile、Categories、Tags、Calendar 等）
- Swup 页面过渡
- Fancybox 图片灯箱
- Live2D 看板娘

原项目的关键 CSS 变量定义在 `src/styles/variables.styl` 和 `src/layouts/Layout.astro` 中：
- `--page-width: 90rem`
- `--hue`（从 config 或 localStorage 读取）
- 所有颜色变量定义在 `src/styles/main.css` 的 `:root` 中

---

## 三、已修复问题（2026-06-19）

### ✅ 问题 1：导航栏 "+" 按钮（移动端菜单）不可用

**原因：** 
1. `#nav-menu-panel` 位于 `#navbar` 的 `overflow:hidden` 容器内部，导致 `scaleY(0)` 变换的面板被裁切
2. CSS 中 `.float-panel-closed` 有多个冲突定义，最后的 `scaleY(0) scaleX(.65)` 覆盖了正确的定义
3. JS 中使用 `onclick` 赋值而非 `addEventListener`

**修复：**
- 将 `#nav-menu-panel` 和 `#display-setting` 移到 `#navbar` 外部，使用 `fixed` + `top-[5.25rem]` + `z-50` 定位
- 在 `mizuki-variables.css` 中统一定义 `.float-panel-closed` 和 `.float-panel:not(.float-panel-closed)` 使用 `!important`
- 移除 `mizuki-main.css` 中冲突的 `.float-panel-closed` transform 规则
- JS 改用 `addEventListener` + `stopPropagation`，避免事件冒泡

---

### ✅ 问题 2：特殊功能页面（追番、项目等）在前台无入口

**修复：**
- CPT 从 `public => false` 改为 `public => true` + `publicly_queryable => false` + `exclude_from_search => true`
- 添加了 Album/Project/Skill 三个缺失的 meta box
- 用户仍需在 WordPress 后台手动创建页面并选择模板，然后在菜单中添加链接

---

### ✅ 问题 3：Hue 颜色调节器同时存在两处

**修复：**
- 页脚滑块改为固定在左下角的浮动小组件（`fixed bottom-4 left-4 z-40`），半透明+hover 完全可见
- 面板内的滑块正常显示
- 两个滑块通过 JS 双向同步

---

### ✅ 问题 4：文章卡片完全不可见

**原因：** 
1. `mizuki-main.css` 中 `.wallpaper-transparent .card-base`（不带 `body.` 前缀）使用 `!important` 覆盖了正常 `.card-bg` —— 即使 body 没有 `wallpaper-transparent` class 也会匹配
2. `mizuki-mobile-fix.css` 同样有未限定 body 作用域的规则
3. `body` 没有设置 `background-color: var(--page-bg)`

**修复：**
- 所有 `.wallpaper-transparent .card-base` 规则限定为 `body.wallpaper-transparent .card-base`
- 在 `mizuki-variables.css` 中添加 `body:not(.wallpaper-transparent) .card-base{ background-color: var(--card-bg) !important }` 保护规则
- 添加 `body{ background-color: var(--page-bg) }` 到变量 CSS
- 添加 `--blur-sm: 8px` 变量到 `mizuki-variables.css`

---

### ✅ 问题 5：Customizer 设置无前端输出

**修复：**
- 在 `inc/customizer.php` 中添加 `mizuki_sidebar_profile_widget()` 函数输出侧栏个人资料（头像、昵称、简介、社交链接）
- 通过 `do_action('mizuki_sidebar_before_widgets')` 钩子输出
- 添加 `mizuki_live2d_script()` 函数输出 Live2D 看板娘 JS

---

### ✅ 问题 6：归档/时间线 N+1 查询

**修复：**
- `archive.php` 和 `template-timeline.php` 不再使用额外 `get_posts()` 查询所有文章来计算年度数量
- 改为从当前 `$wp_query->posts` 或 `$timeline_query->posts` 直接统计年份数量

---

### ✅ 问题 7：无 Banner 时内容布局偏移

**修复：**
- `inc/setup.php` 中 `mizuki_body_classes()` 在无 banner 图时添加 `no-banner-mode` class
- CSS 中 `body.no-banner-mode .absolute.w-full.z-30{ top: 5.5rem }` 确保内容区在导航栏下方
- 非首页移动端添加 `mobile-hide-banner` 和 `mobile-main-no-banner` class

---

### ✅ 问题 8：CSS 布局/样式修正

- `index.php` 和 `search.php` 中封面宽度从 `var(--coverWidth)` (calc 不兼容) 改为 `w-[28%]` 硬编码
- 添加 WordPress 导航菜单项样式（内联 CSS in enqueue.php）
- 添加分页导航样式
- 添加搜索表单样式
- 移动端无侧栏时添加 `mobile-no-sidebar` class

---

## 四、本次修改文件清单

| 文件 | 修改内容 |
|------|----------|
| `header.php` | 面板移出 navbar、添加 mobile-hide-banner/mobile-main-no-banner/mobile-no-sidebar class、调用 sidebar before_widgets hook |
| `footer.php` | 移除重复 toc-container、hue 滑块改为固定浮动样式、添加 RSS/Atom 链接 |
| `index.php` | 修复封面宽度 calc 语法（改为 28% 硬编码） |
| `search.php` | 添加阅读时间、封面图支持、搜索结果卡片样式与首页一致 |
| `archive.php` | 修复 N+1 查询（从 wp_query->posts 统计年份） |
| `single.php` | 添加 pb-6 底部内边距、完善 meta-icon transition class |
| `page.php` | 添加 pb-6 底部内边距、markdown-content class |
| `inc/setup.php` | 添加 no-banner-mode body class |
| `inc/enqueue.php` | 添加导航菜单、分页、搜索表单的内联 CSS |
| `inc/customizer.php` | 添加前端输出：侧栏个人资料/社交链接组件、Live2D 看板娘脚本 |
| `inc/cpt.php` | CPT 改为 public=true+publicly_queryable=false、添加 album/project/skill meta box |
| `assets/css/mizuki-variables.css` | 添加 body 页面背景、添加 --blur-sm、统一定义 float-panel-closed/open、添加 non-wallpaper card-bg 保护规则、no-banner 内容区 top 定位 |
| `assets/css/mizuki-main.css` | 修复 wallpaper-transparent .card-base 泄漏（限定 body 作用域）、移除冲突的 float-panel-closed transform 规则 |
| `assets/css/mizuki-mobile-fix.css` | wallpaper-transparent 规则全部限定为 body.wallpaper-transparent 作用域 |
| `assets/js/mizuki-theme.js` | 面板切换改用 addEventListener+stopPropagation、双向同步两个 hue 滑块、closeAllPanels 互斥逻辑、移除 early return 阻断 |
| `sidebar.php` | 添加 mizuki_sidebar_before_widgets hook 调用 |
| `templates/template-timeline.php` | 修复 N+1 查询 |

---

## 五、CSS 变量依赖关系

主题的核心样式依赖以下 CSS 变量（定义在 `assets/css/mizuki-variables.css`）：

```
--page-width: 90rem
--blur-sm: 8px               ← 新增（原仅在 mizuki-main.css Tailwind 层中定义）
--hue: <由 PHP 或 JS 设置>    ← 在 inc/setup.php 中通过 inline <style> 输出
--card-bg: white / oklch()    ← 亮/暗模式不同值
--card-bg-transparent: ...    ← 壁纸透明模式使用
--page-bg: oklch(.95 .01 var(--hue)) / oklch(.16 .014 var(--hue))  ← 新增 body 背景色
--radius-large: 1rem
--primary: oklch(.7 .14 var(--hue))
```

`--hue` 的设置链路：
1. PHP: `inc/setup.php` → `mizuki_output_hue()` → 输出 `<style id="mizuki-hue">:root{--hue:X;--configHue:X;}</style>`
2. JS: `mizuki-theme.js` → `setHue()` → `document.documentElement.style.setProperty('--hue', ...)` + `localStorage`
3. JS: `customizer-preview.js` → 实时修改 `--hue` 和 `#mizuki-hue` style 元素

---

## 六、仍需手动完成的步骤

1. **创建特色页面并分配模板**：在 WordPress 后台创建页面（如"追番"、"友链"等），选择对应页面模板，然后添加到导航菜单
2. **配置侧边栏小工具**：在 外观 > 小工具 中向"左侧边栏"添加分类、标签、日历等小工具
3. **配置 Customizer**：设置头像、昵称、简介、社交链接等
4. **清理未使用 JS**：`assets/js/` 下的 Swup、Layout、FloatingTOC 等文件未被使用，可安全删除以减小主题体积
