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

    session_start();
    WeixinService::authorization_code();
    
    public function do_req($method)
    {
        session_start();
        
        switch ($method) {
            case 'add':
                $results = $this->addUser();
                break;
            case 'get':
                $results = $this->loadUserInfo();
                break;
            case 'test':
                $results = $this->test();
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
        $query->fields('n', array(
            'wxid'
        ));
        
        $results = $query->execute()->fetchAll();
        $count = count($results);
        
        $fields = array(
            'name' => $_REQUEST['name'],
            'sex' => $_REQUEST['sex'],
            'birthDay' => $_REQUEST['birthDay'],
            'live_address' => $_REQUEST['live_address'],
            'birth_address' => $_REQUEST['birth_address'],
            'wxid' => $wxId
        );
        
        if ($count > 0) {
            $exe_results = \Drupal::database()->update("users_xingzuo_data")
                ->condition('wxid', $wxId)
                ->fields($fields)
                ->execute();
        } else {
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
            'wxid' => $wxId
        );
        
        $query = \Drupal::database()->select('users_xingpan_data', 'n');
        $query->condition('n.wxid', $wxId);
        $query->fields('n', array(
            'wxid'
        ));
        
        $results = $query->execute()->fetchAll();
        $count = count($results);
        
        if ($count > 0) {
            \Drupal::database()->update("users_xingpan_data")
                ->condition('wxid', $wxId)
                ->fields($astro_result_fields)
                ->execute();
        } else {
            \Drupal::database()->insert("users_xingpan_data")
                ->fields($astro_result_fields)
                ->execute();
        }
        
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
        $logger = new Logger("/tmp/ast");
        $wxId = UserService::getWxId();
        $logger->info("check wxid for loadxingpan " . $wxId);
        
        $query = \Drupal::database()->select('users_xingpan_data', 'n');
        $query->condition('n.wxid', $wxId);
        $query->fields('n', array(
            'wxid',
            'result'
        ));
        $results = $query->execute()->fetchAssoc();
        
        if (! empty($results) && isset($results['wxid'])) {
            $results['result'] = json_decode($results['result']);
            $results['wxid'] = $wxId;
            return $results;
        } else {
            return array();
        }
    }

    function loadWeiXinUserInfo()
    {
        $wxId = UserService::getWxId();
    }

    public function test()
    {
        // return DataUtil::getXingzuoByDate("2017-09-04 16:39:57");
        // return WeixinService::getJsTicket();
        return WeixinService::authorization_code();
        
        // return array(urlencode("http://test.vlvlife.com/index.php/astrology/user/token"));
    }

    public function validWxToken()
    {
        return $_REQUEST['echostr'];
    }
}
