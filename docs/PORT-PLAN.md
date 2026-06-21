# Mizuki WordPress 1:1 复刻 — 执行计划与进度

> 目标:把 WordPress 主题外观改到与原版 Mizuki(Astro)**完全一致**。
> 方针(用户指示):**直接复用 Mizuki 开源代码**(编译产物 HTML 结构 / CSS / 内联 JS),仅做 WordPress 适配。
> 方法:**subagent-driven-development** — 编排者(我)搭好共享地基与脚手架,子代理并行移植各组件,我集中构建+验证。

## 关键诊断(为什么之前"差很多")

1. **Tailwind 类未编译**:PHP 模板用了 `w-[28%]`、`md:w-[72%]`、`rounded-[var(--radius-large)]` 等类,但 `mizuki-main.css` 是 Tailwind JIT 只扫描原 Astro 源码编译的 → 这些类**根本不存在** → 布局塌陷。
   - 解决:用 Tailwind v4 CLI 扫描 WP 的 PHP/JS 重新生成补充工具类 `mizuki-tw-utilities.css`(`tools/tw-build/`)。**每次模板新增类后必须 `bash tools/tw-build/build.sh` 重建。**
2. **运行时 JS 缺失**:Mizuki 用 `<head>` 内联脚本在桌面端设置 `documentElement.style.fontSize`(pageScaling,85%~100%)、明暗 class、`--hue`、banner 高度。WP 端自写的 JS 没有这些 → 整体缩放/配色不对。
   - 解决:复用 Mizuki 原版内联脚本(已抽取到 `/tmp/mizuki-inline/`)。
3. **核心栅格类写错**:`#main-grid` 用了 `2xl:grid-cols-[1fr_min(...)_1fr]`,正确应为 `md:grid-cols-[17.5rem_1fr] lg:grid-cols-[17.5rem_1fr_17.5rem]`(1→2→3 列)。
4. **DOM 结构与原版不一致**:navbar / banner / 侧栏 / 卡片的标签层级、class 与 dist 不同。

## 参考与环境

- 原版编译产物(对照基准 / 真值):`/root/mizuki/reference/mizuki/dist/`,本地服务 `http://localhost:8899`
- 原版源码:`/root/mizuki/reference/mizuki/src/`
- WP 站点:`http://localhost:8888`(docker `wp-manual`)
- 抽取的原版内联脚本:`/tmp/mizuki-inline/inline_*.js`;shell 区域:`/tmp/reg_navbar.html`、`/tmp/reg_banner.html`
- 对照截图工具:`node tools/capture-compare.mjs [pageKey]` → `tools/visual-out/{page}-{ref|wp}-{desktop|mobile}.png`

## 开发循环(子代理务必遵守)

```
编辑 PHP/JS  →  bash tools/tw-build/build.sh  (重建补充工具类)
            →  bash tools/sync-theme.sh       (同步到容器)
            →  node tools/capture-compare.mjs <page>  (对照截图)
```
PHP 改完用 `docker exec wp-manual php -l <file>` 校验语法;JS 用 `node --check`。

## 步骤与状态

- [x] **0. 地基** — 补充工具类重建 + 基线截图 + 根因诊断
- [x] **1. Shell 基础** — `<html>` class/vars、复用 `#03` 引导脚本、`#main-grid` 1→2→3 列(已验证与 ref 完全一致)
- [x] **2. Shell 重构** — header.php 拆分 navbar/banner/侧栏到 `inc/parts/`
- [x] **3. Navbar** — logo + WP 菜单(图标)+ 按钮 + semifull 透明(滚动加 .scrolled)
- [x] **4. Banner** — `#banner` 默认图 + `#header-waves` 波浪 + 标题 overlay(top:-30vh/内容 top:35vh,enable-banner)
- [x] **5. 侧栏组件** — 左:资料卡/公告/分类/标签;右:站点统计/日历
- [x] **6. 文章列表 + 单篇** — index.php 卡片;single/page;TOC 生成
- [x] **7. 特色页** — 友链/追番/说说/时间线/项目/技能(均 200,3 列栅格)
- [x] **8. 运行时 JS** — 透明/主题切换/hue/面板/TOC/回顶/fancybox/pageScaling/暗色;移动端文章置顶
- [x] **9. 终验 + 打包** — 全 PHP `php -l` 通过;JS `node --check` 通过;所有页面 200;重建 mizuki-wp-theme.zip

## 剩余打磨(非阻断,可选)
- 首页分类筛选 tabs(原版主内容上方 Archive/分类 标签)
- single.php 文章 meta 图标(日期/分类/字数)与正文版式细化
- 打字机副标题 + banner 轮播 Ken Burns(静止态外观一致,仅缺动画)
- 原生评论套用 Mizuki 卡片样式

## 范围裁剪(WP 不适用,跳过)

音乐播放器侧栏、Live2D(可选开关)、Swup SPA、pagefind(WP 用自带搜索)、devices/albums 页、mermaid、twikoo。
