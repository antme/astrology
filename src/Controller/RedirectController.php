<?php
namespace Drupal\astrology\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\astrology\service\WeixinService;

class RedirectController extends ControllerBase
{

    public function do_req($method)
    {
        $redirect_url = $_COOKIE["ast_redirect_url"];
        
        $code = $_REQUEST['code'];
        
        WeixinService::authorization_code();
        
        if ($redirect_url == "") {
            $redirect_url = "http://test.vlvlife.com";
        }
        return new RedirectResponse($redirect_url);
    }
}