<?php
$descid=intval($urlparams['_p0']);
$do=I('do');
$descinfo=M("modrelease")->where(['id'=>$descid])->find();
$modinfo=M("modlist")->field("id,name,uid")->where(['id'=>$descinfo['modid']])->find();
if($modinfo['uid']!=S('uid'))XModel::error('你没有操作权限');
XModel::Set("modid",$descinfo['modid']);
XModel::Set("descid",$descinfo['id']);
XModel::Set("modname",$modinfo['name']);
XModel::Set("descname",$descinfo['version']);
XModel::Set("version",$descinfo['version']);
XModel::SetTitle($modinfo['name']."-添加文件");
$vlist=M("apiversions")->select();
$vhtml='<select id="apiv">';
$vhtml.="<option value=\"0\">无</option>";
foreach ($vlist as $k=>$v){
    $vhtml.="<option value=".$v['id'].">".$v['version']."</option>";
}
$vhtml.='</select>';

XModel::Set("vhtml",$vhtml);
if(empty($do)){
    XModel::SetContent(XModel::Load("addfile","mods"));
}else{
    $appver=I('appver');
    $faddr=I('faddr');
    $filename=I('fname');
    $ftype=I('ftype');
    $vid=I('vid');
    if(empty($appver)||empty($faddr)||empty($filename)||empty($ftype)){
        XModel::error("适用版本或文件不可为空".$fname.$appver);
    }
    M('modlist')->where(['id'=>$descinfo['modid']])->save(['lastupdatetime'=>time()]);
    M('moddown')->add(['descid'=>$descid,'url'=>$faddr,'modid'=>$descinfo['modid'],'addTime'=>time(),'name'=>$filename,'appver'=>$appver,'type'=>$ftype,'uid'=>S('uid'),'apiv'=>$vid]);
    XModel::success("添加成功","/com/mods/desclist/".$descinfo['modid'],"返回发布说明");
}
