# erphpdown 插件 Mizuki 化重构与功能修复 — 实施计划

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**目标：** 在 Windows 原生环境（无 Docker）搭起本地 WordPress，修复 erphpdown 签到"报成功但不加钱"bug，全面排查插件+主题功能问题并修复，最后把插件全部前端 UI 完全重构为 Mizuki 主题风格。

**架构：** 本地环境 = PHP 8.4 内置服务器 + winget 安装的 MariaDB（独立 datadir，端口 3307）+ wp-cli.phar 脚本化装站，主题/插件用目录联接接入。功能修复直接改插件源码；UI 重构改插件的 CSS 与 HTML 输出，颜色一律走主题 CSS 变量+字面回退，保留全部 JS 功能钩子。

**技术栈：** PHP 8.4 / MariaDB / WordPress 最新中文版 / wp-cli / git-bash + PowerShell（目录联接）

**对应设计文档：** `docs/superpowers/specs/2026-07-20-erphpdown-mizuki-ui-and-fixes-design.md`

## Global Constraints（全局约束）

- 所有面向用户的输出（对话、报告、提交说明正文）一律**中文**。
- 开发机：Windows 11 + git-bash；**禁用 Docker**；PHP 为 winget 版 8.4.22（当前无 php.ini）。
- 数据库：winget 安装 MariaDB；**执行安装命令前必须再次征得用户同意**；数据目录 `e:/code/mizuki-wp/.wp-local/db-data`，端口 **3307**。
- 站点：`http://127.0.0.1:8080`；管理员 `admin / admin123456`；测试用户 `tester / test123456`。
- **不改任何支付业务逻辑**（下单、回调、金额计算）；UI 重构只动外观层。
- HTML 重构必须保留功能钩子：类名 `.erphp-checkin`、`.usercheck`、`.erphpdown-buy`、`.erphpdown-vip-do`、`.erphp-wppay-loader`、`.erphpdown-checkpan`、`.erphpdown-copy`、`.erphp-login-must`、所有 `data-*` 属性、锚点 `id="erphpdown"`；AJAX 的 `action` 名与参数名不变（新增 nonce 参数除外）。
- 颜色/圆角/阴影只用主题 CSS 变量并带字面回退值，例如 `var(--primary, oklch(0.7 0.14 250))`。
- 插件 wp-admin 后台界面**不动**；只重构访客/登录用户可见的前端。
- UI 修改范围限定：`plugins/erphpdown/static/*.css`、`includes/show.php`、`includes/shortcode.php`、`includes/init.php`、`static/erphpdown.js`（仅签到 nonce 与必要换肤）；不动 `admin/`、`payment/`。
- 每个任务独立小步提交，提交信息用中文 conventional 格式（如 `fix(erphpdown): ...`）。
- `.wp-local/`、`tools/local-env/wp-cli.phar` 不入库。
- 仓库根目录已有的 `findings.md`、`progress.md`、`task_plan.md` 等文件属于用户既有资料，**不得覆盖**；本工程排查记录写到 `docs/superpowers/audits/`。

---

## 阶段 0：基线入库与本地环境

### Task 1: plugins/ 原样入库留底

**Files:**
- 无新建；`git add plugins/`

- [ ] **Step 1: 确认 plugins 未被 gitignore**

Run: `cd /e/code/mizuki-wp && git check-ignore plugins/erphpdown/erphpdown.php; echo "exit=$?"`
Expected: 无输出且 `exit=1`（未被忽略）。若被忽略，修改 `.gitignore` 放行 `plugins/`。

- [ ] **Step 2: 提交**

```bash
git add plugins/
git commit -m "chore(erphpdown): 收录 erphpdown v17.1 原始插件代码作为改造基线"
```

### Task 2: 本地环境脚本骨架（php.ini 生成器、router、.gitignore）

**Files:**
- Create: `tools/local-env/make-php-ini.sh`
- Create: `tools/local-env/router.php`
- Modify: `.gitignore`

**Interfaces:**
- Produces: `tools/local-env/php.ini`（由脚本生成，后续所有 php 命令用 `php -c tools/local-env/php.ini` 调用）；`router.php` 供 `php -S` 使用。

- [ ] **Step 1: 写 make-php-ini.sh**

```bash
#!/usr/bin/env bash
# 生成本地开发用 php.ini（扩展目录指向当前 PHP 安装的 ext）
set -euo pipefail
DIR="$(cd "$(dirname "$0")" && pwd)"
PHP_DIR="$(php -r 'echo dirname(PHP_BINARY);')"
mkdir -p /e/code/mizuki-wp/.wp-local
cat > "$DIR/php.ini" <<EOF
[PHP]
memory_limit = 512M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
error_reporting = E_ALL
display_errors = On
log_errors = On
error_log = "e:/code/mizuki-wp/.wp-local/php-error.log"
date.timezone = Asia/Shanghai
extension_dir = "$PHP_DIR/ext"
extension = mysqli
extension = pdo_mysql
extension = curl
extension = gd
extension = mbstring
extension = openssl
extension = zip
extension = fileinfo
extension = intl
extension = sqlite3
extension = pdo_sqlite
EOF
echo "php.ini 已生成: $DIR/php.ini"
php -c "$DIR/php.ini" -m
```

- [ ] **Step 2: 写 router.php**

```php
<?php
// PHP 内置服务器路由：静态文件直出，目录走其 index.php，其余全部交给 WordPress index.php
$root = $_SERVER['DOCUMENT_ROOT'];
$uriPath = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$full = realpath($root . $uriPath);
if ($full !== false && strpos($full, realpath($root)) === 0) {
    if (is_file($full)) {
        return false;
    }
    if (is_dir($full) && is_file($full . '/index.php')) {
        $_SERVER['SCRIPT_NAME'] = rtrim($uriPath, '/') . '/index.php';
        require $full . '/index.php';
        return true;
    }
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $root . '/index.php';
```

- [ ] **Step 3: 更新 .gitignore**

在 `.gitignore` 末尾追加（若无同名条目）：

```
.wp-local/
tools/local-env/wp-cli.phar
tools/local-env/php.ini
```

- [ ] **Step 4: 运行生成器验证扩展加载**

Run: `bash tools/local-env/make-php-ini.sh | grep -E "mysqli|gd|mbstring|intl"`
Expected: 四个扩展名都出现，无 "Unable to load" 警告。

- [ ] **Step 5: 提交**

```bash
git add tools/local-env/make-php-ini.sh tools/local-env/router.php .gitignore
git commit -m "feat(local-env): 本地 PHP 环境脚本（php.ini 生成器与内置服务器路由）"
```

### Task 3: 安装并初始化 MariaDB

**Files:**
- Create: `tools/local-env/setup-db.sh`
- Create: `tools/local-env/start-db.sh`

