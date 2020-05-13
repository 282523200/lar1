<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', [
            //除了show create store 其他要auth(登录)
            'except' => ['show', 'create', 'store','index']
        ]);
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    //用户s的列表
    public function index()
    {
        $users = User::paginate(8);
        return view('users.index', compact('users'));
    }
    
    public function create()
    {
        return view('users.create');
    }
    //用户显示
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    //注册账号
    public function store(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        //通过验证后 放入数据库
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        //登录
        Auth::login($user);

        //放入session 会自动传到视图 两个参数 key value
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');

        //重定向到用户页
        return redirect()->route('users.show', [$user]);
    }

    //编辑个人信息
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    //提交修改个人信息
    public function update(User $user, Request $request)
    {
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            //密码不输就当不修改
            'password' => 'nullable|confirmed|min:6'
        ]);
        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        //数据库修改
        $user->update($data);
        session()->flash('success', '个人资料更新成功！');
        return redirect()->route('users.show', $user->id);
    }

    public function destroy(User $user)
    {
        $this->authorize('destroy', $user);
        //数据库删除
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
}
