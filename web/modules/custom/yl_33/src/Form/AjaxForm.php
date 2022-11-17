<?php

namespace Drupal\yl_33\Form;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Render a form with country and city select fields.
 */
class AjaxForm extends FormBase {

  /**
   * {@inheritdoc}
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $loggerFactory;

  /**
   * {@inheritdoc}
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(LoggerChannelFactoryInterface $loggerFactory, EntityTypeManager $entity_type_manager) {
    $this->loggerFactory = $loggerFactory->get('yl_33');
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {

    return 'yl_33';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $countryOptions = $this->getVocabularyTerms('country');

    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Country:'),
      '#options' => $countryOptions,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::myAjaxCallback',
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => 'cities',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying entry...'),
        ],
      ],
    ];

    $form['city'] = [
      '#type' => 'select',
      '#title' => $this->t('Select City:'),
      "#empty_option" => $this->t('- Select -'),
      '#required' => TRUE,
      '#prefix' => '<div id="cities">',
      '#suffix' => '</div>',
    ];

    if ($countryId = $form_state->getValue('country')) {
      $form['city']['#options'] = $this->getTermsById($countryId);
    }

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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $country_id = $form_state->getValue('country');
    $city_id = $form_state->getValue('city');
    $country_name = $this->entityTypeManager->getStorage('taxonomy_term')->load($country_id)->label();
    $city_name = $this->entityTypeManager->getStorage('taxonomy_term')->load($city_id)->label();

    $message = sprintf('Country: %s, City: %s', $country_name, $city_name);
    $this->loggerFactory->info($message);
  }

  /**
   * Get taxonomy terms by term slug.
   *
   * @return array
   *   of term_id => term_name values.
   */
  public function getVocabularyTerms($vocabulary_name): array {
    $terms_data = [];

    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vocabulary_name);
    foreach ($terms as $term) {
      $terms_data[$term->tid] = $term->name;
    }

    return $terms_data;
  }

  /**
   * Get City taxonomy terms related by the taxonomy Country id.
   */
  public function getTermsById($tid): array {
    $terms = [];

    $termsObj = $this->entityTypeManager->getStorage('taxonomy_term')->getQuery()
      ->condition('vid', 'city')
      ->condition('field_country', $tid)
      ->accessCheck(TRUE)
      ->execute();

    foreach ($termsObj as $term) {
      $terms[$term] = $this->entityTypeManager->getStorage('taxonomy_term')->load($term)->get('name')->value;
    }

    return $terms;
  }

  /**
   * Ajax callback to render a city select.
   */
  public function myAjaxCallback(array &$form, FormStateInterface $form_state) {
    return $form['city'];
  }

}