**Interfaces:**
- Produces: 运行中的 MariaDB（127.0.0.1:3307），库 `wordpress`，账号 `wp / wp123`。

- [ ] **Step 1: 征得用户同意后安装 MariaDB**

先向用户确认一句"现在开始用 winget 安装 MariaDB，可以吗？"，同意后执行：

Run: `winget install --id MariaDB.Server --silent --accept-package-agreements --accept-source-agreements`
Expected: 安装成功；`ls -d "/c/Program Files/MariaDB"*/bin` 能列出 bin 目录。

- [ ] **Step 2: 写 setup-db.sh（初始化数据目录 + 建库建账号）**

```bash
#!/usr/bin/env bash
# 初始化 MariaDB 数据目录并创建 wordpress 库与账号（可重复执行）
set -euo pipefail
BIN="$(ls -d "/c/Program Files/MariaDB"*/bin | head -1)"
DATA="e:/code/mizuki-wp/.wp-local/db-data"
mkdir -p "$DATA"
if [ ! -d "$DATA/mysql" ]; then
  "$BIN/mariadb-install-db.exe" --datadir="$(cygpath -w "$DATA")"
fi
echo "数据目录就绪。请先运行 start-db.sh 再执行本脚本第二段。"
if "$BIN/mariadb.exe" --port=3307 --host=127.0.0.1 -u root -e "SELECT 1" >/dev/null 2>&1; then
  "$BIN/mariadb.exe" --port=3307 --host=127.0.0.1 -u root <<'SQL'
CREATE DATABASE IF NOT EXISTS wordpress DEFAULT CHARACTER SET utf8mb4;
CREATE USER IF NOT EXISTS 'wp'@'localhost' IDENTIFIED BY 'wp123';
CREATE USER IF NOT EXISTS 'wp'@'127.0.0.1' IDENTIFIED BY 'wp123';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wp'@'localhost';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wp'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL
  echo "数据库与账号已就绪 (wp/wp123 @ 127.0.0.1:3307)"
else
  echo "数据库未运行，跳过建库段。"
fi
```

- [ ] **Step 3: 写 start-db.sh**

```bash
#!/usr/bin/env bash
set -euo pipefail
BIN="$(ls -d "/c/Program Files/MariaDB"*/bin | head -1)"
exec "$BIN/mysqld.exe" --datadir="$(cygpath -w "e:/code/mizuki-wp/.wp-local/db-data")" --port=3307 --console
```

- [ ] **Step 4: 初始化并后台启动，验证连接**

Run:
```bash
bash tools/local-env/setup-db.sh          # 第一次：初始化数据目录
bash tools/local-env/start-db.sh &        # 用 Bash 工具 run_in_background 启动
sleep 5 && bash tools/local-env/setup-db.sh   # 第二次：建库建账号
"$(ls -d "/c/Program Files/MariaDB"*/bin | head -1)/mariadb.exe" --port=3307 --host=127.0.0.1 -u wp -pwp123 -e "SHOW DATABASES" | grep wordpress
```
Expected: 输出含 `wordpress`。

- [ ] **Step 5: 提交**

```bash
git add tools/local-env/setup-db.sh tools/local-env/start-db.sh
git commit -m "feat(local-env): MariaDB 初始化与启动脚本（端口3307，独立数据目录）"
```

### Task 4: 安装 WordPress 并接入主题与插件

**Files:**
- Create: `tools/local-env/setup-wp.sh`
- Create: `tools/local-env/start-wp.sh`

**Interfaces:**
- Consumes: Task 2 的 php.ini/router.php，Task 3 的数据库（wp/wp123@127.0.0.1:3307）。
- Produces: 可访问的站点 `http://127.0.0.1:8080`；`WP()` 调用约定 `php -c tools/local-env/php.ini tools/local-env/wp-cli.phar --path=.wp-local/wordpress`；页面"用户中心"（`[erphpdown_sc_user]`）与"VIP会员"（`[erphpdown_sc_vip_page]`）；用户 admin/tester；选项 `ice_ali_money_checkin=1`。

- [ ] **Step 1: 写 setup-wp.sh**

```bash
#!/usr/bin/env bash
# 一键安装本地 WordPress 并接入 Mizuki 主题与 erphpdown 插件（可重复执行）
set -euo pipefail
ROOT="e:/code/mizuki-wp"
ENVDIR="$ROOT/tools/local-env"
WPDIR="$ROOT/.wp-local/wordpress"
WP() { php -c "$ENVDIR/php.ini" "$ENVDIR/wp-cli.phar" --path="$WPDIR" "$@"; }

mkdir -p "$ROOT/.wp-local"
if [ ! -f "$ENVDIR/wp-cli.phar" ]; then
  curl -fL -o "$ENVDIR/wp-cli.phar" https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
fi
if [ ! -f "$WPDIR/wp-load.php" ]; then
  WP core download --locale=zh_CN
fi
if [ ! -f "$WPDIR/wp-config.php" ]; then
  WP config create --dbname=wordpress --dbuser=wp --dbpass=wp123 --dbhost=127.0.0.1:3307 --skip-check
  WP config set WP_DEBUG true --raw
  WP config set WP_DEBUG_LOG true --raw
  WP config set WP_DEBUG_DISPLAY false --raw
fi
if ! WP core is-installed >/dev/null 2>&1; then
  WP core install --url="http://127.0.0.1:8080" --title="Mizuki 本地测试站" \
    --admin_user=admin --admin_password=admin123456 --admin_email=admin@example.com --skip-email
fi
# 目录联接：改仓库代码即时生效
if [ ! -e "$WPDIR/wp-content/themes/mizuki-wp" ]; then
  powershell -NoProfile -Command "New-Item -ItemType Junction -Path '$(cygpath -w "$WPDIR/wp-content/themes/mizuki-wp")' -Target '$(cygpath -w "$ROOT/theme/mizuki-wp")'" >/dev/null
fi
if [ ! -e "$WPDIR/wp-content/plugins/erphpdown" ]; then
  powershell -NoProfile -Command "New-Item -ItemType Junction -Path '$(cygpath -w "$WPDIR/wp-content/plugins/erphpdown")' -Target '$(cygpath -w "$ROOT/plugins/erphpdown")'" >/dev/null
fi
WP theme activate mizuki-wp
WP plugin activate erphpdown
WP user get tester --field=ID >/dev/null 2>&1 || WP user create tester tester@example.com --user_pass=test123456 --role=subscriber
WP option update ice_ali_money_checkin 1
WP post list --post_type=page --fields=post_title | grep -qx '用户中心' || \
  WP post create --post_type=page --post_status=publish --post_title='用户中心' --post_content='[erphpdown_sc_user]'
WP post list --post_type=page --fields=post_title | grep -qx 'VIP会员' || \
  WP post create --post_type=page --post_status=publish --post_title='VIP会员' --post_content='[erphpdown_sc_vip_page]'
echo "== 安装完成: http://127.0.0.1:8080 (admin/admin123456, tester/test123456) =="
```

