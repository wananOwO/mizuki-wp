# erphpdown 插件 Mizuki 化重构与功能修复 — 设计文档

日期：2026-07-20
状态：已经用户逐段确认（UI 重构 / 功能修复 / 验证环境三部分均已口头批准）

## 背景与目标

仓库包含 Mizuki WordPress 主题（`theme/mizuki-wp`，Material Design 3 风格，oklch 动态主色，亮暗双模式）与第三方插件 erphpdown v17.1（`plugins/erphpdown`，会员收费下载/充值/VIP/签到插件，模板兔出品）。

三个目标：

1. **UI 完全重构（方案 B）**：插件所有前端（访客/用户可见）界面重构为与 Mizuki 主题原生一致的风格。插件 wp-admin 后台界面不动。
2. **修复签到 bug 并全面排查**：用户实测"签到按钮点击后提示签到成功，但余额并未增加"。修复它，并对插件+主题做全面功能排查，发现的问题一并修复。
3. **兼容性**：插件与主题存在冲突时，改插件来兼容主题（用户已选择"直接改插件"，接受插件升级会覆盖修改的代价）。

交流语言约束：所有面向用户的输出必须使用中文。

## 第 1 部分：UI 完全重构

### 范围（按组件）

| 组件 | 现状 | 重构后 |
|---|---|---|
| 个人中心 `[erphpdown_sc_user]`（签到按钮、左侧导航、各子页） | 老式竖排文字链接导航 | Mizuki 圆角卡片（`card-base`）布局；导航为胶囊按钮组；签到为主色大按钮；移动端导航横向滚动 |
| 文章内购买/下载框（`erphpdown`、`erphpdown-content-vip` 等盒子） | 橙色 `#ff5f33` 虚线 fieldset | 圆角卡片 + 主色价格高亮 + 主题风格按钮（跟随 `--hue` 变化） |
| 支付弹窗（选支付方式、扫码、layui/自绘 modal） | layui 老式白弹窗 | 保留弹窗 JS 引擎（支付逻辑不动），外观全面换肤：大圆角、遮罩、暗色适配 |
| 充值/VIP 购买表单 | 原生 input + 绿色按钮 | 主题风格输入框与按钮 |
| 记录类表格（充值记录、订单、推广、提现、VIP 记录） | 原生 table | 卡片内嵌表格、细分隔线、暗色适配 |
| 提示消息（layer.msg / popup.showToast） | 黑底小方块 | 圆角胶囊 toast |

### 技术约束（保证不改坏功能）

1. **JS 功能钩子类名全部保留**：插件 JS 依赖 `.erphp-checkin`、`.erphpdown-buy`、`.usercheck`、`data-*` 等选择器绑定事件。新 HTML 保留这些类名，新增样式类并行。
2. **颜色一律用主题 CSS 变量**（`var(--primary)`、`var(--card-bg)`、`var(--btn-regular-bg)` 等）并附兜底值；亮暗模式与主题色相自动跟随。主题未激活时插件界面仍可读（兜底值保证）。
3. **表单字段名、AJAX action、请求参数一律不动**，只动外观层。
4. 修改直接落在插件文件内（`static/erphpdown.css`、`static/pay.css`、`static/profile/erphpdown.css`、`includes/shortcode.php`、`includes/show.php` 等输出 HTML 的文件）。
5. 开工前把 `plugins/` 原样提交进 git 留底，之后逐步提交，可对比回滚。

## 第 2 部分：签到 bug 修复与全面功能排查

### 签到 bug 已知分析

后端 `epd_checkin_callback()`（`includes/functions.erphp.php`）链路：

1. `INSERT IGNORE ... WHERE NOT EXISTS` 插入当日签到记录；
2. 插入成功即调用 `addUserMoney()` 加钱；**只要函数存在就返回 status=200"签到成功"，不检查加钱 SQL 是否成功**；
3. `addUserMoney()`（`includes/mobantu.php`）对 `ice_info` 表 insert/update，失败静默返回 false。

