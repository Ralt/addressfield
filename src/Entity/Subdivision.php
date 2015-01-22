<?php

/**
 * @file
 * Contains \Drupal\addressfield\Entity\Subdivision.
 */

namespace Drupal\addressfield\Entity;

use CommerceGuys\Addressing\Model\SubdivisionInterface;
use CommerceGuys\Addressing\Provider\DataProvider;
use CommerceGuys\Addressing\Provider\DataProviderInterface;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Annotation\ConfigEntityType;

/**
 * Defines the Subdivision configuration entity.
 *
 * @ConfigEntityType(
 *   id = "subdivision",
 *   label = @Translation("Subdivision"),
 *   handlers = {
 *     "list_builder" = "Drupal\addressfield\SubdivisionListBuilder",
 *     "form" = {
 *       "add" = "Drupal\addressfield\Form\SubdivisionForm",
 *       "edit" = "Drupal\addressfield\Form\SubdivisionForm",
 *       "delete" = "Drupal\addressfield\Form\SubdivisionFormDeleteForm"
 *     }
 *   },
 *   admin_permission = "administer subdivisions",
 *   config_prefix = "subdivision",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "status" = "status"
 *   },
 *   links = {
 *     "edit-form" = "entity.subdivision.edit_form",
 *     "delete-form" = "entity.subdivision.delete_form"
 *   }
 * )
 */
class Subdivision extends ConfigEntityBase implements SubdivisionInterface {

  /**
   * The parent.
   *
   * @var SubdivisionInterface
   */
  protected $parent;

  /**
   * The country code.
   *
   * @var string
   */
  protected $countryCode;

  /**
   * The subdivision id.
   *
   * @var string
   */
  protected $id;

  /**
   * The subdivision code.
   *
   * @var string
   */
  protected $code;

  /**
   * The subdivision name.
   *
   * @var string
   */
  protected $name;

  /**
   * The postal code pattern.
   *
   * @var string
   */
  protected $postalCodePattern;

  /**
   * The children.
   *
   * @param SubdivisionInterface []
   */
  protected $children = [];

  /**
   * The locale.
   *
   * @var string
   */
  protected $locale;

  /**
   * The data provider.
   *
   * @var DataProviderInterface
   */
  protected static $dataProvider;

  /**
   * {@inheritdoc}
   */
  public function getParent() {
    if (!$this->parent->getCode()) {
      // The parent object is incomplete. Load the full one.
      $dataProvider = $this->getDataProvider();
      $this->parent = $dataProvider->getSubdivision($this->parent->getId());
    }

    return $this->parent;
  }

  /**
   * {@inheritdoc}
   */
  public function setParent(SubdivisionInterface $parent = NULL) {
    $this->parent = $parent;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountryCode() {
    return $this->countryCode;
  }

  /**
   * {@inheritdoc}
   */
  public function setCountryCode($countryCode) {
    $this->countryCode = $countryCode;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function setId($id) {
    $this->id = $id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCode() {
    return $this->code;
  }

  /**
   * {@inheritdoc}
   */
  public function setCode($code) {
    $this->code = $code;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPostalCodePattern() {
    return $this->postalCodePattern;
  }

  /**
   * {@inheritdoc}
   */
  public function setPostalCodePattern($postalCodePattern) {
    $this->postalCodePattern = $postalCodePattern;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChildren() {
    // When a subdivision has children the data provider sets $children
    // to array('load'), to indicate that they should be lazy loaded.
    if (!isset($this->children) || $this->children === array('load')) {
      $dataProvider = self::getDataProvider();
      $this->children = $dataProvider->getSubdivisions($this->countryCode, $this->id, $this->locale);
    }

    return $this->children;
  }

  /**
   * {@inheritdoc}
   */
  public function setChildren($children) {
    $this->children = $children;
  }

  /**
   * {@inheritdoc}
   */
  public function hasChildren() {
    return !empty($this->children);
  }

  /**
   * {@inheritdoc}
   */
  public function addChild(SubdivisionInterface $child) {
    if (!$this->hasChild($child)) {
      $child->setParent($this);
      $this->children->add($child);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeChild(SubdivisionInterface $child) {
    if ($this->hasChild($child)) {
      $child->setParent(NULL);
      $this->children->removeElement($child);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasChild(SubdivisionInterface $child) {
    return $this->children->contains($child);
  }

  /**
   * Gets the locale.
   *
   * @return string The locale.
   */
  public function getLocale() {
    return $this->locale;
  }

  /**
   * Sets the locale.
   *
   * @param string $locale The locale.
   * @return $this
   */
  public function setLocale($locale) {
    $this->locale = $locale;
    return $this;
  }

  /**
   * Gets the data provider.
   *
   * @return DataProviderInterface The data provider.
   */
  public static function getDataProvider() {
    if (!isset(self::$dataProvider)) {
      self::setDataProvider(new DataProvider());
    }

    return self::$dataProvider;
  }

  /**
   * Sets the data Subdivision provider.
   * @param \CommerceGuys\Addressing\Provider\DataProviderInterface $dataProvider
   */
  public static function setDataProvider(DataProviderInterface $dataProvider) {
    self::$dataProvider = $dataProvider;
  }

}
