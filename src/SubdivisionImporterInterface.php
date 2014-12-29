<?php

/**
 * @file
 * Contains \Drupal\addressfield\SubdivisionImporterInterface.
 */

namespace Drupal\addressfield;

/**
 * Defines an subdivision importer.
 */
interface SubdivisionImporterInterface {

  /**
   * Returns all importable subdivisions.
   *
   * @return \CommerceGuys\Addressing\Model\SubdivisionInterface[]
   *    Array of importable subdivisions.
   */
  public function getImportableSubdivisions($country_code);

  /**
   * Creates a new address format object for the given country code.
   *
   * @param string $country_code
   *   The country code.
   *
   * @return \Drupal\addressfield\Entity\Subdivision | bool
   *    The new subdivision or false if the subdivision is already imported.
   */
  public function importSubdivision($id);
}
