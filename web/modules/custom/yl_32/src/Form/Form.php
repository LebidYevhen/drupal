<?php

namespace Drupal\yl_32\Form;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form.
 */
class Form extends FormBase {

  /**
   * {@inheritdoc}
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  private $logger;

  /**
   * {@inheritdoc}
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  private $entityManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(LoggerChannelFactoryInterface $loggerFactory, EntityTypeManager $entityTypeManage) {
    $this->logger = $loggerFactory->get('yl_32');
    $this->entityManager = $entityTypeManage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {

    return 'yl_32';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['countries'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Country:'),
      '#options' => $this->getVocabularyTerms('country'),
      '#required' => TRUE,
    ];

    $form['cities'] = [
      '#type' => 'select',
      '#title' => $this->t('Select City:'),
      '#options' => $this->getVocabularyTerms('city'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

  /**
   * Get vocabulary terms by term slug.
   *
   * @return array
   *   of term_id => term_name values.
   */
  public function getVocabularyTerms($vocabulary_name) {
    $terms_data = [];

    $vid = $vocabulary_name;
    $terms = $this->entityManager->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $terms_data[$term->tid] = $term->name;
    }

    return $terms_data;
  }

}
