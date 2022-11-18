<?php

namespace Drupal\yl_36\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form.
 */
class FormValidation extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'yl_36';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['yl_36_phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['yl_36_phone']['#attributes']['placeholder'] = '+375 (XX) XXX-XX-XX';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $enteredValue = $form_state->getValue('yl_36_phone');

    if (!preg_match('/^\+375\s?\((17|29|33|44)\)\s?[0-9]{3}-[0-9]{2}-[0-9]{2}$/', $enteredValue)) {
      $form_state->setErrorByName('phone_number', sprintf(
        'The phone number is must matches +375 (17|29|33|44) XXX-XX-XX. The Phone you entered is - %s',
        $enteredValue)
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

}
