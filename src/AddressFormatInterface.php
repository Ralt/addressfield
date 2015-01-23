<?php

/**
 * @file
 * Contains \Drupal\addressfield\AddressFormatInterface.
 */

namespace Drupal\addressfield;

use CommerceGuys\Addressing\Model\AddressFormatInterface as BaseAddressFormatInterface;
use CommerceGuys\Addressing\Model\SubdivisionInterface;

/**
 * Extends the library interface with Drupal-specific methods.
 *
 * The module links address formats to subdivisions as an optimization,
 * making it faster to load only the subdivisions for a specific country code,
 * since Drupal can't filter config entities without loading them all first.
 */
interface AddressFormatInterface extends BaseAddressFormatInterface {

  /**
   * Gets the address format subdivisions.
   *
   * @return \CommerceGuys\Addressing\Model\SubdivisionInterface[]
   *   The address format subdivisions.
   */
  public function getSubdivisions();

  /**
   * Sets the address format subdivisions.
   *
   * @param \CommerceGuys\Addressing\Model\SubdivisionInterface[] $subdivisions
   *   The address format subdivisions.
   */
  public function setSubdivisions($subdivisions);

  /**
   * Checks whether the address format has subdivisions.
   *
   * @return bool
   *   TRUE if the address format has subdivisions, FALSE otherwise.
   */
  public function hasSubdivisions();

  /**
   * Adds a subdivision to the address format.
   *
   * @param \CommerceGuys\Addressing\Model\SubdivisionInterface[] $subdivision
   *   The subdivision.
   */
  public function addSubdivision(SubdivisionInterface $subdivision);

  /**
   * Removes a subdivision from the address format.
   *
   * @param \CommerceGuys\Addressing\Model\SubdivisionInterface[] $subdivision
   *   The subdivision.
   */
  public function removeSubdivision(SubdivisionInterface $subdivision);

  /**
   * Checks whether the address format has a subdivision.
   *
   * @param \CommerceGuys\Addressing\Model\SubdivisionInterface[] $subdivision
   *   The subdivision.
   *
   * @return bool True if the subdivision was found, false otherwise.
   */
  public function hasSubdivision(SubdivisionInterface $subdivision);

}
