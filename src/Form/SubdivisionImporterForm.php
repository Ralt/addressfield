<?php

/**
 * @file
 * Contains \Drupal\addressfield\Form\SubdivisionImporterForm.
 */

namespace Drupal\addressfield\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Locale\CountryManager;
use Drupal\addressfield\SubdivisionFieldImporter;

/**
 * Builds the form to import a currency.
 */
class SubdivisionImporterForm extends FormBase {

  /**
   * The currency importer.
   *
   * @var \Drupal\addressfield\SubdivisionImporterInterface
   */
  protected $subdivisionImporter;

  /**
   * Constructs a new CommerceCurrencyImporterForm.
   */
  public function __construct() {
    $this->subdivisionImporter = \Drupal::service('addressfield.subdivision_importer');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $form['country'] = array(
      '#type' => 'select',
      '#title' => t('Country'),
      '#description' => t('Select the country you want to import subdivisions for.'),
      '#options' => CountryManager::getStandardList(),
      '#ajax' => array(
        'callback' => '::setCountryAjax',
        'wrapper' => 'subdivision-id-wrapper',
      ),
    );

    $form['id'] = array(
      '#markup' => $this->t('Select country to be able to import.'),
      '#prefix' => '<div id="subdivision-id-wrapper">',
      '#suffix' => '</div>',
    );

    if (!empty($values['country'])) {
      $country = $values['country'];
      $subdivisions = $this->subdivisionImporter->getImportableSubdivisions($country);
      if (!$subdivisions) {
        $form['id']['#markup'] = $this->t('All subdevisions for selected country is imported.');
      }
      else {
        $form['id'] = array(
          '#type' => 'select',
          '#title' => $this->t('Subdivision'),
          '#description' => $this->t('Please select the subdivision you would like to import.'),
          '#required' => TRUE,
          '#options' => $this->getSubdivisionsOptions($subdivisions),
          '#prefix' => '<div id="subdivision-id-wrapper">',
          '#suffix' => '</div>',
        );
      }
    }

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

    return $form;
  }

  /**
   * Returns an options list for subdivisions.
   *
   * @param SubdivisionInterface[] $subdivisions
   *   An array of subdivisions.
   *
   * @return array
   *   The list of options for a select widget.
   */
  public function getSubdivisionsOptions(array $subdivisions) {
    $options = array();
    foreach ($subdivisions as $id => $subdivision) {
      $options[$id] = $subdivision->getName();
    }
    asort($options);

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $subdivision = $this->subdivisionImporter->importSubdivision(
      $values['id']
    );
    dpm($subdivision);
    if (!$subdivision) {
      $form_state->setRebuild();
      return;
    }

    try {
      $subdivision->save();
      drupal_set_message(
        $this->t('Imported the %label subdivision.', array('%label' => $subdivision->label()))
      );
      $triggering_element = $form_state->getTriggeringElement();
      if ($triggering_element['#name'] == 'import_and_new') {
        $form_state->setRebuild();
      }
      else {
        $form_state->setRedirect('entity.subdivision.list');
      }
    } catch (\Exception $e) {
      drupal_set_message($this->t('The %label subdivision was not imported.', array('%label' => $subdivision->label())), 'error');
      $this->logger('addressfield')->error($e);
      $form_state->setRebuild();
    }
  }

  public function setCountryAjax(array $form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $country = $values['country'];
    $subdivisions = $this->subdivisionImporter->getImportableSubdivisions($country);
    if (!$subdivisions) {
      $form['id']['#markup'] = $this->t('All subdevisions for selected country is imported.');
    }
    else {
      $form['id'] = array(
        '#type' => 'select',
        '#title' => $this->t('Subdivision'),
        '#description' => $this->t('Please select the subdivision you would like to import.'),
        '#required' => TRUE,
        '#options' => $this->getSubdivisionsOptions($subdivisions),
        '#prefix' => '<div id="subdivision-id-wrapper">',
        '#suffix' => '</div>',
      );
    }
    return $form['id'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'addressfield_currency_importer';
  }
}