- [ ] **Step 2: 写 start-wp.sh**

```bash
#!/usr/bin/env bash
set -euo pipefail
ROOT="e:/code/mizuki-wp"
exec php -c "$ROOT/tools/local-env/php.ini" -S 127.0.0.1:8080 -t "$ROOT/.wp-local/wordpress" "$ROOT/tools/local-env/router.php"
```

- [ ] **Step 3: 执行安装**

Run: `bash tools/local-env/setup-wp.sh`
Expected: 末行输出 `== 安装完成 ...`。若 `mariadb-install-db` 生成的 root 无法从 127.0.0.1 连接导致建库失败，回到 Task 3 Step 4 重跑第二段。

- [ ] **Step 4: 后台启动站点并冒烟验证**

Run（start-wp.sh 用 run_in_background 启动后）:
```bash
curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8080/          # 期望 200
curl -s http://127.0.0.1:8080/ | grep -c "mizuki"                       # 期望 ≥1（主题资源已加载）
curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8080/wp-login.php  # 期望 200
```
Expected: 如注释。同时查看 `.wp-local/wordpress/wp-content/debug.log`，记录（不必立即修）插件在 PHP 8.4 下的报错。

- [ ] **Step 5: 提交**

```bash
git add tools/local-env/setup-wp.sh tools/local-env/start-wp.sh
git commit -m "feat(local-env): WordPress 一键安装脚本（wp-cli + 目录联接主题插件）"
```

---

## 阶段 1：签到 bug 修复（TDD）

### Task 5: 编写签到端到端测试并复现 bug

**Files:**
- Create: `tools/local-env/test-checkin.sh`

**Interfaces:**
- Consumes: Task 4 的 WP() 约定、tester 用户、选项 `ice_ali_money_checkin`。
- Produces: 可重复执行的签到断言脚本（返回码 0=通过，1=失败）。

- [ ] **Step 1: 写测试脚本**

```bash
#!/usr/bin/env bash
# 签到端到端测试：断言"接口报成功"与"余额真实增加"必须一致
set -uo pipefail
ROOT="e:/code/mizuki-wp"
ENVDIR="$ROOT/tools/local-env"
WPDIR="$ROOT/.wp-local/wordpress"
WP() { php -c "$ENVDIR/php.ini" "$ENVDIR/wp-cli.phar" --path="$WPDIR" "$@"; }

UID_T=$(WP user get tester --field=ID)
GIFT=$(WP option get ice_ali_money_checkin)

# 清掉今日签到记录，保证可重复执行
WP eval 'global $wpdb; $wpdb->query("DELETE FROM {$wpdb->prefix}checkins WHERE user_id='"$UID_T"'");'

BEFORE=$(WP eval 'global $wpdb; echo (float)$wpdb->get_var("SELECT ice_have_money FROM {$wpdb->prefix}ice_info WHERE ice_user_id='"$UID_T"'");')

RESP=$(WP eval '
wp_set_current_user('"$UID_T"');
$_REQUEST["nonce"] = wp_create_nonce("erphp-checkin");
do_action("wp_ajax_epd_checkin");
' 2>/dev/null)
echo "接口返回: $RESP"

AFTER=$(WP eval 'global $wpdb; echo (float)$wpdb->get_var("SELECT ice_have_money FROM {$wpdb->prefix}ice_info WHERE ice_user_id='"$UID_T"'");')
echo "余额: $BEFORE -> $AFTER (奖励应为 $GIFT)"

OK=$(echo "$RESP" | grep -c '"status":200' || true)
if [ "$OK" -ge 1 ] && php -r "exit(abs(((float)'$AFTER')-((float)'$BEFORE')-((float)'$GIFT')) < 0.001 ? 0 : 1);"; then
  echo "PASS: 报成功且余额确实增加 $GIFT"
  exit 0
fi
echo "FAIL: 接口报成功=$OK，余额变化=$(php -r "echo ((float)'$AFTER')-((float)'$BEFORE');")，期望=$GIFT"
exit 1
```

- [ ] **Step 2: 运行复现 bug（预期失败）**

Run: `bash tools/local-env/test-checkin.sh; echo "exit=$?"`
Expected: `FAIL`、`exit=1` —— 即接口返回 status 200 但余额变化为 0，复现用户报告。
**若在全新环境下意外 PASS**（说明 bug 依赖脏数据/配置）：执行 `WP option update ice_ali_money_checkin "1元"` 后重跑复现（模拟非数字奖励配置），并把实际观察到的根因（`$wpdb->last_error` 内容、`wp_ice_info` 表状态）记入 `docs/superpowers/audits/2026-07-20-erphpdown-audit.md` 的"签到根因"一节；随后把选项恢复为 `1`。修复必须覆盖实测到的根因。

- [ ] **Step 3: 提交测试脚本**

```bash
git add tools/local-env/test-checkin.sh
git commit -m "test(erphpdown): 签到端到端断言脚本，复现报成功但余额不变的缺陷"
```

### Task 6: 修复签到后端 + nonce + 表唯一索引

**Files:**
- Modify: `plugins/erphpdown/includes/functions.erphp.php:779-826`（重写 `epd_checkin_callback`，附近新增迁移函数）
- Modify: `plugins/erphpdown/includes/init.php:24`（localize 增加 nonce）
- Modify: `plugins/erphpdown/static/erphpdown.js:574-577`（签到请求带 nonce）

**Interfaces:**
- Consumes: 既有 `addUserMoney($userId,$money,$why)`（`includes/mobantu.php:1811`，失败返回 false）、`erphpdown_check_checkin($uid)`。
- Produces: 签到接口契约不变（`action=epd_checkin`，返回 `{status,msg}`，200=成功），新增请求参数 `nonce`（动作名 `erphp-checkin`，localize 键 `_ERPHP.checkin_nonce`）；`wp_checkins` 表新增 `create_day` 列与唯一索引 `uniq_user_day(user_id, create_day)`。

- [ ] **Step 1: 重写 `epd_checkin_callback`（替换 functions.erphp.php 现函数全体，含注释掉的旧代码）**

