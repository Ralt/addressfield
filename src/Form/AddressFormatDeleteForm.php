<?php

/**
 * @file
 * Contains \Drupal\addressfield\Form\AddressFormatFormDeleteForm.
 */

namespace Drupal\addressfield\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Locale\CountryManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the form to delete an address format.
 */
class AddressFormatDeleteForm extends EntityDeleteForm {

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * Creates an AddressFormatDeleteForm instance.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The address format storage.
   */
  public function __construct(CountryManagerInterface $country_manager) {
    $this->countryManager = $country_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('country_manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    $countries = $this->countryManager->getList();
    $address_format = $this->getEntity();

    return $this->t('Are you sure you want to delete the address format for %country?', array(
      '%country' => $countries[$address_format->getCountryCode()],
    ));
  }

}
