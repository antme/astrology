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
        case 'viewquestionresult':
            $results =  $this->viewQuestionResult();
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
     
     $query_str = "select n.entity_id, f.uri, j.field_xingzuojixiong_value from node__field_xingzuo_name as n, node__field_xingzuotubiao as t, file_managed as f, node__field_xingzuojixiong as j where j.entity_id=n.entity_id and n.entity_id = t.entity_id and t.field_xingzuotubiao_target_id = f.fid and n.bundle='shierxingzuo' and n.field_xingzuo_name_value='".$xingzuo."'";
     $xingzuo_results = \Drupal::database()->query($query_str)->fetchObject();
     $xingzuo_results->name = DataUtil::getXingzuoInfo()[$xingzuo];
     
     
     
     $query_str = "select n.entity_id, f.uri, j.field_xingzuojixiong_value from node__field_xingxing as n, node__field_xingzuojixiong as j, node__field_xingxingtubiao as t, file_managed as f  where j.entity_id=n.entity_id and n.entity_id = t.entity_id and t.field_xingxingtubiao_target_id =f.fid  and n.bundle='shidaxingxing' and n.field_xingxing_value='".$xingxin."'";
     $xingxin_results = \Drupal::database()->query($query_str)->fetchObject();  
     
     
     $query_str = "select n.entity_id, f.uri, j.field_xingzuojixiong_value from node__field_gongwei as n, node__field_xingzuojixiong as j, node__field_gongweitubiao as t, file_managed as f  where j.entity_id=n.entity_id and n.entity_id = t.entity_id and t.field_gongweitubiao_target_id =f.fid  and n.bundle='shiergongwei' and n.field_gongwei_value='".$gongwei."'";   
     $gw_results = \Drupal::database()->query($query_str)->fetchObject();    

     
     $xingzuo_results->uri = $this->parserUri($xingzuo_results);
     $xingxin_results->uri = $this->parserUri($xingxin_results);
     $gw_results->uri = $this->parserUri($gw_results);
     
     
     $data->xingzuo = $xingzuo_results;
     $data->xingxing = $xingxin_results;
     $data->gw = $gw_results;
     return $data;
      
  }

  
  public function parserUri($data){
        return "/sites/default/files/" . str_replace("public://","",$data->uri);
  }
  
 

 
}
