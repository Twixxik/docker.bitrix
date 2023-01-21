<?php

declare(strict_types=1);

(static function () {
    $path = '/home/warp/app/vendor/autoload.php';
    if (file_exists($path)) {
        include $path;
    }

    $path = '/home/warp/app/xhprof/xhprof_lib/include.php';
    if (file_exists($path)) {
        include $path;
    }
})();

