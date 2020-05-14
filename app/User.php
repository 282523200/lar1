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
        $user_ids = $this->followings->pluck('id')->toArray();
        array_push($user_ids, $this->id);
        return Status::whereIn('user_id', $user_ids)
                              ->with('user')
                              ->orderBy('created_at', 'desc');
    }

    //粉丝
    public function followers()
    {
        //参数2 自定义表名称(否则 表1_表2)
        //参数3 自定义外键名
        //参数4 要合并的外键名
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    //关注的人
    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    //关注(动作)
    public function follow($user_ids)
    {
        //如果是个数组 就没必要compact
        if (! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        //同步到关注的人里面
        $this->followings()->sync($user_ids, false);
    }

    //取消
    public function unfollow($user_ids)
    {
        if (! is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    //判断是否包含
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }
}
