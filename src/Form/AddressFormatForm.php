<?php

/**
 * @file
 * Contains Drupal\addressfield\Form\AddressFormatForm.
 */

namespace Drupal\addressfield\Form;

use CommerceGuys\Intl\Country\CountryRepository;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddressFormatForm extends EntityForm {

  /**
   * The currency storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $addressFormatStorage;

  /**
   * Creates a AddressFormatForm instance.
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

    return new static($entity_manager->getStorage('commerce_address_format'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $address_format = $this->entity;

    // Reads the country definitions from resources/country.
    $countryRepository = new CountryRepository;
    $countries = $countryRepository->getAll();
    $country_options = array();
    foreach ($countries as $country) {
      $country_options[$country->getCountryCode()] = $this->t($country->getName());
    }

    $form['countryCode'] = array(
      '#type' => 'select',
      '#title' => $this->t('Country code'),
      '#default_value' => $address_format->getCountryCode(),
      '#required' => TRUE,
      '#options' => $country_options,
    );

    $form['format'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Format'),
      '#description' => $this->t('Write how the format of the address should be, available tokens: @tokens', array('@tokens' => implode(', ', $address_format->getFieldsTokens()))),
      '#default_value' => $address_format->getFormat(),
      '#required' => TRUE,
    );

    $form['requiredFields'] = array(
      '#type' => 'select',
      '#title' => t('Required fields'),
      '#multiple' => TRUE,
      '#description' => t('Select which fields should be required.'),
      '#options' => $address_format->getFields(),
      '#default_value' => $address_format->getRequiredFields(),
    );

    $form['uppercaseFields'] = array(
      '#type' => 'select',
      '#title' => t('Uppercase fields'),
      '#multiple' => TRUE,
      '#description' => t('Select which fields needs to be uppercased for automatic post handling.'),
      '#options' => $address_format->getFields(),
      '#default_value' => $address_format->getUppercaseFields(),
    );

    $form['administrativeAreaType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Administrative area type'),
      '#description' => $this->t('Used for presenting the correct label to the end-user.'),
      '#default_value' => $address_format->getAdministrativeAreaType(),
      '#options' => $address_format->getAdministrativeAreaTypes(),
      '#required' => TRUE,
    );

    $form['localityType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Locality type'),
      '#description' => $this->t('Used for presenting the correct label to the end-user.'),
      '#default_value' => $address_format->getLocalityType(),
      '#options' => $address_format->getLocalityTypes(),
      '#required' => TRUE,
    );

    $form['dependentLocalityType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Dependent locality type'),
      '#description' => $this->t('Used for presenting the correct label to the end-user.'),
      '#default_value' => $address_format->getDependentLocalityType(),
      '#options' => $address_format->getDependentLocalityTypes(),
      '#required' => TRUE,
    );

    $form['postalCodeType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Dependent locality type'),
      '#description' => $this->t('Used for presenting the correct label to the end-user.'),
      '#default_value' => $address_format->getpostalCodeType(),
      '#options' => $address_format->getPostalCodeTypes(),
      '#required' => TRUE,
    );

    $form['postalCodePattern'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Postal code pattern'),
      '#description' => $this->t('Defines the postal code pattern that all postal codes must uphold.'),
      '#default_value' => $address_format->getPostalCodePattern(),
    );

    $form['postalCodePrefix'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Postal code prefix'),
      '#description' => $this->t('Defines the postal prefix which is added to all postal codes.'),
      '#default_value' => $address_format->getPostalCodePrefix(),
      '#size' => 5,
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
