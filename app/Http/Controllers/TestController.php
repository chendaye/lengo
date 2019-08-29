<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;

class TestController extends BaseController
{
    public function fastdfs()
    {
        $f = new \FastDFS();
        // 测试跟踪服务器
        $tracker = $f->tracker_get_connection();
        dd($tracker);


        $server = $f->connect_server($tracker['ip_addr'], $tracker['port']);
        dump($server);
        dump($f->disconnect_server($server));
        dump($server);


        // 测试存储服务器
        $storage = $f->tracker_query_storage_store();
        dump($tracker);

        $server = $f->connect_server($storage['ip_addr'], $storage['port']);
        dump($server);
        dump($f->disconnect_server($server));
        dump($server);

        $storage['sock'] = $server['sock'];
        // $file_info = $f->storage_upload_by_filename("/etc/fdfs/client.conf", null, array(), null, $tracker, $storage);
        $file_info = $f->storage_upload_by_filename("/etc/fdfs/client.conf");

        dump($file_info);
    }

    public function redis()
    {
        // dd(env('REDIS_PASSWORD'));
        // dd(config('database.redis.default'));
        Redis::set('name', 'Taylor swift');
        $name = Redis::get('name');
        dump($name);

        //phpredis
        $redis = new \Redis();
        $redis->connect(env('REDIS_HOST'), 6379); //连接Redis
        $redis->auth(env('REDIS_PASSWORD')); //密码验证

        $redis->set('name', '98k');

        echo $redis->get('name');
    }
}
