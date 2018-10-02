<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

use Civi\Token\TokenRow;

final class CRM_Pdfletterevents_ParticipantId implements CRM_Pdfletterevents_EntityId
{
  /** @var int  */
  private $participantId;

  /**
   * @param int $participantId
   */
  private function __construct($participantId)
  {
    $this->participantId = $participantId;
  }

  /**
   * Do the CiviCRM magic to extract the relevant data from $tokenRow.
   *
   * I looked at @see CRM_Event_Tokens::evaluateToken() to write this.
   *
   * @param TokenRow $tokenRow
   * @return self
   * @throws CRM_Pdfletterevents_EntityNotFound
   */
  public static function fromTokenRow(TokenRow $tokenRow)
  {
    $abracadabra = $tokenRow->context['actionSearchResult'];
    $contactId = $abracadabra->contact_id;
    $eventId = $abracadabra->event_id;

    // Let's assume that a contact can only participate once to an event.
    try {
      $result = civicrm_api3('Participant', 'getsingle', [
        'contact_id' => $contactId,
        'event_id' => $eventId,
      ]);
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new CRM_Pdfletterevents_EntityNotFound(
        'Unknown participant',
        0,
        $ex
      );
    }

    return new self($result['id']);
  }

  /**
   * @return int
   */
  public function getValue()
  {
    return $this->participantId;
  }
}
