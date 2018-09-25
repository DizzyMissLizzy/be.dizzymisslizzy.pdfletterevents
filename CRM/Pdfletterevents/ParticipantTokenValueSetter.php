<?php

interface CRM_Pdfletterevents_ParticipantTokenValueSetter
{
  /**
   * @param string $token
   * @param string $value
   */
  public function setValue($token, $value);
}
