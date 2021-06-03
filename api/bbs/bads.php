<?php

if(empty(S('uid')))PR(300,'请先登录');
using("System/Collections/ArrayList");
$bid=intval(I('bid'));
$rid=intval(I('rid'));
if($bid==0&&$rid==0){
    PR(300,'操作失败');
}else{
    if($bid>0&&$rid>0)PR(300,'操作失败');
    else if($bid>0){
        $data=M("bbslist")->where(['id'=>$bid])->find();
        $goods=intval($data['bads']);
        $goodsList=json_decode($data['badsList'],true);
        if(!is_array($goodsList))$goodsList=[];
        $a=new ArrayList($goodsList);
        if($a->Contains(S('uid'))){
            $a->Remove(S('uid'));
            $ngoodsList=$a->getArr();
            $goods=count($ngoodsList);
            M("bbslist")->where(['id'=>$bid])->save(['bads'=>$goods,'badsList'=>json_encode($ngoodsList)]);
            PR(200,'取消踩成功',['data'=>$goods]);
        }else{
            array_push($goodsList,S('uid'));
            $goods=count($goodsList);
            M("bbslist")->where(['id'=>$bid])->save(['bads'=>$goods,'badsList'=>json_encode($goodsList)]);
            PR(200,'踩成功',['data'=>$goods]);
        }
    }else if($rid>0){
        $data=M("bbsreply")->where(['id'=>$rid])->find();
        $goods=intval($data['bads']);
        $goodsList=json_decode($data['badsList'],true);
        if(!is_array($goodsList))$goodsList=[];
        $a=new ArrayList($goodsList);
        if($a->Contains(S('uid'))){
            $a->Remove(S('uid'));
            $ngoodsList=$a->getArr();
            $goods=count($ngoodsList);
            M("bbsreply")->where(['id'=>$rid])->save(['bads'=>$goods,'badsList'=>json_encode($ngoodsList)]);
            PR(200,'取消踩成功',['data'=>$goods]);
        }else{
            array_push($goodsList,S('uid'));
            $goods=count($goodsList);
            M("bbsreply")->where(['id'=>$rid])->save(['bads'=>$goods,'badsList'=>json_encode($goodsList)]);
            PR(200,'踩成功',['data'=>$goods]);
        }
    }
}


