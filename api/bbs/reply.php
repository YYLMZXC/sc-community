<?php

/**
 *
 * API回复
 * 
 **/
$content = I('replyText');
$bid = intval(I("bid"));
$rid = intval(I("rid"));
$type=intval(I('type'));
$rrid=intval(I('rrid'));
function getMsg($str){
    $str=strip_tags($str);
    if(mb_strlen($str,"utf-8")<15)return $str;
    else return mb_substr($str,0,15,"utf-8")."...";
}
if(XModel::getPublishToken()!=I('token')){
    PR(300,'token校验失败，请刷新重试!');
}
if (empty(strip_tags($content)) || (empty($rid) && empty($bid))) {
    PR(300, "参数不可以为空!");
} else {
    if ($type==1) {
        //对贴回复
        if($bid==0)PR(300,'参数缺失');
        $check = M('bbslist')->where(['id' => $bid])->find();
        if ($check == false) {
            PR(300, "回复失败，此贴已被删除");
        }
        M('bbsreply')->add(['content' => $content, 'uid' => S('uid'), 'bbsid' => $bid, 'addTime' => time()]);
        M('bbslist')->where(['id' => $bid])->setInc('replies', 1);
        M('user')->where(['id' => S('uid')])->setInc('money', 3);
        if($check['uid']!=S('uid'))XModel::addMessage($check['uid'],S('uid'),getMsg($content),$bid,$rid,1);
        PR(200, "回复成功，经验+3");
    } else if($type==3){//对回复回复
        if($bid==0||$rid==0||$rrid==0)PR(300,'参数缺失');
        $check = M('bbsreplyto')->where(['id' => $rrid])->find();
        if ($check == false) {
            PR(300, "回复失败，此回复已被删除");
        }
        M('bbsreplyto')->add(['content' => $content, 'uid' => S('uid'), 'rid' => $rid,'bid'=>$bid,'toid'=>$rrid,'addTime' => time()]);
        M('bbslist')->where(['id' => $bid])->setInc('replies', 1);//回复数+1
        M('user')->where(['id' => S('uid')])->setInc('money', 3);//经验+3
        if($check['uid']!=S('uid'))XModel::addMessage($check['uid'],S('uid'),getMsg($content),$check['bid'],$rid,2);
        PR(200, "回复成功，经验+3");
        
    }else if($type==2){ //对评论回复
        if($bid==0||$rid==0)PR(300,'参数缺失');
        $check = M('bbsreply')->where(['id' => $rid])->find();
        if ($check == false) {
            PR(300, "回复失败，此回复已被删除");
        }
        M('bbsreplyto')->add(['content' => $content, 'uid' => S('uid'), 'rid' => $rid,'bid'=>$bid, 'addTime' => time()]);
        M('bbslist')->where(['id' => $bid])->setInc('replies', 1);//回复数+1
        M('user')->where(['id' => S('uid')])->setInc('money', 3);//经验+3
        if($check['uid']!=S('uid'))XModel::addMessage($check['uid'],S('uid'),getMsg($content),$check['bid'],$rid,2);
        PR(200, "回复成功，经验+3");
    }
}
