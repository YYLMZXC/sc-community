<?php

if(empty(S('uid')))XModel::error('请先登录');
$page=intval(I('page'));
XModel::SetTitle("我的Mods列表");
using("model/ModList");
$total=M("modlist")->field('count(*) as num')->where(['uid'=>S('uid')])->find()['num'];
$total=intval($total);
$data=M("modlist")->where(['uid'=>S('uid')])->select();
$html="";
foreach($data as $k=>$v){
    $v['modflags']=ModList::getModsFlags($v['modflags'],14,14,"#ffffff");
    $html.=XModel::LoadFrom("listitem","mods",true,$v);
}
XModel::Set("list",$html);
$container=XModel::Load("list","mods");
$container.=Xmodel::pageHtml(XModel::Get("WEBPATH")."/mods/list",$page,$total);
XModel::SetContent($container);