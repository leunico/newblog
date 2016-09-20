<?php
namespace app\Controllers;

use app\Model\Comment;

class CommentController extends Controller
{
    private $rules = [
                'contents' => 'required|min:3',
                'nickname' => 'required',
                'email' => 'required|email',
                #'website' => 'required',
            ];

    public function add()
    {
        $input = $this->getRequest()->request->all();
        $validator = $this->getValidation()->make($input, $this->rules);

        if ($validator->passes()) {
            $input['ctime'] = time();
            $input['ip'] = $this->getRequest()->getClientIP();

var_dump($input);die();
        } else {
            //未通过
            //输出错误消息
            print_r($validator->messages()->all()); // 或者 $validator->errors();
        }
        die();


        $comment = self::$models->Comment;
        //$comment->IpLimit($fields['ip']); //防止评论灌水攻击
        $fields['contents'] = $comment->SelfXssattack($fields['contents']); //防止Xss攻击
        if(strstr($fields['cid'],'-')){
            $parents = explode('-', $fields['cid']);
            $fields['cid'] = $parents[0];
            $tomail = $parents[1];
            $commentp = $comment->getOneComment('id',$tomail);
            $fields['parent'] = $commentp ? $commentp['id'].','.$commentp['nickname'] : '';
            $fidname = "<a href=\"#comment-".$commentp['id']."\" rel=\"nofollow\" class=\"cute\">@".$commentp['nickname']."</a>";
        }elseif(!empty($fields['cid'])){
            $commentp = $comment->getOneComment('id',$fields['cid']);
            $fields['parent'] = $commentp ? $commentp['id'].','.$commentp['nickname'] : '';
            $fidname = "<a href=\"#comment-".$commentp['id']."\" rel=\"nofollow\" class=\"cute\">@".$commentp['nickname']."</a>";
        }else{
            $fields['parent'] = '';
            $fidname = '';
        }
        $result = $comment->InsertComment($fields);
        if(!$result){
            AjaxError('评论添加失败，多次失败请联系站长！');
        }else{
            $comment->Ifuser($fields['nickname'],$fields['email'],$fields['website']); //记录游客信息
            if(EMAIL_SENT_FOR_REPLY && $fields['cid'] > 0 && !empty($commentp)) $comment->SendMail(self::$models->SmtpMail,$fields['contents'],$commentp);//邮件
            $toid = empty($commentp) ? '#' : $commentp['id'];
            echo "<li class=\"comment even thread-even depth-1 clearfix\" id=\"comment-".$toid."><span class=\"comt-f\"></span> ";
            echo "  <div class=\"c-avatar\"><img alt='' src='".IMG_TXING."' class='avatar avatar-50 photo' height='50' width='50' /><div class=\"c-main\" id=\"div-comment-".$toid.">";
            echo "     <p style=\"color:#8c8c8c;\"><span class=\"c-author\">".$fields['nickname']."</span></p><p>".$fidname.EmojiH($fields['contents'])."</p>";
            echo "        <div class=\"c-meta\">".wordTime($fields['ctime'])." (".date('Y-m-d H:i:s', $fields['ctime']).")";
            echo "</div></div></div></li>";
        }
        #return $this->json(['jj', 'kk']);
    }

}
