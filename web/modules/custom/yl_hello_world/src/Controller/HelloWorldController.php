<?php


namespace Drupal\yl_hello_world\Controller;


class HelloWorldController
{
  public function hello_world()
  {
    return array(
      '#markup' => 'You can see my hello world module.'
    );
  }
}
