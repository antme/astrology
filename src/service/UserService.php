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
}

