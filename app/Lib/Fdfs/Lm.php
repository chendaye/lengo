<?php
namespace Lib\Fdfs;

/**
 * 分布式图片服务器
 *
 * @author long
 */
class Lm extends \FastDFS
{
    /**
     * fdfs API
     *
     * @author long
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 生成下载链接
     *
     * @param string $group
     * @param string $fileId
     * @return string|bool
     * @author long
     */
    public function url(string $group, string $fileId)
    {
        $host = getenv('FDFS_URL');
        $port = getenv('FDFS_WEB_PORT');
        if ($host && $port && $group && $fileId) {
            return $host . ':' . $port . '/' . $group . '/' . $fileId;
        } else {
            return false;
        }
    }



    /**
     * 返回错误信息
     * @return mixed
     */
    public function error()
    {
        return $this->get_last_error_no() . ':' . $this->get_last_error_info();
    }

    /**
     * generate anti-steal token for HTTP download
     *
     * @param string $filename
     * @return bool
     * @author long
     */
    public function httpToken(string $filename)
    {
        return $this->http_gen_token($filename, time());
    }

    /**
     * 通过文件名获取文件信息
     *
     * @param string $group
     * @param string $filename
     * @return array
     * @author long
     */
    public function getInfoByName(string $filename, string $group)
    {
        return $this->get_file_info($group, $filename);
    }

    /**
     * 通过文件id获取文件信息
     *
     * @param string $fileId
     * @return array
     * @author long
     */
    public function getInfoById(string $fileId)
    {
        return $this->get_file_info1($fileId);
    }

    /**
     * 判断文件是否存在
     *
     * @param string $flag
     * @param string $group
     * @return bool
     * @author long
     */
    public function exist(string $flag, string $group = '')
    {
        if ($group != '' && $this->storage_file_exist($flag, $group)) {
            return true;
        } elseif ($this->storage_file_exist1($flag)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 通过文件名上传文件 group tracker_server storage_server 采用默认设置
     *  tracker_server = [  "ip_addr" => "192.168.4.212","port" => 22122",sock" => 7]
     *  storage_server = [   "ip_addr" => "172.18.0.1", "port" => 23000, sock" => 12]
     * group = group1
     * @param string $filename
     * @return array|bool
     * @author long
     */
    public function up(string $filename, array $meta = [], string $ext = null, string $group = null, array $tracker = [], array $storage = [])
    {
        if (!$filename) return false;
        $tracker = empty($tracker) ? $this->tracker_get_connection() : $tracker;
        $storage =  empty($storage) ? $this->tracker_query_storage_store() : $storage;
        $server = $this->connect_server($storage['ip_addr'], $storage['port']);

        $storage['sock'] = $server['sock'];
        $file = $this->storage_upload_by_filename($filename, $ext, $meta, $group, $tracker, $storage);
        if ($file) {
            return $file ?? $this->error();
        }
    }

    /**
     * 删除服务器上的文件
     *
     * @param string $group
     * @param string $filename
     * @param array $tracker
     * @param array $storage
     * @return bool
     * @author long
     */
    public function del(string $group, string $filename, $tracker = [], $storage = [])
    {
        if (!$group || !$filename) {
            return false;
        } else {
            $res = $this->storage_delete_file($group, $filename, $tracker, $storage);
            return $res ?? $this->error();
        }
    }

    /**
     * 下载文件到本地
     *
     * @param [type] $remote
     * @param [type] $local
     * @param string $group
     * @return void
     * @author long
     */
    public function load($remote, $local, $group = 'group1')
    {
        if (!$remote || !$group) {
            return false;
        } else {
            $info = $this->storage_download_file_to_file($remote, $local, $group);
            return $info ?? $this->error();
        }
    }

    /**
     * 链接服务器
     *
     * @param string $ip
     * @param integer $port
     * @return void
     * @author long
     */
    public function linkServer(string $ip, int $port)
    {
        if (!$ip || !$port) {
            return false;
        } else {
            $info = $this->connect_server($ip, $port);
            return $info ?? $this->error();
        }
    }

    /**
     * 断开服务器链接
     *
     * @param string $ip
     * @param integer $port
     * @param integer $sock
     * @return bool
     * @author long
     */
    public function disconnectServer(string $ip, int $port, int $sock)
    {
        if (!$ip || !$port || !$sock) {
            return false;
        } else {
            $info = $this->disconnect_server([
                'ip_addr' => $ip,
                'port' => $port,
                'sock' => $sock
            ]);

            return $info ?? $this->error();
        }
    }

    /**
     * 获取已经链接的tracker 服务器信息
     *
     * @return array
     * @author long
     */
    public function getTrackerServer()
    {
        $info =  $this->tracker_get_connection();
        return $info ?? $this->error();
    }

    /**
     * 测试服务器状态
     *
     * @param string $ip
     * @param integer $port
     * @param integer $sock
     * @return bool
     * @author long
     */
    public function testServer(string $ip, int $port, int $sock)
    {
        if (!$ip || !$port || !$sock) {
            return false;
        } else {
            $info = $this->active_test([
                'ip_addr' => $ip,
                'port' => $port,
                'sock' => $sock
            ]);

            return $info ?? $this->error();
        }
    }

    /**
     * 链接所有Tracker fwq
     *
     * @return bool
     * @author long
     */
    public function linkAllTracker()
    {
        return $this->active_test();
    }

    /**
     * 关闭所有Tracker服务器
     *
     * @return bool
     * @author long
     */
    public function closeAllTracker()
    {
        return $this->tracker_close_all_connections();
    }

    /**
     * 获取storage 服务器的状态
     *
     * @param string $group
     * @param array $tracker
     * @return array|bool
     * @author long
     */
    public function storageStatus(string $group = null, array $tracker = [])
    {
        $info = $this->tracker_list_groups($group, $tracker);
        return $info ?? $this->error();
    }

    /**
     * 获取一个 storage 服务器 ，去存储文件
     *
     * @param string $group
     * @param array $tracker
     * @return void
     * @author long
     */
    public function storageQuery(string $group = null, array $tracker = [])
    {
        $info = $this->tracker_query_storage_store($group, $tracker);
        return $info ?? $this->error();
    }

    /**
     * 获取 storage 服务器列表 ，去存储文件
     *
     * @param string $group
     * @param array $tracker
     * @return void
     * @author long
     */
    public function storageQueryList(string $group = null, array $tracker = [])
    {
        $info = $this->tracker_query_storage_store_list($group, $tracker);
        return $info ?? $this->error();
    }

    /**
     * 获取storage 服务器去下载文件
     *
     * @param string $group
     * @param array $tracker
     * @return void
     * @author long
     */
    public function storageFetch(string $group = null, array $tracker = [])
    {
        $info = $this->tracker_query_storage_fetch($group, $tracker);
        return $info ?? $this->error();
    }

    /**
     * 获取存储制定文件的 storage  列表
     *
     * @param string $group
     * @param string $filename
     * @param array $tracker
     * @return array|bool
     * @author long
     */
    public function storageList(string $group, string $filename, $tracker = [])
    {
        $info = $this->tracker_query_storage_list($group, $filename, $tracker);
        return $info ?? $this->error();
    }
}
