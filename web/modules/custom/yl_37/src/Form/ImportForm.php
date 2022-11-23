<?php

namespace Drupal\yl_37\Form;

use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\file\Entity\File;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\FileUsage\DatabaseFileUsageBackend;
use Drupal\yl_37\CSVBatchImport;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ImportForm.
 *
 * @package Drupal\yl_37\Form
 */
class ImportForm extends ConfigFormBase {

  /**
   * DateFormatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  private DateFormatter $dateFormatter;

  /**
   * FileUsage service.
   */
  private DatabaseFileUsageBackend $fileUsage;

  /**
   * EntityTypeManager service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|\Drupal\Core\Entity\EntityTypeManager
   */
  private $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManager $entity_type_manager, DateFormatter $date_formatter, DatabaseFileUsageBackend $file_usage) {
    $this->entityTypeManager = $entity_type_manager;
    $this->dateFormatter = $date_formatter;
    $this->fileUsage = $file_usage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('date.formatter'),
      $container->get('file.usage'),
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['yl_37.import'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('yl_37.import');

    $form['file'] = [
      '#title' => $this->t('CSV file'),
      '#type' => 'managed_file',
      '#upload_location' => 'public://',
      '#default_value' => $config->get('fid') ? [$config->get('fid')] : NULL,
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#required' => TRUE,
    ];

    if (!empty($config->get('fid'))) {
      $file = $this->entityTypeManager->getStorage('file')->load($config->get('fid'));
      $created = $this->dateFormatter
        ->format($file->created->value, 'medium');

      $form['file_information'] = [
        '#markup' => $this->t('This file was uploaded at @created.', ['@created' => $created]),
      ];

      $form['actions']['start_import'] = [
        '#type' => 'submit',
        '#value' => $this->t('Start import'),
        '#submit' => ['::startImport'],
        '#weight' => 100,
      ];
    }

    $form['additional_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Additional settings'),
    ];

    $form['additional_settings']['skip_first_line'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Skip first line'),
      '#default_value' => $config->get('skip_first_line'),
      '#description' => $this->t('If file contain titles, this checkbox help to skip first line.'),
    ];

    $form['additional_settings']['delimiter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Delimiter'),
      '#default_value' => $config->get('delimiter'),
      '#required' => TRUE,
    ];

    $form['additional_settings']['enclosure'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enclosure'),
      '#default_value' => $config->get('enclosure'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('yl_37.import');
    $fid_old = $config->get('fid');
    $fid_form = $form_state->getValue('file')[0];

    if (empty($fid_old) || $fid_old != $fid_form) {
      if (!empty($fid_old)) {
        $previous_file = $this->entityTypeManager->getStorage('file')->load($fid_old);
        $this->fileUsage
          ->delete($previous_file, 'yl_37', 'config_form', $previous_file->id());
      }
      $new_file = $this->entityTypeManager->getStorage('file')->load($fid_form);
      $new_file->save();
      $this->fileUsage
        ->add($new_file, 'yl_37', 'config_form', $new_file->id());
      $config->set('fid', $fid_form)
        ->set('creation', time());
    }

    $config->set('skip_first_line', $form_state->getValue('skip_first_line'))
      ->set('delimiter', $form_state->getValue('delimiter'))
      ->set('enclosure', $form_state->getValue('enclosure'))
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function startImport(array &$form, FormStateInterface $form_state) {
    $config = $this->config('yl_37.import');
    $fid = $config->get('fid');
    $skip_first_line = $config->get('skip_first_line');
    $delimiter = $config->get('delimiter');
    $enclosure = $config->get('enclosure');
    $import = new CSVBatchImport($fid, $skip_first_line, $delimiter, $enclosure);
    $import->setBatch();
  }

}
