<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

use Civi\Token\TokenRow;

final class CRM_Pdfletterevents_ParticipantTokenSubscriber extends CRM_Pdfletterevents_AbstractTokenSubscriber
{
  /**
   * @var CRM_Pdfletterevents_TokenSet
   */
  private $tokenSet;

  public function __construct(CRM_Pdfletterevents_ParticipantTokenSet $tokenSet)
  {
    $this->tokenSet = $tokenSet;
    parent::__construct(
      'participant',
      $tokenSet->getTokenNames()
    );
  }

  /**
   * @param TokenRow $row
   * @return CRM_Pdfletterevents_ParticipantId
   * @throws CRM_Pdfletterevents_EntityNotFound
   */
  public function getEntityId(TokenRow $row)
  {
    return CRM_Pdfletterevents_ParticipantId::fromTokenRow($row);
  }

  public function getTokenValueSetter(TokenRow $row)
  {
    return new CRM_Pdfletterevents_ParticipantTokenValueSetter($row);
  }

  /**
   * Retrieves the token value, and stores the result.
   *
   * No value will be stored if the participant is not found.
   *
   * @param CRM_Pdfletterevents_TokenValueSetter $tokenValueSetter
   * @param CRM_Pdfletterevents_EntityId $entityId
   * @param string $field
   */
  public function updateToken(CRM_Pdfletterevents_TokenValueSetter $tokenValueSetter, CRM_Pdfletterevents_EntityId $entityId, $field)
  {
    // Performance can be improved, but for now I just want the code to be clean.
    try {
      $result = civicrm_api3('Participant', 'getsingle', [
        'id' => $entityId->getValue(),
        'return' => [$field],
      ]);
    }
    catch (CiviCRM_API3_Exception $ex) {
      return;
    }

    $tokenValueSetter->setValue($field, $result[$field]);
  }
}
