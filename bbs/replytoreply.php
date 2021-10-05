
<?php
/**
 * 对回复回复
 * */
if(empty(S('uid'))){
    XModel::error("请先登录");
}
$rrid=intval($urlparams['_p0']);
$bbsinfo=M('bbsreplyto')->where(['id'=>$rrid])->find();
$userinfo=M('user')->where(['id'=>$bbsinfo['uid']])->find();

XModel::Set("rrid",$rrid);
XModel::Set("rid",$bbsinfo['rid']);
XModel::Set("bid",$bbsinfo['bid']);
XModel::Set('subtitle',"回复".$userinfo['nickname']);
$btoken=XModel::getPublishToken();
if(empty($btoken)){XModel::setPublishToken();$btoken=XModel::getPublishToken();}
XModel::Set('token',$btoken);
if($bbsinfo==false){
    XModel::error("回复被删除或不存在");
}else{
    XModel::Set('bbstitle',$userinfo['nickname']);
}
XModel::SetContent(XModel::Load("replytoreply","bbs",true));
