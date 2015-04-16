<?php

/**
 * @file
 * Contains \Drupal\address\AddressFormatImporterFactory.
 */

namespace Drupal\address;

class AddressFormatImporterFactory implements AddressFormatImporterFactoryInterface {

  /**
   * {@inheritdoc}
   */
  public function createInstance($address_formats_folder = NULL) {
    return new AddressFormatImporter($address_formats_folder);
  }

}
