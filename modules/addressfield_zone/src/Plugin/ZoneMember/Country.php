<?php

/**
 * @file
 * Contains \Drupal\addressfield_zone\Plugin\ZoneMember\Country.
 */

namespace Drupal\addressfield_zone\Plugin\ZoneMember;

/**
 * Defines a country zone.
 *
 * @ZoneMember(
 *   id = "zonemember_country"
 *   label = @Translation("Country")
 * )
 */
class Country implements ZoneMemberInterface {
  public function getPluginId() {}
  public function getPluginDefinition() {}
  public function getId(){}
  public function setId($id){}
  public function getName(){}
  public function setName($name){}
  public function getParentZone(){}
  public function setParentZone(ZoneInterface $parentZone = null){}
  public function match(AddressInterface $address) {}
}
