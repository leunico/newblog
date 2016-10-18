<?php
namespace app\Controllers;

use app\Model\Push;
use Illuminate\Database\Query\Expression as raw;

class PushController extends Controller
{
    public function index()
    {
        $this->parameters['pushs'] = Push::orderBy('ctime', 'desc')->take(4)->get();
// var_dump($this->parameters);exit;
        if($this->getRequest()->getMethod() == 'POST'){
            $input = $this->getRequest()->request->all();
            var_dump($input);exit;
            $result = $diary->editPush(self::$models->make('Qiniu',['alice']),$pushurl,$pushimg,$_FILES['doc']);
            $result ? View::AdminMessage('admin/pushs', '修改成功') : View::AdminErrorMessage('goback', '修改失败');
        }

        return $this->render('admin/pushs', $this->parameters);
    }

}
