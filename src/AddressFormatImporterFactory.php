<?php

/**
 * @file
 * Contains \Drupal\address\AddressFormatImporterFactory.
 */

namespace Drupal\address;

use \Drupal\Core\Entity\EntityManagerInterface;

class AddressFormatImporterFactory implements AddressFormatImporterFactoryInterface {

  /**
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  public function __construct(EntityManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($address_formats_folder = NULL) {
    return new AddressFormatImporter($this->entityManager->getStorage('address_format'), $address_formats_folder);
  }

}
