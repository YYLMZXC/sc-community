<?php
$modid=intval($urlparams['_p0']);
XModel::Set('id',$modid);
Xmodel::Set('subtitle','教程列表');
$page=intval(I('page'));
$total=M("modwords")->field('count(*) as num')->where(['modid'=>$modid,'uid'=>S('uid')])->count();
$total=intval($total);
$data=M("modwords")->where(['modid'=>$modid])->limit($page,$limit)->select();
$html="";
foreach($data as $k=>$v){
    $html.=XModel::LoadFrom("mworditem","mods",true,$v);
}

XModel::Set('list',$html);
$container=XModel::Load("mwordlist","mods",true);
$container.=Xmodel::pageHtml("/com/mods/mwordlist/$modid",$page,$total);
XModel::SetContent($container);