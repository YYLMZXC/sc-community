<?php
using("model/ModList");
$modid=intval($urlparams['_p0']);
$page=intval(I('page'));
XModel::Set("id",$modid);
$data=M('modlist')->where(['id'=>$modid])->find();
XModel::Set('ifblock',true);
if($data==false)XModel::error("参数错误");
if($data['uid']==S('uid'))$userinfo=$user;
else $userinfo=M('user')->where(['id'=>$data['uid']])->find();
$blockcate=M("modblockcate")->where(['modid'=>$modid])->select();
foreach($blockcate as $k=>$v){
    $imghtml="";
    $blocks=M('modblocks')->where(['modid'=>$modid,'cateid'=>$v['id']])->select();
    foreach($blocks as $ka=>$va){
        $imghtml.="<a href=\"#\"><img width=\"16\" height=\"16\" src=\"data:image/png;base64,".$va['icon']."\">".$va['name']."</a>";
    }
    $blockshtml.="<tr><th class=\"item-list-type-left\" style=\"width:auto;padding: 5px;\">".$v['name']."</th><td class=\"item-list-type-right\"><span style=\"margin-right:14px;\">".$imghtml."</span></td></tr>";
}
XModel::SetTitle($data['fullname']."-方块生物buff资料");
XModel::Set("blockshtml",$blockshtml);
XModel::Set("flagshtml",ModList::getModsFlags($data['modflags'],14,14,"#ffffff"));
XModel::Set("modname",$data['fullname']);
XModel::Set("moddesc",$data['description']);
XModel::Load("blocklist","mods",false);