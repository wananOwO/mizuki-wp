# Mizuki WordPress 主题 — 性能优化实施计划

> 目标：实现 300% 综合性能提升
> 基于：PERFORMANCE-REVIEW-REPORT.md 审查报告

---

## 优化任务分解

### Task 1: API 阻塞修复 — WP-Cron 异步 [P0]
**文件**: `inc/api-handlers.php`
**改动**:
1. 新增 `mizuki_schedule_anime_refresh()` — 注册 WP-Cron 事件
2. 新增 `mizuki_cron_refresh_anime()` — Cron 回调，后台刷新数据
3. 移除所有 `sleep()` 调用
4. 前台函数 `mizuki_get_bangumi_data()` / `mizuki_get_bilibili_data()` 只读缓存
5. 缓存未命中时返回空数组 + 触发 cron，不阻塞
**预计提升**: 消除 100+ 秒阻塞风险 (∞% 改善)

### Task 2: 消除 N+1 查询 + 字数统计优化 [P0]
**文件**: `inc/parts/sidebar.php`
**改动**:
1. `mizuki_site_stats_widget()` 中总字数改用 SQL:
   ```php
   $total_words = $wpdb->get_var("SELECT SUM(CHAR_LENGTH(post_content)) FROM $wpdb->posts WHERE post_status='publish' AND post_type='post'");
   ```
2. 保留 12 小时 transient 缓存
3. 在 `save_post` 钩子中清除该 transient
**预计提升**: 消除 500 次 DB 查询 → 1 次

### Task 3: 修复 posts_per_page => -1 [P0]
**文件**: `inc/api-handlers.php:75`
**改动**: `posts_per_page => -1` → `posts_per_page => 200`
**预计提升**: 内存安全

### Task 4: CSS 条件加载 [P1]
**文件**: `inc/enqueue.php`
**改动**:
1. 将全局 CSS 分为 3 组:
   - **核心组** (全站加载): variables, main, tw-utilities, markdown-base, markdown-components, banner, mobile-fix, transition, sidebar-track, widget-responsive, overrides (11 个)
   - **文章页组** (is_single): katex, ec, fancybox, markdown-extend
   - **特色页组** (特定模板): filter-tabs, twikoo, encrypted
2. 重构 `mizuki_enqueue_global_styles()` 只入队核心组
3. 新增 `mizuki_enqueue_post_assets()` 入队文章页组
4. 新增 `mizuki_enqueue_feature_assets()` 入队特色页组
**预计提升**: 首页减少 ~100K CSS + 4 HTTP 请求

### Task 5: JS 条件加载 [P1]
**文件**: `inc/enqueue.php`
**改动**:
1. Fancybox: 仅 `is_single()` 或相册页模板加载
2. filter-handler: 仅特色页模板加载
3. mizuki-theme.js: 提取核心功能（主题切换、回到顶部）和 fancybox 初始化分离
**预计提升**: 首页减少 CDN 请求 + 本地 JS

### Task 6: 所有 CPT 查询添加 no_found_rows [P1]
**文件**: 7 个 template 文件
**改动**: 所有 CPT WP_Query 添加:
```php
'no_found_rows'  => true,
'cache_results'  => true,
'update_post_meta_cache' => true,
'update_term_meta_cache' => false,
```
**预计提升**: 每个查询减少 1 次 FOUND_ROWS 查询

### Task 7: 导航 URL 缓存 [P1]
**文件**: `inc/parts/navbar.php`
**改动**:
1. `mizuki_nav_page_url()` 添加静态缓存:
```php
function mizuki_nav_page_url( $slug ) {
    static $cache = array();
    if ( isset( $cache[ $slug ] ) ) return $cache[ $slug ];
    $page = get_page_by_path( $slug );
    $cache[ $slug ] = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
    return $cache[ $slug ];
}
```
**预计提升**: 减少 7 次重复 DB 查询

### Task 8: 分类/标签查询去重 [P1]
**文件**: `inc/template-tags.php`, `inc/parts/sidebar.php`, `archive.php`
**改动**: 使用全局变量或对象缓存复用分类查询结果
**预计提升**: 减少 2-3 次重复 taxonomy 查询

### Task 9: CPT 注册参数优化 [P2]
**文件**: `inc/cpt.php`
**改动**:
1. `public => false` + `show_ui => true` (语义正确)
2. 添加 `rewrite => false`, `query_var => false`
3. 添加 `show_in_rest => true`
4. 按 CPT 差异化 `supports` 参数
**预计提升**: 消除 6 个无用 rewrite 规则 + 6 个无用 query_var

### Task 10: theme_mod 调用去重 [P2]
**文件**: `inc/setup.php`, `header.php`
**改动**:
1. 在 `functions.php` 中添加 `mizuki_get_theme_configs()` 缓存所有 theme_mod
2. 全局使用缓存值，不重复调用 `get_theme_mod()`
**预计提升**: 减少 5-8 次重复 option 查询

### Task 11: 内联 CSS 提取为文件 [P2]
**文件**: `inc/enqueue.php`
**改动**: 提取 65 行内联 CSS 到 `assets/css/mizuki-wp-nav.css`
**预计提升**: 允许浏览器缓存

### Task 12: save_post 添加 post type 检查 [P2]
**文件**: `inc/cpt.php:159`
**改动**: 早期检查 post type，不匹配时立即返回
**预计提升**: 减少 save_post 处理开销

### Task 13: 清理死代码 JS [P3]
**文件**: `assets/js/` 中的 9+ 未使用文件
**改动**: 删除从未入队的 Astro 编译产物 JS
**预计提升**: 减少主题包体积 ~84K

---

## 实施顺序与依赖

```
Task 1 (API Cron) ──→ 独立
Task 2 (N+1) ──→ 独立
Task 3 (per_page) ──→ 独立
Task 4 (CSS 条件) ──→ 独立
Task 5 (JS 条件) ──→ 依赖 Task 4 (同一个文件)
Task 6 (no_found_rows) ──→ 独立
Task 7 (nav 缓存) ──→ 独立
Task 8 (category 去重) ──→ 独立
Task 9 (CPT 参数) ──→ 独立
Task 10 (theme_mod 缓存) ──→ 独立
Task 11 (内联 CSS) ──→ 依赖 Task 4 (同一文件)
Task 12 (save_post) ──→ 独立
Task 13 (死代码清理) ──→ 独立
```

可并行组:
- **组 A**: Task 1, 2, 3, 6, 7, 8, 9, 10, 12, 13 (全部独立)
- **组 B**: Task 4 → Task 5 → Task 11 (enqueue.php 文件串行)

预计实施时间: 1-2 小时(subagent 并行)
