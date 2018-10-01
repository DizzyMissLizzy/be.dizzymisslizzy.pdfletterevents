<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

interface CRM_Pdfletterevents_TokenSet
{
  /**
   * @return array associative array that maps tokens to their descriptions.
   *
   * E.g. ['{participant.event_id}' => 'Event ID', ...]
   */
  public function getTokens();

  /**
   * @return array associative array that maps token names to their descriptions.
   *
   * E.g. ['event_id' => 'Event ID', ...]
   */
  public function getTokenNames();
}
