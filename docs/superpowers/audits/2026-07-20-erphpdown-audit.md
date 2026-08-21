# erphpdown 审计记录

## 签到根因

### 结论摘要

`epd_checkin_callback()`（`plugins/erphpdown/includes/functions.erphp.php:779`）在"今日签到记录插入成功"后，**只要 `addUserMoney` 函数存在就无条件返回 `status=200`（签到成功）**，完全不检查 `addUserMoney()` 的返回值。`addUserMoney()`（`plugins/erphpdown/includes/mobantu.php:1811`）内部对 `ice_info` 表执行 `UPDATE`/`INSERT` 时失败会静默 `return false`，调用方对此不闻不问。这就是用户报告"点击签到提示成功，但余额并不增加"在代码层面的确切成因：**AJAX 回调把"签到记录写入成功"与"加钱成功"当成同一件事来汇报，二者其实是两次独立的、可能各自失败的数据库写入。**

### 复现结论：默认干净数据下 `test-checkin.sh` 意外 PASS

Task 4 环境里 `ice_ali_money_checkin` 选项值为数字 `1`（干净配置）。在这种默认状态下按 brief Step 1 跑测试脚本：

```
接口返回: {"status":200,"msg":null}
余额: 0 -> 1 (奖励应为 1)
PASS: 报成功且余额确实增加 1
exit=0
```

即：干净数字配置 + 已存在或不存在 `ice_info` 行，两条代码路径（`INSERT`/`UPDATE`）在正常输入下都会成功，`addUserMoney()` 返回真值，余额确实增加，脚本 PASS。**这本身就是一个真实发现**：仅凭"清空今日签到记录后重跑"不足以复现用户报告的 bug——需要让 `addUserMoney()` 的底层 SQL 写入真正失败。按 brief Step 2 的指示，转入"非数字奖励配置"协议以定位真实触发条件。

### 投毒实验：`ice_ali_money_checkin` 设为非数字值

`addUserMoney($userId, $money)` 的两条分支对 `$money` 的 SQL 拼接方式不同：

- **已有 `ice_info` 行（老用户/已签到过的用户）→ `UPDATE`** (`mobantu.php:1821`)：
  ```php
  $wpdb->query("update $wpdb->iceinfo set ice_have_money=ice_have_money+".$money." where ice_user_id=".$userId);
  ```
  `$money` **未加引号**、直接拼进算术表达式。
- **无 `ice_info` 行（该用户首次触发加钱）→ `INSERT`** (`mobantu.php:1819`)：
  ```php
  $wpdb->query("insert into $wpdb->iceinfo(ice_have_money,ice_user_id,ice_get_money)values('$money','$userId',0)");
  ```
  `$money` **加了引号**，作为字符串字面量插入数值列。

这两条路径对"非数字 `$money`"的容错完全不同，实测如下（`UID=2` 即 tester，环境：MariaDB 12.3，`@@sql_mode = ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION`，**未开启** `STRICT_TRANS_TABLES`）。

#### 场景 A：已有 `ice_info` 行，`ice_ali_money_checkin = "1元"`，走真实 `do_action('wp_ajax_epd_checkin')`

```
$ WP option update ice_ali_money_checkin "1元"
$ bash tools/local-env/test-checkin.sh
接口返回: {"status":200,"msg":null}
余额: 1 -> 1 (奖励应为 1元)
FAIL: 接口报成功=1，余额变化=0，期望=1元
exit=1
```

用 `register_shutdown_function` 在 `exit()` 之前的同一 PHP 进程内读取 `$wpdb->last_error` / `$wpdb->last_query`（直接 `2>/dev/null` 会在 `exit()` 时丢失该信息，故用 shutdown hook 写 STDERR）：

```
stdout: {"status":200,"msg":null}
stderr:
Warning: Undefined variable $msg in .../functions.erphp.php on line 819
SHUTDOWN last_error=[Unknown column '1元' in 'SELECT']
SHUTDOWN ice_have_money=[1.00]
```

直接复算 `addUserMoney()` 拼出的 SQL 并单独执行以确认：

```
SQL: update wp_ice_info set ice_have_money=ice_have_money+1元 where ice_user_id=2
query() return: false
last_error: Unknown column '1元' in 'SELECT'
```

**`$wpdb->query()` 返回 `false`，`ice_have_money` 完全没有变化**，但 `epd_checkin_callback()` 不检查这个返回值，AJAX 仍返回 `{"status":200}`。这就是用户症状的精确复现：接口报成功，余额不增加。

#### 场景 A'：换一个更"像人为配置失误"的非数字值 `"1,5"`（如误把小数点打成逗号）

同一台已有 `ice_info` 行的用户，同样触发真实语法错误：

```
last_error=[You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near '5 where ice_user_id=2' at line 1]
last_query=[update wp_ice_info set ice_have_money=ice_have_money+1,5 where ice_user_id=2]
```

