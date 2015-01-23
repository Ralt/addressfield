<?php

/**
 * @file
 * Contains \Drupal\addressfield\Entity\Subdivision.
 */

namespace Drupal\addressfield\Entity;

use CommerceGuys\Addressing\Model\SubdivisionInterface;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\Annotation\ConfigEntityType;

/**
 * Defines the Subdivision configuration entity.
 *
 * @ConfigEntityType(
 *   id = "subdivision",
 *   label = @Translation("Subdivision"),
 *   handlers = {
 *     "storage" = "Drupal\addressfield\SubdivisionStorage",
 *     "list_builder" = "Drupal\addressfield\SubdivisionListBuilder",
 *     "form" = {
 *       "add" = "Drupal\addressfield\Form\SubdivisionForm",
 *       "edit" = "Drupal\addressfield\Form\SubdivisionForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   admin_permission = "administer subdivisions",
 *   config_prefix = "subdivision",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/config/regional/subdivisions/{address_format}/{parent}",
 *     "edit-form" = "/admin/config/regional/subdivisions/manage/{subdivision}",
 *     "delete-form" = "/admin/config/regional/subdivisions/manage/{subdivision}/delete"
 *   }
 * )
 */
class Subdivision extends ConfigEntityBase implements SubdivisionInterface {

  /**
   * The country code.
   *
   * @var string
   */
  protected $countryCode;

  /**
   * The parent id.
   *
   * @var string
   */
  protected $parentId;

  /**
   * The parent entity.
   *
   * @var \CommerceGuys\Addressing\Model\SubdivisionInterface
   */
  protected $parent;

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
   * The children ids.
   *
   * @var array
   */
  protected $childrenIds = [];

  /**
   * The children entities.
   *
   * @var \CommerceGuys\Addressing\Model\SubdivisionInterface[]
   */
  protected $children = [];

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
  public function getParent() {
    if (empty($this->parentId)) {
      return NULL;
    }
    if (empty($this->parent)) {
      $this->parent = self::load($this->parentId);
    }

    return $this->parent;
  }

  /**
   * {@inheritdoc}
   */
  public function setParent(SubdivisionInterface $parent = NULL) {
    $this->parent = $parent;
    $this->parentId = $parent ? $parent->getId() : NULL;

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
  public function getChildrenIds() {
    return $this->childrenIds;
  }

  /**
   * {@inheritdoc}
   */
  public function getChildren() {
    if (empty($this->children) && !empty($this->childrenIds)) {
      $this->children = self::loadMultiple($this->childrenIds);
    }

    return $this->children;
  }

  /**
   * {@inheritdoc}
   */
  public function setChildren($children) {
    $this->children = $children;
    $this->childrenIds = $this->recalculateChildrenIds($children);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasChildren() {
    return !empty($this->childrenIds);
  }

  /**
   * {@inheritdoc}
   */
  public function addChild(SubdivisionInterface $child) {
    if (!$this->hasChild($child)) {
      $this->childrenIds[] = $child->id();
      $this->children = NULL;
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeChild(SubdivisionInterface $child) {
    if ($this->hasChild($child)) {
      // Remove the child and rekey the array.
      $index = array_search($child, $this->childrenIds);
      unset($this->childrenIds[$index]);
      $this->childrenIds = array_values($this->childrenIds);
      $this->children = NULL;
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasChild(SubdivisionInterface $child) {
    return in_array($child->id(), $this->childrenIds);
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();

    // Depend on the parent entity. That is either another subdivision,
    // or the address format (for top level subdivisions).
    $parent = $this->getParent();
    if ($parent) {
      $this->addDependency('config', $parent->getConfigDependencyName());
    }
    else {
      $format = AddressFormat::load($this->countryCode);
      $this->addDependency('config', $format->getConfigDependencyName());
    }

    return $this->dependencies;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    if ($this->isSyncing()) {
      // Imported configuration already has the correct relationships.
      return;
    }

    if (!$update) {
      // Add the new subdivision to the parent entity. That is either another
      // subdivision, or the address format (for top level subdivisions).
      $parent = $this->getParent();
      if ($parent) {
        $parent->addChild($this);
        $parent->save();
      }
      else {
        $format = AddressFormat::load($this->countryCode);
        $format->addSubdivision($this);
        $format->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    foreach ($entities as $entity) {
      if ($entity->isSyncing()) {
        // Imported configuration already has the correct relationships.
        return;
      }

      // Remove the deleted subdivisions from parent entities. Those are either
      // other subdivisions, or address formats (for top level subdivisions).
      $parent = $entity->getParent();
      if ($parent) {
        $parent->removeChild($entity);
        $parent->save();
      }
      else {
        $format = AddressFormat::load($entity->getCountryCode());
        if ($format) {
          $format->removeSubdivision($entity);
          $format->save();
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $parameters = [];
    if ($rel == 'collection') {
      $parameters['address_format'] = $this->countryCode;
      $parameters['parent'] = $this->parentId;
    }
    else {
      $parameters['subdivision'] = $this->id;
    }

    return $parameters;
  }

}
