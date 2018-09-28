<?php

interface CRM_Pdfletterevents_ParticipantTokenSet
{
  /**
   * @return array associative array that maps tokens to their descriptions.
   *
   * E.g. ['{participant.event_id}' => 'Event ID', ...]
   */
  public function getParticipantTokens();

  /**
   * @return array associative array that maps token names to their descriptions.
   *
   * E.g. ['event_id' => 'Event ID', ...]
   */
  public function getParticipantTokenNames();
}
