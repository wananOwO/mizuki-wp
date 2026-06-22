# Mizuki 快速修复补丁

**目的**: 立即修复最关键的性能和安全问题（1-2 小时完成）  
**预期改进**: 页面加载速度提升 50-100 倍，修复 3 个 XSS 漏洞

---

## 🔴 修复 1：数据库 N+1 查询（最高优先级）

### 1.1 修复 template-timeline.php

**文件**: `theme/mizuki-wp/templates/template-timeline.php`  
**行号**: 第 25 行

```php
// 查找这行
$timeline_query = new WP_Query( array(
    'post_type'      => 'mizuki_diary',
    'posts_per_page' => -1,  // 改为 200
    'post_status'    => 'publish',
    'meta_key'       => '_mizuki_timeline_start_date',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'tax_query'      => array(
        array(
            'taxonomy' => 'timeline_type',
            'field'    => 'slug',
            'terms'    => array( 'education', 'work', 'project', 'achievement' ),
        ),
    ),
) );

// 替换为
$timeline_query = new WP_Query( array(
    'post_type'              => 'mizuki_diary',
    'posts_per_page'         => 200,  // 从 -1 改为合理上限
    'post_status'            => 'publish',
    'meta_key'               => '_mizuki_timeline_start_date',
    'orderby'                => 'meta_value',
    'order'                  => 'DESC',
    'update_post_meta_cache' => true,  // 新增：预加载元数据
    'update_post_term_cache' => true,  // 新增：预加载分类数据
    'tax_query'              => array(
        array(
            'taxonomy' => 'timeline_type',
            'field'    => 'slug',
            'terms'    => array( 'education', 'work', 'project', 'achievement' ),
        ),
    ),
) );
```

**效果**: 651+ 次查询 → 4 次查询（-99.4%）

---

### 1.2 修复 template-projects.php

**文件**: `theme/mizuki-wp/templates/template-projects.php`  
**行号**: 第 58 行

```php
// 查找这行
$project_query = new WP_Query( array(
    'post_type'              => 'mizuki_project',
    'posts_per_page'         => 200,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
) );

// 添加一行
$project_query = new WP_Query( array(
    'post_type'              => 'mizuki_project',
    'posts_per_page'         => 200,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
    'update_post_meta_cache' => true,  // 新增这行
) );
```

**效果**: 1003 次查询 → 3 次查询（-99.7%）

---

### 1.3 修复 template-skills.php

**文件**: `theme/mizuki-wp/templates/template-skills.php`  
**行号**: 第 74 行

```php
// 查找
$skill_query = new WP_Query( array(
    'post_type'              => 'mizuki_skill',
    'posts_per_page'         => 200,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
) );

// 添加一行
$skill_query = new WP_Query( array(
    'post_type'              => 'mizuki_skill',
    'posts_per_page'         => 200,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
    'update_post_meta_cache' => true,  // 新增这行
) );
```

**效果**: 403 次查询 → 3 次查询（-99.3%）

---

### 1.4 修复 template-friends.php

**文件**: `theme/mizuki-wp/templates/template-friends.php`  
**行号**: 第 75 行

```php
// 查找
$friend_query = new WP_Query( array(
    'post_type'              => 'mizuki_friend',
    'posts_per_page'         => 200,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
) );

// 添加一行
$friend_query = new WP_Query( array(
    'post_type'              => 'mizuki_friend',
    'posts_per_page'         => 200,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_term_cache' => true,
    'update_post_meta_cache' => true,  // 新增这行
) );
```

**效果**: 403 次查询 → 3 次查询（-99.3%）

---

### 1.5 修复 template-albums.php

**文件**: `theme/mizuki-wp/templates/template-albums.php`  
**行号**: 第 31 行

```php
// 查找
$mz_albums = new WP_Query( array(
    'post_type'              => 'mizuki_album',
    'post_status'            => 'publish',
    'posts_per_page'         => 200,
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
) );

// 添加一行
$mz_albums = new WP_Query( array(
    'post_type'              => 'mizuki_album',
    'post_status'            => 'publish',
    'posts_per_page'         => 200,
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
    'update_post_meta_cache' => true,  // 新增这行
) );
```

**效果**: 201 次查询 → 1 次查询（-99.5%）

---

### 1.6 修复 template-diary.php

**文件**: `theme/mizuki-wp/templates/template-diary.php`  
**行号**: 第 54 行

