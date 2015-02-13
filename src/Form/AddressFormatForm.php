<?php

/**
 * @file
 * Contains Drupal\address\Form\AddressFormatForm.
 */

namespace Drupal\address\Form;

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
  public function __construct(EntityStorageInterface $storage, CountryManagerInterface $countryManager) {
    $this->storage = $storage;
    $this->countryManager = $countryManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entityManager */
    $entityManager = $container->get('entity.manager');

    return new static($entityManager->getStorage('address_format'), $container->get('country_manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $formState) {
    $form = parent::form($form, $formState);
    $addressFormat = $this->entity;

    $countryCode = $addressFormat->getCountryCode();
    if ($countryCode == 'ZZ') {
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
        '#default_value' => $addressFormat->getCountryCode(),
        '#required' => TRUE,
        '#options' => $this->countryManager->getList(),
        '#disabled' => !$addressFormat->isNew(),
      );
    }

    $form['format'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Format'),
      '#description' => $this->t('Available tokens: @tokens', array('@tokens' => implode(', ', $addressFormat->getFieldsTokens()))),
      '#default_value' => $addressFormat->getFormat(),
      '#required' => TRUE,
    );
    $form['requiredFields'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Required fields'),
      '#options' => $addressFormat->getFields(),
      '#default_value' => $addressFormat->getRequiredFields(),
    );
    $form['uppercaseFields'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Uppercase fields'),
      '#description' => t('Uppercased on envelopes to faciliate automatic post handling.'),
      '#options' => $addressFormat->getFields(),
      '#default_value' => $addressFormat->getUppercaseFields(),
    );
    $form['postalCodePattern'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Postal code pattern'),
      '#description' => $this->t('Regular expression used to validate postal codes.'),
      '#default_value' => $addressFormat->getPostalCodePattern(),
    );
    $form['postalCodePrefix'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Postal code prefix'),
      '#description' => $this->t('Added to postal codes during formatting.'),
      '#default_value' => $addressFormat->getPostalCodePrefix(),
      '#size' => 5,
    );

    $form['postalCodeType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Postal code type'),
      '#default_value' => $addressFormat->getPostalCodeType(),
      '#options' => $addressFormat->getPostalCodeTypes(),
      '#empty_value' => '',
    );
    $form['dependentLocalityType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Dependent locality type'),
      '#default_value' => $addressFormat->getDependentLocalityType(),
      '#options' => $addressFormat->getDependentLocalityTypes(),
      '#empty_value' => '',
    );
    $form['localityType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Locality type'),
      '#default_value' => $addressFormat->getLocalityType(),
      '#options' => $addressFormat->getLocalityTypes(),
      '#empty_value' => '',
    );
    $form['administrativeAreaType'] = array(
      '#type' => 'select',
      '#title' => $this->t('Administrative area type'),
      '#default_value' => $addressFormat->getAdministrativeAreaType(),
      '#options' => $addressFormat->getAdministrativeAreaTypes(),
      '#empty_value' => '',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface $formState) {
    parent::validate($form, $formState);

    // Disallow adding an address format for a country that already has one.
    if ($this->entity->isNew()) {
      $country = $formState->getValue('countryCode');
      if ($this->storage->load($country)) {
        $formState->setErrorByName('countryCode', $this->t('The selected country already has an address format.'));
      }
    }

    // Require the matching type field for the fields specified in the format.
    $format = $formState->getValue('format');
    $requirements = array(
      '%postal_code' => 'postalCodeType',
      '%dependent_locality' => 'dependentLocalityType',
      '%locality' => 'localityType',
      '%administrative_area' => 'administrativeAreaType',
    );
    foreach ($requirements as $token => $requiredField) {
      if (strpos($format, $token) !== FALSE && !$formState->getValue($requiredField)) {
        $title = $form[$requiredField]['#title'];
        $formState->setErrorByName($requiredField, $this->t('%title is required.', array('%title' => $title)));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $formState) {
    $addressFormat = $this->entity;
    $addressFormat->save();
    drupal_set_message($this->t('Saved the %label address format.', array(
      '%label' => $addressFormat->label(),
    )));
    $formState->setRedirectUrl($addressFormat->urlInfo('collection'));
  }

}
