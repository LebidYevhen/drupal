<?php

namespace Drupal\yl_check_credentials\Routing;

use Drupal\user\Entity\User;
use Drupal\Core\Access\AccessResult;
use Symfony\Component\Routing\Route;

/**
 * The class responsible for creating the route.
 */
class CheckCredentialsRoute {

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
      $user->hasRole('manager') && $this->isMinuteEven() ||
      $user->isAuthenticated() && !$this->isMinuteEven()
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
   * Creates a Route object.
   *
   * @return array
   *   of Route objects.
   */
  public function routes(): array {
    $routes = [];

    // Declares a single route under the name 'yl_check_credentials.route'.
    $routes['yl_check_credentials.route'] = new Route(
    // Path to attach this route to:
      '/check_credentials',
      // Route defaults:
      [
        '_controller' => '\Drupal\yl_check_credentials\Controller\CheckCredentialsController::content',
        '_title'      => 'Check Credentials page',
      ],
      // Route requirements:
      [
        '_permission'    => 'access content',
        '_custom_access' => '\Drupal\yl_check_credentials\Routing\CheckCredentialsRoute::access',
      ]
    );

    return $routes;
  }

}
