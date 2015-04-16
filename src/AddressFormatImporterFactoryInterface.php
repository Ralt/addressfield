<?php

/**
 * @file
 * Contains \Drupal\address\AddressFormatImporterFactoryInterface.
 */

namespace Drupal\address;

/**
 * Defines an address format importer factory.
 */
interface AddressFormatImporterFactoryInterface {

  /**
   * Creates an instance of an AddressFormatImporter.
   *
   * @param string $addressFormatsFolder
   *   The address formats folder of definitions.
   *
   * @return \Drupal\address\AddressFormatImporterInterface
   *   An address format importer.
   */
  public function createInstance($addressFormatsFolder);

}
