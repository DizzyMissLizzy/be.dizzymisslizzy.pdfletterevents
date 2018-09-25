<?php

use Civi\Token\TokenRow;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject;

/**
 * Test setting token values with @see CRM_Pdfletterevents_TokenRowParticipantTokenValueSetter.
 */
final class CRM_Pdfletterevents_TokenRowParticipantTokenValueSetterTest extends TestCase {

  /**
   * The setup() method is executed before the test is executed (optional).
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * The tearDown() method is executed after the test was executed (optional)
   * This can be used for cleanup.
   */
  public function tearDown() {
    parent::tearDown();
  }

  /**
   * @test
   */
  public function itShouldSetTheTokenValue() {
    /** @var MockObject|TokenRow $tokenRowMock */
    $tokenRowMock = $this->createMock(TokenRow::class);
    $tokenRowMock->expects($this->once())
      ->method('tokens')
      ->with('participant', 'token', 'value');

    $valueSetter = new CRM_Pdfletterevents_TokenRowParticipantTokenValueSetter($tokenRowMock);
    $valueSetter->setValue('token', 'value');
  }

}
