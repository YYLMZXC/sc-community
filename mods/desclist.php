<?php
$modid=intval($urlparams['_p0']);
XModel::Set("id",$modid);
XModel::SetTitle("版本列表");
$page=intval(I('page'));
$modname=M("modlist")->where(['id'=>$modid])->field("name")->find();
$total=M("modrelease")->where(['modid'=>$modid,'uid'=>S('uid')])->count();
$data=M("modrelease")->where(['modid'=>$modid])->order('addTime','desc')->select();
$html="";
foreach($data as $k=>$v){
    $html.=XModel::LoadFrom("desclistitem","mods",true,$v);
}
XModel::Set("modname",$modname['name']);
XModel::Set("list",$html);
$container=XModel::Load("desclist","mods",true);
$container.=XModel::pageHtml(XModel::Get('WEBPATH')."/mods/desclist/$modid",$page,$total);
XModel::SetContent($container);