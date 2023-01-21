<?php

declare(strict_types=1);

(static function () {
    $path = '/home/warp/app/vendor/autoload.php';
    if (!include $path) {
        \trigger_error(\sprintf('Composer autoload file not found: %s', $path));
    }
})();

