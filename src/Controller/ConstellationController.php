<?php
/**
 * @file
 * Contains \Drupal\astrology\Controller\ConstellationController.
 */

namespace Drupal\astrology\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Database\Database;

/**
 * ConstellationController
 */
class ConstellationController extends ControllerBase {

  public function do_req($method) {
      
    switch ($method){
        case  'list':
            $results=$this->listConstellation();
            break;          
        case 'get':
            $results =  $this->getConstellationInfo();
             break;     
        default:
            break;
    }
    
    return new JsonResponse( $results );
  }
  
  public function listConstellation(){
      
      $query = \Drupal::database()->select('node', 'n');
      $query->fields('n', ['nid','uuid']);
      $query->range(0, 1);
      $results = $query->execute()->fetchAssoc();
      
      return   $results;
  }
  
  public function getConstellationInfo(){
      $nid = $_REQUEST['nid'];
      $query = \Drupal::database()->select('node', 'n');
      $query->fields('n', ['nid','uuid']);
      $query->condition('n.nid', $nid);
      $query->range(0, 1);
      $results = $query->execute()->fetchAssoc();
      
      return   $results;
      
  }




 
}
