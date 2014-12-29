<?php

/**
 * @file
 * Contains \Drupal\addressfield\AddressFormatImporterInterface.
 */

namespace Drupal\addressfield;

/**
 * Defines an address format importer.
 */
interface AddressFormatImporterInterface {

  /**
   * Returns all importable address formats.
   *
   * @return \CommerceGuys\Addressing\Model\AddressFormatInterface[]
   *    Array of importable address formats.
   */
  public function getImportableAddressFormats();

  /**
   * Creates a new address format object for the given country code.
   *
   * @param string $country_code
   *   The country code.
   *
   * @return \Drupal\addressfield\Entity\AddressFormat | bool
   *    The new address format or false if the address format is already imported.
   */
  public function importAddressFormat($country_code);
}
