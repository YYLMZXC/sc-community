<?php
using("System/Collections/ArrayList");
$title=I("title");
$content=I("content");
$id=intval(I("cid"));
if(empty(S('uid')))PR(300,"请先登录");
else if(empty($id)){
    PR(300,'错误的参数');
}else if(empty($title)||empty($content)){
    PR(300,'标题或内容不能为空');
}else if(strlen($content)<10){
    PR(300,'内容长度不能小于10字');
}else if(XModel::getPublishToken()!=I('token')){
    PR(300,'token校验失败，请刷新重试!');
}else{
    $cate=M('bbscate')->where(['id'=>$id])->find();
    $arr=new ArrayList($cate['authority']);
    if(false){
        PR(300,'你没有权限在此发帖');
    }else{
        M('bbslist')->add(['uid'=>S('uid'),'cid'=>$id,'title'=>$title,'flags'=>'[]','content'=>$content,'addTime'=>time()]);
        M('user')->where(['id'=>S('uid')])->setInc('money',3);
        XModel::setPublishToken();
        
        PR(200,'发帖成功，经验+3');
    }
}