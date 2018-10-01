<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

use Civi\Token\TokenRow;

final class CRM_Pdfletterevents_ParticipantTokenValueSetter implements CRM_Pdfletterevents_TokenValueSetter
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
