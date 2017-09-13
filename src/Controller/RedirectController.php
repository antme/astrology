<?php
namespace Drupal\astrology\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\astrology\service\WeixinService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\astrology\service\LoggerUtil;

class RedirectController extends ControllerBase
{
    public function do_req($method)
    {
        session_start();
        
        $results = array();
        switch ($method){
            case  'wx':
                $redirect_url = $_COOKIE["ast_redirect_url"];                
                $code = $_REQUEST['code'];
                WeixinService::authorization_code();
                
                if ($redirect_url == "" || !$redirect_url || $redirect_url==null) {
                    $redirect_url = "http://test.vlvlife.com/astrology_mobile/index.html#ast_redirect";
                }
                LoggerUtil::log("do_req:" . $method, $redirect_url."#ast_redirect");
                return new RedirectResponse($redirect_url."#ast_redirect");
                break;
            case 'js_sdk':
                $results = $this->loadJSSdkConfig();
                break;
            default:
                break;
        }
        
        $res =  new JsonResponse( $results );
        $res->setCallback($_REQUEST['callback']);
        return $res;
    
    }
    
    public function loadJSSdkConfig(){
        $tickets = WeixinService::getJsTicket();
        $url =  $_REQUEST["url"];
        
        $sdk_params = array(
            "appId"=>"wx5dd7a0373f62385b",
            "timestamp"=>time(),
            "nonceStr"=>uniqid()
            
        );

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=".$tickets['js_ticket']."&noncestr=" . $sdk_params['nonceStr'] . "&timestamp=" . $sdk_params['timestamp'] . "&url=" . $url;
        
        $signature = sha1($string);
        $sdk_params["signature"] = $signature;
        $sdk_params["raw_str"] = $string;
        $sdk_params["js_ticket"] = $tickets['js_ticket'];
        return $sdk_params;
     
    }
}