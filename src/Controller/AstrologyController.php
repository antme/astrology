<?php
/**
 * @file
 * Contains \Drupal\astrology\Controller\AstrologyController.
 */

namespace Drupal\astrology\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * AstrologyController
 */
class AstrologyController extends ControllerBase {
  /**
   * Generates an example page.
   */
    public function do_req($method) {
        
        switch ($method){
            case  'read_result':
                $results=$this->readXinpanResultData();
                break;
            case 'get':
                $results =  $this->getConstellationInfo();
                break;
            default:
                break;
        }
        
        return new JsonResponse( $results );
    }
    
    public function readXinpanResultData(){
        
        $wxId='test';
        
        $query = \Drupal::database()->select('users_xingpan_data', 'n');
        $query->condition('n.wxid', $wxId);
        $query->fields('n', array('wxid', 'result'));
        $results = $query->execute()->fetchAssoc();
        $results['result'] = json_decode($results['result']);
        
        return $results;
        
    }
}
