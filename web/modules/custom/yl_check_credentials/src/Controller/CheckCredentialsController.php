<?php

namespace Drupal\yl_check_credentials\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the yl_check_credentials module.
 */
class CheckCredentialsController extends ControllerBase {

  /**
   * Returns a page.
   *
   * @return array
   *   A simple renderable array or throw 403 error.
   */
  public function checkCredentialsAction(): array {
    $current_user = \Drupal::currentUser()->getRoles();
    $current_time = \Drupal::time()->getCurrentTime();
    $minutes      = date('i', $current_time);

    if ((in_array('manager', $current_user) && ($minutes % 2 == 0))
          || (in_array('authenticated', $current_user) && ($minutes % 2 != 0))
      ) {
      return [
        '#theme'       => 'yl_check_credentials',
        '#access_info' => 'Access granted!',
      ];
    }
    else {
      throw new AccessDeniedHttpException();
    }

  }//end checkCredentialsAction()

}//end class
