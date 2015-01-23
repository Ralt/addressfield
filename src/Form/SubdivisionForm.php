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
      '#description' => $this->t(''),
      '#default_value' => $subdivision->getId(),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#disabled' => !$subdivision->isNew(),
    );
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $subdivision->getName(),
      '#maxlength' => 255,
      '#required' => TRUE,
    );
    $form['code'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Code'),
      '#default_value' => $subdivision->getCode(),
      "#description" => $this->t('Represents the subdivision on the envelope. For example: "CA" for California. The code will be in the local (non-latin) script if the country uses one.'),
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
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $subdivision = $this->entity;
    $subdivision->save();
    drupal_set_message($this->t('Saved the %label subdivision.', array(
      '%label' => $subdivision->label(),
    )));
    $form_state->setRedirectUrl($subdivision->urlInfo('collection'));
  }

}
