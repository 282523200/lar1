<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        //create 要未登陆的访问
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    public function create()
    {
        return view('sessions.create');
    }

    //登录
    public function store(Request $request)
    {
        //输入验证 并存入值
        $credentials = $this->validate($request, [
            'email' => 'required|email|max:255',
            'password' => 'required'
            ]);

        //数据库验证这个值 第二个参数true 5年 默认2小时 数据库字段remember_token保存
        if (Auth::attempt($credentials, $request->has('remember'))) {
            
            //检查有没有邮件激活过
            if (Auth::user()->activated) {
                session()->flash('success', '欢迎回来！');
                $fallback = route('users.show', Auth::user());
                //intended 跳到上次的地方 参数是默认值没有的化跳这个
                return redirect()->intended($fallback);
            } else {
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }

        return;
    }

    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
