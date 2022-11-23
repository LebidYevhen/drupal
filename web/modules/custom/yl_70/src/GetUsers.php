<?php

namespace Drupal\yl_70;

use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;

/**
 * Get users with email and date of birth fields.
 *
 * @package Drupal\yl_70\Services
 */
class GetUsers {

  /**
   * Connection with database.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $connection;

  /**
   * GetUsers constructor.
   */
  public function __construct() {
    $this->connection = Database::getConnection();
  }

  /**
   * Get a list of users who have their email and date of birth set.
   */
  public function getUsers(): array {
    $query = $this->connection->select('users_field_data', 'n');
    $query->innerJoin('user__field_user_birthdate', 'u', 'n.uid = u.entity_id');
    $query->fields('n', ['uid', 'mail', 'name', 'langcode']);
    $query->fields('u', ['field_user_birthdate_value']);
    $query->where('MONTH(u.field_user_birthdate_value) = MONTH(SYSDATE()) AND DAY(u.field_user_birthdate_value) = DAY(SYSDATE())');

    return $query->execute()->fetchAll();
  }

}
