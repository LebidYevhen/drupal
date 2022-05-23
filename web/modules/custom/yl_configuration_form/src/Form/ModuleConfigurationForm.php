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
    $config               = $this->config('yl_configuration_form.admin_settings');
    $form['country_name'] = [
      '#type'          => 'select',
      '#options'       => $this->getListOfCountries(),
      '#title'         => $this->t('Select a country'),
      '#default_value' => $config->get('country_var'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('yl_configuration_form.admin_settings')
         ->set('country_var', $form_state->getValue('country_name'))
         ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Get an array of country code => country name pairs.
   *
   * @return array
   *   An array of country code => country name pairs.
   *
   * @see \Drupal\Core\Locale\CountryManager::getStandardList()
   */
  public function getListOfCountries() {
    $country_manager = \Drupal::service('country_manager');
    $list            = $country_manager->getList();
    $countries       = [];
    foreach ($list as $key => $value) {
      $val             = $value->__toString();
      $countries[$key] = $val;
    }

    return $countries;
  }

}
