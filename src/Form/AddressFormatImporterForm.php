<?php

/**
 * @file
 * Contains \Drupal\addressfield\Form\AddressFormatImporterForm.
 */

namespace Drupal\addressfield\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Locale\CountryManager;
use Drupal\addressfield\AddressFieldImporter;


/**
 * Builds the form to import an adress format.
 */
class AddressFormatImporterForm extends FormBase {

  /**
   * The address format importer.
   *
   * @var \Drupal\addressfield\AddressFormatImporterInterface
   */
  protected $addressFormatImporter;

  /**
   * Constructs a new AddressFormatImporterForm.
   */
  public function __construct() {
    $this->addressFormatImporter = \Drupal::service('addressfield.address_format_importer');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $address_formats = $this->addressFormatImporter->getImportableAddressFormats();

    if (!$address_formats) {
      $form['message'] = array(
        '#markup' => $this->t('All address formats are already imported.'),
      );
    }
    else {
      $form['country_code'] = array(
        '#type' => 'select',
        '#title' => $this->t('Country'),
        '#description' => $this->t('Please select the country you would like to import.'),
        '#required' => TRUE,
        '#options' => CountryManager::getStandardList(),
      );

      $form['actions']['#type'] = 'actions';
      $form['actions']['import'] = array(
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#name' => 'import',
        '#value' => $this->t('Import'),
        '#submit' => array('::submitForm'),
      );
      $form['actions']['import_new'] = array(
        '#type' => 'submit',
        '#name' => 'import_and_new',
        '#value' => $this->t('Import and new'),
        '#submit' => array('::submitForm'),
      );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $address_format = $this->addressFormatImporter->importAddressFormat(
      $values['country_code']
    );

    try {
      $address_format->save();
      drupal_set_message(
        $this->t('Imported the %label address format.', array('%label' => $address_format->label()))
      );
      $triggering_element = $form_state->getTriggeringElement();
      if ($triggering_element['#name'] == 'import_and_new') {
        $form_state->setRebuild();
      }
      else {
        $form_state->setRedirect('entity.address_format.list');
      }
    } catch (\Exception $e) {
      drupal_set_message($this->t('The %label address format was not imported.', array('%label' => $address_format->label())), 'error');
      $this->logger('addressfield')->error($e);
      $form_state->setRebuild();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'addressfield_address_format_importer';
  }
}
