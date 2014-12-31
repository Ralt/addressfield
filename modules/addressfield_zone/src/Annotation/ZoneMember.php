<?php

/**
 * @file
 * Contains \Drupal\addressfield_zone\Annotation\ZoneMember.
 */

namespace Drupal\addressfield_zone\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a zone member annotation object.
 *
 * Plugin Namespace: Plugin\ZoneMember
 *
 * @Annotation
 */
class ZoneMember extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the zone member.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

}
