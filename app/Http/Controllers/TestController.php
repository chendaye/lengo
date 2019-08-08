<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;

class TestController extends BaseController
{
    public function test()
    {
        $f = new \FastDFS();
        dd($f);
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
}
