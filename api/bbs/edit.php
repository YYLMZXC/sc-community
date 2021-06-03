<?php
$title=I("title");
$content=I("content");
$bid=intval(I("bid"));
$ukey="user-publish-".S('uid');
if(empty(S('uid')))PR(300,"请先登录");
else if(empty($bid)){
    PR(300,'错误的参数');
}
else if(empty($title)||empty($content)){
    PR(300,'标题或内容不能为空');
}else if(strlen($content)<10){
    PR(300,'内容长度不能小于10字');
}else{
    $bbsinfo=M('bbslist')->where(['id'=>$bid])->find();
    if($bbsinfo['uid']!=S('uid')&&$user['isadmin']!=1){
        PR(300,'你没有权限编辑此帖');
    }else{
        M('bbslist')->where(['id'=>$bid])->save(['title'=>$title,'content'=>$content,'lastupdatetime'=>time(),'edit_user'=>S('uid')]);
        PR(200,'编辑成功');
    }
}