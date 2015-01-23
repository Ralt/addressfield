<?php

/**
 * @file
 * Contains \Drupal\addressfield\Entity\AddressFormat.
 */

namespace Drupal\addressfield\Entity;

use CommerceGuys\Addressing\Model\SubdivisionInterface;
use Drupal\addressfield\AddressFormatInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the AddressFormat configuration entity.
 *
 * @ConfigEntityType(
 *   id = "address_format",
 *   label = @Translation("Address format"),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\addressfield\Form\AddressFormatForm",
 *       "edit" = "Drupal\addressfield\Form\AddressFormatForm",
 *       "delete" = "Drupal\addressfield\Form\AddressFormatDeleteForm"
 *     },
 *     "list_builder" = "Drupal\addressfield\AddressFormatListBuilder",
 *   },
 *   admin_permission = "administer address formats",
 *   config_prefix = "address_format",
 *   entity_keys = {
 *     "id" = "countryCode",
 *     "label" = "countryCode",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/config/regional/address-formats",
 *     "edit-form" = "/admin/config/regional/address-formats/manage/{address_format}",
 *     "delete-form" = "/admin/config/regional/address-formats/manage/{address_format}/delete"
 *   }
 * )
 */
class AddressFormat extends ConfigEntityBase implements AddressFormatInterface {

  /**
   * The country code.
   *
   * @var string
   */
  protected $countryCode;

  /**
   * The format.
   *
   * @var string
   */
  protected $format;

  /**
   * The required fields.
   *
   * @var array
   */
  protected $requiredFields = [];

  /**
   * The fields that need to be uppercased.
   *
   * @var array
   */
  protected $uppercaseFields = [];

  /**
   * The administrative area type.
   *
   * @var string
   */
  protected $administrativeAreaType;

  /**
   * The locality type.
   *
   * @var string
   */
  protected $localityType;

  /**
   * The dependent locality type.
   *
   * @var string
   */
  protected $dependentLocalityType;

  /**
   * The postal code type.
   *
   * @var string
   */
  protected $postalCodeType;

  /**
   * The postal code pattern.
   *
   * @var string
   */
  protected $postalCodePattern;

  /**
   * The postal code prefix.
   *
   * @var string
   */
  protected $postalCodePrefix;

  /**
   * The subdivision ids.
   *
   * @var array
   */
  protected $subdivisionIds = [];

  /**
   * The subdivision entities.
   *
   * @var \CommerceGuys\Addressing\Model\SubdivisionInterface[]
   */
  protected $subdivisions = [];

