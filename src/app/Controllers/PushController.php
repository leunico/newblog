<?php
namespace app\Controllers;

use app\Model\Push;
use Illuminate\Database\Query\Expression as raw;

class PushController extends Controller
{
    public function index()
    {
        $this->parameters['pushs'] = $push = Push::orderBy('ctime', 'desc')->take(4)->get();
        if($this->getRequest()->getMethod() == 'POST'){
            $input = $this->getRequest()->request->all();
            $files = $this->getRequest()->files->get('doc', '');

            if(!empty($input)){
                foreach($input['pushid'] as $key => $item){
                    $pushimg = $files[$key] ? $this->get('qiniu')->setPushImg($files[$key], $input['pushimg'][$key]) : $input['pushimg'][$key];
                    $fields = ['pushurl' => $input['pushurl'][$key], 'pushimg' => $pushimg];
                    $push[$key]->update($fields);
                }
            }

            return $this->success('manage/pushs', '数据操作成功！');
        }

        return $this->render('admin/pushs', $this->parameters);
    }

}
