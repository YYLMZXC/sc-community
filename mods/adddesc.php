<?php
$do=I('do');
$modid=intval($urlparams['_p0']);
XModel::Set("id",$modid);
$modinfo=M("modlist")->field('fullname')->where(['id'=>$modid])->find();
XModel::SetTitle($modinfo['fullname']."-添加更新日志");
XModel::Set("modname",$modinfo['fullname']);
XModel::SetContent(XModel::Load("adddesc","mods"));