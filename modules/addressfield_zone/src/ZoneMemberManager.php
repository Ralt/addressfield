<?php

/**
 * @file
 * Contains \Drupal\addressfield_zone\ZoneMemberManager/
 */

namespace Drupal\addressfield_zone;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages zone member plugins.
 */
class ZoneMemberManager extends DefaultPluginManager {

  /**
   * Constructs a new ZoneMemberManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/ZoneMember', $namespaces, $module_handler, 'Drupal\addressfield_zone\ZoneMemberInterface', 'Drupal\addressfield_zone\Annotation\ZoneMember');

    $this->alterInfo('addressfield_zone_member_info');
    $this->setCacheBackend($cache_backend, 'addressfield_zone_member_plugins');
  }

}
