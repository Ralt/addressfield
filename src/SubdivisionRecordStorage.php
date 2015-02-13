<?php

/**
 * @file
 * Contains \Drupal\addressfield\SubdivisionStorage.
 */

namespace Drupal\addressfield;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Component\Utility\String;

/**
 * Storage for subdivision storage records.
 *
 * Storage records of other config entities map 1-1 to config objects.
 * Since there are too many subdivisions (> 12 000), creating a config object
 * for each one would be impractical. Instead, their storage records are stored
 * grouped by parent ID. For example, 'US_CA' and 'US_DC' are both stored in
 * the addressfield.subdivisions.US config object. This reduces the number
 * of needed config objects significantly (to around 520).
 */
class SubdivisionRecordStorage implements SubdivisionRecordStorageInterface {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a SubdivisionRecordStorage object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Gets the root key of the config object.
   *
   * A root key is needed because core doesn't support having a sequence at
   * the root level (#2248709).
   *
   * @return string
   *   The root key.
   */
  protected function getRootKey() {
    return 'subdivisions';
  }

  /**
   * Returns the prefix used to create the config name.
   *
   * @return string
   *   The prefix.
   */
  protected function getPrefix() {
    return 'addressfield.subdivisions.';
  }

  /**
   * Gets the name of the config object for the provided ID.
   *
   * The config object name is constructed from the prefix and the parent ID.
   * The parent ID is constructed by taking n-1 segments of the original ID.
   * E.g. for "BR_AL_64b095" the parent ID is "BR_AL", and the config name
   * is "addressfield.subdivisions.BR_AL".
   *
   * @param string $id
   *   The ID.
   *
   * @return string
   *   The config name, or NULL if the provided ID is malformed.
   */
  protected function getConfigName($id) {
    $parent_id = NULL;
    $id_parts = explode('_', $id);
    if (count($id_parts) > 1) {
      array_pop($id_parts);
      $parent_id = $this->getPrefix() . implode('_', $id_parts);
    }

    return $parent_id;
  }

  /**
   * Gets the names of the config objects for the provided IDs.
   *
   * @param array $ids
   *   The IDs.
   *
   * @return array
   *   An array in the $config_name => $ids format.
   */
  protected function getConfigNames($ids) {
    $config_names = array();
    foreach ($ids as $id) {
      // Gather the needed config names. Ignore any malformed id.
      $config_name = $this->getConfigName($id);
      if ($config_name) {
        $config_names[$config_name][] = $id;
      }
    }

    return $config_names;
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $ids, $override_free = FALSE) {
    $config_names = $this->getConfigNames($ids);
    $root_key = $this->getRootKey();
    $records = array();
    foreach ($this->configFactory->loadMultiple(array_keys($config_names)) as $config) {
      $data = $override_free ? $config->getOriginal($root_key, FALSE) : $config->get($root_key);
      $loaded_ids = array_keys($data);
      $needed_ids = array_intersect($ids, $loaded_ids);
      $records += array_intersect_key($data, array_flip($needed_ids));
    }

    return $records;
  }

  /**
   * {@inheritdoc}
   */
  public function loadChildren(array $parent_ids, $override_free = FALSE) {
    $prefix = $this->getPrefix();
    $config_names = array();
    foreach ($parent_ids as $parent_id) {
      $config_names[] = $prefix . $parent_id;
    }
    $root_key = $this->getRootKey();
    $records = array();
    foreach ($this->configFactory->loadMultiple($config_names) as $config) {
      $records += $override_free ? $config->getOriginal($root_key, FALSE) : $config->get($root_key);
    }

    return $records;
  }

  /**
   * {@inheritdoc}
   */
  public function exists($id) {
    $config_name = $this->getConfigName($id);
    if (!$config_name) {
      // Malformed id.
      return FALSE;
    }
    $configs = $this->configFactory->loadMultiple(array($config_name));
    if (empty($configs)) {
      return FALSE;
    }
    $config = reset($configs);
    $data = $config->get($root_key . '.' . $id);

    return !empty($data);
  }

  /**
   * {@inheritdoc}
   */
  public function delete(array $ids) {
    $root_key = $this->getRootKey();
    foreach ($this->getConfigNames($ids) as $config_name => $grouped_ids) {
      $config = $this->configFactory->getEditable($config_name);
      foreach ($grouped_ids as $id) {
        $config->clear($root_key . '.' . $id);
      }

      // Delete the config object if it contains no other records.
      $data = $config->get($root_key);
      if (empty($data)) {
        $config->delete();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save($id, array $record) {
    // Make sure that the ID can be mapped to a config name.
    $config_name = $this->getConfigName($id);
    if (!$config_name) {
      throw new \InvalidArgumentException(String::format('The subdivision ID "@id" is malformed.', array('@id' => $id)));
    }

    $root_key = $this->getRootKey();
    $config = $this->configFactory->getEditable($config_name);
    $config->set($root_key . '.' . $id, $record);
    $config->save();
  }

  /**
   * {@inheritdoc}
   */
  public function saveMultiple(array $records) {
    $ids = array_keys($records);
    $root_key = $this->getRootKey();
    foreach ($this->getConfigNames($ids) as $config_name => $grouped_ids) {
      $config = $this->configFactory->getEditable($config_name);
      foreach ($grouped_ids as $id) {
        $config->set($root_key . '.' . $id, $records[$id]);
      }
      $config->save();
    }
  }

}