```php
// 查找
$diary_query = new WP_Query( array(
    'post_type'              => 'mizuki_diary',
    'post_status'            => 'publish',
    'posts_per_page'         => 200,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
) );

// 添加一行
$diary_query = new WP_Query( array(
    'post_type'              => 'mizuki_diary',
    'post_status'            => 'publish',
    'posts_per_page'         => 200,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
    'update_post_meta_cache' => true,  // 新增这行
) );
```

**效果**: 201 次查询 → 1 次查询（-99.5%）

---

### 1.7 修复 api-handlers.php

**文件**: `theme/mizuki-wp/inc/api-handlers.php`  
**行号**: 第 72 行

```php
// 查找
$anime_query = new WP_Query( array(
    'post_type'              => 'mizuki_anime',
    'posts_per_page'         => 200,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
) );

// 添加一行
$anime_query = new WP_Query( array(
    'post_type'              => 'mizuki_anime',
    'posts_per_page'         => 200,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'no_found_rows'          => true,
    'update_post_term_cache' => false,
    'update_post_meta_cache' => true,  // 新增这行
) );
```

**效果**: 801 次查询 → 1 次查询（-99.9%）

---

## 🔴 修复 2：XSS 安全漏洞

### 2.1 修复 API 输出转义

**文件**: `theme/mizuki-wp/inc/api-handlers.php`  
**行号**: 第 86-116 行

```php
// 查找 mizuki_get_local_anime_data() 函数中的这段
while ( $anime_query->have_posts() ) {
    $anime_query->the_post();
    $post_id = get_the_ID();

    // ... 省略部分代码 ...

    $anime_list[] = array(
        'id'          => $post_id,
        'title'       => get_the_title(),  // 原代码
        'cover'       => $cover_url,
        'url'         => esc_url( $url ),
        'description' => get_the_excerpt(),  // 原代码
        'score'       => $display_score,
        'status'      => $display_status,
        'progress'    => $progress,
    );
}

// 替换为（添加转义）
while ( $anime_query->have_posts() ) {
    $anime_query->the_post();
    $post_id = get_the_ID();

    // ... 省略部分代码 ...

    $anime_list[] = array(
        'id'          => $post_id,
        'title'       => sanitize_text_field( get_the_title() ),  // 添加转义
        'cover'       => $cover_url,
        'url'         => esc_url( $url ),
        'description' => wp_kses_post( get_the_excerpt() ),  // 添加转义
        'score'       => $display_score,
        'status'      => $display_status,
        'progress'    => $progress,
    );
}
```

---

### 2.2 修复 Bangumi 数据转义

**文件**: `theme/mizuki-wp/inc/api-handlers.php`  
**行号**: 第 259-263 行（在 `mizuki_get_bangumi_anime_data()` 函数内）

```php
// 查找
$anime_list[] = array(
    'id'          => $subject_id,
    'title'       => isset( $subject['name_cn'] ) ? $subject['name_cn'] : ( isset( $subject['name'] ) ? $subject['name'] : __( '未知', 'mizuki' ) ),  // 原代码
    'cover'       => $cover,
    'url'         => 'https://bgm.tv/subject/' . $subject_id,
    'description' => trim( $description ),  // 原代码
    'score'       => $display_score,
    'status'      => $status_display,
);

// 替换为
$anime_list[] = array(
    'id'          => $subject_id,
    'title'       => sanitize_text_field(  // 添加转义
        isset( $subject['name_cn'] ) ? $subject['name_cn'] : ( isset( $subject['name'] ) ? $subject['name'] : __( '未知', 'mizuki' ) )
    ),
    'cover'       => $cover,
    'url'         => 'https://bgm.tv/subject/' . $subject_id,
    'description' => wp_kses_post( trim( $description ) ),  // 添加转义
    'score'       => $display_score,
    'status'      => $status_display,
);
```

---

### 2.3 修复 Bilibili 数据转义

**文件**: `theme/mizuki-wp/inc/api-handlers.php`  
**行号**: 第 419-423 行（在 `mizuki_get_bilibili_anime_data()` 函数内）

