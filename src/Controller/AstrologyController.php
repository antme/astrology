<?php
/**
 * @file
 * Contains \Drupal\astrology\Controller\AstrologyController.
 */

namespace Drupal\astrology\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\astrology\service\UserService;
use Drupal\astrology\Data\DataUtil;

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
            case 'read_xingxiang':
                $results =  $this->readXingXiangData();
                break;
            default:
                break;
        }
        
        $res =  new JsonResponse( $results );
        $res->setCallback($_REQUEST['callback']);
        return $res;
    }
    
    public function readXinpanResultData(){
        
        $wxId='test';
        
        $query = \Drupal::database()->select('users_xingpan_data', 'n');
        $query->condition('n.wxid', $wxId);
        $query->fields('n', array('wxid', 'result'));
        $results = $query->execute()->fetchAssoc();
        $results['result'] = json_decode($results['result']);
        
        
        $type = $_REQUEST['type'];
        $join_type = "gerenxingpan_caifumima";
        if($type == "emotion"){
            $join_type = 'gerenxingpan_ganqingmima';
            
        }else   if($type == "businese"){
            $join_type = 'gerenxingpan_shiyemima';
            
        }  if($type == "relationship"){
            $join_type = 'gerenxingpan_renjiguanxi';
            
        }  if($type == "fortune"){
            $join_type = 'gerenxingpan_caifumima';
            
        }
        
        
       
        $query_sql = "select t.title, b.body_value from  node_field_data as t, node__body as b, node as n where n.type = '" .$join_type ."' and n.nid=t.nid and n.nid=b.entity_id";
        $query_results = \Drupal::database()->query($query_sql)->fetchAll();
        
        $final_query_results = array();
        
        $xingxinXingzuo = $results['result']->xingxinXingzuo;
        $xingxinGonwei =  $results['result']->xingxinGonwei;
        $gongweiXingzuo =  $results['result']->gonweiXingzuo;
        
        $check_arr = array();
        
        foreach ($xingxinXingzuo as $item){
            if(!in_array($item, $check_arr)){
                array_push($check_arr, $item);   
            }
        }
        foreach ($xingxinGonwei as $item){
            if(!in_array($item, $check_arr)){
                array_push($check_arr, $item);    
            }
        }
        foreach ($gongweiXingzuo as $item){
            if(!in_array($item, $check_arr)){
                array_push($check_arr, $item); 
            }
        }
   
        
        foreach ($check_arr as $value){
            
            foreach ($query_results as $item){
                if($item->title == $value){
                    array_push($final_query_results, $item);
                }
            }  
        }
        

        return $final_query_results;
        
    }
    
    public function readXingXiangData(){
        $wxId = 'test';
        $user = UserService::loadUserInfo($wxId);
        $xingzuo_name = DataUtil::getXingzuoByDate($user['birthDay']);
        
        
        $query_str = "select n.title, x.field_xinggetezhi_value, s.field_shejiaoguanxi_value, b.body_value from node__field_xinggetezhi as x, node__field_shejiaoguanxi as s, node__body as b, node__field_guanlianxingzuo_list as g,
 node_field_data as n where n.nid=g.entity_id and g.entity_id=x.entity_id and g.entity_id=s.entity_id and b.entity_id=g.entity_id and g.field_guanlianxingzuo_list_value='" . $xingzuo_name['xingzuo'] ."';";
        $results = \Drupal::database()->query($query_str)->fetchAll();

        return $results;
    }
}
