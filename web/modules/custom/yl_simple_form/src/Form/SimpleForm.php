<?php

namespace Drupal\yl_simple_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SimpleForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
    public function getFormId()
    {
        return 'yl_simple_form';
    }

  /**
   * {@inheritdoc}
   */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['user_name'] = [
        '#type' => 'textfield',
        '#title' => t('User Name:'),
        '#required' => true,
        ];

        $form['email'] = [
        '#type' => 'textfield',
        '#title' => t('Email:'),
        '#required' => true,
        ];

        $form['submit'] = [
        '#type' => 'submit',
        '#value' => t('Submit'),
        ];
        return $form;
    }

  /**
   * {@inheritdoc}
   */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $value = $form_state->getValue('email');

        if ($value == !\Drupal::service('email.validator')->isValid($value)) {
            $form_state->setErrorByName(
                'email',
                t('The email address %mail is not valid.', ['%mail' => $value])
            );
        }
    }

  /**
   * {@inheritdoc}
   */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $userName = $form_state->getValue('user_name');
        $email = $form_state->getValue('email');
        $message = "User Name: $userName, Email: $email";

        \Drupal::logger('yl_simple_form')->info($message);
    }
}
