<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use Lib\Fdfs\Lm;

class UserController extends Controller
{
    public function add(User $user, Request $request)
    {
        $data = $request->all();
        return $this->response->array($data);
    }

    /**
     * 头像上传
     *
     * @param Request $request
     * @return array
     * @author long
     */
    public function avatar(Request $request)
    {
        $lm = new Lm();
        $tmp = $request->file('avatar');
        $file = $lm->up((string)$tmp);
        $file['url'] = $lm->url($file[ 'group_name'], $file[ 'filename']);

        return $this->response->array($file);
    }
}