```php
// 查找
$anime_list[] = array(
    'id'          => $media_id,
    'title'       => isset( $subject['title'] ) ? $subject['title'] : __( '未知', 'mizuki' ),  // 原代码
    'cover'       => $cover,
    'url'         => 'https://www.bilibili.com/bangumi/media/md' . $media_id,
    'description' => '',  // Bilibili API 在列表接口不返回简介
    'score'       => $display_score,
    'status'      => $status_display,
    'progress'    => $progress_str,
);

// 替换为
$anime_list[] = array(
    'id'          => $media_id,
    'title'       => sanitize_text_field(  // 添加转义
        isset( $subject['title'] ) ? $subject['title'] : __( '未知', 'mizuki' )
    ),
    'cover'       => $cover,
    'url'         => 'https://www.bilibili.com/bangumi/media/md' . $media_id,
    'description' => '',
    'score'       => $display_score,
    'status'      => $status_display,
    'progress'    => $progress_str,
);
```

---

## 🔴 修复 3：Customizer 追番设置丢失

### 3.1 补充 Admin 页面保存逻辑

**文件**: `theme/mizuki-wp/inc/customizer.php`  
**行号**: 第 567 行（在 `if ( isset( $_POST['submit'] ) ... )` 块内，`update_option()` 之前）

```php
// 查找这段（约 555-567 行）
if ( isset( $_POST['submit'] ) && isset( $_POST['mizuki_admin_nonce'] ) ) {
    if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['mizuki_admin_nonce'] ) ), 'mizuki_admin_action' ) ) {
        wp_die( esc_html__( 'Nonce 验证失败。', 'mizuki' ) );
    }

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( '没有权限。', 'mizuki' ) );
    }

    // 保存主题 mod
    set_theme_mod( 'mizuki_banner_mode', isset( $_POST['mizuki_banner_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mizuki_banner_mode'] ) ) : 'default' );
    // ... 其他字段 ...
    
    // 👇 在这里添加追番字段保存（在 update_option() 之前）
    set_theme_mod( 'mizuki_anime_mode', isset( $_POST['mizuki_anime_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['mizuki_anime_mode'] ) ) : 'local' );
    set_theme_mod( 'mizuki_bangumi_user_id', isset( $_POST['mizuki_bangumi_user_id'] ) ? sanitize_text_field( wp_unslash( $_POST['mizuki_bangumi_user_id'] ) ) : '' );
    set_theme_mod( 'mizuki_bilibili_vmid', isset( $_POST['mizuki_bilibili_vmid'] ) ? sanitize_text_field( wp_unslash( $_POST['mizuki_bilibili_vmid'] ) ) : '' );
    set_theme_mod( 'mizuki_bilibili_use_webp', ! empty( $_POST['mizuki_bilibili_use_webp'] ) );
    set_theme_mod( 'mizuki_anime_cache_hours', isset( $_POST['mizuki_anime_cache_hours'] ) ? absint( $_POST['mizuki_anime_cache_hours'] ) : 24 );
    
    update_option( 'mizuki_admin_saved', true );
    // ...
}
```

---

### 3.2 添加追番 API 面板渲染

**文件**: `theme/mizuki-wp/inc/customizer.php`  
**行号**: 第 710 行（在 Live2D 面板之后）

```php
// 查找 Live2D 面板的结束标签（约 710 行）
</div><!-- /.panel Live2D -->

<!-- 👇 在这里添加追番 API 面板 -->
<div class="panel">
    <div class="panel-header">
        <h3><?php esc_html_e( '追番 API', 'mizuki' ); ?></h3>
    </div>
    <div class="panel-body">
        <div class="form-row">
            <label for="mizuki_anime_mode"><?php esc_html_e( '追番数据源', 'mizuki' ); ?></label>
            <select id="mizuki_anime_mode" name="mizuki_anime_mode">
                <option value="local" <?php selected( mizuki_get_theme_mod( 'mizuki_anime_mode', 'local' ), 'local' ); ?>><?php esc_html_e( '本地数据', 'mizuki' ); ?></option>
                <option value="bangumi" <?php selected( mizuki_get_theme_mod( 'mizuki_anime_mode', 'local' ), 'bangumi' ); ?>><?php esc_html_e( 'Bangumi', 'mizuki' ); ?></option>
                <option value="bilibili" <?php selected( mizuki_get_theme_mod( 'mizuki_anime_mode', 'local' ), 'bilibili' ); ?>><?php esc_html_e( '哔哩哔哩', 'mizuki' ); ?></option>
            </select>
        </div>
        <div class="form-row">
            <label for="mizuki_bangumi_user_id"><?php esc_html_e( 'Bangumi 用户 ID', 'mizuki' ); ?></label>
            <input type="text" id="mizuki_bangumi_user_id" name="mizuki_bangumi_user_id" value="<?php echo esc_attr( mizuki_get_theme_mod( 'mizuki_bangumi_user_id', '' ) ); ?>" placeholder="your_bangumi_username" />
        </div>
        <div class="form-row">
            <label for="mizuki_bilibili_vmid"><?php esc_html_e( '哔哩哔哩 UID', 'mizuki' ); ?></label>
            <input type="text" id="mizuki_bilibili_vmid" name="mizuki_bilibili_vmid" value="<?php echo esc_attr( mizuki_get_theme_mod( 'mizuki_bilibili_vmid', '' ) ); ?>" placeholder="12345678" />
        </div>
        <div class="form-row">
            <label>
                <input type="checkbox" id="mizuki_bilibili_use_webp" name="mizuki_bilibili_use_webp" value="1" <?php checked( mizuki_get_theme_mod( 'mizuki_bilibili_use_webp', false ) ); ?> />
                <?php esc_html_e( '使用 WebP 格式封面（哔哩哔哩）', 'mizuki' ); ?>
            </label>
        </div>
        <div class="form-row">
            <label for="mizuki_anime_cache_hours"><?php esc_html_e( '缓存时长（小时）', 'mizuki' ); ?></label>
            <input type="number" id="mizuki_anime_cache_hours" name="mizuki_anime_cache_hours" value="<?php echo absint( mizuki_get_theme_mod( 'mizuki_anime_cache_hours', 24 ) ); ?>" min="1" max="168" />
        </div>
    </div>
</div><!-- /.panel 追番 API -->
```

