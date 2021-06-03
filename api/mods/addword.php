<?php
$modid=I('modid');
$title=I('title');
$text=I('content');
$modinfo=M("modlist")->where(['id'=>$modid])->find();
if($modinfo==false)PR(300,"不存在此Mod或此Mod已被删除");
else if($modinfo['uid']!=S('uid'))PR(300,"你没有权限操作此Mod");
else if(empty($title)||empty($text))PR(300,'标题或内容不可为空');
else {
    $text=base64_encode($text);
    M("modwords")->add(['uid'=>S('uid'),'modid'=>$modid,'title'=>$title,'content'=>$text]);
    PR(200,'添加教程成功');
}