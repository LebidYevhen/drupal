<?php

namespace Drupal\yl_check_credentials\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\user\Entity\User;

/**
 * Provides route responses for the yl_check_credentials module.
 */
class CheckCredentialsController {

  /**
   * Checks access for a specific user role and conditions.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   access granted if the user has a role manager and @link isMinuteEven
   *   true or user is authenticated and @link isMinuteEven false, if not
   *   access is not granted.
   */
  public function access(): AccessResult {
    $user = User::load(\Drupal::currentUser()->id());

    return AccessResult::allowedIf(
    ($user->hasRole('manager') && $this->isMinuteEven()) ||
    ($user->isAuthenticated() && !$this->isMinuteEven())
    );
  }

  /**
   * Checks if the current minute is even or not.
   *
   * @return bool
   *   true if minutes are even, false else.
   */
  public function isMinuteEven(): bool {
    $current_time = \Drupal::time()->getCurrentTime();

    $minutes = \Drupal::service('date.formatter')
      ->format($current_time, 'custom', 'i');

    if ($minutes % 2 == 0) {
      return TRUE;
    }

    return FALSE;
  }

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
