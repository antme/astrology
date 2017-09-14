<?php
namespace Drupal\astrology\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\astrology\service\WeixinService;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderController extends ControllerBase
{

    public function do_req($method)
    {
        $results = array();
        switch ($method) {
            case 'reqPreOrderId':
                
                $results = WeixinService::reqPreOrderId();
                
                break;
            
            default:
                break;
        }
        
        $res = new JsonResponse($results);
        $res->setCallback($_REQUEST['callback']);
        return $res;
    }
}