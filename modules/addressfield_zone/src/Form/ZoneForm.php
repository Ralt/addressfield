<?php

/**
 * @file
 * Contains \Drupal\addressfield_zone\Form\ZoneForm.
 */

namespace Drupal\addressfield_zone\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\addressfield_zone\Entity\Zone;

class ZoneForm extends EntityForm {

  /**
   * The zone storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $zoneStorage;

  /**
   * Creates a ZoneForm instance.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $zone_storage
   *   The zone storage.
   */
  public function __construct(EntityStorageInterface $zone_storage) {
    $this->zoneStorage = $zone_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityManagerInterface $entity_manager */
    $entity_manager = $container->get('entity.manager');

    return new static($entity_manager->getStorage('addressfield_zone'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $zone = $this->entity;

    $form['id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Machine name'),
      '#default_value' => $zone->getId(),
      '#element_validate' => array('::validateId'),
      '#description' => $this->t('Only lowercase, underscore-separated letters allowed.'),
      '#pattern' => '[a-z_]+',
      '#maxlength' => 255,
      '#required' => TRUE,
    );
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => $zone->getName(),
      '#maxlength' => 255,
      '#required' => TRUE,
    );
    $form['scope'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Scope'),
      '#default_value' => $zone->getScope(),
      '#maxlength' => 255,
    );
    $form['priority'] = array(
      '#type' => 'number',
      '#title' => $this->t('Priority'),
      '#default_value' => $zone->getPriority(),
    );

    // Build the list of existing zone members for this zone.
    $form['members'] = array(
      '#type' => 'table',
      '#header' => array(
        $this->t('Zone Member'),
        $this->t('Weight'),
        $this->t('Operations'),
      ),
      '#tabledrag' => array(
        array(
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'zone-member-order-weight',
        ),
      ),
      '#attributes' => array(
        'id' => 'zone-zone-members',
      ),
      '#empty' => t('There are currently no zone member in this zone. Add one by selecting an option below.'),
      // Render zone members below parent elements.
      '#weight' => 5,
    );

    return $form;
  }

  /**
   * Validates the id field.
   */
  public function validateId(array $element, FormStateInterface $form_state, array $form) {
    $zone = $this->getEntity();
    $id = $element['#value'];
    if (!preg_match('/[a-z_]+/', $id)) {
      $form_state->setError($element, $this->t('The machine name must be in lowercase, underscore-separated letters only.'));
    }
    elseif ($zone->isNew()) {
      $loaded_zones = $this->zoneStorage->loadByProperties(array(
        'id' => $id,
      ));
      if ($loaded_zones) {
        $form_state->setError($element, $this->t('The machine name is already in use.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $zone = $this->entity;

    try {
      $zone->save();
      drupal_set_message($this->t('Saved the %label zone.', array(
        '%label' => $zone->label(),
      )));
      $form_state->setRedirect('entity.addressfield_zone.list');
    }
    catch (\Exception $e) {
      drupal_set_message($this->t('The %label zone was not saved.', array(
        '%label' => $zone->label()
      )), 'error');
      $this->logger('addressfield_zone')->error($e);
      $form_state->setRebuild();
    }
  }

}
