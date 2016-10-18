<?php
namespace app\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Tag extends BaseModel
{
    protected $table = 'info_tag';

    protected $fillable = ['tag', 'num'];

    public function articles()
    {
        return $this->belongsToMany('app\Model\Article'); // 'article_tag', 'tag_id', 'article_id'
    }
}