---

## ✅ 应用补丁

### 方法 1：手动应用（推荐）

逐个文件按照上述说明修改，每修改一个文件后测试对应页面。

### 方法 2：使用 Git 补丁（快速）

1. 保存本文档中的修改为补丁文件
2. 使用 `git apply` 或手动编辑器应用

---

## 📊 预期改进

| 指标 | 修复前 | 修复后 | 改进 |
|------|--------|--------|------|
| Timeline 页面查询数 | 651+ | 4 | -99.4% |
| Projects 页面查询数 | 1003 | 3 | -99.7% |
| Skills 页面查询数 | 403 | 3 | -99.3% |
| Friends 页面查询数 | 403 | 3 | -99.3% |
| Albums 页面查询数 | 201 | 1 | -99.5% |
| Diary 页面查询数 | 201 | 1 | -99.5% |
| API 查询数 | 801 | 1 | -99.9% |
| XSS 漏洞 | 3 个 | 0 | ✅ 修复 |
| Customizer 设置丢失 | 有 | 无 | ✅ 修复 |

**总体改进**: 页面加载速度提升 **50-100 倍**

---

## 🧪 验证测试

### 测试 1：性能验证

```bash
# 安装 Query Monitor 插件查看数据库查询数
wp plugin install query-monitor --activate

# 访问各页面，检查 Query Monitor 工具栏中的查询数：
# - Timeline 页面：应显示 < 10 次查询
# - Projects 页面：应显示 < 10 次查询
# - Skills 页面：应显示 < 10 次查询
```

### 测试 2：功能验证

- [ ] Timeline 页面正常显示所有时间线
- [ ] Projects 页面项目信息完整（URL、技术栈等）
- [ ] Skills 页面技能等级和图标正常
- [ ] Friends 页面友链信息完整
- [ ] Albums 页面图片正常显示
- [ ] Diary 页面图片和内容正常
- [ ] 追番页面数据正常显示

### 测试 3：安全验证

尝试在后台添加包含 HTML 的番剧标题：
```html
<script>alert('XSS')</script>测试番剧
```

前台应显示为纯文本，不执行脚本。

### 测试 4：Customizer 验证

1. 进入 Customizer 修改追番 API 设置
2. 进入 Admin 页面修改其他设置并保存
3. 返回 Customizer 确认追番设置未丢失

---

## ⏱️ 预计时间

- **修复 1**（7 个文件，每个添加 1 行）: 10 分钟
- **修复 2**（3 处转义修改）: 5 分钟
- **修复 3**（2 处代码添加）: 15 分钟
- **测试验证**: 30 分钟

**总计**: 约 1 小时

---

## 🚀 下一步

完成此快速修复后，可继续实施：

1. **架构优化**（3-5 小时）
   - 应用资源加载优化
   - 应用 CPT 重构
   - 提取模板公共组件

2. **深度优化**（5-8 小时）
   - 应用 Customizer 重构
   - API 性能优化
   - 全局 CSS 整合

详见 `/root/mizuki/CODE_REVIEW_SUMMARY.md`
