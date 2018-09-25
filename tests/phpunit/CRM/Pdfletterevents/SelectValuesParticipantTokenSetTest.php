<?php /** @noinspection PhpUnhandledExceptionInspection */

use CRM_Pdfletterevents_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * Test whether tokens and token names are properly returned.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
final class CRM_Pdfletterevents_SelectValuesParticipantTokenSetTest extends \PHPUnit_Framework_TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  /**
   * @var int
   */
  private $customFieldId;

  /**
   * @var int
   */
  private $customGroupId;

  const CUSTOM_GROUP_NAME = 'test_pdfletterevents_custom_group';

  public function setUpHeadless() {
    // Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
    // See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp() {
    parent::setUp();

    // Set up some custom field for participants.
    // FIXME: This needs some cleanup.
    try {
      $result = civicrm_api3('CustomGroup', 'create', [
        'name' => self::CUSTOM_GROUP_NAME,
        'extends' => 'Participant',
        'title' => 'Custom group for testing',
        // Don't forget to specify a table name, otherwise you will get strange errors.
        'table_name' => self::CUSTOM_GROUP_NAME,
      ]);
    }
    catch (CiviCRM_API3_Exception $ex) {
      // Custom group probably already exists.
      $result = civicrm_api3('CustomGroup', 'get', [
        'name' => self::CUSTOM_GROUP_NAME,
      ]);
    }
    $this->customGroupId = $result['id'];

    try {
      $result = civicrm_api3('CustomField', 'create', [
        'custom_group_id' => $this->customGroupId,
        'label' => 'My Field',
        'data_type' => 'String',
        'html_type' => 'Text',
        'is_required' => 0,
        'is_searchable' => 1,
        'is_search_range' => 0,
        'is_active' => 1,
        'is_view' => 0,
        'text_lenght' => 20,
        'column_name' => 'my_field'
      ]);
    }
    catch (CiviCRM_API3_Exception $ex) {
      // Custom field probably already exists.
      $result = civicrm_api3('CustomField', 'get', [
        'custom_group_id' => $this->customGroupId,
        'label' => 'My field',
      ]);
    }

    $this->customFieldId = $result['id'];
  }

  public function tearDown() {
    parent::tearDown();
  }

  /**
   * @test
   */
  public function itShouldReturnTokenNames() {
    $tokenSet = new CRM_Pdfletterevents_SelectValuesParticipantTokenSet();
    $tokenNames = $tokenSet->getParticipantTokenNames();

    $this->assertArrayHasKey('event_start_date', $tokenNames);
    $this->assertArrayHasKey('event_end_date', $tokenNames);
    $this->assertArrayHasKey("custom_{$this->customFieldId}", $tokenNames);
  }
}
