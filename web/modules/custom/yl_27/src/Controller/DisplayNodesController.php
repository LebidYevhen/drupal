<?php

namespace Drupal\yl_27\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\path_alias\AliasManager;
use Psr\Container\ContainerInterface;

/**
 * Get nodes from the database and returns a render-able array.
 */
class DisplayNodesController extends ControllerBase {

  /**
   * {@inheritdoc}
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * {@inheritdoc}
   *
   * @var \Drupal\path_alias\AliasManager
   */
  protected $aliasManager;

  /**
   * DisplayNodesController constructor.
   */
  public function __construct(Connection $db, AliasManager $alias_manager) {
    $this->database = $db;
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('path_alias.manager'),
    );
  }

  /**
   * Get nodes from the database.
   *
   * @param int $number_of_nodes
   *   Number of nodes to get.
   */
  public function getNodes(int $number_of_nodes) {
    return $this->database
      ->select('node_field_data', 'n')
      ->fields('n')
      ->range(0, $number_of_nodes)
      ->execute()
      ->fetchAll();
  }

  /**
   * Get specific node data.
   *
   * @return array
   *   Associative array.
   */
  public function getNodesData(): array {
    $nodesData = [];

    $nodes = $this->getNodes(5);

    foreach ($nodes as $node) {
      $title = $node->title;
      $created = date('l, F j, Y - H:i', $node->created);
      $link = $this->aliasManager
        ->getAliasByPath('/node/' . $node->nid);

      $nodesData[] = [
        'title' => $title,
        'created' => $created,
        'link' => $link,
      ];
    }

    return $nodesData;
  }

  /**
   * Returns a render-able array.
   */
  public function content() {
    $nodes = $this->getNodesData();

    return [
      '#theme' => 'yl_27_theme_hook',
      '#nodes' => $nodes,
    ];
  }

}
