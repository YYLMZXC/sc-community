<?php
$modid=intval($urlparams['_p0']);

using("model/ModList");

$data=M('modlist')->where(['id'=>$modid])->find();
XModel::SetTitle($data['fullname']."-发布历史");
XModel::Set("ifrelease",true);

if($data==false)XModel::error("参数错误");
if($data['uid']==S('uid'))$userinfo=$user;
else $userinfo=M('user')->where(['id'=>$data['uid']])->find();
M('modlist')->where(['id'=>$modid])->setInc('views',1);
$modflags=json_decode($data['modflags']);
$flagshtml="";
foreach($modflags as $k=>$v){
    $flags=M('modflags')->where(['id'=>$v])->find();
    $cc['svg']=XModel::loadSvg($flags['icon'],12,12,"#fff");
    $cc['name']=$flags['name'];
    $flagshtml.=XModel::LoadFrom("modflag","mods",true,$cc);
}
XModel::Set("flagshtml",$flagshtml);
XModel::Set("nickname",$userinfo['nickname']);
XModel::Set("imgsrc",$userinfo['headimg']);
XModel::Set("modname",$data['fullname']);
XModel::Set("moddesc",$data['description']);
XModel::Set("description",mb_substr($data['description'],0,20)."...");

//获得release列表
$itemlist=M("modrelease")->where(['modid'=>$modid])->order('addTime','desc')->limit($page,5)->select();
$list="";
foreach($itemlist as $k=>$v){
    $islastest=M("modrelease")->field('id')->where(['modid'=>$modid])->order('addTime','desc')->limit(0,1)->find();
    if($islastest!=false&&$islastest['id']<=$v['id'])$v['lastest']=XModel::LoadFrom("lastest","mods",true,[]);
    $moddowns=M('moddown')->where(['descid'=>$v['id']])->select();
    $v['usericon']=$outconfig['usericon'];
    $v['nickname']=$userinfo['nickname'];
    $v['imgsrc']=$userinfo['headimg'];
    $v['uid']=$userinfo['id'];
    $v['md']=strtoupper(md5(XModel::Get("modname").$v['version']));
    $v['time']=TimeFormat($v['addTime']);
    $v['versionmd']=substr(md5(XModel::Get("modname").$v['version']),0,6);
    $v['details']=ModList::formatReleaseList($moddowns);
    $v['markdownbody']=$v['description'];
    $list.=XModel::LoadFrom("releaseitem","mods",true,$v);
}
XModel::Set("releaselist",$list);
XModel::Set("verhtml",ModList::getAppVerHtml($data['supportVersion']));
XModel::Load("releaselist","mods",false);
