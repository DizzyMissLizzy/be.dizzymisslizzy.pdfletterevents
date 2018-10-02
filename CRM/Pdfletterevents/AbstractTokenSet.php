<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

abstract class CRM_Pdfletterevents_AbstractTokenSet implements CRM_Pdfletterevents_TokenSet
{
  /** @inheritdoc */
  abstract function getTokens();

  /** @inheritdoc */
  public function getTokenNames()
  {
    // There is probably an easier way to do this.

    $result = [];

    $pattern = '/\{[a-z_]+\.([^.]*)\}/';
    foreach ($this->getTokens() as $token => $description) {
      $tokenName = preg_replace($pattern, '$1', $token);
      $result[$tokenName] = $description;
    }

    return $result;
  }

}
