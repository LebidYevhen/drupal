<?php

namespace Drupal\yl_check_credentials\Controller;

/**
 * Provides route responses for the yl_check_credentials module.
 */
class CheckCredentialsController {

  /**
   * Returns a page.
   *
   * @return array
   *   with theme hook data and access information.
   */
  public function content(): array {

    return [
      '#theme'       => 'yl_check_credentials',
      '#access_info' => 'Access granted!',
    ];
  }

}
