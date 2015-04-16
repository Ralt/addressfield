<?php

/**
 * @file
 * Contains \Drupal\address\AddressFormatImporter.
 */

namespace Drupal\address;

use \Drupal\Core\Entity\EntityManagerInterface;
use \Drupal\Core\Entity\EntityStorageInterface;
use \CommerceGuys\Addressing\Repository\AddressFormatRepository;
use \CommerceGuys\Addressing\Model\AddressFormatInterface;

class AddressFormatImporter implements AddressFormatImporterInterface {

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $addressFormatStorage;

  /**
   * The address format manager.
   *
   * @var \CommerceGuys\Addressing\Repository\AddressFormatRepositoryInterface
   */
  protected $addressFormatRepository;

  public function __construct(EntityStorageInterface $storage, $address_formats_folder) {
    $this->addressFormatStorage = $storage;
    $this->addressFormatRepository = new AddressFormatRepository($address_formats_folder);
  }

  /**
   * {@inheritdoc}
   */
  public function import() {
    $operations = [];
    foreach (array_chunk($this->addressFormatRepository->getAll(), ADDRESS_BATCH_SIZE) as $address_formats) {
      $operations[] = [
        [get_class($this), 'importAddressFormatBatch'],
        [$address_formats],
      ];
    }

    batch_set([
      'title' => t('Installing address formats'),
      'init_message' => t('Preparing to import address formats'),
      'operations' => $operations,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getImportableAddressFormats() {
    $imported = $this->addressFormatStorage->loadMultiple();
    $importable = $this->addressFormatRepository->getAll();

    // Remove any already imported address format.
    foreach ($imported as $address_format) {
      if (isset($importable[$address_format->id()])) {
        unset($importable[$address_format->id()]);
      }
    }

    return $importable;
  }

  /**
   * {@inheritdoc}
   */
  public function createAddressFormat($country_code) {
    return self::mapAddressFormatEntity(
      $this->addressFormatStorage,
      $this->addressFormatRepository->get($country_code)
    );
  }

  /**
   * Batch callback for each chunk of address formats.
   *
   * @param array $address_formats
   *   The chunk of address formats.
   * @param object &$context
   *   The context of the batch.
   */
  public static function importAddressFormatBatch($address_formats, &$context) {
    $storage = \Drupal::service('entity.manager')->getStorage('address_format');
    foreach ($address_formats as $address_format) {
      self::importAddressFormat($storage, $address_format);
    }

    $context['finished'] = 1;
  }

  /**
   * Imports a single address format.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The address format storage.
   * @param \CommerceGuys\Addressing\Model\AddressFormatInterface $address_format
   *   The address format to import.
   */
  public static function importAddressFormat(EntityStorageInterface $storage, AddressFormatInterface $address_format) {
    if ($storage->load($address_format->getCountryCode())) {
      return;
    }

    self::mapAddressFormatEntity($storage, $address_format)->save();
  }

  /**
   * Maps an AddressFormat with an address_format entity and returns the entity.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The address format storage.
   * @param \CommerceGuys\Addressing\Model\AddressFormatInterface $address_format
   *   The address format to map.
   */
  protected static function mapAddressFormatEntity(EntityStorageInterface $storage, AddressFormatInterface $address_format) {
    $values = [
      'countryCode' => $address_format->getCountryCode(),
      'format' => $address_format->getFormat(),
      'requiredFields' => $address_format->getRequiredFields(),
      'uppercaseFields' => $address_format->getUppercaseFields(),
      'administrativeAreaType' => $address_format->getAdministrativeAreaType(),
      'localityType' => $address_format->getLocalityType(),
      'dependentLocalityType' => $address_format->getDependentLocalityType(),
      'postalCodeType' => $address_format->getPostalCodeType(),
      'postalCodePattern' => $address_format->getPostalCodePattern(),
      'postalCodePrefix' => $address_format->getPostalCodePrefix(),
    ];

    return $storage->create($values);
  }

}
