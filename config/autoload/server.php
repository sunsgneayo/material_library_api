<?php

declare(strict_types=1);

use Hyperf\Server\Server;
use Hyperf\Server\Event;
use Swoole\Constant;

return [
    'mode'      => SWOOLE_PROCESS,
    'servers'   => [
        [
            'name'      => 'http',
            'type'      => Server::SERVER_HTTP,
            'host'      => '0.0.0.0',
            'port'      => (int)env('HTTP_PORT', 8888),
            'sock_type' => SWOOLE_SOCK_TCP,
            'callbacks' => [
                Event::ON_REQUEST => [Hyperf\HttpServer\Server::class, 'onRequest'],
            ],
        ],
    ],
    'settings'  => [
        'daemonize'                          => (bool)env('PRODUCTION_ENVIRONMENT',true),

        Constant::OPTION_ENABLE_COROUTINE    => true,
        Constant::OPTION_WORKER_NUM          => swoole_cpu_num(),
        Constant::OPTION_PID_FILE            => BASE_PATH . '/runtime/hyperf.pid',
        Constant::OPTION_OPEN_TCP_NODELAY    => true,
        Constant::OPTION_MAX_COROUTINE       => 100000,
        Constant::OPTION_OPEN_HTTP2_PROTOCOL => true,
        Constant::OPTION_MAX_REQUEST         => 100000,
        Constant::OPTION_SOCKET_BUFFER_SIZE  => 20 * 1024 * 1024,
        Constant::OPTION_BUFFER_OUTPUT_SIZE  => 20 * 1024 * 1024,
        // 将 public 替换为上传目录
        'document_root'                      => BASE_PATH . '/public',
        'enable_static_handler'              => true,
        //最大上传限制
        'package_max_length'                 => 50 *1024*1024
    ],
    'callbacks' => [
        Event::ON_WORKER_START => [Hyperf\Framework\Bootstrap\WorkerStartCallback::class, 'onWorkerStart'],
        Event::ON_PIPE_MESSAGE => [Hyperf\Framework\Bootstrap\PipeMessageCallback::class, 'onPipeMessage'],
        Event::ON_WORKER_EXIT  => [Hyperf\Framework\Bootstrap\WorkerExitCallback::class, 'onWorkerExit'],
    ],
];
