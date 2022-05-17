<?php

namespace Drupal\yl_block_display_text\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with simple text.
 *
 * @Block (
 * id = "yl_block_display_text_block",
 * admin_label = @Translation("Block display text")
 * )
 */
class BlockDisplayText extends BlockBase {

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
      '#theme' => 'yl_block_display_text',
      '#text' => 'Hello, World',
    ];
  }

}
