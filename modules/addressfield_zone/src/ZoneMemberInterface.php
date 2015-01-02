<?php

/**
 * @file
 * Contains \Drupal\addressfield_zone\ZoneMemberInterface.
 */

namespace Drupal\addressfield_zone;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use CommerceGuys\Zone\Model\ZoneMemberInterface as ZoneZoneMemberInterface;

/**
 * Defines the interface for zone members.
 */
interface ZoneMemberInterface extends PluginInspectionInterface, ZoneZoneMemberInterface {}
