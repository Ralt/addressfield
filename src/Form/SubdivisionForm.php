<?php

/**
 * @file
 * Contains Drupal\addressfield\Form\CommerceCurrencyForm.
 */

namespace Drupal\addressfield\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Locale\CountryManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubdivisionForm extends EntityForm {

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

    $form['parent'] = array(
      '#type' => 'details',
      '#title' => t('Parent subdivision'),
      '#group' => 'advanced',
      '#attributes' => array(
        'class' => array('subdivision-form-parent'),
      ),
      '#attached' => array(
        'library' => array('addressfield/drupal.addressfield'),
      ),
      '#weight' => 90,
      '#optional' => TRUE,
    );

    $form['countryCode'] = array(
      '#type' => 'select',
      '#title' => $this->t('Country Code'),
      '#default_value' => $subdivision->getCountryCode(),
      '#options' => CountryManager::getStandardList(),
      '#description' => 'This is a CLDR country code, since CLDR includes additional countries for addressing purposes, such as Canary Islands (IC).',
      '#required' => TRUE,
    );

    $form['code'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Code'),
      '#default_value' => $subdivision->getCode(),
      "#description" => $this->t('Represents the subdivision on the envelope. For example: "CA" for California. The code will be in the local (non-latin) script if the country uses one.'),
      '#required' => TRUE,
    );

    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $subdivision->getName(),
      '#required' => TRUE,
    );

    $form['postalCodePattern'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Postal Code Pattern'),
      '#default_value' => $subdivision->getPostalCodePattern(),
      "#description" => 'This is a regular expression pattern used to validate postal codes, ensuring that a postal code begins with the expected characters.'
    );


    return $form;
  }

  /**
   * Validates the currency code.
   * @param array $element
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param array $form
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
    } catch (\Exception $e) {
      drupal_set_message($this->t('The %label subdivision was not saved.', array('%label' => $subdivision->label())), 'error');
      $this->logger('addressfield')->error($e);
      $form_state->setRebuild();
    }
  }

}
