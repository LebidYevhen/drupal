<?php

namespace Drupal\yl_page_block_js\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Page Block Js' Block.
 *
 * @Block(
 *   id = "yl_page_block_js",
 *   admin_label = @Translation("Page Block Js"),
 * )
 */
class PageBlockJs extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['label_display' => FALSE];
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [
      '#theme'     => 'yl_page_block_js',
      '#test_var' => 'test var',
    ];
  }

}
