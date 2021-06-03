<?php
/**
 * 编辑帖子
 * */
$bid=intval($urlparams['_p0']);
if(empty(S('uid'))){
    XModel::error("请先登录");
}
$bbsinfo=M('bbslist')->field("title")->where(['id'=>$bid])->find();
XModel::SetTitle("编辑帖子-".$bbsinfo['title']);
XModel::Set("bid",$bid);
XModel::Set("bbstitle",$bbsinfo['title']);
XModel::SetContent(XModel::Load("edit","bbs"));