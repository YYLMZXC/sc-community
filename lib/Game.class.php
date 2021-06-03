<?php
class Game{
    //读取存档模式
    public static function readGameMode($file){
        $zip=zip_open($file);
        while($file=zip_read($zip)){
            if(zip_entry_name($file)!="Project.xml")continue;
            $entity=zip_entry_read($file,zip_entry_filesize($file));
            $xml=new SimpleXMLElement($entity);
            $a=$xml->children();
            foreach ($a->{"Subsystems"}->children() as $k){
                if($k['Name']=="GameInfo"){
                    foreach($k->children() as $kk){
                        if($kk['Name']=="GameMode"){
                            return ($kk['Value']);
                        }
                    }
                }
            }
        }
        return "None";
    }    
    //模式转换
    public static function toGameMode($variable){
        switch ($variable) {
            case 'Harmless':
                return "无害";
                break;
            case 'Creative':
                return "创造";
                break;
            case 'Adventure':
                return "冒险";
                break;
            case 'Cruel':
                return "残酷";
                break;
            case 'Challenging':
                return "挑战";
                break;
            default:
                return "未知";
                break;
        }
        
    }    
    
}