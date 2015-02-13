<?php

/**
 * @file
 * Contains \Drupal\address\Controller\SubdivisionController.
 */

namespace Drupal\address\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Provides route responses for subdivisions.
 */
class SubdivisionController extends ControllerBase {

  /**
   * Provides the subdivision add form.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match.
   *
   * @return array
   *   The subdivision add form.
   */
  public function addForm(RouteMatchInterface $route_match) {
    $address_format = $route_match->getParameter('address_format');
    $parent = $route_match->getParameter('parent');
    $values = array(
      'countryCode' => $address_format->getCountryCode(),
      'parentId' => $parent ? $parent->id() : NULL,
    );
    $subdivision = $this->entityManager()->getStorage('subdivision')->create($values);

    return $this->entityFormBuilder()->getForm($subdivision, 'add');
  }

  /**
   * Provides the subdivision list.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match.
   *
   * @return array
   *   The subdivision list.
   */
  public function buildList(RouteMatchInterface $route_match) {
    $list_builder = $this->entityManager()->getListBuilder('subdivision');
    $list_builder->setAddressFormat($route_match->getParameter('address_format'));
    $list_builder->setParent($route_match->getParameter('parent'));
    $build = array();
    $build['subdivision_table'] = $list_builder->render();

    return $build;
  }

}
