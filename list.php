<?php
$page=intval(I('Cursor'));
if($page<1)$page=0;//单独处理
$SortOrder=I('SortOrder');
$version=I('Version');
$type=I('Type');
$Action=I('Action');
$rate=I('FeedbackParameter');
$url=I('Url');
$userId=I('UserId');
$Feedback=I('Feedback');
$timeID=strtotime(date("Y-m-d"));
$q=M("stat")->where(['time'=>$timeID])->find();
if(empty($q)){
    M('stat')->add(['times'=>1,'time'=>$timeID]);
}else{
    if($Action=="feedback"){
        if($Feedback=="Rating"){
            $save['ftimes']=$q['ftimes']+1;
        }else if($Feedback=="PlayTime"){
            $save['mtimes']=$q['mtimes']+1;
        }else{
            $save['times']=$q['times']+1;
        }
    }else $save['times']=$q['times']+1;
    M('stat')->where(['id'=>$q['id']])->save($save);
}


if($Action=="feedback"&&$Feedback=="Rating"){
    using("Crypt");
    $ae=explode('/',$url);
    $encode=explode("data=",$ae[count($ae)-1])[1];
    $encode=Crypt::Decode($encode);
    $decode=json_decode($encode,true);
    $id=intval($decode['id']);
    $data=M('directory')->where(['id'=>$id])->find();
    $ratelist=json_decode($data['level'],true);
    $error = json_last_error();
    if(!empty($error))$ratelist=[];
    $ratelist[]=intval($rate);
    $encode=json_encode($ratelist);
    M('directory')->where(['id'=>$id])->save(['level'=>$encode,'rate'=>XModel::getRate($encode)]);
    PR(200,'评分成功');
}

function makeItem($name,$downurl,$type,$size,$level,$uid,$downTimes,$nickname,$addTime,$worldType){
    if(!empty($worldType)){
        return sprintf('<Result Name="%s" Url="%s" Type="%s" Size="%s" RatingsAverage="%s" UserId="%s" ExtraText="[%s](%s人次下载, %s上传于%s)" />',$name,$downurl,$type,$size,XModel::getRate($level),$uid,$worldType,$downTimes,$nickname,date('Y/m/d',$addTime));        
    }else{
        return sprintf('<Result Name="%s" Url="%s" Type="%s" Size="%s" RatingsAverage="%s" UserId="%s" ExtraText="(%s人次下载, %s上传于%s)" />',$name,$downurl,$type,$size,XModel::getRate($level),$uid,$downTimes,$nickname,date('Y/m/d',$addTime));
    }
}
function PrintArray($arr,$page){
    using("Crypt");
    using("System/IO/Path");
    $outout='<Results NextCursor="'.($page+1).'">';
    
    foreach($arr as $k=>$v){
        $uinfo=M('user')->field('user,nickname')->where(['id'=>$v['uid']])->find();
        if(empty($v['fullname'])){
            $p=new Path($v['path']);
            $ydata=['directory','downTimes',$v['id'],"https://cdn.schub.top/gamefiles/".$uinfo['user'].$v['path'],"id"=>$v['id']];
            $urlCode="https://m.schub.top/com/down?data=".Crypt::Encode(json_encode($ydata));
            $outout.=makeItem($p->getFileName(),$urlCode,$p->TypeToSC($v['type']),$v['size'],$v['level'],$v['uid'],$v['downTimes'],$uinfo['nickname'],$v['addTime'],$v['worldType']);
        }else{
            $modfile=M("moddown")->field('url,name,downTimes,addTime')->where(['uid'=>$v['uid'],'modid'=>$v['id'],'appver'=>['like','%2.2%']])->order("addTime","desc")->find();
            if(empty($modfile))continue;
            $ydata=['directory','downTimes',$v['id'],"https://cdn.schub.top".$modfile['url'],"id"=>$v['id']];
            $urlCode="https://m.schub.top/com/down?data=".Crypt::Encode(json_encode($ydata));
            $outout.=makeItem($modfile['name'],$urlCode,"Mod",filesize("/www/wwwroot/cdn.aijiajia.xyz".$modfile['url']),$v['level'],$v['uid'],$modfile['downTimes'],$uinfo['nickname'],$modfile['addTime'],"Mod");
        }
    }
    $outout.='</Results>';
    echo $outout;
}
$where['type']=-1;
switch ($type) {
    case '世界':
$where['type']=1;
        break;
    case '方块材质':
$where['type']=2;
        break;
    case '人物皮肤':
$where['type']=3;
        break;
    case '家具包':
$where['type']=4;
        break;
    case 'Mod':
$where['type']=5;
break;
    default:
$where['type']=-1;
        break;
}

if(!empty($userId))$where['type']=-2;

$data=[];
if($where['type']==-1){
    if($SortOrder=='时间')$data=M('directory')->where(['isShow'=>1])->order('addTime','desc')->limit($page,15)->select();
    else $data=M('directory')->where(['isShow'=>1])->order('rate','desc')->limit($page,15)->select();
}else if($where['type']==-2){
    if($SortOrder=='时间')$data=M('directory')->where(['uid'=>S('uid')])->order('addTime','desc')->limit($page,15)->select();
    else $data=M('directory')->where(['uid'=>S('uid')])->order('rate','desc')->limit($page,15)->select();
}else if($where['type']==5){
    if($SortOrder=='时间')$data=M('modlist')->order('lastupdatetime','desc')->limit($page,15)->select();
    else $data=M('modlist')->order('rate','desc')->limit($page,15)->select();
}else{
    $where['isShow']=1;
    if($SortOrder=='时间')$data=M('directory')->where($where)->order('addTime','desc')->limit($page,15)->select();
    else $data=M('directory')->where($where)->order('rate','desc')->limit($page,15)->select();
}
PrintArray($data,$page);
?>