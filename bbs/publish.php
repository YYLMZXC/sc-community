<?php
/**
 * 回帖
 * */
if(empty(S('uid'))){
    XModel::error("请先登录");
}
$cateid=intval($urlparams['_p0']);
$cateinfo=M('bbscate')->where(['id'=>$cateid])->find();
$btoken=XModel::getPublishToken();
if(empty($btoken)){XModel::setPublishToken();$btoken=XModel::getPublishToken();}
XModel::SetTitle($cateinfo['name']."发帖");
XModel::Set('catename',$cateinfo['name']);
XModel::Set('cid',$cateid);
XModel::Set('token',$btoken);
XModel::SetContent(XModel::Load("publish","bbs",true));
