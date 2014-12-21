<?php

/**
 * @file
 * Contains \Drupal\addressfield\AddressItemInterface.
 */

namespace Drupal\addressfield;

use Drupal\Core\Field\FieldItemInterface;
use CommerceGuys\Addressing\Model\AddressInterface;

/**
 * Defines an interface for the link field item.
 */
interface AddressItemInterface extends FieldItemInterface {

  /**
   * Get an address model object
   *
   * @return AddressInterface
   */
  public function getAddress();

  /**
   * Get the formatted address.
   *
   * @param string           $originCountryCode The country code of the origin country.
   *                                            e.g. US if the parcels are sent from the USA.
   * @param string           $originLocale      The locale used to get the country names.
   * @return string The formatted address, divided by unix newlines (\n).
   */
  public function getPostal($originCountryCode, $originLocale = 'en');

}
