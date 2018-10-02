<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

use Civi\Token\TokenRow;

// FIXME: What about \CRM_Event_Tokens?
final class CRM_Pdfletterevents_EventTokenSubscriber extends CRM_Pdfletterevents_AbstractTokenSubscriber
{
  /**
   * @var CRM_Pdfletterevents_TokenSet
   */
  private $tokenSet;

  public function __construct(CRM_Pdfletterevents_EventTokenSet $tokenSet)
  {
    $this->tokenSet = $tokenSet;
    parent::__construct('event', $tokenSet->getTokenNames());
  }

  /**
   * Retrieves the token value, and stores the result.
   *
   * No value will be stored if the entity is not found.
   *
   * @param CRM_Pdfletterevents_TokenValueSetter $tokenValueSetter
   * @param CRM_Pdfletterevents_EntityId $entityId
   * @param string $field
   */
  public function updateToken(CRM_Pdfletterevents_TokenValueSetter $tokenValueSetter, CRM_Pdfletterevents_EntityId $entityId, $field)
  {
    // Performance can be improved, but for now I just want the code to be clean.
    // It is possible that I should use what's in CRM_Utils_EventToken::getEventTokenReplacement()
    // instead.
    try {
      $result = civicrm_api3('Event', 'getsingle', [
        'id' => $entityId->getValue(),
        // 'return' => [$field],
      ]);
    }
    catch (CiviCRM_API3_Exception $ex) {
      return;
    }

    $tokenValueSetter->setValue($field, $result[$field]);
  }

  /**
   * Retrieves the entity ID from a token row.
   *
   * This is the hard thing to implement, because @see TokenRow has little structure.
   *
   * @param TokenRow $row
   * @return CRM_Pdfletterevents_EntityId
   * @throws CRM_Pdfletterevents_EntityNotFound
   */
  public function getEntityId(TokenRow $row)
  {
    return CRM_Pdfletterevents_EventId::fromTokenRow($row);
  }

  /**
   * @param TokenRow $row
   * @return CRM_Pdfletterevents_TokenValueSetter
   */
  public function getTokenValueSetter(TokenRow $row)
  {
    return new CRM_Pdfletterevents_EventTokenValueSetter($row);
  }
}
