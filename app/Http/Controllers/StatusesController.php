<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Status;

class StatusesController extends Controller
{
    public function __construct()
    {
        //登录才能操作
        $this->middleware('auth');
    }

    //写入微博
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

    //删除微博
    public function destroy(Status $status)
    {
        //不通过权限 403
        $this->authorize('destroy', $status);
        $status->delete();
        session()->flash('success', '微博已被成功删除！');
        return redirect()->back();
    }
}
