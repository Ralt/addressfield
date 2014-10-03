<?php

/**
 * @file
 * Contains Drupal\addressfield\Form\CommerceCurrencyForm.
 */

namespace Drupal\addressfield\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubdivisionForm extends EntityForm {

  /**
   * The currency storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $currencyStorage;

  /**
   * Creates a CommerceCurrencyForm instance.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $currency_storage
   *   The currency storage.
   */
  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');

    return new static($entity_manager->getStorage('subdivision'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $subdivision = $this->entity;

    $form['id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('ID'),
      '#default_value' => $subdivision->getId(),
      '#maxlength' => 255,
      '#required' => TRUE,
    );

    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $subdivision->getName(),
      '#maxlength' => 255,
      '#required' => TRUE,
    );

    return $form;
  }

  /**
   * Validates the currency code.
   */
  public function validateCurrencyCode(array $element, FormStateInterface &$form_state, array $form) {
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $subdivision = $this->entity;

    try {
      $subdivision->save();
      drupal_set_message($this->t('Saved the %label subdivision.', array(
        '%label' => $subdivision->label(),
      )));
      $form_state->setRedirect('entity.subdivision.list');
    }
    catch (\Exception $e) {
      drupal_set_message($this->t('The %label subdivision was not saved.', array('%label' => $subdivision->label())), 'error');
      $this->logger('addressfield')->error($e);
      $form_state->setRebuild();
    }
  }

}
