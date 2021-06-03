<?php
/**
 * 回帖
 * */
if(empty(S('uid'))){
    XModel::error("请先登录");
}
$bid=intval($urlparams['_p0']);
$bbsinfo=M('bbslist')->where(['id'=>$bid])->find();
$userinfo=M('user')->where(['id'=>$bbsinfo['uid']])->find();
XModel::Set('bid',$bid);
XModel::Set('subtitle',"回复".$bbsinfo['title']);
if($bbsinfo==false){
    XModel::error("帖子被删除或不存在");
}else{
    XModel::Set("bbstitle",$bbsinfo['title']);
}
$btoken=XModel::getPublishToken();
if(empty($btoken)){XModel::setPublishToken();$btoken=XModel::getPublishToken();}
XModel::Set('token',$btoken);
XModel::SetContent(XModel::Load("reply","bbs"));
