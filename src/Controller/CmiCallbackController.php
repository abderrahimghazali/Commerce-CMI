<?php

namespace Drupal\commerce_cmi\Controller;

use Drupal\Core\Controller\ControllerBase;



/**
 * Returns response for cmi Form Payment Method.
 */
class CmiCallbackController extends ControllerBase {

  /**
   * cmi callback request.
   *
   * @todo Handle Callback from cmi payment gateway.
   */
  public function CmiCallback() {}

  /**
   * cmi OK request.
   *
   * @todo Handle OK request from cmi payment gateway.
   */
  public function CmiOK() {}

  /**
   * cmi Fail request.
   *
   * @todo Handle Fail request from cmi payment gateway.
   */
  public function CmiFail() {}

}