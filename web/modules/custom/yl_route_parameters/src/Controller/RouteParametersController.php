<?php

namespace Drupal\yl_route_parameters\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class that renders and send data to the theme_hook().
 */
class RouteParametersController extends ControllerBase {

  /**
   * Loops through the array of machine names and render html of blocks.
   *
   * @param int $amount
   *   Number from the url query parameter.
   *
   * @return array
   *   rendered data to the theme hook.
   */
  public function content(int $amount): array {
    $rendered_data = [];

    // Filled array of duplicated machine block name.
    $machine_names = array_fill(0, $amount, 'system_powered_by_block');

    $block_manager = \Drupal::service('plugin.manager.block');
    foreach ($machine_names as $machine_name) {
      $plugin_block = $block_manager->createInstance($machine_name);
      $render = $plugin_block->build();
      $rendered_data[] = $render;
      \Drupal::service('renderer')
        ->addCacheableDependency($render, $plugin_block);
    }

    return [
      '#theme' => 'yl_route_parameters',
      '#blocks' => $rendered_data,
      '#title' => $this->t('Blocks'),
    ];

  }

}
