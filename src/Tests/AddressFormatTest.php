<?php

/**
 * @file
 * Contains \Drupal\address\Tests\AddressFormatTest.
 */

namespace Drupal\address\Tests;

use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use Drupal\address\AddressFormatImporter;
use Drupal\simpletest\WebTestBase;


/**
 * Ensures that address format functions work correctly.
 *
 * @group address
 */
class AddressFormatTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('system', 'user', 'address');

  /** @var \Drupal\Core\Entity\EntityStorageInterface */
  protected $addressFormatStorage;

  /** @var CommerceGuys\Addressing\Repository\AddressFormatRepositoryInterface */
  protected $addressFormatRepository;

  protected function setUp() {
    parent::setUp();
    $user = $this->drupalCreateUser(['administer address formats']);
    $this->drupalLogin($user);

    $this->addressFormatStorage = \Drupal::service('entity.manager')->getStorage('address_format');
    $this->addressFormatRepository = new AddressFormatRepository();
  }

  /**
   * Tests that the address format forms exist.
   */
  public function testAddressFormatFormExists() {
    $this->drupalGet('admin/config/regional/address-formats/');
    $this->assertResponse(200, 'The address format list builder exists.');

    $this->drupalGet('admin/config/regional/address-formats/add');
    $this->assertResponse(200, 'The address format add form exists.');
  }

  /**
   * Tests that the address formats are imported at startup.
   *
   * Protected because it's called by testDeleteAddressFormat, it'd be pointless to call it twice.
   */
  protected function defaultAddressFormats() {
    // batch process is not run with anything but the UI because the form API handles batches
    foreach ($this->addressFormatRepository->getAll() as $format) {
      AddressFormatImporter::importAddressFormat($this->addressFormatStorage, $format);
    }
    $this->assertTrue(
      count($this->addressFormatStorage->loadMultiple()) === count($this->addressFormatRepository->getAll()),
      'The importer imported all the address formats.'
    );
  }

  /**
   * Tests that deleting an address format works.
   *
   * Protected because it's called by testAddAddressFormat, it'd be pointless to call it twice.
   */
  protected function deleteAddressFormat() {
    $this->defaultAddressFormats();
    $this->drupalPostForm('admin/config/regional/address-formats/manage/AC/delete', ['confirm' => 1], t('Delete'));
    $this->assertTrue(
      (count($this->addressFormatRepository->getAll()) - 1) === count($this->addressFormatStorage->loadMultiple()),
      'There is now one less address format than in the repository.'
    );
  }

  /**
   * Tests that adding a new address format works.
   */
  public function testAddAddressFormat() {
    $this->deleteAddressFormat();

    $this->drupalPostForm(
      'admin/config/regional/address-formats/add',
      ['countryCode' => 'AC', 'format' => 'foo'],
      t('Save')
    );
    $this->assertTrue(
      count($this->addressFormatRepository->getAll()) === count($this->addressFormatStorage->loadMultiple()),
      'There is now as many address formats than in the repository.'
    );
  }

}
