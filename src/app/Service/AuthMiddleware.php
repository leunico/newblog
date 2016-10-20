<?php
namespace app\service;

use Alicecore\AppFramework;
use Alicecore\Handle\Extension\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use app\Model\Article;

class AuthMiddleware implements MiddlewareInterface
{
    protected $app;

    public function boot(Request $request, AppFramework $app)
    {
        $this->app = $app;
        $logininfo = $app->getSession()->get($app['session_pre'].'admin_user_login');
        if(empty($logininfo) || !isset($logininfo['username']) || !isset($logininfo['id']))
            return $app->render('admin/public/show_error_message', ['jumpurl' => 'login', 'msg' => '对不起，你还没有登录！']);

        if(isset($logininfo['block']) && $logininfo['block'] == 1){
            $session->clear();
            return $app->render('admin/public/show_error_message', ['jumpurl' => '', 'msg' => '你的帐号被管理员拉黑了！']);
        }
    }

    public function security(Request $request, AppFramework $app)
    {
        $this->boot($request, $app);
        $id = $request->get('id', '');
        $user = $this->app->getView()->getUser('all');
        if(empty($id)){
            if($user['is_admin'] == 0)
                return $app->render('admin/public/show_error_message', ['jumpurl' => 'manage', 'msg' => '对不起，你没有这个操作的权限！']);
        }else{
            $info = Article::find($id);
            if($info['uid'] !== $user['id'] && $user['is_admin'] == 0)
                return $app->render('admin/public/show_error_message', ['jumpurl' => 'manage', 'msg' => '你没有这篇文章的操作权限！']);
        }
    }
}