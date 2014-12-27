<?php

/**
 * @file
 * Contains \Drupal\addressfield\AddressFormatListBuilder.
 */

namespace Drupal\addressfield;

use Drupal\Component\Utility\SafeMarkup;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of address formats.
 */
class AddressFormatListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['countryCode'] = $this->t('Country code');
    $header['format'] = array(
      'data' => $this->t('Format'),
      'class' => array(RESPONSIVE_PRIORITY_LOW),
    );
    $header['status'] = $this->t('Status');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['countryCode'] = $entity->id();
    $row['format']['data'] = SafeMarkup::set(str_replace("\n", '<br />', $entity->getFormat()));
    $row['status'] = $entity->status() ? t('Enabled') : t('Disabled');
    return $row + parent::buildRow($entity);
  }

}
