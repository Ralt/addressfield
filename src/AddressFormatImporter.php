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
   * The address format manager.
   *
   * @var \CommerceGuys\Addressing\Repository\AddressFormatRepositoryInterface
   */
  protected $addressFormatRepository;

  public function __construct($address_formats_folder) {
    $this->addressFormatRepository = new AddressFormatRepository($adress_formats_folder);
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
  protected static function importAddressFormat(EntityStorageInterface $storage, AddressFormatInterface $address_format) {
    if ($storage->load($address_format->getCountryCode())) {
      return;
    }

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

    $storage->create($values)->save();
  }

}
