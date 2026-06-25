<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Docker PHP 环境测试</title>
    <style>
        body { font-family: sans-serif; max-width: 720px; margin: 40px auto; line-height: 1.6; }
        h1 { color: #333; }
        .ok { color: #0a7; }
        .fail { color: #c33; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Nginx + PHP 7.0 测试页</h1>
    <p>如果你能看到这个页面，说明 <strong>Nginx → PHP-FPM</strong> 链路正常。</p>
    <ul>
        <li>PHP 版本：<code><?php echo PHP_VERSION; ?></code></li>
        <li>服务器时间：<code><?php echo date('Y-m-d H:i:s'); ?></code></li>
        <li>时区：<code><?php echo date_default_timezone_get(); ?></code></li>
        <li>站点目录：<code>/Users/rainzzer/code/docker-php/docker-test</code></li>
    </ul>

    <h2>扩展检测</h2>
    <ul>
        <?php
        $extensions = ['pdo_mysql', 'mysqli', 'redis', 'gd', 'imagick', 'xdebug', 'zip', 'mbstring'];
        foreach ($extensions as $ext) {
            $loaded = extension_loaded($ext);
            $class = $loaded ? 'ok' : 'fail';
            $text = $loaded ? '已加载' : '未加载';
            echo "<li class=\"{$class}\">{$ext}：{$text}</li>";
        }
        ?>
    </ul>

    <h2>服务连通性</h2>
    <ul>
        <?php
        function check($label, $ok, $detail = '') {
            $class = $ok ? 'ok' : 'fail';
            $text = $ok ? '正常' : '失败';
            $extra = $detail ? "（{$detail}）" : '';
            echo "<li class=\"{$class}\">{$label}：{$text}{$extra}</li>";
        }

        try {
            new PDO('mysql:host=mysql57;port=3306', 'root', 'root');
            check('MySQL (mysql57)', true);
        } catch (Exception $e) {
            check('MySQL (mysql57)', false, $e->getMessage());
        }

        try {
            $redis = new Redis();
            $redis->connect('redis50', 6379);
            check('Redis (redis50)', $redis->ping() === '+PONG' || $redis->ping() === true);
        } catch (Exception $e) {
            check('Redis (redis50)', false, $e->getMessage());
        }
        ?>
    </ul>

    <p><a href="info.php">查看 phpinfo()</a></p>
</body>
</html>
