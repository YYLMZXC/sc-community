<?php
if(empty(S('uid')))XModel::error('请先登录');
using("model/Message");
XModel::SetTitle("SC中文社区-消息中心");
$total=M('message')->field('count(*) as num')->where(['uid'=>S('uid')])->find()['num'];
$data=M('message')->field('id')->where(['uid'=>S('uid')])->order('addTime','desc')->limit($page,10)->select();
$itemhtml="";
foreach($data as $k=>$v){
    $itemhtml.=Message::getMessage($v['id']);
}
if(empty($itemhtml))$itemhtml="还没有任何消息";
$arr['itemhtml']=$itemhtml;
$container=XModel::LoadFrom("message","user",true,$arr);
$container.=XModel::pageHtml(XModel::Get("WEBPATH")."/user/message",$page,$total);
XModel::SetContent($container);
