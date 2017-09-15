<?php
namespace Drupal\astrology\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\astrology\service\WeixinService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\astrology\service\LoggerUtil;
use Drupal\astrology\service\ConfigService;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\astrology\service\UserService;
use Symfony\Component\HttpFoundation\Response;

class RedirectController extends ControllerBase
{
    public function do_req($method)
    {
        session_start();
        $results = array();
        switch ($method){
            case  'wx':
                $redirect_url = $_REQUEST["ast_redirect"];                
                WeixinService::authorization_code();
                
                if ($redirect_url == "" || !$redirect_url || $redirect_url==null) {
                    $redirect_url = "http://test.vlvlife.com/astrology_mobile/index.html";
                }
                LoggerUtil::log("do_req:" . $method, $redirect_url);
           
               
                break;
            case 'js_sdk':
                $results = $this->loadJSSdkConfig();
                break;
            default:
                break;
        }
        
        
        
        if($method == "login"){
            $login_info = UserService::loadLoginInfo();
            
            
            if((empty($login_info) || empty($login_info->openId)) &&  !strstr($_SERVER['HTTP_REFERER'], "ast_redirect")){
                LoggerUtil::log("login", " need login for sessiong ====>" .  $_SESSION['ast_c_id_session_id'] . " and referer is " . $_SERVER['HTTP_REFERER']);
                $headers = array("Content-Type"=>"application/javascript");
                return new Response("login('". ConfigService::getAppId() ."');", "200", $headers);
            }else{
                $headers = array("Content-Type"=>"application/javascript");
                return new Response("", "200", $headers);
            }
            
        }else if($method == "wx"){
            
            return new RedirectResponse($redirect_url);
        }else{
        
            $res =  new JsonResponse( $results );
            $res->setCallback($_REQUEST['callback']);
            return $res;
        }
    
    }
    
    public function loadJSSdkConfig(){
        $tickets = WeixinService::getJsTicket();
        $url =  $_REQUEST["url"];
        
        $sdk_params = array(
            "appId"=> ConfigService::getAppId(),
            "timestamp"=>time(),
            "nonceStr"=>uniqid()
            
        );

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=".$tickets['js_ticket']."&noncestr=" . $sdk_params['nonceStr'] . "&timestamp=" . $sdk_params['timestamp'] . "&url=" . urldecode($url);
        
        $signature = sha1($string);
        $sdk_params["signature"] = $signature;
        $sdk_params["raw_str"] = $string;
        $sdk_params["js_ticket"] = $tickets['js_ticket'];
        return $sdk_params;
     
    }
}