<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

use Civi\Token\TokenRow;

final class CRM_Pdfletterevents_EventId implements CRM_Pdfletterevents_EntityId
{
  /**
   * @var int
   */
  private $eventId;

  /**
   * @param int $eventId
   */
  private function __construct($eventId)
  {
    $this->eventId = $eventId;
  }

  /**
   * @return int
   */
  public function getValue()
  {
    return $this->eventId;
  }

  /**
   * @param TokenRow $tokenRow
   * @return CRM_Pdfletterevents_EntityId
   */
  public static function fromTokenRow(TokenRow $tokenRow)
  {
    $abracadabra = $tokenRow->context['actionSearchResult'];
    return new CRM_Pdfletterevents_EventId($abracadabra->event_id);
  }
}