与用户现象（提示成功但余额不变）吻合。候选根因（须在真实环境确认）：

- `ice_info` 表无该用户行且 insert 失败（表结构/字符集/严格模式）；
- 奖励金额配置值非数字导致 SQL 语法错误；
- MySQL 8/MariaDB 严格模式拒绝老式 SQL 且错误被吞。

### 修复方式

- 真实环境复现 → 定位确切失败点 → 修复；
- 把"加钱失败但报成功"改为如实返回失败信息；
- 顺带修复已发现隐患：签到 AJAX 无 nonce 校验；`INSERT IGNORE` 无唯一索引时防重形同虚设（并发可重复签到）。

### 全面排查清单

环境搭好后逐项实测：

- **插件用户链路**：注册 → 登录 → 签到（验证 `ice_info` 余额真实变化）→ 充值页 → 余额购买 → 收费下载 → 收费查看 → VIP 购买 → 推广注册返利 → 提现申请 → 个人资料 → 各记录页与翻页。
- **主题功能**：三种布局切换、亮暗切换、色相调整、搜索、评论、归档/分类/标签、特色模板页（友链/追番/相册等）、移动端。
- **兼容性专项**：插件 jQuery 脚本与主题 JS 冲突；layui z-index 与主题浮动面板层级；插件页面在主题布局下的宽度；PHP 8.4 deprecated 噪音。
- **产出**：问题清单（现象+根因+修复方案），先交用户过目，确认后统一修复。

### 修复原则

功能 bug 直接修在插件里；以"最小改动修好"为准，不顺手重写业务逻辑。

## 第 3 部分：本地验证环境（Windows 原生，无 Docker）

用户明确：开发机为 Windows 11、无虚拟化、不可用 Docker；数据库选择 winget 安装 MariaDB。

| 组件 | 方案 |
|---|---|
| PHP | 已有 8.4.22（winget 版，无 php.ini）。新建 php.ini 启用 mysqli、pdo_mysql、gd、mbstring、curl、zip、intl（ext 目录 DLL 已齐备） |
| 数据库 | winget 静默安装 MariaDB；建 `wordpress` 库与专用账号；安装动作执行前需再次征求用户同意 |
| WordPress | 下载最新中文版核心至 `e:\code\mizuki-wp\.wp-local\`（加入 .gitignore） |
| Web 服务器 | `php -S 127.0.0.1:8080` + router.php（处理固定链接重写） |
| 主题/插件接入 | Windows 目录联接（junction）把 `theme/mizuki-wp`、`plugins/erphpdown` 链入 WordPress，仓库改动即时生效 |

### 初始化数据（脚本一次完成）

- 建站 + 管理员 + 普通测试用户；
- 启用主题与插件，设置签到奖励金额；
- 建个人中心页（`[erphpdown_sc_user]`）、充值页等插件必需页面；
- 测试文章：收费下载一篇、收费查看一篇、VIP 专属一篇。

### 验证方式

- 开发期间：脚本+浏览器实测链路，核对 PHP 错误日志与数据库真实数据（签到后直查 `ice_info`）；
- 验收：环境保持运行，用户浏览器访问 `127.0.0.1:8080` 亲测；
- 环境脚本与 php.ini 归档至 `tools/local-env/`，一条命令可重建。

## 错误处理与回滚

- `plugins/` 先原样入库再动工；每个改动小步提交；
- 任何支付相关逻辑不重写、不删除，只换 UI 皮肤；
- 修复导致行为变化的（如签到失败如实报错）在提交信息中注明。

## 测试策略

- 以本地真实 WordPress 环境手工+脚本实测为主（插件功能高度依赖数据库与登录态）；
- 关键修复（签到）附带可重复验证步骤：签到前后查询余额对比；
- 主题既有测试脚本（`tests/`、`test-*.php`）保持可运行。