  /**
   * Get the list of administrative area types defined by the AddressFormatInterface.
   *
   * @return array of string values.
   */
  public function getAdministrativeAreaTypes() {
    return array(
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_AREA => t('Area'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_COUNTY => t('County'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_DEPARTMENT => t('Department'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_DISTRICT => t('District'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_DO_SI => t('Do si'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_EMIRATE => t('Emirate'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_ISLAND => t('Island'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_OBLAST => t('Oblast'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_PARISH => t('Parish'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_PREFECTURE => t('Prefecture'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_PROVINCE => t('Province'),
      AddressFormat::ADMINISTRATIVE_AREA_TYPE_STATE => t('State'),
    );
  }

  /**
   * Get the list of dependent locality types defined by the AddressFormatInterface.
   *
   * @return array of string values.
   */
  public function getDependentLocalityTypes() {
    return array(
      AddressFormat::DEPENDENT_LOCALITY_TYPE_DISTRICT => t('District'),
      AddressFormat::DEPENDENT_LOCALITY_TYPE_NEIGHBORHOOD => t('Neighborhood'),
      AddressFormat::DEPENDENT_LOCALITY_TYPE_VILLAGE_TOWNSHIP => t('Village township'),
      AddressFormat::DEPENDENT_LOCALITY_TYPE_SUBURB => t('Suburb'),
    );
  }

  /**
   * Get the list of fields defined by the AddressFormatInterface.
   *
   * @return array of string values.
   */
  public function getFields() {
    return array(
      AddressFormat::FIELD_RECIPIENT => t('Recipient'),
      AddressFormat::FIELD_ORGANIZATION => t('Organization'),
      AddressFormat::FIELD_ADDRESS => t('Address'),
      AddressFormat::FIELD_SORTING_CODE => t('Sorting code'),
      AddressFormat::FIELD_POSTAL_CODE => t('Postal code'),
      AddressFormat::FIELD_DEPENDENT_LOCALITY => t('Dependent locality'),
      AddressFormat::FIELD_LOCALITY => t('Locality'),
      AddressFormat::FIELD_ADMINISTRATIVE_AREA => t('Administrative area'),

    );
  }

  /**
   * Get the list of locality types defined by the AddressFormatInterface.
   *
   * @return array of string values.
   */
  public function getLocalityTypes() {
    return array(
      AddressFormat::LOCALITY_TYPE_CITY => t('City'),
      AddressFormat::LOCALITY_TYPE_DISTRICT => t('District'),
      AddressFormat::LOCALITY_TYPE_POST_TOWN => t('Post town'),
    );
  }

  /**
   * Get the list of postal code types defined by the AddressFormatInterface.
   *
   * @return array of string values.
   */
  public function getPostalCodeTypes() {
    return array(
      AddressFormat::POSTAL_CODE_TYPE_POSTAL => t('Postal'),
      AddressFormat::POSTAL_CODE_TYPE_ZIP => t('Zip'),
      AddressFormat::POSTAL_CODE_TYPE_PIN => t('Pin'),
    );
  }

  /**
   * Get the list of tokens used by formatting the format.
   *
   * @return array of string values.
   */
  public function getFieldsTokens() {
    $fields = array_keys($this->getFields());
    foreach ($fields as &$field) {
      $field = '%' . $field;
    }
    return $fields;
  }

  /**
   * Overrides \Drupal\Core\Entity\Entity::id().
   */
  public function id() {
    return $this->getCountryCode();
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
  public function getFormat() {
    return $this->format;
  }

  /**
   * {@inheritdoc}
   */
  public function setFormat($format) {
    $this->format = $format;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    return $this->requiredFields;
  }

  /**
   * {@inheritdoc}
   */
  public function setRequiredFields(array $requiredFields) {
    $this->requiredFields = $requiredFields;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUppercaseFields() {
    return $this->uppercaseFields;
  }

  /**
   * {@inheritdoc}
   */
  public function setUppercaseFields(array $uppercaseFields) {
    $this->uppercaseFields = $uppercaseFields;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAdministrativeAreaType() {
    return $this->administrativeAreaType;
  }

  /**
   * {@inheritdoc}
   */
  public function setAdministrativeAreaType($administrativeAreaType) {
    $this->administrativeAreaType = $administrativeAreaType;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLocalityType() {
    return $this->localityType;
  }

  /**
   * {@inheritdoc}
   */
  public function setLocalityType($localityType) {
    $this->localityType = $localityType;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDependentLocalityType() {
    return $this->dependentLocalityType;
  }

  /**
   * {@inheritdoc}
   */
  public function setDependentLocalityType($dependentLocalityType) {
    $this->dependentLocalityType = $dependentLocalityType;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPostalCodeType() {
    return $this->postalCodeType;
  }

  /**
   * {@inheritdoc}
   */
  public function setPostalCodeType($postalCodeType) {
    $this->postalCodeType = $postalCodeType;
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
  public function getPostalCodePrefix() {
    return $this->postalCodePrefix;
  }

  /**
   * {@inheritdoc}
   */
  public function setPostalCodePrefix($postalCodePrefix) {
    $this->postalCodePrefix = $postalCodePrefix;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubdivisions() {
    if (empty($this->subdivisions) && !empty($this->subdivisionIds)) {
      $this->subdivisions = Subdivision::loadMultiple($this->subdivisionIds);
    }

    return $this->subdivisions;
  }

  /**
   * {@inheritdoc}
   */
  public function setSubdivisions($subdivisions) {
    $this->subdivisions = $subdivisions;
    $this->subdivisionIds = '';

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasSubdivisions() {
    return !empty($this->subdivisionIds);
  }

  /**
   * {@inheritdoc}
   */
  public function addSubdivision(SubdivisionInterface $subdivision) {
    if (!$this->hasSubdivision($subdivision)) {
      $this->subdivisionIds[] = $subdivision->id();
      $this->subdivisions = NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function removeSubdivision(SubdivisionInterface $subdivision) {
    if ($this->hasSubdivision($subdivision)) {
      // Remove the subdivision and rekey the array.
      $index = array_search($subdivision->id(), $this->subdivisionIds);
      unset($this->subdivisionIds[$index]);
      $this->subdivisionIds = array_values($this->subdivisions);
      $this->subdivisions = NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function hasSubdivision(SubdivisionInterface $subdivision) {
    return in_array($subdivision->id(), $this->subdivisionIds);
  }

}
