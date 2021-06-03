<?php
$str = "";$pi=1;
$list = M('bbscate')->where(['status' => 1, 'parent' => 0])->order('_order', 'desc')->select(); //获取一级分类
$data=[];
foreach ($list as $k => $v) {
    $glist = M('bbscate')->field("id,flag,name,description,titlecolor,color")->where(['parent' => $v['id']])->order('_order', 'desc')->select();
    foreach ($glist as $kk => $vv) {
        //获取该分类下的帖子数和回复数
        $n = M('bbslist')->field('sum(replies) as replycount,count(*) as listcount')->where(['cid' => $vv['id'],'stat'=>1])->find();
        //获取该分类下的第一个帖子
        $t = M('bbslist')->field('title,id,uid')->where(['cid' => $vv['id'],'stat'=>1])->order('id', 'desc')->limit(0, 1)->find();
        //获取用户信息
        $user = M('user')->field('nickname,headimg')->where(['id' => $t['uid']])->find();
        $vv['title']=$t['title'];
        $vv['nickname']=$user['nickname'];
        $vv['headimg']=$user['headimg'];
        $vv['uid']=$t['uid'];
        $vv['replycount']=$n['replycount'];
        $vv['listcount']=$n['listcount'];
        $data[]=$vv;
    }
}
PR(200,"Succ",$data);

