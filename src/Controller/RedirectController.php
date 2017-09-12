<?php

namespace Drupal\astrology\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;


class RedirectController extends ControllerBase {
    
    
    public function do_req($method) {
        
        $redirect_url = $_REQUEST['ast_redirect_url'];
        $code = $_REQUEST['code'];
    
        
        return new RedirectResponse($redirect_url);
        
    }
    
    
}