<?php
namespace app\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Article extends BaseModel
{
    protected $table = 'info_article';

    public function comments()
    {
        return $this->hasMany('app\Model\Comment', 'aid');
    }

}