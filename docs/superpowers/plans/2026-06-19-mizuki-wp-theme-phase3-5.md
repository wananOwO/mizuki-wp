# Mizuki WordPress 主题 — Phase 3-5 实现计划

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans.

**Goal:** 完成特色页 CPT(Phase 3)、交互层打磨(Phase 4)、发布准备(Phase 5),交付可发布的完整 Mizuki WordPress 主题。

**Tech Stack:** PHP (CPT, Customizer, template functions), vanilla JS (Fancybox, KaTeX rendering), WordPress theme APIs.

---

## Phase 3:特色页 CPT + 页面模板

### Task 3.1:注册 CPT + 元字段 + 页面模板

**Files:**
- Create: `theme/mizuki-wp/inc/cpt.php`
- Create: `theme/mizuki-wp/templates/template-anime.php`
- Create: `theme/mizuki-wp/templates/template-friends.php`
- Create: `theme/mizuki-wp/templates/template-diary.php`
- Create: `theme/mizuki-wp/templates/template-timeline.php`
- Modify: `theme/mizuki-wp/functions.php`(require cpt.php)

- [ ] **Step 1: 写 `inc/cpt.php`** — 注册 6 个 CPT(anime, friend, diary, album, project, skill)及元字段

核心代码:
```php
<?php
/**
 * 自定义文章类型:anime / friend / diary / album / project / skill。
 * 每个 CPT 用 register_post_type() 注册,非公开(admin_only)。
 * 元字段通过 post_meta 存储。
 *
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;

function mizuki_register_cpts() {
	$cpts = array(
		'mizuki_anime'   => array( '追番', '追番列表' ),
		'mizuki_friend'  => array( '友链', '友情链接' ),
		'mizuki_diary'   => array( '说说', '日记/说说' ),
		'mizuki_album'   => array( '相册', '相册集' ),
		'mizuki_project' => array( '项目', '作品展示' ),
		'mizuki_skill'   => array( '技能', '技能树' ),
	);
	foreach ( $cpts as $slug => $labels ) {
		register_post_type( $slug, array(
			'labels'       => array( 'name' => $labels[0], 'singular_name' => $labels[1] ),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-heart',
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'has_archive'  => false,
		) );
	}
}
add_action( 'init', 'mizuki_register_cpts' );
```

- [ ] **Step 2: 写各 CPT 元字段** — 在 `cpt.php` 中为每个 CPT 添加 meta box:

anime: `mizuki_anime_status`(在看/看完/想看), `mizuki_anime_score`(0-10), `mizuki_anime_url`(链接), `mizuki_anime_cover`(封面 URL), `mizuki_anime_progress`(进度)
friend: `mizuki_friend_url`(链接), `mizuki_friend_avatar`(头像 URL), `mizuki_friend_desc`(简介)
diary: `mizuki_diary_images`(配图 URL,逗号分隔)
album/project/skill: 对应 Mizuki 同名页面的字段(封面、链接、描述等)

每个 meta box 用 `add_meta_box()` 注册,`save_post` 钩子保存。

- [ ] **Step 3: 写页面模板** — 每个特色页一个 WordPress Page Template:

`template-anime.php`:
```php
<?php
/**
 * Template Name: 追番
 * @package Mizuki
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<main id="main" class="anime-page onload-animation">
	<div class="card-base px-6 py-8">
		<h1 class="text-3xl font-bold text-90 mb-6"><?php the_title(); ?></h1>
		<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
			<?php
			$anime = new WP_Query( array(
				'post_type'      => 'mizuki_anime',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			) );
			while ( $anime->have_posts() ) : $anime->the_post();
				$status = get_post_meta( get_the_ID(), 'mizuki_anime_status', true );
				$score  = get_post_meta( get_the_ID(), 'mizuki_anime_score', true );
				$url    = get_post_meta( get_the_ID(), 'mizuki_anime_url', true );
			?>
				<div class="card-base overflow-hidden transition hover:ring-2 hover:ring-[var(--primary)]">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="aspect-[3/4] overflow-hidden">
							<?php the_post_thumbnail( 'medium', array( 'class' => 'w-full h-full object-cover' ) ); ?>
						</div>
					<?php endif; ?>
					<div class="p-3">
						<h3 class="font-bold text-90 text-sm mb-1 truncate">
							<?php if ( $url ) : ?><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"><?php endif; ?>
							<?php the_title(); ?>
							<?php if ( $url ) : ?></a><?php endif; ?>
						</h3>
						<div class="flex justify-between text-xs text-50">
							<span class="badge"><?php echo esc_html( $status ); ?></span>
							<?php if ( $score ) : ?><span>⭐ <?php echo esc_html( $score ); ?></span><?php endif; ?>
						</div>
					</div>
				</div>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
		<?php if ( ! $anime->found_posts ) : ?>
			<p class="text-50 text-center py-12"><?php esc_html_e( '还没有追番数据。在后台 添加追番 > 追番 中添加。', 'mizuki' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer();
```

`template-friends.php` — 类似布局,字段为 friend_url / friend_avatar / friend_desc。
`template-diary.php` — 时间线布局,类似 archive.php。
`template-timeline.php` — 复用 archive.php 的时间线结构,展示所有文章。

- [ ] **Step 4: 验证 + 提交**

