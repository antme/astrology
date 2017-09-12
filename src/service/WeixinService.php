<?php
namespace Drupal\astrology\service;

class WeixinService
{

    public static function getAccessToken()
    {
        $appId = "wx5dd7a0373f62385b";
        $appsecret = "35ed9225570fd3c1f130d3501c496fc2";
        
        $query = \Drupal::database()->select('wx_ticket_token', 'n');
        $query->condition('n.type', "access_token");
        $query->condition('n.expire_time', time(), ">");
        $query->orderBy("expire_time", "DESC");
        $query->fields('n', array(
            'token'
        ));
        
        $results = $query->execute()->fetchAssoc();
        
        if ($results && $results["token"]) {
            
            return array(
                "token" => $results["token"]
            );
        } else {
            
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appId . "&secret=" . $appsecret;
            $output = WeixinService::req_url($url);
            
            $fields = array(
                'token' => $output->access_token,
                'type' => "access_token",
                'expire_time' => time() + $output->expires_in
            );
            $exe_results = \Drupal::database()->insert("wx_ticket_token")
                ->fields($fields)
                ->execute();
            return array(
                "token" => $output->access_token
            );
        }
    }

    public static function getJsTicket()
    {
        $appId = "wx5dd7a0373f62385b";
        $appsecret = "35ed9225570fd3c1f130d3501c496fc2";
        
        $query = \Drupal::database()->select('wx_ticket_token', 'n');
        $query->condition('n.type', "js_ticket");
        $query->condition('n.expire_time', time(), ">");
        $query->orderBy("expire_time", "DESC");
        $query->fields('n', array(
            'token'
        ));
        
        $results = $query->execute()->fetchAssoc();
        
        if ($results && $results["token"]) {
            
            return array(
                "js_ticket" => $results["token"]
            );
        } else {
            $access_token = WeixinService::getAccessToken();
            
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $access_token['token'] . "&type=wx_card";
            $output = WeixinService::req_url($url);
            
            $fields = array(
                'token' => $output->ticket,
                'type' => "js_ticket",
                'expire_time' => time() + $output->expires_in
            );
            $exe_results = \Drupal::database()->insert("wx_ticket_token")
                ->fields($fields)
                ->execute();
            return array(
                "js_ticket" => $output->ticket
            );
            // return $output;
        }
    }

    public static function authorization_code()
    {
        $appId = "wx5dd7a0373f62385b";
        $appsecret = "35ed9225570fd3c1f130d3501c496fc2";
        
        $code = $_REQUEST['code'];
        $openid = UserService::getWxId();
        
        if ($openid == "") {
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appId . "&secret=" . $appsecret . "&code=" . $code . "&grant_type=authorization_code";
            $output = WeixinService::req_url($url);
            $openid = $output->openid;
            $access_token = $output->access_token;
            $refresh_token = $output->refresh_token;
            $expires_in = $output->expires_in;
            $scope = $output->scope;
            $user = UserService::loadWeixinUserInfo($openid);
            
            if ($user['openid']) {} else {
                
                $user_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
                $user_result = WeixinService::req_url($user_url);
                $fields = array(
                    'openid' => $user_result->openid,
                    'nickname' => $user_result->nickname,
                    'sex' => $user_result->sex,
                    'province' => $user_result->province,
                    'city' => $user_result->city,
                    'country' => $user_result->country,
                    'headimgurl' => $user_result->headimgurl
                );
                
                \Drupal::database()->insert("users_wei_xin")
                    ->fields($fields)
                    ->execute();
                setcookie("ast_c_id", $user_result->openid, time() + 7 * 24 * 3600, "/");
            }
        }
    }

    public static function req_url($url)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output);
        return $output;
    }
}