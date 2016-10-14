<?php
namespace app\Controllers;

use app\Model\User;
use Illuminate\Database\Query\Expression as raw;

class UserController extends Controller
{
    private $rules = [
            'username' => 'required|min:2|max:20',
            'email' => 'required|email',
        ];

    public function edit()
    {
        $this->parameters['users'] = $user = User::find($this->getView()->getUser('id'));
        $oldusername = $user->username;
        if($user && $this->getRequest()->getMethod() == 'POST'){
            $input = $this->validation($this->rules);
            if(!is_array($input))
                return $input;

            if(!empty($input['oldpw']) && !empty($input['newpw'])){
                if($user->password !== md5($input['oldpw']))
                    return $this->error('goback', '旧密码不正确！');

                $input['password'] = md5($input['newpw_a']);
            }

            $result = $user->update($input);
            if($result){
                if($input['username'] !== $oldusername){
                    $key = $this->get('session_pre').'admin_user_login';
                    $loginInfo = $this->getSession()->get($key);
                    $loginInfo['username'] = $input['username'];
                    $this->getSession()->set($key, $loginInfo);
                    $user->articles()->where('uid', $user->id)->update(['author' => $input['username']]);
                }
            }

            return $this->success('goback', '修改成功！');
        }

       return $this->render('admin/user_edit', $this->parameters);
    }

    public function index()
    {
        $this->parameters['userList'] = User::leftJoin('info_article', 'user.id', '=', 'info_article.uid')
        ->select(new raw('COUNT(info_article.id) as count, user.*'))
        ->groupBy('user.id')
        ->orderBy('ctime', 'desc')->orderBy('count', 'desc')
        ->skip($this->getLimit())->take($this->pagesize)
        ->get();
        $this->parameters['pageNav'] = $this->pageNavManage(User::all()->count());

        return $this->render('admin/users', $this->parameters);
    }

    public function block($id)
    {
        $user = User::find($id);
        $user->is_block = 1-$user->is_block;
        $user->save();
        return $this->success('manage/users', '操作成功!');
    }

    public function delete($id)
    {
        $user = User::find($id);
        if(empty($user))
            return $this->error('manage/users', '没有这个用户！');

        $user->delete();
        return $this->success('manage/users', '用户删除成功！');
    }

    public function add()
    {
        if($this->getRequest()->getMethod() == 'POST'){
            $input = $this->validation($this->rules);
            if(!is_array($input))
                return $input;

            $input['wxname'] = $input['openid'] = 'Not wechat';
            $input['ctime'] = time();
            $input['password'] = md5($input['newpw']);

            if(User::where('email', $input['email'])->get()->toArray())
                return $this->error('manage/users', '邮箱已经存在了！');

            User::create($input);
            return $this->success('manage/users', '用户添加成功！');
        }

        return $this->render('admin/user_add', $this->parameters);
    }

}
