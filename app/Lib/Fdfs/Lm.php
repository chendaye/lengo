<?php
namespace Lib\Fdfs;

/**
 * 分布式图片服务器
 *
 * @author long
 */
class Lm extends \FastDFS {

    private $tracker = null; // 跟踪服务器， 主要做调度工作， 负载均衡
    private $storage = null;  // 存储服务器
    /**
     * 组， 也可称为卷。 同组内服务器上的文件是完全相同的 ，
     * 同一组内的storage server之间是对等的， 文件上传、 删除等操作可以在任意一台storage server上进行 。
     */
    private $group_name = null;


    /**
     * 构造函数
     *
     * @author long
     */
    public function __construct( $tracker = null, $storage = null, $group_name = null)
    {
        parent::__construct();

        if ($group_name) {
            $this->group_name = $group_name;
        } else {

            $this->tracker = $this->tracker_get_connection(); // get a connected tracker server
            $this->storage = $this->tracker_query_storage_store();
            if (!$this->tracker || !$this->storage) {
                error_log("error:", $this->get_last_error_no() . ",error info:" . $this->get_last_error_info());
                exit(1);
            }
        }
    }

    /**
     * 设置组名
     * @param $group_name 组名
     */
    public function setGroupName($group_name)
    {
        $this->group_name = $group_name;
    }

    /**
     *
     * @param $file 要上传文件的绝对路径
     * @param null $ext_name
     * @param null $group_name
     * @param array $meta 数组，文件附带的元元素。如array('width'=>1024,'height'=>768)
     * @return mixed  成功返回数组 array('group_name'=>'xx','filename'=>'yy') 失败返回false
     */
    public function upload($file, $ext_name = null, $group_name = null, $meta = [])
    {
        if ($group_name) {
            $this->group_name = $group_name;
        }
        if ($this->group_name) {
            $file_info = storage_upload_by_filename($file, $ext_name, $meta, $this->group_name);
        } else {
            $file_info = storage_upload_by_filename($file, $ext_name, $meta, null, $this->tracker, $this->group_name);
        }
        if ($file_info['group_name']) {
            $this->group_name = $file_info['group_name'];
        }
        return $file_info;
    }

    /**
     * 文件读取
     * @param $group_name 组名
     * @param $file_id 文件名
     * @param int $file_offset 文件读取的开始位置
     * @param int $download_bytes 读取的大小
     * @return mixed  成功返回文件内容；失败返回false
     */
    public function download($group_name, $file_id, $file_offset = 0, $download_bytes = 0)
    {
        $file_content = storage_download_file_to_buff($group_name, $file_id, $file_offset, $download_bytes);
        return $file_content;
    }

    /**
     * 创建下载链接
     * @param $group_name
     * @param $file_id
     * @return string
     */
    public function createDownUrl($group_name, $file_id)
    {
        $storage_static_host = config('fastdfs.storage_static_host');
        if ($file_id) {
            return 'http://' . $storage_static_host . '/' . $group_name . '/' . $file_id;
        }
        return '';
    }

    /**
     * 文件删除
     * @param $group_name 组名
     * @param $file_id 文件名
     * @return mixed 成功返回true 失败返回false
     */
    public function delete($group_name, $file_id)
    {
        return storage_delete_file($group_name, $file_id);
    }

    /**
     * 返回错误信息
     * @return mixed
     */
    public function error()
    {
        return get_last_error_info();
    }
}

