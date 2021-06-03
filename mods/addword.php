<?php
$modid=intval($urlparams['_p0']);
$outconfig['subtitle']="添加Mod教程";
$modinfo=M("modlist")->where(['id'=>$modid])->find();
if($modinfo==false)XModel::error("不存在此Mod或此Mod已被删除");
else if($modinfo['uid']!=S('uid'))XModel::error("你没有权限操作此Mod");
else{
    XModel::Set("id",$modid);
    XModel::SetContent(XModel::Load("addword","mods"));
}





