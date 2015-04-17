<?php

/**
 * @file
 * Contains \Drupal\address\AddressFormatImporterFactory.
 */

namespace Drupal\address;

use Drupal\Core\Entity\EntityManagerInterface;

class AddressFormatImporterFactory implements AddressFormatImporterFactoryInterface {

  /**
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  public function __construct(EntityManagerInterface $entityManager) {
    $this->entityManager = $entityManager;
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($addressFormatsFolder = NULL) {
    return new AddressFormatImporter($this->entityManager->getStorage('address_format'), $addressFormatsFolder);
  }

}
