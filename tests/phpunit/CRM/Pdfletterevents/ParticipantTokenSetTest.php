<?php /** @noinspection PhpUnhandledExceptionInspection */

/*
 * Copyright (C) 2018 Johan Vervloet
 * Licensed under Apache License, Version 2
 * https://www.apache.org/licenses/LICENSE-2.0.html
 */

use CRM_Pdfletterevents_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject;

/**
 * Test whether tokens and token names are properly returned.
 *
 * You should use PHPUnit 5 to run this test.
 *
 * @group headless
 */
final class CRM_Pdfletterevents_ParticipantTokenSetTest extends TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {
  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * @test
   */
  public function itShouldReturnTokenNames() {
    $tokenSet = new CRM_Pdfletterevents_ParticipantTokenSet();
    $tokenNames = $tokenSet->getTokenNames();

    $this->assertArrayHasKey('event_start_date', $tokenNames);
    $this->assertArrayHasKey('event_end_date', $tokenNames);

    // I can't test for custom field tokens right now, because of the use
    // of a static variable for caching at
    // CRM_Core_SelectValues::participantTokens().
    // So let's not test that, instead of endlessly trying to work around this issue.
  }
}
