<?php
if(!S('uid')){
    _print(300,'请先登录');
}else{
    using("System/IO/Upload");
    using("System/IO/Path");
    $up=new Upload();
    $up->setHost("");
    $up->setNeedDelPath("/www/wwwroot/cdn.aijiajia.xyz/");
    $up->setAcceptType(['*']);
    $res=$up->save($_FILES['file'],"/www/wwwroot/cdn.aijiajia.xyz/modsfile");
    if($res['code']>0)PR(0,$res['msg']);
    else{
        $p=new Path($res['name']);
        $type=$p->getExtension();
        if($type!=".zip"&&$type!='.scmod'&&$type!='.scbtex'&&$type!='.dae'&&$type!='.apk'){
            unlink($res['filepath']);
            PR(0,"不支持该文件格式的上传");
        }
        PR(1,"上传成功",["_extra"=>['url'=>$res['downUrl'],'size'=>$res['rsize'],'name'=>$res['name'],'type'=>$type]]);
    }
}