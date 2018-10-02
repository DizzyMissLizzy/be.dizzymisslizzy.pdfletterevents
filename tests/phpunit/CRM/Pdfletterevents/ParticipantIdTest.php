<?php /** @noinspection PhpUnhandledExceptionInspection */

use Civi\Token\TokenRow;
use CRM_Pdfletterevents_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject;

/**
 * Test whether we can build participant ID's from something like a TokenRow.
 *
 * You should use PHPUnit 5 to run this test.
 *
 * @group headless
 */
final class CRM_Pdfletterevents_ParticipantIdTest extends TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  /** @var int */
  private $eventId;

  /** @var int */
  private $contactId;

  /** @var int */
  private $participantId;

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    parent::setUp();

    // Create a new contact and event, and subscribe. (Let's just assume this event exists.)

    $result = civicrm_api3('Contact', 'create', [
      'email' => 'test@example.org',
      'contact_type' => 'Individual',
    ]);
    $this->contactId = $result['id'];

    $result = civicrm_api3('Event', 'create', [
      'title' => 'Test event',
      'event_type_id' => 1,
      'start_date' => '2019-03-08',
    ]);
    $this->eventId = $result['id'];

    $result = civicrm_api3('Participant', 'create', [
      'event_id' => $this->eventId,
      'contact_id' => $this->contactId,
    ]);
    $this->participantId = $result['id'];
  }

  public function tearDown() {
    parent::tearDown();
  }

  public function testFromTokenRow() {
    // mimic the structure of the CiviCRM TokenRow.
    $stub = new stdClass();
    $stub->contact_id = $this->contactId;
    $stub->event_id = $this->eventId;

    /** @var MockObject|TokenRow $tokenRowMock */
    $tokenRowMock = $this->createMock(TokenRow::class);
    $tokenRowMock->context = [
      'actionSearchResult' => $stub
    ];

    $id = CRM_Pdfletterevents_ParticipantId::fromTokenRow($tokenRowMock);

    $this->assertEquals($this->participantId, $id->getValue());
  }
}
