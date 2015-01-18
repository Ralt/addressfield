<?php

/**
 * @file
 * Contains \Drupal\addressfield\Tests\AddressFormatTest.
 */

namespace Drupal\addressfield\Tests;

use Drupal\Core\Locale\CountryManager;
use Drupal\simpletest\WebTestBase;


/**
 * Ensures that address format functions work correctly.
 *
 * @group addressfield
 */
class AddressFormatTest extends WebTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('system', 'user', 'addressfield');

  /**
   *
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * Utility function to create a random address format.
   *
   * @return AddressFormat A random address format config entity.
   */
  protected function createRandomAddressFormat() {
    $country_codes = array_keys(CountryManager::getStandardList());

    // Find a random country_code that doesn't exist yet.
    while ($key = array_rand($country_codes)) {
      if (entity_load('address_format', $country_codes[$key])) {
        continue;
      }
      $country_code = $country_codes[$key];
      break;
    }

    $values = array(
      'countryCode' => $country_code,
    );

    $address_format = entity_create('address_format', $values);
    $address_format->save();
    return $address_format;
  }

  /**
   * Tests creating a address format programmatically.
   */
  function testAddressFormatCreationProgramatically() {
    // Create a address format type programmaticaly.
    $address_format = $this->createRandomAddressFormat();
    $address_format_exists = (bool) entity_load('address_format', $address_format->id());
    $this->assertTrue($address_format_exists, 'The new address format has been created in the database.');

    // Login a test user.
    $web_user = $this->drupalCreateUser(array('administer address formats'));
    $this->drupalLogin($web_user);
    // Visit the address format edit page.
    $this->drupalGet('admin/config/regional/address-format/' . $address_format->id());
    $this->assertResponse(200, 'The new address format can be accessed at admin/config/regional/address-format.');
  }

  /**
   * Tests creating a address format via the import form.
   */
  function testAddressFormatCreationImportForm() {
    $country_codes = array_keys(CountryManager::getStandardList());

    // Login a test user.
    $web_user = $this->drupalCreateUser(array('administer address formats'));
    $this->drupalLogin($web_user);
    // Find a random country_code that doesn't exist yet.
    while ($key = array_rand($country_codes)) {
      if (entity_load('address_format', $country_codes[$key])) {
        continue;
      }
      $country_code = $country_codes[$key];
      break;
    }

    $edit = array(
      'country_code' => $country_code,
    );
    $this->drupalPostForm('admin/config/regional/address-format/import', $edit, t('Import'));

    $this->drupalGet('admin/config/regional/address-format/' . $country_code);
    $this->assertResponse(200, 'The new address format can be accessed at admin/config/regional/address-format.');
  }
}
