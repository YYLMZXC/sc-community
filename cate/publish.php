<?php
/**
 * 发布帖子
 **/
if(empty(S('uid'))){
    $outconfig['error']="请先登录";
    echo xxfunc::loadRes("error.html","index",$outconfig);die;
}
$do=I('do');
$id=$urlparams['_p0'];
$cate=$msql->table("bbscate")->field('name,authority')->where(['id'=>$id])->find();
    $outconfig['title']=$cate['name'];
    $outconfig['catename']=$cate['name'];
    $outconfig['cid']=$id;
    $outconfig['subtitle']="发帖";
if(empty($do)||$do!='submit'){
    $container= xxfunc::loadRes('publish.html','model',$outconfig);
    $outconfig['container']=$container;
    echo xxfunc::loadRes('container.html','model',$outconfig);
}else{
    
}