```php
function epd_checkin_callback(){
	global $wpdb;
	if ( ! is_user_logged_in() ) {
		wp_send_json( array( 'status' => 401, 'msg' => '请先登录！' ) );
	}
	if ( ! check_ajax_referer( 'erphp-checkin', 'nonce', false ) ) {
		wp_send_json( array( 'status' => 403, 'msg' => '安全校验失败，请刷新页面后重试。' ) );
	}
	$user_id = get_current_user_id();
	$gift = floatval( get_option( 'ice_ali_money_checkin' ) );
	if ( $gift <= 0 ) {
		wp_send_json( array( 'status' => 201, 'msg' => '抱歉，签到功能已关闭！' ) );
	}
	erphp_checkin_schema_guard();
	if ( erphpdown_check_checkin( $user_id ) ) {
		wp_send_json( array( 'status' => 201, 'msg' => '您今天已经签过到了，明儿再来哦～' ) );
	}
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$inserted = $wpdb->query( $wpdb->prepare(
		"INSERT IGNORE INTO `{$wpdb->checkin}` (user_id, credit, create_time, create_day, user_ip) VALUES (%d, %s, NOW(), CURDATE(), %s)",
		$user_id, (string) $gift, $ip
	) );
	if ( ! $inserted ) {
		wp_send_json( array( 'status' => 201, 'msg' => '您今天已经签过到了，明儿再来哦～' ) );
	}
	$added = function_exists( 'addUserMoney' ) ? addUserMoney( $user_id, $gift, '每日签到奖励' ) : false;
	if ( ! $added ) {
		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->checkin}` WHERE user_id = %d AND create_day = CURDATE()", $user_id ) );
		error_log( '[erphpdown] 签到加钱失败 user=' . $user_id . ' db_error=' . $wpdb->last_error );
		wp_send_json( array( 'status' => 500, 'msg' => '签到失败：奖励发放异常，请联系管理员。' ) );
	}
	wp_send_json( array( 'status' => 200, 'msg' => '签到成功，获得 ' . $gift . ' 余额' ) );
}
add_action( 'wp_ajax_epd_checkin', 'epd_checkin_callback');
```

- [ ] **Step 2: 在同文件 `epd_checkin_callback` 上方新增迁移守卫（幂等，init 时机也挂一份）**

```php
add_action( 'init', 'erphp_checkin_schema_guard' );
function erphp_checkin_schema_guard(){
	if ( get_option( 'erphp_checkin_day_migrated' ) ) return;
	global $wpdb;
	if ( ! $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->checkin ) ) ) return;
	if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->checkin}` LIKE 'create_day'" ) ) {
		$wpdb->query( "ALTER TABLE `{$wpdb->checkin}` ADD COLUMN `create_day` date NULL" );
		$wpdb->query( "UPDATE `{$wpdb->checkin}` SET create_day = DATE(create_time) WHERE create_day IS NULL" );
		$wpdb->query( "DELETE t1 FROM `{$wpdb->checkin}` t1 JOIN `{$wpdb->checkin}` t2 ON t1.user_id = t2.user_id AND t1.create_day = t2.create_day AND t1.ID > t2.ID" );
		$wpdb->query( "ALTER TABLE `{$wpdb->checkin}` ADD UNIQUE KEY `uniq_user_day` (`user_id`, `create_day`)" );
	}
	if ( empty( $wpdb->last_error ) ) update_option( 'erphp_checkin_day_migrated', 1 );
}
```

- [ ] **Step 3: init.php localize 加 nonce（替换第 24 行）**

```php
	wp_localize_script( 'erphpdown', '_ERPHP', array(
		'ajaxurl' => admin_url("admin-ajax.php"),
		'checkin_nonce' => wp_create_nonce( 'erphp-checkin' ),
	));
```

- [ ] **Step 4: erphpdown.js 签到请求带 nonce（erphpdown.js:575-577 处）**

把

```js
		        $.post(_ERPHP.ajaxurl, {
		            "action": "epd_checkin"
		        }, function(result) {
```

改为

```js
		        $.post(_ERPHP.ajaxurl, {
		            "action": "epd_checkin",
		            "nonce": _ERPHP.checkin_nonce
		        }, function(result) {
```

- [ ] **Step 5: 语法检查**

Run: `php -c tools/local-env/php.ini -l plugins/erphpdown/includes/functions.erphp.php && php -c tools/local-env/php.ini -l plugins/erphpdown/includes/init.php`
Expected: 两个 `No syntax errors`。

- [ ] **Step 6: 跑测试验证通过（连跑两次验证防重）**

Run: `bash tools/local-env/test-checkin.sh; echo "exit=$?"`
Expected: `PASS`、`exit=0`。

再运行（不清记录的重复签到检查）:
```bash
WP() { php -c tools/local-env/php.ini tools/local-env/wp-cli.phar --path=.wp-local/wordpress "$@"; }
WP eval 'wp_set_current_user('"$(WP user get tester --field=ID)"'); $_REQUEST["nonce"]=wp_create_nonce("erphp-checkin"); do_action("wp_ajax_epd_checkin");'
```
Expected: 返回 `"status":201` 且 msg 为"您今天已经签过到了…"，余额不再增加。

- [ ] **Step 7: 浏览器侧验证（真实前端链路）**

用 curl 带 cookie 登录 tester 并请求用户中心页确认按钮存在；随后（或验收时）在浏览器登录 tester 点击"今日签到"，弹出"签到成功"，"我的资产"余额 +1。
Run（结构性检查）: `curl -s -b /tmp/cj -c /tmp/cj -d "log=tester&pwd=test123456&wp-submit=登录" http://127.0.0.1:8080/wp-login.php >/dev/null && curl -s -b /tmp/cj "$(WP post list --post_type=page --fields=post_title,url | grep 用户中心 | awk '{print $NF}')" | grep -c "erphp-checkin"`
Expected: `≥1`（未签到时按钮在）。

- [ ] **Step 8: 提交**

```bash
git add plugins/erphpdown/includes/functions.erphp.php plugins/erphpdown/includes/init.php plugins/erphpdown/static/erphpdown.js
git commit -m "fix(erphpdown): 签到奖励发放失败不再假报成功；补 nonce 校验与每日唯一索引"
```

---

## 阶段 2：全面功能排查

### Task 7: 插件用户链路逐项实测并出具问题清单

**Files:**
- Create: `docs/superpowers/audits/2026-07-20-erphpdown-audit.md`

**Interfaces:**
- Produces: 排查报告，格式固定为逐条 `### 问题N：<现象> | 位置 | 根因 | 修复方案 | 严重度(高/中/低)`。

- [ ] **Step 1: 后台配置测试数据（经 wp-admin 或 wp-cli）**

- 用 admin 登录 `http://127.0.0.1:8080/wp-admin/`，在 erphpdown 设置里确认：货币名称、签到奖励=1、勾选文章类型 post。
- 新建 3 篇文章：①"收费下载测试"（编辑页 erphpdown metabox 配一个下载 URL，价格 2 元）；②"收费查看测试"（隐藏内容收费查看，价格 1 元）；③"VIP 专属测试"（仅 VIP 可下载）。
- 给 tester 充值：`WP eval 'addUserMoney(<tester_id>, 50, "测试充值");'`。

