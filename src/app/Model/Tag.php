<?php
namespace app\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Tag extends BaseModel
{
    protected $table = 'info_tag';

    public function article()
    {
        return $this->belongsToMany('app\Model\Article'); // 'article_tag', 'tag_id', 'article_id'
    }
}