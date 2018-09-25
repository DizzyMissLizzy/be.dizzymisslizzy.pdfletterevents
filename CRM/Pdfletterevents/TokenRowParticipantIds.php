<?php


use Civi\Token\TokenRow;

final class CRM_Pdfletterevents_TokenRowParticipantIds implements CRM_Pdfletterevents_ParticipantIds
{
  /** @var int  */
  private $participantId;
  /** @var int  */
  private $contactId;
  /** @var int  */
  private $eventId;

  /**
   * CRM_Pdfletterevents_TokenRowParticipantRowData constructor.
   * @param int $participantId
   * @param int $contactId
   * @param int $eventId
   */
  private function __construct($participantId, $contactId, $eventId)
  {
    $this->participantId = $participantId;
    $this->contactId = $contactId;
    $this->eventId = $eventId;
  }

  /**
   * Do the CiviCRM magic to extract the relevant data from $tokenRow.
   *
   * I looked at @see CRM_Event_Tokens::evaluateToken() to write this.
   *
   * @param TokenRow $tokenRow
   * @return self
   * @throws CRM_Pdfletterevents_UnknownParticipant
   */
  public static function fromTokenRow(TokenRow $tokenRow)
  {
    $abracadabra = $tokenRow->context['actionSearchResult'];
    $contactId = $abracadabra->contact_id;
    $eventId = $abracadabra->event_id;

    // Let's assume that a contact can only participate once to an event.
    try {
      $result = civicrm_api3('participant', 'getSingle', [
        'contact_id' => $contactId,
        'event_id' => $eventId,
      ]);
    }
    catch (CiviCRM_API3_Exception $ex) {
      throw new CRM_Pdfletterevents_UnknownParticipant(
        'Unknown participant',
        0,
        $ex
      );
    }

    return new self($result['id'], $contactId, $eventId);
  }

  /**
   * @return int
   */
  public function getParticipantId()
  {
    return $this->participantId;
  }

  /**
   * @return int
   */
  public function getContactId()
  {
    return $this->contactId;
  }

  /**
   * @return int
   */
  public function getEventId()
  {
    return $this->eventId;
  }
}
