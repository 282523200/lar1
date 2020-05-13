<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class StatusesController extends Controller
{
    public function __construct()
    {
        //登录才能操作
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        //内容验证
        $this->validate($request, [
            'content' => 'required|max:140'
        ]);

        //关联插入数据库
        Auth::user()->statuses()->create([
            'content' => $request['content']
        ]);

        session()->flash('success', '发布成功！');
        return redirect()->back();
    }
}
