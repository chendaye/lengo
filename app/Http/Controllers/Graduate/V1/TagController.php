<?php

namespace App\Http\Controllers\Graduate\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use Validator;

class TagController extends Controller
{
    public function add(Tag $tag, Request $request)
    {
        // 验证规则
        $rules = [
            'tag' => ['required']
        ];

        // 错误信息
        $messages = [
            'name.required' => '请填写标签名',
        ];

        $payload = $request->only('tag');

        $validator = Validator::make($payload, $rules, $messages);
        // 验证格式
        if ($validator->fails()) {
            return $this->response->array(['error' => $validator->errors()]);
        }

        // 创建标签
        $tag->tag = $payload['tag'];
        $result = $tag->save();

        return $this->success($result);
    }
}
