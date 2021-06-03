<?php

$outconfig['subtitle']="发布Mod";
$do=I('do');
using("XString");
if(empty($do)){
$flagshtml="";
$flags=M('modflags')->order('_order','asc')->select();
for($i=0;$i<4;$i++){
    $flagshtml.='<div style="display: inline-block;" class="marginB">';
    for($j=0;$j<4;$j++){
        $pos=$j+$i*4;
        if($pos>=count($flags))continue;
        $arr['svg']=XModel::loadSvg($flags[$pos]['icon'],12,12,"#fff");
        $arr['name']=$flags[$pos]['name'];
        $arr['id']=$flags[$pos]['id'];
        $flagshtml.=XModel::LoadFrom("modflag","mods",true,$arr);
    }
    $flagshtml.="</div>";
}
XModel::Set('flags',$flagshtml);
XModel::SetContent(XModel::Load("add","mods"));
}elseif($do=='submit'){
    $name=I('name');
    $fullname=I('fullname');
    $enname=I('enname');
    $desc=I('desc');
    $appver=I('appver');
    $author=I('author');
    $flags=I('flags');
    $flagsarr=json_decode($flags,true);
    if(!is_array($flagsarr))$flags="[]";
    $hashCode=XString::hashCode($fullname);
    $check=M("modlist")->where(['hash'=>$hashCode,'uid'=>S('uid')])->find();
    if(empty($check)){
        if(empty($fullname)||empty($desc)||empty($appver)||empty($author)){
            XModel::error("mod全称或描述或适应游戏版本或作者不能为空");
        }else{
            M('modlist')->add(['fullname'=>$fullname,'hash'=>$hashCode,'name'=>$name,'enname'=>$enname,'description'=>$desc,'supportVersion'=>$appver,'author'=>$author,'lastupdatetime'=>time(),'modflags'=>$flags,'uid'=>S('uid'),'addTime'=>time()]);
            XModel::success("添加Mod成功","/com/mods/list?t=".rand(111,999),"返回Mods列表");
        }
    }else{
        XModel::error("Mod已存在，请勿重复添加");
    }
}
