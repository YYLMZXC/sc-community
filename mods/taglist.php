<?php
$modid=intval($urlparams['_p0']);
$page=intval(I('page'));
$outconfig['prevpage']=$page-1;
$outconfig['nextpage']=$page+1;
$outconfig['iftag']=1;
$outconfig['subname']="版本历史";

$data=$msql->table('modlist')->where(['id'=>$modid])->find();
if($data==false)scbbs::error("参数错误");
if($data['uid']==S('uid'))$userinfo=$user;
else $userinfo=$msql->table('user')->where(['id'=>$data['uid']])->find();

$outconfig['flagshtml']=$scbbs->getModsFlags($data['modflags'],14,14,"#ffffff");
$outconfig['nickname']=$userinfo['nickname'];
$outconfig['usericon']=scbbs::getHeadimg($userinfo['headimg'],$userinfo['nickname']);;
$outconfig['modname']=$data['fullname'];
$outconfig['id']=$modid;
$outconfig['moddesc']=$data['description'];
//获得release列表
$itemlist=$msql->table("modrelease")->where(['modid'=>$modid])->order('addTime','desc')->limit($page*5,5)->select();
$list="";
foreach($itemlist as $k=>$v){
    $moddowns=$msql->table('moddown')->where(['descid'=>$v['id']])->select();
    $v['md']=md5($outconfig['modname']+$v['version']);
    $v['version']=$v['version'];
    $v['versionmd']=substr($v['md'],0,6);
    $v['time']=scbbs::formatTime($v['addTime']);
    $v['lilist']=xxfunc::formatLiList($moddowns);
    $list.=xxfunc::show("tagitem","mods",$v);
}
$outconfig['itemlist']=$list;
$outconfig['verhtml']=xxfunc::getAppVerHtml($data['supportVersion']);
xxfunc::show("taglist","mods",$outconfig,true);