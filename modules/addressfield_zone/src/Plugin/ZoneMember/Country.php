<?php

/**
 * @file
 * Contains \Drupal\addressfield_zone\Plugin\ZoneMember\Country.
 */

namespace Drupal\addressfield_zone\Plugin\ZoneMember;

use Drupal\addressfield_zone\ZoneMemberInterface;
use CommerceGuys\Addressing\Model\AddressInterface;
use CommerceGuys\Addressing\Model\ZoneInterface;

/**
 * Defines a country zone.
 *
 * @ZoneMember(
 *   id = "zonemember_country"
 *   label = @Translation("Country")
 * )
 */
class Country implements ZoneMemberInterface {

  /**
   * {@inheritdoc}
   */
  public function getPluginId() {
    return 'zonemember_country';
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginDefinition() {
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
  }

  /**
   * {@inheritdoc}
   */
  public function setId($id) {
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
  }

  /**
   * {@inheritdoc}
   */
  public function getParentZone() {
  }

  /**
   * {@inheritdoc}
   */
  public function setParentZone(ZoneInterface $parentZone = null) {
  }

  /**
   * {@inheritdoc}
   */
  public function match(AddressInterface $address) {
  }

}
