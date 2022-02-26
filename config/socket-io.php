<?php
return [
    'io_port' => env('SOCKET_IO_PORT', '12306'),
    'io_inner_http' => env('SOCKET_IO_INNER_HTTP', 'http://0.0.0.0:12307'),
];
