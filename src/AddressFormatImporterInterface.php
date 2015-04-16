<?php

/**
 * @file
 * Contains \Drupal\address\AddressFormatImporterInterface.
 */

namespace Drupal\address;

/**
 * Defines an address format importer.
 */
interface AddressFormatImporterInterface {

  /**
   * Imports all the address formats defined in a folder.
   */
  public function import();

  /**
   * Gets the list of not imported but available formats.
   *
   * @return array
   *   The list of importable address formats.
   */
  public function getImportableAddressFormats();

  /**
   * Creates an AddressFormat.
   *
   * @param string $countryCode
   *   The country code of the address format.
   */
  public function createAddressFormat($countryCode);

}
