<?php

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject;

final class CRM_Pdfletterevents_EventTokenSubscriberTest extends TestCase {
  /**
   * @test
   */
  public function itShouldCallTheParentConstructor() {
    /** @var MockObject|CRM_Pdfletterevents_TokenSet $dummyTokenSet */
    $dummyTokenSet = $this->createMock(CRM_Pdfletterevents_TokenSet::class);

    $subscriber = new CRM_Pdfletterevents_EventTokenSubscriber($dummyTokenSet);
    $this->assertEquals('event', $subscriber->entity);
  }
}
