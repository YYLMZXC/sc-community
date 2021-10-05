<?php
$modid=intval($urlparams['_p0']);
if(empty(S('uid')))XModel::error('请先登录');
$page=intval(I('page'));
$modinfo=M("modlist")->field("uid,name")->where(['id'=>$modid])->find();
$total=M("modblocks")->where(['modid'=>$modid])->count();
$data=M("modblocks")->where(['modid'=>$modid])->limit($page,$limit)->select();
$blisthtml="";
foreach ($data as $k=>$v){
    $blisthtml.='<li><div><img class="img-circle" width="32" height="32" src="https://cdn.schub.top'.$v['icon'].'"/>&emsp;'.$v['name'].'</div><div style="width:100%"><a class="btn btn-sm btn-info" href="javascript:;" onclick="delblock({$id});">删除</a></div></li>';
}
XModel::SetTitle($modinfo['name']."-方块列表");
XModel::Set("id",$modid);
XModel::Set("blisthtml",$blisthtml);
XModel::Set("modname",$modinfo['name']);
$html="";
$content=XModel::Load("mblocklist",'mods');
$content.=XModel::PageHtml("/com/mods/mblocklist/$modid",$page,$total);
XModel::SetContent($content);