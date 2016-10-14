<?php
namespace app\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;

class User extends BaseModel
{
    protected $table = 'user';

    protected $fillable = ['username', 'password', 'email', 'wxname', 'openid', 'is_block'];

    public function articles()
    {
        return $this->hasMany('app\Model\Article', 'uid');
    }
}