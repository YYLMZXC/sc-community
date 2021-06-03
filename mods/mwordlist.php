<?php
$modid=intval($urlparams['_p0']);
$outconfig['id']=$modid;
$outconfig['subtitle']="教程列表";
$page=intval(I('page'));
$total=$msql->table("modwords")->field('count(*) as num')->where(['modid'=>$modid,'uid'=>S('uid')])->find()['num'];
$total=intval($total);
$data=$msql->table("modwords")->where(['modid'=>$modid])->select();
$html="";
foreach($data as $k=>$v){
    $html.=xxfunc::show("mworditem","mods",$v);
}
$outconfig['list']=$html;
$container=xxfunc::show("mwordlist","mods",$outconfig);
$container.=scbbs::pageHtml("/com/mods/mwordlist/$modid",$page,$total);
$outconfig['container']=$container;
xxfunc::show("container","model",$outconfig,true);