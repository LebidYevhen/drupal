<?php


namespace Drupal\yl_display_nodes\Controller;


class DisplayNodesController
{
  public function get_nodes()
  {
    $nodes = \Drupal::database()
      ->select('node_field_data', 'n')
      ->fields('n')
      ->range(0, 5)
      ->execute()
      ->fetchAll();

    return $nodes;
  }

  public function display_nodes()
  {
    $nodes_html = '';
    $node_html_structure = '<div class="node"><h2><a href="%s">%s</a></h2><p>%s</p></div>';
    $host = \Drupal::request()->getSchemeAndHttpHost();

    foreach ($this->get_nodes() as $node) {
      $node_url = $host . '/node/'. $node->nid;
      $node_title = $node->title;
      $node_date_created = date('F j, Y', $node->created);



      $nodes_html = $nodes_html . sprintf($node_html_structure, $node_url, $node_title, $node_date_created);
    }

    return array(
      '#markup' => $nodes_html
    );
  }
}
