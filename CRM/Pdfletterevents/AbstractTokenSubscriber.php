<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

use Civi\Token\AbstractTokenSubscriber;
use Civi\Token\TokenRow;

abstract class CRM_Pdfletterevents_AbstractTokenSubscriber extends AbstractTokenSubscriber implements CRM_Pdfletterevents_TokenSubscriber
{
  /**
   * Evaluate the content of a single token.
   *
   * @param TokenRow $row
   *   The record for which we want token values.
   * @param string $entity
   *   The name of the token entity.
   * @param string $field
   *   The name of the token field.
   * @param mixed $prefetch
   *   Any data that was returned by the prefetch().
   */
  public function evaluateToken(TokenRow $row, $entity, $field, $prefetch = NULL)
  {
    // Convert TokenRow to easier interfaces.
    try {
      $entityId = $this->getEntityId($row);
    } catch (CRM_Pdfletterevents_EntityNotFound $ex) {
      // Let's ignore the token if the entity was not found.
      return;
    }

    $tokenValueSetter = $this->getTokenValueSetter($row);

    $this->updateToken($tokenValueSetter, $entityId, $field);
  }
}
