<?php
namespace Drupal\astrology\service;

class UserService
{
    
    
    public static function loadUserInfo($wxId){
        
        $query = \Drupal::database()->select('users_xingzuo_data', 'n');
        $query->fields(null,  array(
            'name',
            'sex',
            'birthDay',
            'live_address',
            'birth_address',
            'wxid'
        ));
        $query->condition('n.wxid', $wxId);
        $results = $query->execute()->fetchAssoc();
        return $results;
    }
    
    public static function getWxId(){
        $wxId=$_COOKIE["_wx_id_tst"];
        if($wxId==""){
            $wxId = uniqid();
            
            setcookie("_wx_id_tst",$wxId,time()+7*24*3600,"/");
        }
        
        return $wxId;
    }
}

