<?php
/**
 * 诊断脚本：检查 taxonomy 注册状态
 * 
 * 使用方法：
 * 1. 将此文件上传到 WordPress 根目录
 * 2. 在浏览器中访问：http://你的域名/diagnose-taxonomy.php
 * 3. 查看输出结果
 */

// 加载 WordPress
require_once __DIR__ . '/wp-load.php';

echo "=== Mizuki 主题 Taxonomy 诊断报告 ===\n\n";

// 1. 检查主题是否激活
echo "1. 主题状态：\n";
$theme = wp_get_theme();
echo "   当前主题：{$theme->name} ({$theme->version})\n";
echo "   主题路径：{$theme->get_template_directory()}\n\n";

// 2. 检查 cpt.php 文件是否存在
echo "2. 文件检查：\n";
$cpt_file = $theme->get_template_directory() . '/inc/cpt.php';
echo "   cpt.php 存在：" . (file_exists($cpt_file) ? '是' : '否') . "\n";
if (file_exists($cpt_file)) {
    $content = file_get_contents($cpt_file);
    echo "   cpt.php 大小：" . strlen($content) . " 字节\n";
    echo "   包含 register_taxonomy：" . (strpos($content, 'register_taxonomy') !== false ? '是' : '否') . "\n";
    echo "   包含 skill_category：" . (strpos($content, 'skill_category') !== false ? '是' : '否') . "\n";
}
echo "\n";

// 3. 检查 taxonomy 注册状态
echo "3. Taxonomy 注册状态：\n";
$taxonomies = array('skill_category', 'project_category', 'friend_tag', 'timeline_type', 'anime_status');
foreach ($taxonomies as $tax) {
    $exists = taxonomy_exists($tax);
    echo "   {$tax}：" . ($exists ? '已注册' : '未注册') . "\n";
    if ($exists) {
        $terms = get_terms(array('taxonomy' => $tax, 'hide_empty' => false));
        $count = is_wp_error($terms) ? 0 : count($terms);
        echo "     分类数量：{$count}\n";
    }
}
echo "\n";

// 4. 检查 CPT 注册状态
echo "4. CPT 注册状态：\n";
$cpts = array('mizuki_skill', 'mizuki_project', 'mizuki_friend', 'mizuki_anime', 'mizuki_diary', 'mizuki_album');
foreach ($cpts as $cpt) {
    $obj = get_post_type_object($cpt);
    if ($obj) {
        echo "   {$cpt}：已注册\n";
        echo "     taxonomies：" . implode(', ', $obj->taxonomies) . "\n";
    } else {
        echo "   {$cpt}：未注册\n";
    }
}
echo "\n";

// 5. 检查 mizuki_taxonomy_version 选项
echo "5. 版本选项：\n";
$version = get_option('mizuki_taxonomy_version', '');
echo "   mizuki_taxonomy_version：" . ($version ? $version : '未设置') . "\n";
echo "\n";

// 6. 建议
echo "6. 建议：\n";
if (!taxonomy_exists('skill_category')) {
    echo "   ❌ 自定义 taxonomy 未注册\n";
    echo "   解决方案：\n";
    echo "   1. 确认已导入最新版本的 zip 包\n";
    echo "   2. 在 WordPress 后台 → 外观 → 主题 → 重新激活 Mizuki 主题\n";
    echo "   3. 或者手动运行以下代码：\n";
    echo "      <?php mizuki_create_default_taxonomies(); ?>\n";
} else {
    echo "   ✅ 所有 taxonomy 已正确注册\n";
}

echo "\n=== 诊断完成 ===\n";
