<?php

/**
 * @file
 * Contains \Drupal\addressfield\Plugin\field\formatter\AddressDefaultFormatter.
 */

namespace Drupal\addressfield\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'address_default' formatter.
 *
 * @FieldFormatter(
 *   id = "address_default",
 *   label = @Translation("Basic Address"),
 *   field_types = {
 *     "address",
 *   },
 * )
 */
class AddressDefaultFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();
    foreach ($items as $delta => $item) {
      $elements[$delta]['#type'] = 'fieldset';
      $elements[$delta]['#tite'] = $items[$delta]->local;
      $elements[$delta]['countryCode'] = array(
        '#type' => 'item',
        '#title' => t('Country Code'),
        '#markup' => $items[$delta]->countryCode,
      );
      $elements[$delta]['administrativeArea'] = array(
        '#type' => 'item',
        '#title' => t('Administrative Area'),
        '#markup' => $items[$delta]->administrativeArea,
      );
      $elements[$delta]['locality'] = array(
        '#type' => 'item',
        '#title' => t('Locality'),
        '#markup' => $items[$delta]->locality,
      );
      $elements[$delta]['dependentLocality'] = array(
        '#type' => 'item',
        '#title' => t('Dependant Locality'),
        '#markup' => $items[$delta]->dependentLocality,
      );
      $elements[$delta]['postalCode'] = array(
        '#type' => 'item',
        '#title' => t('Postal Code'),
        '#markup' => $items[$delta]->postalCode,
      );
      $elements[$delta]['sortingCode'] = array(
        '#type' => 'item',
        '#title' => t('Sorting Code'),
        '#markup' => $items[$delta]->sortingCode,
      );
      $elements[$delta]['addressLine1'] = array(
        '#type' => 'item',
        '#title' => t('Address Line 1'),
        '#markup' => $items[$delta]->addressLine1,
      );
      $elements[$delta]['addressLine2'] = array(
        '#type' => 'item',
        '#title' => t('Address Line 2'),
        '#markup' => $items[$delta]->addressLine2,
      );
      $elements[$delta]['organization'] = array(
        '#type' => 'item',
        '#title' => t('Organization'),
        '#markup' => $items[$delta]->organization,
      );
      $elements[$delta]['recipient'] = array(
        '#type' => 'item',
        '#title' => t('Recipient'),
        '#markup' => $items[$delta]->recipient,
      );
    }
    return $elements;
  }

}
