<?php

/**
 * @file
 * Contains \Drupal\addressfield\Entity\Query\SubdivisionQueryFactory.
 */

namespace Drupal\addressfield\Entity\Query;

use Drupal\addressfield\SubdivisionRecordStorageInterface;
use Drupal\Core\Config\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Query\QueryBase;

/**
 * Provides a factory for creating subdivision query objects.
 */
class SubdivisionQueryFactory extends QueryFactory {

  /**
   * The record storage.
   *
   * @var \Drupal\addressfield\SubdivisionRecordStorageInterface
   */
  protected $recordStorage;

  /**
   * Constructs a SubdivisionQueryFactory object.
   *
   * @param \Drupal\addressfield\SubdivisionRecordStorageInterface $record_storagey
   *   The record storage used by the subdivision query.
   */
  public function __construct(SubdivisionRecordStorageInterface $record_storage) {
    $this->recordStorage = $record_storage;
    $this->namespaces = QueryBase::getNamespaces($this);
  }

  /**
   * {@inheritdoc}
   */
  public function get(EntityTypeInterface $entity_type, $conjunction) {
    return new SubdivisionQuery($entity_type, $conjunction, $this->recordStorage, $this->namespaces);
  }

}
