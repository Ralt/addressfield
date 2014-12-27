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
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

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
 *   admin_permission = "administer subdevisions",
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
  protected $children;

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
   * Overrides \Drupal\Core\Entity\Entity::id().
   */
  public function id() {
    return $this->getId();
  }

  //@todo Do we need to have two getters for the id?
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

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['revision_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Revision ID'))
      ->setDescription(t('The product revision ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['parent'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Parent'))
      ->setDescription(t('The parent of this subdivision.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'subdivision')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\addressfield\Entity\Subdivision::getParent')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'subdivision',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['countryCode'] = BaseFieldDefinition::create('countryCode')
      ->setLabel(t('Country Code'))
      ->setDescription(t('The country code of this subdivision.'))
      ->setRevisionable(TRUE)
      ->setRequired(TRUE);

    $fields['id'] = BaseFieldDefinition::create('id')
      ->setLabel(t('ID'))
      ->setRequired(TRUE)
      ->setDescription(t('The ID code of this subdivision.'));

    $fields['code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Code'))
      ->setDescription(t('The subdivision code for this Subdivision.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the subdivision.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setRequired(TRUE);

    $fields['postalCodePattern'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Postal Code Pattern'))
      ->setDescription(t('The Postal Code Pattern of the subdivision.'))
      ->setRevisionable(TRUE)
      ->setRequired(TRUE);

    $fields['locale'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Locale'))
      ->setDescription(t('The Locale of the subdivision.'))
      ->setRevisionable(TRUE)
      ->setRequired(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Active'))
      ->setDescription(t('Disabled products cannot be added to shopping carts and may be hidden in administrative product lists.'))
      ->setDefaultValue(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings(array(
        'default_value' => 1,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'weight' => 10,
        'settings' => array(
          'display_label' => TRUE
        )
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the product was created.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the product was last edited.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['revision_log'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Revision log message'))
      ->setDescription(t('The log entry explaining the changes in this revision.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', array(
        'type' => 'string_textarea',
        'weight' => 25,
        'settings' => array(
          'rows' => 4,
        ),
      ));

    return $fields;
  }
}
