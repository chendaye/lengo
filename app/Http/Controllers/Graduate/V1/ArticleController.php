<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Validator;
use Lib\Fdfs\Lm;

class ArticleController extends AuthController
{

    /**
     * å°�é�¢ä¸Šä¼ 
     *
     * @param Request $request
     * @return array
     * @author long
     */
    public function cover(Request $request)
    {
        $lm = new Lm();
        $tmp = $request->input('cover');
        $path = $this->base64ToPic($tmp);
        if ($path) {
            $file = $lm->up($path);
            $file['url'] = $lm->url($file['group_name'], $file['filename']);
            unlink($path); // 删除文件
            return $this->success($file);
        } else {
            return $this->error('封面上传失败！');
        }
    }

    /**
     * base64 生成图片
     *
     * @param [type] $data
     * @return string
     * @author chendaye
     */
    public function base64ToPic($data)
    {
        // 取base64 编码  cover: data:image/png;base64,iVBORw...
        if (strstr($data, ",")) {
            $image = explode(',', $data);
            $image = $image[1];
        }

        // 获取图片后缀
        if (strstr( $data, ";")) {
            $ext = explode(';', $data);
            $ext = explode('/', $ext[0]);
            $ext = $ext[1];
        }

        $imageName = date("His", time()) . "_" . time() . '.' . $ext;

        $imageSrc = "/tmp/" . $imageName; //图片名字
        $r = file_put_contents($imageSrc, base64_decode($image)); //返回的是字节数
        if (!$r) {
            return false;
        } else {
            return $imageSrc;
        }
    }
}
