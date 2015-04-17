<?php

/**
 * @file
 * Contains \Drupal\address\AddressFormatImporter.
 */

namespace Drupal\address;

use Drupal\Core\Entity\EntityStorageInterface;
use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use CommerceGuys\Addressing\Model\AddressFormatInterface;

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

  public function __construct(EntityStorageInterface $storage, $addressFormatsFolder) {
    $this->addressFormatStorage = $storage;
    $this->addressFormatRepository = new AddressFormatRepository($addressFormatsFolder);
  }

  /**
   * {@inheritdoc}
   */
  public function import() {
    $operations = [];
    foreach (array_chunk($this->addressFormatRepository->getAll(), ADDRESS_BATCH_SIZE) as $addressFormats) {
      $operations[] = [
        [get_class($this), 'importAddressFormatBatch'],
        [$addressFormats],
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
    foreach ($imported as $addressFormat) {
      if (isset($importable[$addressFormat->id()])) {
        unset($importable[$addressFormat->id()]);
      }
    }

    return $importable;
  }

  /**
   * {@inheritdoc}
   */
  public function createAddressFormat($countryCode) {
    return self::mapAddressFormatEntity(
      $this->addressFormatStorage,
      $this->addressFormatRepository->get($countryCode)
    );
  }

  /**
   * Batch callback for each chunk of address formats.
   *
   * @param array $addressFormats
   *   The chunk of address formats.
   * @param object &$context
   *   The context of the batch.
   */
  public static function importAddressFormatBatch($addressFormats, &$context) {
    $storage = \Drupal::service('entity.manager')->getStorage('address_format');
    foreach ($addressFormats as $addressFormat) {
      self::importAddressFormat($storage, $addressFormat);
    }

    $context['finished'] = 1;
  }

  /**
   * Imports a single address format.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The address format storage.
   * @param \CommerceGuys\Addressing\Model\AddressFormatInterface $addressFormat
   *   The address format to import.
   */
  public static function importAddressFormat(EntityStorageInterface $storage, AddressFormatInterface $addressFormat) {
    if ($storage->load($addressFormat->getCountryCode())) {
      return;
    }

    self::mapAddressFormatEntity($storage, $addressFormat)->save();
  }

  /**
   * Maps an AddressFormat with an address_format entity and returns the entity.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The address format storage.
   * @param \CommerceGuys\Addressing\Model\AddressFormatInterface $addressFormat
   *   The address format to map.
   */
  protected static function mapAddressFormatEntity(EntityStorageInterface $storage, AddressFormatInterface $addressFormat) {
    $values = [
      'countryCode' => $addressFormat->getCountryCode(),
      'format' => $addressFormat->getFormat(),
      'requiredFields' => $addressFormat->getRequiredFields(),
      'uppercaseFields' => $addressFormat->getUppercaseFields(),
      'administrativeAreaType' => $addressFormat->getAdministrativeAreaType(),
      'localityType' => $addressFormat->getLocalityType(),
      'dependentLocalityType' => $addressFormat->getDependentLocalityType(),
      'postalCodeType' => $addressFormat->getPostalCodeType(),
      'postalCodePattern' => $addressFormat->getPostalCodePattern(),
      'postalCodePrefix' => $addressFormat->getPostalCodePrefix(),
    ];

    return $storage->create($values);
  }

}
