<?php
using("Crypt");
class ModList extends XModel{
    //获取Mod的标签Html
    public static function getModsFlags($flags,$w=18,$h=18,$color="#333"){
        $modflags=json_decode($flags);
        $flagshtml="";
        foreach($modflags as $k=>$v){
            $flags=M('modflags')->where(['id'=>$v])->find();
            $cc['svg']=parent::loadSvg($flags['icon'],$w,$h,$color);
            $cc['name']=$flags['name'];
            $flagshtml.=parent::LoadFrom("modflag","mods",true,$cc);
        }
        return $flagshtml;
    }
    //格式化Release的列表
    public static function formatReleaseList($moddownlist){
        $html="";
        foreach($moddownlist as $k=>$v){
            $json=json_encode(['moddown','downTimes',$v['id'],XModel::Get("CDNURL").$v['url']]);
            $ydata="https://m.schub.top/com/down?data=".Crypt::Encode($json);
            $v['url']=$ydata;
            if(strstr($v['appver'],'|')){
             $dr=explode('|',$v['appver']);   
             foreach ($dr as $kd=>$vd){
                 $v['ver'].='<summary style="color:blueviolet;" class="signed-commit-badge signed-commit-badge-medium verified" style="float:right;" title="Commit signature">'.$vd.'</summary>';
            }
            }else{
            $v['ver']='<summary style="color:blueviolet;" class="signed-commit-badge signed-commit-badge-medium verified" style="float:right;" title="Commit signature">'.$v['appver'].'</summary>';                
            }
            $html.=parent::LoadFrom("resitem","mods",true,$v);
        }
        $arr['reslist']=$html;
        $arr['count']=count($moddownlist);
        return parent::LoadFrom("details","mods",true,$arr);
    }
    //格式化发布说明内容输出
    public static function formatRcontent($content){
        $lines=explode("\n",$content);
        $html="";
        $lastish=true;
        foreach($lines as $k=>$v){
            if($v[0]==' '&&$v[1]==' '){//双空格开头
                if(!$lastish){
                    $html.="<li>".$v."</li>";
                }else{
                    $html.="<ul><li>".$v."</li>";
                }
                $lastish=false;
            }else{
                if($lastish){
                    $html.="<h2>".$v."</h2>";
                }else{
                    $html.="</ul><h2>".$v."</h2>";
                }
                $lastish=true;
            }
        }
        return $html;
    }    
    //格式化版本标签
    public static function getAppVerHtml($text){
        $d=explode("|",$text);
        $out="";
        foreach($d as $k=>$v){
            if(empty($v))continue;
            $arr['ver']=$v;
            $out.= parent::LoadFrom("appveritem","mods",true,$arr);
        }
        return $out;
    }  
    
    
}