<?php
namespace Drupal\astrology\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\astrology\service\WeixinService;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectController extends ControllerBase
{

    public function do_req($method)
    {
        $redirect_url = $_COOKIE["ast_redirect_url"];
        
        $code = $_REQUEST['code'];
        
        WeixinService::authorization_code();
        
        if ($redirect_url == "" || !$redirect_url || $redirect_url==null) {
            $redirect_url = "http://test.vlvlife.com/#ast_redirect";
        }
        return new RedirectResponse($redirect_url+"#ast_redirect");
    }
}