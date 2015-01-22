<?php

/**
 * @file
 * Contains Drupal\addressfield\Form\AddressFormatForm.
 */

namespace Drupal\addressfield\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Locale\CountryManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddressFormatForm extends EntityForm {

  /**
   * The address format storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $addressFormatStorage;

  /**
   * Creates an AddressFormatForm instance.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $address_format_storage
   *   The address format storage.
   */
  public function __construct(EntityStorageInterface $address_format_storage) {
    $this->addressFormatStorage = $address_format_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');

    return new static($entity_manager->getStorage('address_format'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $address_format = $this->entity;

    $form['countryCode'] = array(
      '#type' => 'select',
      '#title' => $this->t('Country code'),
      '#default_value' => $address_format->getCountryCode(),
      '#required' => TRUE,
      '#options' => CountryManager::getStandardList(),
    );
    $form['format'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Format'),
      '#description' => $this->t('Available tokens: @tokens', array('@tokens' => implode(', ', $address_format->getFieldsTokens()))),
      '#default_value' => $address_format->getFormat(),
      '#required' => TRUE,
    );
    $form['requiredFields'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Required fields'),
      '#options' => $address_format->getFields(),
      '#default_value' => $address_format->getRequiredFields(),
    );
    $form['uppercaseFields'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Uppercase fields'),
      '#description' => t('Select which fields needs to be uppercased for automatic post handling.'),
      '#options' => $address_format->getFields(),
      '#default_value' => $address_format->getUppercaseFields(),
    );
    $form['postalCodePattern'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Postal code pattern'),
      '#description' => $this->t('Regular expression used to validate postal codes.'),
      '#default_value' => $address_format->getPostalCodePattern(),
    );
    $form['postalCodePrefix'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Postal code prefix'),
      '#description' => $this->t('Defines the postal prefix which is added to all postal codes.'),
      '#default_value' => $address_format->getPostalCodePrefix(),
      '#size' => 5,
    );

    $form['postalCodeType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Postal code type'),
      '#default_value' => $address_format->getPostalCodeType(),
      '#options' => $address_format->getPostalCodeTypes(),
      '#empty_value' => '',
    );
    $form['dependentLocalityType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Dependent locality type'),
      '#default_value' => $address_format->getDependentLocalityType(),
      '#options' => $address_format->getDependentLocalityTypes(),
      '#empty_value' => '',
    );
    $form['localityType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Locality type'),
      '#default_value' => $address_format->getLocalityType(),
      '#options' => $address_format->getLocalityTypes(),
      '#empty_value' => '',
    );
    $form['administrativeAreaType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Administrative area type'),
      '#default_value' => $address_format->getAdministrativeAreaType(),
      '#options' => $address_format->getAdministrativeAreaTypes(),
      '#empty_value' => '',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $address_format = $this->entity;

    try {
      $address_format->save();
      drupal_set_message($this->t('Saved the %label address format.', array(
        '%label' => $address_format->label(),
      )));
      $form_state->setRedirect('entity.address_format.list');
    }
    catch (\Exception $e) {
      drupal_set_message($this->t('The %label address_format was not saved.', array('%label' => $address_format->label())), 'error');
      $this->logger('addressfield')->error($e);
      $form_state->setRebuild();
    }
  }

}
