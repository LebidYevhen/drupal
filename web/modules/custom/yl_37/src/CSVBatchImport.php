<?php

namespace Drupal\yl_37;

use Drupal\user\Entity\User;
use Drupal\file\Entity\File;

/**
 * Class CSVBatchImport.
 *
 * @package Drupal\yl_37
 */
class CSVBatchImport {

  /**
   * This is where we will store all the information about our Batch operation.
   */
  private array $batch;

  /**
   * FID for CSV file.
   *
   * @var mixed
   */
  private $fid;

  /**
   * File object.
   *
   * @var mixed
   */
  private $file;

  /**
   * By default, the first line will be read and processed.
   */
  private bool $skipFirstLine;

  /**
   * CSV column separator.
   */
  private string $delimiter;

  /**
   * CSV field limiter.
   */
  private string $enclosure;

  /**
   * {@inheritdoc}
   */
  public function __construct($fid, $skip_first_line = FALSE, $delimiter = ';', $enclosure = ',', $batch_name = 'Custom CSV import') {
    $this->fid = $fid;
    $this->file = File::load($fid);
    $this->skipFirstLine = $skip_first_line;
    $this->delimiter = $delimiter;
    $this->enclosure = $enclosure;
    $this->batch = [
      'title' => $batch_name,
      'finished' => [$this, 'finished'],
      'file' => \Drupal::service('extension.list.module')->getPath('yl_37') . '/src/CSVBatchImport.php',
    ];
    $this->parseCsv();
  }

  /**
   * {@inheritdoc}
   */
  public function setOperation($data) {
    $this->batch['operations'][] = [[$this, 'processItem'], $data];
  }

  /**
   * {@inheritdoc}
   */
  public function setBatch() {
    batch_set($this->batch);
  }

  /**
   * {@inheritdoc}
   */
  public function processBatch() {
    batch_process();
  }

  /**
   * {@inheritdoc}
   */
  public function finished($success, $results, $operations) {
    if ($success) {
      $message = \Drupal::translation()
        ->formatPlural(count($results), 'One post processed.', '@count posts processed.');
    }
    else {
      $message = $this->t('Finished with an error.');
    }
    \Drupal::messenger()->addStatus($message);
  }

  /**
   * {@inheritdoc}
   */
  public function parseCsv() {
    if (($handle = fopen($this->file->getFileUri(), 'r')) !== FALSE) {
      if ($this->skipFirstLine) {
        fgetcsv($handle, 0, ';');
      }
      while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
        $this->setOperation($data);
      }
      fclose($handle);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($name, $mail, $pass, &$context) {

    $ids = \Drupal::entityQuery('user')
      ->condition('mail', $mail)
      ->execute();
    if (empty($ids)) {
      $user = User::create();
      $user->setPassword($pass);
      $user->enforceIsNew();
      $user->setEmail($mail);
      $user->setUsername($name);
      $user->activate();

      $user->save();
      $context['results'][] = $user->id() . ' : ' . $user->getAccountName();
      $context['message'] = $user->label();
    }
  }

}
