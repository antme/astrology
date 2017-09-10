<?php
namespace Drupal\astrology\Data;

class DataUtil
{




    public static function getXingzuoByDate($dateStr)
    {
        $dateArray = explode(' ', $dateStr);
        $dateArray1 = explode('-', $dateArray[0]);
        $month = (int) $dateArray1[1];
        $day = (int) $dateArray1[2];
        $xingzhuo = "";
        if ($month == 1) {
            if ($day >= 20) {
                $xingzhuo = "shuiping";
            } else {
                $xingzhuo = "mojie";
            }
        } else if ($month == 2) {
            if ($day < 18) {
                $xingzhuo = "shuiping";
            } else {
                $xingzhuo = "shuangyu";
            }
        } else if ($month == 3) {
            if ($day < 20) {
                $xingzhuo = "shuangyu";
            } else {
                $xingzhuo = "baiyang";
            }
        } else if ($month == 4) {
            if ($day < 21) {
                $xingzhuo = "baiyang";
            } else {
                $xingzhuo = "jinniu";
            }
        } else if ($month == 5) {
            if ($day < 21) {
                $xingzhuo = "jinniu";
            } else {
                $xingzhuo = "shuangzi";
            }
        } else if ($month == 6) {
            if ($day < 22) {
                $xingzhuo = "shuangzi";
            } else {
                $xingzhuo = "juxie";
            }
        } else if ($month == 7) {
            if ($day < 23) {
                $xingzhuo = "juxie";
            } else {
                $xingzhuo = "shizi";
            }
        } else if ($month == 8) {
            if ($day < 23) {
                $xingzhuo = "shizi";
            } else {
                $xingzhuo = "chunv";
            }
        } else if ($month == 9) {
            if ($day < 23) {
                $xingzhuo = "chunv";
            } else {
                $xingzhuo = "tianping";
            }
        } else if ($month == 10) {
            if ($day < 24) {
                $xingzhuo = "tianping";
            } else {
                $xingzhuo = "tianxie";
            }
        } else if ($month == 11) {
            if ($day < 23) {
                $xingzhuo = "tianxie";
            } else {
                $xingzhuo = "sheshou";
            }
        } else if ($month == 12) {
            if ($day < 22) {
                $xingzhuo = "sheshou";
            } else {
                $xingzhuo = "mojie";
            }
        }
        
        return array(
            "xingzuo" => $xingzhuo
        );
    }

    public static function getXingzuoCNName($enName)
    {
        
        $XINGZUO_ARRAY = DataUtil::getXingzuoInfo();
        return $XINGZUO_ARRAY[$enName];
        
      
    }
    
    
    public static function getGongweiCNName($enName)
    {
        
        $GONGWEI_ARRAY = DataUtil::getGongweiInfo();
        return $GONGWEI_ARRAY[$enName];
        
        
    }
    
    public static function getXingxinCNName($enName)
    {
        
        $XINGXIN_ARRAY = DataUtil::getXingxinInfo();
        return $XINGXIN_ARRAY[$enName];
        
        
    }
    
    
    public static function getXingzuoInfo(){
        
         $XINGZUO_ARRAY = array(
            "baiyang" => "白羊",
            "jinniu" => "金牛",
            "shuangzi" => "双子",
            "juxie" => "巨蟹",
            "shizi" => "狮子",
            "chunv" => "处女",
            "tianping" => "天枰",
            "tianxie" => "天蝎",
            "sheshou" => "射手",
            "mojie" => "摩羯",
            "shuiping" => "水瓶",
            "shuangyu" => "双鱼"
        );
         
         return $XINGZUO_ARRAY;
    }
    
    public static function getGongweiInfo(){
        
        $GONGWEI_ARRAY = array(
            "diyigong" => "第1宫",
            "diergong" => "第2宫",
            "disangong" => "第3宫",
            "disigong" => "第4宫",
            "diwugong" => "第5宫",
            "diliugong" => "第6宫",
            "diqigong" => "第7宫",
            "dibagong" => "第8宫",
            "dijiugong" => "第8宫",
            "dishigong" => "第10宫",
            "dishiyigong" => "第11宫",
            "dishiergong" => "第12宫"
        );
        
        return $GONGWEI_ARRAY;
    }
    
    
    public static function getXingxinInfo(){
        
        $XINGXIN_ARRAY = array(
            "taiyang" => "太阳",
            "yueliang" => "月亮",
            "shuixing" => "水星",
            "jinxing" => "金星",
            "huoxing" => "火星",
            "muxing" => "木星",
            "tuxing" => "土星",
            "tianwangxing" => "天王星",
            "haiwangxing" => "海王星",
            "mingwangxing" => "冥王星"
        );
        
        return $XINGXIN_ARRAY;
    }
    
    
    public static function getNengLiangDingWeiName($enName){
        if($enName=="huozu"){
            return "火族";   
        }else if($enName=="tuzu"){
            return "土族";
        }else if($enName=="fengzu"){
            return "风族";
        }else if($enName=="shuizu"){
            return "水族";
        }

    }
    
    
    public static function getNengLiangXingzhiName($enName){
        if($enName=="yangxing"){
            return "阳性";
        }else if($enName=="yinxing"){
            return "阴性";
        }
    }
    
    
    public static function getNengLiangFangXiang($enName){
        if($enName=="insidetooutside"){
            return "由内向外";
        }else if($enName=="outsidetoinside"){
            return "由内向外";
        }
    }
    
    
    public static function getRandomXingzuo(){
        $XINGZUO_ARRAY = DataUtil::getXingzuoInfo();
        $rand_keys = array_rand($XINGZUO_ARRAY, 1);
        return $rand_keys;
    }
    
    public static function getRandomGongwei(){
        $GONGWEI_ARRAY = DataUtil::getGongweiInfo();
        $rand_keys = array_rand($GONGWEI_ARRAY, 1);
        return $rand_keys;
    }
    public static function getRandomXingxin(){
        $XINGXIN_ARRAY = DataUtil::getXingxinInfo();
        $rand_keys = array_rand($XINGXIN_ARRAY, 1);
        return $rand_keys;
    }
    
    
}