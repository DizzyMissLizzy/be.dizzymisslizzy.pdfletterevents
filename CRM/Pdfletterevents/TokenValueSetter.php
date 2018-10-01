<?php

interface CRM_Pdfletterevents_TokenValueSetter
{
  /**
   * @param string $token
   * @param string $value
   */
  public function setValue($token, $value);
}
