<?php

/**
 * @file
 * Contains \Drupal\addressfield\AddressFormatImporter.
 */

namespace Drupal\addressfield;

use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\addressfield\AddressFormatImporterInterface;
use Drupal\language\ConfigurableLanguageManagerInterface;


class AddressFormatImporter implements AddressFormatImporterInterface {

  /**
   * The address format manager.
   *
   * @var \CommerceGuys\Addressing\Repository\AddressFormatRepositoryInterface
   */
  protected $addressFormatRepository;

  /**
   * The address format storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $addressFormatStorage;

  /**
   * The configurable language manager.
   *
   * @var \Drupal\language\ConfigurableLanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new CurrencyImporter.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(EntityManagerInterface $entity_manager, LanguageManagerInterface $language_manager) {
    $this->addressFormatStorage = $entity_manager->getStorage('address_format');
    $this->languageManager = $language_manager;
    $this->addressFormatRepository = new AddressFormatRepository();
  }

  /**
   * {@inheritdoc}
   */
  public function getImportableAddressFormats() {
    $language = $this->languageManager->getCurrentLanguage();
    $importable_address_formats = $this->addressFormatRepository->getAll($language->getId());
    $imported_address_formats = $this->addressFormatStorage->loadMultiple();

    // Remove any already imported currencies.
    foreach ($imported_address_formats as $address_format) {
      if (isset($importable_address_formats[$address_format->id()])) {
        unset($importable_address_formats[$address_format->id()]);
      }
    }

    return $importable_address_formats;
  }

  /**
   * {@inheritdoc}
   */
  public function importAddressFormat($country_code) {
    if ($this->addressFormatStorage->load($country_code)) {
      return FALSE;
    }
    $language = $this->languageManager->getDefaultLanguage();
    $address_format = $this->getAddressFormat($country_code, $language);

    $values = array(
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
    );
    $entity = $this->addressFormatStorage->create($values);

    return $entity;
  }

  /**
   * Get a single currency.
   *
   * @param string $country_code
   *   The country code.
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language.
   *
   * @return CommerceGuys\Addressing\Model\AddressFormat
   *   Returns \CommerceGuys\Addressing\Model\AddressFormat
   */
  protected function getAddressFormat($country_code, LanguageInterface $language) {
    return $this->addressFormatRepository->get($country_code, $language->getId());
  }
}
