<?php
$rid=intval($urlparams['_p0']);
using("model/Replylist");

XModel::SetTitle("查看回复列表");
$replyinfo = M('bbsreplyto')->field('bid')->where(['rid' => $rid])->order('addTime', 'desc')->find();
XModel::Set('bid',$replyinfo['bid']);
XModel::Set('data',Replylist::getReplylistTo($rid,$page));
XModel::SetContent(XModel::Load("replytomain","bbs",true));
