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

}
