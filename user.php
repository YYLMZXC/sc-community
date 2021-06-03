<?php
/**
 * 用户信息界面
 * */
 
$uid=intval($urlparams['_p0']);
if($uid==0){
    XModel::error("错误的参数");
}else{
    $userinfo=M('user')->where(['id'=>$uid])->find();
    $fileinfo=M('directory')->field('count(*) as num')->where(['uid'=>$uid])->find();
    $bbsinfo=M('bbslist')->field('count(*) as num')->where(['uid'=>$uid])->find();
    $authflags="";
    if($userinfo['isadmin'])$authflags.=XModel::getmGroup(100);
    $authflags.=XModel::getmGroup($userinfo['mgroup']);
    if(empty($fileinfo['num']))$fileinfo['num']=0;
    if(empty($bbsinfo['num']))$bbsinfo['num']=0;
    XModel::Set('subtitle',$userinfo['nickname']."的个人主页");
    XModel::Set('authflags',$authflags);
    XModel::Set('nickname',$userinfo['nickname']);
    XModel::Set('userimg',XModel::getHeadimg($userinfo['headimg'], $userinfo['nickname']));
    XModel::Set('lastTime',TimeFormat($userinfo['last_login_time']));
    XModel::Set('filecount',$fileinfo['num']);
    XModel::Set('bbscount',$bbsinfo['num']);
    XModel::Set('subtitle','');
    XModel::SetContent(XModel::Load("profile","user"));
}
