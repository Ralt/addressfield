<?php

/**
 * @file
 * Contains \Drupal\addressfield\SubdivisionImporter.
 */

namespace Drupal\addressfield;

use CommerceGuys\Addressing\Repository\SubdivisionRepository;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\addressfield\SubdivisionImporterInterface;
use Drupal\language\ConfigurableLanguageManagerInterface;


class SubdivisionImporter implements SubdivisionImporterInterface {

  /**
   * The address format manager.
   *
   * @var \CommerceGuys\Addressing\Repository\AddressFormatRepositoryInterface
   */
  protected $subdivisionRepository;

  /**
   * The address format storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $subdivisionStorage;

  /**
   * The configurable language manager.
   *
   * @var \Drupal\language\ConfigurableLanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new SubdivisionImporter.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(EntityManagerInterface $entity_manager, LanguageManagerInterface $language_manager) {
    $this->subdivisionStorage = $entity_manager->getStorage('subdivision');
    $this->languageManager = $language_manager;
    $this->subdivisionRepository = new SubdivisionRepository();
  }

  /**
   * {@inheritdoc}
   */
  public function getImportableSubdivisions($country_code) {
    $language = $this->languageManager->getCurrentLanguage();
    $importable_subdivisions = $this->subdivisionRepository->getAll($country_code, 0, $language->getId());
    $imported_subdivisions = $this->subdivisionStorage->loadMultiple();

    // Remove any already imported currencies.
    foreach ($imported_subdivisions as $subdivision) {
      if (isset($importable_subdivisions[$subdivision->id()])) {
        unset($importable_subdivisions[$subdivision->id()]);
      }
    }

    return $importable_subdivisions;
  }

  /**
   * {@inheritdoc}
   */
  public function importSubdivision($id) {
    if ($this->subdivisionStorage->load($id)) {
      return FALSE;
    }
    $language = $this->languageManager->getDefaultLanguage();
    $subdivision = $this->getSubdivision($id, $language);

    print_r($subdivision);

    $values = array(
      'id' => $subdivision->getId(),
      'name' => $subdivision->getName(),
      'code' => $subdivision->getCode(),
      'countryCode' => $subdivision->getCountryCode(),
    );

    if ($parent = $subdivision->getParent()) {
      $values['parentId'] = $parent->getId();
    }
    $entity = $this->subdivisionStorage->create($values);

    return $entity;
  }

  /**
   * Get a single currency.
   *
   * @param string $id
   *   The id for subdivision.
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language.
   *
   * @return CommerceGuys\Addressing\Model\AddressFormat
   *   Returns \CommerceGuys\Addressing\Model\AddressFormat
   */
  protected function getSubdivision($id, LanguageInterface $language) {
    return $this->subdivisionRepository->get($id, $language->getId());
  }
}
