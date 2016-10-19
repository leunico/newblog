<?php
namespace app\Controllers;

use app\Model\Diary;
use Illuminate\Database\Query\Expression as raw;

class DiaryController extends Controller
{
    private $rules = [
            'order' => 'required|numeric',
            'classfa' => 'required',
            'content' => 'required|min:10',
            'time' => 'required',
        ];

    public function index()
    {
        $this->parameters['timewaitList'] = Diary::orderBy('order','ASC')
        ->skip($this->getLimit())->take($this->pagesize)
        ->get();
        $this->parameters['pageNav'] = $this->pageNavManage(Diary::all()->count());

        return $this->render('admin/timewait', $this->parameters);
    }

    public function edit($id)
    {
        $this->parameters['timewaits'] = $diary = Diary::find($id);
        if($this->getRequest()->getMethod() == 'POST'){
            $input = $this->validation($this->rules);
            if(!is_array($input))
                return $input;

            $file = $this->getRequest()->files->get('img', '');
            if(!empty($file)){
                $input['img'] = $this->get('qiniu')->setDiaryImg($file);
                if(!empty($input['img']))
                    $this->get('qiniu')->delImg($diary['img']);
                else
                    return $this->error('goback', '图片上传失败！');
            }else{
                return $this->error('goback', '请添加图片！');
            }

            $result = $diary->update($input);
            return $this->success('manage/timewaits', '操作成功！');
        }

        return $this->render('admin/timewait_edit', $this->parameters);
    }

    public function add()
    {
        if($this->getRequest()->getMethod() == 'POST'){
            $input = $this->validation($this->rules);
            if(!is_array($input))
                return $input;

            $input['ctime'] = time();
            $input['img'] = $this->get('qiniu')->setDiaryImg($this->getRequest()->files->get('img', ''));

            if(empty($input['img']))
                return $this->error('goback', '图片上传错误或没有上传图片！');

            $result = Diary::create($input);
            return $this->success('manage/timewaits', '数据添加成功！');
        }

        return $this->render('admin/timewait_add', $this->parameters);
    }

    public function delete($id)
    {
        $info = Diary::find($id);
        if(empty($info))
            return $this->error('manage/timewaits', '没有这条数据！');

        $this->get('qiniu')->delImg($info['img']);
        $info->delete();
        return $this->success('manage/timewaits', '数据删除成功！');
    }

}
