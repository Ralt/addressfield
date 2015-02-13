<?php

/**
 * @file
 * Contains \Drupal\addressfield\Entity\Query\SubdivisionQuery.
 */

namespace Drupal\addressfield\Entity\Query;

use Drupal\addressfield\SubdivisionRecordStorageInterface;
use Drupal\Core\Config\Entity\Query\Query as ConfigQuery;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the entity query for subdivision entities.
 */
class SubdivisionQuery extends ConfigQuery {

  /**
   * The record storage.
   *
   * @var \Drupal\addressfield\SubdivisionRecordStorageInterface
   */
  protected $recordStorage;

  /**
   * Constructs a SubdivisionQuery object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param string $conjunction
   *   - AND: all of the conditions on the query need to match.
   *   - OR: at least one of the conditions on the query need to match.
   * @param \Drupal\addressfield\SubdivisionRecordStorageInterface $record_storage
   *   The record storage.
   * @param array $namespaces
   *   List of potential namespaces of the classes belonging to this query.
   */
  function __construct(EntityTypeInterface $entity_type, $conjunction, SubdivisionRecordStorageInterface $record_storage, array $namespaces) {
    $this->recordStorage = $record_storage;
    // Copy of QueryBase::__construct(), since we can't call the parent
    // __construct() because ConfigQuery needs the ConfigFactory param.
    $this->entityTypeId = $entity_type->id();
    $this->entityType = $entity_type;
    $this->conjunction = $conjunction;
    $this->namespaces = $namespaces;
    $this->condition = $this->conditionGroupFactory($conjunction);
  }

  /**
   * {@inheritdoc}
   */
  protected function loadRecords() {
    // There are too many subdivisions to load at once, so the query must
    // be restricted by a condition on id, parentId or countryCode.
    $ids = $this->getConditionValues('id');
    if ($ids) {
      return $this->recordStorage->loadMultiple($ids);
    }
    $parent_ids = $this->getConditionValues('parentId');
    if ($parent_ids) {
      return $this->recordStorage->loadChildren($parent_ids);
    }
    $country_codes = $this->getConditionValues('countryCode');
    if ($country_codes) {
      return $this->recordStorage->loadChildren($country_codes);
    }

    throw new \RuntimeException('The subdivision query must have a condition on id, parentId, or countryCode.');
  }

  /**
   * Gets all condition values for the provided key.
   *
   * @param string $key
   *   The key.
   *
   * @return array
   *   An array of values.
   */
  protected function getConditionValues($key) {
    if ($this->condition->getConjunction() != 'AND') {
      return array();
    }

    $values = array();
    foreach ($this->condition->conditions() as $condition) {
      if (is_string($condition['field']) && $condition['field'] == $key) {
        $operator = $condition['operator'] ?: (is_array($condition['value']) ? 'IN' : '=');
        if ($operator == '=') {
          $values = array($condition['value']);
          break;
        }
        elseif ($operator == 'IN') {
          $values = $condition['value'];
          break;
        }
      }
    }

    return $values;
  }

}
