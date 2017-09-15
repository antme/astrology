<?php
namespace Drupal\astrology\service;

class UserService
{

    public static function loadUserInfo($wxId)
    {
        if ($wxId && $wxId != "") {
            $query = \Drupal::database()->select('users_xingzuo_data', 'n');
            $query->fields('n', array(
                'name',
                'sex',
                'birthDay',
                'live_address',
                'birth_address',
                'wxid'
            ));
            $query->condition('n.wxid', $wxId);
            $query->orderBy("last_update", "DESC");
            $results = $query->execute()->fetchAll();
            
            if (! empty($results) && isset($results[0]->wxid)) {
                return $results[0];
            }
        }
        return new \stdClass();
    }

    public static function getWxId()
    {
        $sessionId = $_SESSION['ast_c_id_session_id'];
        $login_info = UserService::loadLoginInfo();
        
        if (! empty($login_info)) {
            LoggerUtil::log("getWxId", "load wxid from session " . $sessionId . " and open id is : " . $login_info->openId);
            $wxId = $login_info->openId;
        }
        
        if (empty($wxId) && ($_SERVER['HTTP_HOST'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost')) {
            $wxId = uniqid();
            setcookie("ast_c_id", $wxId, time() + 7 * 24 * 3600, "/");
        }
        return $wxId;
    }

    public static function loadWeixinUserInfo($openId)
    {
        $query = \Drupal::database()->select('users_wei_xin', 'n');
        $query->fields(null, array(
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
        LoggerUtil::log1($results);
        return $results;
    }

    public static function login($openId, $sessionid)
    {
        setcookie("ast_c_id_cookie_id", $openId, time() + 7 * 24 * 3600, "/");
        $_SESSION['ast_c_id_session_id'] = $sessionid;
        
        $fields = array(
            'ast_c_id_session_id' => $sessionid,
            'openId' => $openId,
            'expire_time' => time() + 30 * 60
        );
        $exe_results = \Drupal::database()->insert("users_login")
            ->fields($fields)
            ->execute();
    }

    public static function loadLoginInfo()
    {
        $sessionId = $_SESSION['ast_c_id_session_id'];
        if (empty($sessionId)) {
            $sessionId = $_COOKIE['ast_c_id_session_id'];
        }
        
        LoggerUtil::log("loadLoginInfo", " loadLoginInfo from session:" . $sessionId . " or cookie :" . $_COOKIE['ast_c_id_cookie_id']);
        
        if (empty($sessionId)) {
            $sessionId = uniqid();
            setcookie("ast_c_id_cookie_id", $sessionId, time() + 7 * 24 * 3600, "/");
            $_SESSION['ast_c_id_session_id'] = $sessionId;
        }
        $query = \Drupal::database()->select('users_login', 'n');
        $query->condition('n.ast_c_id_session_id', $sessionId);
        $query->condition('n.expire_time', time(), ">");
        $query->orderBy('n.expire_time', 'DESC');
        $query->fields('n', array(
            'openId',
            'ast_c_id_session_id'
        ));
        $result = $query->execute()->fetchAll();
        
        if (! empty($result)) {
            return $result[0];
        }
        return $result;
    }

    public static function countTotaLReport()
    {
        $wxId = UserService::getWxId();
        $query = \Drupal::database()->select('users_xingzuo_data', 'n');
        $query->condition('n.wxid', $wxId);
        return $query->countQuery()
            ->execute()
            ->fetchAll()[0];
    }

    public static function listPersonReport()
    {
        $wxId = UserService::getWxId();
        $query = \Drupal::database()->select('users_xingzuo_data', 'n');
        $query->fields('n', array(
            'name',
            'id'
        ));
        $query->condition('n.wxid', $wxId);
        $query->orderBy("last_update", "DESC");
        return $query->execute()->fetchAll();
    }
    
    public static function loadXingzuoData($user_xz_id)
    {
        $query = \Drupal::database()->select('users_xingzuo_data', 'n');
       
        
        $query->fields('n', array(
            'name',
            'sex',
            'birthDay',
            'live_address',
            'birth_address'
        ));
        
        
        $query->condition('n.id', $user_xz_id);
        return $query->execute()->fetchAssoc();
    }
}

