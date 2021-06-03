<?php
/**
 * 对回复回复
 * */
if(empty(S('uid'))){
    XModel::error("请先登录");
}
$rid=intval($urlparams['_p0']);
$bbsinfo=M('bbsreply')->where(['id'=>$rid])->find();
$userinfo=M('user')->where(['id'=>$bbsinfo['uid']])->find();
if($bbsinfo==false){
    XModel::error("回复被删除或不存在");
}else{
    XModel::Set("bbstitle",$userinfo['nickname']);
}
$btoken=XModel::getPublishToken();
if(empty($btoken)){XModel::setPublishToken();$btoken=XModel::getPublishToken();}
XModel::Set('bid',$bbsinfo['bbsid']);
XModel::Set('rid',$rid);
XModel::Set('token',$btoken);
XModel::SetContent(XModel::Load("replyto","bbs"));
