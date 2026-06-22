#!/usr/bin/env bash
# 将本地修复后的主题包部署到远程站点 118.31.67.212:8080
set -euo pipefail

REMOTE="118.31.67.212"
PASS="20071104qqprQTQ"
ZIP="/root/mizuki/mizuki-wp-theme.zip"
REMOTE_WP_THEMES="/www/wwwroot/118.31.67.212_8080/wp-content/themes"

echo "=== 1) 上传主题包到远程 ==="
sshpass -p "$PASS" scp -P 22 -o StrictHostKeyChecking=no "$ZIP" root@$REMOTE:/tmp/mizuki-wp-theme.zip

echo "=== 2) 远程备份当前主题 + 解压新包 ==="
sshpass -p "$PASS" ssh -p 22 -o StrictHostKeyChecking=no root@$REMOTE bash <<'ENDSSH'
set -e
cd /www/wwwroot/118.31.67.212_8080/wp-content/themes
# 备份当前主题(如果存在)
if [ -d mizuki-wp ]; then
  timestamp=$(date +%Y%m%d_%H%M%S)
  mv mizuki-wp "mizuki-wp.bak.$timestamp"
  echo "旧主题已备份为 mizuki-wp.bak.$timestamp"
fi
# 解压新包
unzip -q /tmp/mizuki-wp-theme.zip -d .
rm /tmp/mizuki-wp-theme.zip
echo "新主题已部署到 $PWD/mizuki-wp"

# 验证关键文件
for f in inc/customizer.php inc/template-tags.php templates/template-timeline.php; do
  if [ ! -f "mizuki-wp/$f" ]; then
    echo "ERROR: 关键文件 $f 缺失!"
    exit 1
  fi
done
echo "关键文件完整性检查通过"

# 输出修复代码特征验证
echo "=== 验证修复代码是否生效 ==="
grep -c "mizuki_render_category_manager" mizuki-wp/inc/customizer.php || echo "WARNING: customizer 未找到管理面板函数"
grep -c "__none__" mizuki-wp/templates/template-timeline.php || echo "WARNING: timeline 未找到 __none__ 修复"
grep -c "mizuki_get_category_labels" mizuki-wp/inc/template-tags.php || echo "WARNING: template-tags 未找到 helper"
echo "部署完成"
ENDSSH

echo "=== 3) 清理远程 OPcache ==="
sshpass -p "$PASS" ssh -p 22 -o StrictHostKeyChecking=no root@$REMOTE "systemctl reload php-fpm 2>/dev/null || service php-fpm reload 2>/dev/null || echo 'PHP-FPM reload skipped'"

echo ""
echo "✅ 部署完成。请访问 http://118.31.67.212:8080/timeline/ 验证:"
echo "   1) 点击「项目」tab,只有标题为「记一次浏览器自动化实现」的条目应该可见"
echo "   2) 点击「成就」tab,只有标题为「世界,您好!」的条目应该可见"
echo "   3) 点击「全部」tab,两条都应该可见"
echo ""
echo "如需回滚,远程备份在: $REMOTE_WP_THEMES/mizuki-wp.bak.*"