- [ ] **Step 2: 逐项实测并记录（每项：操作 → 预期 → 实际 → 数据库核对）**

清单（tester 身份走前台，curl+浏览器结合，关键项查表核对）：
1. 注册新用户（含推广参数 `?aff=<tester_id>` 的注册返利）
2. 登录/登出
3. 签到（已修，回归确认）
4. 用户中心各标签页渲染与翻页（在线充值/升级VIP/购买清单/充值记录/VIP记录/推广注册/推广VIP/申请提现/个人资料）
5. 余额购买"收费下载测试"→ 下载按钮出现 → `wp_ice_money`、`wp_ice_info` 数据正确
6. 收费查看隐藏内容
7. 余额升级 VIP → `wp_ice_vip` 生效、VIP 免费下载
8. 提现申请提交
9. 在线支付通道仅验证到"弹出支付窗/生成订单"为止（无真实商户号，不测回调）
10. `debug.log` 里 PHP 8.4 的 Deprecated/Warning/Fatal 全量收集归类

- [ ] **Step 3: 写入排查报告并提交**

```bash
git add docs/superpowers/audits/2026-07-20-erphpdown-audit.md
git commit -m "docs(audit): erphpdown 插件用户链路排查报告"
```

### Task 8: 主题功能与兼容性排查

**Files:**
- Modify: `docs/superpowers/audits/2026-07-20-erphpdown-audit.md`（追加"主题排查"与"兼容性排查"两节）

- [ ] **Step 1: 主题功能逐项实测**

首页三布局切换、亮暗切换、色相调整、搜索、评论、归档/分类/标签、模板页（友链/追番/相册/时间轴/日记/技能/项目/关于）、移动端视口（375px）关键页面、banner 三模式。

- [ ] **Step 2: 兼容性专项**

1. 浏览器控制台 JS 报错（插件 jQuery 脚本 vs 主题脚本）
2. layui 弹窗与主题浮动面板/导航栏 z-index 叠层
3. 插件页面（用户中心/VIP页）在主题布局下宽度与暗色模式下的可读性截图记录
4. 插件样式是否污染主题元素（如全局 `a`、`table` 选择器）

- [ ] **Step 3: 追加写入报告并提交**

```bash
git add docs/superpowers/audits/2026-07-20-erphpdown-audit.md
git commit -m "docs(audit): 主题功能与插件兼容性排查报告"
```

### Task 9: 问题清单过目 + 逐项修复

**Files:**
- 视清单而定（预期集中在 `plugins/erphpdown/includes/*.php`；主题 bug 落在 `theme/mizuki-wp/`）

- [ ] **Step 1: 把排查报告呈给用户**，中文摘要每个问题一行，等待用户确认修复范围（用户可剔除不修项）。
- [ ] **Step 2: 逐项修复**。每项独立循环：复现（写最小验证命令）→ 最小修复 → 验证命令通过 → `php -l` → 单独提交 `fix(erphpdown|theme): <问题N> <一句话>`。修复时遵守全局约束（不动支付逻辑核心；PHP 8.4 Deprecated 用兼容写法消除）。
- [ ] **Step 3: 全部修完后重跑** `bash tools/local-env/test-checkin.sh` 与阶段 0 冒烟三条 curl，Expected 全部通过；报告中每个问题标注"已修复/保留"。

---

## 阶段 3：UI 完全重构（Mizuki 风格）

> 通用约束回顾：保留功能钩子类名与 `data-*`；新样式类一律 `epd-` 前缀；颜色只用主题变量+回退。每个任务完成后：`php -l` 全部改动文件 + curl 抓页面 grep 新类名 + 浏览器亮/暗两模式目检。

### Task 10: 设计令牌与按钮体系（重写 erphpdown.css 组件段）

**Files:**
- Modify: `plugins/erphpdown/static/erphpdown.css`（保留第 1-2 行 layui 引擎压缩段与 `@font-face`/`.erphp-icon-*` 图标段；其后的全部组件样式整段替换）

**Interfaces:**
- Produces: 令牌类与基础组件类，后续任务直接使用：`.epd-card`（卡片容器）、`.epd-legend`(卡片标题)、按钮体系（主按钮=既有类 `.erphpdown-buy/.erphpdown-vip-do/.erphp-checkin` 等自动应用）、`--epd-*` 变量族。

- [ ] **Step 1: 在图标段之后写入新组件样式（整段替换旧组件 CSS，核心如下，执行时可在此基础上补齐同类选择器）**

