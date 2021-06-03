<?php
/**
 * MYSQL数据操作类
 * Powered By xxq
 * <Date:2020-08-17></Date:2020-08-17>
 * 读写分离试用
**/
class xxsql{
    private $error=true;//是否输出日志
    private $tablePrefix="";//表前缀
    private $tableName="";//表名
    private $workMode=0;//工作模式 0正常 1为主从分离模式
    private $sqlServer=[];//数据库连接信息
    private $where="";//条件sql语句缓存
    private $field="*";//域sql语句缓存
    private $debugMode=false;//调试模式为true,则返回最终sql语句
    private $con;//数据库连接缓存
    private $finalSql="";//最后的sql语句
    private $order="";//排序sql语句
    private $limit="";//限制sql语句

    //添加一个sql服务器
    public function addAserver($src,$user,$db,$pwd,$type){                
        array_push($this->sqlServer,['user'=>$user,'pwd'=>$pwd,'db'=>$db,'src'=>$src,'type'=>$type]);
        $this->_connect(0);
        return $this->sqlServer;
    }
    //设置是否输出日志
    public function setLogOn($on){
        $this->error=$on;
        return $this;
    }
    //构造方法
    public function __construct($sql_src,$sql_user,$sql_pass,$db_name){        
        if(empty($sql_user)||empty($sql_pass)||empty($sql_src)||empty($db_name)){
            die("缺少构造参数");
        }
        $this->addAserver($sql_src,$sql_user,$db_name,$sql_pass,"host");//设定为主机
    }
    //连接数据库
    private function _connect($pos){
        $conn=mysqli_connect($this->sqlServer[$pos]['src'],$this->sqlServer[$pos]['user'],$this->sqlServer[$pos]['pwd']);        
        if(!$conn){
            die("数据库连接失败!请检查用户名或者密码");
        }else{
            if(!mysqli_select_db($conn,$this->sqlServer[$pos]['db'])){
                die("数据库连接失败!请检查用户名或者密码");
            }else{
                $this->con=$conn;
            }
        }
        mysqli_query($this->con,"SET character_set_client = utf8");//设定字符集编码
    }
    //设定工作模式
    public function setWorkMode($mode){
        $this->workMode=$mode;
        return $this;
    }
    //开启调试模式
    public function debug($f){
        $this->debugMode=$f;
        return $this;
    }
    //设定表名
    public function table($tableName){
        $this->where="";//重置数据
        $this->field="*";
        $this->order="";
        $this->limit="";
        if(!empty($this->tablePrefix))
        $this->tableName=$this->tablePrefix."_".$tableName;
        else $this->tableName=$tableName;
        return $this;
    }
    //设定表前缀
    public function setPrefix($prefix){
        $this->tablePrefix=$prefix;
        return $this;
    }
    //设定查询域
    public function field($param)
    {
        $this->field=$param;
        return $this;
    }
    //替换不安全的字符
    public function safeStr($str){
        
        return str_replace(
        ["'","\\",'"','#'],
        ["''","\\\\",'\\"','\#'],
        $str);
    }
    //设定查询条件['a'=> 1 ,'b' => 2 ,'_logic'=>'and']
    public function where($where){
        $pstr=" where ";
        if(is_array($where)){
            if(empty($where['_complex'])){
                $lineArr=$where;unset($lineArr['_logic']);
                $dNum=0;$aNum=count($lineArr)-1;
                foreach($lineArr as $k => $v){
                    if(is_array($v)){//是数组
                    if($v[0]=='in'){
                        $conditionstr=" $k in (";
                        foreach($v[1] as $ka=>$va){
                            $conditionstr.="$va,";
                        }
                        $conditionstr=substr($conditionstr,0,strlen($conditionstr)-1);
                        $conditionstr.=") ";
                        $pstr.=$conditionstr;
                        continue;
                    }else if($v[0]=='like'){
                        $conditionstr=" $k like '%".$this->safeStr($v[1])."%'";
                        $pstr.=$conditionstr;
                        continue;
                    }
                        $conditionstr=str_replace(['lt','gt','elt','egt','eq'],['>','<','>=','<=','='],$v[0]);
                        $pstr.=$k." $conditionstr "."'".$this->safeStr($v[1])."'";
                        if($dNum<$aNum)if(!empty($where['_logic']))$pstr.=" ".$where['_logic']." ";else $pstr.=" and ";
                        $dNum+=1;    
                    }else{
                        $pstr.=$k." = "."'".$this->safeStr($v)."'";
                        if($dNum<$aNum)if(!empty($where['_logic']))$pstr.=" ".$where['_logic']." ";else $pstr.=" and ";
                        $dNum+=1;    
                    }
                }
            }else{
                $ddNum=0;$aaNum=count($where['_complex'])-1;
                foreach($where['_complex'] as $k => $v){
                    $pstr.=" (";$lineArr=$v;unset($lineArr['_logic']);
                    $dNum=0;$aNum=count($lineArr)-1;
                    foreach($lineArr as $kk => $vv){
                        if(is_array($v)){//是数组
                            $conditionstr=str_replace(['lt','gt','elt','egt','eq'],['>','<','>=','<=','='],$v[0]);
                            $pstr.=$k." $conditionstr "."'".$this->safeStr($v[1])."'";
                            if($dNum<$aNum)if(!empty($where['_logic']))$pstr.=" ".$where['_logic']." ";else $pstr.=" and ";
                            $dNum+=1;    
                        }else{
                            $pstr.=$k." = "."'".$this->safeStr($v)."'";
                            if($dNum<$aNum)if(!empty($where['_logic']))$pstr.=" ".$where['_logic']." ";else $pstr.=" and ";
                            $dNum+=1;    
                        }
                    }
                    if($ddNum<$aaNum)$pstr.=" ) ".$where['_logic'][$ddNum]." ";
                    else $pstr.=" ) ";
                    $ddNum+=1;
                }
            }
            $this->where=$pstr;
        }else{
            $this->where=$pstr.$where;
        }
        return $this;
    }
    //输出出错日志
    public function addLog($sql,$str){
        if($this->error){
            $f=fopen("./error.txt","a+");
            fputs($f,"[".date("m-d H:i:s",time())."][$sql]".$str."\n");
            fclose($f);
        }
    }
    //设定查询返回记录条数
    public function count(){
        $this->finalSql="select count(*) from ".$this->tableName.$this->where;
        if($this->debugMode) return print_r($this->finalSql);
        if($this->workMode){//连接从机
            $this->_connect(1);
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }
            return mysqli_fetch_array($status,MYSQLI_ASSOC)['count(*)'];    
        }
        else{
            $this->_connect(0);
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }
            return mysqli_fetch_array($status,MYSQLI_ASSOC)['count(*)'];    
        }
    }
    //删除某条记录
    public function delete(){
        if(empty($this->where)){
            $this->finalSql="TRUNCATE ".$this->tableName;
        }else{
            $this->finalSql="delete from ".$this->tableName.$this->where;
        }
        if($this->debugMode) return print_r($this->finalSql);
        $this->_connect(0);
        $status=mysqli_query($this->con,$this->finalSql);
        if(!$status){
            $this->addLog($this->finalSql,mysqli_error($this->con));
        return false;
        }else return true;
    }
    //排序
    public function order($str,$ordertype){
        $this->order=" order by $str $ordertype ";
        return $this;
    }
    //限定返回条数
    public function limit($page,$pageNum){
        if(is_numeric($page)||is_numeric($pageNum))
        {
        if($page<0)$page=0;
        $this->limit=" limit $page,$pageNum";
        }
        return $this;
    }
    //返回查询多条记录
    public function select(){
        $return=[];
        $this->finalSql="select ".$this->field." from ".$this->tableName.$this->where.$this->order.$this->limit;
        if($this->debugMode) return print_r($this->finalSql);
        if($this->workMode){//分离模式
            $this->_connect(1);//连接从机
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }
            $return = mysqli_fetch_all($status,MYSQLI_ASSOC);
            return $return;
        }else{//单机模式
            $this->_connect(0);
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }
            $return = mysqli_fetch_all($status,MYSQLI_ASSOC);
            return $return;
        }
    }
    //返回查询一条记录
    public function find(){
        $this->finalSql="select ".$this->field." from ".$this->tableName.$this->where.$this->order.$this->limit;
        if($this->debugMode) return print_r($this->finalSql);
        if($this->workMode){//分离模式
            $this->_connect(1);//连接从机
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }
            return mysqli_fetch_array($status,MYSQLI_ASSOC);
        }else{//单机模式
            $this->_connect(0);
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }
            return mysqli_fetch_array($status,MYSQLI_ASSOC);
        }
    }
    //修改记录
    public function save($data){
        $setStr=" ";
        $dNum=0;$aNum=count($data)-1;
        foreach($data as $k=>$v){
            if(is_array($v)){//是数组
                $setStr.="$k = $k ".$v[0]." ".$this->safeStr($v)." ";
                if($dNum<$aNum)$setStr.=",";
            }else{
                $setStr.="$k = '".$this->safeStr($v)."'";
                if($dNum<$aNum)$setStr.=",";
            }
            $dNum+=1;
        }
        $this->finalSql="update ".$this->tableName." set ".$setStr.$this->where;
        if($this->debugMode) return print_r($this->finalSql);
        if($this->workMode){//分离模式
            $this->_connect(0);//从主机上存数据
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }else return true;
        }else{//单机模式
            $this->_connect(0);
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }else return true;
        }
    }
    //添加一条数据
    public function add($data){
        $addStr="INSERT INTO ".$this->tableName."(";
        $dNum=0;$aNum=count($data)-1;
        foreach($data as $k=>$v){
            $addStr.=" $k,";
        }
        $addStr=substr($addStr,0,strlen($addStr)-1);
        $addStr.=") VALUES(";
        foreach($data as $k=>$v){
            $addStr.="'".$this->safeStr($v)."',";
        }
        $addStr=substr($addStr,0,strlen($addStr)-1);
        $addStr.=")";
        $this->finalSql=$addStr;
        if($this->debugMode) return print_r($this->finalSql);
        if($this->workMode){//分离模式
            $this->_connect(0);//从主机上存数据
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }else return mysqli_insert_id($this->con);
        }else{//单机模式
            $this->_connect(0);
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }else return mysqli_insert_id($this->con);
        }       
    }
    //添加多行数据
    public function addAll($data){
        foreach($data as $k=>$v){
            $flag=$this->add($v);
            if(!$flag)return false;
        }
        return true;
    }
    //数据加1
    public function setInc($column,$num){
        if(!is_numeric($num))$num=0;
        $this->finalSql="update ".$this->tableName." set $column = $column + $num ".$this->where;
        if($this->debugMode) return print_r($this->finalSql);
        if($this->workMode){//分离模式
            $this->_connect(0);//从主机上存数据
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }else return true;
        }else{//单机模式
            $this->_connect(0);
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }else return true;
        }
    }
    //数据减1
    public function setDec($column,$num){
        if(!is_numeric($num))$num=0;
        $this->finalSql="update ".$this->tableName." set $column = $column - $num ".$this->where;
        if($this->debugMode) return print_r($this->finalSql);
        if($this->workMode){//分离模式
            $this->_connect(0);//从主机上存数据
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }else return true;
        }else{//单机模式
            $this->_connect(0);
            $status=mysqli_query($this->con,$this->finalSql);
            if(!$status){
                $this->addLog($this->finalSql,mysqli_error($this->con));
            return false;
            }else return true;
        }
    }
}

?>