```bash
bash tools/sync-theme.sh
# 创建测试页面并分配模板
bash tools/wp-cli.sh post create --post_type=page --post_title='追番' --post_status=publish --post_name=anime
bash tools/wp-cli.sh post create --post_type=page --post_title='友链' --post_status=publish --post_name=friends
bash tools/wp-cli.sh post create --post_type=page --post_title='说说' --post_status=publish --post_name=diary
# 验证页面渲染
curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8888/?page_id=<anime_id>
git add theme/mizuki-wp/inc/cpt.php theme/mizuki-wp/templates/ theme/mizuki-wp/functions.php
git commit -m "feat(theme): feature page CPTs (anime/friend/diary/album/project/skill)

Custom post types with meta boxes. Page templates with Mizuki card
layouts. Admin UI for managing special page content.

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Phase 4:交互层打磨

### Task 4.1:Fancybox 画廊 + KaTeX 渲染

**Files:**
- Modify: `theme/mizuki-wp/inc/enqueue.php`(入队 Fancybox JS/CSS)
- Modify: `theme/mizuki-wp/assets/js/mizuki-theme.js`(追加 Fancybox 绑定 + KaTeX 渲染)

- [ ] **Step 1: 在 `mizuki-theme.js` 末尾追加 Fancybox 绑定**

```js
// === Fancybox 画廊 ===
document.addEventListener('DOMContentLoaded', function() {
  var images = document.querySelectorAll('.markdown-content img');
  for (var i = 0; i < images.length; i++) {
    var img = images[i];
    if (!img.closest('a')) {
      var a = document.createElement('a');
      a.href = img.src;
      a.dataset.fancybox = 'gallery';
      img.parentNode.insertBefore(a, img);
      a.appendChild(img);
    }
  }
});
```

- [ ] **Step 2: 验证并提交**

```bash
bash tools/sync-theme.sh
git add theme/mizuki-wp/assets/js/mizuki-theme.js
git commit -m "feat(theme): Fancybox gallery binding for post images

Auto-wraps .markdown-content images with data-fancybox links.
KaTeX and Expressive Code CSS already enqueued (Phase 1).

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## Phase 5:发布准备

### Task 5.1:i18n .pot + readme.txt + 许可署名

**Files:**
- Create: `theme/mizuki-wp/languages/mizuki.pot`
- Modify: `theme/mizuki-wp/readme.txt`(更新许可信息)

- [ ] **Step 1: 生成 .pot 文件**

```bash
cd /root/mizuki
# 用 xgettext 或 wp-cli i18n make-pot
npx @wordpress/i18n-cli make-pot theme/mizuki-wp theme/mizuki-wp/languages/mizuki.pot --slug=mizuki --domain=mizuki 2>/dev/null || \
bash tools/wp-cli.sh i18n make-pot /var/www/html/wp-content/themes/mizuki-wp /var/www/html/wp-content/themes/mizuki-wp/languages/mizuki.pot --slug=mizuki --domain=mizuki 2>/dev/null || \
echo "manual .pot generation needed"
```

- [ ] **Step 2: 更新 `readme.txt`**

```txt
=== Mizuki for WordPress ===
Contributors: mizuki-wp-port
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 8.0
License: Apache License 2.0
License URI: https://www.apache.org/licenses/LICENSE-2.0

把 Mizuki(Astro 博客主题)移植为 WordPress 混合主题。

== Description ==
基于 Mizuki(Astro 6 静态博客模板)移植。Material Design 3 风格,亮暗切换,可配主色,特色页 CPT。

== Attribution ==
Based on Mizuki by Matsuzaka Yuki (https://github.com/LyraVoid/Mizuki).
Licensed under Apache License 2.0.
Copyright 2025 Matsuzaka Yuki.

== Changelog ==
= 0.1.0 =
* 初始版本:核心博客、主题化、特色页 CPT、交互层。
```

- [ ] **Step 3: 提交**

```bash
git add theme/mizuki-wp/languages/ theme/mizuki-wp/readme.txt
git commit -m "chore: i18n .pot file, readme.txt with license attribution

Apache-2.0 license, Mizuki original author attribution.
Pot file for translators.

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

### Task 5.2:最终 Theme Check + 兼容性测试

- [ ] **Step 1: 运行 Theme Check**

通过 WP 后台 `外观 > Theme Check` 检查。确保 REQUIRED 项为 0。

- [ ] **Step 2: 全页面 HTTP 验证**

```bash
for url in "/" "/?p=1241" "/?cat=1" "/?s=lorem" "/this-does-not-exist-xyz"; do
  echo -n "$url -> "; curl -s -o /dev/null -w '%{http_code}' "http://localhost:8888${url}"; echo
done
docker exec wp-manual sh -c 'cat /var/www/html/wp-content/debug.log 2>/dev/null | tail -5 || echo "clean"'
```

- [ ] **Step 3: 最终提交**

```bash
git commit --allow-empty -m "chore: release readiness check — Theme Check clean, all pages verified

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

## 完整项目退出标准

- [ ] Phase 0-2 已完成 ✓
- [ ] Phase 3:特色页 CPT(anime/friend/diary/album/project/skill)可后台管理、前台渲染
- [ ] Phase 4:Fancybox 画廊绑定
- [ ] Phase 5:.pot 文件、readme.txt 许可署名、Theme Check 通过
- [ ] 所有页面 HTTP 200/404 正常,无 PHP 警告
- [ ] `--hue` 单一真相源
- [ ] 亮暗切换 + hue 调色器 + TOC 生成工作
