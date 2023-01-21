<?php

if (!extension_loaded('xhprof')) {
    return;
}
global $xhprof_config;
$xhprof_config = [
    'url' => sprintf('http://%s/xhprof/xhprof_html/index.php', $_SERVER['SERVER_NAME']),
    'namespace' => 'cp',
    'start_time' => 0,
    'time_limit' => 0,
    'options' => [
        'ignored_functions' => [],
    ],
    'stopped' => true,
];

include_once __DIR__ . '/utils/xhprof_lib.php';
include_once __DIR__ . '/utils/xhprof_runs.php';

if (!function_exists('xhprof_start')) {
    /**
     * Запускает профилирование.
     * @param int $time_limit Максимальное время выполнения
     * @return void
     */
    function xhprof_start(int $time_limit = 0): void
    {
        global $xhprof_config;
        $xhprof_config['start_time'] = microtime(true);
        $xhprof_config['time_limit'] = $time_limit;
        $xhprof_config['stopped'] = false;
        if (extension_loaded('xhprof')) {
            register_shutdown_function('xhprof_stop');
            xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY, $xhprof_config['options']);
        }
    }
}


if (!function_exists('xhprof_stop')) {
    function xhprof_stop(): void
    {
        global $xhprof_config;
        if ($xhprof_config['stopped']) {
            return;
        }

        $xhprof_config['stopped'] = true;

        $execTime = (microtime(true) - $xhprof_config['start_time']);

        if ($xhprof_config['time_limit'] <= 0 || ($execTime >= $xhprof_config['time_limit'])) {
            $data = xhprof_disable();
            $runs = new XHProfRuns_Default();
            $id = $runs->save_run($data, $xhprof_config['namespace']);
            $url = sprintf($xhprof_config['url'] . '?run=%s&source=%s', $id, $xhprof_config['namespace']);
            echo <<<POPUP
<div style="
    width: 300px;
    font-size: 0.85rem;
    line-height: normal;
    font-weight: normal;
    position: fixed;
    bottom: 1rem;
    left: 1rem;
    z-index: 999;
    opacity: 1;
    border: 2px solid red;
    font-family: Verdana,Arial, sans-serif;
    color: black;
    background: white;
    margin: 0;
    padding: 4px;
    text-align: left;">
    <b>XHProf</b> (sec.: {$execTime}): <br> <a target="_blank" href="{$url}" style="text-decoration: underline">Result: {$id}.{$xhprof_config['namespace']}</a>
</div>
POPUP;
        }
    }
}
