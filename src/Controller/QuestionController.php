<?php
/**
 * @file
 * Contains \Drupal\astrology\Controller\QuestionController.
 */

namespace Drupal\astrology\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\astrology\Data\DataUtil;
use Drupal\astrology\service\UserService;
use Drupal\astrology\service\WeixinService;
use Symfony\Component\HttpFoundation\JsonResponse;


use Drupal\astrology\service\WeixinService
/**
 * QuestionController
 */
class QuestionController extends ControllerBase {

  public function do_req($method) {
      session_start();
     
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
        case 'listHistoryQuestionDate':
            $results =  $this->listHistoryQuestionDate();
            break;   
        case 'listHistoryQuestionName':
            $results =  $this->listHistoryQuestionName();
            break;   
        default:
            break;
    }
    
    $res =  new JsonResponse( $results );
    $res->setCallback($_REQUEST['callback']);
    return $res;
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
     $wxId = UserService::getWxId();
     $question_name = $_REQUEST['question_name'];
     
     $query_str = "select n.entity_id, f.uri, j.field_xingzuojixiong_value from node__field_xingzuo_name as n, node__field_xingzuotubiao as t, file_managed as f, node__field_xingzuojixiong as j where j.entity_id=n.entity_id and n.entity_id = t.entity_id and t.field_xingzuotubiao_target_id = f.fid and n.bundle='shierxingzuo' and n.field_xingzuo_name_value='".$xingzuo."'";
     $xingzuo_results = \Drupal::database()->query($query_str)->fetchObject();
  
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
     
     
     
     $fields = array(
         'wxid' => $wxId,
         'question_name' => $question_name,
         'zx_date' => date("Y-m-d"),
         'result' => json_encode($data)
     );
     
     \Drupal::database()->insert("users_zhanxing_history")->fields($fields)->execute();
     
