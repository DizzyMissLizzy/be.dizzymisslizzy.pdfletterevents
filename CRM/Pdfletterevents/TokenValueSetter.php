<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

interface CRM_Pdfletterevents_TokenValueSetter
{
  /**
   * @param string $token
   * @param string $value
   */
  public function setValue($token, $value);
}
