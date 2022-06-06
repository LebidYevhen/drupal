<?php

namespace Drupal\yl_page_block_js\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 */
class PageBlockJsController extends ControllerBase {

  /**
   *
   */
  public function content() {
    $output = [];

    $output['#title'] = 'Page Block Js page title';
    $output['#markup'] = 'Page Block Js';
    $output['#attached']['library'][] = 'yl_page_block_js/yl_page_block_js_lib';

    return $output;
  }

}
