<?php

namespace Drupal\yl_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block with simple text.
 *
 * @Block (
 * id = "yl_block",
 * admin_label = @Translation("Custom Text Block")
 * )
 */
class YLBlock extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $build = [];
    $build['#theme'] = 'yl_block_block';

    return $build;
  }
}
