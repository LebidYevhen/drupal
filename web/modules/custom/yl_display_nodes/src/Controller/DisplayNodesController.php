<?php

namespace Drupal\yl_display_nodes\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;

/**
 * Class DisplayNodesController gets nodes from the database and return them.
 *
 * @package Drupal\yl_display_nodes\Controller
 */
class DisplayNodesController extends ControllerBase {

  /**
   * Gets nodes from the database.
   *
   * @return mixed
   *   data from the query.
   */
  public function getNodes() {
    $query = \Drupal::database()->select('node_field_data', 'n');
    $query->innerJoin('node', 'u', 'n.nid = u.nid');
    $query->fields('n', ['title', 'created', 'nid']);
    $query->fields('u', ['nid']);

    return $query
      ->condition('n.status', 1)
      ->range(0, 5)
      ->execute()
      ->fetchAll();
  }

  /**
   * Loops through @return array
   *   with data to display in the twig template.
   *
   * @link getNodes() data and returns them to the twig template.
   *
   */
  public function getNodesAction(): array {
    $nodes_data = [];

    $options = ['absolute' => TRUE, 'attributes' => ['class' => 'node-link']];
    foreach ($this->getNodes() as $node) {
      $nid = $node->nid;
      $node_title = $node->title;
      $node_date_created = \Drupal::service('date.formatter')
        ->format($node->created, NULL, NULL, date_default_timezone_get());
      $link_object = Link::createFromRoute(
        $node_title,
        'entity.node.canonical',
        ['node' => $nid],
        $options);

      $nodes_data[] = [
        'link_object' => $link_object->toString(),
        'node_date_created' => $node_date_created,
      ];
    }

    return [
      '#theme' => 'yl_display_nodes',
      '#nodes_data' => $nodes_data,
      'attributes' => [
        'class' => ['nodes'],
      ],
    ];
  }

}
