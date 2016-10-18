<?php
namespace app\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Push extends BaseModel
{
    protected $table = 'info_indexpush';

    protected $fillable = ['ctime', 'pushimg', 'pushurl'];
}