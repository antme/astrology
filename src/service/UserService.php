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
        $wxId = $_COOKIE["ast_c_id"];
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

