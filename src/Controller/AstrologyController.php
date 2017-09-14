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
use function GuzzleHttp\Promise\each;
use Drupal\astrology\service\WeixinService;
use Symfony\Component\HttpFoundation\Response;
use Drupal\astrology\service\LoggerUtil;
use Drupal\astrology\service\ConfigService;

/**
 * AstrologyController
 */
class AstrologyController extends ControllerBase {
  /**
   * Generates an example page.
   */
    public function do_req($method) {
        
        $sessionId = $_SESSION['ast_c_id_session_id'];
        LoggerUtil::log1("sessionId ====>" . $sessionId);
        switch ($method){
            case  'read_result':
                $results=$this->readXinpanResultData();
                break;
            case 'read_xingxiang':
                $results =  $this->readXingXiangData();
                break;
            case 'read_kaiyun':
                $results =  $this->readKaiYunData();
                break;
                
            case 'read_yunshi':
                $results =  $this->readNengLiangYunShiData();
                break;
            default:
                break;
        }

        
        if($method == "login"){
            $login_info = UserService::loadLoginInfo();
            
            if((empty($login_info) || empty($login_info['openId'])) &&  !strstr($_SERVER['HTTP_REFERER'], "ast_redirect")){
                LoggerUtil::log("login", " need login for sessiong ====>" .  $_SESSION['ast_c_id_session_id'] . " and referer is " . $_SERVER['HTTP_REFERER']);   
                $headers = array("Content-Type"=>"application/javascript");
                return new Response("login('". ConfigService::getAppId() ."');", "200", $headers);
            }else{
                $headers = array("Content-Type"=>"application/javascript");
                return new Response("", "200", $headers);
            }
            
        }else{
            $res =  new JsonResponse( $results );
            $res->setCallback($_REQUEST['callback']);
            return $res;
        }
    }
    
    public function readXinpanResultData(){
        
        $wxId=UserService::getWxId();
        
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
        $wxId = UserService::getWxId();
        $user = UserService::loadUserInfo($wxId);
        $xingzuo_name = DataUtil::getXingzuoByDate($user['birthDay']);
        
        
        $query_str = "select n.title, x.field_xinggetezhi_value, s.field_shejiaoguanxi_value, b.body_value from node__field_xinggetezhi as x, node__field_shejiaoguanxi as s, node__body as b, node__field_guanlianxingzuo_list as g,
 node_field_data as n where n.nid=g.entity_id and g.entity_id=x.entity_id and g.entity_id=s.entity_id and b.entity_id=g.entity_id and g.field_guanlianxingzuo_list_value='" . $xingzuo_name['xingzuo'] ."';";
        $results = \Drupal::database()->query($query_str)->fetchAll();

        return $results;
    }

    public function readKaiYunData(){
        $wxId = UserService::getWxId();
        $user = UserService::loadUserInfo($wxId);
        $xingzuo_name = DataUtil::getXingzuoByDate($user['birthDay']);
        
        
        $query_str = "select b.field_buchangxingzuo_value, y.field_xingyunyanse_value,j.field_duiyingjinshu_value, k.field_kaiyunbaoshi_value from node__field_kaiyunbaoshi as k, node__field_xingzuo_name as g
        , node__field_duiyingjinshu as j,node__field_xingyunyanse as y,node__field_buchangxingzuo as b where b.entity_id=g.entity_id and y.entity_id=g.entity_id and j.entity_id=g.entity_id and k.entity_id=g.entity_id
        and g.field_xingzuo_name_value='" . $xingzuo_name['xingzuo'] ."'";
        
        
        $results = \Drupal::database()->query($query_str)->fetchAll();
        foreach ($results as $item){
            $item->xz_name_cn= DataUtil::getXingzuoCNName($item->field_buchangxingzuo_value)."座";          
        }
        
        return $results;
        
    }
    
    public function readNengLiangYunShiData(){
        
        
        $wxId = UserService::getWxId();
        $user = UserService::loadUserInfo($wxId);
        $xingzuo_name = DataUtil::getXingzuoByDate($user['birthDay']);
        
        
        
        
        $query_str = "select x.field_xingxing_value, d.field_nengliangdingwei_value, z.field_nengliangxingzhi_value, f.field_nengliangfangxiang_value,
        xg.field_xingzuoxinggejiyu_value from node__field_xingxing as x, node__field_nengliangdingwei as d , node__field_nengliangxingzhi as z,
        node__field_xingzuoxinggejiyu as xg,node__field_nengliangfangxiang as f, node__field_xingzuo_name as g where x.entity_id=g.entity_id and d.entity_id=g.entity_id and z.entity_id=g.entity_id
        and f.entity_id=g.entity_id and xg.entity_id=g.entity_id and g.field_xingzuo_name_value='" . $xingzuo_name['xingzuo'] ."'";
        
   
        
        
        $results = \Drupal::database()->query($query_str)->fetchAll();
        foreach ($results as $item){
            $item->field_xingxing_value = DataUtil::getXingxinCNName($item->field_xingxing_value);
            $item->field_nengliangdingwei_value = DataUtil::getNengLiangDingWeiName($item->field_nengliangdingwei_value);
            $item->field_nengliangxingzhi_value = DataUtil::getNengLiangXingzhiName($item->field_nengliangxingzhi_value);
            
            $item->field_nengliangfangxiang_value = DataUtil::getNengLiangFangXiang($item->field_nengliangfangxiang_value);
        }
        
        return $results;
        
        
    }
}
