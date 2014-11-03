<?php

/**
 * @file
 * Contains \Drupal\addressfield\Form\CommerceCurrencyImporterForm.
 */

namespace Drupal\addressfield\Form;

use CommerceGuys\Intl\Country\CountryRepository;
use CommerceGuys\Intl\Currency\CurrencyInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use CommerceGuys\Addressing\Repository\AddressFormatRepository;
use CommerceGuys\Addressing\Repository\SubdivisionRepository;

/**
 * Builds the form to import a currency.
 */
class SubdivisionImporterForm extends FormBase {

  /**
   * The currency importer.
   *
   * @var \Drupal\addressfield\CurrencyImporterInterface
   */
  protected $currencyImporter;

  /**
   * Constructs a new CommerceCurrencyImporterForm.
   */
  public function __construct() {
//    $this->currencyImporter = \Drupal::service('addressfield.currency_importer');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $currencies = $this->currencyImporter->getImportableCurrencies();

    if (!$currencies) {
      $form['message'] = array(
        '#markup' => $this->t('All currencies are already imported.'),
      );
    }
    else {
      $form['currency_code'] = array(
        '#type' => 'select',
        '#title' => $this->t('Currency code'),
        '#description' => $this->t('Please select the currency you would like to import.'),
        '#required' => TRUE,
        '#options' => $this->getCurrencyOptions($currencies),
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
   * Returns an options list for currencies.
   *
   * @param CurrencyInterface[] $currencies
   *   An array of currencies.
   *
   * @return array
   *   The list of options for a select widget.
   */
  public function getCurrencyOptions(array $currencies) {
    $options = array();
    foreach ($currencies as $currency_code => $currency) {
      $options[$currency_code] = $currency->getName();
    }
    asort($options);

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $currency = $this->currencyImporter->importCurrency(
      $values['currency_code']
    );

    try {
      $currency->save();
      drupal_set_message(
        $this->t('Imported the %label currency.', array('%label' => $currency->label()))
      );
      $triggering_element = $form_state->getTriggeringElement();
      if ($triggering_element['#name'] == 'import_and_new') {
        $form_state->setRebuild();
      }
      else {
        $form_state->setRedirect('entity.commerce_currency.list');
      }
    } catch (\Exception $e) {
      drupal_set_message($this->t('The %label currency was not imported.', array('%label' => $currency->label())), 'error');
      $this->logger('addressfield')->error($e);
      $form_state->setRebuild();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'addressfield_currency_importer';
  }
}
