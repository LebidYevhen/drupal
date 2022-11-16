<?php

namespace Drupal\yl_simple_form\Form;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Simple Form.
 */
class SimpleForm extends FormBase {

  /**
   * {@inheritdoc}
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  private $logger;

  /**
   * {@inheritdoc}
   *
   * @var \Drupal\Component\Utility\EmailValidator
   */
  private EmailValidator $emailer;

  /**
   * {@inheritdoc}
   */
  public function __construct(LoggerChannelFactoryInterface $loggerFactory, EmailValidator $emailValidator) {
    $this->logger = $loggerFactory->get('yl_simple_form');
    $this->emailer = $emailValidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('logger.factory'),
      $container->get('email.validator'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {

    return 'yl_simple_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['user_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('User Name:'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email:'),
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
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');

    if ($value == !$this->emailer->isValid($value)) {
      $form_state->setErrorByName(
        'email',
        $this->t('The email address %mail is not valid.', ['%mail' => $value])
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $userName = $form_state->getValue('user_name');
    $email = $form_state->getValue('email');
    $message = sprintf('User Name: %s, Email: %s', $userName, $email);

    $this->logger->info($message);
  }

}