```css
/* ===== Mizuki 设计令牌（主题激活时自动跟随主题色/暗色，独立页面走回退值） ===== */
.erphpdown, .erphpdown-see, .erphpdown-content-vip, .erphpdown-box-default,
.erphpdown-sc, .erphpdown-sc-user-wrap, .erphpdown-vip-content,
.layui-layer, .wppay-custom-modal-box {
  --epd-primary: var(--primary, oklch(0.7 0.14 250));
  --epd-btn-content: var(--btn-content, oklch(0.55 0.12 250));
  --epd-btn-bg: var(--btn-regular-bg, oklch(0.95 0.025 250));
  --epd-btn-bg-hover: var(--btn-regular-bg-hover, oklch(0.9 0.05 250));
  --epd-btn-bg-active: var(--btn-regular-bg-active, oklch(0.85 0.08 250));
  --epd-card-bg: var(--card-bg, #ffffff);
  --epd-float-bg: var(--float-panel-bg, #ffffff);
  --epd-text: var(--deep-text, oklch(0.25 0.02 250));
  --epd-meta: var(--content-meta, rgba(0,0,0,.6));
  --epd-line: var(--line-divider, rgba(0,0,0,.08));
  --epd-radius: var(--radius-std, 0.75rem);
  --epd-radius-lg: var(--radius-large, 1rem);
  --epd-shadow: var(--shadow-button, 0 2px 8px rgba(0,0,0,.08));
  color: var(--epd-text);
}

/* ===== 卡片 ===== */
.epd-card{background:var(--epd-card-bg);border-radius:var(--epd-radius-lg);padding:1.25rem 1.5rem;margin:0 0 1.25rem;border:none;position:relative;font-size:.9rem;line-height:1.6;overflow:hidden}
.epd-legend{font-size:1rem;font-weight:700;color:var(--epd-text);margin:0 0 .75rem;display:flex;align-items:center;gap:.5rem}
.epd-legend::before{content:"";width:4px;height:1.1em;border-radius:2px;background:var(--epd-primary)}

/* ===== 按钮体系（既有功能类名直接获得新皮肤） ===== */
.erphpdown-btn,.erphpdown-buy,.erphpdown-buy-index,.erphpdown-vip,.erphpdown-down,
.erphp-login-must,.erphpdown-sc-btn,.erphpdown-see-btn,.erphpdown-vip-do{
  display:inline-flex;align-items:center;justify-content:center;gap:.35rem;
  border-radius:9999px;padding:.5rem 1.15rem;font-size:.875rem;line-height:1;
  border:none;cursor:pointer;text-decoration:none!important;
  background:var(--epd-btn-bg);color:var(--epd-btn-content)!important;
  transition:background .15s ease,transform .1s ease;margin:0 .25rem .25rem 0;
}
.erphpdown-btn:hover,.erphpdown-down:hover,.erphpdown-sc-btn:hover{background:var(--epd-btn-bg-hover);opacity:1}
.erphpdown-btn:active,.erphpdown-down:active,.erphpdown-sc-btn:active{background:var(--epd-btn-bg-active);transform:scale(.97)}
/* 主行动按钮：购买 / 升级VIP / 签到 */
.erphpdown-buy,.erphpdown-buy-index,.erphpdown-vip,.erphpdown-vip-do,.erphp-login-must,.erphpdown-see-btn,.erphp-checkin{
  background:var(--epd-primary);color:#fff!important;box-shadow:var(--epd-shadow);
}
.erphpdown-buy:hover,.erphpdown-vip:hover,.erphpdown-vip-do:hover,.erphp-checkin:hover{filter:brightness(1.08);opacity:1}
.usercheck.active{background:var(--epd-btn-bg);color:var(--epd-meta)!important;box-shadow:none;cursor:default}

/* ===== 文章内购买/下载框 ===== */
.erphpdown,.erphpdown-content-vip{border:none!important;background:var(--epd-card-bg);border-radius:var(--epd-radius-lg)!important;padding:1.25rem 1.5rem!important;margin:0 0 1.25rem!important;box-shadow:var(--epd-shadow)}
.erphpdown > .epd-legend,.erphpdown-title{color:var(--epd-text);font-weight:700;font-size:1rem;margin-bottom:.6rem}
.erphpdown .erphpdown-price{color:var(--epd-primary);font-weight:800;font-size:1.15em;padding:0 .3em}
.erphpdown .erphpdown-tips,.erphpdown .erphpdown-tips-free,.erphpdown-see-tips{color:var(--epd-meta);font-size:.8rem;margin-top:.6rem}
.erphpdown .erphpdown-child{border:1px solid var(--epd-line)!important;border-radius:var(--epd-radius)!important;background:transparent;padding:.9rem 1rem!important;margin:0 0 .75rem!important}
.erphpdown .erphpdown-child > .epd-legend{font-size:.9rem;margin-bottom:.5rem}
.erphpdown .epdvip{position:absolute;top:0;right:0;background:var(--epd-primary);color:#fff;padding:.2rem .6rem;font-size:.72rem;border-radius:0 var(--epd-radius-lg) 0 var(--epd-radius)}
.erphpdown-item{margin-bottom:.4rem}
```

（同段落继续补齐：`.erphpdown-box-default` 系列、`.erphpdown-see-video`、`.erphpdown-free`、复制按钮 `.erphpdown-copy` —— 全部按同一令牌写法。）

- [ ] **Step 2: 验证**

Run: `curl -s http://127.0.0.1:8080/?p=<收费下载测试文章ID> | grep -c "erphpdown"` 且浏览器亮/暗模式目检购买框成为圆角卡片、价格为主题色。
Run: `php -c tools/local-env/php.ini -l plugins/erphpdown/includes/init.php`（未改也跑，习惯性护栏）。
Expected: 页面正常渲染、无样式崩坏。

- [ ] **Step 3: 提交**

```bash
git add plugins/erphpdown/static/erphpdown.css
git commit -m "refactor(erphpdown-ui): 设计令牌与按钮/购买框卡片化，全面接入 Mizuki 主题变量"
```

### Task 11: show.php 购买框 HTML 结构重构（fieldset→div）

**Files:**
- Modify: `plugins/erphpdown/includes/show.php`（约 197、304、309、330 行等全部 fieldset/legend 输出点）

**Interfaces:**
- Consumes: Task 10 的 `.epd-card`、`.epd-legend`。
- Produces: 购买框骨架 `<div class="erphpdown erphpdown-default epd-card" id="erphpdown"><div class="epd-legend">…</div>…</div>`；子块 `<div class="erphpdown-child"><div class="epd-legend">…</div>…</div>`。

- [ ] **Step 1: 仅在 show.php 内做整词替换（Edit replace_all）**

1. `<fieldset class="erphpdown erphpdown-default" id="erphpdown"><legend>` → `<div class="erphpdown erphpdown-default epd-card" id="erphpdown"><div class="epd-legend">`
2. `<fieldset class="erphpdown-child"><legend>` → `<div class="erphpdown-child"><div class="epd-legend">`
3. `</legend>` → `</div>`
4. `</fieldset>` → `</div>`

替换后 grep 确认 show.php 中不再有 `fieldset|legend`：
Run: `grep -cE "fieldset|legend" plugins/erphpdown/includes/show.php`
Expected: `0`。

- [ ] **Step 2: 语法与渲染验证**

Run: `php -c tools/local-env/php.ini -l plugins/erphpdown/includes/show.php`
Run: `curl -s "http://127.0.0.1:8080/?p=<收费下载测试文章ID>" | grep -c "epd-card"`
Expected: `No syntax errors`；`≥1`；浏览器确认购买/下载/复制提取码按钮全部可点、功能与改前一致。

- [ ] **Step 3: 提交**

```bash
git add plugins/erphpdown/includes/show.php
git commit -m "refactor(erphpdown-ui): 购买/下载框改用卡片结构，保留全部功能钩子"
```

### Task 12: 个人中心重构（shortcode.php + profile CSS）

**Files:**
- Modify: `plugins/erphpdown/includes/shortcode.php:69-146`（`erphpdown_sc_user` 的 HTML 骨架）
- Rewrite: `plugins/erphpdown/static/profile/erphpdown.css`

**Interfaces:**
- Consumes: Task 10 令牌与按钮类。
- Produces: 结构 `.erphpdown-sc-user-wrap`(flex 布局) > `.erphpdown-sc-user-aside`(胶囊导航卡片) + `.erphpdown-sc-user-content`(内容卡片)；类名不变，仅内联 style 移除、外观交给 CSS。

- [ ] **Step 1: 改 `erphpdown_sc_user` 输出**

- 签到 li 去掉 `style="padding-top:0"`，两种状态分别输出：
  - 已签到：`<li class="epd-checkin-li"><a href="javascript:;" class="usercheck erphpdown-sc-btn active">✓ 已签到</a></li>`
  - 未签到：`<li class="epd-checkin-li"><a href="javascript:;" class="usercheck erphpdown-sc-btn erphp-checkin">今日签到</a></li>`
