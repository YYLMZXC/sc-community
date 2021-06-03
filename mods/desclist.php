<?php
$modid=intval($urlparams['_p0']);
XModel::Set("id",$modid);
XModel::SetTitle("更新日志列表");
$page=intval(I('page'));
$total=M("modrelease")->field('count(*) as num')->where(['modid'=>$modid,'uid'=>S('uid')])->find()['num'];
$total=intval($total);
$data=M("modrelease")->where(['modid'=>$modid])->order('addTime','desc')->select();
$html="";
foreach($data as $k=>$v){
    $html.=XModel::LoadFrom("desclistitem","mods",true,$v);
}
XModel::Set("list",$html);
$container=XModel::Load("desclist","mods",true);
$container.=XModel::pageHtml("/com/mods/desclist/$modid",$page,$total);
XModel::SetContent($container);