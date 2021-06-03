<?php
    using("XString");
    $modver=I('modver');
    $desc=I('desc');
    $modid=intval(I('modid'));
    $hashCode=XString::hashCode($modver);
    $check=M("modrelease")->where(['hash'=>$hashCode,'uid'=>S('uid'),'modid'=>$modid])->find();
    if(empty($check)){
        if(empty($modver)||empty($desc)){
            PR(300,"版本或说明不能为空");
        }else{
            M('modlist')->where(['id'=>$modid])->save(['lastupdatetime'=>time()]);
            M('modrelease')->add(['version'=>$modver,'hash'=>$hashCode,'description'=>$desc,'modid'=>$modid,'uid'=>S('uid'),'addTime'=>time()]);
            PR(200);
        }
    }else{
        PR(300,"该版本的发布说明已存在，请勿重复添加");
    }