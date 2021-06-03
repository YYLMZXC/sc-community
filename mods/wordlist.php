<?php
$modid=intval($urlparams['_p0']);
$page=intval(I('page'));
$data=$msql->table('modlist')->where(['id'=>$modid])->find();
$outconfig['subname']="Mod教程";

if($data==false)scbbs::error("参数错误");
if($data['uid']==S('uid'))$userinfo=$user;
else $userinfo=$msql->table('user')->where(['id'=>$data['uid']])->find();
$outconfig['ifword']=1;
$outconfig['flagshtml']=$scbbs->getModsFlags($data['modflags'],14,14,"#ffffff");
$outconfig['modname']=$data['fullname'];
$outconfig['id']=$modid;
$outconfig['moddesc']=$data['description'];
//获得word列表
$itemlist=$msql->table("modwords")->where(['modid'=>$modid])->order('addTime','desc')->limit($page*15,15)->select();
$list="";
foreach($itemlist as $k=>$v){
    $list.="<li><a target=\"_blank\" href='/com/mods/worditem/".$v['id']."'>".$v['title']."</a></li>";
}
$outconfig['wordlist']=$list;
xxfunc::show("wordlist","mods",$outconfig,true);