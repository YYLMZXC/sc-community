<?php
$do=I('do');
$did=intval($urlparams['_p0']);//desc id
$outconfig['subtitle']="";
$descinfo=M("modrelease")->where(['id'=>$did])->find();
$modinfo=M("modlist")->field("name")->where(['id'=>$descinfo['modid']])->find();
XModel::SetTitle($modinfo['name']."-编辑版本");
XModel::Set('version',$descinfo['version']);
XModel::Set('description',base64_encode($descinfo['description']));
XModel::Set('id',$did);
XModel::Set('modname',$modinfo['name']);
XModel::Set('modid',$descinfo['modid']);
XModel::SetContent(XModel::Load("editdesc","mods"));