- 其余 li/a 结构不变（active 类逻辑保留），`退出` 项保留。

- [ ] **Step 2: 重写 profile/erphpdown.css（全文件替换）**

```css
/* erphpdown 个人中心 — Mizuki 风格 */
.erphpdown-sc-user-wrap{display:flex;gap:1rem;align-items:flex-start;flex-wrap:wrap}
.erphpdown-sc-user-aside{flex:0 0 13rem;list-style:none;margin:0;padding:.75rem;background:var(--epd-card-bg);border-radius:var(--epd-radius-lg);box-shadow:var(--epd-shadow)}
.erphpdown-sc-user-aside li{margin:0 0 .25rem;padding:0;list-style:none}
.erphpdown-sc-user-aside li a{display:flex;align-items:center;padding:.55rem .9rem;border-radius:var(--epd-radius);color:var(--epd-text);text-decoration:none;font-size:.875rem;transition:background .15s ease}
.erphpdown-sc-user-aside li a:hover{background:var(--epd-btn-bg-hover)}
.erphpdown-sc-user-aside li.active > a{background:var(--epd-btn-bg);color:var(--epd-btn-content);font-weight:600}
.erphpdown-sc-user-aside .epd-checkin-li a{justify-content:center}
.erphpdown-sc-user-content{flex:1 1 20rem;min-width:0}
.erphpdown-sc{background:var(--epd-card-bg);border-radius:var(--epd-radius-lg);padding:1.25rem 1.5rem;margin-bottom:1.25rem;box-shadow:var(--epd-shadow)}
.erphpdown-sc h2{font-size:1rem;font-weight:700;margin:0 0 1rem;color:var(--epd-text)}
/* 表格 */
.erphpdown-sc table{width:100%;border-collapse:collapse;font-size:.85rem}
.erphpdown-sc table th{color:var(--epd-meta);font-weight:600;text-align:left;padding:.5rem .6rem;border-bottom:1px solid var(--epd-line)}
.erphpdown-sc table td{padding:.55rem .6rem;border-bottom:1px solid var(--epd-line)}
.erphpdown-sc table tr:last-child td{border-bottom:none}
/* 表单 */
.erphpdown-sc input[type=text],.erphpdown-sc input[type=password],.erphpdown-sc input[type=number],.erphpdown-sc input[type=email],.erphpdown-sc select,.erphpdown-sc textarea{background:transparent;border:1px solid var(--epd-line);border-radius:var(--epd-radius);padding:.5rem .75rem;color:var(--epd-text);font-size:.875rem;outline:none;transition:border-color .15s}
.erphpdown-sc input:focus,.erphpdown-sc select:focus,.erphpdown-sc textarea:focus{border-color:var(--epd-primary)}
/* 移动端：导航横向滚动 */
@media (max-width: 768px){
  .erphpdown-sc-user-aside{flex:1 1 100%;display:flex;overflow-x:auto;gap:.25rem;padding:.5rem}
  .erphpdown-sc-user-aside li{flex:0 0 auto;margin:0}
}
```

- [ ] **Step 3: 验证**

Run: `php -c tools/local-env/php.ini -l plugins/erphpdown/includes/shortcode.php`
Run: 登录 tester 后 curl 用户中心页 `grep -c "epd-checkin-li"` Expected `1`；浏览器逐个点导航标签（充值/VIP/记录/资料…）确认内容与签到按钮功能正常，亮暗两模式目检。

- [ ] **Step 4: 提交**

```bash
git add plugins/erphpdown/includes/shortcode.php plugins/erphpdown/static/profile/erphpdown.css
git commit -m "refactor(erphpdown-ui): 个人中心卡片化布局与胶囊导航，签到按钮主色化"
```

### Task 13: VIP 购买页与记录表格类页面

**Files:**
- Modify: `plugins/erphpdown/includes/shortcode.php`（`erphpdown_sc_vip_page` 的 `vip-item` 结构维持，不动 data-type）
- Modify: `plugins/erphpdown/static/erphpdown.css`（追加 vip 卡片段）

**Interfaces:**
- Consumes: Task 10 令牌。
- Produces: `.erphpdown-vip-content` 网格 + `.vip-item` 定价卡。

- [ ] **Step 1: erphpdown.css 追加**

```css
/* ===== VIP 定价卡 ===== */
.erphpdown-vip-content{display:grid;grid-template-columns:repeat(auto-fit,minmax(11rem,1fr));gap:1rem;margin:0 0 1.25rem}
.erphpdown-vip-content .vip-item{background:var(--epd-card-bg);border:1px solid var(--epd-line);border-radius:var(--epd-radius-lg);padding:1.5rem 1rem;text-align:center;transition:transform .15s ease,box-shadow .15s ease;float:none;width:auto;margin:0}
.erphpdown-vip-content .vip-item:hover{transform:translateY(-4px);box-shadow:var(--epd-shadow)}
.erphpdown-vip-content .vip-item .name{font-size:1rem;font-weight:700;color:var(--epd-text);margin-bottom:.75rem}
.erphpdown-vip-content .vip-item .price{display:block;font-size:1.9rem;font-weight:800;color:var(--epd-primary);margin-bottom:.5rem}
.erphpdown-vip-content .vip-item .price small{font-size:.75rem;color:var(--epd-meta);font-weight:400;margin-left:.2rem}
.erphpdown-vip-content .vip-item .border-decor{color:var(--epd-meta);font-size:.8rem;margin:0 0 1rem;border:none}
.erphpdown-vip-content .vip-item .border-decor span{background:var(--epd-btn-bg);border-radius:9999px;padding:.25rem .8rem}
```

- [ ] **Step 2: 验证**：curl VIP会员页 `grep -c "vip-item"` ≥1；浏览器确认卡片网格、"立即升级"主色按钮、点击仍能弹出升级流程（余额升级路径实测一次）。

- [ ] **Step 3: 提交**

```bash
git add plugins/erphpdown/static/erphpdown.css plugins/erphpdown/includes/shortcode.php
git commit -m "refactor(erphpdown-ui): VIP 定价卡网格化与记录表格 Mizuki 风格"
```

### Task 14: layui 弹窗与 toast 换肤

**Files:**
- Modify: `plugins/erphpdown/static/erphpdown.css`（文件末尾追加 skin 段，覆盖 line 2 引擎默认色）

- [ ] **Step 1: 追加 skin 段**

