<?php

/**
 * 论坛功能库
 * */
class scbbs
{
    public function __construct($msql)
    { //传入msql_class库

    }
    //添加消息
    public function addMessage($uid,$fromuid,$msg,$bid,$rid,$type){
        //type 1对帖回复 2对回复回复
        M('message')->add(['uid'=>$uid,'fromuid'=>$fromuid,'noread'=>1,'msg'=>$msg,'bid'=>$bid,'rid'=>$rid,'type'=>$type,'addTime'=>time()]);
    }

    public static function formatBlockCate($cdata){
        $html="<select name=\"blockcate\" class=\"select\">";
        foreach($cdata as $k=>$v){
            $html.="<option value=\"".$v['id']."\">".$v['name']."</optoin>";
        }
        $html.="</select>";
        return $html;
    }
    //获取未读消息
    public function getNoReadMsg(){
        if(empty(S('uid')))return "";
        $msgnoreadcnt=$this->M('message')->field('count(*) as num')->where(['uid'=>S('uid'),'noread'=>1])->find()['num'];
        $msgnoreadcnt=intval($msgnoreadcnt);
        if($msgnoreadcnt==0)return "";
        return '<svg width="18" height="18" style="vertical-align: middle;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="white" d="M532 386.2c27.5-27.1 44-61.1 44-98.2 0-80-76.5-146.1-176.2-157.9C368.3 72.5 294.3 32 208 32 93.1 32 0 103.6 0 192c0 37 16.5 71 44 98.2-15.3 30.7-37.3 54.5-37.7 54.9-6.3 6.7-8.1 16.5-4.4 25 3.6 8.5 12 14 21.2 14 53.5 0 96.7-20.2 125.2-38.8 9.2 2.1 18.7 3.7 28.4 4.9C208.1 407.6 281.8 448 368 448c20.8 0 40.8-2.4 59.8-6.8C456.3 459.7 499.4 480 553 480c9.2 0 17.5-5.5 21.2-14 3.6-8.5 1.9-18.3-4.4-25-.4-.3-22.5-24.1-37.8-54.8zm-392.8-92.3L122.1 305c-14.1 9.1-28.5 16.3-43.1 21.4 2.7-4.7 5.4-9.7 8-14.8l15.5-31.1L77.7 256C64.2 242.6 48 220.7 48 192c0-60.7 73.3-112 160-112s160 51.3 160 112-73.3 112-160 112c-16.5 0-33-1.9-49-5.6l-19.8-4.5zM498.3 352l-24.7 24.4 15.5 31.1c2.6 5.1 5.3 10.1 8 14.8-14.6-5.1-29-12.3-43.1-21.4l-17.1-11.1-19.9 4.6c-16 3.7-32.5 5.6-49 5.6-54 0-102.2-20.1-131.3-49.7C338 339.5 416 272.9 416 192c0-3.4-.4-6.7-.7-10C479.7 196.5 528 238.8 528 288c0 28.7-16.2 50.6-29.7 64z"></path></svg><a title="'.$msgnoreadcnt.'条未读消息" href="/com/user/message"><i
style="width:20px;height:20px;border-radius:50%;background-color:red;display: inline-block;vertical-align: middle;font-style: normal;color: white;font-weight:bold;text-align:center;">'.$msgnoreadcnt.'</i></a>';
    }
    
    //获取消息
    public function getMessage($msgid){
        $data=M('message')->where(['id'=>$msgid])->find();
        $fromuser=M('user')->where(['id'=>$data['fromuid']])->find();
        $arr['nickname']=$fromuser['nickname'];
        $arr['fromuid']=$fromuser['id'];
        $arr['id']=$msgid;
        $arr['time']=self::formatTime($data['addTime']);
        $arr['msg']=$data['msg'];
        $text=xxfunc::show("messageitem","user",$arr);
        return $text;
    }

    //获取Mod的标签Html
    public function getModsFlags($flags,$w=18,$h=18,$color="#333"){
        $modflags=json_decode($flags);
        $flagshtml="";
        foreach($modflags as $k=>$v){
            $flags=$this->M('modflags')->where(['id'=>$v])->find();
            $cc['svg']=xxfunc::loadSvg($flags['icon'],$w,$h,$color);
            $cc['name']=$flags['name'];
            $flagshtml.=xxfunc::show("modflag","mods",$cc);
        }
        return $flagshtml;
    }

    public static function getmGroup($index){
        switch($index){
            case 100:return self::getAuthFlags("管理员");
            case 99:return self::getAuthFlags("开发者","#4CAF50");
            case 98:return self::getAuthFlags("SC玩家");
            case 97:return self::getAuthFlags("管理员");
            case 96:return self::getAuthFlags("管理员");
            case 95:return self::getAuthFlags("管理员");
        }
    }
    //获得贴子Html
    public function getView($id){
        global $outconfig;

        //处理内容
        $content = $data['content'];
        return [
            $data['title'],
            $parentinfo['name'],
            $cateinfo['name'],
            $data['title'],
            $content,
            self::decodeFlags($userinfo['flags']),
            $data['uid'],
            $userinfo['nickname'],
            $replyliststr,
            $replycnt,
            $id,
            $cateid,
            $this->, //帖子标签12
            self::getHeadimg($userinfo['headimg'], $userinfo['nickname']), //用户头像13
            self::formatTime($data['addTime']),
            $data['replies'],
            $data['views'],
            $data['goods'],
            $data['bads']
        ];
    }
    public static function getAuthFlags($name,$color="#444444"){
        //开发者 4CAF50
        return '<a href="javascript:;">
        <small class="label group-label inline-block" style="background-color: '.$color.';">'.$name.'</small></a>';
    }


    //标签对应图
    public static function flagToImg($str){
        $s=new Str($str);
        if ($s->startWidth("fa")) return '<i class="' . $str . '"></i>';
        else if($s->startWidth("http")) return "<img src=\"" . $str . "\">";
        else if($s->startWidth("<svg")) return $str;
        else return "";
    }

    //获取用户头像
    public static function getHeadimg($headimg, $nickname, $style=""){
        if (!empty($headimg)) return '<img id="usericon" class="user-picture" style="'.$style.'" src="' . $headimg . '" alt="' . $nickname . '" title="' . $nickname . '">';
        else return '<div class="user-icon" style="background-color: #795548;" data-original-title="" title="' . $nickname . '">' . mb_substr($nickname, 0, 1, "utf-8") . '</div>';
    }

    public static function formatTime($timestamp)
    {
        $h = date('H', $timestamp);
        if ($h < 18 && $h >= 12) $state = "下午";
        else if ($h >= 0 && $h < 6) $state = "凌晨";
        else if ($h >= 6 && $h < 12) $state = "上午";
        else if ($h >= 18 && $h < 24) $state = "晚上";
        return date("Y年m月d日 $state H:i", $timestamp);
    }
  
}
