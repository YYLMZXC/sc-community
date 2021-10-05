<?php
$q=I("q");
using("model/ModList");
using("XString");
if(empty($q)){
    $data=M("modlist")->order('lastupdatetime','desc')->limit($page,10)->select();
    $total=M('modlist')->field('count(*) as num')->find()['num'];
    $total=intval($total);
}else{
    $data=M("modlist")->where(['fullname'=>['like',$q]])->order('lastupdatetime','desc')->limit($page,10)->select();
    $total=M('modlist')->field('count(*) as num')->where(['fullname'=>['like',$q]])->find()['num'];
    $total=intval($total);
}
$lishtml="";
XModel::SetTitle("Mods百科");
foreach($data as $kc=>$vc){
    $cnt=M("moddown")->field('SUM(downTimes) as num')->where(['modid'=>$vc['id']])->find();
    if(empty($cnt['num']))$cnt['num']=0;
    if(mb_strlen($vc['description'],"utf-8")>100)$vc['description']=mb_substr($vc['description'],0,100,"utf-8")."...";
    if(!empty($q)){
        $vc['fullname']=XString::FormatSearch($q,$vc['fullname']);
    }
    $vc['lasttime']=TimeFormat($vc['lastupdatetime']);
    $vc['downtimes']=$cnt['num'];
    $vc['modflags']=ModList::getModsFlags($vc['modflags'],12,12,"#ffffff");
    $lishtml.=XModel::LoadFrom("viewitem","mods",true,$vc);
}
XModel::Set("lis",$lishtml);
XModel::Set("q","输入搜索关键字");

if(!empty($q))XModel::Set("q",$q);
$container=XModel::Load("viewlist","mods");
$container.=XModel::pageHtml(XModel::Get("WEBPATH")."/mods/viewlist?q=".$q,$page,$total);
XModel::SetContent($container);
