<?php

namespace Drupal\yl_configuration_form\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class ModuleConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'yl_configuration_form_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'yl_configuration_form.admin_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('yl_configuration_form.admin_settings');

    $form['country_name'] = [
      '#type'          => 'select',
      '#options'       => $this->getTaxonomyTerms('country'),
      '#title'         => $this->t('Select a country'),
      '#term_id'       => $config->get('term_id'),
      '#default_value' => $config->get('term_id'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('yl_configuration_form.admin_settings')
      ->set('term_id', $form_state->getValue('country_name'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Gets an associative array of pairs term_id => term_name.
   *
   * @param string $term_name
   *   Term name.
   *
   * @return array
   *   An associative array of pairs term_id => term_name.
   */
  public function getTaxonomyTerms(string $term_name): array {
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($term_name);

    $terms_data = [];
    foreach ($terms as $term) {
      $term_id   = $term->tid;
      $term_name = $term->name;

      $terms_data[$term_id] = $term_name;
    }

    return $terms_data;
  }

}
