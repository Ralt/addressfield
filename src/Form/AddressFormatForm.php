<?php

/**
 * @file
 * Contains Drupal\addressfield\Form\AddressFormatForm.
 */

namespace Drupal\addressfield\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Locale\CountryManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddressFormatForm extends EntityForm {

  /**
   * The address format storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * Creates an AddressFormatForm instance.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The address format storage.
   */
  public function __construct(EntityStorageInterface $storage, CountryManagerInterface $country_manager) {
    $this->storage = $storage;
    $this->countryManager = $country_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');

    return new static($entity_manager->getStorage('address_format'), $container->get('country_manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $address_format = $this->entity;

    $country_code = $address_format->getCountryCode();
    if ($country_code == 'ZZ') {
      $form['countryCode'] = array(
        '#type' => 'item',
        '#title' => $this->t('Country'),
        '#markup' => $this->t('Generic'),
      );
    }
    else {
      $form['countryCode'] = array(
        '#type' => 'select',
        '#title' => $this->t('Country'),
        '#default_value' => $address_format->getCountryCode(),
        '#required' => TRUE,
        '#options' => $this->countryManager->getList(),
        '#disabled' => !$address_format->isNew(),
      );
    }

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
      '#description' => t('Uppercased on envelopes to faciliate automatic post handling.'),
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
      '#description' => $this->t('Added to postal codes during formatting.'),
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
  public function validate(array $form, FormStateInterface $form_state) {
    parent::validate($form, $form_state);

    // Disallow adding an address format for a country that already has one.
    if ($this->entity->isNew()) {
      $country = $form_state->getValue('countryCode');
      if ($this->storage->load($country)) {
        $form_state->setErrorByName('countryCode', $this->t('The selected country already has an address format.'));
      }
    }

    // Require the matching type field for the fields specified in the format.
    $format = $form_state->getValue('format');
    $requirements = array(
      '%postal_code' => 'postalCodeType',
      '%dependent_locality' => 'dependentLocalityType',
      '%locality' => 'localityType',
      '%administrative_area' => 'administrativeAreaType',
    );
    foreach ($requirements as $token => $required_field) {
      if (strpos($format, $token) !== FALSE && !$form_state->getValue($required_field)) {
        $title = $form[$required_field]['#title'];
        $form_state->setErrorByName($required_field, $this->t('%title is required.', array('%title' => $title)));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $address_format = $this->entity;
    $address_format->save();
    drupal_set_message($this->t('Saved the %label address format.', array(
      '%label' => $address_format->label(),
    )));
    $form_state->setRedirectUrl($address_format->urlInfo('collection'));
  }

}
