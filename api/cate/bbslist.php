<?php
$cid=intval(I("id"));

$model=M('bbslist')->field("id,uid,title,views,stat,top,replies,addTime")->where(['cid' => $cid,'stat'=>1]);
        switch ($type) {
            case 1:
                $model2 = $model->order('top desc,addTime desc', '');
                break;
            case 2:
                $model2 = $model->order('addTime', 'desc');
                break;
            case 3:
                $model2 = $model->order('addTime', 'desc');
                break;
            case 4:
                $model2 = $model->order('addTime', 'desc');
                break;
            case 5:
                $model2 = $model->order('addTime', 'desc');
                break;
            default:
                $model2 = $model;
                break;
        }
$list = $model2->limit($page, $limit)->select();
foreach ($list as $k=>$v){
    $user=M("user")->field("nickname,headimg")->where(['id'=>$v['uid']])->find();
    $list[$k]['nickname']=$user['nickname'];
    $list[$k]['addTime']=TimeFormat($v['addTime']);
    $list[$k]['headimg']=XModel::getHeadImg($user['headimg'],$user['nickname']);
}
PR(200,"Succ",$list);