<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

final class CRM_Pdfletterevents_EventTokenSet extends CRM_Pdfletterevents_AbstractTokenSet
{
  /** @inheritdoc */
  function getTokens()
  {
    $tokens = CRM_Core_SelectValues::eventTokens();
    $customTokens = CRM_Core_BAO_CustomField::getFields('Event');

    foreach ($customTokens as $tokenKey => $tokenValue) {
      $tokens["{event.custom_$tokenKey}"] = $tokenValue['label'] . '::' . $tokenValue['groupTitle'];
    }

    return $tokens;
  }
}
