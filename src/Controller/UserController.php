<?php
/**
 * @file
 * Contains \Drupal\astrology\Controller\UserController.
 */

namespace Drupal\astrology\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Database\Database;
use Drupal\astrology\Data\DataUtil;

/**
 * UserController
 */
class UserController extends ControllerBase {

  public function do_req($method) {
      
    switch ($method){
        case  'add':
            $results=$this->addUser();
            break;          
        case 'get':
            $results = $this->loadUserInfo();
            break;
        case 'test':
            $results = $this->test();
             break;     
        default:
            break;
    }
    
    return new JsonResponse( $results );
  }
  
  public function addUser(){
      $wxId='test';

      $query = \Drupal::database()->select('users_xingzuo_data', 'n');
      $query->condition('n.wxid', $wxId);
      $query->fields('n', array('wxid'));
   
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
      
      
      if($count > 0){
          $exe_results = \Drupal::database()->update("users_xingzuo_data")->condition('wxid', $wxId)->fields($fields)->execute();          
      }else{
          $exe_results = \Drupal::database()->insert("users_xingzuo_data")->fields($fields)->execute();
     
      }
      return   $exe_results;
  }
  
  public function loadUserInfo(){
      $wxId='test';
      
      $query = \Drupal::database()->select('users_xingzuo_data', 'n');
      $query->fields(null,  array(
          'name',
          'sex',
          'birthDay',
          'live_address',
          'birth_address',
          'wxid'
      ));
      $query->condition('n.wxid', $wxId);   
      $results = $query->execute()->fetchAll();
      return $results;
  }
  
  public function test(){
      //return DataUtil::getXingzuoByDate("2017-09-04 16:39:57");
      return DataUtil::getRandomXingzuo();
  }


}
