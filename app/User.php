<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;


    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    //设置类型
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    //定义 gravatar
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    //模型启动时触发
    public static function boot()
    {
        parent::boot();

        //插入数据库时的事件
        static::creating(function ($user) {
            $user->activation_token = Str::random(10);
        });
    }
    //模型绑定到status模型
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }
    
    //获取该user发布过的所有微博
    public function feed()
    {
        return $this->statuses()
                    ->orderBy('created_at', 'desc');
    }

//
    public function followers(){
        return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
    }

    //
    public function followings()
    {
        return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
    }
}
