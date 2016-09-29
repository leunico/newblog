<?php
namespace app\Controllers;

use app\Model\User;

class AdminController extends Controller
{
    private $sessionId = 'admin_user_login';

    private $rules = [
                'email'      => 'required|email',
                'password'   => 'required|min:6|max:20'
            ];

    public function index()
    {
        $loginInfo = $this->getSession()->get($this->sessionId);
        if (!empty($loginInfo) && !empty($loginInfo['username']) && !empty($loginInfo['id']))
            $this->success('admin/index', '你已经登录了!');

        $this->parameters['scene_id'] = rand(100,999);
        $this->parameters['wximage'] = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$this->getWeixin()->get_code_image($this->parameters['scene_id']);

        return $this->render('admin/login', $this->parameters);
    }

    public function login()
    {
        $input = $this->getRequest()->request->all();
        $validator = $this->getValidation()->make($input, $this->rules);
        if(!$validator->passes()){
            $errors = $validator->messages()->all();
            $this->error('', $errors[0]);
        }

        $email = $input['email'];
        $password = md5($input['password']);
        $result = User::where('email', $email)->where('password', $password)->first()->toArray();

        if(empty($result))
            return $this->error('login', '密码or帐号错误，登录后台失败!');

        if(isset($result['is_block']) && $result['is_block'] == 1)
            return $this->error('login', 'sorry,你的帐号被管理员拉黑！');

        $session = $this->getSession();
        $key = $this->get('session_pre').'admin_user_login';
        $result['type'] = 'pc';
        $session->set($key, $result);

        return $this->redirect($this->getView()->Route('manage'));
    }

}
