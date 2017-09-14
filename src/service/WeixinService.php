<?php
namespace Drupal\astrology\service;

class WeixinService
{

    public static function getAccessToken()
    {
        $appId = ConfigService::getAppId();
        $appsecret = ConfigService::getAppSecret();
        
        $query = \Drupal::database()->select('wx_ticket_token', 'n');
        $query->condition('n.type', "access_token");
        $query->condition('n.expire_time', time(), ">");
        $query->orderBy("expire_time", "DESC");
        $query->fields('n', array(
            'token'
        ));
        
        $results = $query->execute()->fetchAssoc();
        
        if (! empty($results) && ! empty($results["token"])) {
            
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
        $appId = ConfigService::getAppId();
        $appsecret = ConfigService::getAppSecret();
        
        $query = \Drupal::database()->select('wx_ticket_token', 'n');
        $query->condition('n.type', "js_ticket");
        $query->condition('n.expire_time', time(), ">");
        $query->orderBy("expire_time", "DESC");
        $query->fields('n', array(
            'token'
        ));
        
        $results = $query->execute()->fetchAssoc();
        
        if (! empty($results) && ! empty($results["token"])) {
            
            return array(
                "js_ticket" => $results["token"]
            );
        } else {
            $access_token = WeixinService::getAccessToken();
            
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $access_token['token'] . "&type=jsapi";
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
        $appId = ConfigService::getAppId();
        $appsecret = ConfigService::getAppSecret();
        $login_user = UserService::loadLoginInfo();
        LoggerUtil::log1($login_user);
        if (empty($login_user) || empty($login_user['openid'])) {
            
            $code = $_REQUEST['code'];
            
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appId . "&secret=" . $appsecret . "&code=" . $code . "&grant_type=authorization_code";
            $output = WeixinService::req_url($url);
            LoggerUtil::log("authorization_code", $url);
            $openid = $output->openid;
            $access_token = $output->access_token;
            $refresh_token = $output->refresh_token;
            $expires_in = $output->expires_in;
            $scope = $output->scope;
            $user = UserService::loadWeixinUserInfo($openid);
            
            $sessionid = $_SESSION['ast_c_id_session_id'];
            if (empty($sessionid)) {
                
                $sessionid = uniqid();
            }
            
            LoggerUtil::log("authorization_code", $output->errcode . ":" . $output->errmsg);
            if (isset($user) && ! empty($user['openid'])) {
                
                LoggerUtil::log("authorization_code", "found user from openid" . $user['openid']);
                UserService::login($user['openid'], $sessionid);
            } else {
                LoggerUtil::log("authorization_code", "query user from weixin with session id " . $sessionid . " and weixin open id is " . $openid);
                $user_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
                $user_result = WeixinService::req_url($user_url);
                if (! empty($user_result) && isset($user_result->openid)) {
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
                    
                    LoggerUtil::log("authorization_code", "new user from openid" . $user_result->openid);
                    
                    UserService::login($user['openid'], $sessionid);
                } else {
                    LoggerUtil::log("authorization_code", "request user info from weixin failed with openid: " . $openid);
                }
            }
        }
    }

    public static function reqPreOrderId()
    {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $nonce_str = uniqid();
        $order_code = uniqid();
        $key = "b865048d1ae9e14c8a193652e3bf704b";
        
        $weixin_user = UserService::loadWeixinUserInfo(UserService::getWxId());
        
        if (! empty($weixin_user) && ! empty($weixin_user['openid'])) {
            $openid = $weixin_user['openid'];
            $params = array(
                "appid" => ConfigService::getAppId(),
                "body" => "星座帮你选",
                "mch_id" => ConfigService::getmch_id(),
                "nonce_str" => $nonce_str,
                "notify_url" => ConfigService::getJSApiNoticeUrl(),
                "openid" => $openid,
                "out_trade_no" => $order_code,
                "spbill_create_ip" => WeixinService::GetIP(),
                "total_fee" => 1,
                "trade_type" => "JSAPI"
            );
            
            // 这里参数的顺序要按照 key 值 ASCII 码升序排序
            $sign_str = "appid=" . $params['appid'] . "&body=" . $params['body'] . "&mch_id=" . $params['mch_id'] . "&nonce_str=" . $params['nonce_str'] . "&notify_url=" . $params['notify_url'] . "&openid=" . $openid . "&out_trade_no=" . $params['out_trade_no'] . "&spbill_create_ip=" . $params['spbill_create_ip'] . "&total_fee=" . $params['total_fee'] . "&trade_type=" . $params['trade_type'];
            
            $final_sign = $sign_str . "&key=" . $key; // 注：key为商户平台设置的密钥key
            $sign = strtoupper(md5($final_sign)); // 注：MD5签名方式
            
            $params['sign'] = $sign;
            
            $body = WeixinService::arrayToXml($params);
            
            $msg = WeixinService::rawpost($url, $body);
            
            $msgArr = WeixinService::parseMsg($msg);
            
            if ($msgArr && is_object($msgArr) && $msgArr->return_code == 'SUCCESS') {
                
                $order_info = array(
                    "appId"=> ConfigService::getAppId(),
                    "timestamp"=>time(),
                    "nonceStr"=>$nonce_str
                    
                );
                $orderids =  "prepay_id=" . (string)$msgArr->prepay_id;
                
                // 这里参数的顺序要按照 key 值 ASCII 码升序排序
                $order_string = "appId=".ConfigService::getAppId()."&nonceStr=" . $nonce_str . "&package=" . urldecode($orderids) .  "&signType=MD5&timeStamp=" . $order_info['timestamp'];
                
                $signature = strtoupper(md5($order_string));
                $order_info["paySign"] = $signature;
                $order_info["package"] =  $orderids;
                $order_info["order_string"] =  $order_string;
                
                return $order_info;
            }
        }
        return array(
            "prepay_id" => ""
        );
    }

    /**
     * 将数组转换成xml
     * 
     * @param $arr 数组对象
     */
    public static function arrayToXml($array_data)
    {
        $body = '<xml>';
        foreach ($array_data as $k => $v) {
            $body .= "<{$k}><![CDATA[{$v}]]></{$k}>";
        }
        $body .= '</xml>';
        return $body;
    }

    /**
     * 原始原始POST
     * 
     * @param $url 请求的url地址
     * @param $raw 原始数据，可以为字符串或数组
     * @return mixed 返回请求值
     */
    public static function rawpost($url, $raw)
    {
        $resource = curl_init();
        curl_setopt($resource, CURLOPT_POST, true);
        curl_setopt($resource, CURLOPT_URL, $url);
        curl_setopt($resource, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($resource, CURLOPT_HTTPHEADER, array('Expect:'));
        $data = curl_exec($resource);
        curl_close($resource);
        return $data;
    }

    /**
     * 解析接收到的消息
     * 
     * @param string $msg
     *            消息体
     * @return bool|SimpleXMLElement
     */
    public static function parseMsg($msg = '')
    {
        if (! $msg || empty($msg)) {
            return false;
        }
        $msgObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($msgObj === false || ! ($msgObj instanceof \SimpleXMLElement)) {
            return false;
        }
        return $msgObj;
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

    public static function GetIP()
    {
        if (! empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (! empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (! empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        } else {
            $cip = "无法获取！";
        }
        return $cip;
    }
}