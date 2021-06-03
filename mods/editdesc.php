<?php
$do=I('do');
$did=intval($urlparams['_p0']);//desc id
$outconfig['subtitle']="";
$descinfo=M("modrelease")->where(['id'=>$did])->find();
$modinfo=M("modlist")->field("fullname")->where(['id'=>$descinfo['modid']])->find();
XModel::SetTitle($modinfo['fullname']."-修改更新日志");
XModel::Set('version',$descinfo['version']);
XModel::Set('description',base64_encode($descinfo['description']));
XModel::Set('id',$did);
XModel::Set('mid',$descinfo['modid']);
XModel::SetContent(XModel::Load("editdesc","mods"));