     return $data;
  }
  
  public function viewQuestionResult(){
      $xingzuo_id=$_REQUEST['xingzuo_id'];
      $xingxin_id=$_REQUEST['xingxin_id'];
      $gongwei_id=$_REQUEST['gongwei_id'];
      
      $query_str = "select n.entity_id,n.field_xingzuo_name_value, f.uri, j.field_xingzuojixiong_value from node__field_xingzuo_name as n, node__field_xingzuotubiao as t, file_managed as f, node__field_xingzuojixiong as j where j.entity_id=n.entity_id and n.entity_id = t.entity_id and t.field_xingzuotubiao_target_id = f.fid and n.bundle='shierxingzuo' and n.entity_id='".$xingzuo_id."'";
      $xingzuo_results = \Drupal::database()->query($query_str)->fetchObject();
      
      $query_str = "select n.entity_id, n.field_xingxing_value, f.uri, j.field_xingzuojixiong_value from node__field_xingxing as n, node__field_xingzuojixiong as j, node__field_xingxingtubiao as t, file_managed as f  where j.entity_id=n.entity_id and n.entity_id = t.entity_id and t.field_xingxingtubiao_target_id =f.fid  and n.bundle='shidaxingxing' and n.entity_id='".$xingxin_id."'";
      $xingxin_results = \Drupal::database()->query($query_str)->fetchObject();
      
      $query_str = "select n.entity_id, n.field_gongwei_value, f.uri, j.field_xingzuojixiong_value from node__field_gongwei as n, node__field_xingzuojixiong as j, node__field_gongweitubiao as t, file_managed as f  where j.entity_id=n.entity_id and n.entity_id = t.entity_id and t.field_gongweitubiao_target_id =f.fid  and n.bundle='shiergongwei' and n.entity_id='".$gongwei_id."'";
      $gw_results = \Drupal::database()->query($query_str)->fetchObject();
      
      $xingzuo_results->uri = $this->parserUri($xingzuo_results);
      $xingzuo_results->name = DataUtil::getXingzuoCNName($xingzuo_results->field_xingzuo_name_value);
      
      $xingxin_results->uri = $this->parserUri($xingxin_results);
      $xingxin_results->name = DataUtil::getXingxinCNName($xingxin_results->field_xingxing_value);
      
      $gw_results->uri = $this->parserUri($gw_results);
      $gw_results->name = DataUtil::getGongweiCNName($gw_results->field_gongwei_value);
      
  
      $good = 0;
      $bad = 0;
      $normal = 0;
      if($xingzuo_results->field_xingzuojixiong_value == "good"){
          $good = $good +1;
      }else if($xingzuo_results->field_xingzuojixiong_value == "bad"){
          $bad = $bad +1;
      }else if($xingzuo_results->field_xingzuojixiong_value == "normal"){
          $normal = $normal + 1;
      }
      
      if($xingxin_results->field_xingzuojixiong_value == "good"){
          $good = $good +1;
      }else if($xingxin_results->field_xingzuojixiong_value == "bad"){
          $bad = $bad +1;
      }else if($xingxin_results->field_xingzuojixiong_value == "normal"){
          $normal = $normal +1;
      }
 
      if($gw_results->field_xingzuojixiong_value == "good"){
          $good = $good +1;
      }else if($gw_results->field_xingzuojixiong_value == "bad"){
          $bad = $bad +1;
      }else if($gw_results->field_xingzuojixiong_value == "normal"){
          $normal = $normal +1;
      }
      
      $question_id=$_REQUEST['questionId']; 
      
      $results = array(
          "good"=>  $good,
          "bad"=> $bad,
          "normal"=>$normal
      );
 
      
      $good_result = true;
      
      if($bad>=2){
          $good_result = false;
      }else if($bad==1){
          if($normal==2){
              $good_result = false;
          }
      }
      
      if($good_result){
          
      }else{
          
      }
      
      $luozai_xingzuo_table = "";
      $luozai_xingzuo_field = "";
      if($xingzuo_results->field_xingzuo_name_value == "baiyang"){
          
          $luozai_xingzuo_table="node__field_xingxingluozaibaiyangzuo";
          $luozai_xingzuo_field="field_xingxingluozaibaiyangzuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "jinniu"){
          $luozai_xingzuo_table="node__field_xingxingluozaijinniuzuo";
          $luozai_xingzuo_field="field_xingxingluozaijinniuzuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "shuangzi"){
          $luozai_xingzuo_table="node__field_xingxingluozaishuangzizuo";
          $luozai_xingzuo_field="field_xingxingluozaishuangzizuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "juxie"){
          $luozai_xingzuo_table="node__field_xingxingluozaijuxiezuo";
          $luozai_xingzuo_field="field_xingxingluozaijuxiezuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "shizi"){
          $luozai_xingzuo_table="node__field_xingxingluozaishizizuo";
          $luozai_xingzuo_field="field_xingxingluozaijuxiezuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "chunv"){
          $luozai_xingzuo_table="node__field_xingxingluozaichunuzuo";
          $luozai_xingzuo_field="field_xingxingluozaichunuzuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "tianping"){
          $luozai_xingzuo_table="node__field_xingxingluozaitianchengzuo";
          $luozai_xingzuo_field="field_xingxingluozaitianchengzuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "tianxie"){
          $luozai_xingzuo_table="node__field_xingxingluozaitianxiezuo";
          $luozai_xingzuo_field="field_xingxingluozaitianxiezuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "sheshou"){
          $luozai_xingzuo_table="node__field_xingxingluozaisheshouzuo";
          $luozai_xingzuo_field="field_xingxingluozaisheshouzuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "mojie"){
          $luozai_xingzuo_table="node__field_xingxingluozaimojiezuo";
          $luozai_xingzuo_field="field_xingxingluozaimojiezuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "shuiping"){
          $luozai_xingzuo_table="node__field_xingxingluozaishuipingzuo";
          $luozai_xingzuo_field="field_xingxingluozaishuipingzuo_value";
          
      }else if($xingzuo_results->field_xingzuo_name_value == "shuangyu"){
          $luozai_xingzuo_table="node__field_xingxingluozaishuangyuzuo";
          $luozai_xingzuo_field="field_xingxingluozaishuangyuzuo_value";
          
      }
      
      
      $luozai_gong_table = "";
      $luozai_gong_field = "";
      if($gw_results->field_gongwei_value == "diyigong"){
          $luozai_gong_table = "node__field_xingxingluozaidiyigong";
          $luozai_gong_field = "field_xingxingluozaidiyigong_value";
      }else  if($gw_results->field_gongwei_value == "diergong"){
          $luozai_gong_table = "node__field_xingxingluozaidiergong";
          $luozai_gong_field = "field_xingxingluozaidiergong_value";
      }else  if($gw_results->field_gongwei_value == "disangong"){
          $luozai_gong_table = "node__field_xingxingluozaidisangong";
          $luozai_gong_field = "field_xingxingluozaidisangong_value";
      }else  if($gw_results->field_gongwei_value == "disigong"){
          $luozai_gong_table = "node__field_xingxingluozaidisigong";
          $luozai_gong_field = "field_xingxingluozaidisigong_value";
      }else  if($gw_results->field_gongwei_value == "diwugong"){
          $luozai_gong_table = "node__field_xingxingluozaidiwugong";
          $luozai_gong_field = "field_xingxingluozaidiwugong_value";
      }else  if($gw_results->field_gongwei_value == "diliugong"){
          $luozai_gong_table = "node__field_xingxingluozaidiliugong";
          $luozai_gong_field = "field_xingxingluozaidiliugong_value";
      }else  if($gw_results->field_gongwei_value == "diqigong"){
          $luozai_gong_table = "node__field_xingxingluozaidiqigong";
          $luozai_gong_field = "field_xingxingluozaidiqigong_value";
      }else  if($gw_results->field_gongwei_value == "dibagong"){
          $luozai_gong_table = "node__field_xingxingluozaidibagong";
          $luozai_gong_field = "field_xingxingluozaidibagong_value";
      }else  if($gw_results->field_gongwei_value == "dijiugong"){
          $luozai_gong_table = "node__field_xingxingluozaidijiugong";
          $luozai_gong_field = "field_xingxingluozaidijiugong_value";
      }else  if($gw_results->field_gongwei_value == "dishigong"){
          $luozai_gong_table = "node__field_xingxingluozaidishigong";
          $luozai_gong_field = "field_xingxingluozaidishigong_value";
      }else  if($gw_results->field_gongwei_value == "dishiyigong"){
          $luozai_gong_table = "node__field_xingxingluozaidishiyigong";
          $luozai_gong_field = "field_xingxingluozaidishiyigong_value";
      }else  if($gw_results->field_gongwei_value == "dishiergong"){
          $luozai_gong_table = "node__field_xingxingluozaidishiergong";
          $luozai_gong_field = "field_xingxingluozaidishiergong_value";
      }
          
          
      
      $xing_luo_xingzuo_sql = "select l." . $luozai_xingzuo_field . " from  " . $luozai_xingzuo_table . " as l, node__field_xingxing as x where x.entity_id=l.entity_id  and x.bundle='zhanxingtouzi_xingxingluozaixing' and x.field_xingxing_value='" . $xingxin_results->field_xingxing_value ."'";
      $xing_luo_xingzuo_result = \Drupal::database()->query($xing_luo_xingzuo_sql)->fetchObject();
      $xingzuo_results->descrption = $xing_luo_xingzuo_result->$luozai_xingzuo_field;
      
      
      $xing_luo_gong_sql = "select l." . $luozai_gong_field . " from  " . $luozai_gong_table . " as l, node__field_xingxing as x where x.entity_id=l.entity_id  and x.bundle='zhanxingtouzi_xingxingluozaigong' and x.field_xingxing_value='" . $xingxin_results->field_xingxing_value ."'";
      $xing_luo_gong_result = \Drupal::database()->query($xing_luo_gong_sql)->fetchObject();
      $gw_results->descrption = $xing_luo_gong_result->$luozai_gong_field;
      
      
      $data->xingzuo = $xingzuo_results;
      $data->xingxing = $xingxin_results;
      $data->gw = $gw_results;
      
      return $data;
      
    
  }
  
  public function listHistoryQuestionDate(){
      $wxId = UserService::getWxId();
      $fields = array(
          'zx_date' 
      );
      $query = \Drupal::database()->select('users_zhanxing_history', 'n');
      $query->distinct('zx_date');
      $query->orderBy('zx_date', 'DESC');
      $query->condition('n.wxid', $wxId);
      $query->fields('n', $fields);
      
      $results = $query->execute()->fetchAll();
      return $results;
  }
  
  
  
  public function listHistoryQuestionName(){
      $wxId = UserService::getWxId();      
      $fields = array(
          'wxid',
          'question_name',
          'zx_date' ,
          'result'
      );
      $query = \Drupal::database()->select('users_zhanxing_history', 'n');
      $query->condition('n.wxid', $wxId);    
      $query->condition('n.zx_date', $_REQUEST['zx_date']); 
      $query->fields('n', $fields);
      
      $results = $query->execute()->fetchAll();
      return $results;
  }

  
  public function parserUri($data){
        return "/sites/default/files/" . str_replace("public://","",$data->uri);
  }
  
 
  

 
}
