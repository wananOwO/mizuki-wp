# 设计规格:将 Mizuki(Astro 静态博客)重制为 WordPress 主题

- 日期:2026-06-18
- 状态:已批准设计,待落实现计划
- 原项目:[LyraVoid/Mizuki](https://github.com/LyraVoid/Mizuki)(Astro 6,Apache-2.0,作者 Matsuzaka Yuki)

## 1. 背景与目标

Mizuki 是一个基于 **Astro 6** 的功能丰富静态博客模板,使用 Tailwind CSS、Svelte 交互岛与 TypeScript,采用 Material Design 3 风格。本项目要把它**重制为一个 WordPress 主题**,使站点可由 WordPress 内容后台驱动,并以**开源形式发布给大众使用**。

### 确定的需求约束

| 维度 | 决定 |
| --- | --- |
| 用途 | 开源发布给别人用 → 遵循主题开发规范、可配置、文档完善 |
| 功能范围 | 核心博客 + 主题外观 + 特色页;搜索/评论用 WP 原生 |
| 还原度 | **视觉效果必须与原版完全一致(像素级)**;代码实现不限 |
| 主题形态 | **混合主题**:经典 PHP 模板渲染 + `theme.json` 管全局色板/样式 |
| 内容迁移 | 不需要;仅交付主题,内容由使用者自行发布 |

### 核心设计思路

达成"视觉完全一致"最可靠的方式,是**复用 Mizuki 构建产物的真实 DOM 结构与编译后 CSS/JS/字体**,PHP 仅负责"在相同结构里填入 WordPress 数据"。因输出的 class 与 DOM 与原版一致,视觉天然一致。

## 2. 范围

### 纳入范围
- 核心博客:文章、分类、标签、归档时间线、首页、关于页、RSS、阅读时间。
- 主题外观:亮暗切换、主色可配(hue)、全屏 Banner 轮播、响应式、平滑过渡。
- 特色页:追番、友链、说说/日记、相册、项目、技能、时间线(以 CPT + 页面模板实现,手动录入)。
- 原生搜索与评论(WordPress 自带能力,套用 Mizuki 样式)。
- 发布合规:i18n、输出转义、Theme Check、文档与署名。

### 不纳入范围(本期)
- 内容迁移工具(Markdown → WP)。
- Pagefind 静态搜索、Twikoo 评论(改用 WP 原生)。
- 追番/追剧从 Bangumi/Bilibili 自动同步(改为手动录入)。
- 全站可视化编辑(Site Editor)的完整支持。

## 3. 开放项(实现前需确认)

1. **许可证**:Mizuki 为 Apache-2.0,与 GPLv3 兼容、与 WordPress.org 要求的 GPLv2 不完全兼容。
   - 默认按 **GitHub 开源(GPLv3)** 推进;若要上 WordPress.org 官方库需另行处理许可。
2. **还原基准来源**:实现第一步需 clone Mizuki 并构建(`pnpm build`),从产物提取真实 HTML 结构与编译资源。

## 4. 架构

混合经典主题,目录结构:

```
mizuki-wp/
├── style.css            # 主题头信息元数据
├── theme.json           # 全局设置:色板/排版/布局/亮暗,映射 Mizuki CSS 变量(--hue 等)
├── functions.php        # 引导:theme supports、入队资源、菜单、CPT、设置面板
├── front-page.php       # 首页:Banner 轮播 + 文章卡片流
├── index.php / home.php # 博客列表
├── single.php           # 单篇:TOC、阅读时间、KaTeX、代码高亮、评论
├── page.php / archive.php / search.php / 404.php
├── header.php / footer.php / sidebar.php / comments.php
├── inc/                 # setup / enqueue / template-tags / customizer / cpt
├── templates/           # 特色页页面模板(anime/friends/diary/albums/projects/skills/timeline)
├── parts/               # 可选:区块模板部件
├── assets/              # 复用 Mizuki 构建产物:css / js / fonts / icons
└── readme.txt / README.md
```

每个单元职责单一、接口清晰:`inc/*` 各文件分别负责注册、入队、模板函数、设置、CPT;`templates/*` 各特色页独立,可单独理解与测试。

## 5. 数据模型映射(Astro → WordPress)

- Astro `posts` 集合 → **WP 文章**:title→post_title、published→post_date、tags→标签、category→分类、description→摘要、image→特色图;扩展项(pinned、lang)→ 文章 meta。
- `about` 等 → **WP 页面** + 页面模板。
- **特色页 → CPT + 元字段**(手动录入):
  - `anime`:封面、状态、评分、链接、进度
  - `friend`:名称、URL、头像、简介
  - `diary`:短文、配图、日期
  - `album` / `project` / `skill`:对应同名页字段
  - 时间线/归档:文章按时间聚合的页面模板

## 6. 主题化与配色

- `theme.json` 定义色板与全局样式,暴露与 Mizuki 一致的 CSS 自定义属性(`--hue` 等);提供"主题色 hue"控件写入该变量,复刻一键换色。
- 亮暗切换:复用 Mizuki 的 JS(切换 class/属性 + localStorage)与同一套 CSS 变量。

## 7. 交互层

- Swup 页面过渡:保留(服务端渲染站点同样可用),按 WP 容器配置。
- Banner 轮播、主题切换、TOC、返回顶部、图片画廊、KaTeX、Expressive-Code:沿用编译后 CSS+JS;Svelte 交互岛编译为独立 JS 挂件挂载或改写为原生 JS。
- 搜索:Pagefind 不可用 → WordPress 原生搜索(`search.php`)。
- Live2D:`l2d-widget` 作为可开关 JS 挂件入队。

## 8. functions.php 职责

- `add_theme_support`:post-thumbnails、title-tag、html5、custom-logo、automatic-feed-links、editor-styles、responsive-embeds、align-wide。
- 注册导航菜单、widget 区(资料卡、分类、标签、公告)。
- 注册特色页 CPT 与分类法。
- 入队编译后 CSS/JS/字体(带版本号与正确依赖)。
- 设置面板:Banner 图与轮播、主题色 hue、个人资料、Live2D 开关等。

## 9. 错误处理与发布健壮性

- 所有输出转义/净化(`esc_html`/`esc_url`/`esc_attr`/`wp_kses_post`);所有设置项输入净化。
- 全量 i18n:文本域 `mizuki`、gettext 包裹、提供 `.pot`。
- 所有函数/句柄加前缀防冲突;无 PHP notice/warning。
- 优雅降级:无特色图→占位图;特色页 CPT 为空→友好空状态。
- 遵循 WPCS 与主题审核要求;`readme.txt` 含许可与对原作者署名。

## 10. 测试与验收

- **Theme Check 插件**:通过 WordPress.org 主题自动检查项。
- **官方 Theme Unit Test 数据**:导入测试 XML,验证各模板类型正确渲染。
- **视觉回归(核心验收)**:相同内容下在 Mizuki 原版与 WP 主题分别截图,逐页对比(首页 Banner、文章卡片、单篇、归档时间线、特色页),确认像素级一致。
- **环境兼容**:最新 WordPress + PHP 8.x;无 PHP notice、无浏览器控制台报错;响应式与亮暗检查;跨浏览器抽查。

## 11. 分阶段实现

0. clone Mizuki + 构建,提取还原基准;搭建 WP 本地环境(wp-env/Local)。
1. 主题骨架 + 入队资源 → 核心博客像素级一致。
2. 主题化(hue/亮暗)、侧栏/widget、原生搜索、404、评论。
3. 特色页 CPT + 模板。
4. 交互层打磨(Swup、轮播、画廊、KaTeX、代码块、Live2D)。
5. 发布准备:Theme Check、i18n、readme/文档、许可处理、兼容性测试。
