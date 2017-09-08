<?php
/**
 * @file
 * Contains \Drupal\astrology\Controller\QuestionController.
 */

namespace Drupal\astrology\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Database\Database;
use Drupal\astrology\Data\DataUtil;
use Drupal\node\Entity\Node;

/**
 * QuestionController
 */
class QuestionController extends ControllerBase {

  public function do_req($method) {
      
    switch ($method){
        case  'list':
            $results=$this->listQuestions();
            break;          
        case 'get':
            $results =  $this->getQuestion();
             break;    
        case 'zhanxing':
            $results =  $this->randomZhanxin();
            break;   
        default:
            break;
    }
    
    return new JsonResponse( $results );
  }
  
  public function listQuestions(){
      
      $query_str = "select n.uuid, d.title from node as n inner join node_field_data as d on d.nid=n.nid where n.type='jiujiedewenti' and d.type='jiujiedewenti';";
      $results = \Drupal::database()->query($query_str)->fetchAll();

      
      return   $results;
  }
  
  public function getQuestion(){
      $nid = $_REQUEST['nid'];
      $query = \Drupal::database()->select('node', 'n');
      $query->fields('n', ['nid','uuid']);
      $query->condition('n.nid', $nid);
      $query->range(0, 1);
      $results = $query->execute()->fetchAssoc();
      
      return   $results;
      
  }

  
  public function randomZhanxin(){
     $xingzuo = DataUtil::getRandomXingzuo();
     $xingxin = DataUtil::getRandomXingxin();
     $gongwei = DataUtil::getRandomGongwei();     
     
     $query_str = "select n.entity_id from node__field_xingzuo_name as n where n.bundle='shierxingzuo' and n.field_xingzuo_name_value='".$xingzuo."';";
     $xingzuo_results = \Drupal::database()->query($query_str)->fetchObject();
     $jixong_Query = "select field_xingzuojixiong_value as jixong from node__field_xingzuojixiong where entity_id='" . $results->entity_id ."'";
     $jx_results = \Drupal::database()->query($jixong_Query)->fetchObject();
     $xingzuo_results->jixong =  $jx_results->jixong;
     
     $query_str = "select n.entity_id from node__field_xingxing as n where n.bundle='shidaxingxing' and n.field_xingxing_value='".$xingxin."';";
     $xingxin_results = \Drupal::database()->query($query_str)->fetchObject();  
     $jixong_Query = "select field_xingzuojixiong_value as jixong from node__field_xingzuojixiong where entity_id='" . $xingxin_results->entity_id ."'";
     $jx_results = \Drupal::database()->query($jixong_Query)->fetchObject();
     $xingxin_results->jixong =  $jx_results->jixong;
     
     
     $query_str = "select n.entity_id from node__field_gongwei as n where n.bundle='shiergongwei' and n.field_gongwei_value='".$gongwei."';";
     $gw_results = \Drupal::database()->query($query_str)->fetchObject();    
     $jixong_Query = "select field_xingzuojixiong_value as jixong from node__field_xingzuojixiong where entity_id='" . $gw_results->entity_id ."'";
     $jx_results = \Drupal::database()->query($jixong_Query)->fetchObject();
     $gw_results->jixong =  $jx_results->jixong;

    // return $gw_results;
     return Node::load($gw_results->entity_id);
     
     //return $gw_results;
      
  }


 
}
