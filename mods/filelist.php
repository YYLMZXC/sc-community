<?php
$descid=intval($urlparams['_p0']);
$data=M('moddown')->where(['descid'=>$descid,'uid'=>S('uid')])->select();
$desc=M("modrelease")->field("version,modid")->where(['id'=>$descid])->find();
$modinfo=M("modlist")->field("name")->where(['id'=>$desc['modid']])->find();
XModel::SetTitle($modinfo['name']."-更新日志的文件列表");
$lishtml="";
foreach($data as $k=>$v){
    $v['url']=XModel::Get("CDNURL").$v['url'];
    $lishtml.=XModel::LoadFrom("fileitem","mods",true,$v);
}
if(empty($lishtml))$lishtml="还没有添加任何文件";
XModel::Set("modid",$desc['modid']);
XModel::Set("modname",$modinfo['name']);
XModel::Set("descname",$desc['version']);
XModel::Set("lishtml",$lishtml);
XModel::SetContent(XModel::Load("filelist","mods"));
