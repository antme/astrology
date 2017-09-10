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
    
    $res =  new JsonResponse( $results );
    $res->setCallback($_REQUEST['callback']);
    return $res;
  }
  
  public function listConstellation(){
      
      $query_str = "select n.entity_id from node__field_xingzuo_name as n where n.field_xingzuo_name_value='" + $xingzuo_name  + "';";
      $results = \Drupal::database()->query($query_str)->fetchAll();
      
      return   $results;
  }
  
  public function getConstellationInfo(){
      $xingzuo_name = $_REQUEST['xingzuo_name'];
      $query_str = "select n.entity_id from node__field_xingzuo_name as n where n.field_xingzuo_name_value='".$xingzuo_name."';";
      $results = \Drupal::database()->query($query_str)->fetchAll();
      foreach($results as $key=>$value) {
          
          $tags_Query = "select field_xingzuobiaoqian_value as tags from node__field_xingzuobiaoqian where entity_id='" . $value->entity_id ."'";
          $tags_results = \Drupal::database()->query($tags_Query)->fetchAll();
          $results[$key]->tags =  $tags_results;
          
          $story_Query = "select field_xingzuogushi_value as stories from node__field_xingzuogushi where entity_id='" . $value->entity_id ."'";
          $story_results = \Drupal::database()->query($story_Query)->fetchAll();
          $results[$key]->stories =  $story_results;
          
          $stars_Query = "select field_xingzuomingxing_value as stars from node__field_xingzuomingxing where entity_id='" . $value->entity_id ."'";
          $stars_results = \Drupal::database()->query($stars_Query)->fetchAll();
          $results[$key]->stars =  $stars_results;
          
          
    }
   
      return   $results;
  }


  


 
}
