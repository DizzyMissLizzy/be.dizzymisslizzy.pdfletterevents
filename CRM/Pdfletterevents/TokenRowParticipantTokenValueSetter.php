<?php

use Civi\Token\TokenRow;

final class CRM_Pdfletterevents_TokenRowParticipantTokenValueSetter implements CRM_Pdfletterevents_ParticipantTokenValueSetter
{
  /**
   * @var TokenRow
   */
  private $tokenRow;

  public function __construct(TokenRow $tokenRow)
  {
    $this->tokenRow = $tokenRow;
  }

  /**
   * @param string $token
   * @param string $value
   */
  public function setValue($token, $value)
  {
    $this->tokenRow->tokens('participant', $token, $value);
  }
}
