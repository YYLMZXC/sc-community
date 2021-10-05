<?php
using("model/ModList");
$modid=intval($urlparams['_p0']);
$data=M('modlist')->where(['id'=>$modid])->find();
XModel::Set('subname',"Mod教程");
if($data==false)XModel::error("参数错误");
if($data['uid']==S('uid'))$userinfo=$user;
else $userinfo=M('user')->where(['id'=>$data['uid']])->find();
XModel::Set('ifword',1);
XModel::Set('flagshtml',ModList::getModsFlags($data['modflags'],14,14,"#ffffff"));
XModel::Set('modname',$data['fullname']);
XModel::Set('id',$modid);
XModel::Set('moddesc',$data['description']);
//获得word列表
$itemlist=M("modwords")->where(['modid'=>$modid])->order('addTime','desc')->limit(XModel::Get("page"),15)->select();
$list="<ul>";
foreach($itemlist as $k=>$v){
    $list.="<li><a target=\"_blank\" href='/com/mods/worditem/".$v['id']."'>".$v['title']."</a></li>";
}
$list.="</ul>";
XModel::Set('wordlist',$list);
XModel::Load("wordlist","mods",false);