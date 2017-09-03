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




 
}
