<?php
/**
 * @file
 * Contains \Drupal\astrology\Controller\AstrologyController.
 */

namespace Drupal\astrology\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * AstrologyController
 */
class AstrologyController extends ControllerBase {
  /**
   * Generates an example page.
   */
	public function render() {
	    $response['data'] = 'Some test data to return';
    	$response['method'] = 'GET';

    	return new JsonResponse( $response );
  	}    
}
