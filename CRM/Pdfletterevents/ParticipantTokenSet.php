<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

/**
 * This class retrieves participant related tokens from CRM_Core_SelectValues.
 *
 * I don't like the static classes in CiviCRM, I want interfaces and implementations that can nicely be mocked
 * in unit tests. (Which I don't do yet, but hey, maybe sometimes :-))
 *
 * I ended up with these ugly long class names, because the PSR-4 namespaces didn't seem to work for me.
 * see https://civicrm.stackexchange.com/questions/16418/class-naming-and-namespaces-best-practice-as-an-extension-author#16436
 */
final class CRM_Pdfletterevents_ParticipantTokenSet extends CRM_Pdfletterevents_AbstractTokenSet
{
  /** @inheritdoc */
  public function getTokens()
  {
    return CRM_Core_SelectValues::participantTokens();
  }
}
