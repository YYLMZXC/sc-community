<?php
$id=intval($urlparams['_p0']);
$do=I('do');
if(empty($do)){
$modinfo=M("modlist")->where(['id'=>$id])->find();
XModel::SetTitle($modinfo['fullname']."-编辑Mod信息");
XModel::Set('id',$id);
XModel::Set('author',$modinfo['author']);
XModel::Set('name',$modinfo['name']);
XModel::Set('fullname',$modinfo['fullname']);
XModel::Set('enname',$modinfo['enname']);
XModel::Set('description', $modinfo['description']);
XModel::Set('modflags',htmlentities($modinfo['modflags']));
XModel::Set('appver',$modinfo['supportVersion']);
$flagshtml="";
$flags=M('modflags')->where('_order','asc')->select();
for($i=0;$i<4;$i++){
    $flagshtml.='<div style="display: inline-block;">';
    for($j=0;$j<4;$j++){
        $pos=$j+$i*4;
        if($pos>=count($flags))continue;
        $arr['svg']=XModel::loadSvg($flags[$pos]['icon'],12,12,"#fff");
        $arr['name']=$flags[$pos]['name'];
        $flagshtml.='<div class="list-group-item" data-id="'.$flags[$pos]['id'].'">'.XModel::LoadFrom("modflag","mods",true,$arr)."</div>";
    }
    $flagshtml.="</div>";
}

XModel::Set('flagshtml',$flagshtml);
XModel::SetContent(XModel::Load("edit","mods"));

    
}elseif($do=='submit'){
    $name=I('name');
    $fullname=I('fullname');
    $enname=I('enname');
    $desc=I('desc');
    $appver=I('appver');
    $author=I('author');
    $flags=I('flags');
    using("XString");
    $flagsarr=json_decode($flags,true);
    if(!is_array($flagsarr))$flags="[]";
    $hashCode=XString::hashCode($fullname);
    $check=M("modlist")->where(['id'=>$id,'uid'=>S('uid')])->find();
    if(!empty($check)){
        if(empty($fullname)||empty($desc)||empty($appver)||empty($author)){
            XModel::error("mod全称或描述或适应游戏版本或作者不能为空");
        }else{
            M('modlist')->where(['id'=>$id])->save(['fullname'=>$fullname,'hash'=>$hashCode,'name'=>$name,'enname'=>$enname,'description'=>$desc,'supportVersion'=>$appver,'author'=>$author,'lastupdatetime'=>time(),'modflags'=>$flags]);
            XModel::success("修改Mod成功","/com/mods/list?t=".rand(111,999),"返回Mods列表");
        }
    }else{
        XModel::error("Mod已删除，不可进行修改");
    }
    
}
