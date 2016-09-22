<?php
namespace app\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Tag extends BaseModel
{
    protected $table = 'info_tag';

    public function article()
    {
        return $this->hasMany('app\Model\Article', 'tag');
    }
}