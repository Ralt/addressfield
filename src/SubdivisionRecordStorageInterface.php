<?php

/**
 * @file
 * Contains \Drupal\addressfield\SubdivisionStorage.
 */

namespace Drupal\addressfield;

/**
 * Defines the interface for subdivision record storage classes.
 */
interface SubdivisionRecordStorageInterface {

  /**
   * Loads one or more storage records.
   *
   * @param array $ids
   *   An array of requested IDs.
   * @param bool $overide_free
   *   Whether the underlying configuration should be retrieved override free.
   *
   * @return array
   *   An array of loaded records.
   */
  public function loadMultiple(array $ids, $override_free = FALSE);

  /**
   * Loads all storage records under the provided parent IDs.
   *
   * @param array $parent_ids
   *   An array of parent IDs.
   * @param bool $overide_free
   *   Whether the underlying configuration should be retrieved override free.
   *
   * @return array
   *   An array of loaded records.
   */
  public function loadChildren(array $parent_ids, $override_free = FALSE);

  /**
   * Determines if a record already exists in storage.
   *
   * @param string $id
   *   The ID of the storage record.
   *
   * @return bool
   *   True if the record exists, false otherwise.
   */
  public function exists($id);

  /**
   * Deletes the storage records.
   *
   * @param array $ids
   *   An array of IDs to delete.
   */
  public function delete(array $ids);

  /**
   * Saves the storage record.
   *
   * @param string $id
   *   The ID of the storage record.
   * @param array $record
   *   The storage record.
   */
  public function save($id, array $record);

  /**
   * Saves multiple storage records.
   *
   * @param array $records
   *   An array of records, keyed by ID.
   */
  public function saveMultiple(array $records);

}