结论一致：任何包含非纯数字字符（中文单位、逗号、空格等）的 `ice_ali_money_checkin` 配置值，只要用户此前已经在 `ice_info` 表中有记录（即不是该用户第一次被加钱），`UPDATE` 分支必然产生 SQL 语法错误，`addUserMoney()` 返回 `false`，余额零变化，而 AJAX 照常报 200。

#### 场景 B（对照）：无 `ice_info` 行（该用户第一次加钱），`ice_ali_money_checkin = "1元"`

```
$ WP eval 'DELETE FROM wp_ice_info WHERE ice_user_id=2'
resp={"status":200,"msg":null} before=0 after=1
row now: [ice_have_money] => 1.00   (其余字段正常)
```

`INSERT` 分支给 `$money` 加了引号（`'1元'`），SQL 语法本身合法；MariaDB 在**非严格模式**下把字符串 `'1元'` 隐式转换为 `DOUBLE` 时取其前导数字部分（`1`），插入成功、无报错（`last_error` 为空）。也就是说：**同一个非数字配置值，对"首次加钱用户"是静默截断成功（金额被隐式改写但不报错），对"已有余额记录的用户"是彻底失败且不报错**——两种路径殊途同归地隐藏了配置错误，只是表现形式不同（错误金额 vs 零变化）。这也解释了为什么该 bug 不是每个用户都必然复现：取决于该用户是否已经有 `ice_info` 行。

### 已排除的候选根因

- **`ice_info` 表结构/字符集导致 insert 必然失败**——设计文档列出的候选之一。实测：结构正常（`DESCRIBE` 见下），首次 insert 在合法输入下正常成功，非数字输入时也未報 SQL 层错误（只是被隐式转换）。**不是**此环境下的失败点。
- **MySQL 8/MariaDB 严格模式拒绝老式 SQL 且错误被吞**——本环境 `sql_mode` **未包含** `STRICT_TRANS_TABLES`，但依然 100% 复现了失败（场景 A/A'）。可见触发条件**不需要**严格模式；`UPDATE` 分支的裸拼接算术表达式在任何 `sql_mode` 下，只要 `$money` 含非数字字符就会产生语法错误。严格模式只影响 `INSERT` 分支在遇到非数字值时是报错还是静默截断，不是本 bug 的必要条件。

### 确认的根因（驱动 Task 6 修复）

1. **核心缺陷**：`epd_checkin_callback()` 把 `addUserMoney($current_user->ID, $gift)` 的返回值完全丢弃，只要"今日签到记录 INSERT"成功就无条件报 `status=200`。这是所有变体症状的共同病灶——修复必须让该回调检查 `addUserMoney()` 的返回值，加钱失败时如实返回失败状态/信息，而不是報成功。
2. **具体触发条件（已实测复现）**：当 `get_option('ice_ali_money_checkin')` 的值包含任何非纯数字字符（中文单位如"元"、千分位逗号等常见误配置）时：
   - 若该用户在 `wp_ice_info` 中**已有记录**（正常情况——用户此前充过值/签到过/被加过钱）：`addUserMoney()` 的 `UPDATE ... ice_have_money=ice_have_money+{$money}` 因未加引号拼接产生 **SQL 语法错误**，`$wpdb->query()` 返回 `false`，余额零变化，AJAX 仍报成功。**这是用户报告症状的精确复现路径。**
   - 若该用户**无记录**（首次加钱）：`INSERT` 分支因为给 `$money` 加了引号，语法合法，MariaDB 非严格模式下静默把非数字前缀转换为数字插入（金额可能不等于预期值，但至少不是零变化）。
3. 修复范围应覆盖：（a）`epd_checkin_callback()` 检查 `addUserMoney()` 返回值并如实报告；（b）`addUserMoney()`/相关 SQL 对 `$money` 做数值校验或使用参数化写法，避免非数字配置值污染 SQL；（c）管理页 `ice_ali_money_checkin` 输入框已是 `type="number"`（`admin/erphp-settings.php:258`），但 `update_option` 时用的是 `trim()` 而非数值校验（`admin/erphp-settings.php:23`），后台仍可保存非数字字符串（例如通过浏览器开发工具绕过 `type="number"`，或历史遗留脏数据）——修复时应在写入 option 时做数值 sanitize，双重防护。

### 附：环境与数据取证

```
$wpdb->checkin = wp_checkins ; $wpdb->iceinfo = wp_ice_info   (plugins/erphpdown/erphpdown.php:19,28)

wp_checkins 表结构: ID, user_id, credit, create_time, user_ip
wp_ice_info 表结构: ice_id, ice_have_money(double 10,2), ice_user_id, ice_get_money, ice_have_aff, ice_get_aff, ice_ip, userType, endTime

@@sql_mode = ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION  （未含 STRICT_TRANS_TABLES）
```

调查结束后已把 `ice_ali_money_checkin` 恢复为 `1`（确认：`WP option get ice_ali_money_checkin` → `1`），并重跑 `test-checkin.sh` 确认恢复干净状态后重新 PASS：

```
接口返回: {"status":200,"msg":null}
余额: 1 -> 2 (奖励应为 1)
PASS: 报成功且余额确实增加 1
exit=0
```
