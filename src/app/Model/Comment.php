<?php
namespace app\Model;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Comment extends BaseModel
{
    protected $table = 'info_comment';

    protected $guarded = ['comment_mail_notify'];
}