<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

use Civi\Token\TokenRow;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject;

final class CRM_Pdfletterevents_EventIdTest extends TestCase
{
  const SOME_EVENT_ID = 123;

  public function testFromTokenRow() {
    $stub = new stdClass();
    $stub->event_id = self::SOME_EVENT_ID;

    /** @var MockObject|TokenRow $tokenRowMock */
    $tokenRowMock = $this->createMock(TokenRow::class);
    $tokenRowMock->context = [
      'actionSearchResult' => $stub
    ];

    $id = CRM_Pdfletterevents_EventId::fromTokenRow($tokenRowMock);

    $this->assertEquals(self::SOME_EVENT_ID, $id->getValue());
  }
}
