<?php

$modid=intval($urlparams['_p0']);
$do=I('do');
$name=I('name');

XModel::SetTitle($modinfo['fullname']."-添加方块分类");
if($modid==0)XModel::error('错误的参数');
if($do!='submit'){
    XModel::Set("modid",$modid);
    XModel::SetContent(XModel::Load("addblockcate","mods"));
}else{
    if(empty($name)){
        XModel::error("名称不可为空");
    }else{
        $cdata=$msql->table("modblockcate")->where(['uid'=>S('uid'),'name'=>$name])->find();
        if($cdata!=false)XModel::error("该分类已存在");
        else{
            $msql->table("modblockcate")->add(['uid'=>S('uid'),'modid'=>$modid,'name'=>$name]);
            XModel::success("添加成功",XModel::Get("WEBPATH")."/mods/mblocklist/".$modid,"返回列表");
        }
    }
}
