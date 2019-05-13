<?php

namespace App\Http\Controllers;

class SwooleController extends Controller
{
    public function test()
    {

        // 表明服务器启动后监听本地 9051 端口
        $server = new swoole_http_server('127.0.0.1', 9501);
        
        // 服务器启动时返回响应
        $server->on("start", function ($server) {
            echo "Swoole http server is started at http://127.0.0.1:9501\n";
        });

        // 向服务器发送请求时返回响应
        // 可以获取请求参数，也可以设置响应头和响应内容
        $server->on("request", function ($request, $response) {
            $response->header("Content-Type", "text/plain");
            $response->end("Hello World\n");
        });

        // 启动 HTTP 服务器
        $server->start();
    }
}
