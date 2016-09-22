<?php
namespace app\Controllers;

use app\Model\Comment;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    private $rules = [
                'contents' => 'required|min:3',
                'nickname' => 'required',
                'email'    => 'required|email',
                #'website' => 'required',
            ];

    public function add()
    {
        //$response = new Response('xxxxxxoooooo!!!!', Response::HTTP_METHOD_NOT_ALLOWED);
        //return $response;

        $input = $this->getRequest()->request->all();
        $validator = $this->getValidation()->make($input, $this->rules);
        if(!$validator->passes()){
            $errors = $validator->messages()->all(); // 或者 $validator->errors();
            return new Response($errors[0], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $input['ctime'] = time();
        $input['ip'] = $this->getRequest()->getClientIP();
        $input['contents'] = $this->container->escape($input['contents']);
        #if($this->getViceController()->ipValidator($input['ip']))
        #    return new Response('Sorry！ 一天评论不能超过12条', Response::HTTP_METHOD_NOT_ALLOWED);

        if(strstr($input['cid'],'-')){
            $parents = explode('-', $input['cid']);
            $input['cid'] = $parents[0];
            $tomail = $parents[1];
            $commentp = Comment::find($tomail);
            $input['parent'] = $commentp ? $commentp['id'].','.$commentp['nickname'] : '';
            $fidname = "<a href=\"#comment-".$commentp['id']."\" rel=\"nofollow\" class=\"cute\">@".$commentp['nickname']."</a>";
        }elseif(!empty($input['cid'])){
            $commentp = Comment::find($input['cid']);
            $input['parent'] = $commentp ? $commentp['id'].','.$commentp['nickname'] : '';
            $fidname = "<a href=\"#comment-".$commentp['id']."\" rel=\"nofollow\" class=\"cute\">@".$commentp['nickname']."</a>";
        }else{
            $input['parent'] = '';
            $fidname = '';
        }

        $result = Comment::create($input);
        if(!$result){
            return new Response('评论添加失败，多次失败请联系站长！', Response::HTTP_METHOD_NOT_ALLOWED);
        }else{
            $this->getViceController()->touristCookie($input['nickname'], $input['email'], $input['website']);

            if($this->getMailSwitch() && $input['cid'] > 0 && !empty($commentp)) #邮件发送服务还没写！
                $this->getMail()->SendMail($input['contents'], $commentp);

            $toid = empty($commentp) ? '#' : $commentp['id'];
            $returnstr = "<li class=\"comment even thread-even depth-1 clearfix\" id=\"comment-".$toid."><span class=\"comt-f\"></span> ";
            $returnstr .= "  <div class=\"c-avatar\"><img alt='' src='".$this->getView()->getImage('ty.jpg')."' class='avatar avatar-50 photo' height='50' width='50' /><div class=\"c-main\" id=\"div-comment-".$toid.">";
            $returnstr .= "     <p style=\"color:#8c8c8c;\"><span class=\"c-author\">".$input['nickname']."</span></p><p>".$fidname.$this->getView()->EmojiH($input['contents'])."</p>";
            $returnstr .= "        <div class=\"c-meta\">".$this->getView()->wordTime($input['ctime'])." (".date('Y-m-d H:i:s', $input['ctime']).")";
            $returnstr .= "</div></div></div></li>";
        }

        return $this->json([$returnstr]);
    }

}
