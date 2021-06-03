<?php

$descid=intval($urlparams['_p0']);
$data=M('moddown')->where(['descid'=>$descid,'uid'=>S('uid')])->select();
$modinfo=M("modlist")->field("fullname")->where(['id'=>$data[0]['modid']])->find();
XModel::SetTitle($modinfo['fullname']."-更新日志的文件列表");
$lishtml="";
$outconfig['modid']=$data[0]['modid'];
foreach($data as $k=>$v){
    $v['url']=XModel::Get("CDNURL").$v['url'];
    $lishtml.=XModel::LoadFrom("fileitem","mods",true,$v);
}
if(empty($lishtml))$lishtml="还没有添加任何文件";
XModel::Set("modid",$data[0]['modid']);
XModel::Set("lishtml",$lishtml);
XModel::SetContent(XModel::Load("filelist","mods"));
