<?php

/**
 * @file
 * Contains \Drupal\addressfield\Plugin\Field\FieldWidget\AddressDefaultWidget.
 */

namespace Drupal\addressfield\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'address' widget.
 *
 * @FieldWidget(
 *   id = "address_default",
 *   label = @Translation("Address"),
 *   field_types = {
 *     "address"
 *   },
 * )
 */
class AddressDefaultWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = array();
    $element['#title'] = t('Address');
    $element['local'] = array(
      '#type' => 'textfield',
      '#title' => t('Local'),
      '#default_value' => isset($items[$delta]->local) ? $items[$delta]->local : NULL,
    );
    $element['countryCode'] = array(
      '#type' => 'textfield',
      '#title' => t('Country'),
      '#default_value' => isset($items[$delta]->countryCode) ? $items[$delta]->countryCode : NULL,
    );
    $element['administrativeArea'] = array(
      '#type' => 'textfield',
      '#title' => t('Administrative Area'),
      '#default_value' => isset($items[$delta]->administrativeArea) ? $items[$delta]->administrativeArea : NULL,
    );
    $element['locality'] = array(
      '#type' => 'textfield',
      '#title' => t('Locality'),
      '#default_value' => isset($items[$delta]->locality) ? $items[$delta]->locality : NULL,
    );
    $element['dependentLocality'] = array(
      '#type' => 'textfield',
      '#title' => t('Dependant Locality'),
      '#default_value' => isset($items[$delta]->dependentLocality) ? $items[$delta]->dependentLocality : NULL,
    );
    $element['postalCode'] = array(
      '#type' => 'textfield',
      '#title' => t('Postal Code'),
      '#default_value' => isset($items[$delta]->postalCode) ? $items[$delta]->postalCode : NULL,
    );
    $element['sortingCode'] = array(
      '#type' => 'textfield',
      '#title' => t('Sorting Code'),
      '#default_value' => isset($items[$delta]->sortingCode) ? $items[$delta]->sortingCode : NULL,
    );
    $element['addressLine1'] = array(
      '#type' => 'textfield',
      '#title' => t('Address Line 1'),
      '#default_value' => isset($items[$delta]->addressLine1) ? $items[$delta]->addressLine1 : NULL,
    );
    $element['addressLine2'] = array(
      '#type' => 'textfield',
      '#title' => t('Address Line 2'),
      '#default_value' => isset($items[$delta]->addressLine2) ? $items[$delta]->addressLine2 : NULL,
    );
    $element['organization'] = array(
      '#type' => 'textfield',
      '#title' => t('Organization'),
      '#default_value' => isset($items[$delta]->organization) ? $items[$delta]->organization : NULL,
    );
    $element['recipient'] = array(
      '#type' => 'textfield',
      '#title' => t('Recipient'),
      '#default_value' => isset($items[$delta]->recipient) ? $items[$delta]->recipient : NULL,
    );
    return $element;
  }
}
