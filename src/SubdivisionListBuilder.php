<?php

/**
 * @file
 * Contains \Drupal\addressfield\SubdivisionListBuilder.
 */

namespace Drupal\addressfield;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Subdivisions.
 */
class SubdivisionListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['countryCode'] = $this->t('Country Code');
    $header['code'] = $this->t('Subdivision Code');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['countryCode'] = $entity->getCountryCode();
    $row['code'] = $entity->getCode();
    $row['name'] = $this->getLabel($entity);
    return $row + parent::buildRow($entity);
  }

}
