<?php
$modid=intval($urlparams['_p0']);
if(empty(S('uid')))scbbs::error('请先登录');
$page=intval(I('page'));
$outconfig['subtitle']="方块列表";
$total=$msql->table("modlist")->field('count(*) as num')->where(['uid'=>S('uid')])->find()['num'];
$total=intval($total);
$outconfig['id']=$modid;
$data=$msql->table("modlist")->where(['uid'=>S('uid')])->select();
$html="";
$outconfig['container']=xxfunc::show("mblocklist",'mods',$outconfig);
xxfunc::show("container","model",$outconfig,true);