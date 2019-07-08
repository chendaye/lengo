<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Validator;
use Lib\Fdfs\Lm;

class ArticleController extends AuthController
{

    /**
     * 封面上传
     *
     * @param Request $request
     * @return array
     * @author long
     */
    public function cover(Request $request)
    {
        $lm = new Lm();
        // $tmp = $request->file('avatar');
        // $file = $lm->up((string) $tmp);
        // $file['url'] = $lm->url($file['group_name'], $file['filename']);

        return $this->response->array($request->input('cover'));
    }
}
