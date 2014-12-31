<?php

/**
 * @file
 * Contains \Drupal\addressfield_zone\Form\ZoneDeleteForm.
 */

namespace Drupal\addressfield_zone\Form;


use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete a zone.
 */
class ZoneDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %label?', array('%label' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('entity.addressfield_zone.list');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      $this->entity->delete();
      $form_state->setRedirectUrl($this->getCancelUrl());
      drupal_set_message($this->t('Zone %label has been deleted.', array('%label' => $this->entity->label())));
    }
    catch (\Exception $e) {
      drupal_set_message($this->t('Zone %label could not be deleted.', array('%label' => $this->entity->label())), 'error');
      $this->logger('addressfield_zone')->error($e);
    }
  }

}
