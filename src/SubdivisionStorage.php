<?php

/**
 * @file
 * Contains \Drupal\addressfield\SubdivisionStorage.
 */

namespace Drupal\addressfield;

use Drupal\addressfield\Entity\AddressFormat;
use Drupal\Core\Config\Entity\ConfigEntityStorage;

class SubdivisionStorage extends ConfigEntityStorage {

  /**
   * {@inheritdoc}
   */
  public function loadByProperties(array $values = array()) {
    // @todo Override the config query service for this entity type instead.
    // This will allow us to not ignore other properties.
    if (!empty($values['parentId'])) {
      $parent = $this->load($values['parentId']);
      return $parent->getChildren();
    }
    elseif (!empty($values['countryCode'])) {
      $format = AddressFormat::load($values['countryCode']);
      return $format->getSubdivisions();
    }
  }

}
