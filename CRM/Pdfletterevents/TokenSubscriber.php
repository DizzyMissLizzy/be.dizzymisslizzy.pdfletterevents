<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

use Civi\Token\AbstractTokenSubscriber;
use Civi\Token\TokenRow;

/**
 * Easier interface for a token subscriber than CiviCRM's @see AbstractTokenSubscriber
 *
 * Maybe getEntityId() and getTokenValueSetter() should be in another class?
 */
interface CRM_Pdfletterevents_TokenSubscriber
{
  /**
   * Retrieves the token value, and stores the result.
   *
   * No value will be stored if the entity is not found.
   *
   * @param CRM_Pdfletterevents_TokenValueSetter $tokenValueSetter
   * @param CRM_Pdfletterevents_EntityId $entityId
   * @param string $field
   */
  public function updateToken(CRM_Pdfletterevents_TokenValueSetter $tokenValueSetter, CRM_Pdfletterevents_EntityId $entityId, $field);

  /**
   * Retrieves the entity ID from a token row.
   *
   * This is the hard thing to implement, because @see TokenRow has little structure.
   *
   * @param TokenRow $row
   * @return CRM_Pdfletterevents_ParticipantId
   * @throws CRM_Pdfletterevents_EntityNotFound
   */
  public function getEntityId(TokenRow $row);

  /**
   * @param TokenRow $row
   * @return CRM_Pdfletterevents_TokenValueSetter
   */
  public function getTokenValueSetter(TokenRow $row);
}
