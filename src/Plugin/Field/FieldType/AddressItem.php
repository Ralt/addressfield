<?php

/**
 * @file
 * Contains \Drupal\addressfield\Plugin\Field\FieldType\AddressItem.
 */

namespace Drupal\addressfield\Plugin\Field\FieldType;

use Drupal\addressfield\AddressItemInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use CommerceGuys\Addressing\Formatter\PostalFormatter;
use CommerceGuys\Addressing\Provider\DataProvider;
use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use CommerceGuys\Addressing\Model\Address;

/**
 * Plugin implementation of the 'address' field type.
 *
 * @FieldType(
 *   id = "address",
 *   label = @Translation("Address"),
 *   description = @Translation("Address in multinational formats"),
 *   default_widget = "address_default",
 *   default_formatter = "address_postal"
 * )
 */
class AddressItem extends FieldItemBase implements AddressItemInterface {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'local' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'countryCode' => array(
          'type' => 'varchar',
          'length' => 256,
          'not null' => TRUE,
        ),
        'administrativeArea' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'locality' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'dependentLocality' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'postalCode' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'sortingCode' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'addressLine1' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'addressLine2' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'organization' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
        'recipient' => array(
          'type' => 'varchar',
          'length' => 256,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['local'] = DataDefinition::create('string')
      ->setLabel(t('The locale'));
    $properties['countryCode'] = DataDefinition::create('string')
      ->setLabel(t('The two-letter country code.'));
    $properties['administrativeArea'] = DataDefinition::create('string')
      ->setLabel(t('The top-level administrative subdivision of the country.'));
    $properties['locality'] = DataDefinition::create('string')
      ->setLabel(t('The locality (i.e. city).'));
    $properties['dependentLocality'] = DataDefinition::create('string')
      ->setLabel(t('The dependent locality (i.e. neighbourhood).'));
    $properties['postalCode'] = DataDefinition::create('string')
      ->setLabel(t('The postal code.'));
    $properties['sortingCode'] = DataDefinition::create('string')
      ->setLabel(t('The sorting code.'));
    $properties['addressLine1'] = DataDefinition::create('string')
      ->setLabel(t('The first line of the address block.'));
    $properties['addressLine2'] = DataDefinition::create('string')
      ->setLabel(t('The second line of the address block.'));
    $properties['organization'] = DataDefinition::create('string')
      ->setLabel(t('The organization'));
    $properties['recipient'] = DataDefinition::create('string')
      ->setLabel(t('The recipient.'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('countryCode')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function getAddress() {
    $address = new Address();
    $address->setLocale($this->get('local')->getValue());
    $address->setCountryCode($this->get('countryCode')->getValue());
    $address->setAdministrativeArea($this->get('administrativeArea')->getValue());
    $address->setLocality($this->get('locality')->getValue());
    $address->setDependentLocality($this->get('dependentLocality')->getValue());
    $address->setPostalCode($this->get('postalCode')->getValue());
    $address->setSortingCode($this->get('sortingCode')->getValue());
    $address->setAddressLine1($this->get('addressLine1')->getValue());
    $address->setAddressLine2($this->get('addressLine2')->getValue());
    $address->setRecipient($this->get('organization')->getValue());
    $address->setOrganization($this->get('recipient')->getValue());

    return $address;
  }

  /**
   * {@inheritdoc}
   */
  public function getPostal($originCountryCode, $originLocale = NULL) {
    $dataProvider = new DataProvider();
    $formatter = new PostalFormatter($dataProvider);
    if (empty($originLocale)) {
      $language = \Drupal::languageManager()->getCurrentLanguage();
      $originLocale = $language->getId();
    }

    return $formatter->format($this->getAddress(), $originCountryCode, $originLocale);
  }
}
