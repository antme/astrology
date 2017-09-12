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
      
        if(!empty($results) && isset($results['wxid'])){
            return $results();
        }
        return array();
    }
    
    public static function getWxId(){
        $wxId = $_COOKIE["ast_c_id"];
        if($wxId=="" && ($_SERVER['HTTP_HOST']=='127.0.0.1' || $_SERVER['HTTP_HOST']=='localhost')){
            $wxId = uniqid();           
            setcookie("ast_c_id",$wxId,time()+7*24*3600,"/");
        }
        return $wxId;
    }
    
    
    public static function loadWeixinUserInfo($openId){        
        $query = \Drupal::database()->select('users_wei_xin', 'n');
        $query->fields(null,  array(
            'openid',
            'nickname',
            'sex',
            'province',
            'city',
            'country',
            'headimgurl'
        ));
        $query->condition('n.openid', $openId);
        $results = $query->execute()->fetchAssoc();
        return $results;
    }
}

