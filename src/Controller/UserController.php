<?php
/**
 * @file
 * Contains \Drupal\astrology\Controller\UserController.
 */
namespace Drupal\astrology\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\astrology\service\UserService;
use Drupal\astrology\service\WeixinService;
use function GuzzleHttp\json_decode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\astrology\service\Logger;
use Drupal\astrology\service\LoggerUtil;
use Symfony\Component\HttpFoundation\Response;


/**
 * UserController
 */
class UserController extends ControllerBase
{

    
    public function do_req($method)
    {
        
        
        switch ($method) {
            case 'add':
                $results = $this->addUser();
                break;
            case 'get':
                $results = $this->loadUserInfo();
                break;
            case 'test':
                $results =$this->test();
                break;
            case 'loadxingpan':
                $results = $this->loadxingpan();
                break;
            case 'token':
                $results = $this->validWxToken();
                break;
            case 'load_wx_user':
                $results = $this->loadWeiXinUserInfo();
                break;
            case 'count_total_report':
                $results = $this->countTotaLReport();
                break;
            case 'list_person_report':
                $results = $this->listPersonReport();
                break;
            default:
                break;
        }
        if($method == "token"){
            return new Response($_REQUEST['echostr']);
        }else{
            $res = new JsonResponse($results);
            $res->setCallback($_REQUEST['callback']);
            return $res;
        }
    }

    public function addUser()
    {
        $wxId = UserService::getWxId();
        $query = \Drupal::database()->select('users_xingzuo_data', 'n');
        $query->condition('n.wxid', $wxId);
        $query->condition('n.name', $_REQUEST['name']);
//         $query->condition('n.birthDay', $_REQUEST['birthDay']);
        $query->fields('n', array(
            'wxid',
            'id'
        ));
        
      
        LoggerUtil::log("addUser", $_REQUEST['birthDay']);
        $results = $query->execute()->fetchAll();
        $count = count($results);
        $fields = array(
            'name' => $_REQUEST['name'],
            'sex' => $_REQUEST['sex'],
            'birthDay' => $_REQUEST['birthDay'],
            'live_address' => $_REQUEST['live_address'],
            'birth_address' => $_REQUEST['birth_address'],
            'region_id_list' => $_REQUEST['region_id_list'],
            'wxid' => $wxId,
            'last_update' => time()
        );
        $x_z_d_id = "";
        if ($count > 0) {
          
            $x_z_d_id =   $results[0]->id;
            $exe_results = \Drupal::database()->update("users_xingzuo_data")
            ->condition('id', $x_z_d_id)
                ->fields($fields)
                ->execute();
        } else {
            $x_z_d_id = uniqid();
            $fields['id'] = $x_z_d_id;
            $exe_results = \Drupal::database()->insert("users_xingzuo_data")
                ->fields($fields)
                ->execute();
        }
        
        $url = "http://127.0.0.1:8080/api/v1/calc?birthday=" . urlencode($_REQUEST['birthDay']) . "&address=" . urlencode($_REQUEST['birth_address']);
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        
        $astro_result_fields = array(
            'result' => $output,
            'wxid' => $wxId,
            'ispay' => 0,
            'id' => uniqid(),
            'createdOn'=>time(),
            'u_x_d_id' => $x_z_d_id
        );
        
        \Drupal::database()->delete("users_xingpan_data")->condition('u_x_d_id', $x_z_d_id)->execute();
        \Drupal::database()->insert("users_xingpan_data")->fields($astro_result_fields)->execute();
  
        $astro_result = json_decode($output);
        
        return $astro_result->fileName;
    }

    public function loadUserInfo()
    {
        $wxId = UserService::getWxId();
        return UserService::loadUserInfo($wxId);
    }

    function loadxingpan()
    {
        
        return AstrologyController::loadXinPanData("", $_REQUEST['user_data_id']);
    }

    function loadWeiXinUserInfo()
    {
        $wxId = UserService::getWxId();
    }
    
    public function countTotaLReport(){
       return UserService::countTotaLReport();      
    }
    
    public function listPersonReport(){
        return UserService::listPersonReport();    
    }

    public function test()
    {
        // return DataUtil::getXingzuoByDate("2017-09-04 16:39:57");
        // return WeixinService::getJsTicket();
//         return WeixinService::authorization_code();

       // appId=wxa615717ee45f9b47&nonceStr=59ba458f278ed&package=prepay_id=wx20170914170209636d776a4b0062094763&signType=MD5&timeStamp=1505379727&key=b865048d1ae9e14c8a193652e3bf704b
        $s1 = "appid=wxa615717ee45f9b47&nonceStr=59ba458f278ed&package=prepay_id=wx20170914170209636d776a4b0062094763&signType=MD5&timestamp=1505379727&key=b865048d1ae9e14c8a193652e3bf704b";
        
        $s2 = "appId=wxa615717ee45f9b47&nonceStr=59ba458f278ed&package=prepay_id=wx20170914170209636d776a4b0062094763&signType=MD5&timeStamp=1505379727&key=b865048d1ae9e14c8a193652e3bf704b";
        
        if($s1 == $s2){
            var_dump(strtoupper(md5("appId=wxa615717ee45f9b47&nonceStr=59ba458f278ed&package=prepay_id=wx20170914170209636d776a4b0062094763&signType=MD5&timeStamp=1505379727&key=b865048d1ae9e14c8a193652e3bf704b")));
            var_dump(strtoupper(md5($s1)));
            var_dump(strtoupper(md5($s2)));
            
        }
        
        
        // return array(urlencode("http://test.vlvlife.com/index.php/astrology/user/token"));
    }

    public function validWxToken()
    {
        return $_REQUEST['echostr'];
    }
}
