<?php
class Bbslist extends XModel{
    //获取该分类下的帖子列表
    public static function getCateView($cid, $type, $page){
        //1最新2热贴3直播...
        if (empty($type) || !is_numeric($type)) $type = 1;
        $userinfo = []; //用户信息缓存
        $model = M('bbslist')->where(['cid' => $cid,'stat'=>1]);
        $model2 = null;
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
        $list = $model2->limit($page * 15, 15)->select();
        $str = self::formatBbslist($list, $page, $cid);
        return $str;
    }
    //格式化一个帖子的布局
    public static function formatOneBbs($i, $v, $userinfo)
    {
        $usericon = self::getHeadimg($userinfo[$v['uid']]['headimg'], $userinfo[$v['uid']]['nickname']);
        $arr = ['title' => $v['title'], 'username' => $userinfo[$v['uid']]['nickname'], 'uid' => $v['uid'], 'bid' => $v['id'], 'views' => $v['views'], 'replies' => $v['replies'], 'userflags' => self::decodeFlags($userinfo[$v['uid']]['flags']), 'addTime' => TimeFormat($v['addTime']), 'usericon' => $usericon, 'bbsflags' => self::decodeFlags($v['flags']) ,'WEBPATH'=>parent::Get("WEBPATH")];
        return parent::LoadFrom("bbsitem", 'bbs' ,true ,$arr);
    }    
    //格式化为帖子列表
    public static function formatBbslist($datalist, $page, $cid)
    {
        $bbsitemlist = "";
        foreach ($datalist as $k => $v) {
            if (empty($userinfo[$v['uid']])) {
                $userinfo[$v['uid']] = M('user')->where(['id' => $v['uid']])->find();
            }
            $bbsitemlist .= self::formatOneBbs($k + 1, $v, $userinfo);
        }
        if (empty($bbsitemlist)) $bbsitemlist = "暂无帖子,需要你的火力支援";
        //计算总数
        $c = M('bbslist')->field('count(*) as num')->where(['cid' => $cid,'stat'=> 1])->find()['num'];
        if(empty($c))$c=1;
        
        $cateinfo=M("bbscate")->field("name")->where(['id'=>$cid])->find();
        parent::Set('total',$c);
        parent::Set('webTitle',$cateinfo['name']."帖子列表");
        parent::Set('catename',$cateinfo['name']);
        parent::Set('bbsitemlist',$bbsitemlist);
        parent::Set('pagehtml',parent::pageHtml("/com/cate/".$cid, $page , parent::$Conf['total'],15));
        parent::SetContent(self::Load("bbslist", "bbs"));
    }    
    
}
