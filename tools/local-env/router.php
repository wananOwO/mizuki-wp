<?php
// PHP 内置服务器路由：静态文件直出，目录走其 index.php，其余全部交给 WordPress index.php
$root = $_SERVER['DOCUMENT_ROOT'];
$uriPath = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$full = realpath($root . $uriPath);
$rootReal = rtrim(realpath($root), '/\\');

// 允许的真实路径前缀：文档根，外加项目内被软链进 wp-content 的源码目录。
// 本地用 junction/symlink 把 plugins/、theme/ 链进 .wp-local/wordpress/wp-content，
// realpath() 会把软链解析到 .wp-local 之外的真实源码路径；若只校验文档根，
// 插件/主题的 CSS/JS/图片等静态资源全部越界 fall through 到 WordPress 首页，
// 导致浏览器里样式脚本整体加载不到。此处把这两个真实源码目录一并列入白名单。
// 仍以「目录 + 分隔符」精确匹配，杜绝 .git / tools / .wp-local 等旁目录被顺带暴露。
$projectRoot = rtrim(realpath(__DIR__ . '/../..'), '/\\');
$allowedRoots = array($rootReal);
if ($projectRoot !== '') {
    $allowedRoots[] = $projectRoot . DIRECTORY_SEPARATOR . 'plugins';
    $allowedRoots[] = $projectRoot . DIRECTORY_SEPARATOR . 'theme';
}
$inAllowed = false;
if ($full !== false) {
    foreach ($allowedRoots as $base) {
        if ($full === $base || strpos($full, $base . DIRECTORY_SEPARATOR) === 0) {
            $inAllowed = true;
            break;
        }
    }
}
if ($inAllowed) {
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