```css
/* ===== layui 弹窗 Mizuki 皮肤（只覆外观，不动引擎定位/动画） ===== */
.layui-layer{background-color:var(--epd-float-bg);border-radius:var(--epd-radius-lg);box-shadow:0 8px 32px rgba(0,0,0,.18)}
.layui-layer-title{background:transparent;border-bottom:1px solid var(--epd-line);color:var(--epd-text);font-weight:600;border-radius:var(--epd-radius-lg) var(--epd-radius-lg) 0 0}
.layui-layer-content{color:var(--epd-text)}
.layui-layer-btn a{border-radius:9999px;border:1px solid var(--epd-line);background:transparent;color:var(--epd-text)}
.layui-layer-btn .layui-layer-btn0{border-color:transparent;background:var(--epd-primary);color:#fff}
.layui-layer-shade{background-color:#000!important}
/* toast 胶囊 */
.layui-layer-msg{border:none;border-radius:9999px;overflow:hidden}
.layui-layer-hui{background-color:rgba(0,0,0,.75);border-radius:9999px}
.layui-layer-hui .layui-layer-content{padding:10px 22px}
```

- [ ] **Step 2: 验证**：浏览器点签到触发 `layer.msg` 看 toast 胶囊样式；点"立即购买"看弹窗圆角与标题栏；暗色模式弹窗背景应跟随 `--float-panel-bg` 变深。

- [ ] **Step 3: 提交**

```bash
git add plugins/erphpdown/static/erphpdown.css
git commit -m "refactor(erphpdown-ui): layui 弹窗与消息提示 Mizuki 皮肤"
```

### Task 15: 支付弹窗 pay.css 重写

**Files:**
- Rewrite: `plugins/erphpdown/static/pay.css`（保留全部选择器名，逐条重写外观值；`.wppay-custom-modal-box`、`.wppay-modal`、支付方式选择、二维码区、金额区）

- [ ] **Step 1: 通读现有 pay.css 347 行**，列出全部选择器清单；按"布局属性保留、颜色/圆角/边框/阴影替换为 `--epd-*`"重写：模态卡片 `border-radius: var(--epd-radius-lg)`、背景 `var(--epd-float-bg)`、文字 `var(--epd-text)`、支付方式选中态描边 `var(--epd-primary)`、确认按钮同主按钮体系。微信/支付宝品牌图标色不改（品牌色保留）。
- [ ] **Step 2: 验证**：tester 在"收费下载测试"文章点"立即购买"，弹出支付选择窗目检亮暗两模式；订单能生成（`wp_wppay` 或对应表有记录）即为链路未破坏。
- [ ] **Step 3: 提交**

```bash
git add plugins/erphpdown/static/pay.css
git commit -m "refactor(erphpdown-ui): 支付弹窗 Mizuki 皮肤（保留支付逻辑与品牌色）"
```

### Task 16: 独立页面（buy.php / checkout.php / download.php）适配

**Files:**
- Modify: 视审计结果，预期 `plugins/erphpdown/buy.php`、`checkout.php`、`download.php` 的 `<head>` 样式引用段

- [ ] **Step 1: 逐个打开三个文件**，确认各自输出的 HTML 骨架与引用的 CSS；这些页面运行在主题之外（iframe/直链），主题变量不存在 → 令牌回退值生效即可。确保三个页面都引用了重写后的 `static/erphpdown.css` 或 `pay.css`；若引用了未重写的内联样式块，把颜色值替换为与令牌回退一致的字面值。
- [ ] **Step 2: 验证**：浏览器直接访问 `http://127.0.0.1:8080/wp-content/plugins/erphpdown/buy.php?postid=<ID>&timestamp=<ts>`（或走文章内触发 iframe），目检风格统一、按钮可点。
- [ ] **Step 3: 提交** `refactor(erphpdown-ui): 独立购买/下载页令牌回退样式适配`。

### Task 17: 暗色模式与移动端全面核查

**Files:**
- Modify: `plugins/erphpdown/static/erphpdown.css`（如需补 `:root.dark` 特例）

- [ ] **Step 1: 遍历核查**：亮/暗 × 桌面(1280px)/移动(375px) 四种组合，走完：文章购买框、用户中心全部标签、VIP 页、支付弹窗、toast。对比主题原生卡片的对比度；发现刺眼/看不清处，为对应选择器添加 `:root.dark` 下的覆写（只允许用主题暗色变量或降低透明度，不得写死深色 hex）。
- [ ] **Step 2: 回归**：`bash tools/local-env/test-checkin.sh` PASS；三条冒烟 curl 200。
- [ ] **Step 3: 提交** `fix(erphpdown-ui): 暗色与移动端适配修正`。

---

## 阶段 4：回归与验收

### Task 18: 全链路回归

- [ ] **Step 1: 自动检查全绿**

```bash
for f in $(git diff --name-only HEAD~15 -- '*.php'); do php -c tools/local-env/php.ini -l "$f" || exit 1; done
bash tools/local-env/test-checkin.sh
curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8080/
```
Expected: 全部通过 / PASS / 200。

- [ ] **Step 2: 手动链路复走**（tester）：签到→充值余额→购买→下载→VIP→记录页→登出，全部正常。
- [ ] **Step 3: 主题回归**：布局切换、亮暗、色相滑杆调整时插件界面颜色同步变化（这是 UI 重构的核心验收点）。
- [ ] **Step 4: 清理**：确认 `debug.log` 无新增 Fatal/Warning（Deprecated 已在 Task 9 处理范围内）。

### Task 19: 用户浏览器验收

- [ ] **Step 1: 保持 MariaDB 与 php -S 运行**，把访问方式、账号、验收要点清单（中文）发给用户：`http://127.0.0.1:8080`，tester/test123456 走用户链路，admin/admin123456 看后台。
- [ ] **Step 2: 等待用户反馈**；反馈问题按 Task 9 的循环逐项修复并单独提交。

### Task 20: 收尾

- [ ] **Step 1: 更新排查报告**终态（每个问题"已修复/保留"标注齐全）并提交。
- [ ] **Step 2: 汇总提交历史**，向用户中文总结：修了什么、改了什么、怎么再次拉起环境（`start-db.sh` → `start-wp.sh`）。
- [ ] **Step 3: 询问用户**是否需要打包插件 zip（供线上站部署）；需要则 `cd plugins && zip -r ../erphpdown-mizuki-<日期>.zip erphpdown -x "*.git*"`。

---

## 自审记录

- **规格覆盖**：设计文档三部分 → UI 重构=Task 10-17；签到修复=Task 5-6；全面排查=Task 7-9；本地环境=Task 1-4；错误处理/回滚=Task 1 留底+小步提交；测试策略=Task 5 脚本+Task 18 回归。无遗漏。
- **占位符扫描**：Task 9/15/16 的具体修改内容依赖运行时排查结果，属固有的"清单驱动"任务，已给出固定流程、验证命令与提交规范，不属于偷懒占位。
- **类型/接口一致性**：`WP()` 调用约定、`--epd-*` 令牌名、`.epd-card/.epd-legend`、nonce 动作名 `erphp-checkin` 与 localize 键 `checkin_nonce`，在各任务间已核对一致。
